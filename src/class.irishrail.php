<?php

class IrishRail implements TransportInterface {
	
	private $stations;
	
	function __construct() {			
	
	}

	function getETA($item) {
		$fields = array ( 'Exparrival','Scharrival', 'Expdepart', 'Schdepart');
		foreach($fields as $f) {
			$t = trim((string) $item->{$f});
			if($t!="" && $t!="00:00") return $t;
		}
		return "";	
	}

	/*public function getStations($filter) {
		return TransportHelper::filter_data($this->stations,$filter);
	}*/

	public function getStations($filter = null) {
		$cache = new AppCache();
		$cache->setKey("getALLTrainStations");
		$cache->setTime(200000);	
		$content = $cache->getCache();
		if($content !== false) return $content;
		$url = "http://api.irishrail.ie/realtime/realtime.asmx/getAllStationsXML";
		$html = TransportHelper::getUrl($url); 
		$xml = simplexml_load_string($html);	
		$arr = 	TransportHelper::XMLtoArray($xml);
		$obj = $arr['objStation'];
		$ret = array();
		$added = array();
		for($i=0; $i<count($obj); $i++) {
			$item = $obj[$i];
			if(strlen(trim($item['StationAlias']))>0) $item['StationDesc']=$item['StationAlias'];
			$station = $item['StationDesc'];
			$code = strtolower(trim($item['StationCode']));
			if(!in_array($station,$added)) {
				$ret[$code] = array (
					"nam" => $item['StationDesc'],
					"lat" => $item['StationLatitude'],
					"lon" => $item['StationLongitude']
					);
				$added[] = $station;
			};
		}
		uksort($ret,function ($a,$b) { 
			return strtolower($a)>strtolower($b); 
		});
		if(count($ret)>0) {
			$output = TransportHelper::ResponseSuccess("ok",TransportHelper::filter_data($ret,$filter));
			$cache->saveOutput($output);
			return $output;
		}
		return TransportHelper::ResponseError("Not info available");
	}

	public function getStationInfo($stationcode,$filter) {
		$service_url = "http://api.irishrail.ie/realtime/realtime.asmx/getStationDataByCodeXML?StationCode={stationcode}";
		$url = str_replace("{stationcode}",$stationcode,$service_url);
    	$req = TransportHelper::getUrl($url); 	
    	$xml = simplexml_load_string($req);
    	$arr = $xml->objStationData;
    	$ret = array();
		$added = array();		
		if($arr) foreach($arr as $item) {
			 $eta = $this->getETA($item);
			 $due = (int) $item->Duein;
			 if(strlen(trim($item->Origin))==1) break;
			 if(trim($item->Exparrival)=="00:00") $item->Exparrival="";
			 if($due<180) {
				 $ret[] = array(
					"tra" => trim($item->Traincode),
					"ori" => (string) $item->Origin,
					'des' => (string) $item->Destination,
					'sta' => (string) $item->Status,
					'las' => (string) $item->Lastlocation,
					'due' => (string) $item->Duein,
					'eta' => (string) $eta,
					'dir' => (string) $item->Direction=="Northbound"?"in":"out"
				  );
			 }
		}
		if(count($ret)>0) {
			return TransportHelper::ResponseSuccess("ok",TransportHelper::filter_data($ret,$filter));
		} else {
			return TransportHelper::ResponseError("Not info available");
		}			
	}
}

?>