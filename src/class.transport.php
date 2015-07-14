<?php

class TransportServiceType {
	const TRANSPORT_LUAS = 1;
	const TRANSPORT_DUBLINBUS = 2;
	const TRANSPORT_IRISHRAIL = 3;		
}

interface TransportInterface {
	public function getStations($filter);	
	public function getStationInfo($stationcode, $filter);	
}

class TransportHelper {
	public static function getUrl($url) {
 		$ch = curl_init();	
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, 'Mozilla/5.0 (Windows; U; Windows NT 6.1; en-US; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5');

	    $data = curl_exec($ch);	   
	    curl_close($ch);	
	    return $data;
	}
	public static function filter_data($arr, $filter) {
		$filtered = array();
		if($filter == NULL) return $arr;
		$hasfilter = false;			
		foreach($arr as $index => $info) {
			if(is_array($filter)) foreach($info as $k => $v) {				
				if(isset($filter[$k])) $hasfilter = true;
				if(isset($filter[$k]) && strtolower($v) == strtolower($filter[$k])) {
					$filtered[$index] = $info;
					break;
				} 
			} 
		}
		if(!$hasfilter) return $arr;
		return $filtered;	
	}
	public static function ResponseSuccess($message,$data) {
		return json_encode(array("success" => 1, "data" => $data, "message" => $message ));
	}
	public static function ResponseError($message) {
		return json_encode(array("success" => 0, "message" => $message ));
	}

	
	public static function XMLtoArray($xml) {
		$array = json_decode(json_encode($xml), TRUE);		   
		foreach ( array_slice($array, 0) as $key => $value ) {
			if ( empty($value) ) $array[$key] = NULL;
			elseif ( is_array($value) ) $array[$key] = self::XMLtoArray($value);
		}
		return $array;
	}

	public static function xmle($xmlo) {
		return trim((string) $xmlo);
	}
}

class TransportService {	

	private $selectedservice = null;

	function __construct($transporttype) {
		switch($transporttype) {
			case TransportServiceType::TRANSPORT_IRISHRAIL:
				$this->selectedservice = new IrishRail();
				break;
			case TransportServiceType::TRANSPORT_LUAS:
				$this->selectedservice = new DublinLuas();
				break;
			case TransportServiceType::TRANSPORT_DUBLINBUS:					
				$this->selectedservice = new DublinBus();
				break;
		}
	}
	function getStations($filter = null) {
		return $this->selectedservice->getStations($filter);
	}
	function getStationInfo($stationcode, $filter = null) {
		return $this->selectedservice->getStationInfo($stationcode,$filter);
	}
};



?>