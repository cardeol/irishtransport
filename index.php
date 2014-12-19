<?php

include("config.php");


$app = new \Slim\Slim();

// handle GET requests for /articles
$app->get('/', function () use ($app) {  
  	
});


$app->run();

?>