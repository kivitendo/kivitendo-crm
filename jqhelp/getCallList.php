<?php
/*********************************************************************
*** CRMTI - Customer Relationship Management Telephone Integration ***
*** geschrieben von Ronny Kumke ronny@lxcars.de Artistic License 2 ***
*** begonnen im April 2011, liest crmti aus, Version 1.1.0         ***
*********************************************************************/

require_once("../inc/conf.php");
//require_once("../inc/db.php");
require_once("../inc/stdLib.php");

$action = $_GET['action'];

switch( $action ){
    case 'complete':
        getCompleteList();
        break;
    case 'last':
        getLastItem();
        break;
    default:
        echo "Error: action not defined!";
}

function CreateFunctionsAndTable(){ //Legt beim ersten Aufruf der Datenbank die benötigten Tabellen und Funktionen an.
    global $db;
    $sql = file_get_contents("update/install_crmti.sql");
    $statement = explode(";;", $sql );//zum Erzeugen von Funktionen sind Semikola notwendig, fertiges sql-Statement = ;;
    $sm0 = '/\/\*.{0,}\*\//';// SuchMuster ' /* bla */ '
    $sm1 = '/--.{0,}\n/';    // SuchMuster ' --bla \n '
    foreach( $statement as $key=>$value ){
        $sok0 = preg_replace( $sm0, '',$statement[$key] );
        $sok1 = preg_replace( $sm1, '',$sok0 );
        $rc=$db->query( $sok1 );
    }
    $sql="insert into schema_info (tag, login) values ('crm_telefon_integration', '".$_SESSION['login'].")'";
    $rc=$_SESSION['db']->query($sql);
}

function getCompleteList(){
    global $bgcol;
    //global $db;
    $sql = "SELECT json_agg( json_calls ) FROM ( SELECT EXTRACT(EPOCH FROM TIMESTAMPTZ(crmti_init_time)) AS call_date, crmti_status, crmti_src, crmti_dst, crmti_caller_id, crmti_caller_typ, crmti_direction  FROM crmti ORDER BY crmti_init_time DESC) AS json_calls";
    $rs = $_SESSION['db']->getone( $sql );
    if( !$rs ){
        CreateFunctionsAndTable();
    }
    //print_r( $rs );
    //echo $rs['json_agg'];
    echo "{
    headers : [
      [

        { text: 'First Name', class: 'fname', width: '10%' },
        'Last Name',
        { text: 'Age', class: 'age', 'data-sorter' : false },
        'Total',
        { text: 'Discount', class : 'sorter-false' },
        { text: 'Date', class : 'date' }
      ]
    ],
    footers : 'clone',
    rows : [

      [ 'Peter', 'Parker', 28, '$9.99',  '20%', 'Jul 6, 2006 8:14 AM'   ],
      [ 'John',  'Hood',   33, '$19.99', '25%', 'Dec 10, 2002 5:14 AM'  ],
      [ 'Clark', 'Kent',   18, '$15.89', '44%', 'Jan 12, 2003 11:14 AM' ],


      { newTbody: true, class: 'tablesorter-infoOnly' },
      { cells : [ { html: '<strong>Info Row</strong>', colSpan: 6 } ] },


      { newTbody: true },
      [ 'Bruce', 'Evans',    22, '$13.19',  '11%', 'Jan 18, 2007 9:12 AM'  ],
      [ 'Bruce', 'Almighty', 45, '$153.19', '44%', 'Jan 18, 2001 9:12 AM'  ],

      { class: 'specialRow',
        cells: [

          { text: 'Fred', class: 'fname' },
          { text: 'Smith', class: 'lname' },
          { text: 18, class: 'age', 'data-info': 'fake ID!, he's only 16' },
          { text: '$22.44', class: 'total' },
          '8%',
          { text: 'Aug 20, 2012 10:15 AM', class: 'date' }
        ],
        'data-info' : 'This row likes turtles'
      }
    ]
  }";

    return 1;
}

function getLastItem(){
    return 'lastItem';
}

?>