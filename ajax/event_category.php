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
       //WITH new_data (id, label, color, cat_order) AS ( VALUES ( 12, 'Büro', '', 0 ),( 11, 'Inter-Data', '', 1 ),( 10, 'Geburtstage', '', 2 ),( 2, 'Urlaub / Krankheit', '#ffffff', 3 ),( 5, 'Ersatzfahrzeuge', '#ddd', 4 ),( 3, 'Werkstatt-Plan', '#111', 5 ) )
       //UPDATE event_category SET label = d.label, color = d.color, cat_order = d.cat_order FROM new_data d WHERE d.id = event_category.id;
        $table = 'event_category';
        $columns = array( 'label' => 'text', 'color' => 'text', 'id' => 'int', 'cat_order' => 'int' );
        $where = 'id';

        $columnNames = array_keys( $columns );
        $columnTypes = array_values( $columns );
        //writeLog( var_dump( $columnNames ) );

        $sql = "WITH new_data( ".implode( ', ', $columnNames )." ) AS ( VALUES ";
        foreach( $data as $value ){
            $sql .= '( ';
            $valueArray = array();
            foreach( $value as $key => $data ){
                $highComma = ( $columnTypes[$key] == 'text' || $columnTypes[$key] == 'varchar' || $columnTypes[$key] == 'char' ) ? "'" : "";
                array_push( $valueArray, $highComma.$data.$highComma );
            }
            $sql .= implode( ', ', $valueArray );
            $sql .= ' ), ';
          }
          $sql =  substr( $sql, 0, -2 ); // remove last comma
          $sql .= " ) UPDATE ".$table." SET ";
          $columnArray = array();
          foreach( $columnNames as $value ){
              array_push( $columnArray, $value.' = d.'.$value );
          }
          $sql .= implode( ', ', $columnArray );
          $sql .= ' FROM new_data d WHERE d.'.$where.' = '.$table.'.'.$where;
          writeLog( 'SQL: '.$sql );
          echo $GLOBALS['dbh']->query( $sql );
    }

    function deleteCategory( $delCat ){
        $sql = "DELETE FROM event_category WHERE id = $delCat";
        echo $GLOBALS['dbh']->query( $sql );
    }

 ?>
