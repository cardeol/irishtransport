<?php

include("config.php");


$app = new \Slim\Slim(array(
    'debug' => true
));


$app->get('/', function () use ($app) {  
  	$response = array("test" =>"testing data");
  	echoResponse(200,$response);
});


function echoResponse($status_code, $response)  {
	$app = \Slim\Slim::getInstance();
    $app->status($status_code);
    $app->contentType('application/json'); 
    echo json_encode($response);
}


$app->run();

?>