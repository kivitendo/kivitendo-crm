#!/bin/sh
set -e



echo "Tabelle crmti ev. manuell loschen!!"
echo "*******************************************" 


#Links löschen und *.org in * umbenennen

#TelefonIcon entfernen

if [ -f /usr/lib/lx-office-erp/xslt/xulmenu.xsl.org ]; then
	rm /usr/lib/lx-office-erp/xslt/xulmenu.xsl
	mv /usr/lib/lx-office-erp/xslt/xulmenu.xsl.org /usr/lib/lx-office-erp/xslt/xulmenu.xsl
else
	echo "Error /usr/lib/lx-office-erp/xslt/xulmenu.xsl.org nicht gefunden"
fi

# Menu wiederherstellen 
if [ -f /usr/lib/lx-office-erp/menu.ini.org1 ]; then
	rm /usr/lib/lx-office-erp/menu.ini
	mv /usr/lib/lx-office-erp/menu.ini.org1 /usr/lib/lx-office-erp/menu.ini
else
	echo "Error menue.ini.org1 nicht gefunden"
fi

# AutoComplete entfernen 
if [ -f /usr/lib/lx-office-crm/tpl/firmen3.tpl.org1 ]; then
	rm /usr/lib/lx-office-crm/tpl/firmen3.tpl
	mv /usr/lib/lx-office-crm/tpl/firmen3.tpl.org1 /usr/lib/lx-office-crm/tpl/firmen3.tpl
else
	echo "Error firmen3.tpl.org1 nicht gefunden"
fi

# Suche wiederherstellen 
if [ -f /usr/lib/lx-office-crm/getData.php.org1 ]; then
 	rm /usr/lib/lx-office-crm/getData.php
	mv /usr/lib/lx-office-crm/getData.php.org1 /usr/lib/lx-office-crm/getData.php
else
	echo "Error getData.php.org1 nicht gefunden"
fi 

# Links löschen
if [ -f /usr/lib/lx-office-crm/ti.php ]; then
 	rm /usr/lib/lx-office-crm/ti.php
else
	echo "Error Link ti.php existiert nicht"
fi 

if [ -f /usr/lib/lx-office-crm/tiAnruf.php ]; then
	rm /usr/lib/lx-office-crm/tiAnruf.php
else
	echo "Error Link tiAnruf.php existiert nicht"
fi

 ## Icons bleiben..



echo "done!!"

exit 0