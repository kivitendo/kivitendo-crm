<html>
    <head><title></title>
    {STYLESHEETS}
        <link type="text/css" REL="stylesheet" HREF="{ERPCSS}/main.css"></link>
    {JAVASCRIPTS}
    <script language="JavaScript">
        function getColor(idx) {
            f1=open('farbwahl.html?idx='+idx,'farbe','width=500,height=350');
        }
        function setColor(col,idx) {
            document.getElementById('col'+idx).value=col;
            document.getElementById('col'+idx).style.backgroundColor="#"+col;
        }
    </script>
<body>
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">.:categorie termin:.</p>

<form name="termincat" method="post" action="tcatedit.php">
    <table><tr><th>.:order:.</th><th>.:name:.</th><th>.:color:.</th><th>.:delete:.</th></tr>

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
            <input type='text' size='6' name='tcat[{idx}][ccolor]' id='col{idx}' value='{ccolor}' style='background-color:#{ccolor};'> <input type='button' onClick='getColor({idx})' value='^'>
        </td>
        <td>
            <input type='checkbox' name='tcat[{idx}[del]' value='1'>
        </td>
    </tr>
<!-- END TKat -->

    </table>
    <input type="submit" name="ok" value=".:save:.">
</form>
{END_CONTENT}
</body>
</html>

