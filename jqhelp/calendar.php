<?php
    require_once("../inc/stdLib.php"); 
    require_once("../inc/crmLib.php");  
    $task  = $_POST['task'] ? $_POST['task'] : $_GET['task'];
    //ToDo Funktion AjaxSql schreiben. Diese werten $_POST oder $_GET aus, erster Parameter ist Tabelle, zeiter P ist task (insert, select, update) folgende sind die serialisierten Daten 
    if( !$task ) $task = 'getEvents';
    $startGet       = $_GET['start'];
    $endGet         = $_GET['end'];
    $repeat_end_GET= $_GET['repeat_end'] == 'Invalid date' ? 'NULL' : $_GET['repeat_end'];
    $myuid          = $_GET['myuid'];
    
    foreach( $_POST as $key => $value ){
        $$key = $value;
    }
    $repeat_end_sql = $repeat_end == 'Invalid date' ? 'NULL' : "'$repeat_end'::TIMESTAMP"; 
    switch( $task ){
        case "newEvent":
            $sql="INSERT INTO events ( duration, title, description, \"allDay\", uid, visibility, category, prio, job, color, done, location, cust_vend_pers, repeat, repeat_factor, repeat_quantity, repeat_end ) VALUES ( '[$start, $end)','$title','$desc', $allDay, $uid, $visibility, $category, $prio, '$job', '$color', '$done', '$location', '$cust_vend_pers', '$repeat', '$repeat_factor', '$repeat_quantity', $repeat_end_sql )";
            $rc=$_SESSION['db']->query($sql); 
        break;
        case "updateEvent":
            $sql="UPDATE events SET title = '$title', duration = '[$start, $end)', description = '$desc', \"allDay\" = $allDay, uid = '$uid', visibility = '$visibility', category = '$category', prio = '$prio', job = '$job', color = '$color', done = '$done', location = '$location', cust_vend_pers = '$cust_vend_pers', repeat = '$repeat', repeat_factor = '$repeat_factor', repeat_quantity = '$repeat_quantity', repeat_end = $repeat_end_sql  WHERE id = $id";
            $rc=$_SESSION['db']->query($sql);   
        break;
        case "updateTimestamp":
            $sql="UPDATE events SET  duration = '[$start, $end)', \"allday\" = $allDay WHERE id = $id";
            $rc=$_SESSION['db']->query($sql);   
        break;
        case "deleteEvent":
            $sql="DELETE FROM events WHERE id = $id";
            $rc=$_SESSION['db']->query($sql);   
        break;
        case "getEvents":
            //$sql="SELECT title, start, stop AS end, description AS desc, id, allday, uid, visibility, category, prio, job, color, done, location, cust_vend_pers, repeat, repeat_factor, repeat_quantity, repeat_end FROM events WHERE start <= '$endGet' AND stop >= '$startGet' AND CASE WHEN visibility = 0 THEN uid = $myuid ELSE TRUE END";
            $sql = "select *, lower(tsrange) AS start, upper(tsrange) AS end  FROM (select id, title, repeat, repeat_factor, repeat_quantity, repeat_end, description, location, uid, visibility,  prio, category, \"allDay\", color, job, done, job_planned_end, cust_vend_pers, row_number - 1 AS repeat_num, tsrange(lower(duration) + (row_number - 1)::INT * (repeat_factor||repeat)::interval, upper(duration) + (row_number - 1)::INT * (repeat_factor||repeat)::interval) from (select t.*, row_number() over (partition by id) from events t cross join lateral (select generate_series(0, t.repeat_quantity ) i) x) foo) alle_termine where '[$startGet, $endGet)'::tsrange @> tsrange AND CASE WHEN visibility = 0 THEN uid = $myuid ELSE TRUE END";
          // " // ToDo: Visibility für Gruppen implementieren.            
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