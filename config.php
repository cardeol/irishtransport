<?php



error_reporting(E_ALL);
ini_set('display_errors',1);
ini_set('display_startup_errors',1);
error_reporting(-1);


define("ROOTAPP",dirname(__FILE__));

require(ROOTAPP.'/vendor/autoload.php');
include(ROOTAPP.'/src/class.transport.php');
include(ROOTAPP.'/src/class.cache.php');

?>