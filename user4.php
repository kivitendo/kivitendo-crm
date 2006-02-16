<?php
// $Id: user4.php,v 1.3 2005/11/02 10:37:51 hli Exp $
	require_once("inc/stdLib.php");
	include("inc/template.inc");

	function getVersion() {
                $ver=file("VERSION");
                return $ver[0];
        };
	$version=getVersion();
	$versiondb=getVersiondb();
	if ($_POST["install"]) {
			if ($_POST["ver"]<>"dir") {
			echo exec("cd tmp; /bin/tar xzf lxcrm-update-".$_POST["ver"].".tgz",$tmp,$rc);
			if ($rc>0) {
				echo "Konnte tar-File nicht enpacken.";
				exit;
			}
		}
		require("tmp/lx-crm/update.php");
		exit;
	} 
	exec("ls -1 tmp/lxcrm-update-*.tgz",$updates);
	if (file_exists("tmp/lx-crm/update.php")) {
		$updatedir=file("tmp/lx-crm/VERSION");
		if ($updatedir[0]>$version) {
			$updatedir=$updatedir[0];
		} else { 
			$updatedir=false;
		}
	};
	$t = new Template($base);
	$t->set_file(array("msg" => "user5.tpl"));
	$t->set_var(array(
			Version => $version,
			Versiondb => $versiondb,
			Dir	=> ($updatedir)?"<input type='radio' name='ver' value='dir'>$updatedir":""
			));
	$t->set_block("msg","lokale","Block");
	if ($updates) {
		foreach($updates as $zeile) {
			if ("tmp/lxcrm-update-".$version.".tgz"<$zeile) { 
				$zeile=substr($zeile,17,5);
				$t->set_var(array(
					val     => $zeile,
				));
				$t->parse("Block","lokale",true);
			}
		}
	}
	$t->pparse("out",array("msg"));
?>
