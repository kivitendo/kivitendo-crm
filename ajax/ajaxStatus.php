<?php
require_once __DIR__.'/../inc/ajax2function.php';

function showVersion(){
     $rs = $GLOBALS['dbh']->getOne( "select * from crm order by  version DESC, datum DESC" );
     echo json_encode( $rs );
}

function saveDBs( $output = TRUE ){
    $date = date( 'Y-m-d\TH-i-s' );
    $dbAuthName = $_SESSION['erpConfig']['authentication/database']['db'];
    $dbName     = $_SESSION['dbData']['dbname'];
    $fileName[$dbAuthName] = $dbAuthName.'-'.$date.'.sql';
    $fileName[$dbName]     = $dbName.'-'.$date.'.sql';
    exec( "ls -t ".__DIR__."/../db_dumps/*  | sed '1,12d' | xargs rm -r" );
    exec( 'pg_dump '.$dbAuthName.' > '.__DIR__.'/../db_dumps/'.$fileName[$dbAuthName].' && pg_dump '.$dbName.' > '.__DIR__.'/../db_dumps/'.$fileName[$dbName], $output, $error );
    if( $output ) echo !$error ? json_encode( $fileName ) : '';
}

function showDbFiles(){
    $fileArray = scandir( __DIR__.'/../db_dumps' );
    $remove = array( '.', '..', '.gitignore' );
    foreach( $remove as $remValue ) unset( $fileArray[array_search( $remValue, $fileArray )] );
    echo json_encode( $fileArray );
}

function updateDB(){
    //Prüfen ob Version in DB kleiner ist als die Version in version.php
    if( !needUpdate() ){
        echo json_encode( array( 'Database is up to date!' ) );;
        return;
    }
    //Datenbanken sichern
    //saveDBs( $output = FALSE );
    //Alle Dateien in db_update in einen Array laden
    $fileNameArray = scandir( __DIR__.'/../db_update' );
    $remove = array( '.', '..', '~' );
    foreach( $remove as $remValue ) unset( $fileNameArray[array_search( $remValue, $fileNameArray )] );
    //Inhalt der Dateien in Array laden
    $dbSchema = $GLOBALS['dbh']->getAll( "select * FROM schema_info" );
    foreach( $dbSchema as $key => $value ) $dbSchemaTags[$key] = $value['tag'];//alle Tags in einem flachen Array
    foreach( $fileNameArray as $key => $fileName ){
        $nTag = 0;//Anzahl von  @tag
        $update[$fileName] = file( __DIR__.'/../db_update/'.$fileName );//Array mit Lines in update[fileName] laden
        foreach( $update[$fileName] as $line ){

            if( $tag = trim( explode( '@tag:', $line )[1] ) ){ //Tags extraieren
                $nTag++;
                if( in_array( 'crm_'.$tag, $dbSchemaTags ) ) unset( $fileNameArray[$key] );
            }

        }
        if( $nTag != 1 ){
            echo json_encode( array('Tag error in file: '.$fileName.' !') );
            return;
        }
    }
    writeLog( $fileNameArray );
    echo json_encode( $fileNameArray );
}
?>