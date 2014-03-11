<?php
    require_once("../inc/stdLib.php"); 
    require_once("../inc/crmLib.php");  
    	
	$rs = searchTermin('t');
	
	$year = date('Y');
	$month = date('m');
    
    print_r( $rs );
    echo json_encode( $rs );
    
    
	/*echo json_encode(array(
	
		array(
			'id' => 111,
			'title' => "MyFirst Event",
			'start' => "$year-$month-10",
			'url' => "http://yahoo.com/"
		),
		
		array(
			'id' => 222,
			'title' => "Birtsday Event",
			'start' => "$year-$month-20",
			'end' => "$year-$month-22",
			'url' => "http://yahoo.com/"
		)
	
	));*/

?>