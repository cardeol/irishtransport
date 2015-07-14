<?php


class DublinLuas implements TransportInterface {
	
	const SERVICE_URL = 'http://luasforecasts.rpa.ie/xml/get.ashx?encrypt=false&action=forecast&stop={stationcode}';
	private $stations;

	function __construct() {
		$this->stations = json_decode(self::STATION_DATA,true);
	}

	public function getStations($filter) {
		return TransportHelper::filter_data($this->stations,$filter);
	}		

    public function getStationInfo($stationcode, $filter) {
    	$url = str_replace("{stationcode}",$stationcode,self::SERVICE_URL);
    	$req = TransportHelper::getUrl($url); 	
    	$xml = simplexml_load_string($req);
    	if($xml) {
    		$message = (string) $xml->message;
	    	$forecast = array();
	    	$now = time();	    	
	    	foreach ($xml->direction as $journey) {
	    		$jattrib = $journey->attributes();
	    		$direction = ((string) $jattrib['name'])=="Inbound"?"in":"out";    		
	    		foreach($journey->tram as $xmltram) {
	    			$tram = array();
	    			$attrTram = $xmltram->attributes();
	    			$due = (int) $attrTram['dueMins'];
	    			$tram = array(
	    				"dir" => $direction,
	    				"due" => $due,
	    				"des" => (string) $attrTram['destination'],
	    				"eta" => date("h:i",$now + ($due*60))
	    			);    			
	    			$forecast[] = $tram;
	    		}
	    	}    	
	    	return TransportHelper::ResponseSuccess($message,TransportHelper::filter_data($forecast,$filter));
	    } else {
	    	return TransportHelper::ResponseError("Not info available");
	    }
    }	
}

?>