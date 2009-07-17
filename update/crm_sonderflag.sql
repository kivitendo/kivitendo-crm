-- @tag: sonderflag
-- @description: Sonderflags vom conf.php

CREATE TABLE sonderflag (
    svalue int,
    skey text,
    sorder int
);

-- @php: *
$i=1;
foreach ($cp_sonder as $key=>$val) {
    $rc=$db->query("insert into sonderflag (svalue,skey,sorder) values ($key,'$val',$i)");
    echo "$key:$val:$i:$rc<br>";
    $i++;
}
-- @exec: *

