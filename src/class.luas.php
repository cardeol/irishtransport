<?php
	
    
class LuasApi {

	private $stations;
	private $data_url;
	
	function __construct() {		
		date_default_timezone_set ( "Europe/Dublin" );		
		$this->data_url = 'http://luasforecasts.rpa.ie/xml/get.ashx?encrypt=false&action=forecast&stop={stationcode}';
		$stations = <<<EEE
{"sts":{"name":"St. Stephen's Green","line":"G","lat":53.339072,"lon":-6.261333},"har":{"name":"Harcourt Street","line":"G","lat":53.333358,"lon":-6.26265},"cha":{"name":"Charlemont","line":"G","lat":53.330669,"lon":-6.258683},"ran":{"name":"Ranelagh","line":"G","lat":53.326433,"lon":-6.256203},"bee":{"name":"Beechwood","line":"G","lat":53.320822,"lon":-6.254653},"cow":{"name":"Cowper","line":"G","lat":53.316467,"lon":-6.253447},"mil":{"name":"Milltown","line":"G","lat":53.309917,"lon":-6.251728},"win":{"name":"Windy Arbour","line":"G","lat":53.301558,"lon":-6.250708},"dun":{"name":"Dundrum","line":"G","lat":53.292358,"lon":-6.245117},"bal":{"name":"Balally","line":"G","lat":53.286106,"lon":-6.236772},"kil":{"name":"Kilmacud","line":"G","lat":53.283008,"lon":-6.223886},"sti":{"name":"Stillorgan","line":"G","lat":53.279311,"lon":-6.209919},"san":{"name":"Sandyford","line":"G","lat":53.279311,"lon":-6.204678},"cpk":{"name":"Central Park","line":"G","lat":53.27015,"lon":-6.203764},"gle":{"name":"Glencairn","line":"G","lat":53.266336,"lon":-6.209942},"gal":{"name":"The Gallops","line":"G","lat":53.261164,"lon":-6.206022},"leo":{"name":"Leopardstown Valley","line":"G","lat":53.257996,"lon":-6.197485},"baw":{"name":"Ballyogan Wood","line":"G","lat":53.255047,"lon":-6.184475},"cck":{"name":"Carrickmines","line":"G","lat":53.254033,"lon":-6.169908},"lau":{"name":"Laughanstown","line":"G","lat":53.250606,"lon":-6.155006},"che":{"name":"Cherrywood","line":"G","lat":53.245333,"lon":-6.145853},"bri":{"name":"Brides Glen","line":"G","lat":53.242075,"lon":-6.142886},"tpt":{"name":"The Point","line":"R","lat":53.34835,"lon":-6.229258},"sdk":{"name":"Spencer Dock","line":"R","lat":53.348822,"lon":-6.237147},"mys":{"name":"Mayor Square (NCI)","line":"R","lat":53.349247,"lon":-6.243394},"gdk":{"name":"George's Dock","line":"R","lat":53.349528,"lon":-6.247575},"con":{"name":"Connolly","line":"R","lat":53.350922,"lon":-6.249942},"bus":{"name":"Busaras","line":"R","lat":53.348589,"lon":-6.258172},"abb":{"name":"Abbey Street","line":"R","lat":53.348589,"lon":-6.258172},"jer":{"name":"Jervis","line":"R","lat":53.347686,"lon":-6.265333},"fou":{"name":"The Four Courts","line":"R","lat":53.346864,"lon":-6.273436},"smi":{"name":"Smithfield","line":"R","lat":53.347133,"lon":-6.277728},"mus":{"name":"Museum","line":"R","lat":53.347867,"lon":-6.286714},"heu":{"name":"Heuston","line":"R","lat":53.346647,"lon":-6.291808},"jam":{"name":"James'","line":"R","lat":53.341942,"lon":-6.293361},"fat":{"name":"Fatima","line":"R","lat":53.338439,"lon":-6.292547},"ria":{"name":"Rialto","line":"R","lat":53.337908,"lon":-6.297242},"sui":{"name":"Suir Road","line":"R","lat":53.336617,"lon":-6.307211},"gol":{"name":"Goldenbridge","line":"R","lat":53.335892,"lon":-6.313569},"dri":{"name":"Drimnagh","line":"R","lat":53.335361,"lon":-6.318161},"bla":{"name":"Blackhorse","line":"R","lat":53.334258,"lon":-6.327394},"blu":{"name":"Bluebell","line":"R","lat":53.329297,"lon":-6.33396},"kyl":{"name":"Kylemore","line":"R","lat":53.326656,"lon":-6.343444},"red":{"name":"Red Cow","line":"R","lat":53.316833,"lon":-6.369872},"kin":{"name":"Kingswood","line":"R","lat":53.303694,"lon":-6.36525},"bel":{"name":"Belgard","line":"R","lat":53.299286,"lon":-6.374886},"coo":{"name":"Cookstown","line":"R","lat":53.293506,"lon":-6.384397},"hos":{"name":"Hospital","line":"R","lat":53.289369,"lon":-6.37885},"tal":{"name":"Tallaght","line":"R","lat":53.287494,"lon":-6.374589},"fet":{"name":"Fettercairn","line":"R","lat":53.293519,"lon":-6.395554},"cvn":{"name":"Cheeverstown","line":"R","lat":53.290982,"lon":-6.406849},"cit":{"name":"Citywest Campus","line":"R","lat":53.287833,"lon":-6.418915},"for":{"name":"Fortunestown","line":"R","lat":53.284251,"lon":-6.424602},"sag":{"name":"Saggart","line":"R","lat":53.284679,"lon":-6.43776255}}
EEE;
	   $this->stations = json_decode($stations,true);
	}

	

	private function filter_data($data, $key, $value) {
		$fcast = array();
		if($key=="dest" && strlen($value) == 3) {
			if(isset($this->stations[strtolower($value)])) {
				$st = $this->stations[strtolower($value)];
				$value = $st['name'];
			}
		}
		foreach($data as $i => $journey) {
			if(isset($journey[$key]) && strtolower($journey[$key])!=strtolower($value)) continue;
			$fcast[] = $journey;
		}
		return $fcast;
	}

	private function OuputData($data, $params = null) {
		$params = ($params==null)?array():$params;
        $format = isset($params['format'])?$params['format']:"json";
        $return = isset($params['return'])?$params['return']:true;
        if(isset($params['format'])) unset($params['format']);
        foreach($params as $k => $v) $data = $this->filter_data($data,$k,$v);

        $type = "application/json";
        switch($format) {
                case "xml":
                    if(!$return) $type = "text/xml";        
                    $isstation = isset($data['con']);                            
                    $child = $isstation ? "station"  : "tram";
                    $xml = new SimpleXMLElement("<${child}s />");	
                	foreach($data as $abv => $stationinfo) {
                		$station = $xml->addChild($child);
                		if($isstation) $station->addAttribute("code",$abv);
                		foreach($stationinfo as $k => $v) {
                			$station->addAttribute($k,$v);	
                		}
                	}
                    $data = $xml->asXML();
                    break;
                case "jsonp":
                    $callback = isset($_GET['callback'])?$_GET['callback']:"";
                    $data = $callback."(".json_encode($data).")";                            
                    break;
                case "json":
                    $data = json_encode($data);   
                    break;                     
                case "array":
                default:
                	$return = true;
                	   
        }
        if(!$return && false) {
	        header("Content-type: ".$type);
	        echo($data);
	        return true;
        }
        return $data;            
    }

	private function get_url($url) {
	    $ch = curl_init();	
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		//curl_setopt($ch, CURLOPT_PROXYAUTH, CURLAUTH_NTLM);
		//curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
		//curl_setopt($ch, CURLOPT_PROXYPORT, 8080);
		
	    $data = curl_exec($ch);	   
	    curl_close($ch);	
	    return $data;
    }

	public function getStations($params = null) {
		return $this->OuputData($this->stations,$params);		
	}

    public function getForecast($stationcode, $params = null) {
    	$url = str_replace("{stationcode}",$stationcode,$this->data_url);
    	$req = $this->get_url($url);    
        $xml = simplexml_load_string($req);
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
    				"dest" => (string) $attrTram['destination'],
    				"eta" => date("h:i",$now + ($due*60))
    			);    			
    			$forecast[] = $tram;
    		}
    	}    	
    	return $this->OuputData($forecast,$params);
    }

    public function getStationInfo($stationcode,$params = null) {
    	$data = array();
    	$code = strtolower($stationcode);
    	if(isset($this->stations[$code])) $data = $this->stations[$code];
    	return $this->OuputData($data,$params);
    }
}

