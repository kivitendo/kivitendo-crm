<?php
/*
Wissens-DB Helper-Klassen
*/
    require_once("../inc/stdLib.php");
    include("inc/UserLib.php");
    include("inc/crmLib.php");

function suche ( $wort, $kat ) {
    if ( $kat == '' ) { 
        $kat = 0;
    } 
    $f = fopen("/tmp/x","w"); 
    fputs($f,$wort." ".$kat."\n");
    $treffer  = suchWDB($wort,$kat);
    fputs($f,print_r($treffer,true)."\n"); 
    $notfound="";
    $rc = array('msg'=>'','cnt'=>0,'data'=>'');
    if ( count( $treffer ) == 0 ) {
        $rc['msg'] = $wort.' not found';
        echo json_encode( $rc );
    } else if ( count( $treffer ) == 1 ) {
        $data = getWContent( $treffer[0]['id'] );
        mkcontent( $data );fclose($f);
    } else {
        $rc['cnt'] = count($treffer);
        $rc['data'] = $treffer;
        echo json_encode( $rc );
    }
}

function newcat( $data ) {
    $rc = insWCategorie($data);
    if ( $rc ) { echo $rc; }
    else { echo '0'; };
}

function mkcontent( $data ) {
    $data['cnt'] = 1;
    $tmp = split( ' ', $data['initdate'] );
    $data['datum'] = db2date( $tmp[0] ) . "ho " . substr( $tmp[1], 0, 5 );
    $data['content'] = stripslashes( $data['content'] );
    if ($data['owener'] == null) $data['owener'] = '';
    echo json_encode( $data );
}
function savecontent( $data ) {
    $rc = insWContent($data);
    if ( $rc ) {
        $content = getWContent($data['kat']);
        mkcontent( $content );
    } else {
        $rc = array( 'cnt' => 0, 'msg' => 'Error', 'data' => $data['content'] );
        echo json_encode( $rc );
    }
}

function history ( $data ) {
        if ( $data['id'] )        { $id = $data['id']; } else { echo 'Error'; };
        $rs = getWHistory( $id );
        $cnt = count( $rs );
        if ( $cnt > 1 ) {
            if ( isset($data['v1']) && $data['v1'] != 'undefined' ) { $v1 = $data['v1']; } else { $v1 = $cnt-2; };
            if ( isset($data['v2']) && $data['v1'] != 'undefined' ) { $v2 = $data['v2']; } else { $v2 = $cnt-1; };
            $diffrs = diff( $rs[$v1]["content"], $rs[$v2]["content"] );
            $content["version"] = $cnt;
        }
        $contentdata = '';
        if ( $rs ) {
            for ( $i = 0; $i < $cnt; $i++ ) {
                $v = $rs[$i]["version"];
                $datum  = substr($rs[$i]["initdate"],8,2).".".substr($rs[$i]["initdate"],5,2).".".substr($rs[$i]["initdate"],0,4);
                $datum .= " ".substr($rs[$i]["initdate"],11,2).":".substr($rs[$i]["initdate"],14,2);
                $contdata .= "<p><input type='checkbox' name='diff' id='diff$v' value='$i'>$v ";
                $contdata .= $datum." - ".$rs[$i]["login"]." - ".strlen($rs[$i]["content"])." Byte</p>";
            }
            if ( $cnt > 1 ) 
                $contdata .= "Version: ".$rs[$v1]["version"]."<hr />".$diffrs[0]."<br /><br />Version: ".$rs[$v2]["version"]."<hr />".$diffrs[1];
        } else {
            $contdata = ".:no_data:.$cnt";
        }
        echo $contdata;
}
function getonecontent( $id, $edit ) {
    $data = getWContent( $id );
    if ( $edit == 1 ) {
        $content  = "<textarea id='elm1' name='content' class='tinymce' cols='99' rows='21'>";
        $content .= stripslashes( $data['content'] );
        $content .= "</textarea>";
        if ($_SESSION['tinymce']) 
            $content .= "<script language='javascript' type='text/javascript' src='inc/tiny.js'></script>";
        echo $content;
    } else {
        $content .= stripslashes( $data['content'] );
        echo $content;
    }
}
function neucontent() {
        $content  = "<textarea id='elm1' name='content' cols='99' rows='21'>";
        $content .= "</textarea>";
        if ($GLOBALS['tinymce']) 
            $content .= "<script language='javascript' type='text/javascript' src='inc/tiny.js'></script>";
        echo $content;  
}
function Thread($HauptGrp,$data,&$menu)    { 
    $result=$data[$HauptGrp];
    if (count($result) > 0) {
        $x = 0;
        if ( $HauptGrp != 0 ) {
             $hide = 'submenu';
        } else {
             $hide = '';
        }
        $menu.="<ul name='$hide' class='sub".$HauptGrp."'>\n";
        $ul = "sub".$HauptGrp;
        while($thread[$HauptGrp]=array_shift($result)) {
            //$dbg = "ul: $ul ID: ".$thread[$HauptGrp]["id"]." class: sub".$HauptGrp;
            $kdh=($thread[$HauptGrp]["kdhelp"]=='t')?" +":"";
            $menu.= "<li><a href='#' id='".$thread[$HauptGrp]["id"]."' name='$kdh' class='sub".$HauptGrp."' onClick='toggleMenu(".$HauptGrp.','.$thread[$HauptGrp]["id"].")'>";
            $menu.=$thread[$HauptGrp]["name"]."</a>$kdh $dbg</li>\n"; 
            Thread($thread[$HauptGrp]["id"],$data,$menu);
        }
        $menu.="</ul>\n";
    } ;
}
function getmenu( ) {
    $menu="";
    $data = getWCategorie();
    if ($data) {
        $rc = Thread(0,$data,$menu);
    }
    echo $menu;
}
$f = fopen("/tmp/x","w"); 
fputs($f,print_r($_GET,true));
switch ($_GET['task']) {
    case 'getmenu'           : getmenu( );
                               break;
    case 'savecontent'       : savecontent( $_POST );
                               break;
    case 'history'           : history( $_GET );
                               break;
    case 'suche'             : suche( $_GET['wort'], $_GET['kat'] );
                               break;
    case 'newcat'            : newcat( $_GET );
                               break;
    case 'editcat'           : editcat( $_GET['id'] );
                               break;
    case 'filesearch'        : filesearch( );
                               break;
    case 'edit'              : getonecontent( $_GET['id'], $_GET['edit'] );
                               break;
    case 'neu'               : neucontent();
                               break;
    default                  : echo $_GET['task'].' nicht erlaubt';
};

?>
