<?php

/****************************************************
* saveUserStamm
* in: val = array
* out: rc = boolean
* AnwenderDaten sichern
* !! in eine andere Lib verschieben
*****************************************************/
function saveUserStamm($val) {
global $db;
    if (!$val["interv"]) $val["interv"]=60;
    if (!$val["ssl"]) $val["ssl"]='f';
    if (!$val["proto"]) $val["proto"]='1';
    if (!$val["port"]) $val["port"]=($val["proto"]=='1')?'143':'110';
    if (!$val["termseq"]) $val["termseq"]=30;
    if ($val["vertreter"]==$val["uid"]) {$vertreter="null";} else {$vertreter=$val["vertreter"];};
    $fld = array('name','etikett','addr1','addr2','addr3','workphone','homephone','notes','termbegin','termend',
                'msrv','port','proto','ssl','postf','mailuser','kennw','postf2','interv','pre','abteilung','position',
                'mailsign','email','icalart','icaldest','icalext','preon','streetview','planspace','theme','feature_ac',
                'feature_ac_minlength','feature_ac_delay','auftrag_button','angebot_button','rechnung_button','zeige_extra',
                'zeige_lxcars','tinymce');
    $sql  = "update employee set ";
    foreach ($fld as $key) {
        if ($val[$key]<>"") {
            $sql .= $key."='".$val[$key]."',";
        } else {
            $sql .= $key."=null,";
        }
    }
    $sql .= "vertreter=$vertreter, kdview=".$val["kdview"];
    $sql .= ",termseq=".$val["termseq"]." where id=".$val["uid"];
    $rc=$db->query($sql);
    if ($val["homephone"]) mkTelNummer($val["uid"],"E",array($val["homephone"]));
    if ($val["workphone"]) mkTelNummer($val["uid"],"E",array($val["workphone"]));
}

/****************************************************
* getAllUser
* in: sw = array(Art,suchwort)
* out: rs = array(Felder der db)
* hole alle Anwender
*****************************************************/
function getAllUser($sw) {
global $db;
        if (!$sw[0]) { $where="workphone like '".$_SESSION['Pre'].$sw[1]."%' or homephone like '".$_SESSION['Pre'].$sw[1]."%' "; }
        else { $where="(name ilike '$Pre".$sw[1]."%') or (login  ilike '$Pre".$sw[1]."%')"; }
        $sql="select * from employee where $where and employee.deleted = false";
        $rs=$db->getAll($sql);
        if(!$rs) {
            $rs=false;
        };
        return $rs;
}


/****************************************************
* getUserStamm
* in: id = int
* out: daten = array
* AnwenderDaten holen
* !! in eine andere Lib verschieben
*****************************************************/
function getUserStamm($id) {
global $db;
    $sql="select * from employee where id=$id";
    $rs=$db->getAll($sql);
    if(!$rs) {
        return false;
    } else {
        $sql="select  * from gruppenname N left join grpusr G on G.grpid=N.grpid  where usrid=$id";
        $rs2=$db->getAll($sql);
        $daten = $rs[0];
        if ($rs[0]["vertreter"]) {
            $sql="select * from employee where id=".$rs[0]["vertreter"];
            $rs3=$db->getAll($sql);
            $daten["vname"]=$rs3[0]["login"]." ".$rs3[0]["name"];
        }
        $daten["gruppen"]=$rs2;
        return $daten;
    }
}

function getGruppen() {
global $db;
    $sql="select * from gruppenname order by grpname";
    $rs=$db->getAll($sql);
    if(!$rs) {
        return false;
    } else {
        return $rs;
    }
}

function delGruppe($id) {
global $db;
    $sql="select count(*) as cnt from customer where owener = $id";
    $rs=$db->getAll($sql);
    $cnt=$rs[0]["cnt"];
    $sql="select count(*) as cnt from vendor   where owener = $id";
    $rs=$db->getAll($sql);
    $cnt+=$rs[0]["cnt"];
    $sql="select count(*) as cnt from contacts where cp_owener = $id";
    $rs=$db->getAll($sql);
    $cnt+=$rs[0]["cnt"];
    if ($cnt===0) {
        $sql="delete from grpusr where grpid=$id";
        $rc=$db->query($sql);
        if(!$rc) return "Mitglieder konnten nicht gel&ouml;scht werden";
        $sql="delete from gruppenname where grpid=$id";
        $rc=$db->query($sql);
        if(!$rc) return "Gruppe konnte nicht gel&ouml;scht werden";
        return "Gruppe gel&ouml;scht";
    } else {
        return "Gruppe wird noch benutzt.";
    }
}

function saveGruppe($data) {
global $db;
    if (strlen($data["name"])<2) return "Name zu kurz";
    $newID=uniqid (rand());
    $sql="insert into gruppenname (grpname,rechte) values ('$newID','".$data["rechte"]."')";
    $rc=$db->query($sql);
    if ($rc) {
        $sql="select * from gruppenname where grpname = '$newID'";
        $rs=$db->getAll($sql);
        if(!$rs) {
            return "Fehler beim Anlegen";
        } else {
            $sql="update gruppenname set grpname='".$data["name"]."' where grpid=".$rs[0]["grpid"];
            $rc=$db->query($sql);
            if(!$rc) {
                return "Fehler beim Anlegen";
            }
            return "Gruppe angelegt";
        }
    } else { return "Fehler beim Anlegen"; }
}

function getMitglieder($gruppe) {
global $db;
    $sql="select * from employee left join grpusr on usrid=id where grpid=$gruppe and employee.deleted = false ORDER BY employee.id";
    $rs=$db->getAll($sql);
    if(!$rs) {
        return false;
    } else {
        return $rs;
    }
}

function saveMitglieder($mitgl,$gruppe) {
global $db;
    $sql="delete from grpusr where grpid=$gruppe";
    $rc=$db->query($sql);
    if ($mitgl) {
        foreach($mitgl as $row) {
            $sql="insert into grpusr (grpid,usrid) values ($gruppe,$row)";
            $rc=$db->query($sql);
        }
    }
}

function getOneGrp($id) {
global $db;
    $sql="select grpname from gruppenname where grpid=$id";
    $rs=$db->getAll($sql);
    if(!$rs) {
        return false;
    } else {
        return $rs[0]["grpname"];
    }
}
?>
