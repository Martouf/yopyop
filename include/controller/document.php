<?php
/*******************************************************************************************
 * Nom du fichier		: document.php
 * Date					: 12 novembre 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Permet de gérer des documents. Ce fichier génère des vues utilisée dans un wiki
 *******************************************************************************************
 * Interface qui permet d'afficher une document ou l'interface de modification d'une document
 * 
 * On utilise ce fichier via la réécriture d'url.
 * URL pour lire, ajouter, modifier ou supprimer une ressource. Les paramètres supplémentaires sont envoyés par POST:
 * http://yopyop.ch/document/28-momo.html  (get)
 * http://yopyop.ch/document/document.html?add
 * http://yopyop.ch/document/28-momo.html?update
 * http://yopyop.ch/document/28-momo.html?delete
 *
 * URL pour afficher des vues utiles aux actions demandées:
 * http://yopyop.ch/document/document.html?new   => fourni une interface vide de création qui peut être utilisée pour soumettre un nouvel élément à l'url add
 * http://yopyop.ch/document/28-momo.html?modify   => fourni une interface de modification avec les données de l'élément qui peut être utilisée pour soumettre les modifications à l'url update
 * http://yopyop.ch/document/?name  => (pas encor implémenté) fourni la liste des noms des ressources
 */

/*
 *  Attention, il faut encore terminer mettre en place la gestion des permissions !!!
 *
 */

// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idDocument = $ressourceId;

// Obtient les restrictions courantes pour le visiteur courant sur l'élément courant si une ressources précise est demandée... sinon détermine plus loin au moment d'afficher une liste de ressources
$restrictionsCourantes = array();
if (!empty($idDocument)) {
	
	// obtient le type de gestion des droits d'accès. (restriction ou exclusivité) (0 ou 1)
	$typeAcces = $documentManager->getAccessType($idDocument);
	
	// si l'accès est géré avec des exclusivités détermine les droits d'accès du visiteur
	if ($typeAcces=='1') {
		// obtient les groupes pour lesquels l'accès est autorisés pour ce document
		$listeGroupeAutorise = $documentManager->getGroupeAutorise($idDocument);
		
		// obtient la liste des groupes dans lesquels se trouve l'utilisateur
		$listeGroupeUtilisateur = $groupeManager->getGroupeUtilisateur($_SESSION['id_personne']);
		
		// si l'intersection entre les 2 listes est nul c'est que l'utilisateur n'as pas d'autorisation. On lui place des restrictions sur tout !
		// si l'intersection donne un résultat. C'est que l'utilisateur a une exclusivité et donc accès à tout. On ne place pas de restriction !
		$acces = array_intersect($listeGroupeAutorise,$listeGroupeUtilisateur);
		if (count($acces)==0) {
			$restrictionsCourantes = array_fill_keys(array('1','2','3','4','5','6'), ''); // crée un tableau ave les clés 1,2,3,4,5,6 et une valeur nulle
		}
	}else{
		//$listeRestrictionsCourantes = $restrictionManager->getRestrictionsList($idDocument,'document', $_SESSION['id_personne']);
		$listeRestrictionsCourantes = array(); // todo: supprimer le management des droits par restriction
		// on crée un tableau avec les restrictions placées comme clés. Le tri sur des clés avec isset est 50x plus rapide que d'utiliser la focntion in_array
		$restrictionsCourantes = array_fill_keys($listeRestrictionsCourantes, ''); //Ne pas remplir de null car isset() retournerai FALSE même si la clé existe
	}
}

// // test de documents similaires
// $documentManager->getSimilarDocumentsByTitle('8');
// $documentManager->getSimilarDocumentsByTags('8');

//print_r($listeRestrictionsCourantes);

// détermine la verbosité de l'affichage. Il est ainsi possible d'affiche le contenu complet, le résumé ou juste le nom des documents. C'est le template qui s'occupe de l'affichage
// maximum, normal, resume, nom
$verbosity = "normal";

// détermine l'action demandée (add, update, delete, par défaut on suppose que c'est get, donc on ne l'indique pas)
$action = "get";
if (isset($parametreUrl['add'])) {
	$action = 'add';
}
if (isset($parametreUrl['update'])) {
	$action = 'update';
}
if (isset($parametreUrl['delete'])) {
	$action = 'delete';
}
if (isset($parametreUrl['new'])) {
	$action = 'new';
}
if (isset($parametreUrl['modify'])) {
	$action = 'modify';
}
if (isset($parametreUrl['summary'])) {  // affiche uniquement le nom et le résumé du document
	$action = 'get';
	$verbosity = "resume";
}
if (isset($parametreUrl['name'])) { // affiche uniquement le nom du document
	$action = 'get';
	$verbosity = "nom";
}

// obtient une infos d'affichage pour savoir si l'on veux afficher ou non les méta données (nom de l'auteur et date de modif)
if (isset($parametreUrl['nometa'])) {
	$smarty->assign('metadonneeAutorise',false);
}else{
	$smarty->assign('metadonneeAutorise',true);
}


// obtient le format de sortie. Si rien n'est défini, on choisi html
if (empty($ressourceOutput)) {
	$outputFormat = 'html';
}else{
	$outputFormat = $ressourceOutput;
}

// obtient les tags existants et les places dans le tableau $tags ou retourne une chaine vide si aucun tag n'est défini.
if (empty($ressourceTags)) {
	$tags = "";
}else{
	$tags = explode("/", trim($ressourceTags,"/")); // transforme la chaine séparée par des / en tableau. Au passage supprime les / surnuméraires en début et fin de chaine
}

// va chercher les tags qui sont liés à l'objet courant !
$motsClesElement = $groupeManager->getMotCleElement($idDocument,'document'); // il y a certainement moyen d'optimiser tout ça... on a pas besoin d'avoir le nombre d'oocurence de l'utilisation du mot clé !! Peut être mettre une autre fonction !!

// transforme un tableau avec le mot clé et ses occurences en liste séparée par des virgules
$tagsVirgules = implode(',',array_keys($motsClesElement));
$smarty->assign('tags',$tagsVirgules);

// on défini ensuite les différentes actions possible.
// Pour l'action get, il y a plusieurs formats de sortie possibles. Le template est choisi en conséqunce. Pour le format pdf, le choix est fait en amont par index.php qui va fournir à princeXML le fichier html correspondant.

////////////////
////  GET
///////////////

if ($action=='get') {	
	
	// initialisation de la variable permmetant de mémoriser le droit de modification
	$droitModification = false;
	
	// il y a 2 cas possibles qui peuvent être demandés. Une ressource unique bien précise, ou un groupe de ressources.
	
	// une ressource unique
	if (!empty($idDocument)) {

		// pour modifier le document, il faut être le créateur de celui-ci ou un admin.
		$documentAModifier = $documentManager->getDocument($idDocument);
		$createur = $documentAModifier['createur'];
		if ($_SESSION['id_personne']== $createur) {
			$droitModification = true;
		}

		// si le visiteur est admin
		$visiteur = $personneManager->getPersonne($_SESSION['id_personne']);
		if ($visiteur['rang']=='1') {
			$droitModification = true;
		}
		
		
		// si l'utilisateur a le droit de lire cette ressource.
		if (!isset($restrictionsCourantes['1'])) {  // équivalent à !in_array('1',$restrictionsCourantes) mais plus rapide !

			// va chercher les infos sur la ressource demandée
			$document = $documentManager->getDocument($idDocument);
			$document['nomSimplifie'] = simplifieNom($document['nom']);  // remplace $photoManager->simplifieNomFichier($document['nom']);
			$document['contenu'] = parseEmail($document['contenu']); // protège les adresses e-mail avec un javascript
			$document['dateModification'] = dateTime2Humain($document['date_modification']);
			$document['dateCreation'] = dateTime2Humain($document['date_creation']);
			$document['pseudoCreateur'] = $personneManager->getPseudo($document['createur']);
			$document['tags'] = $groupeManager->getMotCleElement($idDocument,'document');
			
			// supprime les \
			stripslashes_deep($document);
			
			// affichage de la ressource
			$smarty->assign('document',$document);	
			
			// si l'utilisateur a le droit de modification on lui fourni le double click
			if ($droitModification) {
				$smarty->assign('utilisateurConnu',true);
			}else{
				$smarty->assign('utilisateurConnu',false);
			}

			
			$additionalHeader = "
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/global.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/document.js\"></script>";	
			$smarty->assign('additionalHeader',$additionalHeader);
			
			
			// si aucune restriction sur les commentaires n'existe..
			// affiche les commentaires courants et propose une interface pour en ajouter
			if (!isset($restrictionsCourantes['4'])) {
			
				// va chercher les commentaires qui sont associés à la ressource
				$tousCommentaires = $commentaireManager->getCommentaireElement($idDocument,'document');
				$commentaires = array();
				foreach ($tousCommentaires as $key => $aCommentaire) {
					$commentaires[$key] = $aCommentaire;
					$commentaires[$key]['description'] = nl2br($aCommentaire['description']);  // mise en forme basique des commentaire avec des retours chariots
					$commentaires[$key]['dateCreation'] = dateTime2Humain($aCommentaire['date_creation']);
					$commentaires[$key]['auteur'] = $personneManager->getPseudo($aCommentaire['id_auteur']); // pseudo de l'auteur plutôt que id
					$commentaires[$key]['gravatar'] = md5($aCommentaire['mail']);
				}
			
				// supprime les \  et transmet l'affichage à smarty
				stripslashes_deep($commentaires);
				$smarty->assign('commentaires',$commentaires);
			
				// info sur l'utilisateur qui va poster un commentaire
				$smarty->assign('idAuteurCommentaire',$_SESSION['id_personne']);
				$smarty->assign('pseudoUtilisateur',@$_SESSION['pseudo']);
			
				// on autorise le template à afficher les commentaires
				$smarty->assign('commentaireAutorise',true);
			}else{
				// on autorise pas les commentaires
				$smarty->assign('commentaireAutorise',false);
			}

			// certains formats ne sont jamais inclus dans un thème
			if ($outputFormat=='xml'|$outputFormat=='php') {
				
				// ajoute une entrée dans l'historique
				$historiqueManager->insertHistorique();
				$historiqueManager->purge(); // efface l'historique plus vieux qu'une semaine ou que la date fournie (datetime mysql)
				
				// calcule le nom de la même ressource, mais en page html
				$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
				$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
				header('Content-Type: application/atom+xml; charset=UTF-8');
				$smarty->display("document_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
			
				// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
				if ($theme=="no") {
					$smarty->display("document_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
				}else{
					// affiche la ressource inclue dans le template du thème index.tpl
					$smarty->assign('contenu',"document_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
					$smarty->display($theme."index.tpl");
				}
			} // if format = xml
		
		}// restrictions de lecture
	
	// un groupe de ressources
	}else{
		// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
		//http://yopyop.ch/document/    => va afficher la liste de toutes les documents.
		if (empty($tags)) {
			$tousDocuments = $documentManager->getDocumentsByPublicationDate();  // il s'agit de tous les documents dont la date de publication est dans le passé... mais pas du contenu du document... on ne pioche pas dans la table version. Le contenu peut être trop énorme.
		
			// obtient la liste des groupes dans lesquels se trouve l'utilisateur. (vu que l'utilisateur ne change pas, on le sort du foreach.. c'est tout ça de moins de requêtes)
			$listeGroupeUtilisateur = $groupeManager->getGroupeUtilisateur($_SESSION['id_personne']);
		
			$documents = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getDocuments()... et array_intersect
			foreach ($tousDocuments as $key => $aDocument) {
				$document = $aDocument;
				
				// obtient le type de gestion des droits d'accès. (restriction ou exclusivité) (0 ou 1)
				$typeAcces = $aDocument['access'];

				// si l'accès est géré avec des exclusivités détermine les droits d'accès du visiteur
				if ($typeAcces=='1') {
					// obtient les groupes pour lesquels l'accès est autorisés pour ce document
					$listeGroupeAutorise = explode(",", trim($aDocument['groupe_autorise'],",")); // transforme la chaine séparée par des , en tableau. Au passage supprime les , surnuméraires en début et fin de chaine

					// si l'intersection entre les 2 listes est nul c'est que l'utilisateur n'as pas d'autorisation. On lui place des restrictions sur tout !
					// si l'intersection donne un résultat. C'est que l'utilisateur a une exclusivité et donc accès à tout. On ne place pas de restriction !
					$acces = array_intersect($listeGroupeAutorise,$listeGroupeUtilisateur);
					if (count($acces)==0) {
						$restrictionsCourantes = array_fill_keys(array('1','2','3','4','5','6'), ''); // crée un tableau ave les clés 1,2,3,4,5,6 et une valeur nulle
					}
				}else{
					// va cherche le contenu de chaque document du groupe pour autant que le visiteur ait le droit de le voir
					//$listeRestrictionsCourantes = $restrictionManager->getRestrictionsList($aDocument['id_document'],'document', $_SESSION['id_personne']);
					$listeRestrictionsCourantes = array(); // todo: supprimer le management des droits par restriction
					// on crée un tableau avec les restrictions placées comme clés. Le tri sur des clés avec isset est 50x plus rapide que d'utiliser la focntion in_array
					$restrictionsCourantes = array_fill_keys($listeRestrictionsCourantes, ''); //Ne pas remplir de null car isset() retournerai FALSE même si la clé existe
				}
				
				if (!isset($restrictionsCourantes['1'])) {  // équivalent à !in_array('1',$restrictionsCourantes) mais 50x plus rapide !
					$document['nomSimplifie'] = simplifieNom($aDocument['nom']);
					$document['dateModification'] = dateTime2Humain($aDocument['date_modification']);
					$document['tags'] = $groupeManager->getMotCleElement($idDocument,'document');
					$documents[$aDocument['id_document']] = $document;
				}
			}
			
			// demande de n'afficher que le résumé des documents.
			$verbosity = "resume";
		
		}else{
		
			 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
			$taggedElements = $groupeManager->getElementByTags($tags,'document');
		
			$documents = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getDocuments()... et array_intersect
			foreach ($taggedElements as $key => $idDocument) {
				
				// obtient le type de gestion des droits d'accès. (restriction ou exclusivité) (0 ou 1)
				$typeAcces = $documentManager->getAccessType($idDocument);

				// si l'accès est géré avec des exclusivités détermine les droits d'accès du visiteur
				if ($typeAcces=='1') {
					// obtient les groupes pour lesquels l'accès est autorisés pour ce document
					$listeGroupeAutorise = $documentManager->getGroupeAutorise($idDocument);

					// obtient la liste des groupes dans lesquels se trouve l'utilisateur
					$listeGroupeUtilisateur = $groupeManager->getGroupeUtilisateur($_SESSION['id_personne']);

					// si l'intersection entre les 2 listes est nul c'est que l'utilisateur n'as pas d'autorisation. On lui place des restrictions sur tout !
					// si l'intersection donne un résultat. C'est que l'utilisateur a une exclusivité et donc accès à tout. On ne place pas de restriction !
					$acces = array_intersect($listeGroupeAutorise,$listeGroupeUtilisateur);
					if (count($acces)==0) {
						$restrictionsCourantes = array_fill_keys(array('1','2','3','4','5','6'), ''); // crée un tableau ave les clés 1,2,3,4,5,6 et une valeur nulle
					}
				}else{
		
					// va cherche le contenu de chaque document du groupe pour autant que le visiteur ait le droit de le voir
					//$listeRestrictionsCourantes = $restrictionManager->getRestrictionsList($idDocument,'document', $_SESSION['id_personne']);
					$listeRestrictionsCourantes = array(); // todo: supprimer le management des droits par restriction
					// on crée un tableau avec les restrictions placées comme clés. Le tri sur des clés avec isset est 50x plus rapide que d'utiliser la focntion in_array
					$restrictionsCourantes = array_fill_keys($listeRestrictionsCourantes, ''); //Ne pas remplir de null car isset() retournerai FALSE même si la clé existe
				}
				if (!isset($restrictionsCourantes['1'])) {  // équivalent à !in_array('1',$restrictionsCourantes) mais 50x plus rapide !
					$document = $documentManager->getDocument($idDocument);
					$document['nomSimplifie'] = simplifieNom($document['nom']);
					$document['dateModification'] = dateTime2Humain($document['date_modification']);
					$documents[$idDocument] = $document;
				}
			}
		} // if $tags
		
		// supprime les \
		stripslashes_deep($documents);
		
		// transmets les ressources à smarty
		$smarty->assign('documents',$documents);
		
		// si l'utilisateur a le droit d'édition on lui fourni le double click
		if ($droitModification) {
			$smarty->assign('utilisateurConnu',true);
		}else{
			$smarty->assign('utilisateurConnu',false);
		}

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/global.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/document.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		// fourni à smarty le niveau de détail à afficher
		$smarty->assign("verbosity",$verbosity);

		if ($outputFormat=='xml' | $outputFormat=='php') {  // le format de sortie php ainsi que le tpl associé est juste là pour faire des url du genre: http://yopyop.ch/document/lapin/toto.php?baba=fasdkfndsfnj. Ce type d'url est accepét par netnewswire et les navigateurs web alors que si l'extension est .xml... ça ne va pas, les paramètres perturbent tout !
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("document_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("document_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"document_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}	
		} // if output = xml

	} //if groupe de ressource
	
////////////////
////  ADD
///////////////
	
}elseif ($action=='add') {
	// obtient les données
	if(isset($_POST['nom'])){
		$nom = $_POST['nom'];
	}else{
		$nom ='';
	}
	if(isset($_POST['description'])){
		$description = $_POST['description'];
	}else{
		$description ='';
	}
	if(isset($_POST['contenu'])){
		$contenu = $_POST['contenu'];
	}else{
		$contenu ='';
	}
	if(isset($_POST['infomodif'])){
		$infomodif = $_POST['infomodif'];
	}else{
		$infomodif ='';
	}
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='0';
	}
	if(isset($_POST['date_publication'])){
		$date_publication = $_POST['date_publication'];
	}else{
		$date_publication ='';
	}
	if(isset($_POST['access'])){
		$access = $_POST['access'];
	}else{
		$access ='';
	}
	if(isset($_POST['groupe_autorise'])){
		$groupe_autorise = $_POST['groupe_autorise'];
	}else{
		$groupe_autorise ='';
	}

	// on décrête que por ajouter un document il faut être un utilisateur connu
	if ($_SESSION['id_personne'] != '1') {
		// ajoute la nouvelle ressource
		$idDocument = $documentManager->insertDocument($nom,$description,$contenu,$evaluation,$infomodif,$date_publication,$access,$groupe_autorise);

		echo $idDocument; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax
	}

////////////////
////  UPDATE
///////////////

}elseif ($action=='update') {
	// obtient les données
	if(isset($_POST['nom'])){
		$nom = $_POST['nom'];
	}else{
		$nom ='';
	}
	if(isset($_POST['description'])){
		$description = $_POST['description'];
	}else{
		$description ='';
	}
	if(isset($_POST['contenu'])){
		$contenu = $_POST['contenu'];
	}else{
		$contenu ='';
	}
	if(isset($_POST['infoModif'])){
		$infoModif = $_POST['infoModif'];
	}else{
		$infoModif ='';
	}
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='0';
	}
	if(isset($_POST['date_publication'])){
		$date_publication = $_POST['date_publication'];
	}else{
		$date_publication ='';
	}
	if(isset($_POST['access'])){
		$access = $_POST['access'];
	}else{
		$access ='';
	}
	if(isset($_POST['groupe_autorise'])){
		$groupe_autorise = $_POST['groupe_autorise'];
	}else{
		$groupe_autorise ='';
	}
	
	$droitModification = false;
	
	// pour modifier le document, il faut être le créateur de celui-ci ou un admin.
	$documentAModifier = $documentManager->getDocument($idDocument);
	$createur = $documentAModifier['createur'];
	if ($_SESSION['id_personne']== $createur) {
		$droitModification = true;
	}
	
	// si le visiteur est admin
	$visiteur = $personneManager->getPersonne($_SESSION['id_personne']);
	if ($visiteur['rang']=='1') {
		$droitModification = true;
	}
	
	// si l'utilisateur à le droit de modifier le document
	if ($droitModification) {
	
		// si aucune restriction en écriture éxiste
		if (!isset($restrictionsCourantes['2'])) {
	
			// fait la mise à jour
			$documentManager->updateDocument($idDocument,$nom,$description,$contenu,$evaluation,$infoModif,$date_publication,$access,$groupe_autorise);
		}
	}

////////////////
////  DELETE
///////////////

}elseif ($action=='delete') {
	
	// on décrète que por supprimer un document il faut être admin ou le créateur du document
	$droitModification = false;
	
	// pour modifier le document, il faut être le créateur de celui-ci ou un admin.
	$documentAModifier = $documentManager->getDocument($idDocument);
	$createur = $documentAModifier['createur'];
	if ($_SESSION['id_personne']== $createur) {
		$droitModification = true;
	}
	
	// si le visiteur est admin
	$visiteur = $personneManager->getPersonne($_SESSION['id_personne']);
	if ($visiteur['rang']=='1') {
		$droitModification = true;
	}
	if ($droitModification) {
		
		// si aucune restriction en écriture éxiste
		if (!isset($restrictionsCourantes['2'])) {
			$documentManager->deleteDocument($idDocument);
		}
	}
	
////////////////
////  NEW
///////////////
}elseif ($action=='new') {
	
	// on décrète que por ajouter un document il faut être un utilisateur connu
	if ($_SESSION['id_personne'] != '1') {
	
		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/tiny_mce/tiny_mce.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/document.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/global.js\"></script>
			<script type=\"text/javascript\">startRichEditor();</script>";	
		$smarty->assign('additionalHeader',$additionalHeader);
		
		// liste des groupes qui contiennent des personnes
		$listeGroupeUtilisateur = $groupeManager->getMotCleParTypeElement('personne');
		$smarty->assign('listeGroupeUtilisateur',$listeGroupeUtilisateur);
	
		// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
		if ($theme=="no") {
			$smarty->display("document_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
		}else{
			// affiche le formulaire de modification inclu dans le template du thème index.tpl
			$smarty->assign('contenu',"document_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
			$smarty->display($theme."index.tpl");
		}
	} // restriction en écriture

////////////////
////  MODIFY
///////////////
}elseif ($action=='modify') {
	
	$droitModification = false;
	
	// pour modifier le document, il faut être le créateur de celui-ci ou un admin.
	$documentAModifier = $documentManager->getDocument($idDocument);
	$createur = $documentAModifier['createur'];
	if ($_SESSION['id_personne']== $createur) {
		$droitModification = true;
	}
	
	// si le visiteur est admin
	$visiteur = $personneManager->getPersonne($_SESSION['id_personne']);
	if ($visiteur['rang']=='1') {
		$droitModification = true;
	}
	
	
	// on décrète que por modifier un document il faut être admin ou le créateur du document
	if ($droitModification) {
	
		// si aucune restriction en écriture n'éxiste
		if (!isset($restrictionsCourantes['2'])) { 
	
			// va chercher les infos sur la ressource demandée
			$document = $documentManager->getDocument($idDocument);
			$document['nomSimplifie'] = simplifieNom($document['nom']);
			$document['dateModification'] = dateTime2Humain($document['date_modification']);
	
			// supprime les \
			stripslashes_deep($document);
	
			// passe les données de la document à l'affichage
			$smarty->assign('document',$document);
		
			// liste des groupes qui contiennent des personnes
			$listeGroupeUtilisateur = $groupeManager->getMotCleParTypeElement('personne');
			$smarty->assign('listeGroupeUtilisateur',$listeGroupeUtilisateur);
	
			// quelques scripts utiles
			$additionalHeader = "
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/tiny_mce/tiny_mce.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/document.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/global.js\"></script>
				<script type=\"text/javascript\">startRichEditor();</script>";	
			$smarty->assign('additionalHeader',$additionalHeader);
	
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("document_modify_".LANG.".tpl"); // affichage de l'interface de modification de la ressource
			}else{
				// affiche le formulaire de modification inclu dans le template du thème index.tpl
				$smarty->assign('contenu',"document_modify_".LANG.".tpl");
				$smarty->display($theme."index.tpl");
			}
		} // restriction en écriture
	} // utilisateur inconnu
}
?>
