-- @tag: TelCallTermin
-- @description: Bezug zur Termintabelle 
-- @depends: termincat

ALTER TABLE telcall ADD COLUMN termin_id integer;

-- @php: *
    $rs=$GLOBALS['dbh']->getAll("SELECT id,cause,calldate FROM telcall where kontakt = 'X'");
    $i=1;
    if ($rs) foreach ($rs as $row) {
        $day = substr($row["calldate"],0,10);
        $time = substr($row["calldate"],11,5);
        $rc=$GLOBALS['dbh']->myquery("UPDATE telcall SET termin_id = (select id from termine where starttag = '$day' and startzeit = '$time') where id = ".$row["id"]);
        echo ".";
        $i++;
    };
    echo "<br>$i updates<br>";
-- @exec: *

