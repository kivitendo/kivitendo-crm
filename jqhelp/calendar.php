<?php
    require_once("../inc/stdLib.php"); 
    require_once("../inc/crmLib.php");  
    $task  = $_POST['task'] ? $_POST['task'] : $_GET['task'];
    if( !$task ) $task = 'getEvents';
    $title = $_POST['title'];
    $start = $_POST['start'];
    $end   = $_POST['end'];
    $desc  = $_POST['desc'];
    $id    = $_POST['id'];
    $allDay = $_POST['allDay'];
    $uid    = $_POST['uid'];
    $prio   = $_POST['prio'];
    $job    = $_POST['job'];
    //$url = $_POST['url'];
    switch( $task ){
        case "newEvent":
            $sql="INSERT INTO termine (start, stop, cause, c_cause, allDay, prio, job) VALUES ( '$start'::TIMESTAMP, '$end'::TIMESTAMP,'$title','$desc', $allDay, $prio, $job )";
            $rc=$_SESSION['db']->query($sql); 
            $sql = "SELECT MAX(id) FROM termine";
            $rs = $_SESSION['db']->getOne($sql);
            echo $rs['max'];  
        break;
        case "updateEvent":
            $sql="UPDATE termine SET cause = '$title', start = '$start'::TIMESTAMP, stop = '$end'::TIMESTAMP, c_cause = '$desc', allday = $allDay, uid = '$uid', prio = '$prio', job = '$job' WHERE id = $id";
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
            $sql="SELECT cause AS title, start, stop AS end, c_cause AS desc, id, allday, uid, prio, job FROM termine";
            $rs=$_SESSION['db']->getAll($sql);
            foreach( $rs as $key => $value ){
                $rs[$key]['allDay'] = $rs[$key]['allday'] == 't' ? true : false;
                unset( $rs[$key]['allday'] );
            }
            //print_r( $rs ); 
            echo json_encode($rs);  
        break; 
        case "getUsers":
            $sql="SELECT id AS value, name AS text FROM employee WHERE deleted = FALSE"; //login
            $rs=$_SESSION['db']->getAll( $sql );
            //print_r( $rs ); 
            echo json_encode( $rs ) ;  
        break;
        case "getKategorie":
            $sql="SELECT sorder AS value, catname AS text FROM termincat ORDER BY sorder"; 
            $rs=$_SESSION['db']->getAll( $sql );
            //print_r( $rs ); 
            echo json_encode( $rs ) ;  
        break;
       
     }    
   
    return true;
    
?>	