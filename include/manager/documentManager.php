<?php
/*******************************************************************************************
 * Nom du fichier		: documentManager.php
 * Date					: 11 novembre 2008
 * Modif				: 9 février gestion des documents par date de publication
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@extremefondue.ch
 * But de ce fichier	: Défini ce qu'est un document
 *******************************************************************************************
 * Modification du modèle de la table et définition d'un préfixe pour les tables
 * Un document est composé de versions. Pour utiliser ce manager, il faut donc également disposer de la classe versionManager.
 * Un document n'est principalement que le conteneur des méta-données. Le gros du contenu se trouvent dans le champ contenu des versions.
 * La classe personneManager est également requise. (pour trouver le pseudo d'un auteur d'une version... et donc du document)
 */
class documentManager {

	/*******************************************************************
	 * PARAMETERS
	 *******************************************************************/
	protected $connection;
	protected $tablePrefix;
	protected $versionManager;
	protected $personneManager;
	protected $groupeManager;
	

	/*******************************************************************
	 * CONSTRUCTOR
	 *******************************************************************/
	function __construct($connection,$versionManager,$personneManager,$groupeManager){
		//connexion à la base de donnée
		$this->connection=$connection;
		$this->tablePrefix="yop_";  // prefix que l'on met devant le nom de chaque table. Permet de faire cohabiter plusieurs fois l'application dans la même base sql.
	
		// transmet les managers des autres classes utilisées
		$this->versionManager = $versionManager;
		$this->personneManager = $personneManager;
		$this->groupeManager = $groupeManager;
		
	}
	
	/*******************************************************************
	 * GET ..... les infos d'un élément en particulier, ou les infos de tous les éléments.
	 *******************************************************************/
	
	/**
	 * Retourne les infos sur UN document dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la document
	 */
	function getDocument($id_document){
		$clauses['id_document'] = $id_document;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'document',$clauses);
		$document = $this->connection->getAssocArray($result);
		
		// va chercher le contenu du document. C'est la dernière version disponible dans la langue voulue.
		// on met le contenu du document dans le tableaux associatif
		$version = $this->versionManager->getLastVersion($id_document,'fr'); // pour l'instant on ne gère que le français
		
		$document['contenu'] = $version['contenu'];
		$document['auteur'] = $version['auteur'];  // BUG => PHP Notice:  Undefined index: auteur in documentManager.php on line 60 => idem pour contenu et auteur de la ligne 61
		$document['pseudoAuteur'] = $this->personneManager->getPseudo($version['auteur']); // va chercher le pseudo en fonction de l'id !
		
		return $document;
	}
	
	/**
	 * Retourne les infos sur TOUS les documents avec possibilité de filtrer ceux-ci
	 *  => attention ne retourne pas de contenu en provenance des version... à voir plus tard si c'est utile !
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtres à faire
	 * @param string le nom du champ orderby
	 */
	function getDocuments($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'document',$clauses,array('*'),$orderBy);
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
	 * @param $infoModif string : le commentaire à propos de la modification. Il est stocké dans la table version
	 */
	function insertDocument($nom='',$description='',$contenu='',$evaluation='0',$infoModif='',$datePublication='',$access='',$groupe_autorise=''){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['evaluation'] = $evaluation;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		$champs['createur'] = $_SESSION['id_personne'];
		
		// accès
		$champs['access'] = $access;
		$champs['groupe_autorise'] = $groupe_autorise;
		
		// la date de publication est par défaut la même que la date de création
		if (!empty($datePublication)) {
			$champs['date_publication'] = $date_publication;
		}else{
			$champs['date_publication'] = $dateCourante;
		}
		
		// crée le nouvel enregistrement et obtient la clé
		$id_document = $this->connection->insert($this->tablePrefix.'document',$champs);
		
		// crée une nouvelle version avec le contenu
		$this->versionManager->insertVersion($id_document,$nom,$infoModif,$contenu,$_SESSION['id_personne'],'fr',$evaluation);
		
		return $id_document;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un document
	 * Ne met à jour que les champs pour lesquels une nouvelle valeur est fournie
	 * Lorsque l'on met à jour un document, en fait on crée une nouvelle version d'une version. On ne peut modifier que les métadonnées d'un document. 
	 *
	 * @return boolean
	 * @param string toutes les infos du document
	 */
	function updateDocument($id_document,$nom,$description,$contenu,$evaluation,$infoModif='',$datePublication,$access,$groupe_autorise){
				
		if(!empty($nom)){ $champs['nom'] = $nom; }
		if(!empty($description)){ $champs['description'] = $description; }
		if(!empty($evaluation)){ $champs['evaluation'] = $evaluation; }
		if(!empty($datePublication)){ $champs['date_publication'] = $datePublication; }
		if(!empty($access)){ $champs['access'] = $access; }
		if(!empty($groupe_autorise)){ $champs['groupe_autorise'] = $groupe_autorise; }
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		$champs['modificateur'] = $_SESSION['id_personne'];
		
		$conditions['id_document'] = $id_document;
		
		// crée une nouvelle version avec le contenu
		$this->versionManager->insertVersion($id_document,$nom,$infoModif,$contenu,$_SESSION['id_personne'],'fr',$evaluation);
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'document',$champs,$conditions);
	}


	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime un document (Ne supprime pas les versions !!)
	 *
	 * @return -
	 * @param int id de la document à supprimer
	 */
	function deleteDocument ($id_document){
		
		$request = "DELETE FROM `".$this->tablePrefix."document` WHERE `id_document`='".$id_document."' ";
		return $this->connection->query($request);
	}

	
	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/
	
	/**
	 * Retourne les infos sur TOUS les documents dont la date de publication est dans le passé
	 *  => attention ne retourne pas de contenu en provenance des versions.
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param string asc ou desc pour définir l'ordre de tri
	 */
	function getDocumentsByPublicationDate($orderBy = 'asc'){
		
		$query = "select * from ".$this->tablePrefix."document where date_publication < NOW() order by date_publication ".$orderBy;
		$result = $this->connection->query($query);
		return $this->connection->getAssocArrays($result);
	}
	
	/**
	 * Retourne les id de TOUS les documents dont la date de publication est dans le passé, et trié par date de publication
	 * 
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param string asc ou desc pour définir l'ordre de tri
	 */
	function getDocumentsIdByPublicationDate($orderBy = 'asc'){
		
		$query = "select id_document from ".$this->tablePrefix."document where date_publication < NOW() order by date_publication ".$orderBy;
		$result = $this->connection->query($query);
		return $this->connection->getRowArrays($result);
	}
	
	/**
	 * Retourne le type d'accès qui est utilisé par la gestion des droit d'accès. (2 = restriction, 1 = exclusivité)
	 *
	 * @return int le type d'accès
	 * @param int l'id du document
	 */
	function getAccessType($id_document){
		$clauses['id_document'] = $id_document;
		$champs = array('access');

		// select(table,clauses,fields, order by)
		$result = $this->connection->select($this->tablePrefix."document",$clauses,$champs);
		$tab = $this->connection->getRowArrays($result);
		if(!empty($tab)){
			return $tab[0]; // retourne un vrai int et pas un tableau associatif
		}else{
			return '';
		}
	}
	
	/**
	 * Retourne la liste des groupes qui ont le droit d'accèder à la ressource
	 *
	 * @return array liste des id des groupes qui ont accès
	 * @param int l'id du document
	 */
	function getGroupeAutorise($id_document){
		$clauses['id_document'] = $id_document;
		$champs = array('groupe_autorise');

		// select(table,clauses,fields, order by)
		$result = $this->connection->select($this->tablePrefix."document",$clauses,$champs);
		$tab = $this->connection->getRowArrays($result);
		if(!empty($tab)){
			$listeVirgule = $tab[0]; // retourne un vrai string et pas un tableau associatif
		}else{
			$listeVirgule = '';
		}
		
		// retourne un tableau avec les groupes qui sont séparés par des , dans le champ groupe_autorise de la table document.
		if (!empty($listeVirgule)) {
			return explode(",", trim($listeVirgule,",")); // transforme la chaine séparée par des , en tableau. Au passage supprime les , surnuméraires en début et fin de chaine
		}else{
			return array();
		}
	}
	
	/**
	 * Retourne retourne un tableau contenant les notes de similarités
	 * pour chaque document par rapport à celui dont l'id est fourni.
	 * 
	 * La similarité est calculée par rapport au titre du document 
	 *
	 * @return array liste des id triés des groupes similaire
	 * @param int $id_document l'id du document pour lequel on veut avoir la liste des articles similaire
	 * @param int $limit, le nombre maximum de document similaire que l'on veut recevoir.
	 */
	function getSimilarDocumentsByTitle($id_document, $limit='5'){
		
		echo "<br />yop";
		
		// va chercher le titre du document demandé
		$currentDoc = $this->getDocument($id_document);
		$currentDocTitle = $currentDoc['nom'];
		
		echo "<br />pour ",$currentDocTitle;
		
		// va chercher tous les documents.
		$allDocs = $this->getDocuments();
		
		// tableau stockant les similarités pour chaque id de doc
		$similarities = array();
		
		foreach ($allDocs as $key => $aDoc) {
			$docTitle = $aDoc['nom'];
			$similarite = similar_text($currentDocTitle, $docTitle, $pourCent);
			$similarities[$aDoc['nom']] = $pourCent;
		}
		
		arsort($similarities);
		print_r($similarities);
	}
	
	/**
	 * Retourne retourne un tableau contenant les notes de similarités
	 * pour chaque document par rapport à celui dont l'id est fourni.
	 * 
	 * La similarité est calculée par rapport aux tags liés au document
	 *
	 * @return array liste des id triés des groupes similaire
	 * @param int $id_document l'id du document pour lequel on veut avoir la liste des articles similaire
	 * @param int $limit, le nombre maximum de document similaire que l'on veut recevoir.
	 */
	function getSimilarDocumentsByTags($id_document, $limit='5'){
		
		echo "<br />yop2";
		
		// va chercher le titre du document demandé
		$currentDoc = $this->getDocument($id_document);
		$currentDocTitle = $currentDoc['nom'];
		echo "<br />pour ",$currentDocTitle;
		
		$currentDocumentTags = array_keys($this->groupeManager->getMotCleElement($id_document,'document'));
		$currentDocumentTagsString = implode($currentDocumentTags);
			
			// va chercher tous les documents.
			// todo... c'est overkill... on a besoin que des id... à faire une fonction dédiée.
			$allDocs = $this->getDocuments();
			
			// tableau stockant les similarités pour chaque id de doc
			$similarities = array();
			
			foreach ($allDocs as $key => $aDoc) {
				$docTitle = $aDoc['nom'];
				$docTags = array_keys($this->groupeManager->getMotCleElement($aDoc['id_document'],'document'));
				$docTagsString = implode($docTags);
				$similarite = similar_text($docTagsString, $currentDocumentTagsString, $pourCent);
				$similarities[$aDoc['nom']] = $pourCent;
			}
			arsort($similarities);
			print_r($similarities);
	}
	
} // documentManager
?>
