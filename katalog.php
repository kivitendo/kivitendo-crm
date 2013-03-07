<?php
    require("inc/stdLib.php");
    include ('inc/katalog.php');
    $link = "";
    $menu =  $_SESSION['menu'];
if ($_POST['ok']) {
    $artikel = getArtikel($_POST);
    $tax = getTax();
    $vorlage = prepTex();
    $lastPG = '';
    if (file_exists('tmp/katalog.pdf')) unlink('tmp/katalog.pdf');
    if (file_exists('tmp/katalog.tex')) unlink('tmp/katalog.tex');
    if (file_exists('tmp/tabelle.tex')) unlink('tmp/tabelle.tex');
    $f = fopen('tmp/katalog.tex','w');
    $rc = fputs($f,$vorlage['pre']);
    $suche = array('&','_','"','!','#');
    $ersetze = array('\&','\_','\"',' : ','\#');
    if ($artikel) foreach($artikel as $part) {
        $line = $vorlage['artikel'];
        if ($lastPG != $part['partsgroup']) {
            $lastPG = $part['partsgroup'];
            $val = str_replace($suche,$ersetze,$part['partsgroup']);
            $line = preg_replace("/<%partsgroup%>/i",$val,$line);
            $line = preg_replace("/<%newpg%>/i",'new',$line);
            $line = preg_replace("/<%[^%]+%>/i",'0',$line);
            $rc = fputs($f,$line);
            $line = $vorlage['artikel'];
        }
        if ($_POST['preise'] == '1') { $preis = $part['sellprice']; }
        else if ($_POST['preise'] == '2') { $preis = $part['listprice']; }
        else { $preis = $part['price']; };
        if ($_POST['prozent'] > 0) {
            if ($_POST['pm']=='+') { $preis += $preis / 100 * $_POST['prozent']; }
            else                   { $preis -= $preis / 100 * $_POST['prozent']; };
        }
        if ($_POST['addtax']) $preis = $preis * (1 + $tax[$part['bugru']]['rate']);
        foreach ($part as $key=>$val) {
            if ($key == 'description') $val = str_replace($suche,$ersetze,$val);
            //if ($key == 'image') $val = str_replace($suche,$ersetze,$val);
            if ($key == 'image') {
                 if ($val == '') $val = 'image/nopic.png';
                 if (preg_match('/http[s]*:/i',$val)) $val = 'image/nopic.png';
                 if (! preg_match('/\.(png|jpg)$/i',$val)) $val = 'image/nopic.png';
                 if (!file_exists($val)) $val = 'image/nopic.png';
            }
            $line = preg_replace("/<%newpg%>/i",'xxx',$line);
            if ($key == 'partsgroup') $val = 'x';
            if ($key == 'sellprice') $val = sprintf("%0.2f",$preis);
            if ($key == 'bugru') $val = sprintf("%0.1f",$tax[$part['bugru']]['rate']*100);
            $line = preg_replace("/<%$key%>/i",$val,$line);
        }
        $rc = fputs($f,$line);
    }
    $rc = fputs($f,$vorlage['post']);
    fclose($f);
    $rc = @exec('pdflatex -interaction=batchmode -output-directory=tmp/ tmp/katalog.tex',$out,$ret);
    if ( $ret == 0 ) {
        $rc = @exec('pdflatex -interaction=batchmode -output-directory=tmp/ tmp/katalog.tex',$out,$ret);
        if (file_exists('tmp/katalog.pdf'))   {  
            $link = 'tmp/katalog.pdf'; 
            $msg = "RC:$rc Ret:$ret Out:".$out[0];
     	} else { 
            $link = '';
            if (file_exists('tmp/katalog.log'))   { 
                $linklog = 'tmp/katalog.log';
            }
            $msg = "Kein PDF erstellt<br>RC:$rc Ret:$ret Out:".$out[0]; 
        };
    } else {
        if (file_exists('tmp/katalog.pdf'))   {
            $link = 'tmp/katalog.pdf'; 
            $msg  = 'Evlt nicht korrekt<br>';
        }
        $msg .= "Fehler beim Erstellen<br>RC:$rc Ret:$ret Out:".$out[0];
        $linklog = 'tmp/katalog.log';
    }
} else {
    $_POST['pm']='-';
}
    $preise = getPreise();
    $cvars = getCustoms();
    $pglist = getPgList();
    include("inc/template.inc");
    $t = new Template($base);
    $t->set_file(array("kat" => "katalog.tpl"));
    $t->set_block('kat','cvarListe','BlockCV');
    if ($cvars) {
        foreach ($cvars as $cvar) {
           switch ($cvar["type"]) {
               case "bool"   : $fld = "<input type='checkbox' name='vc_cvar_bool_".$cvar["name"]."' value='t'>";
                               break;
               case "date"   : $fld = "<input type='text' name='vc_cvar_timestamp_".$cvar["name"]."' size='10' id='cvar_".$cvar["name"]."' value=''>";
                               $fld.="<input name='cvar_".$cvar["name"]."_button' id='cvar_".$cvar["name"]."_trigger' type='button' value='?'>";
                               $fld.= '<script type="text/javascript"><!-- '."\n";
                               $fld.= 'Calendar.setup({ inputField : "cvar_'.$cvar["name"].'",';
                               $fld.= 'ifFormat   : "%d.%m.%Y",';
                               $fld.= 'align      : "BL",';
                               $fld.= 'button     : "cvar_'.$cvar["name"].'_trigger"});';
                               $fld.= "\n".'--></script>'."\n";
                               break;
               case "select" : $o = explode("##",$cvar["options"]);
                               $fld = "<select name='vc_cvar_text_".$cvar["name"]."'>\n<option value=''>---------\n";
                               foreach($o as $tmp) {
                                 $fld .= "<option value='$tmp'>$tmp\n";
                               }
                               $fld .= "</select>";
                               break;
               default       : $fld = "<input type='text' name='vc_cvar_".$cvar["type"]."_".$cvar["name"]."' value=''>";
           }
           $t->set_var(array(
              'varlable' => $cvar["description"],
              'varfld'   => $fld,
           ));
           $t->parse('BlockCV','cvarListe',true); 
        }
    }
    $t->set_block('kat','Preise','BlockPr');
    if ($preise) foreach ($preise as $id=>$preis) {
           $t->set_var(array(
              'preisid' => $id,
              'preis' => $preis,
              'select' => ($id==$_POST["preise"])?"selected":"",
           ));
           $t->parse('BlockPr','Preise',true); 
    }
    $t->set_var(array(
        ERPCSS          => $_SESSION['basepath'].'crm/css/'.$_SESSION["stylesheet"],
        JAVASCRIPTS     => $menu['javascripts'],
        STYLESHEETS     => $menu['stylesheets'],
        PRE_CONTENT     => $menu['pre_content'],
        START_CONTENT   => $menu['start_content'],
        END_CONTENT     => $menu['end_content'],
        'THEME'         => $_SESSION['theme'],
        'JQUERY'        => $_SESSION['basepath'].'crm/',
        partnumber	    => $_POST['partnumber'],
        description     => $_POST['description'],
        ean             => $_POST['ean'],
        prozent         => $_POST['prozent'],
        'pm'.$_POST['pm']  => 'checked',
        $_POST['order'] => 'selected',
        partsgroup      => $_POST['partsgroup'],
        pglist          => $pglist,
        addtax	        => ($_POST['addtax'])?"checked":"",
        linklog	        => $linklog,
        link	        => $link,
        msg	            => $msg
    ));
    $t->set_block("kat","Liste","Block");
    $t->Lpparse("out",array("kat"),$_SESSION["lang"],"firma");

?>
