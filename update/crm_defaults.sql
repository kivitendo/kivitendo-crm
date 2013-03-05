-- @tag: defaults
-- @description: Einstellungen für die CRM
CREATE TABLE crmdefaults (
    id integer DEFAULT nextval('crmid'::text) NOT NULL,
    employee integer NOT NULL DEFAULT -1,
    key text,
    val text,
    modify timestamp without time zone DEFAULT NOW()
);
-- @php: *
echo 'Variablen in DB schreiben<br>';
$keys = array('ttpart','tttime','ttround','ttclearown','GEODB','BLZDB','CallDel','CallEdit','Expunge','MailFlag','logmail','dir_group','dir_mode','sep_cust_vendor','listLimit','showErr','logfile');
$sql = "insert into crmdefaults (key,val,employee) values ('%s','%s',".$_SESSION['loginCRM'].")";
foreach ($keys as $row ) {
    $rc=$_SESSION['db']->query( sprintf( $sql, $row, $GLOBALS[$row] ) );
    echo "$row:".$GLOBALS[$row].":$rc<br>";
};
echo 'inc/conf.php umbenennen<br>';
$conf = file('inc/conf.php');
rename('inc/conf.php','inc/conf.php.'.date('Ymd'));
$save = array('ERPNAME','ERP_BASE_URL','erpConfigFile');
echo 'Neue inc/conf.php erstellen<br>';
$fp = fopen('inc/conf.php','w');
fputs($fp,"<?php\n");
foreach ($save as $key) {
    fputs($fp,'$'.$key.'="'.$GLOBALS[$key].'";'."\n");
}
fputs($fp,"?>");
fclose($fp);
-- @exec: *

