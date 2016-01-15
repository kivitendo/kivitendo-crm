<?php
require_once( "inc/stdLib.php" );
$keys = array(
    'klicktel_key_db',
    'klicktel_key',
    'ttpart',
    'tttime',
    'ttround',
    'ttclearown',
    'GEODB',
    'BLZDB',
    'CallDel',
    'CallEdit',
    'Expunge',
    'MailFlag',
    'logmail',
    'dir_group',
    'dir_mode',
    'sep_cust_vendor',
    'listLimit',
    'logfile',
    'streetview_man',
    'planspace_man',
);
//foreach ( $keys as $value ) {
    //$_SESSION[$value] = $value == 'dir_mode' ? octdec( $_POST[$value] ) : $_POST[$value];
//}
if ( $_POST['save'] ) {
    $save = true;
    if ( isset( $_POST['ttpart'] ) && $_POST['ttpart'] != '' ) {
        $sql = "SELECT count(*) as cnt FROM parts WHERE partnumber = '".$_POST['ttpart']."'";
        $rs = $GLOBALS['dbh']->getOne( $sql );
        if ( $rs['cnt'] == 0 ) {
            $msg = "Artikel nicht gefunden";
            $save = false;
        }
        elseif ( $rs['cnt'] > 1 ) {
            $msg = "Artikelnummer nicht eindeutig";
            $save = false;
        }
    }
    if ( $save ) {
        $last = $GLOBALS['dbh']->getOne( 'SELECT max(id) as id FROM crmdefaults' );
        $insert = "INSERT INTO crmdefaults (key,val,grp,employee) VALUES (?,?,'mandant',".$_SESSION['loginCRM'].")";
        $data = array( );
        foreach ( $keys as $row ) {
            $data[] = array(
                $row,
                $_POST[$row],
            );
        };
        //Werte in SESSION spreichern
        unset( $_POST['save'] );
        foreach( $_POST as $key => $value ){
            $_SESSION[$key] = $value;
        }
        $rc = $GLOBALS['dbh']->executeMultiple( $insert, $data );
        if ( $rc ) {
            $msg = 'Sichern erfolgt';
            if ( $last['id'] ) {
                $sql = "DELETE FROM crmdefaults WHERE grp = 'mandant' and id <= ".$last['id'];
                $rc = $GLOBALS['dbh']->query( $sql );
            }
            $GLOBALS['dbh']->commit( );
        }
        else {
            $GLOBALS['dbh']->rollback( );
            $msg = 'Sichern fehlgeschlagen';
        }
    }
}
$sql = "SELECT * FROM crmdefaults WHERE grp = 'mandant'";
$rs = $GLOBALS['dbh']->getAll( $sql );
$data = array( );
if ( $rs )
    foreach ( $rs as $row )
        $data[$row['key']] = $row['val'];
foreach ( $keys as $row ) {
    if ( !isset( $data[$row] ) ) {
        if ( isset( $ { $row } ) ) {
            $data[$row] = $ { $row };
        }
        else {
            $data[$row] = '';
        };
    };
};
include( "inc/template.inc" );
$t = new Template( $base );
doHeader( $t );
$t->set_file( array( "mand" => "mandant.tpl" ) );
if ( $_SESSION['Admin'] != 1 ) {
    $t->set_var( array( msg => 'Diese Aktion ist nicht erlaubt. </ br>Sie sind nicht Mitglied der Gruppe Admin.', hide => 'hidden' ) );
}
else {
    $t->set_var( array( 'klicktel_key' => $data['klicktel_key'] ? $data['klicktel_key'] : '95d5a5f8d8ef062920518592da992cba',
                        'klicktel_key_db' => $data['klicktel_key'],
                        'GEODB' => ( $data['GEODB'] == 't' ) ? 'checked' : '',
                        'BLZDB' => ( $data['BLZDB'] == 't' ) ? 'checked' : '',
                        'CallEdit' => ( $data['CallEdit'] == 't' ) ? 'checked' : '',
                        'CallDel' => ( $data['CallDel'] == 't' ) ? 'checked' : '', $data['MailFlag'] => 'selected',
                        'Expunge' => ( $data['Expunge'] == 't' ) ? 'checked' : '',
                        'logmail' => ( $data['logmail'] == 't' ) ? 'checked' : '',
                        'streetview_man' => $data['streetview_man'],
                        'planspace_man' => $data['planspace_man'],
                        'ttpart' => $data['ttpart'],
                        'tttime' => $data['tttime'],
                        'ttround' => $data['ttround'],
                        'ttclearown' => ( $data['clearown'] == 't' ) ? 'checked' : '',
                        'dir_group' => $data['dir_group'],
                        'dir_mode' => $data['dir_mode'],
                        'sep_cust_vendor' => ( $data['sep_cust_vendor'] == 't' ) ? 'checked' : '',
                        'listLimit' => $data['listLimit'],
                        'showErr' => ( $data['showErr'] == 't' ) ? 'checked' : '',
                        'logfile' => ( $data['logfile'] == 't' ) ? 'checked' : '',
                        'crmpath' => $_SESSION['crmpath'],
                        'msg' => $msg, ) );
}
$t->pparse( "out", array( "mand" ) );
?>
