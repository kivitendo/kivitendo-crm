#!/bin/bash
set +e

if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root" 1>&2
   exit 1
fi

## Memo und Script zur Installation von kivitendo unter Ubuntu 18.04 Bionic (LTS)
echo "Pakete installieren"
apt-get update && apt-get upgrade
apt-get install make gcc apache2 libapache2-mod-fcgid libarchive-zip-perl libclone-perl libconfig-std-perl libdatetime-perl libdbd-pg-perl libdbi-perl libemail-address-perl libemail-mime-perl libfcgi-perl libjson-perl liblist-moreutils-perl libnet-smtp-ssl-perl libnet-sslglue-perl libparams-validate-perl libpdf-api2-perl librose-db-object-perl librose-db-perl librose-object-perl libsort-naturally-perl libstring-shellquote-perl libtemplate-perl libtext-csv-xs-perl libtext-iconv-perl liburi-perl libxml-writer-perl libyaml-perl libfile-copy-recursive-perl libgd-gd2-perl libimage-info-perl libalgorithm-checkdigits-perl postgresql git perl-doc libapache2-mod-php php-gd php-imap php-mail php-mail-mime php-pgsql php-fpdf imagemagick fonts-freefont-ttf php-curl dialog php-enchant aspell-de libcgi-pm-perl libdatetime-set-perl libfile-mimeinfo-perl liblist-utilsby-perl libpbkdf2-tiny-perl libregexp-ipv6-perl libtext-unidecode-perl libdaemon-generic-perl libfile-flock-perl libfile-slurp-perl libset-crontab-perl python3 python3-serial

a2enmod cgi

cpan HTML::Restrict
cpan CGI
cpan Mozilla::CA

pear install  Contact_Vcard_Build Contact_Vcard_Parse


dialog --title "Latex installieren" --backtitle "kivitendo installieren" --yesno "Möchten Sie Latex installieren?" 7 60


response=$?
case $response in
   0) echo "Latex wird installiert."
      apt-get install texlive-base-bin texlive-latex-recommended texlive-fonts-recommended texlive-latex-extra texlive-lang-german texlive-generic-extra
      ;;
   1) echo "Latex wird nicht installiert."
      ;;
esac

##Dialog Passwd
dialog --clear --title "Dialog Password" --backtitle "kivitendo installieren" --inputbox "Achtung, Password in Beispieldatenbank bleibt unverändert. (kivitendo)" 10 50 2>/tmp/kivitendo_passwd.$$ kivitendo
PASSWD=`cat /tmp/kivitendo_passwd.$$`

##Dialog Directory
DIR=/var/www
dialog --clear --title "Dialog Installationsverzeichnis" --backtitle "kivitendo installieren" --inputbox "Pfad ohne abschließenden Slash eingenben" 10 50 2>/tmp/kivitendo_dir.$$ /var/www
DIR=`cat /tmp/kivitendo_dir.$$`
rm -f /tmp/kivitendo*


cd $DIR
git clone https://github.com/kivitendo/kivitendo-erp.git
git clone https://github.com/kivitendo/kivitendo-crm.git

echo "ERP-Plugins installieren"
sed -i '$adocument.write("<script type='text/javascript' src='crm/js/ERPplugins.js'></script>")' kivitendo-erp/js/kivi.js


echo "Virtuellen Host anlegen"
if [ -f /etc/apache2/sites-available/kivitendo.apache2.conf ]; then
    echo "Lösche vorherigen Virtuellen Host"
    rm -f /etc/apache2/sites-available/kivitendo.apache2.conf
fi
touch /etc/apache2/sites-available/kivitendo.apache2.conf
echo "AddHandler fcgid-script .fpl
AliasMatch ^/kivitendo/[^/]+\.pl $DIR/kivitendo-erp/dispatcher.fcgi
Alias       /kivitendo/          $DIR/kivitendo-erp/
<Directory $DIR/kivitendo-erp>
  AllowOverride All
  Options ExecCGI Includes FollowSymlinks
  AddHandler cgi-script .py
  DirectoryIndex login.pl
  AddDefaultCharset UTF-8
  Require all granted
</Directory>
<Directory $DIR/kivitendo-erp/users>
  Require all denied
</Directory>
<Directory $DIR/kivitendo-crm>
  AddDefaultCharset UTF-8
  Require all denied
</Directory>
" >>  /etc/apache2/sites-available/kivitendo.apache2.conf
ln -sf /etc/apache2/sites-available/kivitendo.apache2.conf /etc/apache2/sites-enabled/kivitendo.apache2.conf
service apache2 restart

echo "postgres Password ändern"
sudo -u postgres -H -- psql -d template1 -c "ALTER ROLE postgres WITH password '$PASSWD'"

echo "config/kivitendo.conf erzeugen"
cp -f $DIR/kivitendo-erp/config/kivitendo.conf.default $DIR/kivitendo-erp/config/kivitendo.conf

echo "kivitendo.conf bearbeiten"
sed -i "s/admin_password.*$/admin_password = $PASSWD/" $DIR/kivitendo-erp/config/kivitendo.conf
sed -i "s/password =$/password = $PASSWD/" $DIR/kivitendo-erp/config/kivitendo.conf


chown -R www-data: *
cd $DIR/kivitendo-erp/
ln -s ../kivitendo-crm/ crm

##Menü verlinken oder kopieren:
cd $DIR/kivitendo-erp/menus/user
ln -s ../../../kivitendo-crm/menu/10-crm-menu.yaml 10-crm-menu.yaml

##Rechte für CRM ermöglichen:
cd $DIR/kivitendo-erp/sql/Pg-upgrade2-auth
ln -s  ../../../kivitendo-crm/update/add_crm_master_rights.sql add_crm_master_rights.sql

##Übersetzungen anlegen:
cd $DIR/kivitendo-erp/locale/de
mkdir more
ln -s ../../../../kivitendo-crm/menu/t8e/menu.de crm-menu.de
ln -s ../../../../kivitendo-crm/menu/t8e/menu-admin.de crm-menu-admin.de

var=$(git tag | xargs -I@ git log --format=format:"%ai @%n" -1 @ | sort | awk '{print $4,v++,"off"}' | tail -n 8)
_temp="/tmp/answer.$$"

dialog --backtitle "ERP-Version wählen, ESC für Git" --radiolist "Wähle Tag der ausgecheckt werden soll, ESC für aktuelle Git-Version!" 20 50 8 $var 2>$_temp
result=`cat $_temp`

gitlog=$(git log -1 --pretty=oneline --abbrev-commit)

if [ -z "$result" ]; then
     dialog --title "Aktuelle Git" --msgbox "Aktuelle Entwicklerversion:\n$gitlog" 8 66
else
    dialog --title "Ausgewählter Tag" --msgbox "$result wird ausgecheckt!" 6 44
    git checkout $result
fi


dialog --title "Datenbank installieren" --backtitle "kivitendo installieren" --yesno "Möchten Sie die Beispiel-Datenbank für Version 3.2.x installieren?" 7 60
response=$?
case $response in
    0) echo "Datenbank wird installiert."
    sudo -u postgres -H -- createdb kivitendo_auth
    sudo -u postgres -H -- createdb demo-db
    sudo -u postgres -H -- psql kivitendo_auth < $DIR/kivitendo-crm/install/kivitendo_auth.sql
    sudo -u postgres -H -- psql demo-db < $DIR/kivitendo-crm/install/demo-db.sql
    echo "Beim Login: Benutzername: demo, Password: kivitendo"
    echo "***************************************************"
    if [ "$PASSWD" != "kivitendo" ]; then
        echo "Es wurde ein eigenes Passwort vergeben."
        echo "Dieses Passwort muss in der Mandantenkonfiguration eingetragen werden!"
        echo "(http://localhost/kivitendo/admin.pl)"
    fi
    ;;
    1) echo "Datenbank wird nicht installiert."
       ;;
esac



echo "......Installation beendet"
echo ""
echo "kivitendo kann jetzt im Browser unter http://localhost/kivitendo/ aufgerufen werden"
