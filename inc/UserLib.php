<?php
/****************************************************
* saveUserStamm
* in: val = array
* out: rc = boolean
* AnwenderDaten sichern
* !! in eine andere Lib verschieben
*****************************************************/
function saveUserStamm( $val ) {
    // Prüfen ob crmemployee schon existiert, sonst wird bei einer frischen DB ein unschöner Fehler ausgegeben.
    // Besser: crmemployee vordem Aufruf von saveUserStamm() erstellen ToDo!
    $crm_exist = $_SESSION['db']->getOne( "SELECT count(*) FROM information_schema.tables WHERE table_name = 'crmemployee'");
    if ( !(bool) $crm_exist['count']) return false;
    if ( !$val["interv"] )
        $val["interv"] = 60;
    if ( !$val["ssl"] )
        $val["ssl"] = 'f';
    if ( !$val["proto"] )
        $val["proto"] = 't';
    if ( !$val["port"] )
        $val["port"] = ( $val["proto"] == 't' ) ? '143' : '110';
    if ( !$val["termseq"] )
        $val["termseq"] = 30;
    if ( $val["vertreter"] == $val["uid"] ) {
        $vertreter = "null";
    }
    else {
        $vertreter = $val["vertreter"];
    };
    $std = array(
        'name',
    );
    $fld = array(
        'msrv'                    => 't',
        'postf'                   => 't',
        'kennw'                   => 't',
        'postf2'                  => 't',
        'mailsign'                => 't',
        'email'                   => 't',
        'mailuser'                => 't',
        'port'                    => 'i',
        'proto'                   => 't',
        'ssl'                     => 't',
        'addr1'                   => 't',
        'addr2'                   => 't',
        'addr3'                   => 't',
        'workphone'               => 't',
        'homephone'               => 't',
        'notes'                   => 't',
        'abteilung'               => 't',
        'position'                => 't',
        'interv'                  => 'i',
        'pre'                     => 't',
        'preon'                   => 'b',
        'vertreter'               => 'i',
        'etikett'                 => 'i',
        'termbegin'               => 'i',
        'termend'                 => 'i',
        'termseq'                 => 'i',
        'kdviewli'                => 'i',
        'kdviewre'                => 'i',
        'searchtab'               => 'i',
        'icalart'                 => 't',
        'icaldest'                => 't',
        'icalext'                 => 't',
        'deleted'                 => 'b',
        'streetview'              => 't',
        'planspace'               => 't',
        'streetview_default'      => 'b',
        'theme'                   => 't',
        'smask'                   => 't',
        'helpmode'                => 'b',
        'listen_theme'            => 't',
        'auftrag_button'          => 'b',
        'angebot_button'          => 'b',
        'rechnung_button'         => 'b',
        'liefer_button'           => 'b',
        'zeige_extra'             => 'b',
        'zeige_lxcars'            => 'b',
        'zeige_karte'             => 'b',
        'zeige_tools'             => 'b',
        'zeige_etikett'           => 'b',
        'zeige_bearbeiter'        => 'b',
        'feature_ac'              => 'b',
        'feature_ac_minlength'    => 'i',
        'feature_ac_delay'        => 'i',
        'feature_unique_name_plz' => 'b',
        'sql_error'               => 'b',
        'php_error'               => 'b',
        'external_mail'           => 'b',
        'zeige_dhl'               => 'b',
        'data_from_tel'           => 'b',
        'tinymce'                 => 'b',
        'search_history'          => 't',
        'mandsig'                 => 't'
    );
    foreach ( $fld as $key => $value )
        $_SESSION[$key] = isset( $val[$key] ) ? $val[$key] : '';
    //Einstellungen nach dem Sichern gleich übernehmen (ohne neues Login)

    //Sollte besser clientseitig abgefragt werden! etwa so wenn streetview_default dann nehme streetview_man sonst streetview
    /*if( $_SESSION['streetview_default'] ){
        $client_data = getClientData();
        $_SESSION['streetview'] = $client_data['streetview_man'] ? $client_data['streetview_man'] : 'http://maps.google.de/maps?f=d&hl=de&saddr=Alexanderplatz+7,10178+Berlin&daddr=%TOSTREET%,%TOZIPCODE%+%TOCITY%';
    }*/
    if ( isset( $_SESSION['sql_error'] ) && $_SESSION['sql_error'] )
        $_SESSION['db']->setShowError( true );
    else
        $_SESSION['db']->setShowError( false );
    $sql = "update employee set ";
    foreach ( $std as $key ) {
        if ( $val[$key] <> "" ) {
            $sql .= $key."='".$val[$key]."',";
        }
        else {
            $sql .= $key."=null,";
        }
    }
    $sql = substr( $sql, 0, - 1 );
    $sql .= ' where id='.$val['uid'];
    $rc = $_SESSION['db']->query( $sql );
    if ( $val["homephone"] )
        mkTelNummer( $val["uid"], "E", array( $val["homephone"] ) );
    if ( $val["workphone"] )
        mkTelNummer( $val["uid"], "E", array( $val["workphone"] ) );
    $rc = $_SESSION['db']->begin( );
    $rc = $_SESSION['db']->query( 'DELETE FROM crmemployee WHERE uid = '.$val["uid"].' AND manid = '.$_SESSION['manid'] );
    if ( $rc )
        foreach ( $fld as $key => $typ ) {
            if ( array_key_exists( $key, $val ) ) {
                $sql = 'INSERT INTO crmemployee (manid,uid,key,val,typ) VALUES ('.$_SESSION['manid'].','.$val['uid'].",'$key','".$val[$key]."','$typ')";
        }
        else {
            $sql = 'INSERT INTO crmemployee (manid,uid,key,val,typ) VALUES ('.$_SESSION['manid'].','.$val['uid'].",'$key',null,'$typ')";
        }
        $rc = $_SESSION['db']->query( $sql );
        if ( !$rc ) {
            $_SESSION['db']->rollback( );
            $rc = false;
            break;
        }
    }
    if ( $rc ) {
        $rc = $_SESSION['db']->commit( );
        return true;
    }
    return false;
}
/****************************************************
* getAllUser
* in: sw = array(Art,suchwort)
* out: rs = array(Felder der db)
* hole alle Anwender
*****************************************************/
function getAllUser( $sw ) {
    // print_r($sw);
    $Pre = $_SESSION['pre'];
    if ( !$sw[0] ) {
        $where = "workphone like '".$_SESSION['pre'].$sw[1]."%' or homephone like '".$_SESSION['pre'].$sw[1]."%' ";
    }
    else {
        $where = "(name ilike '".$sw[1]."%') or (login  ilike '".$sw[1]."%')";
    }
    $sql = "select * from employee where $where and employee.deleted = false";
    $rs = $_SESSION['db']->getAll( $sql );
    if ( !$rs ) {
        $rs = false;
    }
    return $rs;
}
/****************************************************
* getUserStamm
* in: id = int
* out: daten = array
* AnwenderDaten holen
* !! in eine andere Lib verschieben
*****************************************************/
function getUserStamm( $id, $login = false ) {
    if ( $login ) {
        $sql = "select * from employee where login = '$login'";
    }
    else {
        $sql = "select * from employee where id=$id";
    }
    $daten = $_SESSION['db']->getOne( $sql );
    $id = $daten['id'];
    if ( !$daten ) {
        return false;
    }
    else {
        $sql = "select  * from gruppenname N left join grpusr G on G.grpid=N.grpid  where usrid=$id";
        $rs2 = $_SESSION['db']->getAll( $sql );
        $daten["gruppen"] = $rs2;
        //Mandanteneinstellungen
        $sql = "SELECT key,val FROM crmdefaults WHERE grp = 'mandant'";
        $rs = $_SESSION['db']->getAll( $sql );
        if ( $rs )
            foreach ( $rs as $row ) {
                $daten[$row['key']] = $row['val'];
        }
        //Usereinstellungen
        $sql = "SELECT * FROM crmemployee WHERE uid = $id AND manid = ".$_SESSION['manid'];
        $rs = $_SESSION['db']->getAll( $sql );
        if ( $rs ) {
            foreach ( $rs as $row ) {
                if ( $row['typ'] == 'i' ) {
                    $daten[$row['key']] = (int) $row['val'];
                }
                elseif ( $row['typ'] == 'f' ) {
                    $daten[$row['key']] = (float) $row['val'];
                }
                elseif ( $row['typ'] == 'b' ) {
                    $daten[$row['key']] = ( $row['val'] == 't' ) ? true : false;
                }
                else {
                    $daten[$row['key']] = $row['val'];
                }
            }
        }
        else { //neuer Benutzer hat ja noch keine Einträge in crmemployee
            loadUserDefaults($id);
        }
        if ( $daten["vertreter"] ) {
            $sql = "select * from employee where id=".$daten["vertreter"];
            $rs3 = $_SESSION['db']->getOne( $sql );
            $daten["vname"] = ( $rs3['name'] != '' ) ? $rs3["name"] : $rs3["login"];
        };
        $sql = "SELECT signature FROM defaults";
        $rs = $_SESSION['db']->getOne($sql);
        if ( $rs ) $daten['msignature'] = $rs['signature'];
        return $daten;
    }
}
function getGruppen( $user = false ) {
    if ( $user ) {
        $sql = "select 'G'||grpid as id, grpname as name from gruppenname order by grpname";
    }
    else {
        $sql = "select * from gruppenname order by grpname";
    }
    $rs = $_SESSION['db']->getAll( $sql );
    if ( !$rs ) {
        if ( $user ) {
            return array( );
        }
        else {
            return false;
        }
    }
    else {
        return $rs;
    }
}
function delGruppe( $id ) {
    $sql = "select count(*) as cnt from customer where owener = $id";
    $rs = $_SESSION['db']->getOne( $sql );
    $cnt = $rs["cnt"];
    $sql = "select count(*) as cnt from vendor   where owener = $id";
    $rs = $_SESSION['db']->getOne( $sql );
    $cnt += $rs["cnt"];
    $sql = "select count(*) as cnt from contacts where cp_owener = $id";
    $rs = $_SESSION['db']->getOne( $sql );
    $cnt += $rs["cnt"];
    if ( $cnt === 0 ) {
        $sql = "delete from grpusr where grpid=$id";
        $rc = $_SESSION['db']->query( $sql );
        if ( !$rc )
            return "Mitglieder konnten nicht gel&ouml;scht werden";
        $sql = "delete from gruppenname where grpid=$id";
        $rc = $_SESSION['db']->query( $sql );
        if ( !$rc )
            return "Gruppe konnte nicht gel&ouml;scht werden";
        return "Gruppe gel&ouml;scht";
    }
    else {
        return "Gruppe wird noch benutzt.";
    }
}
function saveGruppe( $data ) {
    if ( strlen( $data["name"] ) < 2 )
        return "Name zu kurz";
    $newID = uniqid( rand( ) );
    $sql = "insert into gruppenname (grpname,rechte) values ('$newID','".$data["rechte"]."')";
    $rc = $_SESSION['db']->query( $sql );
    if ( $rc ) {
        $sql = "select * from gruppenname where grpname = '$newID'";
        $rs = $_SESSION['db']->getOne( $sql );
        if ( !$rs ) {
            return "Fehler beim Anlegen";
        }
        else {
            $sql = "update gruppenname set grpname='".$data["name"]."' where grpid=".$rs["grpid"];
            $rc = $_SESSION['db']->query( $sql );
            if ( !$rc ) {
                return "Fehler beim Anlegen";
            }
            return "Gruppe angelegt";
        }
    }
    else {
        return "Fehler beim Anlegen";
    }
}
function getMitglieder( $gruppe ) {
    $sql = "select * from employee left join grpusr on usrid=id where grpid=$gruppe and employee.deleted = false ORDER BY employee.id";
    $rs = $_SESSION['db']->getAll( $sql );
    if ( !$rs ) {
        return false;
    }
    else {
        return $rs;
    }
}
function saveMitglieder( $mitgl, $gruppe ) {
    $sql = "delete from grpusr where grpid=$gruppe";
    $rc = $_SESSION['db']->query( $sql );
    if ( $mitgl ) {
        foreach ( $mitgl as $row ) {
            $sql = "insert into grpusr (grpid,usrid) values ($gruppe,$row)";
            $rc = $_SESSION['db']->query( $sql );
        }
    }
}
function getOneGrp( $id ) {
    $sql = "select grpname from gruppenname where grpid=$id";
    $rs = $_SESSION['db']->getOne( $sql );
    if ( !$rs ) {
        return false;
    }
    else {
        return $rs["grpname"];
    }
}
/*******************************************************************************************************
*** Lädt die Benutzerdaten wenn noch keine Daten in crmemployee existieren. Alte DB oder neuer User. ***
*******************************************************************************************************/
function loadUserDefaults($id){
    $val = array(
        "streetview"                => "https://maps.google.de/maps?f=d&hl=de&saddr=Ensingerstrasse+19,89073+Ulm&daddr=%TOSTREET%,%TOZIPCODE%+%TOCITY%",
        "planspace"                 => "+",
        "streetview_default"        => "t",
        "msrv"                      => "your_mail_server",
        "mailsign"                  => "--Your Signature",
        "mailuser"                  => "your_mail_login",
        "port"                      => "143",
        "proto"                     => "t",
        "ssl"                       => "f",
        'mandsig'                   => '0',
        "interv"                    => "60",
        "pre"                       => "%",
        "preon"                     => "t",
        "termbegin"                 => "8",
        "termend"                   => "20",
        "termseq"                   => "30",
        "kdviewli"                  => "3",
        "kdviewre"                  => "3",
        "searchtab"                 => "1",
        "theme"                     => "blue-style",
        "auftrag_button"            => "t",
        "angebot_button"            => "t",
        "rechnung_button"           => "t",
        "liefer_button"             => "t",
        "zeige_extra"               => "t",
        "zeige_lxcars"              => "f",
        "zeige_karte"               => "t",
        "zeige_tools"               => "t",
        "zeige_etikett"             => "t",
        "zeige_bearbeiter"          => "t",
        "feature_ac"                => "t",
        "feature_ac_minlength"      => "2",
        "feature_ac_delay"          => "100",
        "feature_unique_name_plz"   => "t",
        "sql_error"                 => "f",
        "tinymce"                   => "t",
        "uid"                       => $id
    );
    saveUserStamm( $val );
}
/* überflüssig
function getClientData(){
    $sql = "SELECT * FROM crmdefaults WHERE grp = 'mandant'";
    $rs = $_SESSION['db']->getAll( $sql );
    $data = array();
    if ( $rs ) foreach ( $rs as $row ) $data[$row['key']] = $row['val'];
    //printArray( $data );
}
*/

?>
