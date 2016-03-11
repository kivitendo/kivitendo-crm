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
        $this->option = $_REQUEST["option"] ;
        //Iduser
        $this->iduser = -1;
        if(isset($_REQUEST["iduser"]) && $_REQUEST["iduser"]) {
            $this->iduser = $_REQUEST["iduser"];
        }
        //Key
        $this->key = "";
        if(isset($_REQUEST["key"]) && $_REQUEST["key"]) {
            $this->key =  $_REQUEST["key"];
        }
        //Content
        $this->content = "";
        if(isset($_REQUEST["content"]) && $_REQUEST["content"]) {
            $this->content = $_REQUEST["content"]   ;
        }
    }

    //Main method
    public function main(){
        $error = false;
        $ret = "";
        //Get Request
        $this->getRequest();
        header('Content-Type: application/json');

        switch( $this->option ){
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
        if( $array ) return $array["idnote"];
        return "";
    }

    public function getData( $idUser ){
        $sql = "select content from postitall where iduser = " . $idUser;
        $rs = $GLOBALS['dbh']->getAll( $sql );
        return $rs["content"];
    }

    protected function save( $idUser, $idNote, $content ){
        if( $this->get( $idUser, $idNote ) ) return $this->updateNote($idUser, $idNote, $content);
        return $this->insertNote($idUser, $idNote, $content);
    }

    private function insertNote( $idUser, $idNote, $content ){
        return $GLOBALS['dbh']->insert( 'postitall', array( 'iduser', 'idnote', 'content' ), array( $idUser, $idNote, $content ) );
    }

    private function updateNote( $idUser, $idNote, $content ){
        return $GLOBALS['dbh']->update( 'postitall', array( 'content' ), array( $content ), "iduser = '".$idUser."' and idNote = '".$idNote."'" );
    }

    private function removeNote( $idUser, $idNote ){
        $sql = "delete from postitall where iduser='".$idUser."' and idNote='".$idNote."'";
        return  $GLOBALS['dbh']->query( $sql );
    }
}

$pia = new PostItAll();
echo $pia->main();
?>
