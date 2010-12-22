<?php
/*******************************************************************************************
 * Nom du fichier		: statutManager.php
 * Date					: 1 janvier 2009
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Défini ce qu'est un statut
 *******************************************************************************************
 * Un statut est une phrase courte qui peut être utilisée pour indiquer son activité/humeur du moment. Très utilisé sur MSN et facebook.
 * Les statuts sont mémorisés sur le long terme, il est donc possible de l'utiliser comme une plateforme de micro-blogging comme twitter.com
 */
class statutManager {

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
	 * Retourne les infos sur UN statut dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la statut
	 */
	function getStatut($id_statut){
		$clauses['id_statut'] = $id_statut;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'statut',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUS les statuts avec possibilité de filtrer ceux-ci
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getStatuts($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'statut',$clauses,array('*'),$orderBy);
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
	function insertStatut($nom,$id_auteur='1',$description='',$evaluation='0',$guid='',$datePublication='2010-12-05 17:00:00',$auteurTexte=''){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['guid'] = $guid;
		$champs['id_auteur'] = $id_auteur;
		$champs['description'] = $description;
		$champs['auteur_texte'] = $auteurTexte;
		$champs['evaluation'] = $evaluation;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		$champs['date_publication'] = $datePublication;
		
		// crée le nouvel enregistrement et obtient la clé
		$id_statut = $this->connection->insert($this->tablePrefix.'statut',$champs);
		
		return $id_statut;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un statut
	 * Ne met à jour que les champs pour lesquels une nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos du statut
	 */
	function updateStatut($id_statut,$nom,$id_auteur,$description,$evaluation,$guid,$datePublication,$auteurTexte){
				
		if(!empty($nom)){ $champs['nom'] = $nom; }
		if(!empty($uid)){ $champs['guid'] = $guid; }
		if(!empty($uid)){ $champs['date_publication'] = $datePublication; }
		if(!empty($uid)){ $champs['auteur_texte'] = $auteurTexte; }
		if(!empty($id_auteur)){ $champs['id_auteur'] = $id_auteur; }
		if(!empty($description)){ $champs['description'] = $description; }
		if(!empty($evaluation)){ $champs['evaluation'] = $evaluation; }
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		
		$conditions['id_statut'] = $id_statut;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'statut',$champs,$conditions);
	}

	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime une statut et ses champs externes
	 *
	 * @return -
	 * @param int id du statut à supprimer
	 */
	function deleteStatut($id_statut){
		
		$request = "DELETE FROM `".$this->tablePrefix."statut` WHERE `id_statut`='".$id_statut."' ";
		return $this->connection->query($request);
	}
	
	/**
	 * obtient les guids
	 *
	 * @return array() le tableau contenant la liste des uid
	 * @param dateTime date limte la plus ancienne pour obtenir les guid 
	 */
	function getGuids($dateLimite){
		
		$query = "select guid from ".$this->tablePrefix."statut where date_creation > '".$dateLimite."'";
		$result = $this->connection->query($query);
		return $this->connection->getRowArrays($result);
	}
	
	
} // statutManager
?>
