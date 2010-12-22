<?php
/*******************************************************************************************
 * Nom du fichier		: fichierManager.php
 * Date					: 9 septembre 2009 (9.9.9)
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Défini ce qu'est un fichier
 *******************************************************************************************
 *  
 *
 */
class fichierManager {

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
	 * Retourne les infos sur UN fichier dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la fichier
	 */
	function getFichier($id_fichier){
		$clauses['id_fichier'] = $id_fichier;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'fichier',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUTE les fichiers avec possibilité de filtrer celle-ci
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getFichiers($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'fichier',$clauses,array('*'),$orderBy);
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
	 * @param string toute les infos du fichier
	 */
	function insertFichier($nom='',$description='',$lien='',$evaluation='0',$externe='0'){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['lien'] = $lien;
		$champs['evaluation'] = $evaluation;
		$champs['externe'] = $externe;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		
		// crée le nouvel enregistrement et obtient la clé
		$id_fichier = $this->connection->insert($this->tablePrefix.'fichier',$champs);
		
		return $id_fichier;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un fichier
	 * Ne met à jour que les champs pour lesquels un nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos de la fichier
	 */
	function updateFichier($id_fichier,$nom,$description,$lien,$evaluation,$externe){
				
		if(!empty($nom)){ $champs['nom'] = $nom; }
		if(!empty($description)){ $champs['description'] = $description; }
		if(!empty($lien)){ $champs['lien'] = $lien; }
		if(!empty($evaluation)){ $champs['evaluation'] = $evaluation; }
		if(!empty($externe)){ $champs['externe'] = $externe; }
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		
		$conditions['id_fichier'] = $id_fichier;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'fichier',$champs,$conditions);
	}


	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime un fichier et ses champs externes
	 *
	 * @return -
	 * @param int id de la fichier à supprimer
	 */
	function deleteFichier ($id_fichier){
		
		// TODO: virer les mots clés avant de supprimer la fichier..
		
		$request = "DELETE FROM `".$this->tablePrefix."fichier` WHERE `id_fichier`='".$id_fichier."' ";
		return $this->connection->query($request);
	}

	
	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/

	
	
	 /* Permet d'obtenir le chemin d'accès de la vignette en fonction du chemin d'accès de l'image.  Ex:  fichiers/2006/toto/vignettes/toto.jpg
	  * Fonctionne aussi avec des url
	  */
	 function getLienVignette($imagePath){
	 	$nomSimplifie = basename($imagePath);
		$nomDossier = dirname($imagePath);  // obtient le nom du dossier. Ex: fichiers/2006/toto/toto.jpg devient fichiers/2006/toto
	 	$thpath = $nomDossier.'/vignettes/'.$nomSimplifie; // chemin d'accès de la vignette: fichiers/2006/toto + /vignettes/ + toto.jpg
	 	return $thpath;
	 }
	
	
	
	/* Permet de supprimer ou modifier tout les caractères qui pourraient poser des
	 * problèmes lors de leur utilisation comme nom de fichier.
	 * Il s'agit du remplacement des caractère accentué par leur équivalent non accentué. (fonctionne en utf-8)
	 * Du remplacement des espaces par des _
	 * Du remplacement des ' par des _
	 *
	 * @return: string le nom du fichier simplifié
	 * @param: $nomFichier string le nom du fichier que l'on veut simplifier
	 */
	function simplifieNomFichier($nomFichier){
		// enlève les accents
		$a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ'; 
	    $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr'; 
	    $nomFichier = utf8_decode($nomFichier);     
	    $nomFichier = strtr($nomFichier, utf8_decode($a), $b); 
	    $nomFichier = strtolower($nomFichier); 
	    $nomFichier = utf8_encode($nomFichier);	

		// remplace les espaces par des _
		$nomFichier = preg_replace("/\s/","_",$nomFichier);
		// supprim les antislashes d'échappement des '
		$nomFichier = stripslashes($nomFichier);
		// Remplace les apostrophes par des _
		$nomFichier = preg_replace("/\'/","_",$nomFichier);

		return $nomFichier;
	}
	

	
} // fichierManager
?>
