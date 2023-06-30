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
        writeLog( $data['name'] );
        // sql command
        $sql = "SELECT gender FROM firstnameToGender WHERE firstname ILIKE '".$data['name']."'";
        writeLog( 'meine Query: '.$sql );
        // search for $sql
        $rs = $GLOBALS['dbh']->getOne( $sql, true );
        writeLog($rs);
        // returns $rs as resonse
        echo $rs;
    }

    function getZipCode($data){
        writeLog($data['zipcode']);
        $sql = "SELECT ort FROM zip_to_city WHERE zipcode = '".$data['zipcode']."'";
        $rs = $GLOBALS['dbh']->getAll( $sql, true );
        echo $rs;
    }

    function f_state($data){
        $sql = "SELECT federal_state FROM zip_to_city WHERE zipcode = '".$data['zipcode']."'";
        $rs = $GLOBALS['dbh']->getOne($sql, true);
        echo $rs;
    }

    function street($data){
        $sql = "SELECT street FROM streets_germany WHERE street ILIKE '".$data['street']."%' AND locality = '".$data['locality']."'";
        $rs = $GLOBALS['dbh']->getAll($sql, true);
        echo $rs;
    }

?>