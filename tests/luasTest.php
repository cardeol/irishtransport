<?php

define("ROOTDIR",dirname(dirname(__FILE__)));

include(ROOTDIR."/src/class.luas.php");


class XtTest extends PHPUnit_Framework_TestCase
{

 	public function testValidReturnData()
    {

      	$luas = new LuasApi();		
		$forecast = $luas->getForecast("bus", array("format" => "json");
		$j = json_decode($forecast);
		$this->assertTrue(is_array($j));		
    }

}
?>

?>