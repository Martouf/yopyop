<?php
/*******************************************************************************************
 * Nom du fichier		: notificationManager.php
 * Date					: 27 avril 2011
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Défini ce qu'est une notification
 *******************************************************************************************
 *  Permet de stocker des messages d'historique, d'alerte, de messagerie interne... etc..
 *
 */
class notificationManager {

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
	 * Retourne les infos sur une notification dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la notification
	 */
	function getNotification($id_notification){
		$clauses['id_notification'] = $id_notification;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'notification',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUTEs les notifications avec possibilité de filtrer celles-ci
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtres à faire
	 * @param string le nom du champ orderby
	 */
	function getNotifications($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'notification',$clauses,array('*'),$orderBy);
		return $this->connection->getAssocArrays($result);
	}
	
	/*******************************************************************
	 * INSERT
	 *******************************************************************/

	/**
	 * Ajouter un élément
	 * le type, l'id du groupe d'élément et l'id du groupe d'utilisateur est requis
	 *
	 * @return l'id du nouvel enregistrement
	 * @param string toute les infos de l'événement
	 */
	function insertNotification($nom='',$description='',$type='',$etat='0',$evaluation='0'){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['type'] = $type;
		$champs['etat'] = $etat;
		$champs['evaluation'] = $evaluation;
		$champs['createur'] = $_SESSION['id_personne'];
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		
		// crée le nouvel enregistrement et obtient la clé
		$id_notification = $this->connection->insert($this->tablePrefix.'notification',$champs);
		
		return $id_notification;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un notification
	 * Ne met à jour que les champs pour lesquels un nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos de la notification
	 */
	function updateNotification($id_notification,$nom,$description,$type,$etat,$evaluation){
		if(!empty($nom)){ $champs['nom'] = $nom; }
		if(!empty($url)){ $champs['description'] = $description; }
		if(!empty($ip)){ $champs['type'] = $type; }
		if(!empty($etat)){ $champs['etat'] = $etat; }
		if(!empty($evaluation)){ $champs['evaluation'] = $evaluation; }
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		
		$conditions['id_notification'] = $id_notification;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'notification',$champs,$conditions);
	}

	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime une notification et ses champs externes
	 *
	 * @return -
	 * @param int id de la notification à supprimer
	 */
	function deleteNotification ($id_notification){
		
		$request = "DELETE FROM `".$this->tablePrefix."notification` WHERE `id_notification`='".$id_notification."' ";
		return $this->connection->query($request);
	}

	
	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/

	/**
	 * supprime kes notifications depuis une date donnée
	 *
	 * @return -
	 * @param int id de la notification à supprimer
	 */
	function purge($dateLimite=''){
		if (empty($dateLimite)) {
			$date = date('Y-m-d H:i:s'); // par défaut, aujourd'hui
			$dateLimite = date('Y-m-d H:i:s',strtotime($date.' - 7 day')); // par défaut, la semaine dernière
		}
		$request = "DELETE FROM `".$this->tablePrefix."notification` WHERE `date_creation`<'".$dateLimite."' ";
		return $this->connection->query($request);
	}
	
} // notificationManager
?>
