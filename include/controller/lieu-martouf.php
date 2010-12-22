<?php
/*******************************************************************************************
 * Nom du fichier		: lieu.php
 * Date					: 30 janvier 2009
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@ecodev.ch
 * But de ce fichier	: Permet de gérer des lieux.
 *******************************************************************************************
 * Interface qui permet d'afficher un lieu ou l'interface de modification d'un lieu
 * 
 * On utilise ce fichier via la réécriture d'url.
 * URL pour lire, ajouter, modifier ou supprimer une ressource. Les paramètres supplémentaires sont envoyés par POST:
 * http://yopyop.ch/lieu/28-momo.html  (get)
 * http://yopyop.ch/lieu/lieu.html?add
 * http://yopyop.ch/lieu/28-momo.html?update
 * http://yopyop.ch/lieu/28-momo.html?delete
 *
 * URL pour afficher des vues utiles aux actions demandées:
 * http://yopyop.ch/lieu/lieu.html?new   => fourni une interface vide de création qui peut être utilisée pour soumettre un nouvel élément à l'url add
 * http://yopyop.ch/lieu/28-momo.html?modify   => fourni une interface de modification avec les données de l'élément qui peut être utilisée pour soumettre les modifications à l'url update
 */

/*
 *  Attention, il faut encore mettre en place la gestion des permissions !!!
 *
 */

// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idLieu = $ressourceId;

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
if (isset($parametreUrl['list'])) {
	$action = 'list';
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
$motsClesElement = $groupeManager->getMotCleElement($idLieu,'lieu'); // il y a certainement moyen d'optimiser tout ça... on a pas besoin d'avoir le nombre d'oocurence de l'utilisation du mot clé !! Peut être mettre une autre fonction !!

// transforme un tableau avec le mot clé et ses occurences en liste séparée par des virgules
$tagsVirgules = implode(',',array_keys($motsClesElement));
$smarty->assign('tags',$tagsVirgules);

// on défini ensuite les différentes actions possible.
// Pour l'action get, il y a plusieurs formats de sortie possibles. Le template est choisi en conséqunce. Pour le format pdf, le choix est fait en amont par index.php qui va fournir à princeXML le fichier html correspondant.

////////////////
////  GET
///////////////

if ($action=='get') {
	// il y a 2 cas possibles qui peuvent être demandés. Une ressource unique bien précise, ou un groupe de ressource.
	
	// une ressource unique
	if (!empty($idLieu)) {
		
		// va chercher les infos sur la ressource demandée
		$lieu = $lieuManager->getLieu($idLieu);
		
		// supprime les \
		stripslashes_deep($lieu);
		
		// affichage de la ressource
		$smarty->assign('lieu',$lieu);	

		// quelques scripts utiles
		$additionalHeader = "
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME']."/utile/css/datePicker.css\" media=\"screen\" />
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
			<script src=\"http://maps.google.com/maps?file=api&v=2.x&key=ABQIAAAAsyuA7bSIBFzUGUhfQJbrIRRSMAugEfcv_MMHwK_fk7DalYdq0xT3z4lmg7IJaljNWbgDs1DlyX9MgA\" type=\"text/javascript\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/wms236.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/lieu.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		// certains formats ne sont jamais inclu dans un thème
		if ($outputFormat=='kml') {
			header('Content-Type: application/vnd.google-earth.kml+xml');
			$smarty->display("lieu_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}elseif ($outputFormat=='xml') {
			
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("lieu_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}elseif ($outputFormat=='json') {

			// calcule le nom de la même ressource, mais au format json. (pour être inclu dans un combobox)
			header('Content-Type: application/json; charset=UTF-8');
			$smarty->display("lieu_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("lieu_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"lieu_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}
		} // if format = kml

	
	// un groupe de ressources
	}else{
		
		// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
		//http://yopyop.ch/lieu/    => va afficher la liste de toutes les lieux.
		if (empty($tags)) {
			$lieux = $lieuManager->getLieux();
		}else{
		
			 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
			$taggedElements = $groupeManager->getElementByTags($tags,'lieu');
		
			$lieux = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getLieux()... et array_intersect
			foreach ($taggedElements as $key => $idLieu) {
				$lieux[$idLieu] = $lieuManager->getLieu($idLieu);
			}
		} // if $tags
		
		// supprime les \
		stripslashes_deep($lieux);
		
		// transmets les ressources à smarty
		$smarty->assign('lieux',$lieux);

		// quelques scripts utiles
		$additionalHeader = "
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME']."/utile/css/datePicker.css\" media=\"screen\" />
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
			<script src=\"http://maps.google.com/maps?file=api&v=2.x&key=ABQIAAAAsyuA7bSIBFzUGUhfQJbrIRRSMAugEfcv_MMHwK_fk7DalYdq0xT3z4lmg7IJaljNWbgDs1DlyX9MgA\" type=\"text/javascript\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/wms236.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/lieu.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		if ($outputFormat=="kml") {
			header('Content-Type: application/vnd.google-earth.kml+xml');
			$smarty->display("lieu_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
			
		}elseif ($outputFormat=='xml') {
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("lieu_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
			
		}elseif ($outputFormat=='json') {
			
			if (isset($parametreUrl['combobox'])) {  // permet de préciser que l'on veur une sortie json adapté au combobox
				$smarty->assign('usage','combobox');
			}else{
				$smarty->assign('usage','normal');
			}
			
			$total = count($lieux);
			$smarty->assign('total',$total);
			// calcule le nom de la même ressource, mais au format json. (pour être inclu dans un combobox)
			header('Content-Type: application/json; charset=UTF-8');
			$smarty->display("lieu_multi_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("lieu_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"lieu_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}	
		} // if output = kml

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
	if(isset($_POST['categorie'])){
		$categorie = $_POST['categorie'];
	}else{
		$categorie ='';
	}
	if(isset($_POST['rue'])){
		$rue = $_POST['rue'];
	}else{
		$rue ='';
	}
	if(isset($_POST['npa'])){
		$npa = $_POST['npa'];
	}else{
		$npa ='';
	}
	if(isset($_POST['commune'])){
		$commune = $_POST['commune'];
	}else{
		$commune ='';
	}
	if(isset($_POST['pays'])){
		$pays = $_POST['pays'];
	}else{
		$pays ='';
	}
	if(isset($_POST['latitude'])){
		$latitude = $_POST['latitude'];
	}else{
		$latitude ='';
	}
	if(isset($_POST['longitude'])){
		$longitude = $_POST['longitude'];
	}else{
		$longitude ='';
	}
	if(isset($_POST['altitude'])){
		$altitude = $_POST['altitude'];
	}else{
		$altitude ='';
	}
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='0';
	}
	
	// ajoute la nouvelle ressource
	$idLieu = $lieuManager->insertLieu($nom,$description,$categorie,$rue,$npa,$commune,$pays,$latitude,$longitude,$altitude,$evaluation);
	
	echo $idLieu; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax

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
	if(isset($_POST['categorie'])){
		$categorie = $_POST['categorie'];
	}else{
		$categorie ='';
	}
	if(isset($_POST['rue'])){
		$rue = $_POST['rue'];
	}else{
		$rue ='';
	}
	if(isset($_POST['npa'])){
		$npa = $_POST['npa'];
	}else{
		$npa ='';
	}
	if(isset($_POST['commune'])){
		$commune = $_POST['commune'];
	}else{
		$commune ='';
	}
	if(isset($_POST['pays'])){
		$pays = $_POST['pays'];
	}else{
		$pays ='';
	}
	if(isset($_POST['latitude'])){
		$latitude = $_POST['latitude'];
	}else{
		$latitude ='';
	}
	if(isset($_POST['longitude'])){
		$longitude = $_POST['longitude'];
	}else{
		$longitude ='';
	}
	if(isset($_POST['altitude'])){
		$altitude = $_POST['altitude'];
	}else{
		$altitude ='';
	}
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='0';
	}
	
	// fait la mise à jour
	$lieuManager->updateLieu($idLieu,$nom,$description,$categorie,$rue,$npa,$commune,$pays,$latitude,$longitude,$altitude,$evaluation);

////////////////
////  DELETE
///////////////

}elseif ($action=='delete') {
	$lieuManager->deleteLieu($idLieu);
	
////////////////
////  NEW
///////////////
}elseif ($action=='new') {
	
	// quelques scripts utiles
	$additionalHeader = "
		<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME']."/utile/css/datePicker.css\" media=\"screen\" />
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
		<script src=\"http://maps.google.com/maps?file=api&v=2.x&key=ABQIAAAAsyuA7bSIBFzUGUhfQJbrIRRSMAugEfcv_MMHwK_fk7DalYdq0xT3z4lmg7IJaljNWbgDs1DlyX9MgA\" type=\"text/javascript\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/wms236.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/lieu.js\"></script>";	
	$smarty->assign('additionalHeader',$additionalHeader);
	
	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("lieu_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"lieu_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
		$smarty->display($theme."index.tpl");
	}

////////////////
////  MODIFY
///////////////
}elseif ($action=='modify') {
	// va chercher les infos sur la ressource demandée
	$lieu = $lieuManager->getLieu($idLieu);
	
	// supprime les \
	stripslashes_deep($lieu);
	
	// passe les données de la lieu à l'affichage
	$smarty->assign('lieu',$lieu);
	
	// quelques scripts utiles
	$additionalHeader = "
		<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME']."/utile/css/datePicker.css\" media=\"screen\" />
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
		<script src=\"http://maps.google.com/maps?file=api&v=2.x&key=ABQIAAAAsyuA7bSIBFzUGUhfQJbrIRRSMAugEfcv_MMHwK_fk7DalYdq0xT3z4lmg7IJaljNWbgDs1DlyX9MgA\" type=\"text/javascript\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/wms236.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/lieu.js\"></script>";	
	$smarty->assign('additionalHeader',$additionalHeader);
	
	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("lieu_modify_".LANG.".tpl"); // affichage de l'interface de modification de la ressource
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"lieu_modify_".LANG.".tpl");
		$smarty->display($theme."index.tpl");
	}
	
////////////////
////  LIST ... fourni une liste pour afficher l'autocomplétion des lieux
///////////////
}elseif ($action=='list') {
	if (isset($parametreUrl['q'])) {
		$q = strtolower($parametreUrl['q']);
	}else{
		$q = '';
	}

	if (isset($parametreUrl['categorie'])) {
		$categorie = $parametreUrl['categorie'];
	}else{
		$categorie = '';
	}
	if(!$q) exit;

	$items = $lieuManager->getLieuxByCategorie($categorie);
	foreach ($items as $key=>$value) {
		if (strpos(strtolower($value), $q) !== false) {
			echo $value."\n";
		}
	}	

////////////////
////  NOM  retourne uniquement le nom en texte brut
///////////////
}elseif ($action=='nom') {

	$lieu = $lieuManager->getLieu($idLieu);
	echo $lieu['nom'],", ",$lieu['commune'];
}


?>
