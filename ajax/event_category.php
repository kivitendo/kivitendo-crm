<?php
    require_once __DIR__.'/../inc/ajax2function.php';

    function newCategory( $data ){ // insert a new category and return the current id
        echo $GLOBALS['dbh']->insert( 'event_category', array( 'label', 'color', 'cat_order' ), $data, TRUE );
    }

    function getCategories(){
        $sql = "SELECT id , label, TRIM( color ) AS color FROM event_category ORDER BY cat_order ASC";
        echo $GLOBALS['dbh']->getAll( $sql, $json = TRUE );
     }

     function updateCategories( $data ){
        $columns = array( 'label' => 'text', 'color' => 'text', 'id' => 'int', 'cat_order' => 'int' );
        echo $GLOBALS['dbh']->updateAll( 'event_category', $columns, $data );
    }

    function deleteCategory( $delCat ){
        $sql = "DELETE FROM event_category WHERE id = $delCat";
        echo $GLOBALS['dbh']->query( $sql );
    }

 ?>
