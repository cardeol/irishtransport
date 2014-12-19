<?php

define("ROOTDIR",dirname(dirname(__FILE__)));

include(ROOTDIR."/src/class.transport.php");


class transportTest extends PHPUnit_Framework_TestCase
{

 	public function testValidReturnData()
    {

      	$luas = new LuasApi();		
		$forecast = $luas->getForecast("bus", array("format" => "json"));
		$j = json_decode($forecast);
		$this->assertTrue(is_array($j),"can be converted in array");
    }

    public function testTranStations() {
    	$train = new TransportService(TransportServiceType::TRANSPORT_TRAIN);
    	$stations = $train->getStations();
    	print_r($stations);
    	$this->assertTrue(true);

    }

}


?>