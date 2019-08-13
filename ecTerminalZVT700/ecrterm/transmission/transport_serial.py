"""
Serial Layer

The Serial Layer is a transport used for

@author g4b
"""

import serial
from ecrterm.common import Transport, noop
from ecrterm.conv import bs2hl, hl2bs, toBytes, toHexString
from ecrterm.crc import crc_xmodem16
from ecrterm.exceptions import (
    TransportLayerException, TransportTimeoutException)
from ecrterm.packets.apdu import APDUPacket
from ecrterm.transmission.signals import (
    ACK, DLE, ETX, NAK, STX, TIMEOUT_T1, TIMEOUT_T2)
from ecrterm.utils import ensure_bytes, is_stringlike
from time import time

SERIAL_DEBUG = False


def std_serial_log(instance, data, incoming=False):
    try:
        if is_stringlike(incoming):
            data = bs2hl(data)
        if incoming:
            print('< %s' % toHexString(data))
        else:
            print('> %s' % toHexString(data))
    except Exception:
        print('| error in log')


class SerialMessage(object):
    """
    Converts a Packet into a serial message by serializing the packet
    and inserting it into the final Serial Packet
    CRC and double-DLEs included.
    """
    apdu = None

    def __init__(self, apdu=None):
        if is_stringlike(apdu):
            # try to get the list of bytes.
            apdu = toBytes(apdu.replace(' ', ''))
        elif isinstance(apdu, APDUPacket):
            apdu = apdu.to_list()
        self.apdu = apdu

    def _get_crc(self):
        data = hl2bs(self.apdu + [ETX])
        try:
            return crc_xmodem16(data)
        except Exception:
            print(self.apdu)
            raise

    def _get_crc_l(self):
        return self._get_crc() & 0x00FF

    def _get_crc_h(self):
        return (self._get_crc() & 0xFF00) >> 8
    crc_l = property(_get_crc_l)
    crc_h = property(_get_crc_h)

    def crc(self):
        return [self.crc_l, self.crc_h]

    def enrich(self, apdu):
        # add 0x10 to each 0x10 in apdu
        # since we use del later, it would occasionally hit the instance
        apdu = apdu[:]
        new_apdu = []
        while len(apdu):
            if apdu.count(DLE):
                new_apdu += apdu[:apdu.index(DLE) + 1] + [DLE]
                del apdu[:apdu.index(DLE) + 1]
            else:
                new_apdu += apdu
                apdu = []
        return new_apdu

    def __repr__(self):
        return 'SerialMessage (APDU: %s, CRC-L: %s CRC-H: %s)' % (
            toHexString(self.apdu),
            hex(self.crc_l),
            hex(self.crc_h))

    def dump_message(self):
        # if 0x10 in apdu:
        apdu = self.enrich(self.apdu)
        return [DLE, STX] + apdu + [DLE, ETX, self.crc_l, self.crc_h]

    def as_bin(self):
        return hl2bs(self.dump_message())


class SerialTransport(Transport):
    SerialCls = serial.Serial
    slog = noop
    insert_delays = True

    def __init__(self, device):
        self.device = device
        self.connection = None

    def connect(self, timeout=30):
        ser = self.SerialCls(
            port=self.device, baudrate=9600, parity=serial.PARITY_NONE,
            stopbits=serial.STOPBITS_TWO, bytesize=serial.EIGHTBITS,
            timeout=timeout,  # set a timeout value, None for waiting forever
            xonxoff=0,  # disable software flow control
            rtscts=0,  # disable RTS/CTS flow control
        )
        if not ser.isOpen():
            ser.open()
        # 8< got that from somwhere, not sure what it does:
        ser.setRTS(1)
        ser.setDTR(1)
        ser.flushInput()
        ser.flushOutput()
        # >8
        if ser.isOpen():
            self.connection = ser
            return True
        return False

    def close(self):
        if self.connection:
            self.connection.close()

    def reset(self):
        if self.connection:
            self.connection.flushInput()
            self.connection.flushOutput()

    def write(self, something=None):
        if something:
            try:
                self.slog(bs2hl(something))
            finally:
                self.connection.write(ensure_bytes(something))  # !?

    def write_ack(self):
        # writes an ack.
        try:
            self.slog([ACK])
        finally:
            self.connection.write(ensure_bytes(chr(ACK)))

    def write_nak(self):
        try:
            self.slog([NAK])
        finally:
            self.connection.write(ensure_bytes(chr(NAK)))

    def read(self, timeout=TIMEOUT_T2):
        """Reads a message packet. any errors are raised directly."""
        # if in 5 seconds no message appears, we respond with a nak and
        # raise an error.
        self.connection.timeout = timeout
        apdu = []
        crc = None
        header = self.connection.read(2)
        header = bs2hl(header)
        # test if there was a transmission:
        if header == []:
            raise TransportLayerException('Reading Header Timeout')
        # test our header to be valid
        if header != [DLE, STX]:
            self.slog(header, True)
            raise TransportLayerException('Header Error: %s' % header)
        # read until DLE, ETX is reached.
        dle = False
        # timeout to T1 after header.
        self.connection.timeout = TIMEOUT_T1
        while not crc:
            b = ord(self.connection.read(1))  # read a byte.
            if b is None:
                # timeout
                raise TransportLayerException('Timeout T1 reading stream.')
            if b == ETX and dle:
                # dle was set, and this is ETX, so we are at the end.
                # we read the CRC now.
                crc = self.connection.read(2)
                if not crc:
                    raise TransportLayerException('Timeout T1 reading CRC')
                else:
                    crc = bs2hl(crc)
                # and break
                continue
            elif b == DLE:
                if not dle:
                    # this is a dle
                    dle = True
                    continue
                else:
                    # this is the second dle. we take it.
                    dle = False
            elif dle:
                # dle was set, but we got no etx here.
                # this seems to be an error.
                raise TransportLayerException('DLE without sense detected.')
            # we add this byte to our apdu.
            apdu += [b]
        self.slog(header + apdu + [DLE, ETX] + crc, True)
        return crc, apdu

    def read_message(self, timeout=TIMEOUT_T2):
        try:
            crc, apdu = self.read(timeout)
            msg = SerialMessage(apdu)
        except Exception:
            # this is a NAK - re-raise for further investigation.
            self.write_nak()
            raise
        # test the CRC:
        if msg.crc() == crc:
            self.write_ack()
            return True, msg
        else:
            # self.write_nak()
            return False, msg

    def receive(self, timeout=TIMEOUT_T2):
        # receive a message up to three times.
        i = 0
        crc_ok = None
        while not i or (i < 2 and not crc_ok):
            crc_ok, message = self.read_message(timeout)
            if not crc_ok:
                self.log('CRC Checksum Error, retry %s' % i)
            i += 1
        if not crc_ok:
            # Message Fail!?
            self.write_nak()
            return False, message.apdu
        # otherwise
        return True, APDUPacket.parse(message.apdu)

    def send_message(self, message, tries=0, no_wait=False):
        """
        sends input with write
        returns output with read.
        if skip_read is True, it only returns true, you have to read
        yourself.
        """
        if message:
            self.write(message.as_bin())
            acknowledge = b''
            ts_start = time()
            while not acknowledge:
                acknowledge = self.connection.read(1)
                # With ingenico devices, acknowledge is often empty.
                # Just retrying seems to help.
                if time() - ts_start > 1:
                    break
            self.slog(acknowledge, True)
            # if nak, we retry, if ack, we read, if other, we raise.
            if acknowledge == ensure_bytes(chr(ACK)):
                # everything alright.
                if no_wait:
                    return True
                return self.receive()
            elif acknowledge == ensure_bytes(chr(NAK)):
                # not everything allright.
                # if tries < 3:
                #    return self.send_message(message, tries + 1, no_answer)
                # else:
                raise TransportLayerException('Could not send message')
            elif not acknowledge:
                raise TransportTimeoutException('No Answer, Possible Timeout')
            else:
                raise TransportLayerException(
                    'Unknown Acknowledgment Byte %s' % bs2hl(acknowledge))

    def send(self, apdu, tries=0, no_wait=False):
        """Automatically converts an apdu into a message."""
        return self.send_message(SerialMessage(apdu), tries, no_wait)


# self test
if __name__ == '__main__':
    c = SerialTransport('/dev/ttyUSB0')
    from ecrterm.packets.base_packets import Registration
    if c.connect():
        print('connected to usb0')
    else:
        exit()
    # register
    answer = c.send_serial(Registration())
    print(answer)
