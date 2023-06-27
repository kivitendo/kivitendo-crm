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


  function getData(){
      //alle Datensätze bereitstellen
      $rs = $GLOBALS[ 'dbh' ]->getAll( 'SELECT * FROM example', true );//getOne
      writeLog( 'hello' );
      echo $rs;

  }

function firstnameToGender( $data ){
    writeLog( $data['name'] );
    $sql = " SELECT gender FROM firstnameToGender WHERE firstname ILIKE '".$data['name']."'";
    writeLog( 'meine Query: '.$sql );
    $rs = $GLOBALS['dbh']->getOne( $sql, true);
      writeLog($rs);
    echo $rs;
     //'F'||'M'
}


  ?>