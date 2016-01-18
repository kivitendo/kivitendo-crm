<?php
        include_once("inc/connection.php");
        include_once("inc/stdLib.php");
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

$mySESSION = $_SESSION;
$myMenu = $_SESSION['menu'];
$myMenu = preg_replace( "^>^","&gt;",$myMenu);
$myMenu = preg_replace( "^<^","&lt;",$myMenu);
$myMenu = preg_replace( "^\n^","<br>",$myMenu);
$mySESSION['menu'] = $myMenu;
printArray( $mySESSION );
//printArray( $_COOKIE );

//echo '<script type="text/javascript">window.location.href="status.php";</script>';

echo $menu['end_content'];
?>
</body>
