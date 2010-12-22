<?php
/*******************************************************************************************
 * Nom du fichier		: historiqueManager.php
 * Date					: 1 septembre 2009
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Défini ce qu'est un historique
 *******************************************************************************************
 *  modification du modèle de la table et définition d'un préfixe pour les tables
 * 	$typesHistoriques = array('1'=>'lire','2'=>'écrire','3'=>'lister','4'=>'commenter','5'=>'taguer');
 *
 */
class historiqueManager {

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
	 * Retourne les infos sur un historique dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la historique
	 */
	function getHistorique($id_historique){
		$clauses['id_historique'] = $id_historique;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'historique',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUTE les historiques avec possibilité de filtrer ceux-ci
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getHistoriques($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'historique',$clauses,array('*'),$orderBy);
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
	function insertHistorique($nom=''){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['url'] = $_SERVER['REQUEST_URI'];
		$champs['ip'] = $_SERVER['REMOTE_ADDR'];
		$champs['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		
		// crée le nouvel enregistrement et obtient la clé
		$id_historique = $this->connection->insert($this->tablePrefix.'historique',$champs);
		
		return $id_historique;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un historique
	 * Ne met à jour que les champs pour lesquels un nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos de la historique
	 */
	function updateHistorique($id_historique,$nom,$url,$ip,$user_agent,$evaluation){
		if(!empty($nom)){ $champs['nom'] = $nom; }
		if(!empty($url)){ $champs['url'] = $url; }
		if(!empty($ip)){ $champs['ip'] = $ip; }
		if(!empty($user_agent)){ $champs['user_agent'] = $user_agent; }
		if(!empty($evaluation)){ $champs['evaluation'] = $evaluation; }
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		
		$conditions['id_historique'] = $id_historique;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'historique',$champs,$conditions);
	}

	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime un historique et ses champs externes
	 *
	 * @return -
	 * @param int id de la historique à supprimer
	 */
	function deleteHistorique ($id_historique){
		
		$request = "DELETE FROM `".$this->tablePrefix."historique` WHERE `id_historique`='".$id_historique."' ";
		return $this->connection->query($request);
	}

	
	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/

	/**
	 * supprime un historique depuis une date donnée
	 *
	 * @return -
	 * @param int id de la historique à supprimer
	 */
	function purge($dateLimite=''){
		if (empty($dateLimite)) {
			$date = date('Y-m-d H:i:s'); // par défaut, aujourd'hui
			$dateLimite = date('Y-m-d H:i:s',strtotime($date.' - 7 day')); // par défaut, la semaine dernière
		}
		$request = "DELETE FROM `".$this->tablePrefix."historique` WHERE `date_creation`<'".$dateLimite."' ";
		return $this->connection->query($request);
	}
	
} // historiqueManager
?>
