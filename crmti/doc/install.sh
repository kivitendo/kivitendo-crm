#!/bin/bash
#set -x

echo "Willkommen bei der CRMTI-Installation"
sleep 3

chown -R www-data: /usr/lib/lx-office-crm/crmti

## Prufen ob schon *.org-Files existieren
## wenn nicht CRM-Files in *.org umbenennen 
## fur selbige - Links zu den entsprechenden von CRMTI erweiterten Dateien erzeugen 

## TelefonIcon erstellen
if [ -f /usr/lib/lx-office-erp/xslt/xulmenu.xsl.org ]; then
 	echo "Error xulmenu.xsl.org existiert bereits"
else
	mv /usr/lib/lx-office-erp/xslt/xulmenu.xsl /usr/lib/lx-office-erp/xslt/xulmenu.xsl.org
	ln -s /usr/lib/lx-office-crm/crmti/xulmenu.xsl /usr/lib/lx-office-erp/xslt/xulmenu.xsl
	echo "xulmenu.xsl als xulmenu.xsl.org gesichert"
fi



##Menu bereiten

if [ -f /usr/lib/lx-office-erp/menu.ini.org1 ]; then
 	echo "Error menu.ini.org1 existiert bereits"
else
 	mv /usr/lib/lx-office-erp/menu.ini /usr/lib/lx-office-erp/menu.ini.org1
	ln -s /usr/lib/lx-office-crm/crmti/menu.ini /usr/lib/lx-office-erp/menu.ini
	echo "menu.ini als menu.ini.org1 gesichert"
	echo "Wenn LxCars installiert ist lxc_menu.ini benützen"
 fi



## AutoComplete 
if [ -f /usr/lib/lx-office-crm/tpl/firmen3.tpl.org1 ]; then
 	echo "Error firmen3.tpl.org existiert bereits"
 	else
 		mv /usr/lib/lx-office-crm/tpl/firmen3.tpl /usr/lib/lx-office-crm/tpl/firmen3.tpl.org1
		ln -s /usr/lib/lx-office-crm/crmti/tpl/firmen3.tpl  /usr/lib/lx-office-crm/tpl/firmen3.tpl 
	echo "firmen3.tpl als firmen3.tpl.org1 gesichert"
 fi

## Suche erweitern
if [ -f /usr/lib/lx-office-crm/getData.php.org1 ]; then
 	echo "Error getData.php.org existiert bereits"
 	else
 		mv /usr/lib/lx-office-crm/getData.php /usr/lib/lx-office-crm/getData.php.org1
		ln -s /usr/lib/lx-office-crm/crmti/getData.php  /usr/lib/lx-office-crm/getData.php 
	echo "getData.php als getData.php.org1 gesichert"
 fi

## Links anlegen
if [ -f /usr/lib/lx-office-crm/ti.php ]; then
 	echo "Error ti.php existiert bereits"
 	else
 		ln -s /usr/lib/lx-office-crm/crmti/ti.php  /usr/lib/lx-office-crm/ti.php 
	echo "Link zu crmti/ti.php erzeugt"
 fi
 
 if [ -f /usr/lib/lx-office-crm/tiAnruf.php ]; then
 	echo "Error tiAnruf.php existiert bereits"
 	else
 		ln -s /usr/lib/lx-office-crm/crmti/tiAnruf.php  /usr/lib/lx-office-crm/tiAnruf.php 
	echo "Link zu crmti/tiAnruf.php erzeugt"
 fi

## Icons furs Menu
cp /usr/lib/lx-office-crm/crmti/image/icons/16x16/*  /usr/lib/lx-office-erp/image/icons/16x16/
cp /usr/lib/lx-office-crm/crmti/image/icons/24x24/*  /usr/lib/lx-office-erp/image/icons/24x24/
cp /usr/lib/lx-office-crm/crmti/image/icons/32x32/*  /usr/lib/lx-office-erp/image/icons/32x32/

chown -R www-data: /usr/lib/lx-office-*
 
 
echo "WICHTIG INSTALL.TXT in lx-office-crm/crmti lesen!"

exit 0
