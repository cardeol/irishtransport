<?php

define("ROOTDIR",dirname(dirname(__FILE__)));

include(ROOTDIR.'/src/class.cache.php');
include(ROOTDIR.'/src/class.transport.php');
include(ROOTDIR.'/src/class.irishrail.php');
include(ROOTDIR.'/src/class.dublinbus.php');
include(ROOTDIR.'/src/class.luas.php');


class transportTest extends PHPUnit_Framework_TestCase
{

 	public function testCache() {
        $this->assertTrue(defined("APPCACHE_DEFAULT_DIR"),'APPCACHE env not defined');
        $this->assertTrue(is_dir(APPCACHE_DEFAULT_DIR),'APPCACHE dir does not exist');
    }

    public function testIrishRail() {
        $train = new TransportService(TransportServiceType::TRANSPORT_IRISHRAIL);
        $this->assertTrue(is_object($train),"Irish rail class not working");        
    }

/*
    public function testTrainStations() {
    	$train = new TransportService(TransportServiceType::TRANSPORT_IRISHRAIL);
    	//$stations = $train->getStations(null);
        print_r($stations);
    	//$this->assertTrue(is_array($stations));
    	//$this->assertTrue(count($stations)>0);
    }


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