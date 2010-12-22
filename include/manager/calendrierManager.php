<?php
/*******************************************************************************************
 * Nom du fichier		: calendrierManager.php
 * Date					: 9 janvier 2008
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@ecodev.ch
 * But de ce fichier	: Défini ce qu'est une calendrier.
 *******************************************************************************************
 * Le calendrier est une entité qui regroupe des événements. Le calendrier est parfois redondant avec le tags. Mais souvent il est complémentaire.
 * Le calendrier peut être distant, il stoke donc toutes les données qui lui permettent de se mettre à jour via une url.
 * Le calendrier détermine la couleur d'un événement.
 * Le calendrier peut être utilisé pour séparer un groupe d'événement d'un autre. Un événement référence un calendrier avec le champ id_calendrier.
 *
 */
class calendrierManager {

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
	 * Retourne les infos sur UN calendrier dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la calendrier
	 */
	function getCalendrier($id_calendrier){
		$clauses['id_calendrier'] = $id_calendrier;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'calendrier',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUS les calendriers avec possibilité de filtrer ceux-ci
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getCalendriers($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'calendrier',$clauses,array('*'),$orderBy);
		return $this->connection->getAssocArrays($result);
	}
	
	/*******************************************************************
	 * INSERT
	 *******************************************************************/

	/**
	 * Ajouter un élément
	 * Seul le prénom est requis
	 *
	 * @return l'id du nouvel enregistrement
	 * @param string toute les infos de l'événement
	 */
	function insertCalendrier($nom,$description,$couleur='',$distant='',$url='',$tags='',$date_last_importation,$evaluation='0'){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['couleur'] = $couleur;
		$champs['distant'] = $distant;
		$champs['url'] = $url;
		$champs['tags'] = $tags;
		$champs['date_last_importation'] = $date_last_importation;
		$champs['evaluation'] = $evaluation;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		
		// crée le nouvel enregistrement et obtient la clé
		$id_calendrier = $this->connection->insert($this->tablePrefix.'calendrier',$champs);
		
		return $id_calendrier;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un calendrier
	 * Ne met à jour que les champs pour lesquels une nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos de la calendrier
	 */
	function updateCalendrier($id_calendrier,$nom,$description,$couleur,$distant,$url,$tags,$date_last_importation,$evaluation){
				
		if(!empty($nom)){ $champs['nom'] = $nom; }
		if(!empty($description)){ $champs['description'] = $description; }
		if(!empty($couleur)){ $champs['couleur'] = $couleur; }
		if(!empty($distant)){ $champs['distant'] = $distant; }
		if(!empty($url)){ $champs['url'] = $url; }
		if(!empty($tags)){ $champs['tags'] = $tags; }
		if(!empty($date_last_importation)){ $champs['date_last_importation'] = $date_last_importation; }
		if(!empty($evaluation)){ $champs['evaluation'] = $evaluation; }
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		
		$conditions['id_calendrier'] = $id_calendrier;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'calendrier',$champs,$conditions);
	}

	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime une calendrier et ses champs externes
	 *
	 * @return -
	 * @param int id de la calendrier à supprimer
	 */
	function deleteCalendrier ($id_calendrier){
		
		$request = "DELETE FROM `".$this->tablePrefix."calendrier` WHERE `id_calendrier`='".$id_calendrier."' ";
		return $this->connection->query($request);
	}

	
	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/
	

	
	
} // calendrierManager
?>
