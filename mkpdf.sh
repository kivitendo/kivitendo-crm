#!/bin/bash
if ! [ -f  tmp/katalog.tex ]; then
    echo tmp/katalog.tex nicht gefunden.
    echo Ist das das CRM-Verzeichnis?
    exit -1
fi;
echo 
echo Achtung!!
echo Es werden mit diesem User Dateien im Verzeichnis tmp erstellt.
echo Der Webserver ist danach evtl. nicht mehr in der Lage 
echo einen Katalog zu erstellen.
echo Entweder die Dateirechte ändern und diese Dateien entfernen!
echo 
pdflatex -interaction=batchmode -output-directory=tmp/ tmp/katalog.tex
if [ $? -eq 0 ]; then
    echo 
    pdflatex -interaction=batchmode -output-directory=tmp/ tmp/katalog.tex
    echo 
    if [ -f  tmp/katalog.pdf ]; then
        echo tmp/katalog.pdf erstellt.
        exit 0
    else
        echo tmp/katalog.pdf nicht erstellt.
        exit -1
    fi 
fi;
echo Probleme bei der PDF-Erstellung __$?__!!
exit -1;
