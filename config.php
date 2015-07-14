<?php


define(DEBUG_APP,true);

if(DEBUG_APP) {
	error_reporting(E_ALL);
	ini_set('display_errors',1);
	ini_set('display_startup_errors',1);
	error_reporting(1);	
}


$timezone = 'Europe/Dublin';
ini_set('date.timezone', $timezone);
date_default_timezone_set ( $timezone );


define("ROOTAPP",dirname(__FILE__));

require(ROOTAPP.'/vendor/autoload.php');
include(ROOTAPP.'/src/class.cache.php');
include(ROOTAPP.'/src/class.transport.php');
include(ROOTAPP.'/src/class.irishrail.php');
include(ROOTAPP.'/src/class.dublinbus.php');
include(ROOTAPP.'/src/class.luas.php');

?>