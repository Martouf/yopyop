<?php
/*******************************************************************************************
 * Nom du fichier		: mesure.php
 * Date					: 20 avril 2011
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@ecodev.ch
 * But de ce fichier	: Permet de gérer des mesures. Pour aller chercher, stocke et afficher des mesures.
 *******************************************************************************************
 * Pour différentier les status des gens
 * 
 * On utilise ce fichier via la réécriture d'url.
 * URL pour lire, ajouter, modifier ou supprimer une ressource. Les paramètres supplémentaires sont envoyés par POST:
 * http://yopyop.ch/mesure/28-momo.html  (get)
 * http://yopyop.ch/mesure/mesure.html?add
 * http://yopyop.ch/mesure/28-momo.html?update
 * http://yopyop.ch/mesure/28-momo.html?delete
 *
 * URL pour afficher des vues utiles aux actions demandées:
 * http://yopyop.ch/mesure/mesure.html?new   => fourni une interface vide de création qui peut être utilisée pour soumettre un nouvel élément à l'url add
 * http://yopyop.ch/mesure/28-momo.html?modify   => fourni une interface de modification avec les données de l'élément qui peut être utilisée pour soumettre les modifications à l'url update
 * http://yopyop.ch/mesure/martouf/mesure.xml => permet de s'abonner à toute la liste des mesures. D'un utilisateurs. 
 */

/*
 *  Attention, il faut encore mettre en place la gestion des permissions !!!
 *
 */

// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idMesure = $ressourceId;

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
if (isset($parametreUrl['import'])) {
	$action = 'import-cvn';
}
$mode = "liste";
if (isset($parametreUrl['mode'])) {
	$mode = $parametreUrl['mode']; // liste, couleur
}
if (isset($parametreUrl['filtre'])) {
	$filtreMesure = $parametreUrl['filtre']; // 1,2,3.... 17 .. voir la liste plus loin... = 2 pour la température de l'eau
}else{
	$filtreMesure = '';
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

// on défini ensuite les différentes actions possible.
// Pour l'action get, il y a plusieurs formats de sortie possibles. Le template est choisi en conséqunce. Pour le format pdf, le choix est fait en amont par index.php qui va fournir à princeXML le fichier html correspondant.

////////////////
////  GET
///////////////

if ($action=='get') {
	// il y a 2 cas possibles qui peuvent être demandé. Une ressource unique bien précise, ou un groupe de ressource.
	
	// une ressource unique
	if (!empty($idMesure)) {
		
		// va chercher les infos sur la ressource demandée
		$mesure = $mesureManager->getMesure($idMesure);
		$mesure['dateModification'] = dateTime2Humain($mesure['date_modification']);
		$mesure['dateMesure'] = dateTime2Humain($mesure['date_mesure']);		
		
		// supprime les \
		stripslashes_deep($mesure);
		
		// affichage de la ressource
		$smarty->assign('mesure',$mesure);	

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/mesure.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		// certains formats ne sont jamais inclus dans un thème
		if ($outputFormat=='xml' or $outputFormat=='php' ) {
			
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("mesure_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("mesure_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"mesure_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}
		} // if format = xml

	
	// un groupe de ressources
	}else{
		
		// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
		//http://yopyop.ch/mesure/    => va afficher la liste de tous les mesures.
		if (empty($tags)) {
			
			if (!empty($filtreMesure)) {
				$filtre = array('type'=>$filtreMesure);
				$tousMesures = $mesureManager->getMesures($filtre);
			}else{
				$tousMesures = $mesureManager->getMesures();
			}
						
			$mesures = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getMesures()... et array_intersect
			foreach ($tousMesures as $key => $aMesure) {
				$mesure = $aMesure;
				$mesure['dateModification'] = dateTime2Humain($aMesure['date_modification']); // va chercher la date de modification humaine du mesures
				$mesure['dateMesure'] = dateTime2Humain($mesure['date_mesure']);
			
				$mesures[$aMesure['id_mesure']] = $mesure;
			}
			$mesures = array_reverse($mesures);
			
		}else{
		
			 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
			$taggedElements = $groupeManager->getElementByTags($tags,'mesure');
			
			$taggedElements = array_reverse($taggedElements);  // met les posts dans l'ordre chronologique inverse
		
			$mesures = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getMesures()... et array_intersect
			foreach ($taggedElements as $key => $idMesure) {
				$mesures[$idMesure] = $mesureManager->getMesure($idMesure);
				$mesures[$idMesure]['dateModification'] = dateTime2Humain($mesures[$idMesure]['date_modification']); // va chercher la date de modification humaine du mesures
				$mesures[$idMesure]['dateMesure'] = dateTime2Humain($mesures[$idMesure]['date_mesure']);
			}
		} // if $tags
		
		// supprime les \
		stripslashes_deep($mesures);
		
		// function sortMesures($a, $b) {
		//                 $date_a = strtotime($a['date_publication']);
		//                 $date_b = strtotime($b['date_publication']);
		//                 return $date_a > $date_b ? 1 : -1;
		//         }
		// 
		//         // trie les evenements par ordre chronologique, inverse
		//         uasort($mesures, 'sortMesures');
		//$mesures = array_reverse($mesures);
		
		// transmets les ressources à smarty
		$smarty->assign('mesures',$mesures);

		// url du flux atom
		$urlFlux = "http://".$serveur."/mesure/".trim($ressourceTags,"/")."/mesures.xml";

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.uitablefilter.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.tablesorter.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/mesure.js\"></script>
			<link rel=\"alternate\" type=\"application/atom+xml\" title=\"Atom\" href=\"".$urlFlux."\" />";
				
		$smarty->assign('additionalHeader',$additionalHeader);


		if ($outputFormat=='xml' or $outputFormat=='php') {
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("mesure_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("mesure_multi_".$mode."_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"mesure_multi_".$mode."_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
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
	if(isset($_POST['valeur'])){
		$valeur = $_POST['valeur'];
	}else{
		$valeur ='';
	}
	if(isset($_POST['type'])){
		$type = $_POST['type'];
	}else{
		$type ='1';
	}
	if(isset($_POST['url'])){
		$url = $_POST['url'];
	}else{
		$url ='';
	}
	if(isset($_POST['date_mesure'])){
		$date_mesure = $_POST['date_mesure'];
	}else{
		$date_mesure ='';
	}
	if(isset($_POST['nom_lieu'])){
		$nom_lieu = $_POST['nom_lieu'];
	}else{
		$nom_lieu ='';
	}
	if(isset($_POST['id_lieu'])){
		$id_lieu = $_POST['id_lieu'];
	}else{
		$id_lieu ='';
	}

	// ajoute la nouvelle ressource
	$idMesure = $mesureManager->insertMesure($nom,$valeur,$type,$url,$date_mesure,$nom_lieu,$id_lieu,$description);

	echo $idMesure; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax

////////////////
////  UPDATE
///////////////

}elseif ($action=='update') {
	// obtient les données
	if(isset($_POST['id_mesure'])){
		$id_mesure = $_POST['id_mesure'];
	}else{
		$id_mesure ='';
	}
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
	if(isset($_POST['valeur'])){
		$valeur = $_POST['valeur'];
	}else{
		$valeur ='';
	}
	if(isset($_POST['type'])){
		$type = $_POST['type'];
	}else{
		$type ='1';
	}
	if(isset($_POST['url'])){
		$url = $_POST['url'];
	}else{
		$url ='';
	}
	if(isset($_POST['date_mesure'])){
		$date_mesure = $_POST['date_mesure'];
	}else{
		$date_mesure ='';
	}
	if(isset($_POST['nom_lieu'])){
		$nom_lieu = $_POST['nom_lieu'];
	}else{
		$nom_lieu ='';
	}
	if(isset($_POST['id_lieu'])){
		$id_lieu = $_POST['id_lieu'];
	}else{
		$id_lieu ='';
	}
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='';
	}
		
	// fait la mise à jour
	$mesureManager->updateMesure($id_mesure,$nom,$valeur,$type,$url,$date_mesure,$nom_lieu,$id_lieu,$description,$evaluation);

////////////////
////  DELETE
///////////////

}elseif ($action=='delete') {
	// avant de supprimer, il faut toujours enlever les tags d'abord. Sinon il reste des ressources fantômes...
	// ceci n'est pas fait ici. Il faut donc le faire rapidement via un appel ajax.
	
	// seulement pour les utilisateurs connu
	if ($_SESSION['id_personne'] != '1') {
		$mesureManager->deleteMesure($idMesure);
	}
////////////////
////  NEW
///////////////
}elseif ($action=='new') {
	// on fourni le pseudo... pour pouvoir écrire... toto mange une glace.
	$smarty->assign('pseudo',$_SESSION['pseudo']);
	
	// quelques scripts utiles
	$additionalHeader = "
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/mesure.js\"></script>";	
	$smarty->assign('additionalHeader',$additionalHeader);
	
	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("mesure_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"mesure_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
		$smarty->display($theme."index.tpl");
	}

////////////////
////  MODIFY
///////////////
}elseif ($action=='modify') {
	
	
	// va chercher les infos sur la ressource demandée
	$mesure = $mesureManager->getMesure($idMesure);
	$mesure['nomSimplifie'] = simplifieNom($mesure['nom']);
	
	// supprime les \
	stripslashes_deep($mesure);
	
	// passe les données de la mesure à l'affichage
	$smarty->assign('mesure',$mesure);
	
	// quelques scripts utiles
	$additionalHeader = "
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/mesure.js\"></script>";	
	$smarty->assign('additionalHeader',$additionalHeader);
	
	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("mesure_modify_".LANG.".tpl"); // affichage de l'interface de modification de la ressource
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"mesure_modify_".LANG.".tpl");
		$smarty->display($theme."index.tpl");
	}

////////////////
////  IMPORT FACEBOOK STATUTS
///////////////
}elseif ($action=='import-cvn') {

	$url = $urlCvn; // disponible dans la config

	if (!empty($url)) {
	    // * obtenir le fichier xml (régulièrement en fonction des visites)	
		// cas avec curl dans php
		function curl_get_file_contents($URL){
			$userAgent = 'Mozilla/4.0';
	        $c = curl_init();
			curl_setopt($c, CURLOPT_USERAGENT, $userAgent);
	        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($c, CURLOPT_URL, $URL);
	        $contents = curl_exec($c);
	        curl_close($c);
	        		
	        if ($contents) return $contents;
	            else return FALSE;
		}
		
		$xmlFeed = curl_get_file_contents($url);
		$xml = simplexml_load_string($xmlFeed);
		//print_r($xml);  // debug
		
		$nom = '';
		$type=1;
		$nom_lieu = "Port du Nid-du-Crô";
		
		$html =''; // contenu a afficher pour le controle du fonctionnement
		
		foreach ($xml as $key => $value) {
			
			if ($key=="pression") {
				$type = 3;
				$nom = "Pression hPa";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);
				
			}elseif($key=="tendance"){
				$type = 4;
				$nom = "Tendance météo";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);
				
			}elseif($key=="temperature"){
				$type = 1;
				$nom = "Température de air °C";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);

			}elseif($key=="minima"){
				$type = 5;
				$nom = "Température de air minima °C";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);

			}elseif($key=="maxima"){
				$type = 6;
				$nom = "Température de air maxima °C";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);

			}elseif($key=="humiditeRelative"){
				$type = 7;
				$nom = "Humidité relative %";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);

			}elseif($key=="pointRosee"){
				$type = 8;
				$nom = "Point de rosée °C";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);

			}elseif($key=="directionVent"){
				$type = 9;
				$nom = "direction du vent en °";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);

			}elseif($key=="sensVent"){
				$type = 10;
				$nom = "Sens du vent";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);

			}elseif($key=="vitesseVent"){
				$type = 11;
				$nom = "Vitesse du vent en noeud";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);

			}elseif($key=="vitesseVentMoyenne"){
				$type = 12;
				$nom = "Vitesse moyenne du vent sur 1min en noeud";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);

			}elseif($key=="beaufortVal"){
				$type = 13;
				$nom = "Force du vent en Bf";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);

			}elseif($key=="beaufort"){
				$type = 14;
				$nom = "Description du vent";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);

			}elseif($key=="sensationThermique"){
				$type = 15;
				$nom = "Sensation thermique °C";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);

			}elseif($key=="tauxPrecipitations"){
				$type = 16;
				$nom = "taux de précipitation en mm/h";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);

			}elseif($key=="totalPrecipitations"){
				$type = 17;
				$nom = "total des précipitations mm";
				$html.= "<li>".$xml->date." ".$nom." ".$value." ".$type." ".$nom_lieu."</li>";
				$mesureManager->insertMesure($nom,$value,$type,$url,$xml->date,$nom_lieu);

			}
		}
		
		echo "<h2>Importation réussie!</h2>";
		echo "<ul>";
		echo $html;
		echo "</ul>";
			// type des mesures
			// 1 temperature air °C
			// 2 température eau °C
			// 3 pression hPa
			// 4 tendance météo
			// 5 température air minima °C 
			// 6 température air maxima °C
			// 7 humidité relative %
			// 8 point de rosée °C
			// 9 direction vent °
			// 10 sens vent (N-E)
			// 11 vitesse vent noeud
			// 12 vitesse moyenne du vent sur une minute en noeud kts
			// 13 force du vent Bf
			// 14 description vent beaufort "petite brise"
			// 15 sensation thermique °C
			// 16 taux de précipitation en mm/h
			// 17 total précipitation mm
				
			
			// SimpleXMLElement Object
			// (
			//     [date] => 2011-04-15 19:25:24
			//     [capteurInterieurActif] => 1
			//     [capteurExterieurActif] => 1
			//     [anemometreActif] => 1
			//     [pluviometreActif] => 1
			//     [pression] => 950
			//     [tendance] => 4
			//     [temperature] => 13.0
			//     [mini] => 5.1
			//     [maxi] => 13.5
			//     [humiditeRelative] => 30
			//     [pointRosee] => 0
			//     [directionVent] => 56
			//     [sensVent] => N-E
			//     [vitesseVent] => 5.4
			//     [vitesseVentMoyenne] => 8.7
			//     [beaufortVal] => 2
			//     [beaufort] => Légère brise
			//     [sensationThermique] => 11
			//     [tauxPrecipitations] => 0
			//     [totalPrecipitations] => 0
			//     [totalPrecipitationsVeille] => 0
			// )
			
		echo "<p class=\"ok\"><a href=\"//" . $serveur . "/mesure/\">retour à la liste</a></p>";
	}else{
		echo "<p class=\"erreur\">veuillez fournir une url avec un contenu xml</p>";
	}
	
}// class

?>
