<?php
/*******************************************************************************************
 * Nom du fichier		: accueil.php
 * Date					: 19 décembre 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Ce script gère la page d'accueil du site
 *******************************************************************************************
 * On appelle la page d'accueil... http://yopyop.ch/accueil/accueil.html ou tout simplement: http://yopyop.ch/accueil/
 *
 * La page d'accueil est composé de plusieurs boites qui peuvent avoir des contenu différents.
 * Cette classe propose les méthodes pour obtenir les contenus de ces boites.
 *
 * Ex de contenu:
 * - Aperçu de 3 documents qui correspondent aux tags fourni
 * - Aperçu (champ description) des trois derniers documents publié... ou mis à jour.
 * - Aperçu de la dernière galerie de photo
 * - contenu d'un flux atom dont l'url est fournie
 * 
 */

/*
 *  Attention, il faut encore mettre en place la gestion des permissions !!!
 *
 */

// obtient les tags existants et les places dans le tableau $tags ou retourne une chaine vide si aucun tag n'est défini.
if (empty($ressourceTags)) {
	$tags = "";
}else{
	$tags = explode("/", trim($ressourceTags,"/")); // transforme la chaine séparée par des / en tableau. Au passage supprime les / surnuméraires en début et fin de chaine
}



// la page d'accueil est composée de plusieurs type de contenus différents. Il y a:
// - le bloc destiné à indiquer l'identité du site.
// - le bloc contenant le résumé des 3 derniers documents modifiés
// - le bloc contenant le résumé de quelques documents associés à des tags pré-déterminé. (ains on peut faire un bloc de news)
// - Le bloc contenant un nuage de tags qui permet de lancer des piste pour les visiteurs
//  .. et plus si afinité..

/////////////////////////////////
// le bloc d'identité du site est un bloc qui reprend le contenu d'un document. (le 2)
$document = $documentManager->getDocument('2');
$smarty->assign('contenuPresentation',stripcslashes($document['contenu']));

/////////////////////////////////
// va chercher le contenu de 3 documents qui correspondent au tags news
 // va chercher les id des documents qui correspondent au tag voulu
$lesDocumentsBillet = $groupeManager->getElementByTags(array('news'),'document');

// filtre les documents par date de publication
$tousLesDocuments = $documentManager->getDocumentsIdByPublicationDate();
$taggedElements = array_intersect($tousLesDocuments,$lesDocumentsBillet);

if (!empty($taggedElements)) {
	$documents = array(); // tableau contenant des tableaux représentant la ressource
	// le tri est effectué par id. Donc par ordre chronologique.
	foreach ($taggedElements as $key => $idDocument) {
		// va cherche le contenu de chaque document du groupe pour autant que le visiteur ait le droit de le voir
		$listeRestrictionsCourantes = $restrictionManager->getRestrictionsList($idDocument,'document', $_SESSION['id_personne']);
		// on crée un tableau avec les restrictions placées comme clés. Le tri sur des clés avec isset est 50x plus rapide que d'utiliser la focntion in_array
		$restrictionsCourantes = array_fill_keys($listeRestrictionsCourantes, ''); //Ne pas remplir de null car isset() retournerai FALSE même si la clé existe
		if (!isset($restrictionsCourantes['1'])) {  // équivalent à !in_array('1',$restrictionsCourantes) mais 50x plus rapide !
			$document = $documentManager->getDocument($idDocument);
			$document['nomSimplifie'] = simplifieNom($document['nom']);
			$documents[$idDocument] = $document;
		}
	}
	// maintenant on a à disposition tous les documents taggué news. On va en extraire juste le nombre que l'on veut.
	$documentsChoisis = array();

	for ($i=0; $i < 3; $i++) { 
		$documentsChoisis[] = array_pop($documents);
	}
	// variable qui va contenir le code html qui sera affiché dans la page.
	$contenuNews = '<h2>Derniers billets du Blog</h2>';
	foreach ($documentsChoisis as $key => $document) {
		$contenuNews .= "<div class=\"ficheNews\">";
		$contenuNews .= "<h3>".stripcslashes($document['nom'])."</h3>";
		$contenuNews .= '<p>'.stripcslashes($document['description']).'</p>';
	//	$contenuNews .= "<a class=\"liensNews\" href=\"/document/".$document['id_document']."-".$document['nomSimplifie'].".html\">En savoir plus...</a>";
		$contenuNews .= "<a class=\"liensNews\" href=\"/blog/news/\">En savoir plus...</a>";
		$contenuNews .= "</div>";
	}

	// assigner le contenu
	$smarty->assign('contenuNews', $contenuNews);
	
}else{
	$smarty->assign('contenuNews', "Pas de news");
}

// /////////////////////////////////
// // va chercher le contenu de 3 documents qui correspondent au tags voulu
//  // va chercher les id des documents qui correspondent au tag voulu
// $taggedElements = $groupeManager->getElementByTags(array('kitang'),'document');
// 
// if (!empty($taggedElements)) {
// 	$documents = array(); // tableau contenant des tableaux représentant la ressource
// 	// le tri est effectué par id. Donc par ordre chronologique.
// 	foreach ($taggedElements as $key => $idDocument) {
// 		// va cherche le contenu de chaque document du groupe pour autant que le visiteur ait le droit de le voir
// 		$listeRestrictionsCourantes = $restrictionManager->getRestrictionsList($idDocument,'document', $_SESSION['id_personne']);
// 		// on crée un tableau avec les restrictions placées comme clés. Le tri sur des clés avec isset est 50x plus rapide que d'utiliser la focntion in_array
// 		$restrictionsCourantes = array_fill_keys($listeRestrictionsCourantes, ''); //Ne pas remplir de null car isset() retournerai FALSE même si la clé existe
// 		if (!isset($restrictionsCourantes['1'])) {  // équivalent à !in_array('1',$restrictionsCourantes) mais 50x plus rapide !
// 			$document = $documentManager->getDocument($idDocument);
// 			$document['nomSimplifie'] = simplifieNom($document['nom']);
// 			$documents[$idDocument] = $document;
// 		}
// 	}
// 	// maintenant on a à disposition tous les documents taggué news. On va en extraire juste le nombre que l'on veut.
// 	$documentsChoisis = array();
// 
// 	for ($i=0; $i < 3; $i++) { 
// 		$documentsChoisis[] = array_pop($documents);
// 	}
// 	// variable qui va contenir le code html qui sera affiché dans la page.
// 	$contenuTheme = '<h2>Clan Kitang</h2>';
// 	foreach ($documentsChoisis as $key => $document) {
// 		$contenuTheme .= "<div class=\"ficheNews\">";
// 		$contenuTheme .= "<h3>".stripcslashes($document['nom'])."</h3>";
// 		$contenuTheme .= '<p>'.stripcslashes($document['description']).'</p>';
// 		$contenuTheme .= "<a class=\"liensNews\" href=\"/document/".$document['id_document']."-".$document['nomSimplifie'].".html\">En savoir plus...</a>";
// 		$contenuTheme .= "</div>";
// 	}
// 
// 	// assigner le contenu
// 	$smarty->assign('ContenuTheme', $contenuTheme);
// 	
// }else{
// 	$smarty->assign('ContenuTheme', "Pas de contenu");
// }

////////////////////////////////
// Va chercher le derniers statut ajouté
$dernierStatut = $statutManager->getStatuts(array(),"date_modification desc limit 1"); //va chercher le dernier

$contenuStatuts = "<span class=\"infoStatut\">";
foreach ($dernierStatut as $key => $statut) {
	$contenuStatuts .= stripcslashes($statut['nom']);
	$contenuStatuts .= "</span>";
	$contenuStatuts .= "<span class=\"dateStatut\"> - <a href=\"http://martouf.ch/statut/\" title=\"voir les anciens statuts\">";
	$contenuStatuts .= dateTime2Humain($statut['date_modification']);
	$contenuStatuts .= "</a></span>";
}
$smarty->assign('contenuStatuts', $contenuStatuts);



/////////////////////////////////
// Va chercher le contenu des 3 derniers documents moodifié
$derniersDocuments = $documentManager->getDocuments(array(),"date_modification desc limit 3"); //va chercher les 3 derniers

// obtient la liste des groupes dans lesquels se trouve l'utilisateur. (vu que l'utilisateur ne change pas, on le sort du foreach.. c'est tout ça de moins de requêtes)
$listeGroupeUtilisateur = $groupeManager->getGroupeUtilisateur($_SESSION['id_personne']);

$restrictionsCourantes = array();

$contenuHistorique = "<h2>Dernières modifications</h2>";
foreach ($derniersDocuments as $key => $document) {
	
	// obtient le type de gestion des droits d'accès. (restriction ou exclusivité) (0 ou 1)
	$typeAcces = $document['access'];

	// si l'accès est géré avec des exclusivités détermine les droits d'accès du visiteur
	if ($typeAcces=='1') {
		// obtient les groupes pour lesquels l'accès est autorisés pour ce document
		$listeGroupeAutorise = explode(",", trim($document['groupe_autorise'],",")); // transforme la chaine séparée par des , en tableau. Au passage supprime les , surnuméraires en début et fin de chaine
				
		// si l'intersection entre les 2 listes est nul c'est que l'utilisateur n'as pas d'autorisation. On lui place des restrictions sur tout !
		// si l'intersection donne un résultat. C'est que l'utilisateur a une exclusivité et donc accès à tout. On ne place pas de restriction !
		$toto = array_intersect($listeGroupeAutorise,$listeGroupeUtilisateur);

		if (count($toto)==0) {
			$restrictionsCourantes = array_fill_keys(array('1','2','3','4','5','6'), ''); // crée un tableau ave les clés 1,2,3,4,5,6 et une valeur nulle
		}
	}else{
		
		// va cherche le contenu de chaque document du groupe pour autant que le visiteur ait le droit de le voir
		$listeRestrictionsCourantes = $restrictionManager->getRestrictionsList($document['id_document'],'document', $_SESSION['id_personne']);
		// on crée un tableau avec les restrictions placées comme clés. Le tri sur des clés avec isset est 50x plus rapide que d'utiliser la focntion in_array
		$restrictionsCourantes = array_fill_keys($listeRestrictionsCourantes, ''); //Ne pas remplir de null car isset() retournerai FALSE même si la clé existe
	}
		
	if (!isset($restrictionsCourantes['1'])) {  // équivalent à !in_array('1',$restrictionsCourantes) mais 50x plus rapide !	
	
		$contenuHistorique .= "<div class=\"ficheNews\">";
		$contenuHistorique .= "<h3>".stripcslashes($document['nom'])."</h3>";
		$contenuHistorique .= '<p>'.stripcslashes($document['description']).'</p>';
		$contenuHistorique .= "<a class=\"liensNews\" href=\"/document/".$document['id_document']."-".simplifieNom($document['nom']).".html\">En savoir plus...</a>";
		$contenuHistorique .= "</div>";
	}
}
$smarty->assign('contenuHistorique', $contenuHistorique);

///////////////////////////////////
// montre les mots-clé
$motCleDocument = $groupeManager->getMotCle('document');

$contenuNuage = "";
foreach ($motCleDocument as $tag => $occurrence) {
	$contenuNuage .= "<a href=\"/document/".$tag."/?summary\" class=\"nuage\" style=\"font-size:".(100+floor(100*log($occurrence)))."%;\" >".$tag."</a> ";
}


$smarty->assign('contenuNuage', $contenuNuage);



////// se débrouille pour afficher qq chose dans le bon format...


// obtient le format de sortie. Si rien n'est défini, on choisi html
if (empty($ressourceOutput)) {
	$outputFormat = 'html';
}else{
	$outputFormat = $ressourceOutput;
}

// url du flux atom des news que l'on propose par défaut
$urlFlux = "http://".$serveur."/blog/news/flux.xml";

// quelques scripts utiles
$additionalHeader = "
	<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
	<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>

	<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
	<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
	<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/global.js\"></script>
	<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/document.js\"></script>
	<link rel=\"alternate\" type=\"application/atom+xml\" title=\"flux atom du blog\" href=\"".$urlFlux."\" />";	
	
$smarty->assign('additionalHeader',$additionalHeader);

// certains formats ne sont jamais inclus dans un thème
if ($outputFormat=='xml') {

	// calcule le nom de la même ressource, mais en page html
	$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
	$smarty->assign('alternateUrl',"http://".$alternateUrl);

	header('Content-Type: application/atom+xml; charset=UTF-8');
	$smarty->display("accueil".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
}else{

	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("accueil_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
	}else{
		// affiche la ressource inclue dans le template du thème index.tpl
		$smarty->assign('contenu',"accueil_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
		$smarty->display($theme."index.tpl");
	}
} // if format = xml



?>
