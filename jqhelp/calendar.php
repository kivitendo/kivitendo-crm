<?php
/************************************************************************************************************
ToDo:
1. Calendar mit Google Calendar synchronisieren...
https://developers.google.com/google-apps/calendar/recurringevents
2. getEvents() für sämltliche User zusammen ausführen und dann mit jQuery den einzelnenen Kalendern zuordnen.
**************************************************************************************************************/

    require_once("../inc/crmLib.php");
    require_once("../inc/stdLib.php");
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
        case "getUserdata":
            //$sql = "SELECT BLA";
            //$rs = $GLOBALS['dbh']->getOne( $sql );
            //echo $rs['json_agg'];
            //Kann gleich aus der SESSION genommen werden
        break;
        case "newEvent":
            $sql = "INSERT INTO events ( duration, title, description, \"allDay\", uid, visibility, category, prio, job, color, done, location, cust_vend_pers, repeat, repeat_factor, repeat_quantity, repeat_end ) VALUES ( '[$start, $end)','$title','$description', $allDay, $uid, $visibility, $category, $prio, '$job', '$color', '$done', '$location', '$cust_vend_pers', '$repeat', '$repeat_factor', '$repeat_quantity', $repeat_end_sql )";
            $rc = $GLOBALS['dbh']->query( $sql );
        break;
        case "updateEvent":
            $sql = "UPDATE events SET title = '$title', duration = '[$start, $end)', description = '$description', \"allDay\" = $allDay, uid = '$uid', visibility = '$visibility', category = '$category', prio = '$prio', job = '$job', color = '$color', done = '$done', location = '$location', cust_vend_pers = '$cust_vend_pers', repeat = '$repeat', repeat_factor = '$repeat_factor', repeat_quantity = '$repeat_quantity', repeat_end = $repeat_end_sql  WHERE id = $id";
            $rc = $GLOBALS['dbh']->query( $sql );
        break;
        case "updateTimestamp":
            $sql = "UPDATE events SET  duration = '[$start, $end)', \"allDay\" = $allDay WHERE id = $id";
            $rc = $GLOBALS['dbh']->query( $sql );
        break;
        case "deleteEvent":
            $sql = "DELETE FROM events WHERE id = $id";
            $rc = $GLOBALS['dbh']->query( $sql );
        break;
        case "getEvents":
            $sql = "SELECT json_agg(json_event) FROM ( select *, lower( tsrange ) AS start, upper( tsrange ) AS end  FROM ( select id, title, repeat, repeat_factor, repeat_quantity, repeat_end, description, location, uid, visibility,  prio, category, \"allDay\", color, job, done, job_planned_end, cust_vend_pers, row_number - 1 AS repeat_num, tsrange(lower(duration) + (row_number - 1)::INT * (repeat_factor||repeat)::interval, upper(duration) + (row_number - 1)::INT * (repeat_factor||repeat)::interval) from (select t.*, row_number() over (partition by id) from events t cross join lateral (select generate_series(0, t.repeat_quantity ) i) x) foo) alle_termine where '[$startGet, $endGet)'::tsrange && tsrange AND $where CASE WHEN visibility = 0 THEN uid = $myuid ELSE TRUE END ) json_event";
            //echo $sql;
            // " // ToDo: Visibility für Gruppen implementieren.
            $rs = $GLOBALS['dbh']->getOne( $sql );
            echo $rs['json_agg'];
        break;
        case "getUsers":
            $sql = "SELECT json_agg( json_employee ) FROM (SELECT id AS value, name AS text FROM employee WHERE deleted = FALSE ) json_employee"; //login
            $rs = $GLOBALS['dbh']->getOne( $sql );
            echo $rs['json_agg'];
        break;
        case "getCategory":
            $sql = "SELECT json_agg( json_category ) FROM (SELECT id AS value, label AS text FROM event_category ORDER BY cat_order ) json_category";
            $rs = $GLOBALS['dbh']->getOne( $sql );
            echo $rs['json_agg'];
        break;
     }
     /*
     ToDo: getUsers, getCategory und getEvents in einem JSON zusammenfassen:
     Bsp.:
        DROP TABLE IF EXISTS users;
            CREATE TABLE users (
            id     serial,
            login     text,
            name     text
        );
        INSERT INTO users ( login, name ) VALUES ( 'emmy', 'Emmy Noether' );
        INSERT INTO users ( login, name ) VALUES ( 'ada', 'Augusta Ada Byron King' );
        INSERT INTO users ( login, name ) VALUES ( 'eu', 'Euklid von Alexandria' );
        DROP TABLE IF EXISTS groups;
        CREATE TABLE groups (
           id     serial,
           name     text
        );
        INSERT INTO groups ( name ) VALUES ( 'Mathematiker' );
        INSERT INTO groups ( name ) VALUES ( 'Informatiker' );

        select json_agg (my_json) from (select row_to_json(x0) as my_json from (SELECT json_agg( i0 ) as groups FROM ( SELECT * FROM groups ) i0) x0 union all select row_to_json(x1) from (SELECT json_agg( i1 ) as users FROM ( SELECT * FROM users ) i1) x1) bla;
    */
?>