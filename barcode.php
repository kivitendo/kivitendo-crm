<?

$pid = $_GET["pid"];
require_once("inc/stdLib.php");

function ende($nr) {
        echo "Abbruch: $nr<br>";
        echo "Fehlende oder falsche Daten.";
        exit(1);
}


function getArtikel($id) {
	$sql  = "SELECT P.*,T.rate FROM ";
	$sql .= "parts P left join buchungsgruppen BG on P.buchungsgruppen_id=BG.id ";
	$sql .= "left join chart C on C.id=BG.income_accno_id_0 ";
	$sql .= "left join  taxkeys TK on TK.chart_id=C.id, tax T  ";
	$sql .= "WHERE P.id=$id and TK.taxkey_id=C.taxkey_id and T.id=TK.tax_id ";
	$sql .= "order by TK.startdate desc limit 1";
        $rs = $_SESSION["db"]->getAll($sql);
	if (count($rs)>=1) $rs = $rs[0];
	$rs["bruttosell"]  = round($rs["sellprice"] * (1  + $rs["rate"]),2);
	$rs["mwst"] = round($rs["sellprice"] * $rs["rate"],2);
	return $rs;
}


$data = getArtikel($pid);
$template = file("./vorlage/barcode.tex");
$template = join("",$template);
preg_match_all("/<%([^%]+)%>/",$template,$treffer);
if ($treffer[1]) foreach ($treffer[1] as $key) {
	$template = preg_replace("/<%".$key."%>/",$data[$key],$template);
}
$file = "barcode_".$_SESSION["login"];
$dir = "tmp/";
$f = fopen("$dir$file.tex","w");
fputs($f,$template);
exec("cd $dir; pdflatex $file.tex",$rc);
unlink("$dir$file.log");
unlink("$dir$file.aux");
unlink("$dir$file.tex");
fclose($f);
header("Location: $dir$file.pdf");
header('Content-type: application/pdf');
header('Content-Disposition: attachment; filename="'.$file.'.pdf"');
readfile("$dir$file.pdf");
?>
