<?php

include("config.php");

$app = new \Slim\Slim(array(
    'debug' => true
));


$app->get('/', function () use ($app) {  
  	$response = array("test" =>"testing data");
  	echoResponse(200,$response);
});


$app->get("/test", function() {
	//$d = new DublinBus();
	//$info = $d->getStationInfo("3237",null);
  // $info = $d->getRoutesByStopId("3237");
  //$info = $d->getStations(null);
  //$info = $d->getAllRoutes();
  $dart = new IrishRail();
  $info = $dart->getStations();

	print_r($info);
});


$app->get("/stations", function() {
	$d = new DublinBus();
	// $info = $d->getStationInfo("3237",null);
    $info = $d->getStations("20");

	print_r($info);
});


function echoResponse($status_code, $response)  {
	$app = \Slim\Slim::getInstance();
    $app->status($status_code);
    $app->contentType('application/json'); 
    echo json_encode($response);
}

$app->get("/deletecache", function() {
  $cache = new AppCache();
  $ache->deleteCache();
});

$app->get('/getstationinfo/:code', function($code) {
  $code = strtolower($code);
  $code = str_replace(".json", "", $code);
  if(empty($code)) return false;  
  header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 20));
  header('Access-Control-Allow-Origin: *');  
  header("Content-Type: application/json");
  $content = APIRAIL::getStationInfo($code);
  echo json_encode($content);
});

$app->get('/gettraininfo/:code', function($code) {
  $code = strtolower($code);
  $code = str_replace(".json", "", $code);
  if(empty($code)) return false;  
  header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + 10));
  header('Access-Control-Allow-Origin: *');  
  header("Content-Type: application/json");
  $content = APIRAIL::getTrainInfo($code);
  echo json_encode($content);
});

$app->get("/getstations",function() {
  header("Content-Type: application/json");
  $res = json_encode(APIRAIL::getStations());
  echo $res;
});


$app->run();

?>