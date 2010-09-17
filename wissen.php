<?php
	require_once("inc/stdLib.php");
	include("inc/template.inc");
	include("inc/crmLib.php");
	$m = ($_POST["m"])?$_POST["m"]:$_GET["m"];
	if ($_GET["kdhelp"]) {
		$tmp=getWPath($m);
		$m.=",".$tmp;
	}
	$tmp = explode(",",$m);
	if (!$tmp[0]) $tmp[0]=0;
	if ($_POST["aktion"] == "newcat") {
		$catinput="<input type='text' size='20' name='catname'><input type='checkbox' name='kdhelp' value='1'> ";
		$catinput.="<input type='hidden' name='hg' value='".$tmp[0]."'>";
		$catinput.="<image src='image/save_kl.png' title='.:save:.' onClick=\"go('savecat')\"><br>";
	} else if ($_POST["aktion"] == "savecat") {
		$rc=insWCategorie($_POST);
	} else if ($_POST["aktion"]=="suche") {
        $kat = explode(",",$_POST["kat"]);
        $treffer  = suchWDB($_POST["wort"],$kat[0]);
        $notfound="";
        if (count($treffer)==0) {
            $notfound="not found";    
        } else if (count($treffer)==1) {
            header ("location:wissen.php?kdhelp=1&m=".$treffer[0]["id"]);
        } else {
            $tmp = "[<a href='wissen.php?kdhelp=1&m=%s' class=''>%s</a>]<br />\n";
            foreach ($treffer as $line) {
                $WDtreffer .= sprintf($tmp,$line["id"],$line["name"]);
            }
        }
	} else if ($_POST["aktion"] == "editcat" && $tmp[0]<>"" ) {
		$catname=getOneWCategorie($tmp[0]);
		$catinput="<input type='hidden' name='cid' value='".$tmp[0]."'>";
		$catinput.="<input type='hidden' name='hg' value='".$catname["hauptgruppe"]."'>";
		$catinput.="<input type='text' size='20' name='catname' value='".$catname["name"]."'>";
		$catinput.="<input type='checkbox' name='kdhelp' value='1' ".(($catname["kdhelp"]=="t")?"checked":"")."> ";
		$catinput.="<image src='image/save_kl.png' title='.:save:.' onClick=\"go('savecat')\"><br>";
	}
	$data=getWCategorie();
	$tpl = new Template($base);
	$pre=""; $post="";
	$button="";
	if ($_POST["aktion"] == "savecontent") {
		$rc=insWContent($_POST);
		$content=getWContent($tmp[0]);
		$contdata=$content["content"];
		if ($content) {
			$datum=substr($content["initdate"],8,2).".".substr($content["initdate"],5,2).".".substr($content["initdate"],0,4);
			$datum.=" ".substr($content["initdate"],11,2).":".substr($content["initdate"],14,2);
			$hl=".:vernr:.: ".$content["version"]." .:from:. $datum .:employee:.: ".$content["login"];
		    $button = "<image src='image/edit_kl.png' title='.:edit:.' onClick=\"go('edit')\"><br>";
			//$button="<input type='image' src='image/edit_kl.png' title='Editieren' name='edit' value='.:edit:.'>";
		} else {
			$hl=".:selectarticle:.";
		    $button = "<image src='image/neu.png' title='.:new:. .:article:.' onClick=\"go('neu')\"><br>";
			//$button="<input type='image' src='image/neu.png' title='.:new:. .:article:.'  name='neu' value='.:new:. .:article:.'>";
		}

	} else if ($_POST["aktion"] == "history"){
		$rs=getWHistory($tmp[0]);
		$cnt=count($rs);
		if ($cnt>1) {
			if ($_POST["diff"][0]>=0 && $_POST["diff"]) { $diff1=$_POST["diff"][0]; } else { $diff1=$cnt-2; };
			if ($_POST["diff"][1]) { $diff2=$_POST["diff"][1];  } else { $diff2=$cnt-1; };
			$diffrs=diff($rs[$diff1]["content"],$rs[$diff2]["content"]);
			$content["version"]=$cnt;
		}
		if ($rs) {
		    $button = "<image src='image/cancel_kl.png' title='.:normview:.' onClick=\"go('reload')\"><br>";
			//$button="<input type='image' src='image/cancel_kl.png' title='.:normview:.' name='reload' value='Normal'>";
			for ($i=0; $i<$cnt; $i++) {
				$datum=substr($rs[$i]["initdate"],8,2).".".substr($rs[$i]["initdate"],5,2).".".substr($rs[$i]["initdate"],0,4);
				$datum.=" ".substr($rs[$i]["initdate"],11,2).":".substr($rs[$i]["initdate"],14,2);
				$contdata.="<p><input type='checkbox' name='diff[]' value='$i'>".$rs[$i]["version"]." ";
				$contdata.=$datum." - ".$rs[$i]["login"]." - ".strlen($rs[$i]["content"])." Byte</p>";
			}
			if ($cnt>1) 
				$contdata.="Version: ".$rs[$diff1]["version"]."<hr />".$diffrs[0]."<br /><br />Version: ".$rs[$diff2]["version"]."<hr />".$diffrs[1];
		} else {
			$contdata=".:no_data:.";
		}
	} else {
		if ($tmp[0]) $content=getWContent($tmp[0]);
		$contdata=$content["content"];
		if ($content) {	
			$datum=substr($content["initdate"],8,2).".".substr($content["initdate"],5,2).".".substr($content["initdate"],0,4);
			$datum.=" ".substr($content["initdate"],11,2).":".substr($content["initdate"],14,2);
			$hl=".:last:. .:vernr:.: ".$content["version"]." .:from:. $datum .:employee:.: ".$content["login"];
		    $button = "<image src='image/edit_kl.png' title='.:edit:.' onClick=\"go('edit')\"><br>";
			//$button="<input type='image' src='image/edit_kl.png' title='.:edit:.' name='edit' value='.:edit:.'>";
		} else {
			$hl=".:selectarticle:.";
		    $button = "<image src='image/neu.png' title='.:new:. .:article:.' onClick=\"go('neu')\"><br>";
			//$button="<input type='image' src='image/neu.png' title='.:new:. .:article:.'  name='neu' value='.:new:. .:article:.'>";
		}
		if ($_POST["aktion"] == "edit") {
			$datum=substr($content["initdate"],8,2).".".substr($content["initdate"],5,2).".".substr($content["initdate"],0,4);
			$datum.=" ".substr($content["initdate"],11,2).":".substr($content["initdate"],14,2);
			$hl=".:last:. .:vernr:.: ".$content["version"]." .:from:. $datum .:employee:.: ".$content["login"];
		    $button = "<image src='image/save_kl.png' title='.:save:.' onClick=\"go('savecontent')\"> ";
		    $button .= "<image src='image/cancel_kl.png' title='.:normview:.' onClick=\"go('reload')\"> ";
		    $button .= "<image src='image/file_kl.png' title='.:picfile:.' onClick=\"filesearch()\"><br>";
			//$button="<input type='image' src='image/save_kl.png' tilte='.:save:.' name='savecontent' value='.:save:.'>";
			//$button.=" <input type='image' src='image/cancel_kl.png' title='.:escape:.' name='abbruch' value='.:escape:.'>";
			$pre="<textarea id='elm1' name='content' cols='95' rows='18'>";
			$post="</textarea>";
		}
		if ($_POST["aktion"] == "neu") {
			$hl=".:new:. .:article:. .:vernr:.: 1 .:from:. ".date("d.m.Y")." .:employee:.: ".$_SESSION["loginCRM"];
		    $button = "<image src='image/save_kl.png' title='.:save:.' onClick=\"go('savecontent')\"> ";
		    $button .= "<image src='image/cancel_kl.png' title='.:normview:.' onClick=\"go('reload')\"> ";
		    $button .= "<image src='image/file_kl.png' title='.:picfile:.' onClick=\"filesearch()\"><br>";
			//$button="<input type='image' src='image/save_kl.png' tilte='.:save:.' name='savecontent' value='.:save:.'>";
			//$button.=" <input type='image' src='image/cancel_kl.png' title='.:escape:.' name='abbruch' value='.:escape:.'>";
			$pre="<textarea id='elm1' name='content' cols='95' rows='18'>";
			$post="</textarea>";
		}
	}

function Thread($HauptGrp,$t,$m,&$tpl)    {	
	global $data,$menu;
	$sp=explode(",",$m.",0");	
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
        ERPCSS      => $_SESSION["stylesheet"],
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
		button2 => ($content["version"]>1)?"<image src='image/history_kl.png' title='History' onClick=\"go('history')\">":"",
		catinput => $catinput,
		tiny => $tiny,
		));
    if ($_POST["aktion"]=="suche") $tpl->set_var(array(
		notfound => $notfound,
		headline => "Trefferliste",
		pre => "",
		post =>  "",
		button1 => "",
		button2 => "",
        content => $WDtreffer,
		));

	$tpl->Lpparse("out",array("wi"),$_SESSION["lang"],"work");
?>
