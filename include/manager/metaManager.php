<?php
/*******************************************************************************************
 * Nom du fichier		: metaManager.php
 * Date					: 13 octobre 2010
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@ecodev.ch
 * But de ce fichier	: Défini ce qu'est une meta
 ******************************************************************************************
 * Le type Meta est particulier. Il est inspiré de ce qui ce fait dans wordpress.
 * C'est un champ de donnée libre qui fonctionne sur le principe clé=>valeur. (nom=>description)
 * Cette métadonnée peut être associée à un élément en particulier en fournissant l'id_element et le table_element de celui-ci.
 *
 */
class metaManager {

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
	 * Retourne les infos sur UNE meta dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la meta
	 */
	function getMeta($id_meta){
		$clauses['id_meta'] = $id_meta;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'meta',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUTES les metas avec possibilité de filtrer celles-ci
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getMetas($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'meta',$clauses,array('*'),$orderBy);
		return $this->connection->getAssocArrays($result);
	}
	
	/*******************************************************************
	 * INSERT
	 *******************************************************************/
	
	/**
	 * Ajouter un élément
	 *
	 * @return l'id du nouvel enregistrement
	 * @param string toute les infos du meta
	 */
	function insertMeta($nom='',$description='',$id_element='',$table_element='',$evaluation='0',$groupeAutoriseLecture='0',$groupeAutoriseEcriture='0',$groupeAutoriseCommentaire='0'){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['id_element'] = $id_element;
		$champs['table_element'] = $table_element;
		$champs['evaluation'] = $evaluation;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		$champs['groupe_autorise_lecture'] = $groupeAutoriseLecture;
		$champs['groupe_autorise_ecriture'] = $groupeAutoriseEcriture;
		$champs['groupe_autorise_commentaire'] = $groupeAutoriseCommentaire;
		$champs['createur'] = $_SESSION['id_personne'];
		$champs['modificateur'] = $_SESSION['id_personne'];
		
		// crée le nouvel enregistrement et obtient la clé
		$id_meta = $this->connection->insert($this->tablePrefix.'meta',$champs);
		
		return $id_meta;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un meta
	 * Ne met à jour que les champs pour lesquels une nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos du meta
	 */
	function updateMeta($id_meta,$description,$nom,$id_element,$table_element,$evaluation,$groupeAutoriseLecture,$groupeAutoriseEcriture,$groupeAutoriseCommentaire){
		if(!empty($nom)){$champs['nom'] = $nom; }
		if(!empty($description)){$champs['description'] = $description; }
		if(!empty($id_element)){$champs['id_element'] = $id_element; }
		if(!empty($table_element)){$champs['table_element'] = $table_element; }
		if(!empty($evaluation)){$champs['evaluation'] = $evaluation; }
		if(!empty($groupeAutoriseLecture)){$champs['groupe_autorise_lecture'] = $groupeAutoriseLecture; }
		if(!empty($groupeAutoriseEcriture)){$champs['groupe_autorise_ecriture'] = $groupeAutoriseEcriture; }
		if(!empty($groupeAutoriseCommentaire)){$champs['groupe_autorise_commentaire'] = $groupeAutoriseCommentaire; }
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		$champs['modificateur'] = $_SESSION['id_personne'];
		
		$conditions['id_meta'] = $id_meta;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'meta',$champs,$conditions);
	}

	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime un meta
	 *
	 * @return -
	 * @param int id de la meta à supprimer
	 */
	function deleteMeta ($id_meta){
		
		$request = "DELETE FROM `".$this->tablePrefix."meta` WHERE `id_meta`='".$id_meta."' ";
		return $this->connection->query($request);
	}

	
	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/
	
} // metaManager
?>
