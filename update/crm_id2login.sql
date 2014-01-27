-- @tag: id2login
-- @description: Pfadnamen von ID auf Login umstellen
-- @php: *
$db =  $_SESSION['db']->getAll('SELECT id,login FROM employee');
$return = 1;
if ( $db ) foreach( $db as $row ) {
    echo $_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname'].'/'.$row['id']." ";
    if ( file_exists($_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname'].'/'.$row['id']) ) {
        echo $_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname'].'/'.$row['login'];
        //Altes Verzeichnis gibt es
        if ( file_exists($_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname'].'/'.$row['login']) ) {
            echo " move<br>";
            //neues auch
            chdir(file_exists($_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname'].'/'.$row['id']));
            $src = glob('*');
            chdir(file_exists($_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname']));
            foreach( $src as $file ) {
                $rc = rename($_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname'].'/'.$row['id'].'/'.$file,
                             $_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname'].'/'.$row['login'].'/'.$file);
            };
            if ( $rc ) {
                unlink($_SESSION['crmpath'].'/'.$_SESSION['dbname'].'/'.$row['id']);
            } else {
                echo "Verzeichnist konnte nicht korrekt umbenannt werden<br>";
            };
        } else {
            echo " rename<br>";
            //Neues Verzeichnis gibt es nicht, umbenennen
            $rc = rename($_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname'].'/'.$row['id'],
                         $_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname'].'/'.$row['login']);
            if ( !$rc ) echo "Verzeichnist konnte nicht angelegt werden<br>";
        }
        $rc = $_SESSION['db']->query("UPDATE documents SET pfad = '".$row['login']."' WHERE pfad = '".$row['id']."'");
        if ( !$rc ) echo "Probleme beim Update der Pfadnamen<br>";
    } else {
        if ( !file_exists($_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname'].'/'.$row['login']) ) { 
            //weder altes noch neues Verzeichnis vorhanen
            echo "<br>".getcwd()."<br>";
            echo $_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname'].'/'.$row['login']."<br>";
            $rc = mkdir($_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname'].'/'.$row['login']);
            if ( !$rc ) echo "Verzeichnis konnte nicht erstellt werden <br>";
        } else {
            echo "<br>";
        }
    };
    if ( $_SESSION['dir_group'] ) 
        chgrp($_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname'].'/'.$row['login'], $_SESSION['dir_group']);
    if ( $_SESSION['dir_mode'] ) 
        chmod($_SESSION['crmpath'].'/dokumente/'.$_SESSION['dbname'].'/'.$row['login'],$_SESSION['dir_mode']);
};
return $return;
-- @exec: *

