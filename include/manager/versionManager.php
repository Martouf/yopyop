<?php
/*******************************************************************************************
 * Nom du fichier		: versionManager.php
 * Date					: 11 novembre 2008
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@extremefondue.ch
 * But de ce fichier	: Défini ce qu'est une version
 *******************************************************************************************
 *  modification du modèle de la table et définition d'un préfixe pour les tables
 *
 */
class versionManager {

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
	 * Retourne les infos sur UNE version dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la version
	 */
	function getVersion($id_version){
		$clauses['id_version'] = $id_version;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'version',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUTES les versions avec possibilité de filtrer celles-ci
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getVersions($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'version',$clauses,array('*'),$orderBy);
		return $this->connection->getAssocArrays($result);
	}
	
	/*******************************************************************
	 * INSERT
	 *******************************************************************/

	/**
	 * Ajouter un élément
	 * 
	 *
	 * @return l'id du nouvel enregistrement
	 * @param string toute les infos de l'événement
	 */
	function insertVersion($id_document,$nom='',$description='',$contenu='',$auteur='',$langue='fr',$evaluation='0'){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['id_document'] = $id_document;
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['contenu'] = $contenu;
		$champs['auteur'] = $auteur;
		$champs['langue'] = $langue;
		$champs['evaluation'] = $evaluation;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		
		// crée le nouvel enregistrement et obtient la clé
		$id_version = $this->connection->insert($this->tablePrefix.'version',$champs);
		
		return $id_version;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un version
	 * Ne met à jour que les champs pour lesquels une nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos de la version
	 */
	function updateVersion($id_version,$id_document,$nom,$description,$contenu,$langue,$auteur,$evaluation){
				
		if(!empty($id_document)){ $champs['id_document'] = $id_document; }
		if(!empty($nom)){ $champs['nom'] = $nom; }
		if(!empty($description)){ $champs['description'] = $description; }
		if(!empty($contenu)){ $champs['contenu'] = $contenu; }
		if(!empty($auteur)){ $champs['auteur'] = $auteur; }
		if(!empty($langue)){ $champs['langue'] = $langue; }
		if(!empty($evaluation)){ $champs['evaluation'] = $evaluation; }
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		
		$conditions['id_version'] = $id_version;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'version',$champs,$conditions);
	}


	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime une version
	 *
	 * @return -
	 * @param int id de la version à supprimer
	 */
	function deleteVersion ($id_version){
		
		$request = "DELETE FROM `".$this->tablePrefix."version` WHERE `id_version`='".$id_version."' ";
		return $this->connection->query($request);
	}

	
	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/
	
	/* Fonction permettant d'avoir toutes les infos sur la dernière version du document
	 * 
	 * @return: array un tableau avec toutes les infos sur la version la plus récente
	 * @param: $id_document : l'id du document pour lequel on veut la dernière version
	 */
	function getLastVersion($id_document,$langue='fr'){
	
		// va chercher la dernière version
		$request = "select * from ".$this->tablePrefix."version where id_document='".$id_document."' and langue='".$langue."' order by date_modification desc limit 1";
		$resultat = $this->connection->query($request);			
		
		// retourne un tableau associatif
		$version = $this->connection->getAssocArray($resultat);
		return $version;
	}
	
	
	/* Fonction permettant d'afficher les différences entre 2 versions...
	 * 
	 * @return: ...
	 * @param: ...
	 */
	
	/* Fonction permettant de restaurer une ancienne version. (on change la date de modification de la version.)
	 * 
	 * @return: ...
	 * @param: ...
	 */
	
} // versionManager
?>
