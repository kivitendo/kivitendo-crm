<?php
// $Id: sflagedit.php  2010-02-18 15:44:55Z hlindemann $
	require_once("inc/stdLib.php");
    include("inc/template.inc");

	if ($_POST["ok"]) {
        $rc = saveSonderFlag($_POST);
	}

    $cp_sonder=getSonder();

    $i=0;
    $max=0;
    $t = new Template($base);

    $t->set_file(array("flag" => "sflag.tpl"));
    $t->set_block("flag","Flag","Block0");

    if ($cp_sonder) foreach ($cp_sonder as $row) {
        $t->set_var(array(
            idx => $i,
            neu => 0,
            skey => $row["skey"],
            svalue => $row["svalue"],
            order => $row["sorder"]
        ));
        $t->parse("Block0","Flag",true);
        $i++;
        if ($row["svalue"]>$max) $max=$row["svalue"];
    };
    if ( $max == 0 ) $max = 0.5;
    $t->set_var(array(
        idx => $i,
        neu => 1,
        skey => "",
        svalue => $max*2,
        order => $row["sorder"]+1
    ));
    $t->parse("Block0","Flag",true);
    $t->Lpparse("out",array("flag"),$_SESSION["lang"],"work");

?>
