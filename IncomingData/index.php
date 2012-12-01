<?php

	require 'vendor/autoload.php';

	$app = new \Slim\Slim();

	// add new Route 
	$app->get('/hello/:name', function ($name) {
		echo "Hello, $name";
	});

	// accept new info
	$app->put( '/:geoinfo', function( $geoinfo ) {
		
		//echo "$geoinfo";
		
		$doc_array = json_decode($geoinfo, true);
		echo $doc_array;
				
		$m = new Mongo( "mongodb://localhost:27017" );
		$db = $m->selectDB( "sanhack" );
		$collection = $db->accradata;
		$collection->insert( $doc_array );
		
/*
		$collection->insert( 
			array( 
				"lat" => "30",
				"long" => "31",
				"timestamp" => "2012-12-01 12:34:56",
				"truckid" => "17"
			)
		);
*/
		
	} );
		
	// run the Slim app
	$app->run();
?>