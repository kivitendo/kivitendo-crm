<?php
function convertPostits(){
    $pid = 0;
    $content = '{"id":xxxidxxx,"created":1451038148498,"domain":"http://localhost","page":"/kivitendo-dev/crm/firma1.php","osname":"5.0 (X11)","content":"<b>xxxcausexxx</b><br>xxxcontentxxx","position":"absolute","posX":"1287px","posY":"309px","right":"","height":240,"width":200,"minHeight":240,"minWidth":180,"oldPosition":{},"style":{"tresd":true,"backgroundcolor":"#FBEC88","textcolor":"#000","textshadow":false,"fontfamily":"verdana","fontsize":"small","arrow":"none"},"features":{"prefix":"#PIApostit_","filter":"domain","savable":true,"randomColor":false,"toolbar":true,"autoHideToolBar":true,"removable":true,"askOnDelete":0,"draggable":true,"resizable":true,"editable":true,"changeoptions":true,"blocked":true,"minimized":true,"expand":true,"fixed":true,"addNew":true,"showInfo":0,"pasteHtml":0,"htmlEditor":0,"autoPosition":0,"addArrow":"back"},"flags":{"blocked":false,"minimized":false,"expand":false,"fixed":false,"highlight":false},"attachedTo":{"element":"","position":"right","fixed":true,"arrow":true}}';
    foreach( $_SESSION['all_erp_users'] as  $value ){
        $sql = "SELECT id FROM employee WHERE login = '".$value['login']."'";
        $rs = $_SESSION['db']->getOne( $sql );
        $value['crmuid'] = $rs['id'];
        $sql1 = "SELECT cause, notes FROM postit WHERE employee = ".$value['crmuid'];
        $rs1 = $_SESSION['db']->getAll( $sql1 );
        foreach( $rs1 as $key => $postit ){
            $tmp0 = str_replace ( "xxxcausexxx", $postit['cause'], $content );
            $tmp1 = str_replace ( "xxxcontentxxx", $postit['notes'], $tmp0 );
            $tmp2 = str_replace ( "xxxidxxx", ++$pid, $tmp1 );
            $number = $key + 1;
            $sql2 = "INSERT INTO postitall ( iduser, idnote, content ) VALUES ('".$value['user_id']."', 'Postit_".$number."', '".$tmp2."')";
            $rc = $_SESSION['db']->query( $sql2 );
        }
    }
}
?>