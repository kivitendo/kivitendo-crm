<html>
	<head><title></title>
{STYLESHEETS}
{CRMCSS}
{JAVASCRIPTS}    
{THEME} 
	<script language="JavaScript">
	<!--
		function showH() {
			f1=open("dochelp.html","hilfe","width=550,height=630,left=100,top=100");
		}
	//-->
	</script>
	<script>
     $(document).ready(
        function(){
            $( "input[type=reset]")
            .button().click(function( event ) { 
                 event.preventDefault();
                 document.location.href = this.getAttribute('name');
            });
        }); 
	</script>    
    </head>
<body>
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">Dokumentvorlagen</p>
<form>
	<input type="reset" name="dokument1.php" value='Dokumente'>
	<input type="reset" name="{Link2}" value='neue Vorlage'>
	<input type="reset" name="{Link3}" value='Felder'>
	<input type="reset" name="#" onClick="showH()" value='Hilfe'>
</form>
<br>
<h3>{vorlage}</h3>

<table>
<!-- BEGIN Liste -->
	<tr>
		<form name="update" action="dokument3.php" method="post">
		<input type="hidden" name="fid" value="{fid}">
		<input type="hidden" name="docid" value="{docid}">
		<td><input type="text" name="feldname" value="{feldname_}" size="15" maxlength="20"></td>
		<td><input type="text" name="platzhalter" value="{platzhalter_}" size="12" maxlength="20"></td>
		<td><input type="text" name="laenge" value="{laenge_}" size="2"maxlength="5"> </td>
		<td><input type="text" name="zeichen" value="{zeichen_}" size="12" maxlength="20"></td>
		<td><input type="text" name="position" value="{position_}" size="2" maxlength="5"></td>
		<td></td>
	</tr>
	<tr>
		<td colspan="5"><input type="text" name="beschreibung" value="{beschreibung_}" size="65"  maxlength="200"></td>
		<td><input type="submit" name="ok" value="ok"><input type="submit" name="del" value="del"></td>
		</form>
	</tr>
<!-- END Liste -->
	<tr>
		<td colspan="6"><hr></td></tr>
	</tr>
	<tr>
		<form name="neu" action="dokument3.php" method="post">
		<input type="hidden" name="docid" value="{docid}">
		<td class="mini"><input type="text" name="feldname" size="15" maxlength="20"><br>Feldname</td>
		<td class="mini"><input type="text" name="platzhalter" size="12" maxlength="20"><br>Platzhalter</td>
		<td class="mini"><input type="text" name="laenge" size="2" maxlength="5"><br>L&auml;nge</td>
		<td class="mini"><input type="text" name="zeichen" size="12" maxlength="20"><br>Zeichen</td>
		<td class="mini"><input type="text" name="position" size="2" maxlength="5"><br>Pos.</td>
		<td class="mini"></td>
	</tr>
	<tr>
		<td class="mini" colspan="5"><input type="text" name="beschreibung" size="60" maxlength="200"><br>Beschreibung</td>
		<td><input type="submit" name="neu" value="neu"></td>
		</form>
	</tr>
</table>
{END_CONTENT}
</body>
</html>
