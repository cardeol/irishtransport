<?php


date_default_timezone_set('Europe/Dublin');

require('vendor/autoload.php');
include('src/class.cache.php');
include('src/class.transport.php');
include('src/class.irishrail.php');
include('src/class.dublinbus.php');
include('src/class.luas.php');


$app = new \Slim\Slim(array(
    'debug' => true
));

function displayResponse($r, $cache = 0) {
  if($r['success']==0) $cache = 0;
  if($cache==0) header('Expires: '.gmdate('D, d M Y H:i:s \G\M\T', time() + $cache)); 
  header('Access-Control-Allow-Origin: *');  
  header("Content-Type: application/json");
  echo json_encode($r);
}

$app->get("/test", function() {
    die("HELLO");
});

$app->group('/dublinbus', function () use ($app) {

    $service = new TransportService(TransportServiceType::TRANSPORT_DUBLINBUS);
    
    $app->get('/stationinfo/:stopid(.json)', function ($stopid) use ($service) {        
        $response = $service->getStationInfo(strtolower($stopid));
        displayResponse($response,10);
    });

    $app->get('/stations(.json)', function () use ($service) {                
        $response = $service->getStations();
        displayResponse($response,(3600*24*30));
    });
});

$app->group('/irishrail', function () use ($app) {

    $service = new TransportService(TransportServiceType::TRANSPORT_IRISHRAIL);
    
    $app->get('/stationinfo/:stopid(.json)', function ($stopid) use ($service) {        
        $response = $service->getStationInfo(strtolower($stopid));
        displayResponse($response,10);
    });

    $app->get('/stations(.json)', function () use ($service) {                
        $response = $service->getStations();
        displayResponse($response,(3600*24*30));
    });
});

$app->group('/luas', function () use ($app) {

    $service = new TransportService(TransportServiceType::TRANSPORT_LUAS);
    
    $app->get('/stationinfo/:stopid(.json)', function ($stopid) use ($service) {        
        $response = $service->getStationInfo(strtolower($stopid));
        displayResponse($response,10);
    });

    $app->get('/stations(.json)', function () use ($service) {                
        $response = $service->getStations();
        displayResponse($response,(3600*24*30));
    });
});



$app->run();

?>