<html>
    <head><title></title>
    <link type="text/css" REL="stylesheet" HREF="css/main.css"></link>
<body>

<p class="listtop">.:categorie termin:.</p>

<form name="termincat" method="post" action="tcatedit.php">
    <table><tr><th>.:order:.</th><th>.:name:.</th><th>.:delete:.</th></tr>

<!-- BEGIN TKat -->
    <tr>
        <td><input type='hidden' name='tcat[{idx}][new]' value='{neu}'>
            <input type='hidden' name='tcat[{idx}][catid]' value='{cid}'>
            <input type='text' name='tcat[{idx}][sorder]' size='2' value='{order}'>
        </td>
        <td>
            <input type='text' name='tcat[{idx}][catname]' size='20' value='{cname}'>
        </td>
        <td>
            <input type='checkbox' name='tcat[{idx}[del]' value='1'>
        </td>
    </tr>
<!-- END TKat -->

    </table>
    <input type="submit" name="ok" value=".:save:.">
</form>
</body>
</html>

