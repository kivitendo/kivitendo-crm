#!/bin/bash
VER="1.5.0beta"
#Jedes neue Paket der gleichen Version bekommt eine eigene Nummer
NR="0"

#hier wurde das Git-Paket entpakt:
SRC=/var/www/lx-office-crm
#hier wird das Debian-Paket gebaut:
DEST=/tmp/package/lx-office-crm_$VER-$NR-all

mkdir -p $DEST
cd $DEST

#Struktur anlegen:
cp -a $SRC/DEBIAN/* .
rm ./mk*.sh
rm README

#Dateien kopieren:
#aber keine fertigen Konfigurationen, nur *.default
cp -a $SRC/inc usr/lib/lx-office-crm
cp -a $SRC/crmajax usr/lib/lx-office-crm
cp -a $SRC/services usr/lib/lx-office-crm
cp -a $SRC/tools usr/lib/lx-office-crm
cp -a $SRC/update usr/lib/lx-office-crm
cp -a $SRC/css var/lib/lx-office-crm
cp -a $SRC/dokumente var/lib/lx-office-crm
cp -a $SRC/tpl var/lib/lx-office-crm
cp -a $SRC/vorlage var/lib/lx-office-crm
cp -a $SRC/hilfe/* usr/share/doc/lx-office-crm/
cp -a $SRC/image/* usr/share/lx-office-crm
cp -a $SRC/*.* usr/lib/lx-office-crm

#Git- und dummy-files löschen
find . -name ".git*" -exec rm -rf {} \;
find . -name ".dummy" -exec rm -rf {} \;

#Die Geodaten nicht ins Paket.
rm usr/lib/lx-office-crm/update/geodaten.sql.gz

#Rechte setzen
chown -R www-data: usr/lib/lx-office-crm
chown -R www-data: var/lib/lx-office-crm

#MD5 Summe bilden:
find usr/ -name "*" -type f -exec md5sum {} \; > DEBIAN/md5sum
find var/ -name "*" -type f -exec md5sum {} \; >> DEBIAN/md5sum

#Größe feststellen:
SIZE=`du -scb . | grep insgesamt | cut -f1`

#Controlfile updaten:
cat DEBIAN/control | sed --expression "s/Installed-Size: 0/Installed-Size: $SIZE/g" > DEBIAN/1.tmp
mv DEBIAN/1.tmp DEBIAN/control
cat DEBIAN/control | sed --expression "s/Version: 0/Version: $VER-$NR/g" > DEBIAN/1.tmp
mv DEBIAN/1.tmp DEBIAN/control
#Revisionsnummer evtl. von Hand eintragen

#Paket bauen:
cd ..
dpkg-deb --build lx-office-crm_$VER-$NR-all

echo "Done"
