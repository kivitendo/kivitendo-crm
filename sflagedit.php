<?php
// $Id: user3.php 1038 2006-04-11 15:44:55Z hlindemann $
	require_once("inc/stdLib.php");
    //$lang = getLanguage();
    //$cntlang = count ($lang);
	if ($_POST["holen"] || !$_POST) {
        $cp_sonder=getSonder();
	} else if ($_POST["reset"]) {
        $cp_sonder=getSonder();
	} else if ($_POST["ok"]) {
        $rc = saveSonderFlag($_POST);
        $cp_sonder=getSonder();
	}
    $line = "<tr><td><input type='hidden' name='sonder[%d][new]' value='%d'><input type='hidden' name='sonder[%d][svalue]' value='%s'> <input type='text' name='sonder[%d][sorder]' size='2' value='%s'> </td>";
    $line.= "<td><input type='text' name='sonder[%d][skey]' size='20' value='%s'> </td>";
    $line.= "<td><input type='checkbox' name='sonder[%d][del]' value='1'> </td></tr>";
    echo '<form name="sonder" method="post" action="sflagedit.php">';
    echo "<table><tr><th>Sortierung</th><th>Name</th><th>l&ouml;schen</th></tr>\n";
    $i=0;
    $max=0;
    if ($cp_sonder) foreach ($cp_sonder as $row) {
        echo sprintf($line,$i,0,$i,$row["svalue"],$i,$row["sorder"],$i,$row["skey"],$i)."\n";    
        $i++;
        if ($row["svalue"]>$max) $max=$row["svalue"];
    };
    echo sprintf($line,$i,1,$i,$max*2,$i,$row["sorder"]+1,$i,'',$i)."\n";    
    echo '</table><input type="submit" name="ok" value="sichern"></form>';

?>
