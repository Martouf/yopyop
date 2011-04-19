<?php
/*******************************************************************************************
 * Nom du fichier		: mesureManager.php
 * Date					: 15 avril 2010
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@martouf.ch
 * But de ce fichier	: Défini ce qu'est une mesure. (pour stocker des mesures météo)
 *******************************************************************************************
 * Une mesure est une valeur que l'on enregistre avec son contexte.
 * Une mesure peut être destinée à enregistrer des valeurs provenant de différent capteur, température, météo, vitesse du vent... très pratique pour la météo
 */
class mesureManager {

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
	 * Retourne les infos sur UNE mesure dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la mesure
	 */
	function getMesure($id_mesure){
		$clauses['id_mesure'] = $id_mesure;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'mesure',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUTES les mesures avec possibilité de filtrer celles-ci
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getMesures($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'mesure',$clauses,array('*'),$orderBy);
		return $this->connection->getAssocArrays($result);
	}
	
	/*******************************************************************
	 * INSERT
	 *******************************************************************/
	
	// type des mesures
	// 1 temperature air °C
	// 2 température eau °C
	// 3 pression hPa
	// 4 tendance météo
	// 5 température air minima °C 
	// 6 température air maxima °C
	// 7 humidité relative %
	// 8 point de rosée °C
	// 9 direction vent °
	// 10 sens vent (N-E)
	// 11 vitesse vent noeud
	// 12 vitesse moyenne du vent sur une minute en noeud kts
	// 13 force du vent Bf
	// 14 description vent beaufort "petite brise"
	// 15 sensation thermique °C
	// 16 taux de précipitation en mm/h
	// 17 total précipitation mm
	

	/**
	 * Ajouter un élément
	 * Seul le prénom est requis
	 *
	 * @return l'id du nouvel enregistrement
	 * @param string toute les infos de l'événement
	 */
	function insertMesure($nom,$valeur,$type='1',$url='',$date_mesure='',$nom_lieu='',$id_lieu='0',$description='',$evaluation='0'){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['valeur'] = $valeur;
		$champs['type'] = $type;
		$champs['url'] = $url;
		$champs['nom_lieu'] = $nom_lieu;
		$champs['id_lieu'] = $id_lieu;
		$champs['description'] = $description;
		$champs['evaluation'] = $evaluation;
		if (empty($date_mesure)) {
			$champs['date_mesure'] = $dateCourante;
		}else{
			$champs['date_mesure'] = $date_mesure;
		}
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		
		// crée le nouvel enregistrement et obtient la clé
		$id_mesure = $this->connection->insert($this->tablePrefix.'mesure',$champs);
		
		return $id_mesure;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un mesure
	 * Ne met à jour que les champs pour lesquels une nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos du mesure
	 */
	function updateMesure($id_mesure,$nom,$valeur,$type,$url,$date_mesure,$nom_lieu,$id_lieu,$description,$evaluation){
		
	    if(!empty($nom)){ $champs['nom'] = $nom; }
	    if(!empty($valeur)){ $champs['valeur'] = $type; }
	    if(!empty($type)){ $champs['type'] = $type; }
	    if(!empty($url)){ $champs['url'] = $url; }
	    if(!empty($nom_lieu)){ $champs['nom_lieu'] = $nom_lieu; }
	    if(!empty($id_lieu)){ $champs['id_lieu'] = $id_lieu; }
	    if(!empty($description)){ $champs['description'] = $description; }
	    if(!empty($evaluation)){ $champs['evaluation'] = $evaluation; }
	    if(!empty($date_mesure)){ $champs['date_mesure'] = $dateCourante; }
	    $champs['date_modification'] = date('Y-m-d H:i:s',time());
	    $champs['modificateur'] = $_SESSION['id_personne'];
		
		$conditions['id_mesure'] = $id_mesure;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'mesure',$champs,$conditions);
	}

	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime une mesure et ses champs externes
	 *
	 * @return -
	 * @param int id du mesure à supprimer
	 */
	function deleteMesure($id_mesure){
		
		$request = "DELETE FROM `".$this->tablePrefix."mesure` WHERE `id_mesure`='".$id_mesure."' ";
		return $this->connection->query($request);
	}
	
	// /**
	//  * obtient les guids
	//  *
	//  * @return array() le tableau contenant la liste des uid
	//  * @param dateTime date limte la plus ancienne pour obtenir les guid 
	//  */
	// function getGuids($dateLimite){
	// 	
	// 	$query = "select guid from ".$this->tablePrefix."mesure where date_creation > '".$dateLimite."'";
	// 	$result = $this->connection->query($query);
	// 	return $this->connection->getRowArrays($result);
	// }
	
	
} // mesureManager
?>
