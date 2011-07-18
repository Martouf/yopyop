<?php
/*******************************************************************************************
 * Nom du fichier		: accueil.php
 * Date					: 18 juillet 2011
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Ce script gère la page d'accueil du site
 *******************************************************************************************
 * On appelle la page d'accueil... http://yopyop.ch/accueil/accueil.html ou tout simplement: http://yopyop.ch/accueil/
 *
 * La page d'accueil est composé de plusieurs boites qui peuvent avoir des contenus différents.
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
// - le nuage de mots-clé pour retrouver les objets


/////////////////////////////////
// le bloc d'identité du site est un bloc qui reprend le contenu d'un document. (le 2)
$document = $documentManager->getDocument('2');
$smarty->assign('contenuPresentation',stripcslashes($document['contenu']));


////////////////// Objets récents ///////////////

// on ne publie au public que les objets qui sont disponibles (etat=1) donc pas les objets encore en cours de création ou les objets privés
$filtreObjets = array('etat'=>'1'); // on ne veut que les objets de la personne dont on affiche le profile qui sont publié (etat=1)


$tousObjets = $objetManager->getObjets($filtreObjets,'date_creation desc limit 6'); // avec 'nom desc limit 1' => seulement 1 et filtré par nom inverses.. (bref un peu les possibilités de la chose)
	
$objets = array(); // tableau contenant des tableaux représentant la ressource
// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getObjets()... et array_intersect
foreach ($tousObjets as $key => $aObjet) {
	$objet = $aObjet;
		
	// obtients un tableau avec la liste des mots-clés attribué à l'objet
	$motCles = $groupeManager->getMotCleElement($aObjet['id_objet'],'objet');

	$listeMotCle= '';
	$premier = true;
	foreach ($motCles as $motCle => $occurence){
		if (!$premier) {
			$listeMotCle .=', ';
		}
	//	$listeMotCle .= $motCle; // juste la liste
		$listeMotCle .= '<em><a href="//'.$serveur.'/objets/'.$motCle.'/" title="voir les objets de la même catégorie...">'.$motCle.'</a></em>'; // liste avec lien html sur les objets liés par les tags
		$premier = false;
	}
	
	$objet['nomSimplifie'] = simplifieNom($aObjet['nom']);
	
	// fourni les infos sur l'image de présentation.
	$image = $photoManager->getPhoto($aObjet['id_image']);
	$image['lienVignette'] = $photoManager->getLienVignette($image['lien']);
	$image['lienMoyenne'] = $photoManager->getLienMoyenne($image['lien']);
	$objet['image'] = $image;
	
	// infos à propos du propriétaire
	$proprietaire = $personneManager->getPersonne($aObjet['createur']);
	$objet['proprietaire'] = $proprietaire;
	
	// fourni pour smarty une chaine de caractère avec la liste des tags (offuscé pour les nom de famille)
	$objet['listeTags'] = $listeMotCle;
	$objets[$aObjet['id_objet']] = $objet;		
}


// supprime les \
stripslashes_deep($objets);

// transmets les ressources à smarty
$smarty->assign('objets',$objets);


///////////////////////////////////
// montre les mots-clé
$motCleDocument = $groupeManager->getMotCle('objet');

$contenuNuage = "";
foreach ($motCleDocument as $tag => $occurrence) {
	$contenuNuage .= "<a href=\"//" . $serveur . "/objets/".$tag."/?summary\" class=\"nuage\" rel=\"tag\" style=\"font-size:".(100+floor(100*log($occurrence)))."%;\" >".$tag."</a> ";
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
