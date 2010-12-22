<?php
/*******************************************************************************************
 * Nom du fichier		: restrictionManager.php
 * Date					: 27 novembre 2008
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Défini ce qu'est une restriction
 *******************************************************************************************
 *  modification du modèle de la table et définition d'un préfixe pour les tables
 * 	$typesRestrictions = array('1'=>'lire','2'=>'écrire','3'=>'lister','4'=>'commenter','5'=>'taguer');
 *
 */
class restrictionManager {

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
	 * Retourne les infos sur UNE restriction dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la restriction
	 */
	function getRestriction($id_restriction){
		$clauses['id_restriction'] = $id_restriction;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'restriction',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUTE les restrictions avec possibilité de filtrer celle-ci
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getRestrictions($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'restriction',$clauses,array('*'),$orderBy);
		return $this->connection->getAssocArrays($result);
	}
	
	/*******************************************************************
	 * INSERT
	 *******************************************************************/

	/**
	 * Ajouter un élément
	 * le type, l'id du groupe d'élément et l'id du groupe d'utilsateur est requis
	 *
	 * @return l'id du nouvel enregistrement
	 * @param string toute les infos de l'événement
	 */
	function insertRestriction($idGroupeUtilisateur,$idGroupeElement,$type,$nom='',$evaluation='0'){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['id_groupe_utilisateur'] = $idGroupeUtilisateur;
		$champs['id_groupe_element'] = $idGroupeElement;
		$champs['type'] = $type;
		$champs['nom'] = $nom;
		$champs['evaluation'] = $evaluation;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		
		// crée le nouvel enregistrement et obtient la clé
		$id_restriction = $this->connection->insert($this->tablePrefix.'restriction',$champs);
		
		return $id_restriction;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un restriction
	 * Ne met à jour que les champs pour lesquels une nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos de la restriction
	 */
	function updateRestriction($id_restriction,$idGroupeUtilisateur,$idGroupeElement,$type,$nom,$evaluation){
		if(!empty($idGroupeUtilisateur)){ $champs['id_groupe_utilisateur'] = $idGroupeUtilisateur; }
		if(!empty($idGroupeElement)){ $champs['id_groupe_element'] = $idGroupeElement; }
		if(!empty($type)){ $champs['type'] = $type; }
		if(!empty($nom)){ $champs['nom'] = $nom; }
		if(!empty($evaluation)){ $champs['evaluation'] = $evaluation; }
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		
		$conditions['id_restriction'] = $id_restriction;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'restriction',$champs,$conditions);
	}

	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime une restriction et ses champs externes
	 *
	 * @return -
	 * @param int id de la restriction à supprimer
	 */
	function deleteRestriction ($id_restriction){
		
		$request = "DELETE FROM `".$this->tablePrefix."restriction` WHERE `id_restriction`='".$id_restriction."' ";
		return $this->connection->query($request);
	}

	
	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/

	/**
	 * obtient un tableau contenant les restrictions entre un visiteur et un objet
	 *
	 * @return array un tableau contenant la liste des restrictions (1,2,3...) entre l'élément demandé et le visiteur demandé
	 * @param int id de l'élément
	 * @param string type de l'élément
	 * @param int id du visiteur
	 */
	function getRestrictionsList($idElement, $typeElement, $idVisiteur){
		$listeRestrictions = array();
		
		// la version texte est plus agréable à manipuler dans le code, mais la version chiffrée dans la base et 2 fois plus rapide pour faire les jointures.
		$typeObjet = array('personne'=>1,'document'=>2,'photo'=>3,'evenement'=>4);
		$table_element = $typeObjet[$typeElement];
		
		// Obtient la liste des groupes dans lesquels se trouve le visiteur. (la requête ne regarde pas le type qui est toujours =1 si ceci devait changer, revoir la requête)
		$query = "select id_groupe from `".$this->tablePrefix."groupe-element` where `".$this->tablePrefix."groupe-element`.id_element='".$idVisiteur."' and `".$this->tablePrefix."groupe-element`.table_element='1'"; // 1 => type ressource=personne
		$result = $this->connection->query($query);
		$listeGroupeVisiteur = $this->connection->getRowArrays($result);
		
		//obtient la liste des groupes dans lesquels se trouve la ressource demandée
		$query = "select id_groupe from `".$this->tablePrefix."groupe-element` where `".$this->tablePrefix."groupe-element`.id_element='".$idElement."' and `".$this->tablePrefix."groupe-element`.table_element='".$table_element."'";
		$result = $this->connection->query($query);
		$listeGroupeRessource = $this->connection->getRowArrays($result);

		 // print_r($listeGroupeVisiteur);
		 // print_r($listeGroupeRessource);
		 
		$listeRestrictions = array();
		foreach ($listeGroupeVisiteur as $idGroupeVisiteur) {
			foreach ($listeGroupeRessource as $idGroupeRessource) {
				// va chercher les restrictions entre les 2 types de groupes.
				$query = "select type from ".$this->tablePrefix."restriction where id_groupe_utilisateur='".$idGroupeVisiteur."' and id_groupe_element='".$idGroupeRessource."'";
				$result = $this->connection->query($query);
				$listeRestrictions = array_merge($listeRestrictions, $this->connection->getRowArrays($result));
			}
		}
		return $listeRestrictions;
	}
	
} // restrictionManager
?>
