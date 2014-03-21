<?php
    require_once("inc/stdLib.php");
    include("inc/crmLib.php");
    include_once("inc/UserLib.php");    
    $usr=getUserStamm($_SESSION["loginCRM"]);
    $ALabels=getLableNames();
    $freitext=$_POST["freitext"];
    if (!$_POST["format"] || empty($_POST["format"])) {
        $_POST["format"]=$usr["etikett"];
    }
    if (!$_POST["format"]) {
        $form=$ALabels[0]["id"];
    } else {
        $form=$_POST["format"]; 
    }
    $label=getOneLable($form);
    if ($_POST["print"] ) {
        $lableformat=array("paper-size"=>$label["papersize"],'name'=>$label["name"], 'metric'=>$label["metric"], 
                            'marginLeft'=>$label["marginleft"], 'marginTop'=>$label["margintop"], 
                            'NX'=>$label["nx"], 'NY'=>$label["ny"], 'SpaceX'=>$label["spacex"], 
                            'SpaceY'=>$label["spacey"],
                            'width'=>$label["width"], 'height'=>$label["height"], 'font-size'=>6);
        require_once('inc/PDF_Label.php');
        $tmp=explode(":",$_POST["xy"]);
        $SX=substr($tmp[0],1);
        $SY=substr($tmp[1],1);
        $pdf = new PDF_Label($lableformat, $label["metric"], $SX, $SY);
        $pdf->Open(); 
        unset($tmp);
        if ($SX<>1 or $SY<>1)    $pdf->AddPage();
        $daten = getCSVData();
        if ($daten == false){   // Fehler beim Laden der Daten, entsprechend Abbrechen
            echo "Fehler beim Laden der Werte bitte Fehlermeldung unter tmp/lxcrm.log prüfen";
            exit;
        }
        if (is_array($daten)) {
            $felder=array_shift($daten);
            $felder=explode(":",$felder["csvdaten"]);
            $felder[]="TEXT";
            foreach ($daten as $row) {
                $data=explode(":",$row["csvdaten"]);
                $data[]=$_POST["text"];
                unset($tmp);
                foreach ($label["Text"] as $row) {
                    preg_match_all("/%([A-Z0-9_]+)%/U",$row["zeile"],$ph, PREG_PATTERN_ORDER);
                    if ($ph) {
                        $first=true;
                        $oder=strpos($row["zeile"],"|");
                        $ph=array_slice($ph,1);
                        if ($ph[0]) { foreach ($ph as $x) {
                            foreach ($x as $u) {
                                $p=array_search($u,$felder);
                                if ($p!==false) { $y=$data[$p]; } else { $y=""; }
                                if ($y<>"" and $first) {
                                    //$y=utf8_decode($y);
                                    $row["zeile"]=str_replace("%".$u."%",$y,$row["zeile"]);
                                    if ($oder>0) $first=false;
                                } else {
                                    $row["zeile"]=str_replace("%".$u."%","",$row["zeile"]);
                                }
                            }
                        }};
                    };
                    if ($oder>0) $row["zeile"]=str_replace("|","",$row["zeile"]);
                    if ($row["zeile"]<>"!") {
                        if ($row["zeile"][0]=="!") {
                            $text=substr($row["zeile"],1);
                        } else {
                            $text=$row["zeile"];
                        }
                        $tmp[]=array("text"=>utf8_decode($text),"font"=>$row["font"]);
                        //$tmp[]=array("text"=>$text,"font"=>$row["font"]);
                    }
                };
                $pdf->Add_PDF_Label2($tmp);
            }
            $pdf->Output();
            exit;
        }
    }
?>
<html>
<head><title></title>
    <link type="text/css" REL="stylesheet" HREF="<?php echo $_SESSION['baseurl'].'css/'.$_SESSION["stylesheet"]; ?>/main.css">
<body>

<form name='form' method='post' action='etiketten.php'>
    <input type="hidden" name="src" value="<?php echo  $dest ?>">
    &nbsp;Etikett:<br>
    &nbsp;<select name='format' >
<?php    foreach ($ALabels as $data) { ?>
        <option value='<?php echo  $data["id"]?>'<?php echo  ($data["id"]==$_POST["format"])?" selected":"" ?>><?php echo  $data["name"] ?>
<?php    } ?>
    </select>&nbsp;<input type='submit' name='chfrm' value='wechseln'><br>
    <hr>
    Freier Text<br>
    &nbsp;<input type="text" name="text" size="25" value="<?php echo  $freitext ?>">
    <hr>
    &nbsp;Bitte Startposition ausw&auml;hlen.<br>
    &nbsp;Es wird Spaltenweise verarbeitet.<br>
<?php
    $sel=" checked";
    for ($y=1; $y<=$label["ny"];$y++) {
        echo "\t\t\t\t".sprintf("&nbsp;%02d",$y);
        for ($x=1; $x<=$label["nx"];$x++) {
            echo "<input type='radio' name='xy' value='x$x:y$y'$sel>";
            $sel=false;
        }
        echo "<br>\n";
    }
?>
    &nbsp;<input type='submit' name='print' value='erzeugen'></form>
</form>
</body>
</html>
