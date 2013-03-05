<?php
	require_once("inc/stdLib.php");
    $keys = array('ttpart','tttime','ttround','ttclearown',
                  'GEODB','BLZDB','CallDel','CallEdit',
                  'Expunge','MailFlag','logmail',
                  'dir_group','dir_mode','sep_cust_vendor',
                  'listLimit','tinymce','showErr','logfile',
                  );
if ( $_POST['save'] ) {
    $save = true;
    if ( isset($_POST['ttpart']) && $_POST['ttpart'] != '') {
        $sql = "SELECT count(*) as cnt FROM parts WHERE partnumber = '".$_POST['ttpart']."'";
        $rs  = $_SESSION['db']->getOne($sql);
        if ( $rs['cnt'] == 0 ) {
            $msg = "Artikel nicht gefunden";
            //$save = false;
        } else if ( $rs['cnt'] > 1 ) {
            $msg = "Artikelnummer nicht eindeutig";
            //$save = false;
        }
    }
    if ( $_POST['dir_mode'] != '' ) $_POST['dir_mode'] = octdec($_POST['dir_mode']);
    if ( $save ) {
        $last = $_SESSION['db']->getOne('SELECT max(id) as id FROM crmdefaults');
        $insert = "INSERT INTO crmdefaults (key,val,employee) VALUES (?,?,".$_SESSION['loginCRM'].")";
        $data = array();
        foreach ($keys as $row) { $data[] = array($row,$_POST[$row]); };
        $rc = $_SESSION['db']->executeMultiple($insert,$data);
        if ( $rc ) {
            $msg = 'Sichern erfolgt';
            if ( $last['id'] ) {
                $sql = 'DELETE FROM crmdefaults WHERE id <= '.$last['id'];
                $rc = $_SESSION['db']->query($sql);
            }
            $_SESSION['db']->commit();
        } else {
            $_SESSION['db']->rollback();
            $msg = 'Sichern fehlgeschlagen';
        }
    }
}
    $sql = "SELECT * FROM crmdefaults";
    $rs  = $_SESSION['db']->getAll($sql);
    $data = array();
    if ( $rs ) foreach ( $rs as $row ) $data[$row['key']] = $row['val'];
    foreach ( $keys as $row ) { 
        if ( ! isset( $data[$row] ) ) {
            if ( isset( ${$row} ) ) {
                $data[$row] = ${$row};
            } else {
                $data[$row] = '';
            };
        };
    };
	include("inc/template.inc");
    $t = new Template($base);
    doHeader($t);
    $t->set_file(array("mand" => "mandant.tpl"));
    if ( $_SESSION['CRMTL'] != 1 ) {
        $t->set_var(array(
            msg => 'Nicht erlaubt',
            hide => 'hidden'
        ));
    } else {
        $t->set_var(array(
            GEODB       => ($data['GEODB'] == 't')?'checked':'',
            BLZDB       => ($data['BLZDB'] == 't')?'checked':'',
            CallEdit    => ($data['CallEdit'] == 't')?'checked':'',
            CallDel     => ($data['CallDel'] == 't')?'checked':'',
            $data['MailFlag'] => 'selected',
            Expunge     => ($data['Expunge'] == 't')?'checked':'',
            logmail     => ($data['logmail'] == 't')?'checked':'',
            ttpart      => $data['ttpart'], 
            tttime      => $data['tttime'],
            ttround     => $data['ttround'],
            ttclearown  => ($data['clearown'] == 't')?'checked':'',
            dir_group   => $data['dir_group'],
            dir_mode    => decoct($data['dir_mode']),
            sep_cust_vendor     => ($data['sep_cust_vendor'] == 't')?'checked':'',
            tinymce     => ($data['tinymce'] == 't')?'checked':'',
            listLimit   => $data['listLimit'],
            showErr     => ($data['showErr'] == 't')?'checked':'',
            logfile     => ($data['logfile'] == 't')?'checked':'',
            msg         => $msg,

        ));
    }
    $t->pparse("out",array("mand"));
?>
