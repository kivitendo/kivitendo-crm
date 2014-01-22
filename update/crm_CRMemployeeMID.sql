-- @tag: CRMemployeeMID
-- @description: Mandantenid je User

ALTER TABLE crmemployee DROP COLUMN ceid;
ALTER TABLE crmemployee ADD COLUMN manid int4;

-- @php: *
$rc = $_SESSION['db']->begin();
$rc = $_SESSION['db']->query('UPDATE crmemployee SET manid = '.$_SESSION['manid']);
$_SESSION['db']->commit();
return true;
-- @exec: *
