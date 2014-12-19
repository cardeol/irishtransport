<?php

define("ROOTDIR",dirname(dirname(__FILE__)));

include(ROOTDIR."/src/class.luas.php");


class XtTest extends PHPUnit_Framework_TestCase
{

 	public function testValidReturnData()
    {

      	$luas = new LuasApi();
		
		$forecast = $luas->getForecast("bus", array("format" => "json", "return" => "true"));
		$j = json_decode($forecast);
		$this->assertTrue(is_array($j));
		print_r($j);
    }
/*
    public function testPushAndPop()
    {
        $stack = array();
        $this->assertEquals(0, count($stack));

        array_push($stack, 'foo');
        $this->assertEquals('foo', $stack[count($stack)-1]);
        $this->assertEquals(1, count($stack));

        $this->assertEquals('foo', array_pop($stack));
        $this->assertEquals(0, count($stack));
    }
    */
}
?>

?>