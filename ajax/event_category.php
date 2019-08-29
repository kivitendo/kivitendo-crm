<?php
    require_once __DIR__.'/../inc/ajax2function.php';

    function newCategory( $data ){
        $sql="INSERT INTO event_category ( label, color, cat_order ) VALUES ( '".$data['newCat']."', '".$data['newColor']."', ( SELECT max( cat_order ) + 1 AS cat_order FROM event_category) )";
        writeLog( $sql );
        $rc=$GLOBALS['dbh']->query($sql);
    }

    function getCategories(){
        $sql = "SELECT id , label, TRIM( color ) AS color FROM event_category ORDER BY cat_order DESC";
        echo $GLOBALS['dbh']->getAll( $sql, $json = TRUE );
     }

     function updateCategories( $data ){
        writeLog( $data );
        $sql = "WITH new_data (id, label, color, cat_order) AS ( VALUES ";
        foreach( $data as $key => $value ){
            $order = ( int ) ( $key / 2 );
            if( $key % 2 ) $sql .= ", '".$value['value']."', ".$order." )";//\r\n
            else $sql .= ($key ? ',' :'' )."( ".substr($value['name'], 4).", '".$value['value']."'";
        }
        $sql .= " ) UPDATE event_category SET label = d.label, color = d.color, cat_order = d.cat_order FROM new_data d WHERE d.id = event_category.id";
        echo $GLOBALS['dbh']->query( $sql );
    }

    function deleteCategory( $delCat ){
        $sql = "DELETE FROM event_category WHERE id = $delCat";
        echo $GLOBALS['dbh']->query( $sql );
    }

 ?>
