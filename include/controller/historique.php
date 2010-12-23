<?php
/*******************************************************************************************
 * Nom du fichier		: historique.php
 * Date					: 2 septembre 2009
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@ecodev.ch
 * But de ce fichier	: Permet de gérer des historiques.
 *******************************************************************************************
 * Interface qui permet d'afficher la liste des historiques. Principalement utilisé pour contrôler les lectures de flux atom vu qu'on a pas de stats
 * 
 * On utilise ce fichier via la réécriture d'url.
 * URL pour lire, ajouter, modifier ou supprimer une ressource. Les paramètres supplémentaires sont envoyés par POST:
 * http://yopyop.ch/historique/28-momo.html  (get)
 * http://yopyop.ch/historique/historique.html?add
 * http://yopyop.ch/historique/28-momo.html?update
 * http://yopyop.ch/historique/28-momo.html?delete
 *
 * URL pour afficher des vues utiles aux actions demandées:
 * http://yopyop.ch/historique/historique.html?new   => fourni une interface vide de création qui peut être utilisée pour soumettre un nouvel élément à l'url add
 * http://yopyop.ch/historique/28-momo.html?modify   => fourni une interface de modification avec les données de l'élément qui peut être utilisée pour soumettre les modifications à l'url update
 */

// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idHistorique = $ressourceId;

// détermine l'action demandée (add, update, delete, par défaut on suppose que c'est get, donc on ne l'indique pas)
$action = "get";
if (isset($parametreUrl['delete'])) {
	$action = 'delete';
}

// obtient le format de sortie. Si rien n'est défini, on choisi html
if (empty($ressourceOutput)) {
	$outputFormat = 'html';
}else{
	$outputFormat = $ressourceOutput;
}



// on défini ensuite les différentes actions possible.
// Pour l'action get, il y a plusieurs formats de sortie possibles. Le template est choisi en conséqunce. Pour le format pdf, le choix est fait en amont par index.php qui va fournir à princeXML le fichier html correspondant.

////////////////
////  GET
///////////////

if ($action=='get') {
	// il y a 2 cas possibles qui peuvent être demandés. Une ressource unique bien précise, ou un groupe de ressource.
	
	// une ressource unique
	if (!empty($idHistorique)) {
		
		// va chercher les infos sur la ressource demandée
		$historique = $historiqueManager->getHistorique($idHistorique);
		
		// supprime les \
		stripslashes_deep($historique);
		
		// affichage de la ressource
		$smarty->assign('historique',$historique);	

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
			<script src=\"http://maps.google.com/maps?file=api&v=2.x&key=".$googleMapsKey."\" type=\"text/javascript\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/historique.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		// certains formats ne sont jamais inclu dans un thème
		if ($outputFormat=='kml') {
			header('Content-Type: application/vnd.google-earth.kml+xml');
			$smarty->display("historique_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}elseif ($outputFormat=='xml') {
			
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("historique_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}elseif ($outputFormat=='json') {

			// calcule le nom de la même ressource, mais au format json. (pour être inclu dans un combobox)
			header('Content-Type: application/json; charset=UTF-8');
			$smarty->display("historique_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("historique_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"historique_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}
		} // if format = kml

	
	// un groupe de ressources
	}else{
		
		// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
		//http://yopyop.ch/historique/    => va afficher la liste de toutes les historiques.
		if (empty($tags)) {
			$tousHistoriques = $historiqueManager->getHistoriques();
			$historiques = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getHistoriques()... et array_intersect
			foreach ($tousHistoriques as $key => $aHistorique) {
				$historique = $aHistorique;
				
				$historique['nomSimplifie'] = simplifieNom($aHistorique['nom']);				
				$historiques[$aHistorique['id_historique']] = $historique;		
			}
		}else{
		
			 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
			$taggedElements = $groupeManager->getElementByTags($tags,'historique');
		
			$historiques = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getHistoriques()... et array_intersect
			foreach ($taggedElements as $key => $idHistorique) {
				$historiques[$idHistorique] = $historiqueManager->getHistorique($idHistorique);
				$historiques[$idHistorique]['nomSimplifie'] = simplifieNom($historiques[$idHistorique]['nom']);
			}
		} // if $tags
		
		// supprime les \
		stripslashes_deep($historiques);
		
		// transmets les ressources à smarty
		$smarty->assign('historiques',$historiques);

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.uitablefilter.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.tablesorter.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/historique_edit_list.js\"></script>";	 // le même fichier avec un e à la fin de liste ne marche pas !! typo3 met un message d'erreur: Reason: Segment "ecoical" was not a keyword for a postVarSet as expected! => est ce qu'il y a un filtre anti français ?
		$smarty->assign('additionalHeader',$additionalHeader);

		if ($outputFormat=="kml") {
			header('Content-Type: application/vnd.google-earth.kml+xml');
			$smarty->display("historique_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
			
		}elseif ($outputFormat=='xml') {
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("historique_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
			
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("historique_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"historique_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}	
		} // if output = kml

	} //if groupe de ressource
	

}elseif ($action=='delete') {
	$historiqueManager->deleteHistorique($idHistorique);
}


?>
