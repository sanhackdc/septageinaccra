<?php

	
	
	$m = new Mongo( "mongodb://localhost:27017" );
	$db = $m->selectDB( "sanhack" );
	$collection = $db->accradata;
	
	$truckidArray = $collection->distinct( "truckid" );

	foreach ( $truckidArray as $oneTruckid ) {
		
		// iterate over the locations
		$truckLocationsCursor = $collection->find( array( "truckid"=>"$oneTruckid" ) );
				
		foreach ( $truckLocationsCursor as $oneLocation ) {
			
			if ( $truckLocationsCursor->count() > 2 ) {
				$locationOne = $truckLocationsCursor->current();
				$locationTwo = $truckLocationsCursor->getNext();
				
				foreach ( $truckLocationsCursor as $locationThree ) {
				
					var_dump( $locationOne );
					echo '<br />';
					var_dump( $locationTwo );
					echo '<br />';
					var_dump( $locationThree );
					echo '<br />';
					
					check_for_stop( $locationOne, $locationTwo, $locationThree );
	
					// move to next chunk
					$locationOne = $locationTwo;
					$locationTwo = $locationThree;

					echo '<br />';
					
				}
			}			
		}
		echo '<br />';
	
	}


	function check_for_stop( $locOne, $locTwo, $locThree ) {
	
		$dateOne = new DateTime( $locOne[ "timestamp" ], new DateTimeZone( "America/New_York" ) );
		$dateTwo = new DateTime( $locTwo[ "timestamp" ], new DateTimeZone( "America/New_York" ) );
		$dateThree = new DateTime( $locThree[ "timestamp" ], new DateTimeZone( "America/New_York" ) );

		var_dump( $dateOne );
		echo '<br />';
		var_dump( $dateTwo );
		echo '<br />';
		var_dump( $dateThree );
		echo '<br />';
		
		// start by checking for timestamps within minutes of each other
		$intervalOneTwo = abs( $locOne[ "timestamp" ] - $locTwo[ "timestamp" ] );
		if ( $intervalOneTwo > 60 )
			return false;
		$intervalTwoThee = abs( $locTwo[ "timestamp" ] - $locThree[ "timestamp" ] );
		if ( $intervalTwoThree > 60 )
			return false;
		
		var_dump( $intervalOneTwo );
		echo '<br />';
		var_dump( $intervalTwoThree );
		echo '<br />';
		
		// check to see if locations are within 10m to define a stop
		$distOneTwo = distanceGeoPoints( $locOne[ "lat" ], $locOne[ "long" ], 
					$locTwo[ "lat" ], $locTwo[ "long" ] );
		$distTwoThree = distanceGeoPoints( $locTwo[ "lat" ], $locTwo[ "long" ], 
					$locThree[ "lat" ], $locThree[ "long" ] );
		var_dump( $intervalOneTwo );
		echo '<br />';
		var_dump( $intervalTwoThree );
		echo '<br />';
		

	}
	
	function distanceGeoPoints ($lat1, $lng1, $lat2, $lng2) {
	
		$earthRadius = 3958.75;
	
		$dLat = deg2rad($lat2-$lat1);
		$dLng = deg2rad($lng2-$lng1);
	
	
		$a = sin($dLat/2) * sin($dLat/2) +
		   cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
		   sin($dLng/2) * sin($dLng/2);
		$c = 2 * atan2(sqrt($a), sqrt(1-$a));
		$dist = $earthRadius * $c;
	
		// from miles
		$meterConversion = 1609;
		$geopointDistance = $dist * $meterConversion;
	
		return $geopointDistance;

	}
	
?>