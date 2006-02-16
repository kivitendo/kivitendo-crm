<!-- $Id: user4.tpl,v 1.3 2005/11/02 10:38:58 hli Exp $ -->
<html>
	<head><title>User Stamm</title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<script language="JavaScript">
	<!--
		function suchFa() {
			val=document.formular.name.value;
			f1=open("suchFa.php?name="+val,"suche","width=350,height=200,left=100,top=100");
		}
		function doit() {
			document.grp2.submit();
		}
		function subusr() {
			nr=document.grp2.elements[4].selectedIndex;
			document.grp2.elements[4].options[nr]=null
		}
		function addusr() {
			nr=document.grp2.users.selectedIndex;
			val=document.grp2.users.options[nr].value;
			txt=document.grp2.users.options[nr].text;
			NeuerEintrag = new Option(txt,val,false,true);
 			document.grp2.elements[4].options[document.grp2.elements[4].length] = NeuerEintrag;
		}
		function selall() {
			len=document.grp2.elements[4].length;
			document.grp2.elements[4].multiple=true;
			for (i=0; i<len; i++) {
				document.grp2.elements[4].options[i].selected=true;
			}
			//document.grp2.submit();
		}
	//-->
	</script>
<body>
<p class="listtop">Mitteilungen</p>
<!-- Beginn Code ----------------------------------------------->
<div  class="norm">
	<br>Mitteilungen einstellen, die bei den Stammdaten einer Firmen ausgegeben werden.
	<br><br>
		<form name="formular" action="user3.php" method="post">
		F&uuml;r Firma: <input type="text" name="name"  size="20" value="{Firma}" maxlength="75">
		<input type="button" name="suche" value="suchen" onClick="suchFa()">
		<input type="submit" name="holen" value="holen"><br>
	<br><br>
		<input type="hidden" name="cp_cv_id" value="{FID}">
		<input type="hidden" name="ID" value="{ID}">
		<input type="text" name="message" size="60" maxlength="60" value="{MSG}" maxlength="60"><br>
		Neue Mitteilung<br><br>
		Priorit&auml;t <input type="radio" name="prio" value="1" {R1}>1
		<input type="radio" name="prio" value="2" {R2}>2
		<input type="radio" name="prio" value="3" {R3}>3
		<input type="submit" name="sichern" value="sichern"> <input type="submit" name="reset" value="reset"><br><br>
</div>		
	<table style="width:80%"><tr>
		<td width="45%" class="norm ce">
			eingestellte Mitteilungen:<br>
			<select name="messages" size="5" style="width:500px">
<!-- BEGIN Selectbox -->
				<option value="{MID}">{MSGTXT}</option>
<!-- END Selectbox -->
			</select>
		</td>
		<td align="center">
			<br>
			<input type="submit" name="edit" value="bearbeiten">
			<input type="submit" name="delete" value="entfernen"></td>
	</tr>

	</table>
	</form>

<!-- End Code ----------------------------------------------->
<!--/td></tr></table-->
</body>
</html>

