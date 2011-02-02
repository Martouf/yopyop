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
	 *  Retourne retourne un tableau contenant les id des 5 documents les plus similaires
	 * 
	 * La similarité est calculée par rapport au titre du document 
	 *
	 * @return array liste des id triés des groupes similaire
	 * @param int $id_document l'id du document pour lequel on veut avoir la liste des articles similaire
	 * @param int $limit, le nombre maximum de document similaire que l'on veut recevoir.
	 */
	function getSimilarDocumentsByTitle($id_document, $limit='5'){

		// va chercher le titre du document demandé
		$currentDoc = $this->getDocument($id_document);
		$currentDocTitle = $currentDoc['nom'];
		
		// va chercher tous les documents.
		$allDocs = $this->getDocuments();
		
		// tableau stockant les similarités pour chaque id de doc
		$similarities = array();
		
		foreach ($allDocs as $key => $aDoc) {
			$docTitle = $aDoc['nom'];
			$similarite = similar_text($currentDocTitle, $docTitle, $pourCent);
			if ($aDoc['nom']!=$currentDocTitle) { // on exclu le document lui même !
				$similarities[$aDoc['id_document']] = $pourCent;
			}
		}
		
		asort($similarities); // trie le tableau: id_document => pourcent avec les 100 pourcent en bas
		$similarDocumentsId = array_keys($similarities);
		
		$similaritiesTruncate = array();
		
		for ($i=0; $i < $limit; $i++) { 
			$similaritiesTruncate[] = array_pop($similarDocumentsId);
		}
		return $similaritiesTruncate;
	}
	
	/**
	 * Retourne retourne un tableau contenant les id des 5 documents les plus similaires
	 * 
	 * La similarité est calculée par rapport aux tags liés au document
	 *
	 * @return array liste des id triés des groupes similaire
	 * @param int $id_document l'id du document pour lequel on veut avoir la liste des articles similaire
	 * @param int $limit, le nombre maximum de document similaire que l'on veut recevoir.
	 */
	function getSimilarDocumentsByTags($id_document, $limit='5'){
		
		// va chercher le titre du document demandé
	//	$currentDoc = $this->getDocument($id_document);
		
		$currentDocumentTags = array_keys($this->groupeManager->getMotCleElement($id_document,'document'));
		$currentDocumentTagsString = implode($currentDocumentTags);
			
		// va chercher tous les documents.
		// todo... c'est overkill... on a besoin que des id... à faire une fonction dédiée.
		$allDocs = $this->getDocuments();
		
		// tableau stockant les similarités pour chaque id de doc
		$similarities = array();
		
		foreach ($allDocs as $key => $aDoc) {
			$docTags = array_keys($this->groupeManager->getMotCleElement($aDoc['id_document'],'document'));
			$docTagsString = implode($docTags);
			$similarite = similar_text($docTagsString, $currentDocumentTagsString, $pourCent);
			if ($aDoc['id_document']!=$id_document) {
				$similarities[$aDoc['id_document']] = $pourCent;
			}
		}
		
		asort($similarities); // trie le tableau: id_document => pourcent avec les 100 pourcent en bas
		$similarDocumentsId = array_keys($similarities);
		
		$similaritiesTruncate = array();
		
		for ($i=0; $i < $limit; $i++) { 
			$similaritiesTruncate[] = array_pop($similarDocumentsId);
		}
		return $similaritiesTruncate;
	}
	
	/**
	 * Retourne un tableau de tableau des articles les plus similaires
	 *
	 * @return array array liste des id triés des groupes similaire
	 * @param int $limit, le nombre maximum de documents similaires que l'on veut recevoir.
	 * @param string $method, la méthode de calcul a utiliser. Comparaison du titre "title" ou des tags "tags".
	 */
	function getSimilarDocuments($limit='5',$method = 'title'){
		
		$allDocSimilarities = array();
		
		$allDocs = $this->getDocuments();
		
		foreach ($allDocs as $key => $aDoc) {
			$allDocs2 = $allDocs;
						
			if ($method == 'title') {
				
				// va chercher le titre du document demandé
				$currentDoc = $this->getDocument($aDoc['id_document']);
				$currentDocTitle = $currentDoc['nom'];
	
				// tableau stockant les similarités pour chaque id de doc
				$similarities = array();
	
				foreach ($allDocs2 as $key => $aDoc2) {
					$docTitle = $aDoc2['nom'];
					$similarite = similar_text($currentDocTitle, $docTitle, $pourCent);
				//	echo "<br />comparaison de : <b>",$aDoc2['nom'],"</b> avec <b>",$currentDocTitle, "</b> => ",$pourCent;
					if ($aDoc2['nom']!=$currentDocTitle) { // on exclu le document lui même !
						$similarities[$aDoc2['id_document']] = $pourCent;
					}
				}
					
				asort($similarities); // trie le tableau: id_document => pourcent avec les 100 pourcent en bas
				$similarDocumentsId = array_keys($similarities);
				$similaritiesTruncate = array();
	
				for ($i=0; $i < $limit; $i++) { 
					$similaritiesTruncate[] = array_pop($similarDocumentsId);
				}
				$allDocSimilarities[$aDoc['id_document']] = $similaritiesTruncate;
			}else{  // if method = tags
				
				$currentDocumentTags = array_keys($this->groupeManager->getMotCleElement($aDoc['id_document'],'document'));
				asort($currentDocumentTags); // trie les tags pour comparer sur la même base
				$currentDocumentTagsString = implode($currentDocumentTags);

				// tableau stockant les similarités pour chaque id de doc
				$similarities = array();

				foreach ($allDocs2 as $key => $aDoc2) {
					$docTags = array_keys($this->groupeManager->getMotCleElement($aDoc2['id_document'],'document'));
					asort($docTags);
					$docTagsString = implode($docTags);
					$similarite = similar_text($docTagsString, $currentDocumentTagsString, $pourCent);
				//	echo "<br />comparaison de : <b>",$docTagsString,"</b> avec <b>",$currentDocumentTagsString, "</b> => ",$pourCent;
					if ($aDoc2['id_document']!=$aDoc['id_document']) {
						$similarities[$aDoc2['id_document']] = $pourCent;
					}
				}

				asort($similarities); // trie le tableau: id_document => pourcent avec les 100 pourcent en bas
				$similarDocumentsId = array_keys($similarities);

				$similaritiesTruncate = array();

				for ($i=0; $i < $limit; $i++) { 
					$similaritiesTruncate[] = array_pop($similarDocumentsId);
				}
				$allDocSimilarities[$aDoc['id_document']] = $similaritiesTruncate;
				
			} // title or tags
		} // foreach
		
		return $allDocSimilarities;
	}
	
	/**
	 * Retourne un string contenant de l'html qui affiche les articles similaires.
	 *
	 * @return array array liste des id triés des groupes similaire
	 * @param int $limit, le nombre maximum de documents similaires que l'on veut recevoir.
	 * @param string $method, la méthode de calcul a utiliser. Comparaison du titre "title" ou des tags "tags".
	 */
	function getStatsSimilarites($limit='5',$method = 'title'){
		
		$similarites = $this->getSimilarDocuments($limit, $method);

		$toutDocs = $this->getDocuments();
		$allDocs = array();
		foreach ($toutDocs as $key => $document) {
			$allDocs[$document['id_document']] = $document;
		}
		
		$html = '';

		foreach ($similarites as $key => $doc) {
			$html .= "<h2>".$allDocs[$key]['nom']."</h2>";
			$html .= "<ul>";
			foreach ($doc as $cle => $value) {
			//	$html .= "<li>".$allDocs[$value]['nom']."</li>";
				$html .= "<li>".$allDocs[$value]['nom']." - ".$allDocs[$value]['id_document']."</li>";
			}
			$html .= "</ul>";
		}
		return $html;
	}
	
	/**
	 * Va mettre à jour le champ similarité de la table document
	 * Attention cette opération peut prendre beaucoup de temps ~ 3min !
	 * Ne pas abuser.
	 *
	 * @return array array liste des id triés des groupes similaire
	 * @param int $limit, le nombre maximum de documents similaires que l'on veut recevoir.
	 * @param string $method, la méthode de calcul a utiliser. Comparaison du titre "title" ou des tags "tags".
	 */
	function updateSimilarite($limit='5',$method = 'title'){
		
		$similarites = $this->getSimilarDocuments($limit, $method);
	
		foreach ($similarites as $idCurrentDoc => $similarDocsTab) {
			$similarDocsString = implode(',',$similarDocsTab);
			
			// update tables
			$champs['similarite'] = $similarDocsString;
			$conditions['id_document'] = $idCurrentDoc;
			// $this->connection->update($this->tablePrefix.'document',$champs,$conditions);
			echo "<br />update table doc...",$idCurrentDoc," avec la chaine....",$similarDocsString;
		}
	}
	
	/**
	 * Retourne un string contenant de l'html qui affiche les articles similaires.
	 *
	 * @param string $documentsList la liste des id des documents similaires à afficher séparé par des , (Le contenu du champs "similarite" de la table document)
	 * @param string mode. Quelle genre de chose afficher. Liste ou resume
	 * @return string le code html pour affiche les documents similaires
	 */
	function getSimilarsDocsFromTable($documentsList,$mode='liste'){
		$idsTab = explode(',',$documentsList);
		
		$html = '';
		$html .= "<ul>";
		foreach ($idsTab as $key => $id_document) {
			// va chercher le document
			$currentDoc = $this->getDocument($id_document);
			stripslashes_deep($currentDoc); // supprime les \ d'échappement
			if ($mode == 'resume') {
				$html .= '<div class="recommandedDoc">';
				$html .= '<h3 class="recommandedDocTitle">';
				$html .= '<a href="//'.$_SERVER['SERVER_NAME'].'/blog/'.$currentDoc['id_document'].'-'.$currentDoc['nomSimplifie'].'.html">'.$currentDoc['description']; // todo: trouver pourquoi la variable $serveur n'est pas visible ici ?
				$html .= $currentDoc['nom'];
				$html .= '</a>';
				$html .= '</h3>';
				$html .= '<p>';
				$html .= '<a href="//'.$_SERVER['SERVER_NAME'].'/blog/'.$currentDoc['id_document'].'-'.$currentDoc['nomSimplifie'].'.html">'.$currentDoc['description'].'</a>'; // todo: trouver pourquoi la variable $serveur n'est pas visible ici ?
				$html .= '</p>';
				$html .= '</div>';
			}else{
				$html .= "<li>";
				$html .= '<a href="//'.$_SERVER['SERVER_NAME'].'/blog/'.$currentDoc['id_document'].'-'.$currentDoc['nomSimplifie'].'.html" title="'.$currentDoc['description'].'">'.$currentDoc['nom']; // todo: trouver pourquoi la variable $serveur n'est pas visible ici ?
				$html .= '</a>';
				$html .= "</li>";
			}
		}
		$html .= "</ul>";
		return $html;
	}
	
} // documentManager
?>
