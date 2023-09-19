#! /bin/bash -e
# Skript zum Erzeugen einer Postgresql-DB aus einem SQL-Dump als root

DATABASE="autoprofis"
TMPDIR="/mytmp"

echo "Postgresql und Apache neu starten"

service postgresql restart
service apache2 restart

echo "Datenbank: $DATABASE  wird aus: $TMPDIR/$DATABASE.sql erzeugt"

su - postgres -c "dropdb $DATABASE"
su - postgres -c "createdb $DATABASE"
su - postgres -c "psql $DATABASE < $TMPDIR/$DATABASE.sql"

echo "Erledigt....."
