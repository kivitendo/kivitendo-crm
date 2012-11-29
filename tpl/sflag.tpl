<html>
    <head><title></title>
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>
<body>

<p class="listtop">.:special flag:.</p>

<form name="termincat" method="post" action="sflagedit.php">
    <table><tr><th>.:order:.</th><th>.:name:.</th><th>.:delete:.</th></tr>

<!-- BEGIN SonderFlag -->
    <tr>
        <td><input type='hidden' name='sonder[{idx}][new]' value='{neu}'>
            <input type='hidden' name='sonder[{idx}][svalue]' value='{svalue}'>
            <input type='text' name='sonder[{idx}][sorder]' size='2' value='{order}'>
        </td>
        <td>
            <input type='text' name='sonder[{idx}][skey]' size='20' value='{skey}'>
        </td>
        <td>
            <input type='checkbox' name='sonder[{idx}][del]' value='1'>
        </td>
    </tr>
<!-- END SonderFlag -->

    </table>
    <input type="submit" name="ok" value=".:save:.">
</form>
</body>
</html>

