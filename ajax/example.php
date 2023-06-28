<?php 
    require_once __DIR__.'/../inc/ajax2function.php';

  
    function newEntry( $data ){
        writeLog( $data );
        $data = json_decode( $data );
        $data = ( array ) $data;
        writeLog($data);
          $rs = $GLOBALS[ 'dbh' ]->insert( 'example', array( 'date_time', 'c_name', 'c_age', 'c_comments' ), array( $data['datetime'], $data['name'],$data['age'], $data['comments']) );
          echo 1;
  }


    function firstnameToGender( $data ){
        // schreibt in die Logs einer Datei
        writeLog( $data['name'] );
        // Datenbank Befehl
        $sql = "SELECT gender FROM firstnameToGender WHERE firstname ILIKE '".$data['name']."'";
        // gibt den Befehl in der Logdatei aus
        writeLog( 'meine Query: '.$sql );
        // sucht in der Datenbank nach $sql
        $rs = $GLOBALS['dbh']->getOne( $sql, true );
        // schreibt das Ergebnis der Suche aus
        writeLog($rs);
        // gibt das Ergebnis der Suche zurück
        echo $rs;
        //'F'||'M'
    }

?>