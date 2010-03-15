<?php
session_start();
include("inc/stdLib.php");
require_once("documents.php");


if ($_GET["src"]) $_POST["src"]=$_GET["src"];

//$filetype=array("rtf","tex","odt","swf","sxw");
//derzeit kein RTF
$filetype=array("tex","odt","swf","sxw");

if ($_POST["erzeugen"]) {
    if (!empty($_FILES["datei"]["name"])) {
        $typ=strtolower(substr($_FILES["datei"]["name"],-3));
        if (in_array($typ,$filetype)) {
            //ok, gültige Datei angehängt, vorlage sichern
            $dest="./dokumente/".$_SESSION["mansel"]."/serbrief";
            $ok=chkdir("serbrief");
            copy($_FILES["datei"]["tmp_name"],$dest."/".$_FILES["datei"]["name"]);
            unlink ($_FILES["datei"]["tmp_name"]);

            //Verzeichnis anlegen für die Serienbriefe
            $savefiledir="serbrief/".substr($_FILES["datei"]["name"],0,-4);
            @mkdir($savefiledir);
            
            //Dokument in db speichern
            $dbfile=new document();
            $dbfile->setDocData("descript",$_POST["subject"]);
            $dbfile->setDocData("pfad","serbrief");
            $dbfile->setDocData("name",$_FILES["datei"]["name"]);
            $dbfile->setDocData("descript",$_POST["body"]);
            $rc=$dbfile->newDocument();
            $dbfile->saveDocument();
        
            //benötigte Daten in Session speichern
            $_SESSION["dateiId"] = $dbfile->id;
            $_SESSION["SUBJECT"]=$_POST["subject"];
            $_SESSION["BODY"]=$_POST["body"];
            $_SESSION["DATE"]=$_POST["date"];
            $_SESSION["src"]=$_POST["src"];
            $_SESSION["savefiledir"]=$savefiledir;
            $_SESSION["datei"]=$_FILES["datei"]["name"];
            
            //POP-Up erzeugt die Dokumente
            $js="f1=open('mkserdocs.php?src=".$_POST["src"]."','SerDoc','width=600,height=100')";

            //Formular nicht noch einmal sicken
            $display="hidden";
        } else {
            $js="alert('Ungültiger Dateityp')";
        }
    } else {
        $js="alert('Bitte einen Dateinamen angeben')";
    }
} 
?>
Daten f&uuml;r den Serienbrief:<br />
<form name="serdoc" action="serdoc.php" enctype='multipart/form-data' method="post">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="5000000">
<input type="hidden" name="src" value="<?php echo  $_POST["src"] ?>">
Datum: <input type="text" name="date" size="12" value="<?php echo  $_POST["date"] ?>"><br />
Betreff: <input type="text" name="subject" size="30" value="<?php echo  $_POST["subject"] ?>"><br />
Zusatztext:<br />
<textarea name="body" cols="50" rows="8"><?php echo  $_POST["body"] ?></textarea><br />
Datei: <input type="file" name="datei" size="28"><br />
<?php echo  $_FILES["datei"]["name"] ?><br />
<input type="submit" name="erzeugen" value="erzeugen" style="visibility:<?php echo  $display ?>">
</form>
<script language="JavaScript"><?php echo  $js ?></script>
