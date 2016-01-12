<?php
if( !varExist( $_SESSION['ok'] ) ) anmelden();
global $dbh;
if( varExist( $_SESSION['dbhost'] ) ) $dbh = new myPDO( $_SESSION["dbhost"], $_SESSION["dbport"], $_SESSION["dbname"], $_SESSION["dbuser"], $_SESSION["dbpasswdcrypt"], $_SESSION["sessid"] );
//else

class myPDO extends PDO{
    private $showErr = TRUE;  //show errors in browser
    private $logAll  = TRUE;  //log all sql queries

    private function writeLog( $log ){
        file_put_contents( $_SESSION['crmpath'].'/tmp/sqlerror.log', date("Y-m-d H:i:s -> " ).print_r( $log, TRUE )."\n", FILE_APPEND );
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
        catch( PDOException $e ) {
            echo $e->getMessage();
        }
    }

    public function query( $sql ){
        $stmt = parent::prepare( $sql );
        if( $this->logAll ) $this->writeLog( __FUNCTION__.': '.$stmt->queryString );
        if( !$result = $stmt->execute() ) $this->error( $stmt->errorInfo() );
        return $result;
    }

    public function getOne( $sql, $json = FALSE ){
        $stmt = parent::prepare( $sql );
        if( $this->logAll ) $this->writeLog( __FUNCTION__.': '.$stmt->queryString );
        if( !$result = $stmt->execute() ) $this->error( $stmt->errorInfo() );
        return  $json ? json_encode( $stmt->fetch( PDO::FETCH_ASSOC ) ) : $stmt->fetch( PDO::FETCH_ASSOC );
    }

    public function getAll( $sql, $json = FALSE  ){
        if( $json ) $sql = "SELECT json_agg( json ) FROM (".$sql.") AS json";
        $stmt = parent::prepare( $sql );
        if( $this->logAll ) $this->writeLog( __FUNCTION__.': '.$stmt->queryString );
        if( !$result = $stmt->execute() ) $this->error( $stmt->errorInfo() );
        return  $stmt->fetchAll( PDO::FETCH_ASSOC );
    }

    /**********************************************
    * insert - create a new data set
    * IN: $table         - string tablename
    * IN: $fields        - array with fields
    * IN: $values        - array with values
    * IN: $lastInsertId  - string returning last id
    * OUT: last id or TRUE
    **********************************************/
    public function insert( $table, $fields, $values, $lastInsertId = FALSE ){
        $stmt = parent::prepare("INSERT INTO $table (".implode(',',$fields).") VALUES (".str_repeat("?,",count($fields)-1)."?) ".( $lastInsertId ? "returning $lastInsertId" : "") );
        if( $this->logAll ) $this->writeLog( __FUNCTION__.': '.$stmt->queryString );
        if( !$result = $stmt->execute( $values ) ) $this->error( $stmt->errorInfo() );
        return $lastInsertId ? $stmt->fetch(PDO::FETCH_ASSOC)[$lastInsertId] : $result; //parent::lastInsertId('id'); doesn't work
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
        $stmt = parent::prepare( "UPDATE $table set ".implode( '= ?, ',$fields )." = ? WHERE ".$where );
        if( $this->logAll ) $this->writeLog( __FUNCTION__.': '.$stmt->queryString );
        if( !$result = $stmt->execute( $values ) ) $this->error( $stmt->errorInfo() );
        return $result;
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

    public function begin(){
        $result = parent::beginTransaction();
        if( $this->logAll ) $this->writeLog( "PDO::beginTransaction() returns: ".$result );
            return $result;
    }

    public function commit(){
        $result = parent::commit();
        if( $this->logAll ) $this->writeLog( "PDO::commit() returns: ".$result );
        return $result;
    }

    public function rollback(){
        $result = parent::rollback();
        if( $this->logAll ) $this->writeLog( "PDO::rollback() returns: ".$result );
        return $result;
    }
}
?>