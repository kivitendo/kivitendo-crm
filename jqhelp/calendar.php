<?php
    require_once("../inc/stdLib.php"); 
    require_once("../inc/crmLib.php");  
    $task  = $_POST['task'] ? $_POST['task'] : $_GET['task'];
    //ToDo Funktion AjaxSql schreiben. Diese werten $_POST oder $_GET aus, erster Parameter ist Tabelle, zeiter P ist task (insert, select, update) folgende sind die serialisierten Daten 
    if( !$task ) $task = 'getEvents';
    $startGet       = $_GET['start'];
    $endGet         = $_GET['end'];
    $myuid          = $_GET['myuid'];
    foreach( $_POST as $key => $value ){
        $$key = $value;
    }
    //echo "location: ".$location;
    //$url = $_POST['url'];
    switch( $task ){
        case "newEvent":
            $sql="INSERT INTO events (start, stop, title, description, allDay, uid, visibility, category, prio, job, color, done, location, cust_vend_pers, repeat, repeat_factor, repeat_quantity, repeat_end ) VALUES ( '$start'::TIMESTAMP, '$end'::TIMESTAMP,'$title','$desc', $allDay, $uid, $visibility, $category, $prio, '$job', '$color', '$done', '$location', '$cust_vend_pers', '$repeat', '$repeat_factor', '$repeat_quantity', '$repeat_end'::TIMESTAMP )";
            $rc=$_SESSION['db']->query($sql); 
            echo "SQL: ".$sql;
            //$sql = "SELECT MAX(id) FROM events";//!!!!!!!!!!!!!!!!!!!!
            //$rs = $_SESSION['db']->getOne($sql);
            //echo $rs['max'];  
        break;
        case "updateEvent":
            $sql="UPDATE events SET title = '$title', start = '$start'::TIMESTAMP, stop = '$end'::TIMESTAMP, description = '$desc', allday = $allDay, uid = '$uid', visibility = '$visibility', category = '$category', prio = '$prio', job = '$job', color = '$color', done = '$done', location = '$location', cust_vend_pers = '$cust_vend_pers', repeat = '$repeat', repeat_factor = '$repeat_factor', repeat_quantity = '$repeat_quantity', repeat_end = '$repeat_end'::TIMESTAMP  WHERE id = $id";
            $rc=$_SESSION['db']->query($sql);   
        break;
        case "updateTimestamp":
            $sql="UPDATE events SET  start = '$start'::TIMESTAMP, stop = '$end'::TIMESTAMP, allday = $allDay WHERE id = $id";
            $rc=$_SESSION['db']->query($sql);   
        break;
        case "deleteEvent":
            $sql="DELETE FROM events WHERE id = $id";
            $rc=$_SESSION['db']->query($sql);   
        break;
        case "getEvents":
            $sql="SELECT title, start, stop AS end, description AS desc, id, allday, uid, visibility, category, prio, job, color, done, location, cust_vend_pers, repeat FROM events WHERE start <= '$endGet' AND stop >= '$startGet' AND CASE WHEN visibility = 0 THEN uid = $myuid ELSE TRUE END";
            //echo $sql;            
            $rs=$_SESSION['db']->getAll($sql);
            foreach( $rs as $key => $value ){
                $rs[$key]['allDay'] = $rs[$key]['allday'] == 't' ? true : false;
                unset( $rs[$key]['allday'] );
                $rs[$key]['done'] = $rs[$key]['done'] == 't' ? true : false;
            }
            //print_r( $rs ); 
            echo json_encode( $rs );  
        break; 
        case "getUsers":
            $sql="SELECT id AS value, name AS text FROM employee WHERE deleted = FALSE"; //login
            $rs=$_SESSION['db']->getAll( $sql );
            //print_r( $rs ); 
            echo json_encode( $rs ) ;  
        break;
        case "getCategory":
            $sql="SELECT id AS value, label AS text FROM event_category ORDER BY id"; 
            $rs=$_SESSION['db']->getAll( $sql );
            //print_r( $rs ); 
            echo json_encode( $rs ) ;  
        break;
        
       
     }    
   
    return true;
    
?>	