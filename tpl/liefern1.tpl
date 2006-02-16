<!-- $Id: liefern1.tpl,v 1.4 2005/11/02 11:35:45 hli Exp $ -->
<html>
	<head><title></title>
	<link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
<body>


<table width="99%" border="0"><tr><td class="norm">
<!-- Beginn Code ------------------------------------------->
<p class="listtop"> Lieferantensuche</p>
<p class="listheading">| 
<a href="{action}?first=A" class="bold">A</a> |
<a href="{action}?first=B" class="bold">B</a> |
<a href="{action}?first=C" class="bold">C</a> |
<a href="{action}?first=D" class="bold">D</a> |
<a href="{action}?first=E" class="bold">E</a> |
<a href="{action}?first=F" class="bold">F</a> |
<a href="{action}?first=G" class="bold">G</a> |
<a href="{action}?first=H" class="bold">H</a> |
<a href="{action}?first=I" class="bold">I</a> |
<a href="{action}?first=J" class="bold">J</a> |
<a href="{action}?first=K" class="bold">K</a> |
<a href="{action}?first=L" class="bold">L</a> |
<a href="{action}?first=M" class="bold">M</a> |
<a href="{action}?first=N" class="bold">N</a> |
<a href="{action}?first=O" class="bold">O</a> |
<a href="{action}?first=P" class="bold">P</a> |
<a href="{action}?first=Q" class="bold">Q</a> |
<a href="{action}?first=R" class="bold">R</a> |
<a href="{action}?first=S" class="bold">S</a> |
<a href="{action}?first=T" class="bold">T</a> |
<a href="{action}?first=U" class="bold">U</a> |
<a href="{action}?first=V" class="bold">V</a> |
<a href="{action}?first=W" class="bold">W</a> |
<a href="{action}?first=X" class="bold">X</a> |
<a href="{action}?first=Y" class="bold">Y</a> |
<a href="{action}?first=Z" class="bold">Z</a> |</p>

<form name="erwsuche" enctype='multipart/form-data' action="{action}" method="post">
<table>
	<tr>
		<td class="smal"><input type="text" name="name" size="31" maxlength="75" value="{name}"><br>Name 1</td>
		<td class="smal"><input type="text" name="email" size="31" maxlength="125" value="{email}"><br>eMail</td>
	</tr>
	<tr>
		<td class="smal"><input type="text" name="department_1" size="31" maxlength="75" value="{department_1}"><br>Name 2</td>
		<td class="smal"><input type="text" name="homepage" size="31" maxlength="125" value="{homepage}"><br>Homepage</td>
	</tr>
	<tr>
		<td class="smal"><input type="text" name="street" size="31" maxlength="75" value="{street}"><br>Strasse</td>
		<td class="smal"><input type="text" name="kundennummer" size="20" maxlength="25" value="{kundennummer}"><br>unsere Kundennummer</td>
	</tr>
	<tr>
		<td class="smal"><input type="text" name="country" size="1" maxlength="10" value="{country}"><input type="text" name="zipcode" size="5" maxlength="10" value="{zipcode}"><input type="text" name="city" size="20" maxlength="75" value="{city}"><br>Land Postleitzahl Ort</td>
		<td class="smal"><input type="text" name="sw" size="31" maxlength="25" value="{sw}"><br>Stichwort/e</td>
	</tr>
	<tr>
		<td class="smal"><input type="text" name="phone" size="20" maxlength="30" value="{phone}"><br>Telefon</td>
		<td class="smal"><input type="text" name="fax" size="20" maxlength="30" value="{fax}"><br>Fax</td>
	</tr>
	<tr>
		<td class="smal"><textarea name="notes" cols="40" rows="4">{notes}</textarea><br>Bemerkungen</td>
		<td class="smal re">
			<b>{Msg}</b><br>
			<input type="checkbox" name="shipto" value="1">auch in abweichender Lieferanschrift suchen<br>
			<input type="checkbox" name="fuzzy" value="%" checked>Unscharf suchen<br>
			<input type="submit" name="suche" value="suchen"><br><br>
			<input type="submit" name="reset" value="clear">
		</td>
	</tr>
</table>
</form>
<!-- End Code ------------------------------------------->
</td></tr></table>
</body>
</html>

