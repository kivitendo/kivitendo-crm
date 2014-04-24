<?php
    require_once("../inc/stdLib.php"); 
    require_once("../inc/crmLib.php");  
    $task  = $_POST['task'] ? $_POST['task'] : "getEvents";
    $title = $_POST['title'];
    $start = $_POST['start'];
    $end   = $_POST['end'];
    $id    = $_POST['id'];
    //$url = $_POST['url'];
    switch( $task ){
        case "newEvent":
            $sql="INSERT INTO termine (start,stop,cause) VALUES ('$start'::TIMESTAMP,'$end'::TIMESTAMP,'$title')";
            $rc=$_SESSION['db']->query($sql);   
        break;
        case "updateEvent":
            $sql="UPDATE termine SET start = '$start'::TIMESTAMP, stop = '$end'::TIMESTAMP WHERE id = $id";
            $rc=$_SESSION['db']->query($sql);   
        break;
        case "getEvents":
            $sql="SELECT start, stop AS end, cause AS title, id FROM termine";
            $rs=$_SESSION['db']->getAll($sql); 
            echo json_encode($rs);  
            ;
        break; 
     }    
   
    return true;
    
?>	