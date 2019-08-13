from binascii import hexlify
from socket import (
    IPPROTO_TCP, SHUT_RDWR, SO_KEEPALIVE, SOL_SOCKET, create_connection)
from socket import timeout as SocketTimeout
from struct import unpack
from sys import platform
from typing import Tuple
from urllib.parse import parse_qs, urlsplit

from ecrterm.common import Transport, noop
from ecrterm.conv import bs2hl
from ecrterm.exceptions import (
    TransportConnectionFailed, TransportLayerException,
    TransportTimeoutException)
from ecrterm.packets.apdu import APDUPacket
from ecrterm.transmission.signals import TIMEOUT_T2

if platform == 'linux':
    from socket import TCP_KEEPIDLE, TCP_KEEPINTVL
elif platform == 'darwin':
    from socket import TCP_KEEPINTVL
try:
    from socket import TCP_KEEPCNT
except ImportError:
    TCP_KEEPCNT = None


def hexformat(data: bytes) -> str:
    """Return a prettified binary data."""
    hexlified = str(hexlify(data), 'ascii')
    splitted = ':'.join(
        hexlified[i:i + 2] for i in range(0, len(hexlified), 2))
    return repr(bytes(data)) + ' -> ' + splitted


class SocketTransport(Transport):
    """
    Transport for TCP/IP. You can set various timeouts by passing
    it in the uri. An example:
    `socket://192.168.1.163:20007?connect_timeout=5&so_keepalive=5&tcp_keepidle=1&tcp_keepintvl=3&tcp_keepcnt=5`

    See http://man7.org/linux/man-pages/man7/tcp.7.html for TCP
    flags details.
    """
    insert_delays = False
    slog = noop
    defaults = dict(
        connect_timeout=5, so_keepalive=0, tcp_keepidle=1, tcp_keepintvl=3,
        tcp_keepcnt=5, debug='false', packetdebug='false')

    def __init__(self, uri: str):
        """Setup the IP and Port."""
        parsed = urlsplit(url=uri)
        if ':' not in parsed.netloc:
            raise AttributeError(
                'uri needs an IP and a port with : separated.')
        self.ip, port = parsed.netloc.split(':')
        self.port = int(port)
        qs_parsed = parse_qs(qs=parsed.query)
        self.connect_timeout = int(qs_parsed.get(
            'connect_timeout', [self.defaults['connect_timeout']])[0])
        self.so_keepalive = int(qs_parsed.get(
            'so_keepalive', [self.defaults['so_keepalive']])[0])
        self.tcp_keepidle = int(qs_parsed.get(
            'tcp_keepidle', [self.defaults['tcp_keepidle']])[0])
        self.tcp_keepintvl = int(qs_parsed.get(
            'tcp_keepintvl', [self.defaults['tcp_keepintvl']])[0])
        self.tcp_keepcnt = int(qs_parsed.get(
            'tcp_keepcnt', [self.defaults['tcp_keepcnt']])[0])
        self._debug = qs_parsed.get(
            'debug', [self.defaults['debug']])[0] == 'true'
        self._packetdebug = qs_parsed.get(
            'packetdebug', [self.defaults['packetdebug']])[0] == 'true'
        if self._debug:
            from ecrterm.ecr import ecr_log
            self.slog = ecr_log

    def connect(self, timeout: int=None) -> bool:
        """
        Connect to the TCP socket. Return `True` on successful
        connection, `False` on an unsuccessful one.
        """
        if timeout is None:
            timeout = self.connect_timeout
        try:
            self.sock = create_connection(
                address=(self.ip, self.port), timeout=timeout)
            if self.so_keepalive:
                self.sock.setsockopt(
                    SOL_SOCKET, SO_KEEPALIVE, self.so_keepalive)
            if self.tcp_keepidle and platform == 'linux':
                self.sock.setsockopt(
                    IPPROTO_TCP, TCP_KEEPIDLE, self.tcp_keepidle)
            if self.tcp_keepintvl and platform in set(['linux', 'darwin']):
                self.sock.setsockopt(
                    IPPROTO_TCP, TCP_KEEPINTVL, self.tcp_keepintvl)
            if self.tcp_keepcnt and TCP_KEEPCNT:
                self.sock.setsockopt(
                    IPPROTO_TCP, TCP_KEEPCNT, self.tcp_keepcnt)
            return True
        except (ConnectionError, SocketTimeout) as exc:
            raise TransportConnectionFailed(exc.args[0])

    def send(self, apdu, tries: int=0, no_wait: bool=False):
        """Send data."""
        to_send = bytes(apdu.to_list())
        self.slog(data=bs2hl(binstring=to_send), incoming=False)
        total_sent = 0
        msglen = len(to_send)
        while total_sent < msglen:
            sent = self.sock.send(to_send[total_sent:])
            if self._packetdebug:
                print('sent', sent, 'bytes of', hexformat(
                    data=to_send[total_sent:]))
            if sent == 0:
                raise RuntimeError('Socket connection broken.')
            total_sent += sent
        if no_wait:
            return True
        return self.receive()

    def _receive_bytes(self, length: int) -> bytes:
        """Receive and return a fixed amount of bytes."""
        recv_bytes = 0
        result = b''
        if self._packetdebug:
            print('\nwaiting for', length, 'bytes')
        while recv_bytes < length:
            try:
                chunk = self.sock.recv(length - recv_bytes)
            except SocketTimeout:
                raise TransportTimeoutException('Timed out.')
            if self._packetdebug:
                print('received', len(chunk), 'bytes:', hexformat(data=chunk))
            if chunk == b'':
                raise TransportLayerException('TCP Stream disconnected.')
            result += chunk
            recv_bytes += len(chunk)
        return result

    def _receive_length(self) -> Tuple[bytes, int]:
        """
        Receive the 4 bytes on the socket which indicates the message
        length, and return the packed and the `int` converted length.
        """
        data = self._receive_bytes(length=3)
        length = data[2]
        if length != 0xff:
            return data, length
        # Need to get 2 more bytes
        length = self._receive_bytes(length=2)
        data += length
        return data, unpack('<H', length)[0]

    def _receive(self, timeout=TIMEOUT_T2) -> bytes:
        """
        Receive the response from the terminal and return is as `bytes`.
        """
        data, length = self._receive_length()
        if not length:  # Length is 0
            return data
        new_data = self._receive_bytes(length=length)
        return data + new_data

    def receive(
            self, timeout=None, *args, **kwargs) -> Tuple[bool, APDUPacket]:
        """
        Receive data, return success status and ADPUPacket instance.
        """
        self.sock.settimeout(timeout)
        data = self._receive()
        self.slog(data=bs2hl(binstring=data), incoming=True)
        return True, APDUPacket.parse(blob=data)

    def close(self):
        """Shutdown and close the connection."""
        self.sock.shutdown(SHUT_RDWR)
        self.sock.close()
