<html>
	<head><title></title>
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>

	<script language="JavaScript">
	<!--
		function sende() {
			subj=document.mailform.Subject.value;
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
	//-->
	</script>
<body onLoad="document.mailform.Subject.focus();">
<!-- Beginn Code ------------------------------------------->
<p class="listtop">Serienmail versenden <font color="red">{Msg}</font></p>
<center>
<table style="width:680px;">
<form name="mailform" action="sermail.php" enctype='multipart/form-data' method="post" onSubmit="return sende();">
<INPUT TYPE="hidden" name="MAX_FILE_SIZE" value="2000000">
<INPUT TYPE="hidden" name="KontaktCC" value="{KontaktCC}">
<tr>
	<td class="mini re" width="60px"></td>
	<td class="mini re" width="*x"></td>
	<td class="mini re" width="*"></td>
</tr>
<tr>
	<td class="mini re">An:</td>
	<td class="mini">Serienmail</td>
	<td rowspan="7" class="le" style="vertical-align:middle;"><input type="submit" name="ok" value="senden"><br><br>{btn}</td>
</tr><tr>
	<td class="mini re">CC:</td>
	<td class="mini"><input type="text" name="CC" value="{CC}" size="65" maxlength="125" tabindex="2"> <input type="button" name="scc" value="suchen" onClick="suchMail('CC');"></td>
</tr><tr>
	<td class="mini re">Betreff:</td>
	<td class="mini"><input type="text" name="Subject" value="{Subject}" size="67" maxlength="125" tabindex="3"></td>
</tr><tr>
	<td class="mini re">Text:</td>
	<td class="mini">
	<textarea name="BodyText" cols="91" rows="15" tabindex="4" onFocus="setcur(this);">{BodyText}</textarea>
	</td>
</tr><tr>
	<td class="mini re">Datei:</td>
	<td><input type="file" name="Datei" size="55" maxlength="125"></td>
</tr>

</form>
</table>
{SENDTXT}
</center>
<!-- End Code ------------------------------------------->
</body>
</html>
