<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);



date_default_timezone_set('Europe/Dublin');

require('vendor/autoload.php');
include('config.php'); // optional settings
include('src/class.cache.php');
include('src/class.transport.php');
include('src/class.irishrail.php');
include('src/class.dublinbus.php');
include('src/class.luas.php');

use Abraham\TwitterOAuth\TwitterOAuth;

$app = new \Slim\Slim(array(
    'debug' => true
));

function displayResponse($r, $cache = 0) {
  //if(is_array($r)) if($r['success']==0) $cache = 0;  
  if($cache==0) header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + $cache)); 
   if (isset($_SERVER['HTTP_ORIGIN'])) {
        header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
        header('Access-Control-Allow-Credentials: true');        
    }
    // Access-Control headers are received during OPTIONS requests
    if ($_SERVER['REQUEST_METHOD'] == 'OPTIONS') {

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_METHOD']))
            header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");         

        if (isset($_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']))
            header("Access-Control-Allow-Headers: {$_SERVER['HTTP_ACCESS_CONTROL_REQUEST_HEADERS']}");

    }
    header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
    header("Cache-Control: post-check=0, pre-check=0", false);
    header("Pragma: no-cache");
    header('Content-Type: application/json'); 
    echo json_encode($r);
}


$app->get("/test", function() {
   
    
});


$app->group('/dublinbus', function () use ($app) {
    $app->get('/stationinfo/:stopid(.json)', function ($stopid)  {        
        $service = new TransportService(TransportServiceType::TRANSPORT_DUBLINBUS);
        $response = $service->getStationInfo(strtolower($stopid));
        displayResponse($response,10);
    });

    $app->get('/stations(.json)', function () {                
        $service = new TransportService(TransportServiceType::TRANSPORT_DUBLINBUS);
        $response = $service->getStations();
        displayResponse($response,(3600*24*30));
    });
});

$app->group('/irishrail', function () use ($app) {
    $app->get("/news(.json)", function() {
        $query = array(
          "q" => "IrishRail"
        );     
        $connection = new TwitterOAuth(CONSUMER_KEY, CONSUMER_SECRET, ACCESS_TOKEN, ACCESS_TOKEN_SECRET);
        $results = $connection->get('statuses/user_timeline', array( "screen_name" => "irishrail", "count" => 25, "exclude_replies" => true ));
        displayResponse($results,20);
    });

    $app->get('/stationinfo/:stopid(.json)', function ($stopid) {        
        $service = new TransportService(TransportServiceType::TRANSPORT_IRISHRAIL);
        $response = $service->getStationInfo(strtolower($stopid));
        displayResponse($response,10);
    });

    $app->get('/stations(.json)', function () {                
        $service = new TransportService(TransportServiceType::TRANSPORT_IRISHRAIL);
        $response = $service->getStations();
        displayResponse($response,(3600*24*30));
    });
});

$app->group('/luas', function () use ($app) {
  
    $app->get('/stationinfo/:stopid(.json)', function ($stopid)  {        
        $service = new TransportService(TransportServiceType::TRANSPORT_LUAS);
        $response = $service->getStationInfo(strtolower($stopid));
        displayResponse($response,10);
    });

    $app->get('/stations(.json)', function () {                
        $service = new TransportService(TransportServiceType::TRANSPORT_LUAS);
        $response = $service->getStations();
        displayResponse($response,(3600*24*30));
    });
});



$app->run();

?>