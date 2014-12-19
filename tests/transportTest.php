<?php

define("ROOTDIR",dirname(dirname(__FILE__)));

include(ROOTDIR."/src/class.transport.php");


class transportTest extends PHPUnit_Framework_TestCase
{

 	
    public function testTrainStations() {
    	$train = new TransportService(TransportServiceType::TRANSPORT_TRAIN);
    	$stations = $train->getStations();
    	$this->assertTrue(is_array($stations));
    	$this->assertTrue(count($stations)>0);
    }

    public function testBusStops() {
    	$bus = new TransportService(TransportServiceType::TRANSPORT_BUS);
    	$stops = $bus->getStations(array("route" => "151"));
    	//print_r($stops);
    }

    public function testAllDestinations() {      	
    	//$bus = new DublinBus();
    	$bus = new TransportService(TransportServiceType::TRANSPORT_BUS);
    	$stops = $bus->getStations();
    	print_r($stops);
    }

}


?>