<?php
/*******************************************************************************************
 * Nom du fichier		: evaluationManager.php
 * Date					: 26 avril 2011
 * modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@ecodev.ch
 * But de ce fichier	: Fourni toutes les méthodes utiles pour manipuler les evaluations
 *******************************************************************************************
 * Un evaluation est un objet qui peut être associé avec n'importe quelle ressource.
 * 
 */

class evaluationManager {

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
	 * Retourne les infos sur TOUTEs les evaluations avec possibilité de filtrer celles-ci.
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() champs un tableau contenant les champs voulus
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getEvaluations($champs=array('*'),$filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'evaluation',$clauses,$champs,$orderBy);
		return $this->connection->getAssocArrays($result);
	}
	
	/**
	 * Retourne les infos sur une evaluation dont l'id est passé en paramètre.
	 * Possibilité de choisir les champs voulu.
	 *
	 * @param array() champs un tableau contenant les champs voulus
	 * @return array() un tableau associatif (key=>value) contenant le résultat de la requête ou false si échec
	 * @param int l'id du evaluation
	 */
	function getEvaluation($id_evaluation,$champs=array('*')){
		$clauses['id_evaluation'] = $id_evaluation;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'evaluation',$clauses,$champs);
		return $this->connection->getAssocArray($result);
	}
	
	
	/*******************************************************************
	 * INSERT
	 *******************************************************************/
	
	/**
	 * Ajouter un evaluation
	 *
	 * @return l'id de l'élément ajouté
	 * @param string toutes les infos de le evaluation
	 */
	function insertEvaluation($id_element,$table_element,$note='0',$nom,$description='',$createur='1'){
		// Correspondance entre une version texte et une version chiffrée des types d'objet.
		// la version texte est plus agréable à manipuler dans le code, mais la version chiffrée dans la base et 2 fois plus rapide pour faire les jointures.
		$typeObjet = array('personne'=>1,'document'=>2,'photo'=>3,'evenement'=>4,'calendrier'=>5,'lieu'=>6,'statut'=>7,'commentaire'=>8,'liste'=>9,'element'=>10,'restriction'=>11,'evaluation'=>12);
		$tableElement = $typeObjet[$table_element];
		
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['id_element'] = $id_element;
		$champs['table_element'] = $tableElement;
		$champs['note'] = $note;
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['createur'] = $createur;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		
		// crée le nouvel enregistrement et obtient la clé
		$id_evaluation = $this->connection->insert($this->tablePrefix.'evaluation',$champs);
		
		return $id_evaluation;
	}
	

	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un evaluation
	 *
	 * @return boolean
	 * @param string tout ce que l'on veut modifier
	 */
	function updateEvaluation($id_evaluation,$id_element,$table_element,$note='0',$nom,$description='',$createur='1'){
		
		// Correspondance entre une version texte et une version chiffrée des types d'objet.
		// la version texte est plus agréable à manipuler dans le code, mais la version chiffrée dans la base et 2 fois plus rapide pour faire les jointures.
		$typeObjet = array('personne'=>1,'document'=>2,'photo'=>3,'evenement'=>4,'calendrier'=>5,'lieu'=>6,'statut'=>7,'commentaire'=>8,'liste'=>9,'element'=>10,'restriction'=>11,'evaluation'=>12);
		$tableElement = $typeObjet[$table_element];
		
		$champs['id_element'] = $id_element;
		$champs['table_element'] = $tableElement;
		$champs['note'] = $note;
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['createur'] = $createur;
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		
		$conditions['id_evaluation'] = $id_evaluation;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'evaluation',$champs,$conditions);
	}


	/*******************************************************************
	 * DELETE
	 *******************************************************************/
	
	/**
	 * supprime un evaluation
	 *
	 * @return true
	 * @param int id_evaluation, l'id de l'evaluation à supprimer
	 */
	function deleteEvaluation ($id_evaluation){
		$request = "DELETE FROM `".$this->tablePrefix."evaluation` WHERE `id_evaluation`='".$id_evaluation."' ";
		return $this->connection->query($request);
	}

	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/
	// à compléter avec des fonctions pour obtenir la note moyenne d'un élément... le nombre d'évaluation pour un élément

	
}
?>
