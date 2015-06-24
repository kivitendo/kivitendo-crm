<?php
// $Id: confedit.php 2009/02/10 14:41:30 hli Exp $
$pfad = getcwd();
$out = '';
if (!isset($_SERVER['PHP_AUTH_USER'])) {
       Header("WWW-Authenticate: Basic realm='Configurations-Editor'");
       Header("HTTP/1.0 401 Unauthorized");
       echo "Sie m&uuml;ssen sich autentifizieren\n";
       exit;
} else {
        if (!$_POST) {
            //Je Shop ein Conf-File == Multishop
            if( isset($_GET["Shop"]) ) {
                 $Shop = $_GET["Shop"];
            } else {
                 $Shop = '';
            };
            if ( $Shop != "" and file_exists ("$pfad/conf".$Shop.'.php') ) {
                require "$pfad/conf".$Shop.'.php';
                $out = "Konfiguration für Shop $Shop gelesen";
            } else {
                 //Singleshop oder noch kein Shop definiert
                require "$pfad/conf.php";
                 $out = "Standard-Konfiguration gelesen";
            }
            if ( $_SERVER['PHP_AUTH_USER']<>$ERPftpuser || $_SERVER['PHP_AUTH_PW']<>$ERPftppwd ) {
                Header("WWW-Authenticate: Basic realm='My Realm'");
                Header("HTTP/1.0 401 Unauthorized");
                echo "Sie m&uuml;ssen sich autentifizieren\n";
                exit;
            }
            echo $out;
    }
}
include_once("$pfad/error.php");
include_once("$pfad/dblib.php");
include_once("$pfad/erplib.php");

$api = php_sapi_name();
if ( $api == 'cli' ) {
    echo "Nur im Browser benutzen\n";
    exit(-1);
};
$err = new error($api);

$zeichen = array("","UTF-8","ISO-8859-1","ISO-8859-15","Windows-1252","ASCII");
function lager($sel,$db) {
        if (!$db) return '';
        $sql  = "select w.description as lager,b.description as platz,b.id from ";
        $sql .= "bin b left join warehouse w on w.id=b.warehouse_id ";
        $sql .= "order by b.warehouse_id,b.id";
        $bin=$db->getall($sql);
        echo "\t<option value=-1 ".(($sel==-1)?'selected':'').">kein Lagerbestand\n";
        echo "\t<option value=1 ".(($sel==1)?'selected':'').">Gesamtbestand\n";
        if ($bin) foreach ($bin as $row) {
        echo "\t<option value=".$row['id'];
        if ($sel==$row['id']) echo " selected";
        echo ">".$row['lager']." ".$row['platz']."\n";
        }
}
function unit($sel,$db) {
        if (!$db) return '';
    $sql="select name from units order by sortkey";
    $pgs=$db->getall($sql);
    if ($sel=='') $sel=$pgs[0]['name'];
    if ($pgs) foreach ($pgs as $row) {
        echo "\t<option value=".$row['name'];
        if ($sel==$row['name']) echo " selected";
        echo ">".$row['name']."\n";
    }
}
function pg($sel,$db) {
    if (!$db) return '';
    $sql="select id,pricegroup from pricegroup";
    $pgs=$db->getall($sql);
    echo "\t<option value=0";
    if ($sel==0) echo " selected";
    echo ">Standard VK\n";
    if ($pgs) foreach ($pgs as $row) {
        echo "\t<option value=".$row['id'];
        if ($sel==$row['id']) echo " selected";
        echo ">".$row['pricegroup']."\n";
    }
}
function fputsA($f,$key,$var,$bg=false) {
    $lf="\n";
    fputs($f,'$'.$key.'["ID"]=\''. $var['ID'].'\';'.$lf);
    fputs($f,'$'.$key.'["NR"]=\''. $var['NR'].'\';'.$lf);
    fputs($f,'$'.$key.'["Unit"]=\''. $var['Unit'].'\';'.$lf);
    fputs($f,'$'.$key.'["TXT"]=\''. $var['TXT'].'\';'.$lf);
    if ($bg) fputs($f,'$'.$key.'["BUGRU"]=\''. $var['BUGRU'].'\';'.$lf);
    if ($bg) fputs($f,'$'.$key.'["TAX"]=\''. $var['TAX'].'\';'.$lf);
}

if ( isset($_POST["ok"]) ) {
    foreach ($_POST as $key=>$val) {
        ${$key} = $val;
    }
};
    if ( empty($ERPport) ) $ERPport = '5432';
    if ( empty($SHOPport) ) $SHOPport = '3306';

    $ok=true;
    $dbP = new mydb($ERPhost,$ERPdbname,$ERPuser,$ERPpass,$ERPport,'pgsql',$err,$debug);
    if (!$dbP->db) {
        $ok=false;
        echo "Keine Verbindung zur ERP<br>";
        $dbP=false;
        unset($divStd['ID']);
        unset($divVerm['ID']);
        unset($minder['ID']);
        unset($versand['ID']);
        unset($nachn['ID']);
        unset($paypal['ID']);
        unset($treuhand['ID']);
        unset($ERPusr['ID']);
    } else {
        $tmp = getTax($dbP);
        $tax = $tmp['TAX'];
        $sql="SELECT id,description,unit,buchungsgruppen_id FROM parts where partnumber = '%s'";
        $rs=$dbP->getOne(sprintf($sql,$divStd['NR']));
        $divStd['ID']=$rs['id'];
        $divStd['Unit']=$rs['unit'];
        $divStd['BUGRU']=$rs['buchungsgruppen_id'];
        $divStd['TAX']=$tax[4][$rs['buchungsgruppen_id']]['rate'];
        $divStd['TXT']=addslashes($rs['description']);
        $rs=$dbP->getOne(sprintf($sql,$divVerm['NR']));
        $divVerm['ID']=$rs['id'];
        $divVerm['Unit']=$rs['unit'];
        $divVerm['BUGRU']=$rs['buchungsgruppen_id'];
        $divVerm['TAX']=$tax[4][$rs['buchungsgruppen_id']]['rate'];
        $divVerm['TXT']=addslashes($rs['description']);
        $rs=$dbP->getOne(sprintf($sql,$versandS['NR']));
        $versandS['ID']=$rs['id'];
        $versandS['Unit']=$rs['unit'];
        $versandS['BUGRU']=$rs['buchungsgruppen_id'];
        $versandS['TAX']=$tax[4][$rs['buchungsgruppen_id']]['rate'];
        if ($versandS['TXT'] == '') $versandS['TXT']=addslashes($rs['description']);
        $rs=$dbP->getOne(sprintf($sql,$versandV['NR']));
        $versandV['ID']=$rs['id'];
        $versandV['Unit']=$rs['unit'];
        $versandV['BUGRU']=$rs['buchungsgruppen_id'];
        $versandV['TAX']=$tax[4][$rs['buchungsgruppen_id']]['rate'];
        if ($versandV['TXT'] == '') $versandV['TXT']=addslashes($rs['description']);
        $rs=$dbP->getOne(sprintf($sql,$nachn['NR']));
        $nachn['ID']=$rs['id'];
        $nachn['Unit']=$rs['unit'];
        $nachn['BUGRU']=$rs['buchungsgruppen_id'];
        $nachn['TAX']=$tax[4][$rs['buchungsgruppen_id']]['rate'];
        if ($nachn['TXT'] == '') $nachn['TXT']=addslashes($rs['description']);
        $rs=$dbP->getOne(sprintf($sql,$minder['NR']));
        $minder['ID']=$rs['id'];
        $minder['Unit']=$rs['unit'];
        $minder['BUGRU']=$rs['buchungsgruppen_id'];
        $minder['TAX']=$tax[4][$rs['buchungsgruppen_id']]['rate'];
        if ($minder['TXT'] == '') $minder['TXT']=addslashes($rs['description']);
        $rs=$dbP->getOne(sprintf($sql,$paypal['NR']));
        $paypal['ID']=$rs['id'];
        $paypal['Unit']=$rs['unit'];
        $paypal['BUGRU']=$rs['buchungsgruppen_id'];
        $paypal['TAX']=$tax[4][$rs['buchungsgruppen_id']]['rate'];
        if ($paypal['TXT'] == '') $paypal['TXT']=addslashes($rs['description']);
        $rs=$dbP->getOne(sprintf($sql,$treuhand['NR']));
        $treuhand['ID']=$rs['id'];
        $treuhand['Unit']=$rs['unit'];
        $treuhand['BUGRU']=$rs['buchungsgruppen_id'];
        $treuhand['TAX']=$tax[4][$rs['buchungsgruppen_id']]['rate'];
        if ($treuhand['TXT'] == '') $treuhand['TXT']=addslashes($rs['description']);
        $rs=$dbP->getOne("select id from employee where login = '".$ERPusrName."'");
        $ERPusrID=$rs['id'];
    }
    $dbM = new mydb($SHOPhost,$SHOPdbname,$SHOPuser,$SHOPpass,$SHOPport,'mysql',$err,$debug);
    if (!$dbM->db) {
        $ok=false;
        echo "Keine Verbindung zum Shop<br>";
        $dbM=false;
    };
if ( isset($_POST["ok"]) ) {
   $lf = "\n";
   $f = @fopen($pfad.'/conf'.$Shop.'.php','w');
   if ($f) {
        $v="1.5";
        $d=date("Y/m/d H:i:s");
        fputs($f,"<?php$lf// Verbindung zur ERP-db$lf");
        fputs($f,'$debug=\''.$debug.'\';'.$lf);
        fputs($f,'$ERPuser=\''.$ERPuser.'\';'.$lf);
        fputs($f,'$ERPpass=\''.$ERPpass.'\';'.$lf);
        fputs($f,'$ERPhost=\''.$ERPhost.'\';'.$lf);
        fputs($f,'$ERPport=\''.$ERPport.'\';'.$lf);
        fputs($f,'$ERPdbname=\''.$ERPdbname.'\';'.$lf);
        fputs($f,'$codeLX=\''.$codeLX.'\';'.$lf);
        fputs($f,'$mwstLX=\''.$mwstLX.'\';'.$lf);
        fputs($f,'$mwstPGLX=\''.$mwstPGLX.'\';'.$lf);
        fputs($f,'$ERPusrName=\''.$ERPusrName.'\';'.$lf);
        fputs($f,'$ERPusrID=\''.$ERPusrID.'\';'.$lf);
        fputs($f,'$ERPimgdir=\''.$ERPimgdir.'\';'.$lf);
        fputs($f,'$maxSize=\''.$maxSize.'\';'.$lf);
        fputs($f,'$ERPftphost=\''.$ERPftphost.'\';'.$lf);
        fputs($f,'$ERPftpuser=\''.$ERPftpuser.'\';'.$lf);
        fputs($f,'$ERPftppwd=\''.$ERPftppwd.'\';'.$lf);
        fputs($f,'//Verbindung zur Shop-DB'.$lf);
        fputs($f,'$SHOPuser=\''.$SHOPuser.'\';'.$lf);
        fputs($f,'$SHOPpass=\''.$SHOPpass.'\';'.$lf);
        fputs($f,'$SHOPhost=\''.$SHOPhost.'\';'.$lf);
        fputs($f,'$SHOPport=\''.$SHOPport.'\';'.$lf);
        fputs($f,'$SHOPdbname=\''.$SHOPdbname.'\';'.$lf);
        fputs($f,'$codeS=\''.$codeS.'\';'.$lf);
        fputs($f,'$mwstS=\''.$mwstS.'\';'.$lf);
        fputs($f,'$SHOPimgdir=\''.$SHOPimgdir.'\';'.$lf);
        fputs($f,'$SHOPftphost=\''.$SHOPftphost.'\';'.$lf);
        fputs($f,'$SHOPftpuser=\''.$SHOPftpuser.'\';'.$lf);
        fputs($f,'$SHOPftppwd=\''.$SHOPftppwd.'\';'.$lf);
        fputs($f,'$nopic=\''.$nopic.'\';'.$lf);
        fputs($f,'$nopicerr=\''.$nopicerr.'\';'.$lf);
        fputsA($f,'divStd',$divStd,true);
        fputsA($f,'divVerm',$divVerm,true);
        fputsA($f,'versandS',$versandS,true);
        fputsA($f,'versandV',$versandV,true);
        fputsA($f,'minder',$minder,true);
        fputsA($f,'nachn',$nachn,true);
        fputsA($f,'treuhand',$treuhand,true);
        fputsA($f,'paypal',$paypal,true);
        fputs($f,'$bgcol[1]=\'#ddddff\';'.$lf);
        fputs($f,'$bgcol[2]=\'#ddffdd\';'.$lf);
        fputs($f,'$preA=\''.$preA.'\';'.$lf);
        fputs($f,'$preK=\''.$preK.'\';'.$lf);
        fputs($f,'$auftrnr=\''.$auftrnr.'\';'.$lf);
        //fputs($f,'$utftrans=\''.$utftrans.'\';'.$lf);
        fputs($f,'$kdnum=\''.$kdnum.'\';'.$lf);
        fputs($f,'$pricegroup=\''.$pricegroup.'\';'.$lf);
        fputs($f,'$staffel=\''.$staffel.'\';'.$lf);
        fputs($f,'$unit=\''.$unit.'\';'.$lf);
        fputs($f,'$longtxt=\''.$longtxt.'\';'.$lf);
        fputs($f,'$invbrne=\''.$invbrne.'\';'.$lf);
        fputs($f,'$variantnr=\''.$variantnr.'\';'.$lf);
        fputs($f,'$getAllVariant=\''.$GetAllVariant.'\';'.$lf);
        fputs($f,'$variant=\''.$variant.'\';'.$lf);
        fputs($f,'$parent=\''.$parent.'\';'.$lf);
        fputs($f,'$subkat=\''.$subkat.'\';'.$lf);
        fputs($f,'$OEinsPart=\''.$OEinsPart.'\';'.$lf);
        fputs($f,'$lager=\''.$lager.'\';'.$lf);
        //fputs($f,'$showErr=true;'.$lf);
        fputs($f,"?>");
        fclose($f);
        echo "Konfiguration conf$Shop.php gesichert.";
    } else {
        echo "Konfigurationsdatei (conf$Shop.php) konnte nicht geschrieben werden";
    }
} 
?>
<html>
<body>
<center>
<table style="background-color:#cccccc" border="0">
<form name="ConfEdit" method="post" action="confedit.php">
<input type="hidden" name="Shop" value="<?php echo $Shop ?>">
<input type="hidden" name="divStd[ID]" value="<?php echo $divStd['ID'] ?>">
<input type="hidden" name="divVerm[ID]" value="<?php echo $divVerm['ID'] ?>">
<input type="hidden" name="minder[ID]" value="<?php echo $minder['ID'] ?>">
<input type="hidden" name="versandS[ID]" value="<?php echo $versandS['ID'] ?>">
<input type="hidden" name="versandV[ID]" value="<?php echo $versandV['ID'] ?>">
<input type="hidden" name="nachn[ID]" value="<?php echo $nachn['ID'] ?>">
<input type="hidden" name="paypal[ID]" value="<?php echo $paypal['ID'] ?>">
<input type="hidden" name="treuhand[ID]" value="<?php echo $treuhand['ID'] ?>">
<input type="hidden" name="ERPusr[ID]" value="<?php echo $ERPusr['ID'] ?>">

<tr><th>Daten</th><th>Lx-ERP</th><th><?php echo $Shop ?></th><th>Shop</th></tr>
<tr>
    <td>db-Host</td>
    <td colspan="2"><input type="text" name="ERPhost" size="25" value="<?php echo $ERPhost ?>"></td>
    <td><input type="text" name="SHOPhost" size="25" value="<?php echo $SHOPhost ?>"></td>
</tr>
<tr>
    <td>db-Port</td>
    <td colspan="2"><input type="text" name="ERPport" size="25" value="<?php echo $ERPport ?>"></td>
    <td><input type="text" name="SHOPport" size="25" value="<?php echo $SHOPport ?>"></td>
</tr>
<tr>
    <td>Database</td>
    <td colspan="2"><input type="text" name="ERPdbname" size="20" value="<?php echo $ERPdbname ?>"></td>
    <td><input type="text" name="SHOPdbname" size="20" value="<?php echo $SHOPdbname ?>"></td>
</tr>
<tr>
    <td>db-User Name</td>
    <td colspan="2"><input type="text" name="ERPuser" size="15" value="<?php echo $ERPuser ?>"></td>
    <td><input type="text" name="SHOPuser" size="15" value="<?php echo $SHOPuser ?>"></td>
</tr>
<tr>
    <td>db-User PWD</td>
    <td colspan="2"><input type="text" name="ERPpass" size="15" value="<?php echo $ERPpass ?>"></td>
    <td><input type="text" name="SHOPpass" size="15" value="<?php echo $SHOPpass ?>"></td>
</tr>
</tr>
    <td>Zeichensatz</td>
    <td colspan="2"><select name="codeLX">
<?php   foreach($zeichen as $code) {
             echo "<option value='".$code."'";
             if ($code == $codeLX) echo " selected";
             echo ">".$code."\n"; };
?>
    </select></td>
    <td ><select name="codeS">
<?php   $zeichen[] = 'AUTO';
        foreach($zeichen as $code) {
             echo "<option value='".$code."'";
             if ($code == $codeS) echo " selected";
             echo ">".$code."\n"; };
?>
    </select></td>
</tr>
<tr>
    <td>VK-Preis </td>
        <td colspan="2"> <input type="radio" name="mwstLX" value="1" <?php echo ($mwstLX==1)?"checked":'' ?>> incl.
        <input type="radio" name="mwstLX" value="0" <?php echo ($mwstLX<>1)?"checked":'' ?>> excl. MwSt</td>
    <td><input type="radio" name="mwstS" value="1" <?php echo ($mwstS==1)?"checked":'' ?>> incl.
        <input type="radio" name="mwstS" value="0" <?php echo ($mwstS<>1)?"checked":'' ?>> excl. MwSt</td>
</tr>
<tr>
    <td>Preisgruppe</td>
    <td><select name="pricegroup">
<? pg($pricegroup,$dbP); ?>
        </select>
        <input type="radio" name="mwstPGLX" value="1" <?php echo ($mwstPGLX==1)?"checked":'' ?>> incl.
        <input type="radio" name="mwstPGLX" value="0" <?php echo ($mwstPGLX<>1)?"checked":'' ?>> excl. MwSt

    </td>
    <td></td>
    <td>Std-Einheit <select name="unit">
<? unit($unit,$dbP); ?>
        </select></td>
</tr>
<tr>
    <td>Staffelpreise in </td>
    <td colspan="2"><select name="staffel">
        <option value=''           <?php echo ($staffel=='')?'selected':'' ?>>keine
        <option value='microfiche' <?php echo ($staffel=='microfiche')?'selected':'' ?>>Mikrofilm 
        <option value='drawing'    <?php echo ($staffel=='drawing')?'selected':'' ?>>Zeichnung
        <option value='ean'        <?php echo ($staffel=='ean')?'selected':'' ?>>EAN-Code
        <option value='formel'     <?php echo ($staffel=='formel')?'selected':'' ?>>Formel
    </select> 5:10!10:9,50!30:9,10!70:9
    </td>

    <td></td>
    <td></td>
</tr>
<tr>
    <td>ParentNr in </td>
    <td colspan="2"><select name="parent">
        <option value=''           <?php echo ($parent=='')?'selected':'' ?>>keine
        <option value='microfiche' <?php echo ($parent=='microfiche')?'selected':'' ?>>Mikrofilm 
        <option value='drawing'    <?php echo ($parent=='drawing')?'selected':'' ?>>Zeichnung
        <option value='ean'        <?php echo ($parent=='ean')?'selected':'' ?>>EAN-Code
        <option value='formel'     <?php echo ($parent=='formel')?'selected':'' ?>>Formel
        <option value='ve'         <?php echo ($parent=='ve')?'selected':'' ?>>Verrechnungseinheit
        <option value='gv'         <?php echo ($parent=='gv')?'selected':'' ?>>Geschäftsvolumen
    </select>
    </td>
    <td></td>
    <td></td>
</tr>
<tr>
    <td>Varianten</td>
    <td>als Subartikel <input type="checkbox" name="variant" value="1" <?php echo ($variant=='1')?'':"checked"; ?>> </td>
    <td>Subkategorie</td>
    <td><input type="text" name="subkat" size="20" value="<?php echo $subkat; ?>"></td>
    <td></td>
</tr>
     
<tr>
    <td>User-ID</td>
    <td colspan="2"><input type="text" name="ERPusrName" size="10" value="<?php echo $ERPusrName ?>">
        <input type="checkbox" name="a1" <?php echo (empty($ERPusrID)?'':"checked") ?>></td>
    <td></td>
</tr>
<tr>
    <td>Image-Dir</td>
    <td colspan="2"><input type="text" name="ERPimgdir" size="30" value="<?php echo $ERPimgdir ?>"></td>
    <td><input type="text" name="SHOPimgdir" size="30" value="<?php echo $SHOPimgdir ?>"></td>
</tr>
<tr>
    <td>Platzhalterbild</td>
    <td colspan="2"><input type="text" name="nopic" size="20" value="<?php echo $nopic; ?>">ohne Endung</td>
    <td colspan="2"><input type="checkbox" value="1" name="nopicerr" <?php echo (empty($nopicerr)?'':"checked") ?>>nur bei fehlerhaftem Upload verwenden</td>
</tr>
<tr>
    <td>FTP-Host</td>
    <td colspan="2"><input type="text" name="ERPftphost" size="20" value="<?php echo $ERPftphost ?>"></td>
    <td><input type="text" name="SHOPftphost" size="20" value="<?php echo $SHOPftphost ?>"></td>
</tr>
<tr>
    <td>FTP-User</td>
    <td colspan="2"><input type="text" name="ERPftpuser" size="15" value="<?php echo $ERPftpuser ?>"></td>
    <td><input type="text" name="SHOPftpuser" size="15" value="<?php echo $SHOPftpuser ?>"></td>
</tr>
<tr>
    <td>FTP-User PWD</td>
    <td colspan="2"><input type="text" name="ERPftppwd" size="15" value="<?php echo $ERPftppwd ?>"></td>
    <td><input type="text" name="SHOPftppwd" size="15" value="<?php echo $SHOPftppwd ?>"></td>
</tr>
<tr>
    <td>Nr Diverse Std-MwSt</td>
    <td><input type="text" name="divStd[NR]" size="10" value="<?php echo $divStd['NR'] ?>">
        <input type="checkbox" name="a1" <?php echo (empty($divStd['ID'])?'':"checked") ?>></td>
    <td>Nr Diverse Verm-MwSt</td>
    <td><input type="text" name="divVerm[NR]" size="10" value="<?php echo $divVerm['NR'] ?>">
        <input type="checkbox" name="a1" <?php echo (empty($divVerm['ID'])?'':"checked") ?>></td>
</tr>
<tr>
    <td>Nr Versand Std-MwSt</td>
    <td><input type="text" name="versandS[NR]" size="10" value="<?php echo $versandS['NR'] ?>">
        <input type="checkbox" name="a1" <?php echo (empty($versandS['ID'])?'':"checked") ?>></td>
    <td>Text:</td>
    <td><input type="text" name="versandS[TXT]" size="20" value="<?php echo $versandS['TXT'] ?>"><?php echo $versandS['TAX'] ?></td>
<tr>
    <td>Nr Versand Verm-MwSt</td>
    <td><input type="text" name="versandV[NR]" size="10" value="<?php echo $versandV['NR'] ?>">
        <input type="checkbox" name="a1" <?php echo (empty($versandV['ID'])?'':"checked") ?>></td>
    <td>Text:</td>
    <td><input type="text" name="versandV[TXT]" size="20" value="<?php echo $versandV['TXT'] ?>"><?php echo $versandV['TAX'] ?></td>
</tr>
<tr>
    <td>Nr Paypal</td>
    <td><input type="text" name="paypal[NR]" size="10" value="<?php echo $paypal['NR'] ?>">
        <input type="checkbox" name="a1" <?php echo (empty($paypal['ID'])?'':"checked") ?>></td>
    <td>Text:</td>
    <td><input type="text" name="paypal[TXT]" size="20" value="<?php echo $paypal['TXT'] ?>"></td>
</tr>
<tr>
    <td>Nr Treuhand</td>
    <td><input type="text" name="treuhand[NR]" size="10" value="<?php echo $treuhand['NR'] ?>">
        <input type="checkbox" name="a1" <?php echo (empty($treuhand['ID'])?'':"checked") ?>></td>
    <td>Text:</td>
    <td><input type="text" name="treuhand[TXT]" size="20" value="<?php echo $treuhand['TXT'] ?>"></td>
</tr>
<tr>
    <td>Nr Mindermenge</td>
    <td><input type="text" name="minder[NR]" size="10" value="<?php echo $minder['NR'] ?>">
        <input type="checkbox" name="a1" <?php echo (empty($minder['ID'])?'':"checked") ?>></td>
    <td>Text:</td>
    <td><input type="text" name="minder[TXT]" size="20" value="<?php echo $minder['TXT'] ?>"></td>
</tr>
<tr>
    <td>Nr Nachname</td>
    <td><input type="text" name="nachn[NR]" size="10" value="<?php echo $nachn['NR'] ?>">
        <input type="checkbox" name="a1" <?php echo (empty($nachn['ID'])?'':"checked") ?>></td>
    <td>Text:</td>
    <td><input type="text" name="nachn[TXT]" size="20" value="<?php echo $nachn['TXT'] ?>"></td>
</tr>
<tr>
    <td colspan="2">Auftragsnummern durch</td>
    <td><input type="radio" name="auftrnr" value="1" <?php echo ($auftrnr==1)?"checked":'' ?>> LxO</td>
    <td><input type="radio" name="auftrnr" value="0" <?php echo ($auftrnr<>1)?"checked":'' ?>> Shop</td>
</tr>
<tr>
    <td colspan="2">Kundennummern durch</td>
    <td><input type="radio" name="kdnum" value="1" <?php echo ($kdnum==1)?"checked":'' ?>> LxO</td>
    <td><input type="radio" name="kdnum" value="0" <?php echo ($kdnum<>1)?"checked":'' ?>> Shop</td>
</tr>
<tr>
    <td colspan="2">Nummernerweiterung</td>
    <td>Auftrag<input type="text" name="preA" size="5" value="<?php echo $preA ?>"></td>
    <td>Kunde<input type="text" name="preK" size="5" value="<?php echo $preK ?>"></td>
</tr>
<tr>
    <td>Lagerbestand aus</td>
    <td><select name="lager">
<? lager($lager,$dbP); ?>
        </select></td>
    <td></td>
    <td></td>
<tr>
<tr>
    <td colspan="3">Langbeschreibung aus Shop &uuml;bernehmen</td>
    <td><input type="radio" name="longtxt"  value="1" <?php echo ($longtxt<>2)?"checked":'' ?>>Ja
    <input type="radio" name="longtxt"  value="2" <?php echo ($longtxt==2)?"checked":'' ?>>Nein</td>

</tr>
<tr>
    <td colspan="3">LxO-Rechnungen sind Netto</td>
    <td><input type="radio" name="invbrne"  value="1" <?php echo ($invbrne<>2)?"checked":'' ?>>Ja
    <input type="radio" name="invbrne"  value="2" <?php echo ($invbrne==2)?"checked":'' ?>>Nein</td>
</tr>
<tr>
    <td colspan="3">Varianten sind eigene Nummern in Lx (-n)</td>
    <td><input type="radio" name="variantnr"  value="1" <?php echo ($variantnr<>2)?"checked":'' ?>>Ja
    <input type="radio" name="variantnr"  value="2" <?php echo ($variantnr==2)?"checked":'' ?>>Nein</td>
</tr>
<tr>
    <td colspan="3">Alle Varianten automatisch in ERP anlegen</td>
    <td><input type="radio" name="GetAllVariant"  value="1" <?php echo ($GetAllVariant<>2)?"checked":'' ?>>Ja
    <input type="radio" name="GetAllVariant"  value="2" <?php echo ($GetAllVariant==2)?"checked":'' ?>>Nein</td>
</tr>
<tr>
    <td colspan="3">Unbekannte Artikel beim Bestellimport anlegen</td>
    <td><input type="radio" name="OEinsPart"  value="1" <?php echo ($OEinsPart<>2)?"checked":'' ?>>Ja
    <input type="radio" name="OEinsPart"  value="2" <?php echo ($OEinsPart==2)?"checked":'' ?>>Nein</td>
</tr>
<tr>
    <td>Logging</td>
    <td>ein<input type="radio" name="debug" value="true" <?php echo ($debug=="true")?"checked":"" ?>>
    aus<input type="radio" name="debug" value="false" <?php echo ($debug!="true")?"checked":"" ?>></td>
    <td></td><td></td>
</tr>

<!--tr>
    <td>Bildergr&ouml;sse (byte)</td>
    <td><input type="text" name="maxSize" size="10" value="<?php echo $maxSize ?>"></td>
    <td></td>
</tr-->


<tr><td colspan="4" align="center"><input type="submit" name="ok" value="sichern"></td></tr>
</form>
</table>
</center>
</body>
</html>
