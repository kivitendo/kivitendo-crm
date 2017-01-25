<?php

require_once __DIR__.'/../inc/ajax2function.php';

//ToDo: Add autocompletion, etc

function getHistory(){
    $rs = $GLOBALS['dbh']->getOne( "select val from crmemployee where uid = '" . $_SESSION["loginCRM"]."' AND manid = ".$_SESSION['manid']." AND key = 'search_history'" );
    echo $rs['val'] ? $rs['val'] : '0';
}

?>