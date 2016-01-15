-- @tag: CRMemployeeMID
-- @description: Mandantenid je User

ALTER TABLE crmemployee DROP COLUMN ceid;
ALTER TABLE crmemployee ADD COLUMN manid int4;

-- @php: *
$rc = $GLOBALS['dbh']->begin();
$rc = $GLOBALS['dbh']->query('UPDATE crmemployee SET manid = '.$_SESSION['manid']);
$GLOBALS['dbh']->commit();
return true;
-- @exec: *
