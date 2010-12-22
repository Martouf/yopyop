<?php
/*******************************************************************************************
 * Nom du fichier		: restriction.php
 * Date					: 27 novembre 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Permet de gérer les restrictions qui sont appliquées sur les ressources disponible sur cette plateforme.
 *******************************************************************************************
 * Interface qui permet d'afficher une restriction ou l'interface de modification d'une restriction
 *
 * 	$typesRestrictions = array('1'=>'lire','2'=>'écrire','3'=>'lister','4'=>'commenter','5'=>'taguer');
 * 
 * On utilise ce fichier via la réécriture d'url.
 * URL pour lire, ajouter, modifier ou supprimer une ressource. Les paramètres supplémentaires sont envoyés par POST:
 * http://yopyop.ch/restriction/28-momo.html  (get)
 * http://yopyop.ch/restriction/restriction.html?add
 * http://yopyop.ch/restriction/28-momo.html?update
 * http://yopyop.ch/restriction/28-momo.html?delete
 *
 * URL pour afficher des vues utiles aux actions demandées:
 * http://yopyop.ch/restriction/restriction.html?new   => fourni une interface vide de création qui peut être utilisée pour soumettre un nouvel élément à l'url add
 * http://yopyop.ch/restriction/28-momo.html?modify   => fourni une interface de modification avec les données de l'élément qui peut être utilisée pour soumettre les modifications à l'url update
 * http://yopyop.ch/restriction/?name  => (à voir si cette url fonctionne vraiment) fourni la liste des nom des ressources
 */


// n'autorise que les admin de rang 1 (le plus haut rang) à pouvoir créer des restrictions.
if ($_SESSION['rang']!='1') {
	header("Location: /");
}


// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idRestriction = $ressourceId;

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
	if (!empty($idRestriction)) {
		
		// va chercher les infos sur la ressource demandée
		$restriction = $restrictionManager->getRestriction($idRestriction);
		
		// supprime les \
		stripslashes_deep($restriction);
		
		// transforme les id de groupe en nom
		$nomGroupeUtilisateur = $groupeManager->getGroupe($restriction['id_groupe_utilisateur'],array('nom'));
		$nomGroupeElement = $groupeManager->getGroupe($restriction['id_groupe_element'],array('nom'));
		
		// indique le nom des restrictions à la place du numéro
		if ($restriction['type']=='1') {
			$nomRestriction = 'lire';
		}elseif ($restriction['type']=='2') {
			$nomRestriction = 'écrire';
		}elseif($restriction['type']=='3'){
			$nomRestriction = 'lister';
		}elseif($restriction['type']=='4'){
			$nomRestriction = 'commenter';
		}else {
			$nomRestriction = 'restriction inconnue !';
		}
		
		// place les infos dans le tableau qui va être disponible pour smarty
		$restriction['nomGroupeUtilisateur'] = $nomGroupeUtilisateur;
		$restriction['nomGroupeElement'] = $nomGroupeElement;
		$restriction['nomRestriction'] = $nomRestriction;
		
		// affichage de la ressource
		$smarty->assign('restriction',$restriction);	

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/dimensions.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/restriction.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		// certains formats ne sont jamais inclu dans un thème
		if ($outputFormat=='xml') {
			
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("restriction_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("restriction_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"restriction_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}
		} // if format = xml

	
	// un groupe de ressources
	}else{
		
		// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
		//http://yopyop.ch/restriction/    => va afficher la liste de toutes les restrictions.
		if (empty($tags)) {
			$restrictionsBrutes = $restrictionManager->getRestrictions();
			
			$restrictions = array(); // tableau contenant des tableaux représentant la ressource
			foreach ($restrictionsBrutes as $key => $aRestriction) {
				$restriction = $aRestriction;
				
				// transforme les id de groupe en nom
				$restriction['nomGroupeUtilisateur'] = $groupeManager->getGroupe($restriction['id_groupe_utilisateur'],array('nom'));
				$restriction['nomGroupeElement'] = $groupeManager->getGroupe($restriction['id_groupe_element'],array('nom'));

				// indique le nom des restrictions à la place du numéro
				if ($restriction['type']=='1') {
					$nomRestriction = 'lire';
				}elseif ($restriction['type']=='2') {
					$nomRestriction = 'écrire';
				}elseif($restriction['type']=='3'){
					$nomRestriction = 'lister';
				}elseif($restriction['type']=='4'){
					$nomRestriction = 'commenter';
				}else {
					$nomRestriction = 'restriction inconnue !';
				}
				$restriction['nomRestriction'] = $nomRestriction;
				
				// ajoute la nouvelle restriction complétée dans le tableau qui les contient
				$restrictions[] = $restriction;
			}
			
		}else{
		
			 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
			$taggedElements = $groupeManager->getElementByTags($tags,'restriction');
		
			$restrictions = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getRestrictions()... et array_intersect
			foreach ($taggedElements as $key => $idRestriction) {
				$restrictions[$idRestriction] = $restrictionManager->getRestriction($idRestriction);
				
					// transforme les id de groupe en nom
				$restrictions[$idRestriction]['nomGroupeUtilisateur'] = $groupeManager->getGroupe($restriction['id_groupe_utilisateur'],array('nom'));
				$restrictions[$idRestriction]['nomGroupeElement'] = $groupeManager->getGroupe($restriction['id_groupe_element'],array('nom'));
				
				// indique le nom des restrictions à la place du numéro
				if ($restriction['type']=='1') {
					$nomRestriction = 'lire';
				}elseif ($restriction['type']=='2') {
					$nomRestriction = 'écrire';
				}elseif($restriction['type']=='3'){
					$nomRestriction = 'lister';
				}elseif($restriction['type']=='4'){
					$nomRestriction = 'commenter';
				}else {
					$nomRestriction = 'restriction inconnue !';
				}
				$restrictions[$idRestriction]['nomRestriction'] = $nomRestriction;
			}
		} // if $tags
		
		// supprime les \
		stripslashes_deep($restrictions);
				
		// transmets les ressources à smarty
		$smarty->assign('restrictions',$restrictions);

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/dimensions.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.datePicker.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/restriction.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		if ($outputFormat=='xml') {
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("restriction_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("restriction_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"restriction_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
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
	if(isset($_POST['id_groupe_utilisateur'])){
		$idGroupeUtilisateur = $_POST['id_groupe_utilisateur'];
	}else{
		$idGroupeUtilisateur ='';
	}
	if(isset($_POST['id_groupe_element'])){
		$idGroupeElement = $_POST['id_groupe_element'];
	}else{
		$idGroupeElement ='';
	}
	if(isset($_POST['type'])){
		$type = $_POST['type'];
	}else{
		$type ='';
	}
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='0';
	}
	
	// ajoute la nouvelle ressource
	$idRestriction = $restrictionManager->insertRestriction($idGroupeUtilisateur,$idGroupeElement,$type,$nom,$evaluation);
	
	echo $idRestriction; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax

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
	if(isset($_POST['id_groupe_utilisateur'])){
		$idGroupeUtilisateur = $_POST['id_groupe_utilisateur'];
	}else{
		$idGroupeUtilisateur ='';
	}
	if(isset($_POST['id_groupe_element'])){
		$idGroupeElement = $_POST['id_groupe_element'];
	}else{
		$idGroupeElement ='';
	}
	if(isset($_POST['type'])){
		$type = $_POST['type'];
	}else{
		$type ='';
	}
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='';
	}
	
	// fait la mise à jour
	$restrictionManager->updateRestriction($idRestriction,$idGroupeUtilisateur,$idGroupeElement,$type,$nom,$evaluation);

////////////////
////  DELETE
///////////////

}elseif ($action=='delete') {
	$restrictionManager->deleteRestriction($idRestriction);
	
////////////////
////  NEW
///////////////
}elseif ($action=='new') {
	
	// quelques scripts utiles
	$additionalHeader = "
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/dimensions.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.bgiframe.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/restriction.js\"></script>";	
	$smarty->assign('additionalHeader',$additionalHeader);
	
	// fourni un tableau avec tous les noms des groupes et leur id
	$nomsGroupes = $groupeManager->getGroupes(array('id_groupe','nom'),array(),'nom');
	$smarty->assign('nomsGroupes',$nomsGroupes);
	
	// fourni les noms des restrictions
	$typesRestrictions = array('1'=>'lire','2'=>'modifier','3'=>'lister','4'=>'commenter','5'=>'taguer');
	$smarty->assign('typesRestrictions',$typesRestrictions);
	
	
	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("restriction_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"restriction_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
		$smarty->display($theme."index.tpl");
	}

////////////////
////  MODIFY
///////////////
}elseif ($action=='modify') {
	// va chercher les infos sur la ressource demandée
	$restriction = $restrictionManager->getRestriction($idRestriction);
	
	// supprime les \
	stripslashes_deep($restriction);
	
	// passe les données de la restriction à l'affichage
	$smarty->assign('restriction',$restriction);
	
	// quelques scripts utiles
	$additionalHeader = "
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/dimensions.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.bgiframe.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/restriction.js\"></script>";	
	$smarty->assign('additionalHeader',$additionalHeader);
	
	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("restriction_modify_".LANG.".tpl"); // affichage de l'interface de modification de la ressource
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"restriction_modify_".LANG.".tpl");
		$smarty->display($theme."index.tpl");
	}	
} // actions

?>
