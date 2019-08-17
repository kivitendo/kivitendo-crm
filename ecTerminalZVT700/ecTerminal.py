#!/usr/bin/env python3
# -*- coding: utf-8 -*-

#DISK5one
from ecrterm.ecr import ECR, ecr_log
from ecrterm.packets.base_packets import Registration

import sys
import json
import cgi

fs = cgi.FieldStorage()
data = {}
for key in fs.keys():
    data[key] = fs.getvalue(key)

def printer(lines_of_text):
    for line in lines_of_text:
        print(line)

sys.stdout.write("Content-Type: application/json")
sys.stdout.write("\n")
sys.stdout.write("\n")

result = {}
result['data'] = 1

sys.stdout.write( json.dumps( result, indent=1 ) )
sys.stdout.write( "\n" )
sys.stdout.close()

if __name__ == '__main__':
    e = ECR( device='socket://' + data['ip'] + ':' + data['port'], password=data['passwd'])
    # reenable logging:
    #e.transport.slog = ecr_log
    #print(e.detect_pt())
    if e.detect_pt():
        e.register(config_byte=Registration.generate_config(
            ecr_prints_receipt=False,
            ecr_prints_admin_receipt=False,
            ecr_controls_admin=True,
            ecr_controls_payment=True))
        if data['action'] == 'end_of_day':
            e.end_of_day()
            e.wait_for_status()
            #status = e.status()
            #print( status )
        if data['action'] == 'pay':
            if e.payment(amount_cent=data['amount']):
                printer(e.last_printout())
                e.wait_for_status()
                e.show_text( lines=['Auf Wiedersehenxx!', ' ', 'Zahlung erfolgt'], beeps=1 )
                #status = e.status()
            else:
                e.wait_for_status()
                e.show_text( lines=['Auf Wiedersehenyy! ', ' ', 'Vorgang abgebrochen'], beeps=2 )
                #status = e.status()


