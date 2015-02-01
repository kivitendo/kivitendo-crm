#!/bin/bash
set +e

##Dialog Passwd
dialog --clear --title "Dialog Password" --inputbox "Password eingeben" 10 50 2>/tmp/kivitendo_passwd.$$ kivitendo
PASSWD=`cat /tmp/kivitendo_passwd.$$`

##Dialog Directory
DIR=/var/www
dialog --clear --title "Dialog Installationsverzeichnis" --inputbox "Pfad ohne abschließenden Slash eingenben" 10 50 2>/tmp/kivitendo_dir.$$ /var/www
DIR=`cat /tmp/kivitendo_dir.$$`
rm -f /tmp/kivitendo*


## Memo und Script zur Installation von Kivitendo-ERP unter Ubuntu 14.04
echo "Pakete installieren"
apt-get install apache2 libapache2-mod-fastcgi libarchive-zip-perl libclone-perl libconfig-std-perl libdatetime-perl libdbd-pg-perl libdbi-perl libemail-address-perl libemail-mime-perl libfcgi-perl libjson-perl liblist-moreutils-perl libnet-smtp-ssl-perl libnet-sslglue-perl libparams-validate-perl libpdf-api2-perl librose-db-object-perl librose-db-perl librose-object-perl libsort-naturally-perl libstring-shellquote-perl libtemplate-perl libtext-csv-xs-perl libtext-iconv-perl liburi-perl libxml-writer-perl libyaml-perl libfile-copy-recursive-perl libgd-gd2-perl libimage-info-perl postgresql-9.3 git perl-doc texlive-base-bin texlive-latex-recommended texlive-fonts-recommended texlive-latex-extra texlive-lang-german texlive-generic-extra libapache2-mod-php5 php5-gd php5-imap php-mail php-mail-mime php-pear php-mdb2 php-mdb2-driver-pgsql php-fpdf libfpdi-php imagemagick ttf-freefont php5-curl tinymce libphp-jpgraph

cpan HTML::Restrict
pear install  Contact_Vcard_Build Contact_Vcard_Parse

echo "Virtuellen Host anlegen"
if [ -f /etc/apache2/sites-available/kivitendeo.apache2.conf ]; then
    echo "Lösche vorherigen Virtuellen Host"
    rm -f /etc/apache2/sites-available/kivitendeo.apache2.conf
fi
touch /etc/apache2/sites-available/kivitendeo.apache2.conf
echo "AddHandler fcgid-script .fpl
AliasMatch ^/kivitendo/[^/]+\.pl $DIR/kivitendo-erp/dispatcher.fcgi
Alias       /kivitendo/          $DIR/kivitendo-erp/

<Directory $DIR/kivitendo-erp>
  AllowOverride All
  Options ExecCGI Includes FollowSymlinks
  DirectoryIndex login.pl
  AddDefaultCharset UTF-8
  Require all granted
</Directory>

<Directory $DIR/kivitendo-erp/users>
  Require all denied
</Directory>
" >>  /etc/apache2/sites-available/kivitendeo.apache2.conf
ln -sf /etc/apache2/sites-available/kivitendeo.apache2.conf /etc/apache2/sites-enabled/kivitendeo.apache2.conf
service apache2 restart

echo "postgres Password ändern"
sudo -u postgres -H -- psql -d template1 -c "ALTER ROLE postgres WITH password '$PASSWD'"

echo "config/kivitendo.conf erzeugen"
cp -f $DIR/kivitendo-erp/config/kivitendo.conf.default $DIR/kivitendo-erp/config/kivitendo.conf

echo "kivitendo.conf bearbeiten"
sed -i 's/admin_password.*$/admin_password = kivitendo/' $DIR/kivitendo-erp/config/kivitendo.conf
sed -i 's/password =$/password = kivitendo/' $DIR/kivitendo-erp/config/kivitendo.conf

cd $DIR
git clone https://github.com/kivitendo/kivitendo-erp.git
git clone https://github.com/kivitendo/kivitendo-crm.git

chown -R www-data: *
cd kivitendo-erp/
ln -s ../kivitendo-crm/ crm

dialog --title "Datenbank installieren" --backtitle "Linux Shell Script Tutorial Example" --yesno "Möchten Sie die Beispiel-Datenbank installieren?" 7 60

response=$?
case $response in
   0) echo "Datenbank wird installiert."
      sudo -u postgres -H -- createdb kivitendo_auth
      sudo -u postgres -H -- createdb demo-db
      sudo -u postgres -H -- psql kivitendo_auth $DIR/kivitendo-crm/install/kivitendo_auth.sql
      sudo -u postgres -H -- psql demo-db $DIR/kivitendo-crm/install/demo-db.sql
      ;;
   1) echo "Datenbank wird nicht installiert."
      ;;
esac
