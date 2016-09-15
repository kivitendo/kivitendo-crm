<?php
require_once __DIR__.'/../inc/ajax2function.php';

function showVersion(){
     $rs = $GLOBALS['dbh']->getOne( "select * from crm order by  version DESC, datum DESC" );
     echo json_encode( $rs );
}

function saveDBs( $showResult = TRUE ){
    $date = date( 'Y-m-d\TH-i-s' );
    $dbAuthName = $_SESSION['erpConfig']['authentication/database']['db'];
    $dbName     = $_SESSION['dbData']['dbname'];
    $fileName[$dbAuthName] = $dbAuthName.'-'.$date.'.sql';
    $fileName[$dbName]     = $dbName.'-'.$date.'.sql';
    exec( "ls -t ".__DIR__."/../db_dumps/*  | sed '1,12d' | xargs rm -r" );
    exec( 'pg_dump '.$dbAuthName.' > '.__DIR__.'/../db_dumps/'.$fileName[$dbAuthName].' && pg_dump '.$dbName.' > '.__DIR__.'/../db_dumps/'.$fileName[$dbName], $output, $error );
    if( $showResult ) echo !$error ? json_encode( $fileName ) : '';
}

function showDbFiles(){
    $fileNameArray = scandir( __DIR__.'/../db_dumps' );
    $remove = preg_grep("/^\.{1,2}|.{1,}~|.{1,}#|\.gitignore$/", $fileNameArray);
    foreach( $remove as $remValue ) unset( $fileNameArray[array_search( $remValue, $fileNameArray )] );
    echo json_encode( $fileNameArray );
}

function updateDB(){
    //Prüfen ob Version in DB kleiner ist als die Version in version.php
    if( !needUpdate() ){
        echo json_encode( array( 'Database is up to date!' ) );;
        return;
    }
    //Datenbanken sichern
    saveDBs( $output = FALSE ); //ToDo: uncomment
    //Alle Dateien in ./db_update in einen Array laden
    $fileNameArray = scandir( __DIR__.'/../db_update' );

    //Anzahl Dateien in db_update
    $countFiles = count($fileNameArray); 

    $remove = preg_grep("/^\.{1,2}|.{1,}~|.{1,}#|\.gitignore$/", $fileNameArray);
    foreach( $remove as $remValue ) unset( $fileNameArray[array_search( $remValue, $fileNameArray )] ); //Sicherheitskopien enfernen
    //Inhalt der Dateien in Array laden
    $dbSchema = $GLOBALS['dbh']->getAll( "select * FROM schema_info" );


    //TODO: Prüfung einbauen, ob die Anzahl der Dateien in db_update mit der Anzahl der Tags in der Tabelle schema_info übereinstimmt
    // und wenn Anzahl in schema_info < Anzahl Dateien, die fehlenden Tags mit INSERT INTO eintragen lassen 
        
    
    foreach( $dbSchema as $key => $value ) $dbSchemaTags[$key] = $value['tag'];//alle Tags in einem flachen Array
    foreach( $fileNameArray as $key => $fileName ){
        $nTag = $nVersion = $lastVersion = $currentVersion = 0;
        $update[$fileName] = file( __DIR__.'/../db_update/'.$fileName, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES );//Array mit Lines in update[fileName] laden
        foreach( $update[$fileName] as $line ){
            if( $tag =  trim( varExist( explode( '@tag:', $line ), 1 ) ) ){ //Tags extraieren
                $nTag++;
                if( in_array( 'crm_'.$tag, $dbSchemaTags ) ) unset( $fileNameArray[$key] ); //schon vorhandene Tags löschen
            }
            if( $fileVersion = trim( varExist( explode( '@version:', $line ),1 ) ) ){ //Version extraieren
                $nVersion++;
                $currentVersion = (int)str_replace( '.', '', $fileVersion );
                if( $lastVersion == $currentVersion ){
                    echo json_encode( array('Doppelte Versionsnummer in  file: '.$fileName.' !') );
                    return;
                }
                if( $currentVersion  <= (int)str_replace( '.', '', VERSION ) && $fileNameArray[$key] ) $fileNameArray[$currentVersion] = $fileNameArray[$key];//kein Update wenn fileversion > VERSION
                unset( $fileNameArray[$key] );
            }
            $lastVersion = $currentVersion;
        }
        if( $nTag != 1 || $nVersion > 1 ){
            echo json_encode( array('Error in file: '.$fileName.' !') );
            return;
        }
    }
    ksort( $fileNameArray ); //nach Version sortieren
    //in $fileNameArray stehen nun die Dateien die zum Update benötigt werden, sortiert
    $comment_patterns = array(
        '/\/\*.*(\n)*.*(\*\/)?/', //C comments
        '/\s*--.*\n/',            //inline comments start with --
        '/\s*#.*\n/',             //inline comments start with #
    );
    foreach( $fileNameArray as $key => $fileName ){ //Dateien die zum Db-Update benutzt werden
        $update[$fileName] = file( __DIR__.'/../db_update/'.$fileName,  FILE_SKIP_EMPTY_LINES  );//Array mit Lines in update[fileName] laden
        $tag = $des = $ver = 0;
        $execPhp = FALSE;
        $sql = $code = '';
        $GLOBALS['dbh']->begin();
        foreach( $update[$fileName] as $key => $line ){
            if( !$tag ) $tag = trim( varExist( explode( '@tag:', $line ), 1 ) );
            if( !$des ) $des = trim( varExist( explode( '@description:', $line ), 1 ) );
            if( !$ver ) $ver = trim( varExist( explode( '@version:', $line ), 1 ) );
            if( strpos( $line, '@php:' ) === FALSE  ){ //vor und nach Zeile @php
                if( !$execPhp ){ //SQL-Code
                    $sql .= $line;
                    if( strpos( $line, ';' ) !== FALSE ){
                        $sql = preg_replace( $comment_patterns, "", $sql );
                        if( $GLOBALS['dbh']->exec( $sql ) === FALSE ){
                            echo json_encode( array( 'SQL-Error in '.$fileName.'<br> SQL-Code: '.$sql ) );
                            return;
                        }
                        $sql = '';
                    }

                }
                else $code .= $line; //Php-Code
            }
            else $execPhp = TRUE;
        }
        //Tag in Schema_info inserten
        $GLOBALS['dbh']->exec( "INSERT INTO schema_info ( tag, login ) VALUES ( 'crm_".$tag."', '".$_SESSION['userConfig']['login']."')" );
        //Versionsnummer der CRM setzen
        $GLOBALS['dbh']->exec( "INSERT INTO  crm ( uid, datum, version ) VALUES ( ".$_SESSION['userConfig']['id'].", now(), '".$ver."' )" );
        $GLOBALS['dbh']->commit();
        if( $code ){ //ist Php-Code vorhanden
            $code = preg_replace( $comment_patterns, "", $code ); // -- exec * entfernen
            //writeLog( 'Php-Code: '.$code );
            if( eval( $code ) === FALSE ){
                echo json_encode( array( 'Php-Error in '.$fileName.' code: '.$code ) );
                return;
            }
        }
    }
    echo json_encode( count( $fileNameArray ) ? $fileNameArray : array( 'Database is up to date!' ) );
}
?>
