<?php 
    require_once __DIR__.'/../inc/ajax2function.php';

  
    function newEntry( $data ){
        $data = json_decode( $data );
        $data = ( array ) $data;
          $rs = $GLOBALS[ 'dbh' ]->insert( 'example', array( 'date_time', 'c_name', 'c_age', 'c_comments' ), array( $data['datetime'], $data['name'],$data['age'], $data['comments']) );
          echo 1;
  }


    function firstnameToGender( $data ){
        // sql command
        $sql = "SELECT gender FROM firstnameToGender WHERE firstname ILIKE '".$data['name']."'";
        // search for $sql
        $rs = $GLOBALS['dbh']->getOne( $sql, true );
        // returns $rs as resonse
        echo $rs;
    }

    function getZipCode($data){
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

    function getcurrencies(){
        $sql = "SELECT id FROM currencies";
        $rs = $GLOBALS['dbh']->getAll($sql, true);
        //$rs = $GLOBALS['dbh']->getAll( 'SELECT * from currencies ORDER BY id = ( SELECT currency_id FROM DEFAULTS ) DESC' );
        echo $rs;
    }

?>