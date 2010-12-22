<?php
/*******************************************************************************************
 * Nom du fichier		: reservationManager.php
 * Date					: 28 juillet 2010
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@ecodev.ch
 * But de ce fichier	: Défini ce qu'est une reservation
 *******************************************************************************************
 *  
 *
 */
class reservationManager {

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
	 * Retourne les infos sur UNE reservation dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la reservation
	 */
	function getReservation($id_reservation){
		$clauses['id_reservation'] = $id_reservation;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'reservation',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUTE les reservations avec possibilité de filtrer celles-ci
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getReservations($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'reservation',$clauses,array('*'),$orderBy);
		return $this->connection->getAssocArrays($result);
	}
	
	/*******************************************************************
	 * INSERT
	 *******************************************************************/

	/**
	 * Ajouter un élément
	 *
	 * @return l'id du nouvel enregistrement
	 * @param string toute les infos de la reservation
	 */
	function insertReservation($nom='',$description='',$id_locataire='',$id_objet='',$id_evenement='',$type='0',$etat='0',$evaluation='0',$groupeAutoriseLecture='0',$groupeAutoriseEcriture='0',$groupeAutoriseCommentaire='0'){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['id_locataire'] = $id_locataire;
		$champs['id_objet'] = $id_objet;
		$champs['id_evenement'] = $id_evenement;
		$champs['type'] = $type;
		$champs['etat'] = $etat;
		$champs['evaluation'] = $evaluation;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		$champs['groupe_autorise_lecture'] = $groupeAutoriseLecture;
		$champs['groupe_autorise_ecriture'] = $groupeAutoriseEcriture;
		$champs['groupe_autorise_commentaire'] = $groupeAutoriseCommentaire;
		$champs['createur'] = $_SESSION['id_personne'];
		$champs['modificateur'] = $_SESSION['id_personne'];
		
		// crée le nouvel enregistrement et obtient la clé
		$id_reservation = $this->connection->insert($this->tablePrefix.'reservation',$champs);
		
		return $id_reservation;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un reservation
	 * Ne met à jour que les champs pour lesquels une nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos de la reservation
	 */
	function updateReservation($id_reservation,$nom,$description,$id_locataire,$id_objet,$id_evenement,$type,$etat,$evaluation,$groupeAutoriseLecture,$groupeAutoriseEcriture,$groupeAutoriseCommentaire){
		if(!empty($nom)){$champs['nom'] = $nom; }
		if(!empty($description)){$champs['description'] = $description; }
		if(!empty($id_locataire)){$champs['id_locataire'] = $id_locataire; }
		if(!empty($id_objet)){$champs['id_objet'] = $id_objet; }
		if(!empty($id_evenement)){$champs['id_evenement'] = $id_evenement; }
		if(!empty($type)){$champs['type'] = $type; }
		if(!empty($etat)){$champs['etat'] = $etat; }
		if(!empty($evaluation)){$champs['evaluation'] = $evaluation; }
		if(!empty($groupeAutoriseLecture)){$champs['groupe_autorise_lecture'] = $groupeAutoriseLecture; }
		if(!empty($groupeAutoriseEcriture)){$champs['groupe_autorise_ecriture'] = $groupeAutoriseEcriture; }
		if(!empty($groupeAutoriseCommentaire)){$champs['groupe_autorise_commentaire'] = $groupeAutoriseCommentaire; }
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		$champs['modificateur'] = $_SESSION['id_personne'];
		
		$conditions['id_reservation'] = $id_reservation;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'reservation',$champs,$conditions);
	}

	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime un reservation et ses champs externes
	 *
	 * @return -
	 * @param int id de la reservation à supprimer
	 */
	function deleteReservation ($id_reservation){
		
		$request = "DELETE FROM `".$this->tablePrefix."reservation` WHERE `id_reservation`='".$id_reservation."' ";
		return $this->connection->query($request);
	}

	
	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/

	
	
} // reservationManager
?>
