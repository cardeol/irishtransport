<?php

	include(dirname(__FILE__)."/nusoap.php");

	class TransportServiceType {
		const TRANSPORT_LUAS = 1;
		const TRANSPORT_BUS = 2;
		const TRANSPORT_TRAIN = 3;		
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
			return array("success" => 1, "data" => $data, "message" => $message );
		}
		public static function ResponseError($message) {
			return array("success" => 0, "message" => $message );
		}
	}

	class TransportService {	

		private $selectedservice = null;

		function __construct($transporttype) {
			switch($transporttype) {
				case TransportServiceType::TRANSPORT_TRAIN:
					$this->selectedservice = new IrishRail();
					break;
				case TransportServiceType::TRANSPORT_LUAS:
					$this->selectedservice = new DublinLuas();
					break;
				case TransportServiceType::TRANSPORT_BUS:					
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


	interface TransportInterface {
		public function getStations($filter);	
		public function getStationInfo($stationcode, $filter);	
	}

	
	class DublinLuas implements TransportInterface {
		const STATION_DATA = <<<EEE
{"sts":{"nam":"St. Stephen's Green","lin":"G","lat":53.339072,"lon":-6.261333},"har":{"nam":"Harcourt Street","lin":"G","lat":53.333358,"lon":-6.26265},"cha":{"nam":"Charlemont","lin":"G","lat":53.330669,"lon":-6.258683},"ran":{"nam":"Ranelagh","lin":"G","lat":53.326433,"lon":-6.256203},"bee":{"nam":"Beechwood","lin":"G","lat":53.320822,"lon":-6.254653},"cow":{"nam":"Cowper","lin":"G","lat":53.316467,"lon":-6.253447},"mil":{"nam":"Milltown","lin":"G","lat":53.309917,"lon":-6.251728},"win":{"nam":"Windy Arbour","lin":"G","lat":53.301558,"lon":-6.250708},"dun":{"nam":"Dundrum","lin":"G","lat":53.292358,"lon":-6.245117},"bal":{"nam":"Balally","lin":"G","lat":53.286106,"lon":-6.236772},"kil":{"nam":"Kilmacud","lin":"G","lat":53.283008,"lon":-6.223886},"sti":{"nam":"Stillorgan","lin":"G","lat":53.279311,"lon":-6.209919},"san":{"nam":"Sandyford","lin":"G","lat":53.279311,"lon":-6.204678},"cpk":{"nam":"Central Park","lin":"G","lat":53.27015,"lon":-6.203764},"gle":{"nam":"Glencairn","lin":"G","lat":53.266336,"lon":-6.209942},"gal":{"nam":"The Gallops","lin":"G","lat":53.261164,"lon":-6.206022},"leo":{"nam":"Leopardstown Valley","lin":"G","lat":53.257996,"lon":-6.197485},"baw":{"nam":"Ballyogan Wood","lin":"G","lat":53.255047,"lon":-6.184475},"cck":{"nam":"Carrickmines","lin":"G","lat":53.254033,"lon":-6.169908},"lau":{"nam":"Laughanstown","lin":"G","lat":53.250606,"lon":-6.155006},"che":{"nam":"Cherrywood","lin":"G","lat":53.245333,"lon":-6.145853},"bri":{"nam":"Brides Glen","lin":"G","lat":53.242075,"lon":-6.142886},"tpt":{"nam":"The Point","lin":"R","lat":53.34835,"lon":-6.229258},"sdk":{"nam":"Spencer Dock","lin":"R","lat":53.348822,"lon":-6.237147},"mys":{"nam":"Mayor Square (NCI)","lin":"R","lat":53.349247,"lon":-6.243394},"gdk":{"nam":"George's Dock","lin":"R","lat":53.349528,"lon":-6.247575},"con":{"nam":"Connolly","lin":"R","lat":53.350922,"lon":-6.249942},"bus":{"nam":"Busaras","lin":"R","lat":53.348589,"lon":-6.258172},"abb":{"nam":"Abbey Street","lin":"R","lat":53.348589,"lon":-6.258172},"jer":{"nam":"Jervis","lin":"R","lat":53.347686,"lon":-6.265333},"fou":{"nam":"The Four Courts","lin":"R","lat":53.346864,"lon":-6.273436},"smi":{"nam":"Smithfield","lin":"R","lat":53.347133,"lon":-6.277728},"mus":{"nam":"Museum","lin":"R","lat":53.347867,"lon":-6.286714},"heu":{"nam":"Heuston","lin":"R","lat":53.346647,"lon":-6.291808},"jam":{"nam":"James'","lin":"R","lat":53.341942,"lon":-6.293361},"fat":{"nam":"Fatima","lin":"R","lat":53.338439,"lon":-6.292547},"ria":{"nam":"Rialto","lin":"R","lat":53.337908,"lon":-6.297242},"sui":{"nam":"Suir Road","lin":"R","lat":53.336617,"lon":-6.307211},"gol":{"nam":"Goldenbridge","lin":"R","lat":53.335892,"lon":-6.313569},"dri":{"nam":"Drimnagh","lin":"R","lat":53.335361,"lon":-6.318161},"bla":{"nam":"Blackhorse","lin":"R","lat":53.334258,"lon":-6.327394},"blu":{"nam":"Bluebell","lin":"R","lat":53.329297,"lon":-6.33396},"kyl":{"nam":"Kylemore","lin":"R","lat":53.326656,"lon":-6.343444},"red":{"nam":"Red Cow","lin":"R","lat":53.316833,"lon":-6.369872},"kin":{"nam":"Kingswood","lin":"R","lat":53.303694,"lon":-6.36525},"bel":{"nam":"Belgard","lin":"R","lat":53.299286,"lon":-6.374886},"coo":{"nam":"Cookstown","lin":"R","lat":53.293506,"lon":-6.384397},"hos":{"nam":"Hospital","lin":"R","lat":53.289369,"lon":-6.37885},"tal":{"nam":"Tallaght","lin":"R","lat":53.287494,"lon":-6.374589},"fet":{"nam":"Fettercairn","lin":"R","lat":53.293519,"lon":-6.395554},"cvn":{"nam":"Cheeverstown","lin":"R","lat":53.290982,"lon":-6.406849},"cit":{"nam":"Citywest Campus","lin":"R","lat":53.287833,"lon":-6.418915},"for":{"nam":"Fortunestown","lin":"R","lat":53.284251,"lon":-6.424602},"sag":{"nam":"Saggart","lin":"R","lat":53.284679,"lon":-6.43776255}}
EEE;
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

class IrishRail implements TransportInterface {
		const STATION_DATA = <<<FFF
{"admtn":{"nam":"Adamstown","lat":"53.3353","lon":"-6.45233"},"arhan":{"nam":"Ardrahan","lat":"53.1572","lon":"-8.81483"},"arklw":{"nam":"Arklow","lat":"52.7932","lon":"-6.15994"},"ashtn":{"nam":"Ashtown","lat":"53.3755","lon":"-6.33135"},"athry":{"nam":"Athenry","lat":"53.3015","lon":"-8.74855"},"athy ":{"nam":"Athy","lat":"52.992","lon":"-6.9762"},"atlne":{"nam":"Athlone","lat":"53.4273","lon":"-7.93683"},"atmon":{"nam":"Attymon","lat":"53.3212","lon":"-8.60608"},"balna":{"nam":"Ballina","lat":"54.1085","lon":"-9.16146"},"bbrdg":{"nam":"Broombridge","lat":"53.3725","lon":"-6.29869"},"bbrgn":{"nam":"Balbriggan","lat":"53.6118","lon":"-6.18226"},"bbrhy":{"nam":"Ballybrophy","lat":"52.8999","lon":"-7.60259"},"bclan":{"nam":"Ballycullane","lat":"52.2834","lon":"-6.83958"},"bfstc":{"nam":"Belfast Central","lat":"54.6123","lon":"-5.91744"},"bhill":{"nam":"Birdhill","lat":"52.7656","lon":"-8.44247"},"bmote":{"nam":"Ballymote","lat":"54.0887","lon":"-8.52088"},"boyle":{"nam":"Boyle","lat":"53.9676","lon":"-8.30438"},"bray ":{"nam":"Bray","lat":"53.2043","lon":"-6.10046"},"brgtn":{"nam":"Bridgetown","lat":"52.2312","lon":"-6.54918"},"brock":{"nam":"Blackrock","lat":"53.3027","lon":"-6.17833"},"bsloe":{"nam":"Ballinasloe","lat":"53.3363","lon":"-8.24081"},"bteer":{"nam":"Banteer","lat":"52.1287","lon":"-8.89793"},"btstn":{"nam":"Booterstown","lat":"53.3099","lon":"-6.19498"},"byhns":{"nam":"Ballyhaunis","lat":"53.7616","lon":"-8.7584"},"bysde":{"nam":"Bayside","lat":"53.3917","lon":"-6.13678"},"cahir":{"nam":"Cahir","lat":"52.3777","lon":"-7.92181"},"cconl":{"nam":"Castleconnell","lat":"52.7128","lon":"-8.49794"},"cgloe":{"nam":"Carrigaloe","lat":"51.8688","lon":"-8.32417"},"cgtwl":{"nam":"Carrigtwohill","lat":"51.9163","lon":"-8.26323"},"chorc":{"nam":"Park West (Cherry Orchard )","lat":"53.334","lon":"-6.37868"},"cjrdn":{"nam":"Cloughjordan","lat":"52.9363","lon":"-8.0246"},"ckosh":{"nam":"Carrick on Shannon","lat":"53.9383","lon":"-8.10657"},"ckosr":{"nam":"Carrick on Suir","lat":"52.3487","lon":"-7.40354"},"clara":{"nam":"Clara","lat":"53.3395","lon":"-7.61596"},"clbar":{"nam":"Castlebar","lat":"53.8471","lon":"-9.2873"},"clmel":{"nam":"Clonmel","lat":"52.3611","lon":"-7.69936"},"clmrs":{"nam":"Claremorris","lat":"53.7204","lon":"-9.00222"},"clonf":{"nam":"Fonthill ( Clondalkin )","lat":"53.3334","lon":"-6.40628"},"clsla":{"nam":"Clonsilla","lat":"53.3831","lon":"-6.4242"},"cmine":{"nam":"Coolmine","lat":"53.3776","lon":"-6.39072"},"cnlly":{"nam":"Connolly","lat":"53.3531","lon":"-6.24591"},"cnock":{"nam":"Castleknock","lat":"53.3816","lon":"-6.37149"},"cobh ":{"nam":"Cobh","lat":"51.8491","lon":"-8.29956"},"colny":{"nam":"Collooney","lat":"54.1871","lon":"-8.49453"},"cork ":{"nam":"Cork","lat":"51.9018","lon":"-8.4582"},"cpile":{"nam":"Campile","lat":"52.2855","lon":"-6.93896"},"crghw":{"nam":"Craughwell","lat":"53.2252","lon":"-8.7359"},"crlow":{"nam":"Carlow","lat":"52.8407","lon":"-6.92217"},"csrea":{"nam":"Castlerea","lat":"53.7612","lon":"-8.48448"},"ctarf":{"nam":"Clontarf Road","lat":"53.3629","lon":"-6.22753"},"curah":{"nam":"Curragh","lat":"53.1725","lon":"-6.86245"},"cvill":{"nam":"Charleville","lat":"52.3468","lon":"-8.65362"},"dbate":{"nam":"Donabate","lat":"53.4855","lon":"-6.15134"},"dbyne":{"nam":"Dunboyne","lat":"53.4175","lon":"-6.46483"},"dcdra":{"nam":"Drumcondra","lat":"53.3632","lon":"-6.25908"},"dckls":{"nam":"Docklands","lat":"53.3509","lon":"-6.23929"},"ddalk":{"nam":"Dundalk","lat":"54.0007","lon":"-6.41291"},"dghda":{"nam":"Drogheda","lat":"53.712","lon":"-6.33538"},"dlery":{"nam":"Dun Laoghaire","lat":"53.2951","lon":"-6.13498"},"dlkey":{"nam":"Dalkey","lat":"53.2756","lon":"-6.10333"},"drmod":{"nam":"Dromod","lat":"53.8591","lon":"-7.9164"},"ecrty":{"nam":"Enniscorthy","lat":"52.5046","lon":"-6.56627"},"enfld":{"nam":"Enfield","lat":"53.4157","lon":"-6.83395"},"ennis":{"nam":"Ennis","lat":"52.8386","lon":"-8.97491"},"etown":{"nam":"Edgeworthstown","lat":"53.6888","lon":"-7.60299"},"ffore":{"nam":"Farranfore","lat":"52.1733","lon":"-9.55278"},"fota ":{"nam":"Fota","lat":"51.896","lon":"-8.3183"},"fxfrd":{"nam":"Foxford","lat":"53.983","lon":"-9.1364"},"galwy":{"nam":"Galway","lat":"53.2736","lon":"-9.04696"},"gcdk ":{"nam":"Grand Canal Dock","lat":"53.3397","lon":"-6.23773"},"ghane":{"nam":"Glounthaune","lat":"51.9112","lon":"-8.3254"},"glgry":{"nam":"Glenageary","lat":"53.2812","lon":"-6.12289"},"gorey":{"nam":"Gorey","lat":"52.6712","lon":"-6.29195"},"gort":{"nam":"Gort","lat":"53.0653","lon":"-8.81595"},"grgrd":{"nam":"Clongriffin","lat":"53.4032","lon":"-6.14839"},"gstns":{"nam":"Greystones","lat":"53.1442","lon":"-6.06085"},"gston":{"nam":"Gormanston","lat":"53.638","lon":"-6.21705"},"hafld":{"nam":"Hansfield","lat":"53.3853","lon":"-6.44205"},"howth":{"nam":"Howth","lat":"53.3891","lon":"-6.07401"},"hston":{"nam":"Heuston","lat":"53.3464","lon":"-6.29461"},"htown":{"nam":"Harmonstown","lat":"53.3786","lon":"-6.19131"},"hwthj":{"nam":"Donaghmede ( Howth Junction )","lat":"53.3909","lon":"-6.15672"},"hzlch":{"nam":"Celbridge (Hazelhatch ) ","lat":"53.3223","lon":"-6.52356"},"kbrck":{"nam":"Kilbarrack","lat":"53.387","lon":"-6.16163"},"kcock":{"nam":"Kilcock","lat":"53.4043","lon":"-6.67892"},"kcool":{"nam":"Kilcoole","lat":"53.107","lon":"-6.04112"},"kdare":{"nam":"Kildare","lat":"53.163","lon":"-6.90802"},"kilny":{"nam":"Killiney","lat":"53.2557","lon":"-6.11317"},"kknny":{"nam":"Kilkenny","lat":"52.655","lon":"-7.24498"},"klrny":{"nam":"Killarney","lat":"52.0595","lon":"-9.50198"},"klstr":{"nam":"Killester","lat":"53.373","lon":"-6.20442"},"lburn":{"nam":"Lisburn","lat":"54.514","lon":"-6.04327"},"ldwne":{"nam":"Lansdowne Road","lat":"53.3347","lon":"-6.22979"},"lford":{"nam":"Longford","lat":"53.7243","lon":"-7.79574"},"lmrck":{"nam":"Limerick","lat":"52.6587","lon":"-8.62397"},"lmrkj":{"nam":"Limerick Junction","lat":"52.5009","lon":"-8.20003"},"lslnd":{"nam":"Little Island","lat":"51.9078","lon":"-8.35466"},"ltown":{"nam":"Laytown","lat":"53.6794","lon":"-6.24253"},"lurgn":{"nam":"Lurgan","lat":"54.4672","lon":"-6.33547"},"lxcon":{"nam":"Leixlip (Confey)","lat":"53.3743","lon":"-6.48624"},"lxlsa":{"nam":"Leixlip (Louisa Bridge)","lat":"53.3704","lon":"-6.50598"},"m3way":{"nam":"M3 Parkway","lat":"53.4349","lon":"-6.46898"},"mdltn":{"nam":"Midleton","lat":"51.9212","lon":"-8.17579"},"mhide":{"nam":"Malahide","lat":"53.4509","lon":"-6.15649"},"mlgar":{"nam":"Mullingar","lat":"53.523","lon":"-7.34608"},"mllow":{"nam":"Mallow","lat":"52.1396","lon":"-8.65521"},"mlsrt":{"nam":"Millstreet","lat":"52.0776","lon":"-9.06973"},"mnebg":{"nam":"Bagenalstown","lat":"52.699","lon":"-6.95213"},"mnlaj":{"nam":"Manulla Junction","lat":"53.828","lon":"-9.19296"},"monvn":{"nam":"Monasterevin","lat":"53.1454","lon":"-7.06361"},"mynth":{"nam":"Maynooth","lat":"53.378","lon":"-6.58993"},"nbrge":{"nam":"Newbridge","lat":"53.1855","lon":"-6.80807"},"newry":{"nam":"Newry","lat":"54.1911","lon":"-6.36225"},"nnagh":{"nam":"Nenagh","lat":"52.8605","lon":"-8.19471"},"ornmr":{"nam":"Oranmore","lat":"53.2751","lon":"-8.94792"},"pdown":{"nam":"Portadown","lat":"54.4295","lon":"-6.43868"},"perse":{"nam":"Pearse","lat":"53.3433","lon":"-6.24829"},"phnpk":{"nam":"Phoenix Park","lat":"53.3777","lon":"-6.34591"},"pmnck":{"nam":"Portmarnock","lat":"53.4169","lon":"-6.1512"},"ptlse":{"nam":"Portlaoise","lat":"53.0371","lon":"-7.30086"},"ptrtn":{"nam":"Portarlington","lat":"53.146","lon":"-7.18055"},"rahny":{"nam":"Raheny","lat":"53.3815","lon":"-6.17699"},"rbrok":{"nam":"Rushbrooke","lat":"51.8496","lon":"-8.32252"},"rcrea":{"nam":"Roscrea","lat":"52.9607","lon":"-7.7941"},"rdrum":{"nam":"Rathdrum","lat":"52.9295","lon":"-6.22641"},"rlept":{"nam":"Rosslare Harbour","lat":"52.2531","lon":"-6.33493"},"rlstd":{"nam":"Rosslare Strand","lat":"52.2726","lon":"-6.39254"},"rlusk":{"nam":"Rush and Lusk","lat":"53.5201","lon":"-6.1439"},"rmore":{"nam":"Rathmore","lat":"52.0854","lon":"-9.21756"},"rscmn":{"nam":"Roscommon","lat":"53.6243","lon":"-8.19631"},"salns":{"nam":"Sallins","lat":"53.2469","lon":"-6.66386"},"scove":{"nam":"Glasthule (Sandycove ) ","lat":"53.2878","lon":"-6.12712"},"seapt":{"nam":"Seapoint","lat":"53.2991","lon":"-6.16512"},"shill":{"nam":"Monkstown ( Salthill )","lat":"53.2954","lon":"-6.15206"},"sidny":{"nam":"Sydney Parade","lat":"53.3206","lon":"-6.21112"},"skill":{"nam":"Shankill","lat":"53.2364","lon":"-6.11691"},"skres":{"nam":"Skerries","lat":"53.5741","lon":"-6.11933"},"sligo":{"nam":"Sligo","lat":"54.2723","lon":"-8.48249"},"smont":{"nam":"Sandymount","lat":"53.3281","lon":"-6.22116"},"suttn":{"nam":"Sutton","lat":"53.392","lon":"-6.11448"},"sxmbr":{"nam":"Sixmilebridge","lat":"52.7376","lon":"-8.78427"},"tara ":{"nam":"Tara Street","lat":"53.347","lon":"-6.25425"},"thrls":{"nam":"Thurles","lat":"52.6766","lon":"-7.82189"},"thtwn":{"nam":"Thomastown","lat":"52.523","lon":"-7.14891"},"tipry":{"nam":"Tipperary","lat":"52.4701","lon":"-8.1625"},"tmore":{"nam":"Tullamore","lat":"53.2704","lon":"-7.49985"},"tpmor":{"nam":"Templemore","lat":"52.7878","lon":"-7.82293"},"trlee":{"nam":"Tralee","lat":"52.271","lon":"-9.69846"},"wbdge":{"nam":"Wellingtonbridge","lat":"52.2678","lon":"-6.75392"},"wbrok":{"nam":"Woodbrook","lat":"53.22","lon":"-6.1101"},"wford":{"nam":"Waterford","lat":"52.2667","lon":"-7.1183"},"wlawn":{"nam":"Woodlawn","lat":"53.3432","lon":"-8.47231"},"wlow ":{"nam":"Wicklow","lat":"52.9882","lon":"-6.05338"},"wport":{"nam":"Westport","lat":"53.7955","lon":"-9.50885"},"wxfrd":{"nam":"Wexford","lat":"52.3434","lon":"-6.4636"}}
FFF;
		private $stations;
		const SERVICE_URL = 'http://api.irishrail.ie/realtime/realtime.asmx/getStationDataByCodeXML?StationCode={stationcode}';

		function __construct() {			
			$st = json_decode(self::STATION_DATA,true);			
			$this->stations = array();
			foreach($st as $key => $item) {
				$item['cod'] = $key;
				$this->stations[] = $item;
			}

		}

		function getETA($item) {
			$fields = array ( 'Exparrival','Scharrival', 'Expdepart', 'Schdepart');
			foreach($fields as $f) {
				$t = trim((string) $item->{$f});
				if($t!="" && $t!="00:00") return $t;
			}
			return "";	
		}

		public function getStations($filter) {
			return TransportHelper::filter_data($this->stations,$filter);
		}

		public function getStationInfo($stationcode,$filter) {
			$url = str_replace("{stationcode}",$stationcode,self::SERVICE_URL);
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
				 if($due<160) {
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

class DublinBus implements TransportInterface {
	
	private $client;

	public function initClient() {
		$this->client = new nusoap_client('http://rtpi.dublinbus.biznetservers.com/DublinBusRTPIService.asmx?WSDL', true,'', '', '', '');
		$err = $this->client->getError();
		$ret = array();
		if($err) {
			$err['status'] = "502";
			$err['error'] = "Unknown error occurred initialising API";
			return $err;
		}
		return array("status" => "200", "message" =>"ok");
	}

	public function getStations($filter = null) {
		$ret = $this->initClient();
		if($ret['status']!="200") return $ret;
		if(!isset($filter['route'])) {
			$result = $this->client->call('GetAllDestinations', array());	
		} else {
			$route = $filter['route'];
			$result = $this->client->call('GetStopDataByRoute', array('route' => $route));	
		}		
		return $result;
	}

	public function getStationInfo($stationcode,$filter) { // 3237
		$url = "http://rtpi.dublinbus.biznetservers.com/DublinBusRTPIService.asmx";
        
        // xml post structure

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

            $xml = new SimpleXMLElement($response2);
            $xml->registerXPathNamespace("s", "http://schemas.xmlsoap.org/soap/envelope/");
            $xml->registerXPathNamespace("a", "http://dublinbus.ie/");            
            $xml->registerXPathNamespace("b", "urn:schemas-microsoft-com:xml-msdata");
            $xml->registerXPathNamespace("c", "urn:schemas-microsoft-com:xml-diffgram-v1");
            $xml->registerXPathNamespace("h", "");


            //$xml = $xml->xpath();
            $xml = new SimpleXMLElement($response2);
            $xml = $xml->GetRealTimeStopDataResponse->GetRealTimeStopDataResult;
            
            $z = $xml;
            print_r($z->asXML());
          	

            //return $response2;

            // user $parser to get your data out of XML response and to display it.

	}


}


exit
?>