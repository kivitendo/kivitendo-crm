<!-- $Id$ -->
<html>
	<head><title>User Stamm</title>
	{STYLESHEETS}
        <link type="text/css" REL="stylesheet" HREF="css/{ERPCSS}"></link>
        {JAVASCRIPTS}
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
{PRE_CONTENT}
{START_CONTENT}
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
	<table>	
	<tr><td>Priotit&auml;t </td><td> Mitteilung</td></tr>
	<tr><td><input type="radio" name="prio" value="3" {R3}> 3 <input type="hidden" name="mid3" value="{mid3}"></td>
		<td><input type="text" name="message3" size="60" maxlength="60" value="{MSG3}" maxlength="60"></td>
	</tr>
	<tr><td><input type="radio" name="prio" value="2" {R2}> 2 <input type="hidden" name="mid2" value="{mid2}"></td>
		<td><input type="text" name="message2" size="60" maxlength="60" value="{MSG2}" maxlength="60"></td>
	</tr>
	<tr><td><input type="radio" name="prio" value="1" {R1}> 1 <input type="hidden" name="mid1" value="{mid1}"></td>
		<td><input type="text" name="message1" size="60" maxlength="60" value="{MSG1}" maxlength="60"></td>
	</tr>
	<tr><td><input type="radio" name="prio" value="0" {R0}>  </td>
		<td>Keine Mitteilungen ausgeben.</td>
	</tr>
	</table>
		<input type="submit" name="sichern" value="sichern"> <input type="submit" name="reset" value="reset"><br><br>
</div>		
	</form>

<!-- End Code ----------------------------------------------->
<!--/td></tr></table-->
{END_CONTENT}
</body>
</html>

