<html>
	<head><title>User Stamm</title>
{STYLESHEETS}
{CRMCSS}
{JQUERY}
{JQUERYUI}
{THEME}
{JAVASCRIPTS}    
	<script language="JavaScript">
	<!--
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
<p class="listtop">Gruppen</p>
<!-- Beginn Code ----------------------------------------------->
{msg}<br>
Zugriffbeschr&auml;nkungen f&uuml;r Kunden und Personen durch Gruppen einrichten<br><br>
<div class="mini">
	<form name="gruppe" action="user2.php" method="post">
		<input type="hidden" name="id" value="{UID}">
		Neue Gruppe: <input type="text" name="name"  size="20" maxlength="40">
		<!--input type="radio" name="rechte" value="r">lesen
		<input type="radio" name="rechte" value="w" checked>schreiben-->
		<input type="submit" name="newgrp" value="eintragen">
	</form>
	<br><br>
	<form name="grp2" action="user2.php" method="post">
		<input type="hidden" name="id" value="{UID}">
		Gruppen: <select name="gruppe">
<!-- BEGIN Selectbox -->
			<option value="{GRPID}"{SEL}>{NAME}</option>
<!-- END Selectbox -->
		</select>
		<input type="submit" name="selgrp" value="holen">
		<input type="submit" name="delgrp" value="l&ouml;schen"><br>
</div>
<table style="width:80%">
	<tr>
		<td width="45%" class="norm ce">
			Mitglieder:<br>
			<select name="grpusr[]" size="10">
<!-- BEGIN Selectbox2 -->
				<option value="{USRID}">{USRNAME}</option>
<!-- END Selectbox2 -->
			</select>
		</td>
		<td width="10%" class="norm ce">
			<input type="button" name="left" value="<--" onClick="addusr()">
			<br><br>
			<input type="button" name="right" value="-->" onClick="subusr()">
		</td>
		<td width="45%" class="norm ce">
			User:<br>
			<select name="users" size="10">
<!-- BEGIN Selectbox3 -->
				<option value="{USRID}">{USRNAME}</option>
<!-- END Selectbox3 -->
			</select>
		</td>
	</tr>
	<tr><td class="norm ce"><input type="submit" name="usrgrp" value="sichern" onClick="selall()"></td><td colspan="2"></td></tr>
</table>
</form>
<!-- End Code ----------------------------------------------->
<!--/td></tr></table-->
{END_CONTENT}
</body>
</html>
