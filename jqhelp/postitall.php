<?php
require_once "../inc/stdLib.php";
/***************************************
* Class PostItAll
****************************************/
class PostItAll{
    //public properties
    public $iduser          = -1;
    public $option          = "";
    public $key             = "";
    public $content         = "";


    //Parse request
    private function getRequest(){
        //Option
        if( !isset( $_REQUEST["option"] ) || !$_REQUEST["option"] ){
            die( "No option" );
        }
        $this->option = addslashes( $_REQUEST["option"] );
        //writeLog( "Option: ".$this->option );
        //Iduser
        $this->iduser = -1;
        if(isset($_REQUEST["iduser"]) && $_REQUEST["iduser"]) {
            $this->iduser = addslashes( $_REQUEST["iduser"]);
        }
        //writeLog( "IdUser: ".$this->iduser );
        //Key
        $this->key = "";
        if(isset($_REQUEST["key"]) && $_REQUEST["key"]) {
            $this->key = addslashes( $_REQUEST["key"]);
        }
        //Content
        $this->content = "";
        if(isset($_REQUEST["content"]) && $_REQUEST["content"]) {
            $this->content = addslashes( str_replace( ';', ',', $_REQUEST["content"]));
        }
        //writeLog("Content: ".$this->content );
    }

    //Main method
    public function main(){
        //writeLog( "main" );
        $error = false;
        $ret = "";
        //Get Request
        $this->getRequest();
        header('Content-Type: application/json');

        switch( $this->option ){
            case 'test':
                if( $this->mysqli != null ){
                    $ret = "test ok";
                }
                else{
                    $error = true;
                    $ret = "test ko";
                }
                break;
            case 'getlength':
                $ret = $this->getLength( $this->iduser );
                break;
            case 'get':
                $ret = $this->get( $this->iduser, $this->key );
                break;
            case 'add':
                $this->add( $this->iduser, $this->key, $this->content );
                break;
            case 'key':
                $ret = $this->key( $this->iduser, $this->key );
                break;
            case 'remove':
                $ret = $this->removeNote( $this->iduser, $this->key );
                break;
            default:
                $error = true;
                $ret = "Option ".$this->option." not implemented";
                break;
        }

        if( $error ){
            echo json_encode( array( 'status' => 'error', 'message' => $ret ) );
        }
        else{
            echo json_encode( array( 'status' => 'success', 'message' => $ret ) );
        }
    }

    protected function getLength( $idUser ){
        $sql = "select count(*) as total from postitall where iduser='" . $idUser . "'";
        writeLog( "getLength. ".$sql );
        $rs = $GLOBALS['dbh']->getOne( $sql );
        return intval( $rs["total"] );
    }

    protected function get( $idUser, $idNote ){
        $sql = "select content from postitall where iduser='" . $idUser . "' and idnote='" . $idNote . "'";
        $rs = $GLOBALS['dbh']->getOne( $sql );
        return $rs['content'];
    }

    protected function add( $idUser, $idNote, $content ){
        return $this->save( $idUser, $idNote, $content );
    }

    protected function exists( $idUser, $idNote ){
        if( $this->get( $idUser, $idNote ) ) return true;
        return false;
    }

    protected function key( $idUser, $key ){
        if( !$key ) $key = "0";
        $sql = "select idnote from postitall where iduser='" . $idUser . "' limit 1 OFFSET ".$key;
        $array = $GLOBALS['dbh']->getOne( $sql );
        //writeLog( $array );
        if( $array ){
            writeLog( $array["idnote"] );
            return $array["idnote"];
        }
        return "";
    }

    public function getData( $idUser ){
        //writeLog( "getData" );
        $sql = "select content from postitall where iduser = " . $idUser;
        $rs = $GLOBALS['dbh']->getAll( $sql );
        writeLog( $rs );
        return $rs["content"];
    }

    protected function save( $idUser, $idNote, $content ){
        if( $this->get( $idUser, $idNote ) ) return $this->updateNote($idUser, $idNote, $content);
        return $this->insertNote($idUser, $idNote, $content);
    }

    private function insertNote( $idUser, $idNote, $content ){
        //$content = str_replace( ';', ',', $content );
        $sql = "insert into postitall (iduser, idnote, content) values ('".$idUser."','".$idNote."','".$content."')";
        //writeLog( $sql );
        return $GLOBALS['dbh']->query( $sql );
    }

    private function updateNote( $idUser, $idNote, $content ){
        $sql = "update postitall set content='".$content."' where iduser='".$idUser."' and idNote='".$idNote."'";
        //writeLog($sql );
        return  $GLOBALS['dbh']->query( $sql );
    }

    private function removeNote( $idUser, $idNote ){
        $sql = "delete from postitall where iduser='".$idUser."' and idNote='".$idNote."'";
        return  $GLOBALS['dbh']->query( $sql );
    }
}

$pia = new PostItAll();
echo $pia->main();
?>
