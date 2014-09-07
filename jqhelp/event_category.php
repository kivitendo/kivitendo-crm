<?php
    require_once("../inc/stdLib.php"); 
    require_once("../inc/crmLib.php");  
    $task  = $_POST['task'] ? $_POST['task'] : $_GET['task'];
    $newCat = $_POST['newCat'];
    $newColor = $_POST['newColor'];
    //echo "Task: ".$task;
    switch( $task ){
        case "newCategory":
            $sql="INSERT INTO event_category ( label, color, cat_order ) VALUES ( '$newCat', '$newColor', ( SELECT max( cat_order ) + 1 AS cat_order FROM event_category) )";
            $rc=$_SESSION['db']->query($sql); 
        break;
        
        case "getCategories":
            $sql = "SELECT json_agg( json_category ) FROM ( SELECT id, label, color FROM event_category ORDER BY cat_order DESC ) AS json_category ;";
            //echo $sql;            
           
            $rs = $_SESSION['db']->getOne( $sql );
            echo $rs['json_agg'];   
        break; 
     }
 ?>