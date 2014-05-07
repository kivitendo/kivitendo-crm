<?php
    require_once("../inc/stdLib.php"); 
    require_once("../inc/crmLib.php");  
    $task  = $_POST['task'] ? $_POST['task'] : "getEvents";
    $title = $_POST['title'];
    $start = $_POST['start'];
    $end   = $_POST['end'];
    $desc  = $_POST['desc'];
    $id    = $_POST['id'];
    $allDay = $_POST['allDay'];
    //$url = $_POST['url'];
    switch( $task ){
        case "newEvent":
            $sql="INSERT INTO termine (start, stop, cause, c_cause, allDay) VALUES ( '$start'::TIMESTAMP, '$end'::TIMESTAMP,'$title','$desc', $allDay )";
            $rc=$_SESSION['db']->query($sql); 
            $sql = "SELECT MAX(id) FROM termine";
            $rs = $_SESSION['db']->getOne($sql);
            echo $rs['max'];  
        break;
        case "updateEvent":
            $sql="UPDATE termine SET cause = '$title', start = '$start'::TIMESTAMP, stop = '$end'::TIMESTAMP, c_cause = '$desc', allday = $allDay WHERE id = $id";
            $rc=$_SESSION['db']->query($sql);   
        break;
        case "updateTimestamp":
            $sql="UPDATE termine SET  start = '$start'::TIMESTAMP, stop = '$end'::TIMESTAMP, allday = $allDay WHERE id = $id";
            $rc=$_SESSION['db']->query($sql);   
        break;
        case "deleteEvent":
            $sql="DELETE FROM termine WHERE id = $id";
            $rc=$_SESSION['db']->query($sql);   
        break;
        case "getEvents":
            $sql="SELECT cause AS title, start, stop AS end, c_cause AS desc, id, allday FROM termine";
            $rs=$_SESSION['db']->getAll($sql);
            foreach( $rs as $key => $value ){
                $rs[$key]['allDay'] = $rs[$key]['allday'] == 't' ? true : false;
                unset( $rs[$key]['allday'] );
            }
            //print_r( $rs ); 
            echo json_encode($rs);  
        break; 
       
     }    
   
    return true;
    
?>	