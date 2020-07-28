<?php

class myPDO extends PDO{
    private $showErr = TRUE;  //show errors in browser
    private $logAll  = FALSE; //log all sql queries (only for debug)
    private $beginExecTime = 0;
    private $roundExecTime = 6;

    private function writeLog( $log ){
        file_put_contents( __DIR__.'/../log/sql.log', date("Y-m-d H:i:s -> " ).print_r( $log, TRUE )."\n", FILE_APPEND );
    }

    private function error( $error ){
        if( $this->showErr ){
            echo "SQL-Error: <pre>";
            print_r( $error );
            echo "</pre>";
        }
        $this->writeLog( $error );
        die();
    }

    public function __construct( $host, $port, $dbname, $user, $passwd, $id = FALSE ){
        try{
            parent::__construct( "pgsql:host=$host;port=$port;dbname=$dbname;", $user, $id ? @openssl_decrypt( base64_decode( $passwd ), 'AES128', $id ) : $passwd );
        }
        catch( PDOException $e ){
            echo $e->getMessage();
        }
    }

    public function query( $sql ){
        if( $this->logAll ) $this->beginExecTime = microtime( TRUE );
        $stmt = parent::prepare( $sql );
        if( !$result = $stmt->execute() ) $this->error( $stmt->errorInfo() );
        if( $this->logAll ) $this->writeLog( __FUNCTION__.': '.$stmt->queryString.': ExecTime: '.( round( ( microtime( TRUE ) - $this->beginExecTime ), $this->roundExecTime ) ) .' sec');
        return $result;
    }

    public function getOne( $sql, $json = FALSE ){
        if( $this->logAll ) $this->beginExecTime = microtime( TRUE );
        if( $json ) $sql = "SELECT row_to_json( json ) FROM (".$sql.") AS json";
        $stmt = parent::prepare( $sql );

        if( !$result = $stmt->execute() ) $this->error( $stmt->errorInfo() );
        $result =  $json ? $stmt->fetch( PDO::FETCH_ASSOC )['row_to_json'] : $stmt->fetch( PDO::FETCH_ASSOC );
        if( $this->logAll ) $this->writeLog( __FUNCTION__.': '.$stmt->queryString.': ExecTime: '.( round( ( microtime( TRUE ) - $this->beginExecTime ), $this->roundExecTime ) ) .' sec');
        return $result;
    }

    public function getAll( $sql, $json = FALSE  ){
        if( $this->logAll ) $this->beginExecTime = microtime( TRUE );
        if( $json ) $sql = "SELECT json_agg( json ) FROM (".$sql.") AS json";
        $stmt = parent::prepare( $sql );

        if( !$result = $stmt->execute() ) $this->error( $stmt->errorInfo() );
        $result = $json ? $stmt->fetchAll( PDO::FETCH_ASSOC )['0']['json_agg'] : $stmt->fetchAll( PDO::FETCH_ASSOC );
        if( $this->logAll ) $this->writeLog( __FUNCTION__.': '.$stmt->queryString.': ExecTime: '.( round( ( microtime( TRUE ) - $this->beginExecTime ), $this->roundExecTime ) ) .' sec');
        return $result;
    }

    /**********************************************
    * insert - create a new data set
    * IN: $table         - string tablename
    * IN: $fields        - array with fields
    * IN: $values        - array with values
    * IN: $lastInsertId  - string returning last id
    * IN: $sequence_name - false = standard sequence name or other sequence name
    * OUT: last id or TRUE
    **********************************************/
    public function insert( $table, $fields, $values, $lastInsertId = FALSE, $sequence_name = FALSE ){
        $values = array_map( 'trim', $values );
        if( $this->logAll ) $this->beginExecTime = microtime( TRUE );
        $stmt = parent::prepare("INSERT INTO $table (".implode(',',$fields).") VALUES (".str_repeat("?,",count($fields)-1)."?) " );
        if( !$result = $stmt->execute( $values ) ){
            $this->error( $stmt->errorInfo() );
            if( $this->logAll ) $this->writeLog( __FUNCTION__.': '.$stmt->queryString.': ExecTime: '.( round( ( microtime( TRUE ) - $this->beginExecTime ), $this->roundExecTime ) ) .' sec');
            return FALSE;
        }
        if( $lastInsertId ){
            $stmt = parent::prepare( "select * from currval( '".( ( $sequence_name ) ? $sequence_name : ( $table."_id_seq" ) )."' )" );
            if( !$result = $stmt->execute() ) $this->error( $stmt->errorInfo() );
            $lastId = $stmt->fetch( PDO::FETCH_ASSOC );//['currval'];
            if( $this->logAll ) $this->writeLog( __FUNCTION__.': '.$stmt->queryString.': ExecTime: '.( round( ( microtime( TRUE ) - $this->beginExecTime ), $this->roundExecTime ) ) .' sec' );
            return $lastId['currval'];
        }
        return 1;
    }

    /**********************************************
    * update - modify data set
    * IN: $table  - string name of the table
    * IN: $fields - array with fields
    * IN: $values - array with values
    * IN: $where  - select a data set
    * OUT: true/false
    **********************************************/
    public function update( $table, $fields, $values, $where ){
        if( $this->logAll ) $this->beginExecTime = microtime( TRUE );
        $stmt = parent::prepare( "UPDATE $table set ".implode( '= ?, ', array_map( 'trim', $fields ) )." = ? WHERE ".$where );
        if( !$result = $stmt->execute( $values ) ) $this->error( $stmt->errorInfo() );
        if( $this->logAll ) $this->writeLog( __FUNCTION__.': '.$stmt->queryString.': ExecTime: '.( round( ( microtime( TRUE ) - $this->beginExecTime ), $this->roundExecTime ) ) .' sec');
        return $result;
    }

    /**********************************************
    * update - modify multiple data set
    * IN: $table   - string name of the table
    * IN: $columns - array with columns and types
    * IN: $data    - multi array with values
    * IN: $where   - most id
    * OUT: true/false
    **********************************************/
    public function updateAll( $table, $columns, $data, $where = 'id' ){
        $data = array_map( 'trim', $data );
        if( $this->logAll ) $this->beginExecTime = microtime( TRUE );
        $columnNames = array_keys( $columns );
        $columnTypes = array_values( $columns );
        $charType    = array( 'text', 'varchar', 'char', 'character' );
        $sql = "WITH new_data( ".implode( ', ', $columnNames )." ) AS ( VALUES ";
        foreach( $data as $value ){
            $sql .= '( ';
            $valueArray = array();
            foreach( $value as $key => $data ){
                $highComma = in_array( $columnTypes[$key], $charType, TRUE ) ? "'" : "";
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
        $stmt = parent::prepare( $sql );
        if( !$result = $stmt->execute() ) $this->error( $stmt->errorInfo() );
        if( $this->logAll ) $this->writeLog( __FUNCTION__.': '.$stmt->queryString.': ExecTime: '.( round( ( microtime( TRUE ) - $this->beginExecTime ), $this->roundExecTime ) ) .' sec');
        return $result;
    }

     /**********************************************
    * getKeyValue  - gets rows of key value data as json or array
    * IN: $table  - string name of the table
    * IN: $keys   - array with keys, select data sets
    * IN: $where  - string select data sets
    * OUT: $json
    **********************************************/
    public function getKeyValueData( $table, $keys, $where, $json = TRUE ){
        if( $this->logAll ) $this->beginExecTime = microtime( TRUE );
        $sql = "SELECT jsonb_object_agg( key, val ) AS json FROM $table WHERE ( $where ) AND ( key = '".implode( "' OR key = '", $keys )."')";
        $stmt = parent::prepare( $sql );
        if( !$result = $stmt->execute() ) $this->error( $stmt->errorInfo() );
        $result = $stmt->fetch( PDO::FETCH_ASSOC )['json'];
        if( $this->logAll ) $this->writeLog( __FUNCTION__.': '.$stmt->queryString.': ExecTime: '.( round( ( microtime( TRUE ) - $this->beginExecTime ), $this->roundExecTime ) ) .' sec');
        return $json ? $result : json_decode ( $result, TRUE, 2 );
    }


    /*********************************************************
    * IN:  $statement - SQL-String with placeholder (?)
    * IN:  $data      - Array of arrays with values
    * OUT: $result    - boolean with result
    *********************************************************/
    public function executeMultiple( $statement, $data ){
        $result = parent::beginTransaction();
        $stmt = parent::prepare( $statement );
        if( $this->logAll ){
            $this->writeLog( __FUNCTION__.': '.$stmt->queryString );
            $this->writeLog( $data );
        }
        foreach( $data as $key => $value ){
            if( !$result = $stmt->execute( $value ) ){
                $this->error( $stmt->errorInfo() );
                parent::rollback();
                return $result;
            }
        }
        return parent::commit();
    }

    /**********************************************************
     * IN:  $table  - string name of table
     * IN:  $data   - array of associative arrays
     * OUT: $result - boolean with result
     * *******************************************************/
    public function insertMultiple( $tableName, $data ){
        $data = array_map( 'trim', $data );
        if( $this->logAll ) $this->beginExecTime = microtime( TRUE );
        $rowsSQL = array(); //Will contain SQL snippets.
        $toBind  = array(); //Will contain the values that we need to bind.
        $columnNames = array_keys( $data[0] ); //Get a list of column names to use in the SQL statement.
        //Loop through our $data array.
        foreach( $data as $arrayIndex => $row ){
            $params = array();
            foreach($row as $columnName => $columnValue){
                $param = ":" . $columnName . $arrayIndex;
                $params[] = $param;
                $toBind[$param] = $columnValue;
            }
            $rowsSQL[] = "(" . implode(", ", $params) . ")";
        }
        //Construct our SQL statement
        $sql = "INSERT INTO $tableName (".implode( ", ", $columnNames ).") VALUES ".implode( ", ", $rowsSQL );
        //Prepare our PDO statement.
        $stmt = parent::prepare( $sql );
        //Bind our values.
        foreach($toBind as $param => $val){
            $stmt->bindValue( $param, $val );
        }
        //Execute our statement (i.e. insert the data).
        $result = $stmt->execute();
        if( $this->logAll ) $this->writeLog( __FUNCTION__.': '.$stmt->queryString.': ExecTime: '.( round( ( microtime( TRUE ) - $this->beginExecTime ), $this->roundExecTime ) ) .' sec');
        return $result;
    }

    public function begin(){
        if( $this->logAll ) $this->beginExecTime = microtime( TRUE );
        $result = parent::beginTransaction();
        if( $this->logAll ) $this->writeLog( "PDO::beginTransaction() returns: ".$result.': ExecTime: 0' );
            return $result;
    }

    public function exec( $sql ){
        if( $this->logAll ) $this->writeLog( __FUNCTION__.': '.$sql );
        $result = parent::exec( $sql );
        return $result;
    }
    public function commit(){
        $result = parent::commit();
        if( $this->logAll ) $this->writeLog( "PDO::commit() returns: ".$result.': ExecTime: '.( round( ( microtime( TRUE ) - $this->beginExecTime ), $this->roundExecTime ) ) .' sec');
        return $result;
    }

    public function rollback(){
        $result = parent::rollback();
        if( $this->logAll ) $this->writeLog( "PDO::rollback() returns: ".$result );
        return $result;
    }

    public function setShowError( $value ){
         $this->showErr = $value;
    }
}
?>
