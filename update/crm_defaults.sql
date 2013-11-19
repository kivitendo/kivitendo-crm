-- @tag: defaults
-- @description: Einstellungen für die CRM
CREATE TABLE crmdefaults (
    id integer DEFAULT nextval('crmid'::text) NOT NULL,
    employee integer NOT NULL DEFAULT -1,
    key text,
    val text,
    grp char(10),
    modify timestamp without time zone DEFAULT NOW()
);
-- @php: *
echo 'Variablen in DB schreiben<br>';
$GLOBALS['streetview']=$GLOBALS['stadtplan'];
$keys = array('ttpart','tttime','ttround','ttclearown','GEODB','BLZDB','CallDel','CallEdit','Expunge','MailFlag','logmail','dir_group','dir_mode','sep_cust_vendor','listLimit','streetview','planspace','showErr','logfile','kicktel_API','google_API');
$sql = "insert into crmdefaults (key,val,grp,employee) values ('%s','%s','mandant',".$_SESSION['loginCRM'].")";
if ( !isset($GLOBALS['listLimit']) or $GLOBALS['listLimit'] < 100 ) $GLOBALS['listLimit'] = 200; 
if ( !isset($GLOBALS['dir_mode'])  ) $GLOBALS['dir_mode']  = '0775'; 
if ( !isset($GLOBALS['dir_group']) ) $GLOBALS['dir_group'] = 'users'; 
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
