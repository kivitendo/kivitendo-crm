<?php
    require_once("../inc/stdLib.php"); 
    require_once("../inc/crmLib.php");  
    $task  = $_POST['task'] ? $_POST['task'] : $_GET['task'];
    //ToDo Funktion AjaxSql schreiben. Diese werten $_POST oder $_GET aus, erster Parameter ist Tabelle, zeiter P ist task (insert, select, update) folgende sind die serialisierten Daten 
    if( !$task ) $task = 'getEvents';
    $startGet       = $_GET['start'];
    $endGet         = $_GET['end'];
    $repeat_end_GET= $_GET['repeat_end'] == 'Invalid date' ? 'NULL' : $_GET['repeat_end'];
    $where          = $_GET['where'];
    $myuid          = $_GET['myuid'];
    
    foreach( $_POST as $key => $value ){
        $$key = htmlspecialchars($value);
    }
    $repeat_end_sql = $repeat_end == 'Invalid date' ? 'NULL' : "'$repeat_end'::TIMESTAMP"; 
    switch( $task ){
        case "initCal":
            $usr = getUserEmployee(array('feature_ac_delay','feature_ac_minlength','termbegin','termend','termseq','countrycode'));
            $usr['loginCRM'] = $_SESSION['loginCRM'];
            $usr['countrycode'] = $_SESSION['countrycode'];
            $grps = array();
            array_unshift( $grps, array( 'value' => '0', 'text' => 'Benutzer' ) );
            array_unshift( $grps, array( 'value' => '-1', 'text' => 'Alle' ) );
            $sql = "SELECT * from gruppenname ORDER BY grpname";
            $rs = $_SESSION['db']->getAll($sql);
            if ( $rs ) foreach ( $rs as $row ) array_push( $grps,  array( 'value' => $row['grpid'], 'text' => $row['grpname']) );
            $usr['grps'] = $grps;
            echo json_encode( $usr );
        break;    
        case "newEvent":
            $sql = "INSERT INTO events ( duration, title, description, \"allDay\", uid, visibility, category, prio, job, color, done, location, cust_vend_pers, repeat, repeat_factor, repeat_quantity, repeat_end ) VALUES ( '[$start, $end)','$title','$description', $allDay, $uid, $visibility, $category, $prio, '$job', '$color', '$done', '$location', '$cust_vend_pers', '$repeat', '$repeat_factor', '$repeat_quantity', $repeat_end_sql )";
            $rc = $_SESSION['db']->query( $sql ); 
        break;
        case "updateEvent":
            $sql = "UPDATE events SET title = '$title', duration = '[$start, $end)', description = '$description', \"allDay\" = $allDay, uid = '$uid', visibility = '$visibility', category = '$category', prio = '$prio', job = '$job', color = '$color', done = '$done', location = '$location', cust_vend_pers = '$cust_vend_pers', repeat = '$repeat', repeat_factor = '$repeat_factor', repeat_quantity = '$repeat_quantity', repeat_end = $repeat_end_sql  WHERE id = $id";
            $rc = $_SESSION['db']->query( $sql );   
        break;
        case "updateTimestamp":
            $sql = "UPDATE events SET  duration = '[$start, $end)', \"allDay\" = $allDay WHERE id = $id";
            $rc = $_SESSION['db']->query( $sql );   
        break;
        case "deleteEvent":
            $sql = "DELETE FROM events WHERE id = $id";
            $rc = $_SESSION['db']->query( $sql );   
        break;
        case "getEvents":
            $sql = "SELECT json_agg(json_event) FROM ( select *, lower( tsrange ) AS start, upper( tsrange ) AS end  FROM ( select id, title, repeat, repeat_factor, repeat_quantity, repeat_end, description, location, uid, visibility,  prio, category, \"allDay\", color, job, done, job_planned_end, cust_vend_pers, row_number - 1 AS repeat_num, tsrange(lower(duration) + (row_number - 1)::INT * (repeat_factor||repeat)::interval, upper(duration) + (row_number - 1)::INT * (repeat_factor||repeat)::interval) from (select t.*, row_number() over (partition by id) from events t cross join lateral (select generate_series(0, t.repeat_quantity ) i) x) foo) alle_termine where '[$startGet, $endGet)'::tsrange && tsrange AND $where CASE WHEN visibility = 0 THEN uid = $myuid ELSE uid IN (SELECT usrid FROM grpusr WHERE grpid=visibility) END ) json_event";
            //echo $sql;            
            // " // ToDo: Visibility für Gruppen implementieren.            
            $rs = $_SESSION['db']->getOne( $sql );
            echo $rs['json_agg'];   
        break; 
        case "getUsers":
            $sql = "SELECT json_agg( json_employee ) FROM (SELECT id AS value, name AS text FROM employee WHERE deleted = FALSE ) json_employee"; //login
            $rs = $_SESSION['db']->getOne( $sql );    
            echo $rs['json_agg'];  
        break;
        case "getCategory":
            //$sql = "SELECT json_agg( json_category ) FROM (SELECT id AS value, label AS text FROM event_category ORDER BY cat_order ) json_category"; 
            //$rs = $_SESSION['db']->getOne( $sql ); 
            //echo $rs['json_agg'];  
            $sql = "SELECT id AS value, label AS text FROM event_category ORDER BY cat_order "; 
            $rs = $_SESSION['db']->getJson( $sql ); 
            echo $rs;  
        break;
     }    
?>		
