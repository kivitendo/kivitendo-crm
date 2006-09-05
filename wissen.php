<?
// $Id: liefer3.php 898 2006-02-17 14:25:37Z hlindemann $
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	$m = ($_POST["m"])?$_POST["m"]:$_GET["m"];
	if ($_GET["kdhelp"]) {
		$tmp=getWPath($m);
		$m.=",".$tmp;
	}
	$tmp = split(",",$m);
	if (!$tmp[0]) $tmp[0]=0;
	if ($_POST["newcat"]) {
		$catinput="<input type='text' size='20' name='catname'><input type='checkbox' name='kdhelp' value='1'> ";
		$catinput.="<input type='hidden' name='hg' value='".$tmp[0]."'>";
		$catinput.="<input type='image' src='image/save_kl.png' title='sichern' name='savecat' value='ok'><br>";
	} else if ($_POST["savecat"]) {
		$rc=insWCategorie($_POST);
	} else if ($_POST["editcat"]) {
		$catname=getOneWCategorie($tmp[0]);
		$catinput="<input type='hidden' name='cid' value='".$tmp[0]."'>";
		$catinput.="<input type='hidden' name='hg' value='".$catname["hauptgruppe"]."'>";
		$catinput.="<input type='text' size='20' name='catname' value='".$catname["name"]."'>";
		$catinput.="<input type='checkbox' name='kdhelp' value='1' ".(($catname["kdhelp"]=="t")?"checked":"")."> ";
		$catinput.="<input type='image' src='image/save_kl.png' title='sichern' name='savecat' value='ok'><br>";
	}
	if ($_POST["newcat"]) {
		$catinput="<input type='text' size='20' name='catname'> <input type='image' src='image/save_kl.png' title='sichern' name='savecat' value='ok'><br>";
	} else if ($_POST["savecat"]) {
		$rc=insWCategorie($_POST);
	}
	$data=getWCategorie();
	$tpl = new Template($base);
	$pre=""; $post="";
	$button="";
	if ($_POST["savecontent"]) {
		$rc=insWContent($_POST);
		$content=getWContent($tmp[0]);
		$contdata=$content["content"];
		if ($content) {
			$datum=substr($content["initdate"],8,2).".".substr($content["initdate"],5,2).".".substr($content["initdate"],0,4);
			$datum.=" ".substr($content["initdate"],11,2).":".substr($content["initdate"],14,2);
			$hl="Versionsnummer: ".$content["version"]." vom $datum Benutzer: ".$content["login"];
			$button="<input type='image' src='image/edit_kl.png' title='Editieren' name='edit' value='Edit'>";
		} else {
			$hl="Bitte einen Beitrag w&auml;hlen";
			$button="<input type='image' src='image/neu.png' title='Neuer Beitrag'  name='neu' value='Neuer Beitrag'>";
		}

	} else if ($_POST["history"]){
		$rs=getWHistory($tmp[0]);
		$cnt=count($rs);
		if ($cnt>1) {
			if ($_POST["diff"][0]>=0 && $_POST["diff"]) { $diff1=$_POST["diff"][0]; } else { $diff1=$cnt-2; };
			if ($_POST["diff"][1]) { $diff2=$_POST["diff"][1];  } else { $diff2=$cnt-1; };
			$diffrs=diff($rs[$diff1]["content"],$rs[$diff2]["content"]);
			$content["version"]=$cnt;
		}
		if ($rs) {
			$button="<input type='image' src='image/cancel_kl.png' title='Normale Ansicht' name='reload' value='Normal'>";
			for ($i=0; $i<$cnt; $i++) {
				$datum=substr($rs[$i]["initdate"],8,2).".".substr($rs[$i]["initdate"],5,2).".".substr($rs[$i]["initdate"],0,4);
				$datum.=" ".substr($rs[$i]["initdate"],11,2).":".substr($rs[$i]["initdate"],14,2);
				$contdata.="<p><input type='checkbox' name='diff[]' value='$i'>".$rs[$i]["version"]." ";
				$contdata.=$datum." - ".$rs[$i]["login"]." - ".strlen($rs[$i]["content"])." Byte</p>";
			}
			if ($cnt>1) 
				$contdata.="Version: ".$rs[$diff1]["version"]."<hr />".$diffrs[0]."<br /><br />Version: ".$rs[$diff2]["version"]."<hr />".$diffrs[1];
		} else {
			$contdata="Kein Daten.";
		}
	} else {
		if ($tmp[0]) $content=getWContent($tmp[0]);
		$contdata=$content["content"];
		if ($content) {	
			$datum=substr($content["initdate"],8,2).".".substr($content["initdate"],5,2).".".substr($content["initdate"],0,4);
			$datum.=" ".substr($content["initdate"],11,2).":".substr($content["initdate"],14,2);
			$hl="Versionsnummer: ".$content["version"]." vom $datum Benutzer: ".$content["login"];
			$button="<input type='image' src='image/edit_kl.png' title='Editieren' name='edit' value='Edit'>";
		} else {
			$hl="Bitte einen Beitrag w&auml;hlen";
			$button="<input type='image' src='image/neu.png' title='Neuer Beitrag'  name='neu' value='Neuer Beitrag'>";
		}
		if ($_POST["edit"]) {
			$datum=substr($content["initdate"],8,2).".".substr($content["initdate"],5,2).".".substr($content["initdate"],0,4);
			$datum.=" ".substr($content["initdate"],11,2).":".substr($content["initdate"],14,2);
			$hl="letzte Versionsnummer: ".$content["version"]." vom $datum Benutzer: ".$content["login"];
			$button="<input type='image' src='image/save_kl.png' tilte='Sichern' name='savecontent' value='Save'>";
			$button.=" <input type='image' src='image/cancel_kl.png' title='Abbruch' name='abbruch' value='Abbruch'>";
			$pre="<textarea id='elm1' name='content' cols='75' rows='18'>";
			$post="</textarea>";
		}
		if ($_POST["neu"]) {
			$hl="Neuer Beitrag. Versionsnummer: 1 am ".date("d.m.Y")." Benutzer: ".$_SESSION["loginCRM"];
			$button="<input type='image' src='image/save_kl.png' tilte='Sichern' name='savecontent' value='Save'>";
			$button.=" <input type='image' src='image/cancel_kl.png' title='Abbruch' name='abbruch' value='Abbruch'>";
			$pre="<textarea id='elm1' name='content' cols='75' rows='18'>";
			$post="</textarea>";
		}
	}

function Thread($HauptGrp,$t,$m,&$tpl)    {	
	global $data,$menu;
	$sp=split(",",$m.",0");	
	$result=$data[$HauptGrp];
	if (count($result) > 0) {
		$x=0;
		$show=(in_array($HauptGrp,$sp))?True:False;
		if ($show) $menu.="<ul>\n";
		$t++;
		while($thread[$HauptGrp]=array_shift($result)) {
			if ($show) { 
				if ($HauptGrp==0) {
					$menu.= "<li><a href='wissen.php?m=".$thread[$HauptGrp]["id"]."'>";
				} else {
					$menu.= "<li><a href='wissen.php?m=".$thread[$HauptGrp]["id"].",$m'>";
				}
				$kdh=($thread[$HauptGrp]["kdhelp"]=='t')?" +":"";
				$menu.=$thread[$HauptGrp]["name"]."</a>$kdh</li>\n"; 
			};
			$y=Thread($thread[$HauptGrp]["id"],$t,$m,$tpl);
		}
		if ($show) $menu.="</ul>\n";
		$t--;
	} else { $x=1; };
	return $x;
}
	$menu="";
	$tpl->set_file(array("wi" => "wissen.tpl"));
	$tpl->set_block("wi","Liste","Block");
	if ($data) {
		Thread(0,0,$m,$tpl);
	}
	if ($tinymce) {
		$tiny="<script language='javascript' type='text/javascript' src='inc/tiny_mce/tiny_mce.js'></script>\n";
		$tiny.="<script language='javascript' type='text/javascript' src='inc/tiny.js'></script>\n";
	}
	$catname=getOneWCategorie($tmp[0]);
	$tpl->set_var(array(
		menu => $menu,
		menuitem => "$m",
		catname => ($catname["name"])?$catname["name"]:"\\",
		content => $contdata,
		version => $content["version"],
		id => $content["id"],
		headline => $hl,
		pre => $pre,
		post =>  $post,
		button1 => $button,
		button2 => ($content["version"]>1)?"<input type='image' src='image/history_kl.png' title='History' name='history' value='History'>":"",
		catinput => $catinput,
		tiny => $tiny
		));
	$tpl->pparse("out",array("wi"));
?>
