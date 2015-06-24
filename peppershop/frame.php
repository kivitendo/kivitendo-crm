<?php
require_once("inc/stdLib.php");

$link = $_GET['action'];
if ( isset($_GET['item']) ) {
    $shop = '?Shop='.$_GET['item'];
} else {
    $shop = '';
}

$pepperdir = 'peppershop/';
$menu = $_SESSION['menu'];
$head = mkHeader();
echo '<html>
<head><title></title>';
echo $menu['stylesheets'];
echo $head['CRMCSS'];
echo $head['JQUERY'];
echo $head['JQUERYUI'];
echo $head['THEME'];
echo $head['JQTABLE'];
echo '</head>
<body>';
echo $menu['pre_content'];
echo $menu['start_content'];
?>

<iframe  id="shop" name="shop" src="<?php echo $_SESSION['baseurl'].$pepperdir.$link.'.php'.$shop ?>" frameborder="0" width="100%" height="100%"></iframe>

<?php echo $menu['end_content']; ?>
</body>
</html>
