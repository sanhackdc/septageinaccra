<?php
	
	$m = new Mongo( "mongodb://localhost:27017" );
	$db = $m->selectDB( "sanhack" );
	$collection = $db->accradata;
	$collection->insert( 
		array(
			"lat" => $_POST[ "lat" ],
			"long" => $_POST[ "long" ],
			"timestamp" => $_POST[ "timestamp" ],
			"truckid" => $_POST[ "truckid" ]
		)
	 );

?>