<?php

	require 'vendor/autoload.php';

//	require "Slim/Slim.php";
//	require "Slim/Log.php";
//	require "Slim/Environment.php";
//	require "Slim/Http/Request.php";
//	require "Slim/Http/Response.php";
//	require "Slim/Http/Headers.php";
//	require "Slim/Router.php";
//require 'Slim/Slim.php';

//\Slim\Slim::registerAutoloader();

	$app = new \Slim\Slim();

	// add new Route 
$app->get('/hello/:name', function ($name) {
    echo "Hello, $name";
});
	
	// run the Slim app
	$app->run();
?>