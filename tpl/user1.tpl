<!-- $Id: user1.tpl,v 1.4 2005/11/02 10:38:58 hli Exp $ -->
<html>
	<head><title>User Stamm</title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
	<style type="text/css">
	#fixiert {
	    position: absolute;
	    top: 3.2em; left: 10em;
	    width: 40em;
	    height: 38em;
	    background-color: white;
	    border: 0px solid silver;
	}
	</style>
	<script language="JavaScript">
	var on = false;
	function onoff() {
		if (on) {
			on = false;
			document.getElementById("fixiert").style.visibility = "hidden";
			document.user.mails.value="Mails zeigen";
		} else {
			on = true;
			document.getElementById("fixiert").style.visibility = "visible";
			document.user.mails.value="Mails verstecken";
		}
	}
	</script>
<body>
<p class="listtop">Benutzer Stammdaten</p>
<!-- Beginn Code ----------------------------------------------->
<form name="user" action="user1.php" method="post">
<div id="user">
<input type="button" name="mails" value="Mails zeigen" onClick="onoff()">

<table border="0" class="smal">

	<input type="hidden" name="UID" value="{UID}">
	<input type="hidden" name="Login" value="{Login}">
	<tr><td class="norm">User ID</td><td>{UID}</td>
		<td class="norm">Vertreter</td><td class="norm"><select name="Vertreter">
<!-- BEGIN Selectbox -->
						<option value="{Vertreter}"{Sel}>{VName}</option>
<!-- END Selectbox -->
						</select>
		</td></tr>
	<tr><td class="norm">Login</td><td>{Login}</td>

		<td class="norm">Etikett</td><td class="norm"><select name="etikett">
<!-- BEGIN SelectboxB -->
						<option value="{LID}"{FSel}>{FTXT}</option>
<!-- END SelectboxB -->
						</select>
		</td></tr>
	<tr><td class="norm">Name</td><td><input type="text" name="Name" value="{Name}" maxlength="75"></td>
		<td class="norm">Abteilung</td>	<td><input type="text" name="Abteilung" value="{Abteilung}" maxlength="25"></td></tr>
	<tr><td class="norm">Strasse</td><td><input type="text" name="Strasse" value="{Strasse}" maxlength="75"></td>
		<td class="norm">Position</td><td><input type="text" name="Position" value="{Position}" maxlength="25"></td></tr>
	<tr><td class="norm">Plz Ort</td><td><input type="text" name="Plz" value="{Plz}" size="6" maxlength="10"> <input type="text" name="Ort" value="{Ort}"  maxlength="75"></td>
		<td class="norm">eMail</td><td><input type="text" name="eMail" value="{eMail}" size="30" maxlength="125"></td></tr>
	<tr><td class="norm">Telefon priv.</td><td><input type="text" name="Tel1" value="{Tel1}" maxlength="30"></td>
		<td class="norm">gesch&auml;ftl.</td><td><input type="text" name="Tel2" value="{Tel2}" maxlength="30"></td></tr>
	<tr><td class="norm">Bemerkung</td><td><textarea name="Bemerkung" cols="37" rows="3">{Bemerkung}</textarea></td>
		<td class="norm">Mail-<br>unterschrift</td><td><textarea name="MailSign" cols="37" rows="3">{MailSign}</textarea></td></tr>
	<tr><td class="norm">Regel</td><td>{Regel}</td>
		<td>&nbsp;</td><td>{GRUPPE}</td></tr>
	<tr><td class="norm">Mailserver</td><td><input type="text" name="Msrv" value="{Msrv}" maxlength="40"></td>
		<td class="norm">Sprache</td>
		<td class="norm"><select name="countrycode">
<!-- BEGIN SelectboxC -->
						<option value="{LID}"{LSel}>{LTXT}</option>
<!-- END SelectboxC -->
						</select> In einer k&uuml;nftigen Version.
		</td></tr>
	<tr><td class="norm">Postfach</td><td class="norm"><input type="text" name="Postf" value="{Postf}" size="10" maxlength="25"> Kennwort <input type="text" name="Kennw" value="{Kennw}" size="10" maxlength="10"></td>
	    <td class="norm">Termine</td><td>
	    	von <select name="termbegin">{termbegin}</select> 
	    	bis <select name="termend">{termend}</select> Uhr</td></tr>
	<!--tr><td>Backup-Pf</td><td><input type="text" name="Postf2" value="{Postf2}" size="10"> </td><td></td></tr-->
	<tr><td class="norm">Intervall</td><td class="norm"><input type="text" name="Interv" value="{Interv}" size="4" maxlength="5">sec. &nbsp;&nbsp; PreSearch <input type="text" name="Pre" value="{Pre}" size="10"></td>
	    <td>&nbsp;</td><td><input type="submit" name="ok" value="sichern"></td></tr>
	<!--tr><td colspan="4"><input type="submit" name="mkmbx" value="Mailbox erzeugen"></td><td></td><td></td></tr-->

	</form>
</table>
</div>
<div id="fixiert" style="visibility:hidden"> 
	<iframe src="userMail.php?id={UID}&start=0" name="Termine" width="100%" height="100%"  marginheight="0" marginwidth="0" align="left">
	<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
	</iframe>
</div>
<!-- End Code ----------------------------------------------->
<!--/td></tr></table-->
</body>
</html>

