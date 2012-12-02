<?php

	$samplingInterval = 60;		// seconds
	$stopTimeDefinition = $samplingInterval * 2 * 1000;	// double and convert to milliseconds
	$stopDistanceDefinition = 100;		// meters
	echo "$samplingInterval<br/>$stopTimeDefinition<br/>$stopDistanceDefinition<br/>";
	
	$m = new Mongo( "mongodb://localhost:27017" );
	$db = $m->selectDB( "sanhack" );
	$collection = $db->accradata;
	
	$truckidArray = $collection->distinct( "truckid" );

	foreach ( $truckidArray as $oneTruckid ) {
		
		// iterate over the locations
		$truckLocationsCursor = $collection->find( array( "truckid"=>"$oneTruckid" ) );
				
		foreach ( $truckLocationsCursor as $oneLocation ) {
			
			echo "<h1>truckid=$oneTruckid</h1>";
			if ( $truckLocationsCursor->count() > 2 ) {
				$locationOne = $truckLocationsCursor->current();
				$locationTwo = $truckLocationsCursor->getNext();
				
				foreach ( $truckLocationsCursor as $locationThree ) {
				
					$isStop = check_for_stop( $locationOne, $locationTwo, $locationThree );
	
					if ( $isStop )
						echo "<p>stop found</p>";
						
					// move to next chunk
					$locationOne = $locationTwo;
					$locationTwo = $locationThree;
					
				}
			}			
		}
		echo '<br />';
	
	}

	function check_if_point_is_stop( $lat, $long, $timestamp, $truckid ) {
	
		// look for two other points for that truck within 100m and 30 secs
		$m = new Mongo( "mongodb://localhost:27017" );
		$db = $m->selectDB( "sanhack" );
		$collection = $db->accradata;
		$truckLocationsCursor = $collection->find( array( "truckid"=>"$truckid" ) );
		
		foreach ( $truckLocationsCursor as $oneLocation ) {
			
			if ( abs( $timestamp - $oneLocation[ "timestamp" ] ) > 60 * 1000 )
				// not within a time interval
				continue;
			
			if ( distanceGeoPoints( $lat, $long, $oneLocation[ "lat" ], $oneLocation[ "long" ] > 100 )
				continue;
			
			return true;
		}
		
		return false;
	}

	function check_for_stop( $locOne, $locTwo, $locThree ) {
	
		// start by checking for timestamps within minutes of each other
		$intervalOneTwo = abs( $locOne[ "timestamp" ] - $locTwo[ "timestamp" ] );
		if ( $intervalOneTwo > 60 * 1000 ) {
			echo "OneTwo<br/>";
			return false;
		}	
		$intervalTwoThee = abs( $locTwo[ "timestamp" ] - $locThree[ "timestamp" ] );
		if ( $intervalTwoThree > 60 * 1000 ) {
			echo "TwoThree<br/>";
			return false;
		}		
		// check to see if locations are within 10m to define a stop
		$distOneTwo = distanceGeoPoints( $locOne[ "lat" ], $locOne[ "long" ], 
					$locTwo[ "lat" ], $locTwo[ "long" ] );
		var_dump( $distOneTwo );
		echo "<br/>";
		
		if ( $distOneTwo > 100 )
			return false;
		echo "<br/>";
					
		$distTwoThree = distanceGeoPoints( $locTwo[ "lat" ], $locTwo[ "long" ], 
					$locThree[ "lat" ], $locThree[ "long" ] );
		var_dump( $distTwoThree );
		if ( $distTwoThree > 100 )
			return false;

		return true;
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