<?php
/*******************************************************************************************
 * Nom du fichier		: lieuManager.php
 * Date					: 30 janvier 2009
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@ecodev.ch
 * But de ce fichier	: Défini ce qu'est un lieu
 *******************************************************************************************
 *  
 *
 */
class lieuManager {

	/*******************************************************************
	 * PARAMETERS
	 *******************************************************************/
	protected $connection;
	protected $tablePrefix;
	

	/*******************************************************************
	 * CONSTRUCTOR
	 *******************************************************************/
	function __construct($connection){
		//connexion à la base de donnée
		$this->connection=$connection;
		$this->tablePrefix="yop_";  // prefix que l'on met devant le nom de chaque table. Permet de faire cohabiter plusieurs fois l'application dans la même base sql.
	}
	
	/*******************************************************************
	 * GET ..... les infos d'un élément en particulier, ou les infos de tous les éléments.
	 *******************************************************************/
	
	/**
	 * Retourne les infos sur UN lieu dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la lieu
	 */
	function getLieu($id_lieu){
		$clauses['id_lieu'] = $id_lieu;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'lieu',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUTE les lieux avec possibilité de filtrer celle-ci
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getLieux($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'lieu',$clauses,array('*'),$orderBy);
		return $this->connection->getAssocArrays($result);
	}
	
	/*******************************************************************
	 * INSERT
	 *******************************************************************/

	/**
	 * Ajouter un élément
	 *
	 * @return l'id du nouvel enregistrement
	 * @param string toute les infos du lieu
	 */
	function insertLieu($nom='',$description='',$categorie='',$rue='',$npa='',$commune='',$pays='',$latitude='',$longitude='',$altitude='',$evaluation='0'){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['categorie'] = $categorie;
		$champs['rue'] = $rue;
		$champs['npa'] = $npa;
		$champs['commune'] = $commune;
		$champs['pays'] = $pays;
		$champs['latitude'] = $latitude;
		$champs['longitude'] = $longitude;
		$champs['altitude'] = $altitude;
		$champs['evaluation'] = $evaluation;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		
		// crée le nouvel enregistrement et obtient la clé
		$id_lieu = $this->connection->insert($this->tablePrefix.'lieu',$champs);
		
		return $id_lieu;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un lieu
	 * Ne met à jour que les champs pour lesquels une nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos du lieu
	 */
	function updateLieu($id_lieu,$nom,$description,$categorie,$rue,$npa,$commune,$pays,$latitude,$longitude,$altitude,$evaluation){
				
		if(!empty($nom)){ $champs['nom'] = $nom; }
		if(!empty($description)){ $champs['description'] = $description; }
		if(!empty($categorie)){ $champs['categorie'] = $categorie; }
		if(!empty($rue)){ $champs['rue'] = $rue; }
		if(!empty($npa)){ $champs['npa'] = $npa; }
		if(!empty($commune)){ $champs['commune'] = $commune; }
		if(!empty($pays)){ $champs['pays'] = $pays; }
		if(!empty($latitude)){ $champs['latitude'] = $latitude; }
		if(!empty($longitude)){ $champs['longitude'] = $longitude; }
		if(!empty($altitude)){ $champs['altitude'] = $altitude; }
		if(!empty($evaluation)){ $champs['evaluation'] = $evaluation; }
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		
		$conditions['id_lieu'] = $id_lieu;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'lieu',$champs,$conditions);
	}

	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime un lieu et ses champs externes
	 *
	 * @return -
	 * @param int id de la lieu à supprimer
	 */
	function deleteLieu ($id_lieu){
		
		$request = "DELETE FROM `".$this->tablePrefix."lieu` WHERE `id_lieu`='".$id_lieu."' ";
		return $this->connection->query($request);
	}

	
	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/

	/**
	 * propose une liste de toutes les valeurs qu'il existe pour le champ categorie
	 *
	 * @return array la liste de valeurs
	 * @param .. heu... rien besoin
	 */
	function getCategories(){
		
		$query = "select distinct categorie from lieu";
		$result = $this->connection->query($query);
		$liste = $this->connection->getAssocArrays($result);

		if(!empty($liste)){
			$listeElements = array();
			foreach ($liste as $key => $value) {
				$element = $value['categorie'];
				$listeElements[] = $element;
			}
			return $listeElements;
		}else{
			return array();
		}
	}
	
	/**
	 * Permet d'obtenir un tableau avec les noms de lieux en fonction d'une catégorie
	 *
	 * @param string le nom de la catégorie selon laquelle on va filtrer les lieux
	 * @return array  un tableau avec tous les motclés.  id => nom
	 * 
	 */
	function getLieuxByCategorie($categorie=""){
		
		if (!empty($categorie)) {
			$query = "select ".$this->tablePrefix."lieu.nom from ".$this->tablePrefix."lieu where ".$this->tablePrefix."lieu.categorie='".$categorie."'";  // order by nom (au besoin)
		}else{
			$query = "select ".$this->tablePrefix."lieu.nom from ".$this->tablePrefix."lieu";  // order by nom (au besoin)
		}
		

		$result = $this->connection->query($query);
		$liste = $this->connection->getAssocArrays($result);
		
		if(!empty($liste)){
			$listeElements = array();
			foreach ($liste as $key => $value) {
				$element = $value['nom'];
				$listeElements[] = $element;
			}
			return $listeElements;
		}else{
			return array();
		}
	}
	
	/**
	 * converti les coordonnées lat long en wgs84 pour avoir des coordonnées ch1903 
	 * Ex: lat: 46.94952985143932 long: 6.834204196929932 => 553 986/200 005
	 *
	 * @return array()  y et x  les coordonnées au format CH1903
	 * @param latitude en degré décimal
	 * @param longitude en degré décimal
	 */
	function getCoordonneeCH1903($lat,$long){
		// Les latitudes φ et les longitudes λ sont à convertir en secondes sexagesimales ["]
		
		// Les grandeurs auxiliaires suivantes sont à calculer (les écarts en latitude et en longitude par rapport à
		// Berne sont exprimés dans l'unité [10000"]) : 
		// φ' = (φ – 169028.66 ")/10000 
		//  λ' = (λ – 26782.5 ")/10000 
		//
		// 3. y [m] = 600072.37 
		//  + 211455.93 * λ'   
		//  - 10938.51 * λ' * φ' 
		//  - 0.36 * λ' * φ'2 
		//  - 44.54 * λ'3 
		//
		// x [m] = 200147.07 
		//  + 308807.95 * φ' 
		//  + 3745.25 * λ' 2 
		//  + 76.63 * φ' 2 
		//  - 194.56 * λ' 2 * φ' 
		//  + 119.79 * φ' 3 
		
		
		// conversion degré décimal en seconde sexagétimale
		// deg = 60 * minutes
		// minutes = 60 * seconde
		// =>deg = 3600 * seconde
		$lat = $lat * 3600;
		$long = $long * 3600;
		
		// latitude:  φ =>lat  ( φ' =>lat2)
		$lat2 = ($lat - 169028.66)/10000;
	
		// longitude:  λ =>long  ( λ' =>long2)
		$long2 = ($long - 26782.5)/10000;
		
		$y = 600072.37 + (211455.93 * $long2) - (10938.51 * $long2 * $lat2) - (0.36 * $long2 * ($lat2*$lat2)) - (44.54 * ($long2*$long2*$long2));
		$y = round($y);
		
		$x = 200147.07 + (308807.95 * $lat2) + (3745.25 * $long2*$long2) + (76.63 * $lat2*$lat2) - (194.56 * $long2*$long2 * $lat2) + (119.79 * $lat2*$lat2*$lat2);
		$x = round($x);
		
		return array('y'=>$y,'x'=>$x);  // 600000,200000
	}
	
	/**
	 * converti les coordonnées ch1903 en latitude et longitude du système wgs84.
	 * Calcul selon: http://geomatics.ladetto.ch/swiss_projection_fr.pdf
	 * Pour des altitudes positives, les 2 systèmes sont équivalents au mètre près. Donc on n'en tient pas compte dans cette fonction.
	 * Ex: 553 986/200 005 => lat: 46.949532367683 long: 6.8342057996687
	 *
	 * @return array()  lat et long  les coordonnées au format wgs84 en notation degré décimale: 46.951081111111,7.4386372222222
	 * @param x (nord) à indiquer en m au format: 200000
	 * @param y (est) à indiquer en m au format: 600000
	 */
	function getCoordonneeWGS84($x,$y){

		// 1. Les coordonnées en projection y (coordonnée est) et x (coordonnée nord) sont à convertir dans le 
		// système civil (Berne = 0 / 0) et à exprimer dans l'unité [1000 km] : 
		//  y' = (y – 600000 m)/1000000 
		//  x' = (x – 200000 m)/1000000 

		// 2. La longitude et la latitude sont à calculer dans l'unité [10000"] : 
		// λ'  = 2.6779094 
		//  + 4.728982 * y' 
		//  + 0.791484 * y' * x' 
		//  + 0.1306 * y' * x'2 
		//  - 0.0436 * y'3 

		// φ'  = 16.9023892 
		//  + 3.238272 * x' 
		//  - 0.270978 * y' 2 
		//  - 0.002528 * x' 2 
		//  - 0.0447 * y' 2 * x' 
		//  - 0.0140 * x' 3 

		// h [m] = h' + 49.55 
		// - 12.60 * y' 
		// - 22.64 * x' 

		// 
		// 3. La longitude et la latitude sont à convertir dans l'unité [°] : 
		// λ = λ' * 100 / 36 
		// φ = φ' *100 / 36 

		$y2 = ($y-600000)/1000000;
		$x2 = ($x-200000)/1000000;

		// longitude:  λ =>long  ( λ' =>long2)
		$long2 = 2.6779094 + (4.728982 * $y2) + (0.791484 * $y2 * $x2) + (0.1306 * $y2 * $x2*$x2) - (0.0436 * $y2*$y2*$y2);

		// latitude:  φ =>lat  ( φ' =>lat2)
		$lat2 = 16.9023892 + (3.238272 * $x2) - (0.270978 * $y2*$y2) - (0.002528 * $x2*$x2) - (0.0447 * $y2*$y2 * $x2) - (0.0140 * $x2*$x2*$x2);	

		// conversion en °
		$long = $long2 *100/36;
		$lat = $lat2 *100/36;

		return array('lat'=>$lat,'long'=>$long);  //pour y=600000, x=200000 => 46.951081111111,7.4386372222222
	}
	
	
	/**
	 * Obtient l'altitude d'un lieu dont on fournit la latitude et longitude
	 * L'altitude est obtenue via un service web de geonames.org sous licence creative Commons cc-by.
	 * 
	 * La version GTOPO
	 *  Webservice Type : REST
	 *	Url : ws.geonames.org/gtopo30?
	 *	Parameters : lat,lng;
	 *	Result : a single number giving the elevation in meters according to gtopo30, ocean areas have been masked as "no data" and have been assigned a value of -9999
	 *	Example http://ws.geonames.org/gtopo30?lat=47.01&lng=10.2
	 *
	 *	This service is also available in JSON format : http://ws.geonames.org/gtopo30JSON?lat=47.01&lng=10.2
	 *  
	 *  OU la version SRTM 3
	 *  http://www.geonames.org/export/web-services.html#srtm3
	 *  Shuttle Radar Topography Mission (SRTM) elevation data. SRTM consisted of a specially modified radar system that flew onboard the Space Shuttle Endeavour during an 11-day mission in February of 2000. The dataset covers land areas between 60 degrees north and 56 degrees south.
	 *  This web service is using SRTM3 data with data points located every 3-arc-second (approximately 90 meters) on a latitude/longitude grid.
	 *
	 *	Webservice Type : REST
	 *	Url : ws.geonames.org/srtm3?
	 *	Parameters : lat,lng;
	 *	Result : a single number giving the elevation in meters according to srtm3, ocean areas have been masked as "no data" and have been assigned a value of -32768
	 *	Example http://ws.geonames.org/srtm3?lat=50.01&lng=10.2
	 *
	 *  http://ws.geonames.org/srtm3?lat=50.01&lng=10.2 
	 *
	 *
	 * @return int altitude
	 * @param latitude en degré décimal
	 * @param longitude en degré décimal
	 */
	function getAltitude($lat,$long){
	//	$url="http://ws.geonames.org/gtopo30?lat=".$lat."&lng=".$long;  // gtopo 30 grille de 60° arc => 900m
		$url="http://ws.geonames.org/srtm3?lat=".$lat."&lng=".$long;  // srtm 3 grille de 3° arc => 90m
		
		$userAgent = 'Mozilla/5.0 (Macintosh; U; PPC Mac OS X; fr) AppleWebKit/523.12 (KHTML, like Gecko) Version/3.0.4 Safari/523.12';

		$curl = curl_init();		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($curl, CURLOPT_URL, $url);
		curl_setopt($curl, CURLOPT_USERAGENT, $userAgent);
	//	curl_setopt($curl, CURLOPT_HEADER, TRUE);
				
		$altitude = curl_exec($curl);
		curl_close($curl);
		
		return $altitude;
	}
	
	/**
	 * Calcul la distance entre 2 lieux à partir de coordonnées CH1903
	 *
	 * @return int distance
	 * @param x1 ordonnée du lieu 1
	 * @param y1 absisse du lieu 1
	 * @param x2 ordonnée du lieu 2
	 * @param y1 absisse du lieu 2
	 */
	function getDistanceCH1903($x1,$y1,$x2,$y2){
		$dx = abs($x1-$x2);
		$dy = abs($y1-$y2);
		return sqrt($dx*$dx+$dy*$dy);
	}
	
	/**
	 * Calcul la distance entre 2 lieux à partir des coordonnées WGS84
	 * Le calcul passe par des coordonnées CH1903. Il y a donc une conversion.
	 * Est ce que le calcul fonctionne aussi hors de suisse ? Est ce qu'il n'y a pas une déformation ?
	 *
	 * @return int distance
	 * @param latitude1 en degré décimal
	 * @param longitude1 en degré décimal
	 * @param latitude2 en degré décimal
	 * @param longitude3 en degré décimal
	 */
	function getDistance($latitude1,$longitude1,$latitude2,$longitude2){
		
		$lieu1 = self::getCoordonneeCH1903($latitude1,$longitude1);
		$lieu2 = self::getCoordonneeCH1903($latitude2,$longitude2);
		
		$distance = self::getDistanceCH1903($lieu1['x'],$lieu1['y'],$lieu2['x'],$lieu2['y']);
		return $distance;
	}
	
	/**
	 * Calcul la dénivellation entre 2 lieux.
	 *
	 * @return int dénivellation en mètres.
	 * @param latitude1 en degré décimal
	 * @param longitude1 en degré décimal
	 * @param latitude2 en degré décimal
	 * @param longitude3 en degré décimal
	 */
	function getDenivellation($latitude1,$longitude1,$latitude2,$longitude2){
		
		$alt1 = self::getAltitude($latitude1,$longitude1);
		$alt2 = self::getAltitude($latitude2,$longitude2);
		
		$denivellation = abs($altitude1 -$altitude2);
		return $distance;
	}

	
} // lieuManager
?>
