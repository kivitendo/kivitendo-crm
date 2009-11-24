<body onLoad="document.op.submit()">
<form method="post" name="op" action="../<?= (substr($_GET["op"],0,1)=="o")?"oe":$_GET["op"] ?>.pl" >
<input type="hidden" name="<?= ($_GET["op"]=="ar" || $_GET["op"]=="oeC")?"customer":"vendor" ?>" value="<?= $_GET["fa"] ?>">
<input type="hidden" name="sort" value="transdate">
<input type="hidden" name="open" value="Y">
<input type="hidden" name="l_invnumber" value="Y">
<input type="hidden" name="l_ordnumber" value="Y">
<input type="hidden" name="l_transdate" value="Y">
<input type="hidden" name="l_reqdate" value="Y">
<input type="hidden" name="l_name" value="Y">
<input type="hidden" name="l_amount" value="Y">
<input type="hidden" name="l_paid" value="Y">
<input type="hidden" name="delivered" value="1">
<input type="hidden" name="notdelivered" value="1">
<input type="hidden" name="l_employee" value="Y">
<input type="hidden" name="vc" value="<?= ($_GET["op"]=="ar" || $_GET["op"]=="oeC")?"customer":"vendor" ?>">
<input type="hidden" name="type" value="<?= ($_GET["op"]=="ar" || $_GET["op"]=="oeC")?"sales":"purchase" ?>_order">
<input type="hidden" name="nextsub" value="<?= ($_GET["op"]=="oeC")?"orders":$_GET["op"]."_transactions" ?>">
<input type="hidden" name="action" value="Weiter">
</form>
