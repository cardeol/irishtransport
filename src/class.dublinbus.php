<?php


class DublinBus implements TransportInterface {
	
	private $client;
	private $routes;
	const SERVICE_URL = "http://rtpi.dublinbus.biznetservers.com/DublinBusRTPIService.asmx";

	function __construct() {
		$this->routes = $this->getAllRoutes();
	}

	private function getDublinBusXML($xml_post_string) {
		$url = self::SERVICE_URL;
		$headers = array(
                    "Content-type: text/xml;charset=\"utf-8\"",
                    "Accept: text/xml",
                    "Cache-Control: no-cache",
                    "Pragma: no-cache",
                    "Content-length: ".strlen($xml_post_string)
                    );
		
        
        // PHP cURL  for https connection with auth
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        //curl_setopt($ch, CURLOPT_USERPWD, $soapUser.":".$soapPassword); // username and password - declared at the top of the doc
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_ANY);
        curl_setopt($ch, CURLOPT_TIMEOUT, 10);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml_post_string); // the SOAP request
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);

        // converting
        $result = curl_exec($ch); 
        curl_close($ch);
        // converting
        $response1 = str_replace("<soap:Body>","",$result);
        $response2 = str_replace("</soap:Body>","",$response1);
        return $response2;
	}

	public function getStations($filter = null) {
		$cache = new AppCache();
		$cache->setKey("getAllBusStops.".json_encode($filter));
		$cache->setTime(2000000);	
		$content = $cache->getCache();
		if($content !== false) return $content;
		$xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:dub="http://dublinbus.ie/">
			   <soapenv:Header/>
			   <soapenv:Body>
			      <dub:GetAllDestinations/>
			   </soapenv:Body>
			</soapenv:Envelope>';
		$resp = $this->getDublinBusXML($xml_post_string);
		$xml = new SimpleXMLElement($resp);
		$arr = TransportHelper::XMLtoArray($xml);
		$ret = array();
		if(isset($arr['GetAllDestinationsResponse'])) {
			$destinations = $arr['GetAllDestinationsResponse']['GetAllDestinationsResult']['Destinations']['Destination'];
			foreach($destinations as $des) {
				$code = trim(strtolower($des['StopNumber']));				
				$item = array();
				$item['nam'] = $des['Description'];
				$item['lat'] = $des['Latitude'];
				$item['lon'] = $des['Longitude'];
				$ret[$code] = $item;
			}
		}		
		if(count($ret)>0) {
			$ret = TransportHelper::ResponseSuccess("ok",TransportHelper::filter_data($ret,$filter));
			$cache->saveOutput($ret);
			return $ret;
		}
		return TransportHelper::ResponseError("Not info available");
	}

	public function getAllRoutes() {
		$cache = new AppCache();
		$cache->setKey("getAllBusRoutes*");
		$cache->setTime(2000000);	
		$content = $cache->getCache();
		if($content !== false) return $content;
		$xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:dub="http://dublinbus.ie/">
			   <soapenv:Header/><soapenv:Body><dub:GetRoutes><dub:filter></dub:filter></dub:GetRoutes></soapenv:Body></soapenv:Envelope>';
		$resp = $this->getDublinBusXML($xml_post_string);
		$xml = new SimpleXMLElement($resp);
		$arr = TransportHelper::XMLtoArray($xml);
		$ret = array();
		if(isset($arr['GetRoutesResponse'])) {
			$routes = $arr['GetRoutesResponse']['GetRoutesResult']['Routes']['Route'];
			foreach($routes as $route) {
				$item = array();
				$code = trim(strtolower($route['Number']));
				if(!isset($route['From'])) continue;
				if(!isset($route['Towards'])) continue;
				$item["ori"] = $route['From'];
				$item["des"] = $route['Towards'];
				$ret[$code] = $item;
			}
			if(count($ret)>0) {
				$dt = TransportHelper::ResponseSuccess("ok",TransportHelper::filter_data($ret,$filter));
				$cache->saveOutput($dt);
				return $dt;
			}			
		}		
		return TransportHelper::ResponseError("Not info available");
	}

	public function getRoutesByStopId($stopid) {
		$cache = new AppCache();
		$cache->setKey("getRoutesByStopId_".$stopid);
		$cache->setTime(100000);
		if($content !== false) return $content;
		$xml_post_string = '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:dub="http://dublinbus.ie/">
		   <soapenv:Header/>
		   <soapenv:Body>
		      <dub:GetRoutesServicedByStopNumber>
		         <dub:stopId>'.$stopid.'</dub:stopId>
		      </dub:GetRoutesServicedByStopNumber>
		   </soapenv:Body>
		</soapenv:Envelope>';
		$resp = $this->getDublinBusXML($xml_post_string);
		$xml = new SimpleXMLElement($resp);
		$arr = TransportHelper::XMLtoArray($xml);
		$ret = array();
		if(isset($arr['GetRoutesServicedByStopNumberResponse'])) {
			$routes = $arr['GetRoutesServicedByStopNumberResponse']['GetRoutesServicedByStopNumberResult']['Route'];
			foreach($routes as $route) {
				$ret[] = $route['Number'];
			}
			if(count($ret)>0) {
				$dt = TransportHelper::ResponseSuccess("ok",TransportHelper::filter_data($ret,$filter));
				$cache->saveOutput($dt);
				return $dt;
			}		
		}
		return TransportHelper::ResponseError("Not info available");

	}	

	public function getStationInfo($stationcode,$filter = null) { // 3237
		// xml post structure
		$cache = new AppCache();
		$cache->setKey("getStationInfo.".$stationcode.".".json_encode($filter));
		$cache->setTime(15);
		if($content !== false) return $content;
        $xml_post_string = 
       '<soapenv:Envelope xmlns:soapenv="http://schemas.xmlsoap.org/soap/envelope/" xmlns:dub="http://dublinbus.ie/">
		   <soapenv:Header/>
		   <soapenv:Body>
		      <dub:GetRealTimeStopData>
		         <dub:stopId>'.$stationcode.'</dub:stopId>
		         <dub:forceRefresh>1</dub:forceRefresh>
		      </dub:GetRealTimeStopData>
		   </soapenv:Body>
		</soapenv:Envelope>';  

		$resp = $this->getDublinBusXML($xml_post_string);       

        $xml = new SimpleXMLElement($resp);
        $xml = $xml->GetRealTimeStopDataResponse->GetRealTimeStopDataResult->asXML();
        $xml = str_replace("diffgr:diffgram","diffgr",$xml);        
        $xml = new SimpleXMLElement($xml);
        $arr = TransportHelper::XMLtoArray($xml);
        $ret = array();        

        if(isset($arr['diffgr'])) {        	
        	foreach($arr['diffgr']['DocumentElement']['StopData'] as $st) {
				$r = array();
				$status = $st['MonitoredVehicleJourney_InCongestion'];
				$route = strtolower($st['MonitoredVehicleJourney_LineRef']);	        	 
				$atime = $st['MonitoredCall_ExpectedArrivalTime'];
				$eta = strtotime($atime);
				$current_time = time();
				$r["tra"] = strtoupper($route);
				$r["sta"] = $status == "false" ? "Normal" : "In congestion";
				$r['ori'] = isset($this->routes[$route]) ? $this->routes[$route]['ori'] : "";
				$r['des'] = isset($this->routes[$route]) ? $this->routes[$route]['des'] : "";
				$r["eta"] = date("H:i",$eta);
				$direction = $st['MonitoredVehicleJourney_DirectionRef']; // inbound
				$r["dir"] = $st['MonitoredVehicleJourney_DestinationName'];
				$due = round(abs($eta - $current_time) / 60,0);
				$toshow = $due."m";
				if($due>60) $toshow = floor($due/60).":".floor($due%60);
				$r["due"] = $toshow;
				$ret[] = $r;
	        };
        }        
        if(count($ret)>0) {
			$dt = TransportHelper::ResponseSuccess("ok",TransportHelper::filter_data($ret,$filter));
			$cache->saveOutput($dt);
			return $dt;
		}		
        return TransportHelper::ResponseError("Not info available");
	}

}

?>