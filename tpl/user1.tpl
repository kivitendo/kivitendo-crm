<!-- $Id$ -->
<html>
	<head><title>User Stamm</title>
    <link type="text/css" REL="stylesheet" HREF="../css/{ERPCSS}"></link>
    <link type="text/css" REL="stylesheet" HREF="css/{ERPCSS}"></link>
	<style type="text/css">
	#mailwin {
	    position: absolute;
	    top: 3.2em; left: 10em;
	    width: 40em;
	    height: 38em;
	    background-color: white;
	    border: 0px solid silver;
	}
	</style>
	<script language="JavaScript">
	var MailOn = false;
	function Mailonoff() {
		if (MailOn) {
			MailOn = false;
			document.getElementById("mailwin").style.visibility = "hidden";
			document.user.mails.value="Mails zeigen";
		} else {
			MailOn = true;
			document.getElementById("mailwin").style.visibility = "visible";
			document.user.mails.value="Mails verstecken";
		}
	}
    function kal(fld) {
        f=open("terminmonat.php?datum={DATUM}&fld="+fld,"Name","width=410,height=390,left=200,top=100");
        f.focus();
    }
    function go(art) {
        document.termedit.action=art+".php";
        document.termedit.submit();
    }
    function getical() {
        document.user.icalart.value = document.termedit.icalart.options[document.termedit.icalart.selectedIndex].value;
        document.user.icaldest.value = document.termedit.icaldest.value;
        document.user.icalext.value = document.termedit.icalext.value;
        return true;
    }
    function selPort() {
        po = document.user.selport.selectedIndex;
        document.user.port.value=document.user.selport.options[po].value;
    }
	</script>
<body>
<p class="listtop">Benutzer Stammdaten</p>
<!-- Beginn Code ----------------------------------------------->
<div id="mailwin" style="visibility:hidden"> 
	<iframe src="userMail.php?id={uid}&start=0" name="Termine" width="100%" height="100%"  marginheight="0" marginwidth="0" align="left">
	<p>Ihr Browser kann leider keine eingebetteten Frames anzeigen</p>
	</iframe>
</div>
<form name="user" action="user1.php" method="post" onSubmit="return getical();">
<div id="user">
<input type="reset" name="mails" value="Mails zeigen" onClick="Mailonoff()">

<table border="0" class="mini">

	<input type="hidden" name="icalart" value="{icalart}">
	<input type="hidden" name="icaldest" value="{icaldest}">
	<input type="hidden" name="icalext" value="{icalext}">
	<input type="hidden" name="uid" value="{uid}">
	<input type="hidden" name="login" value="{login}">
	<tr><td class="norm">Login</td><td>{login} : {uid}</td>
		<td class="norm">Vertreter</td><td class="norm"><select name="vertreter">
<!-- BEGIN Selectbox -->
						<option value="{vertreter}"{Sel}>{vname}</option>
<!-- END Selectbox -->
						</select>
		</td></tr>
	<tr><td class="norm">Kd-Ansicht</td><td>
		<select name="kdview">
		<option value="1"{kdview1}>Lieferanschrift
		<option value="2"{kdview2}>Bemerkungen
		<option value="3"{kdview3}>Variablen
		<option value="4"{kdview4}>FinanzInfos
		<option value="5"{kdview5}>sonst.Infos
		</select>
        </td>
		<td class="norm">Etikett</td><td class="norm"><select name="etikett">
<!-- BEGIN SelectboxB -->
						<option value="{LID}"{FSel}>{FTXT}</option>
<!-- END SelectboxB -->
						</select>
		</td></tr>
	<tr><td class="norm">Name</td><td><input type="text" name="name" value="{name}" maxlength="75"></td>
		<td class="norm">Abteilung</td>	<td><input type="text" name="abteilung" value="{abteilung}" maxlength="75"></td></tr>
	<tr><td class="norm">Strasse</td><td><input type="text" name="addr1" value="{addr1}" maxlength="75"></td>
		<td class="norm">Position</td><td><input type="text" name="position" value="{position}" maxlength="75"></td></tr>
	<tr><td class="norm">Plz Ort</td><td><input type="text" name="addr2" value="{addr2}" size="6" maxlength="10"> <input type="text" name="addr3" value="{addr3}"  maxlength="75"></td>
		<td class="norm">E-Mail</td><td><input type="text" name="email" value="{email}" size="30" maxlength="125">{emailauth}</td></tr>
	<tr><td class="norm">Telefon priv.</td><td><input type="text" name="homephone" value="{homephone}" maxlength="30"></td>
		<td class="norm">gesch&auml;ftl.</td><td><input type="text" name="workphone" value="{workphone}" maxlength="30"></td></tr>
	<tr><td class="norm">Bemerkung</td><td><textarea name="notes" cols="37" rows="3">{notes}</textarea></td>
		<td class="norm">Mail-<br>unterschrift</td><td><textarea name="mailsign" cols="37" rows="3">{mailsign}</textarea></td></tr>
	<tr><td class="norm">Regel</td><td>{role}</td>
		<td>&nbsp;</td><td>{GRUPPE}</td></tr>
	<tr><td class="norm">Mailserver</td><td><input type="text" name="msrv" value="{msrv}" size="25" maxlength="75"></td>
		<td class="norm">Mailuser</td>
		<td class="norm"><input type="text" name="mailuser" value="{mailuser}" size="25" maxlength="75">
		</td></tr>
	<tr><td class="norm">Postfach</td><td class="norm"><input type="text" name="postf" value="{postf}" size="10" maxlength="75"> Port <input type="text" name="port" value="{port}" size="4" maxlength="6">
        <select name="selport" onChange="selPort();">
            <option value=""></option>
            <option value="110">110</option>
            <option value="143">143</option>
            <option value="993">993</option>
            <option value="995">995</option>
        </select>
        </td>
		<td class="norm">Kennwort</td>
		<td class="norm"><input type="password" name="kennw" value="{kennw}" maxlength="75">
	<!--tr><td>Backup-Pf</td><td><input type="text" name="Postf2" value="{Postf2}" size="10"> </td><td></td></tr-->
		</td></tr>
	<tr><td class="norm">Protokoll</td><td><input type="radio" name="proto" value="0" {protopop}>POP <input type="radio" name="proto" value="1" {protoimap}>IMAP</td>
		<td class="norm">SSL</td>
		<td class="norm"><input type="radio" name="ssl" value="n" {ssln}>notls <input type="radio" name="ssl" value="t" {sslt}>ssl <input type="radio" name="ssl" value="f" {sslf}>tls
		</td></tr>
	<tr><td class="norm">Termine</td><td>
	    	von <select name="termbegin">{termbegin}</select> 
	    	bis <select name="termend">{termend}</select> Uhr</td>
	    <td class="norm">Terminabstand</td><td><input type="text" name="termseq" value="{termseq}" size="3"> Minuten</td></tr>
	<tr><td class="norm">Intervall</td><td>
        <input type="text" name="interv" value="{interv}" size="4" maxlength="5">sec. &nbsp;&nbsp; PreSearch <input type="text" name="pre" value="{pre}" size="10"></td>
	    <td class="norm">immer mit Pre</td><td><input type="checkbox" value='t' name="preon" {preon}>Ja</td></tr>
	<!--tr><td colspan="4"><input type="submit" name="mkmbx" value="Mailbox erzeugen"></td><td></td><td></td></tr-->

	   <tr><td>&nbsp;</td><td><input type="submit" name="ok" value="sichern"></td></tr>

	</form>
</table>
Kalenderexport: 
<form name="termedit" method="post" action="mkics.php" onSubmit="return false;">
<table>
<tr>
<td><input type="text" size="10" id="start" name="start"><img src='image/date.png' border='0' align='middle' onClick="kal('start')";></td>
<td><input type="text" size="10" id="stop" name="stop"><img src='image/date.png' border='0' align='middle' id='triggerStop' onClick="kal('stop')";></td>
<td><select name="icalart">
    <option value="file" {icalartfile}>File (Server)
    <option value="mail" {icalartmail}>E-Mail
    <option value="client" {icalartclient}>Browser
    </select>
</td>
<td><input type="text" size="4"  id="ext"  name="icalext" value="{icalext}"></td>
<td><input type="text" size="40"  id="dest"  name="icaldest" value="{icaldest}"></td>
<td><a href="#" onClick="go('mkics')">go</a></td>
</tr><tr>
<td class="klein">von</td>
<td class="klein">bis</td>
<td class="klein">Art</td>
<td class="klein">Endung</td>
<td class="klein">Ziel</td>
<td></td>
</tr></table>
</form>
<img src="{IMG}" width="500" height="280" title="Netto sales over 12 Month">
</div>
<!-- End Code ----------------------------------------------->
<!--/td></tr></table-->
</body>
</html>

