-- @tag: sonderflag2
-- @description: Sonderflags in vendor + customer durch benutzerdefinierte Variablen ersetzen

-- @php: *
$sql = "select * from sonderflag"; 
$sf = $db->getAll($sql);
$sql = "insert into custom_variable_configs (name,description,type,module,searchable,includeable,sortkey) ";
$sql.= "values ('%s','%s','bool','CT','t','t',(select max(sortkey)+1 from custom_variable_configs))";
if ($sf) {
  foreach ($sf as $r) {
    $key = strtolower(preg_replace("/[^a-zA-Z_\d]+/","",$r["skey"]));
    $sql2 = sprintf($sql,$key,$r["skey"]);
    $rc=$db->query($sql2);
    $rs = $db->getOne("select id from custom_variable_configs where name = '$key'");
    $val[$r["svalue"]] = $rs["id"];
    $sflag .= $r["skey"].":".$r["svalue"]." ";
  };
  echo "Flags: ".$sflag;
  $tabs = array("customer","vendor");
  foreach ($tabs as $t) {
      $sql = "select id,sonder from ".$t." where sonder > 0";
      $crs = $db->getAll($sql);
      if ($crs) foreach ($crs as $r) {
         foreach ($val as $k=>$v) {
           if (($r["sonder"] & $k) == $k) {
              $sql = "insert into custom_variables (config_id,trans_id,bool_value) ";
              $sql.= "values ($v,".$r["id"].",'t')";
              $rc = $db->query($sql);
           };
         };
      };
  };
  $sql = "select cp_id,cp_sonder from contacts where cp_sonder > 0";
  $rs = $db->getAll($sql);
  if ($rs) foreach ($rs as $r) {
    $rc = $db->query("update contacts set cp_notes = cp_notes || ' $sflag = ".$r["cp_sonder"]."' where cp_id = ".$r["cp_id"]);
  }
}
-- @exec: *

ALTER TABLE customer DROP COLUMN sonder;
ALTER TABLE vendor DROP COLUMN sonder;
ALTER TABLE contacts DROP COLUMN cp_sonder;
DROP TABLE sonderflag;
