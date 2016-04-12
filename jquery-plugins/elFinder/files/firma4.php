<?php
     require_once("inc/stdLib.php");
     include("inc/template.inc");
     include("inc/persLib.php");
     include("inc/crmLib.php");
     include("inc/FirmenLib.php");
     include("inc/wvLib.php");
     if ( isset($_GET) ) {
         $fid = isset( $_GET['fid'] )?$_GET['fid']:false;
         $pid = isset( $_GET['pid'] )?$_GET['pid']:false;
         $Q   = isset( $_GET['Q'] )?$_GET['Q']:false;
     } else {
         $fid = isset( $_POST['fid'] )?$_POST['fid']:false;
         $pid = isset( $_POST['pid'] )?$_POST['pid']:false;
         $Q   = isset( $_POST['Q'] )?$_POST['Q']:false;
     };
     $fa = $vertrag = false;
     if ( !empty($fid) ) {
          $dir = $Q;
          $fa = getFirmenStamm($fid,true,$Q);
          $dir .= ($Q=='C')?$fa['customernumber']:$fa['vendornumber'];
          if ( !empty($pid) ){
               $id    = $pid;
               $co    = getKontaktStamm($pid);
               $name  = $co['cp_givenname'].' '.$co['cp_name'];
               $plz   = $co['cp_zipcode'];
               $ort   = $co['cp_city'];
               $firma = $fa['name'];
               $dir   .= '/'.$co['cp_id'];
          } else {
               $id    = $fid;
               $name  = $fa['name'];
               $plz   = $fa['zipcode'];
               $ort   = $fa['city'];
               $firma = 'Firmendokumente';
          }
          $vertrag = getCustContract($fid);
          $link1 = "firma1.php?Q=$Q&id=$fid";
          $link2 = "firma2.php?Q=$Q&fid=$fid&id=$id";
          $link3 = "firma3.php?Q=$Q&fid=$fid";
          $link4 = "firma4.php?Q=$Q&fid=$fid&pid=$pid";
     } else {    
          $fid   = 0;
          $co    = getKontaktStamm($pid);
          $name  = $co['cp_givenname'].' '.$co['cp_name'];
          $plz   = $co['cp_zipcode'];
          $ort   = $co['cp_city'];
          $firma = 'Einzelperson';
          $link1 = '#';
          $link2 = "firma2.php?Q=$Q&id=$pid";
          $link3 = '#';
          $link4 = "firma4.php?Q=$Q&pid=$pid&fid=0";
          $dir   = $co['cp_id'];
     }
     $x  =  chkdir($dir);
     $t = new Template($base);
     $t->set_file(array('doc' => 'firma4.tpl'));
     doHeader($t);
     $t->set_var(array(
               'CRMURL'  => $_SESSION['baseurl'].'crm/',
               'FAART'   => ($Q=='C')?'.:Customer:.':'.:Vendor:.',       //"Kunde":"Lieferant",
               'Q'       => $Q,
               'FID'     => $fid,
               'customernumber' => ($Q=='C')?$fa['customernumber']:$fa['vendornumber'],
               'kdnr'    => $fa['nummer'],
               'PID'     => $pid,
               'Link1'   => $link1,
               'Link2'   => $link2,
               'Link3'   => $link3,
               'Link4'   => $link4,
               'Name'    => $name,
               'Plz'     => $plz,
               'Ort'     => $ort,
               'Firma'   => $firma.$x
               ));
     $t->set_block('doc','Liste','Block');
     $user = getVorlagen();
     $i = 0;
     if ( !$user ) $user[0] = array('docid' => 0, 'vorlage' => 'Keine Vorlagen eingestellt', 'applikation' => 'O');
     if ( $user ) foreach( $user as $zeile ) {
          switch ( $zeile['applikation'] ) {
                  case 'O':
                          $format = 'OOo';
                          break;
                  case 'R':
                          $format = 'RTF';
                          break;
                  case 'B':
                          $format = 'BIN';
                          break;
          }
          $t->set_var(array(
               'LineCol'     =>   ($i%2+1),
               'ID'          =>   $zeile['docid'],
               'Bezeichnung' =>   $zeile['vorlage'],
               'Appl'        =>   $format,
          ));
          $i++;
          $t->parse('Block','Liste',true);
     }
     $t->set_block('doc','Vertrag','Block3');
     if ( $vertrag ) foreach ( $vertrag as $row ) {
          $t->set_var(array(
                    'vertrag' => $row['contractnumber'],
                    'cid'     => $row['cid']
          ));
          $t->parse('Block3','Vertrag',true);
     }     
     $t->Lpparse('out',array('doc'),$_SESSION['countrycode'],'firma');

?>
