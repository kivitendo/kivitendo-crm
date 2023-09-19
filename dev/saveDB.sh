#! /bin/bash -e
# Skript zum sichern einer Postgresql-DB als root

DATABASE="autoprofis"
TMPDIR="/mytmp"

echo "Datenbank: $DATABASE  wird in: $TMPDIR gesichert"

su - postgres -c "pg_dump $DATABASE > $TMPDIR/$DATABASE.sql"

echo "Erledigt"
