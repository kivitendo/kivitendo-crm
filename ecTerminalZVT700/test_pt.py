#!/usr/bin/env python
# -*- coding: utf-8 -*-
"""
Example script to demonstrate a payment process.
"""
from ecrterm.ecr import ECR, ecr_log
from ecrterm.packets.base_packets import Registration


def printer(lines_of_text):
    for line in lines_of_text:
        print(line)


if __name__ == '__main__':
    e = ECR(device='socket://192.168.178.9:22000', password='012345')
    # reenable logging:
    e.transport.slog = ecr_log
    print(e.detect_pt())
    if e.detect_pt():
        e.register(config_byte=Registration.generate_config(
            ecr_prints_receipt=False,
            ecr_prints_admin_receipt=False,
            ecr_controls_admin=True,
            ecr_controls_payment=True))

        e.wait_for_status()
        status = e.status()
        if status:
            print('Status code of PT is %s' % status)
            # laut doku sollte 0x9c bedeuten, ein tagesabschluss erfolgt
            # bis jetzt unklar ob er es von selbst ausführt.

            if status == 0x9c:
                print('End Of Day')
                e.end_of_day()
                # last_printout() would work too:
                printer(e.daylog)
            else:
                print('Unknown Status Code: %s' % status)
                # status == 0xDC for ReadCard (06 C0) -> Karte drin.
                # 0x9c karte draussen.

        if e.payment(amount_cent=20):
            printer(e.last_printout())
            e.wait_for_status()
            e.show_text(
                lines=['Auf Wiedersehen!', ' ', 'Zahlung erfolgt'], beeps=1)
        else:
            e.wait_for_status()
            e.show_text(
                lines=['Auf Wiedersehen!', ' ', 'Vorgang abgebrochen'],
                beeps=2)
