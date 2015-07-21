<?php

define("ROOTDIR",dirname(dirname(__FILE__)));

include(ROOTDIR.'/src/class.cache.php');
include(ROOTDIR.'/src/class.transport.php');
include(ROOTDIR.'/src/class.irishrail.php');
include(ROOTDIR.'/src/class.dublinbus.php');
include(ROOTDIR.'/src/class.luas.php');


class transportTest extends PHPUnit_Framework_TestCase
{

 	
    public function testTrainStations() {
    	$train = new TransportService(TransportServiceType::TRANSPORT_IRISHRAIL);
    	//$stations = $train->getStations(null);
        print_r($stations);
    	//$this->assertTrue(is_array($stations));
    	//$this->assertTrue(count($stations)>0);
    }

/*
    public function testBusStops() {
    	$bus = new TransportService(TransportServiceType::TRANSPORT_DUBLINBUS);
    	$stops = $bus->getStations(array("route" => "151"));
    	//print_r($stops);
    }

     public function testLuasStops() {
        $bus = new TransportService(TransportServiceType::TRANSPORT_LUAS);
        $stops = $bus->getStations(array("route" => "151"));
        //print_r($stops);
    }

*/
}


?>