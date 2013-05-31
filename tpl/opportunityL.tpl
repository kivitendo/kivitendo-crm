<html>
    <head><title></title>
{STYLESHEETS}
{CRMCSS}
{JQUERY}
{JQUERYUI}
{THEME}    
{JQTABLE}    
{JAVASCRIPTS}
    <script language="JavaScript">
    <!--
        function showO(id) {
            self.location="opportunity.php?id="+id
        }
    //-->
    </script>
    <script>
        $(document).ready(
            function() {
                $("#oppliste").tablesorter({widthFixed: true, widgets: ['zebra']});
            });
    </script>    
<body>
{PRE_CONTENT}
{START_CONTENT}
<p class="listtop">.:opportunity:.</p>
<table id='oppliste' class='tablesorter'>
    <thead>
    <tr>
        <td>.:company:.</td>
        <td>.:order:.</td>
        <td style="width:20;text-align:right">%</td>
        <td style="width:80;text-align:center">&euro;</td>
        <td>.:status:.</td>
        <td>.:targetdate:.</td>
        <td>.:employee:.</td>
        <td>.:changed:.</td>
    </tr>
    </thead><tbody>
<!-- BEGIN Liste -->
    <tr onClick="showO({id});">
        <td>{firma}</td>
        <td>{title}</td>
        <td style="width:20;text-align:right">{chance}</td>
        <td style="width:80;text-align:right"> {betrag}</td>
        <td>{status}</td>
        <td style="width:60;text-align:right"> {datum}</td>
        <td>{user}</td>
        <td>{chgdate}</td>
    </tr>
<!-- END Liste -->
    </tbody>
</table>
{END_CONTENT}
</body>
</html>
