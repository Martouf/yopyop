<?php
/*******************************************************************************************
 * Nom du fichier		: transactionManager.php
 * Date					: 28 juillet 2010
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@ecodev.ch
 * But de ce fichier	: Défini ce qu'est une transaction (historique de la transaction bancaire)
 *******************************************************************************************
 *  
 *
 */
class transactionManager {

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
	 * Retourne les infos sur UNE transaction dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la transaction
	 */
	function getTransaction($id_transaction){
		$clauses['id_transaction'] = $id_transaction;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'transaction',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUTES les transactions avec possibilité de filtrer celles-ci
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getTransactions($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'transaction',$clauses,array('*'),$orderBy);
		return $this->connection->getAssocArrays($result);
	}
	
	/*******************************************************************
	 * INSERT
	 *******************************************************************/
	
	/**
	 * Ajouter un élément
	 *
	 * @return l'id du nouvel enregistrement
	 * @param string toute les infos du transaction
	 */
	function insertTransaction($nom='',$description='',$id_source='',$id_destinataire='',$montant='',$evaluation='0',$groupeAutoriseLecture='0',$groupeAutoriseEcriture='0',$groupeAutoriseCommentaire='0'){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['id_source'] = $id_source;
		$champs['id_destinataire'] = $id_destinataire;
		$champs['montant'] = $montant;
		$champs['ip'] = $_SERVER['REMOTE_ADDR'];
		$champs['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
		$champs['evaluation'] = $evaluation;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		$champs['groupe_autorise_lecture'] = $groupeAutoriseLecture;
		$champs['groupe_autorise_ecriture'] = $groupeAutoriseEcriture;
		$champs['groupe_autorise_commentaire'] = $groupeAutoriseCommentaire;
		$champs['createur'] = $_SESSION['id_personne'];
		$champs['modificateur'] = $_SESSION['id_personne'];
		
		// crée le nouvel enregistrement et obtient la clé
		$id_transaction = $this->connection->insert($this->tablePrefix.'transaction',$champs);
		
		return $id_transaction;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un transaction
	 * Ne met à jour que les champs pour lesquels une nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos du transaction
	 */
	function updateTransaction($id_transaction,$nom,$description,$id_source,$id_destinataire,$montant,$ip,$user_agent,$evaluation,$groupeAutoriseLecture,$groupeAutoriseEcriture,$groupeAutoriseCommentaire){
		if(!empty($nom)){$champs['nom'] = $nom; }
		if(!empty($description)){$champs['description'] = $description; }
		if(!empty($id_source)){$champs['id_source'] = $id_source; }
		if(!empty($id_destinataire)){$champs['id_destinataire'] = $id_destinataire; }
		if(!empty($montant)){$champs['montant'] = $montant; }
		if(!empty($ip)){$champs['ip'] = $ip; }
		if(!empty($user_agent)){$champs['user_agent'] = $user_agent; }
		if(!empty($evaluation)){$champs['evaluation'] = $evaluation; }
		if(!empty($groupeAutoriseLecture)){$champs['groupe_autorise_lecture'] = $groupeAutoriseLecture; }
		if(!empty($groupeAutoriseEcriture)){$champs['groupe_autorise_ecriture'] = $groupeAutoriseEcriture; }
		if(!empty($groupeAutoriseCommentaire)){$champs['groupe_autorise_commentaire'] = $groupeAutoriseCommentaire; }
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		$champs['modificateur'] = $_SESSION['id_personne'];
		
		$conditions['id_transaction'] = $id_transaction;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'transaction',$champs,$conditions);
	}

	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime un transaction et ses champs externes
	 *
	 * @return -
	 * @param int id de la transaction à supprimer
	 */
	function deleteTransaction ($id_transaction){
		
		$request = "DELETE FROM `".$this->tablePrefix."transaction` WHERE `id_transaction`='".$id_transaction."' ";
		return $this->connection->query($request);
	}

	
	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/

	/**
	 * supprime les transaction depuis une date donnée
	 *
	 * @return -
	 * @param int id de la transaction à supprimer
	 */
	function purge($dateLimite=''){
		if (empty($dateLimite)) {
			$date = date('Y-m-d H:i:s'); // par défaut, aujourd'hui
			$dateLimite = date('Y-m-d H:i:s',strtotime($date.' - 7 day')); // par défaut, la semaine dernière
		}
		$request = "DELETE FROM `".$this->tablePrefix."transaction` WHERE `date_creation`<'".$dateLimite."' ";
		return $this->connection->query($request);
	}
	
} // transactionManager
?>
