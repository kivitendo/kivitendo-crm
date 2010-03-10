-- @tag: sonderflag
-- @description: Sonderflags vom conf.php

CREATE TABLE sonderflag (
    svalue int,
    skey text,
    sorder int
);
ALTER TABLE sonderflag ADD  primary key (svalue);
ALTER TABLE sonderflag ADD CONSTRAINT benutzereingabe unique (skey);

-- @php: *
$i=1;
foreach ($cp_sonder as $key=>$val) {
    $sql = "insert into sonderflag (svalue,skey,sorder) values ($key,'$val',$i)";
    echo $sql;
    $rc=$db->query($sql);
    echo "$key:$val:$i:$rc<br>";
    $i++;
}
-- @exec: *

