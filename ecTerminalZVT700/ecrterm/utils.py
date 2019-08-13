"""
Utility Functions.

@author g4b
"""
StatusEnquiry = Completion = ECR = None


def is_stringlike(v):
    return isinstance(v, str) or isinstance(v, bytes)


def ensure_bytes(v):
    if isinstance(v, str):
        return bytearray(ord(c) for c in v)
    if isinstance(v, list):
        return bytearray(v)
    return v


def detect_pt_serial(device='/dev/ttyUSB0', timeout=2, silent=True, ecr=None):
    """
    connects to given serial port and tests if a PT is present.
    if present: tries to return version number or True
    returns False otherwise.

    @param timeout: set the timeout to have a faster response time.
    @param silent: if False, exceptions won't be caught, default: True.
    @param ecr: give a working ecr to perform this task. note: you have to
        reconnect the transport since the timeout is changed.
    """
    global ECR, StatusEnquiry, Completion
    if ECR is None:
        from ecrterm.ecr import ECR  # avoid circular import
    if StatusEnquiry is None:
        from ecrterm.packets.base_packets import StatusEnquiry, Completion

    def __detect_pt_serial(port, timeout, ecr):
        e = ecr or ECR(port)
        # reconnect to have lower timeout
        e.transport.close()
        e.transport.connect(timeout=timeout)
        errors = e.transmit(StatusEnquiry())
        try:
            if not errors:
                if isinstance(e.last.completion, Completion):
                    return e.last.completion.fixed_values.get(
                        'sw-version', True) or True
                return True
            return False
        finally:
            # Reset timeout
            e.transport.close()
            e.transport.connect()
    if silent:
        try:
            return __detect_pt_serial(device, timeout, ecr)
        except Exception:
            return False
    else:
        return __detect_pt_serial(device, timeout, ecr)


if __name__ == '__main__':
    if detect_pt_serial():
        print("PT is online at ttyUSB0")
    else:
        print("PT cant be found at ttyUSB0")
