<?php
/*******************************************************************************************
 * Nom du fichier		: groupe.php
 * Date					: 25 novembre 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Permet de gérer des groupes.
 *******************************************************************************************
 * Interface qui permet d'afficher un groupe ou l'interface de modification d'un groupe.
 * Le groupe est un objet un peu particulier, par ce qu'il est l'objet de base du tag !
 * Donc on utilise bien souvent des groupes, mais par l'interface de tags
 *
 * comment taguer un élément ?
 * http://yopyop.ch/groupe/tag.html?type=document&id=28&tag=salade,fruit,recette
 * http://yopyop.ch/groupe/tag.html?type=document&id=28&addtag=cuisine
 * http://yopyop.ch/groupe/tag.html?type=document&id=28&deletetag=cuisine 
 * http://yopyop.ch/groupe/tag.html?type=document&id=28&gettag    => retourne la liste des tags spéarés par des, => salade,fruit,recette
 *
 */

/*
 *  Attention, il faut encore mettre en place la gestion des permissions !!!
 *  Interdit d'accès aux inconnus
 */

if ($_SESSION['id_personne'] != '1') {


	// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
	$idGroupe = $ressourceId;

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

	// pour taguer..
	if (isset($parametreUrl['tag'])) {
		$action = 'tag';
	}
	if (isset($parametreUrl['addtag'])) {
		$action = 'addtag';
	}
	if (isset($parametreUrl['deletetag'])) {
		$action = 'deletetag';
	}
	if (isset($parametreUrl['gettag'])) {
		$action = 'gettag';
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
		if (!empty($idGroupe)) {
		
			// va chercher les infos sur la ressource demandée
			$groupe = $groupeManager->getGroupe($idGroupe);
		
			// supprime les \
			stripslashes_deep($groupe);
		
		
		
			// affichage de la ressource
			$smarty->assign('groupe',$groupe);	

			// quelques scripts utiles
			$additionalHeader = "
				<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME']."/utile/css/datePicker.css\" media=\"screen\" />
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/dimensions.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/date.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/date_fr.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.bgiframe.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.datePicker.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/groupe.js\"></script>";	
			$smarty->assign('additionalHeader',$additionalHeader);

			// certains formats ne sont jamais inclu dans un thème
			if ($outputFormat=='xml') {
			
				// calcule le nom de la même ressource, mais en page html
				$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
				$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
				header('Content-Type: application/atom+xml; charset=UTF-8');
				$smarty->display("groupe_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
			
				// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
				if ($theme=="no") {
					$smarty->display("groupe_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
				}else{
					// affiche la ressource inclue dans le template du thème index.tpl
					$smarty->assign('contenu',"groupe_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
					$smarty->display($theme."index.tpl");
				}
			} // if format = xml

	
		// un groupe de ressources
		}else{
		
			// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
			//http://yopyop.ch/groupe/    => va afficher la liste de toutes les groupes.
			if (empty($tags)) {
				$groupes = $groupeManager->getGroupes();
			}else{
		
				 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
				$taggedElements = $groupeManager->getElementByTags($tags,'groupe');
		
				$groupes = array(); // tableau contenant des tableaux représentant la ressource
				// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getGroupes()... et array_intersect
				foreach ($taggedElements as $key => $idGroupe) {
					$groupes[$idGroupe] = $groupeManager->getGroupe($idGroupe);
				}
			} // if $tags
		
			// supprime les \
			stripslashes_deep($groupes);
		
			// transmets les ressources à smarty
			$smarty->assign('groupes',$groupes);

			// quelques scripts utiles
			$additionalHeader = "
				<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME']."/utile/css/datePicker.css\" media=\"screen\" />
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/dimensions.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/date.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/date_fr.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.bgiframe.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.datePicker.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/groupe.js\"></script>";	
			$smarty->assign('additionalHeader',$additionalHeader);

			if ($outputFormat=='xml') {
				// calcule le nom de la même ressource, mais en page html
				$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
				$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
				header('Content-Type: application/atom+xml; charset=UTF-8');
				$smarty->display("groupe_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
			
				// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
				if ($theme=="no") {
					$smarty->display("groupe_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
				}else{
					// affiche la ressource inclue dans le template du thème index.tpl
					$smarty->assign('contenu',"groupe_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
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
		if(isset($_POST['type'])){
			$type = $_POST['type'];
		}else{
			$type ='';
		}	
		// ajoute la nouvelle ressource
		$idGroupe = $groupeManager->insertGroupe($nom,$description);
	
		echo $idGroupe; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax

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
		if(isset($_POST['type'])){
			$type = $_POST['type'];
		}else{
			$type ='';
		}
	
		// fait la mise à jour
		$groupeManager->updateGroupe($idGroupe,$nom,$description,$type);

	////////////////
	////  DELETE
	///////////////

	}elseif ($action=='delete') {
		$groupeManager->deleteGroupe($idGroupe);
	
	////////////////
	////  NEW
	///////////////
	}elseif ($action=='new') {
	
		// quelques scripts utiles
		$additionalHeader = "
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME']."/utile/css/datePicker.css\" media=\"screen\" />
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/dimensions.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/date.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/date_fr.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.datePicker.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/groupe.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);
	
		// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
		if ($theme=="no") {
			$smarty->display("groupe_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
		}else{
			// affiche le formulaire de modification inclu dans le template du thème index.tpl
			$smarty->assign('contenu',"groupe_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
			$smarty->display($theme."index.tpl");
		}

	////////////////
	////  MODIFY
	///////////////
	}elseif ($action=='modify') {
		// va chercher les infos sur la ressource demandée
		$groupe = $groupeManager->getGroupe($idGroupe);
	
		// supprime les \
		stripslashes_deep($groupe);
	
		// passe les données de la groupe à l'affichage
		$smarty->assign('groupe',$groupe);
	
		// quelques scripts utiles
		$additionalHeader = "
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME']."/utile/css/datePicker.css\" media=\"screen\" />
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/dimensions.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.datePicker.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/groupe.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);
	
		// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
		if ($theme=="no") {
			$smarty->display("groupe_modify_".LANG.".tpl"); // affichage de l'interface de modification de la ressource
		}else{
			// affiche le formulaire de modification inclu dans le template du thème index.tpl
			$smarty->assign('contenu',"groupe_modify_".LANG.".tpl");
			$smarty->display($theme."index.tpl");
		}	
	////////////////
	////  LIST ... fourni une liste pour afficher l'autocomplétion des mots-clés
	///////////////
	}elseif ($action=='list') {
		if (isset($parametreUrl['q'])) {
			$q = strtolower($parametreUrl['q']);
		}else{
			$q = '';
		}
	
		if (isset($parametreUrl['type'])) {
			$typeElement = $parametreUrl['type'];
		}else{
			$typeElement = 'document';
		}
		if(!$q) exit;

		$items = $groupeManager->getMotCleParTypeElement($typeElement);  // tableau des motsclés  (getMotCle() retourne aussi les occurences.. mais ici pas besoin)
		foreach ($items as $key=>$value) {
			if (strpos(strtolower($value), $q) !== false) {
				echo $value."\n";
			}
		}
	////////////////
	////  TAG ... permet de tager un élément en lui fournissant la liste de tags spéaré par des , dans une chaine de caractère.
	///////////////
	}elseif ($action=='tag') {
	
		if(isset($parametreUrl['type'])){
			$type = $parametreUrl['type'];
		}else{
			$type = 'document';  // type par défaut
		}
		if(isset($parametreUrl['id'])){
			$id = $parametreUrl['id'];
		}else{
			$id = '';
		}
	
		$taglist = $parametreUrl['tag'];
	
		$listeMotsClesVirgule = explode(",", trim($taglist)); // on supprime les espaces au début et à la fin ce qui pourrait être interprété comme un nouveau mot clé "espace"

		// les appels ajax on pour conséquence d'encoder les carctères de l'url. Ainsi les espaces devienent %20 et les ' %27. on utilise urldecode pour retrouver les caractères originaux
		// Puis, on supprime les espaces en début et fin de chaine, mais on garde les espaces au milieux. (utilisé pour les nom de personne par exemple)
		$listeMots = array();
		foreach ($listeMotsClesVirgule as $key => $mot) {
			$listeMots[] = trim(urldecode($mot));
		}


		// listeMots contient les mots qui sont soumis et de toute façon à garder.
		// motsClesActuels contient les mots qui sont actuellement associé à l'element
		$motsClesActuels = array_keys($groupeManager->getMotCleElement($id, $type));

		// motsClé à supprimer est la différence entre les mots-clés actuel et mots-clés soumis.
		$motsClesASupprimer = array_diff($motsClesActuels,$listeMots);

		// print_r($motsClesASupprimer);  //ici

		// suprimer les mots à supprimer
		foreach( $motsClesASupprimer as $key => $mot ){
			if(!empty($mot)){
				$groupeManager->supprimerMotCle($id, $mot, $type);
			}
		}

		// ajouter les mots à ajouter.
		foreach($listeMots as $mot){
			//$mot = preg_replace("/\s/","",$mot);  //supprime les espaces.. ennuyeux si on veut garder des noms de famille
			$mot = trim($mot);  // supprime les espaces en bout du mot clé, mais laisse ceux qui sont au milieu.
			if(!empty($mot)){  // en combinaison avec le trim cette fonction permet de supprimer les erreurs de séparation de motclé dans le genre...  ,   toto,  ici, , encore,     à la fin il ne reste que toto ici et encore
				$groupeManager->ajouteMotCle($id, $mot, $type);
			//	echo "<br>va ajouter:";  //ici
			//	echo "<br>mot= >".$mot."<";
			//	echo "<br>id= ".$id;
			//	echo "<br>table= ".$type;
			}
		} // foreach
	
	////////////////
	////  ADDTAG ... permet d'ajouter un tag à un élément
	///////////////
	}elseif ($action=='addtag') {
	
		if(isset($parametreUrl['type'])){
			$type = $parametreUrl['type'];
		}else{
			$type = 'document';  // type par défaut
		}
		if(isset($parametreUrl['id'])){
			$id = $parametreUrl['id'];
		}else{
			$id = '';
		}
	
		$keyword = trim($parametreUrl['addtag']);
	
		if(!empty($keyword)){
			$groupeManager->ajouteMotCle($id, $keyword, $type);
		}
	
	////////////////
	////  DELETETAG ... permet de supprimer un tag à un élément
	///////////////
	}elseif ($action=='deletetag') {
	
		if(isset($parametreUrl['type'])){
			$type = $parametreUrl['type'];
		}else{
			$type = 'document';  // type par défaut
		}
		if(isset($parametreUrl['id'])){
			$id = $parametreUrl['id'];
		}else{
			$id = '';
		}
	
		$keyword = trim($parametreUrl['deletetag']);

	
		if(!empty($keyword)){
			//echo "supprimer: ".$type.$id." =>".$keyword;
			$groupeManager->supprimerMotCle($id, $keyword, $type);
		}
	
	////////////////
	////  GETTAG ... obtient la liste séparée par des , des tags associé à un élémnets
	///////////////
	}elseif ($action=='gettag') {
	
		if(isset($parametreUrl['type'])){
			$type = $parametreUrl['type'];
		}else{
			$type = 'document';  // type par défaut
		}
		if(isset($parametreUrl['id'])){
			$id = $parametreUrl['id'];
		}else{
			$id = '';
		}
	
		$motsClesElement = $groupeManager->getMotCleElement($id,$type);

		// transforme un tableau avec le mot clé et ses occurences en liste séparée par des virgules
		$tags = implode(',',array_keys($motsClesElement));
		echo $tags;
	
	
	}// id action
}// if utilisateur connu
?>
