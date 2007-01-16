<!-- $ID: $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>

	<script language="JavaScript">
	<!--
		function doInit() {
			{JS}
		}
		function sende() {
			to=document.mailform.TO.value;
			cc=document.mailform.CC.value;
			subj=document.mailform.Subject.value;
			if (to == "" && cc == "") {
				alert ("Kein Empfänger angegeben");
				return false;
			}
			if (subj == "") {
				alert("Kein Betreff angegeben");
				return false;
			}
			return true;
		}
		function suchMail(wo) {
			doc=eval("document.mailform."+wo);
			val=doc.value;
			f1=open("suchMail.php?name="+val+"&adr="+wo,"suche","width=450,height=200,left=100,top=100");
		}
		function upload() {
			f1=open("mailupload.php","suche","width=350,height=200,left=100,top=100");
		}
		function sign(){
			txt=document.mailform.BodyText.value;
			sign="{Sign}";
			document.mailform.BodyText.value=txt+"\n"+sign.replace(/<br>/g,"\n");
		}
		function setcur(textEl) {
			if(textEl.selectionStart || textEl.selectionStart == '0') {
		     		textEl.selectionStart=0;
     				textEl.selectionEnd=0;
			}
		}
		function getVorlage() {
			x=document.mailform.vorlage.selectedIndex
			if (x>0) {
				document.location.href="mail.php?KontaktTo={KontaktTO}&MID="+document.mailform.vorlage.options[x].value;
			}
		}
	//-->
	</script>
<body onLoad="doInit();" >
<!--table width="103%" class="karte" border="0"><tr><td class="karte"-->
<!-- Beginn Code ------------------------------------------->
<p class="listtop">eMail versenden <font color="red">{Msg}</font></p>
<center>
<table style="width:680px;">
<form name="mailform" action="mail.php" enctype='multipart/form-data' method="post" onSubmit="return sende();">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="2000000">
<INPUT TYPE="hidden" name="QUELLE" value="{QUELLE}">
<INPUT TYPE="hidden" name="KontaktTO" value="{KontaktTO}">
<INPUT TYPE="hidden" name="KontaktCC" value="{KontaktCC}">
<INPUT TYPE="hidden" name="MID" value="{vorlage}">
<tr>
	<td class="smal re" width="60px"></td>
	<td class="smal re" width="*x"></td>
	<td class="smal re" width="*"></td>
</tr>
<tr>
	<td class="smal re">An:</td>
	<td class="smal"><input type="text" name="TO" value="{TO}" size="65" maxlength="125" tabindex="1"> <input type="button" name="sto" value="suchen" onClick="suchMail('TO');"></td>
	<td rowspan="7" class="le" style="vertical-align:middle;"><input type="submit" name="ok" value="senden"><br><br>{btn}
							<br><br><input type="submit" name="save" value="Vorlage
sichern"></td>
</tr><tr>
	<td class="smal re">CC:</td>
	<td class="smal"><input type="text" name="CC" value="{CC}" size="65" maxlength="125" tabindex="2"> <input type="button" name="scc" value="suchen" onClick="suchMail('CC');"></td>
</tr><tr>
	<td class="smal re">Vorlage:</td>
	<td class="smal"><select name="vorlage" tabindex="3" style="width:480px;" onChange="getVorlage();">
		<option value=""></option>
<!-- BEGIN Betreff -->
		<option value="{MID}">{CAUSE}</option>
<!-- END Betreff -->
	</select></td>
</tr><tr>
	<td class="smal re">Betreff:</td>
	<td class="smal"><input type="text" name="Subject" value="{Subject}" size="67" maxlength="125" tabindex="3"></td>
</tr><tr>
	<td class="smal re">Text:</td>
	<td class="smal">
	<textarea name="BodyText" cols="91" rows="13" tabindex="4" onFocus="setcur(this);">{BodyText}</textarea>
	</td>
</tr><tr>
	<td class="smal re">Datei:</td>
	<td><input type="file" name="Datei[]" size="55" maxlength="125"></td>
</tr><tr>
	<td class="smal re">Datei:</td>
	<td><input type="file" name="Datei[]" size="55" maxlength="125"></td>
</tr><tr>
	<td class="smal re">Datei:</td>
	<td><input type="file" name="Datei[]" size="55" maxlength="125"></td>
</tr>

</form>
</table>
</center>
<!-- End Code ------------------------------------------->
<!--/td></tr></table-->
</body>
</html>
