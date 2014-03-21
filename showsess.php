<?php
        include("inc/stdLib.php");
        $menu = $_SESSION['menu'];
?>
<html>
<head><title></title>
    <link type="text/css" REL="stylesheet" HREF="<?php echo $_SESSION['baseurl'].'css/'.$_SESSION["stylesheet"]; ?>/main.css"></link>
    <?php echo $menu['stylesheets']; ?>
    <?php echo $menu['javascripts']; ?>
</head>
<body>
<?php
echo $menu['pre_content'];
echo $menu['start_content']; 
if ( isset($_GET['ok']) && $_GET["ok"]) {
	$x = $_SESSION['menu'];
        $y = preg_replace( "^>^","&gt;",$x);
        $y = preg_replace( "^<^","&lt;",$y);
        $y = preg_replace( "^\n^","<br>",$y);
        $_SESSION['menu'] = $y;
	echo "<pre>";
        print_r($_SESSION);
	print_r($_COOKIE);
	echo "</pre>";
	echo "<form name='x' action='showsess.php' method='post'>";
	echo "<input type='submit' name='del' value='Session l&ouml;schen'>";
	echo "</form>";
        $_SESSION['menu'] = $x;
} else {
	while( list($key,$val) = each($_SESSION) ) {
		unset($_SESSION[$key]);
	}
        echo '<script type="text/javascript">window.location.href="status.php";</script>';
}
echo $menu['end_content'];
?>
</body>
