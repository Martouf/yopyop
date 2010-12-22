<?php
/*******************************************************************************************
 * Nom du fichier		: fichier.php
 * Date					: 9.9.9
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Permet de gérer des fichiers.
 *******************************************************************************************
 * Interface qui permet d'afficher une fichier ou l'interface de modification d'une fichier
 * 
 * 
 * On utilise ce fichier via la réécriture d'url.
 * URL pour lire, ajouter, modifier ou supprimer une ressource. Les paramètres supplémentaires sont envoyés par POST:
 * http://yopyop.ch/fichier/28-momo.html  (get)
 * http://yopyop.ch/fichier/fichier.html?add
 * http://yopyop.ch/fichier/28-momo.html?update
 * http://yopyop.ch/fichier/28-momo.html?delete
 *
 * URL pour afficher des vues utiles aux actions demandées:
 * http://yopyop.ch/fichier/fichier.html?new   => fourni une interface vide de création qui peut être utilisée pour soumettre un nouvel élément à l'url add
 * http://yopyop.ch/fichier/28-momo.html?modify   => fourni une interface de modification avec les données de l'élément qui peut être utilisée pour soumettre les modifications à l'url update
 * http://yopyop.ch/fichier/?name  => (à voir si cette url fonctionne vraiment) fourni la liste des nom des ressources
 */

/*
 *  Attention, il faut encore mettre en place la gestion des permissions !!!
 *
 */



 // Le système n'est pas terminée, ici on a juste les fonction qui permettent d'envoyer un fichier et de le lier via tinymce. Il n'y a pas encore de fonction qui permet d'exporter un fichier de manière sécurisée au travers d'un script.




// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idFichier = $ressourceId;

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


// actions particulières pour ajouter des fichiers depuis tinymce
if (isset($parametreUrl['newfichier'])) {
	$action = 'newfichier';
}
if (isset($parametreUrl['addfichier'])) {
	$action = 'addfichier';
}

// actions particulières pour ajouter du contenu html brut dans tinymce
if (isset($parametreUrl['newhtmlbrut'])) {
	$action = 'newhtmlbrut';
}
if (isset($parametreUrl['addhtmlbrut'])) {
	$action = 'addhtmlbrut';
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
$motsClesElement = $groupeManager->getMotCleElement($idFichier,'fichier'); // il y a certainement moyen d'optimiser tout ça... on a pas besoin d'avoir le nombre d'oocurence de l'utilisation du mot clé !! Peut être mettre une autre fonction !!

// transforme un tableau avec le mot clé et ses occurences en liste séparée par des virgules
$tagsVirgules = implode(',',array_keys($motsClesElement));
$smarty->assign('tags',$tagsVirgules);


// on défini ensuite les différentes actions possible.
// Pour l'action get, il y a plusieurs formats de sortie possibles. Le template est choisi en conséqunce. Pour le format pdf, le choix est fait en amont par index.php qui va fournir à princeXML le fichier html correspondant.

$droitModification = false;

if (!empty($idFichier)) {
	// pour modifier une fichier, il faut être le créateur de celle-ci ou un admin.
	$fichierAModifier = $fichierManager->getFichier($idFichier);
	$createur = $fichierAModifier['createur'];
	if ($_SESSION['id_personne']== $createur) {
		$droitModification = true;
	}

	// si le visiteur est admin
	$visiteur = $personneManager->getPersonne($_SESSION['id_personne']);
	if ($visiteur['rang']=='1') {
		$droitModification = true;
	}
}


////////////////
////  GET
///////////////

if ($action=='get') {
	
	// il y a 2 cas possibles qui peuvent être demandé. Une ressource unique bien précise, ou un groupe de ressource.
	
	// une ressource unique
	if (!empty($idFichier)) {
		
		// va chercher les infos sur la ressource demandée
		$fichier = $fichierManager->getFichier($idFichier);
		
		// supprime les \
		stripslashes_deep($fichier);
	
		// obtients un tableau avec la liste des mots-clés attribué à l'image
		$motCles = $groupeManager->getMotCleElement($idFichier,'fichier');
		
		$listeMotCle= '';
		foreach ($motCles as $motCle => $occurence){
			// si le mot clé est un "prénom nom", le découpe et ne prend que le prénom pour des raisons d'anonymat sur google
			$motCleEnpartie = explode(" ", $motCle);
			$prenom = $motCleEnpartie[0];
			$listeMotCle = $listeMotCle.$prenom.' ';
		}
		
		// fourni pour smarty une chaine de caractère avec la liste des tags (offuscé pour les nom de famille)
		$fichier['listeTags'] = $listeMotCle;
		
		// affichage de la ressource
		$smarty->assign('fichier',$fichier);	
		
		// si l'utilisateur a le droit de modification on lui fourni une icon vers la page de modification
		if ($droitModification) {
			$smarty->assign('utilisateurConnu',true);
		}else{
			$smarty->assign('utilisateurConnu',false);
		}

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/global.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/fichier.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		// certains formats ne sont jamais inclu dans un thème
		// affiche l'image brute
		if ($outputFormat=='jpg' or $outputFormat=='gif' or $outputFormat=='png' or $outputFormat=='JPG') {
			
			$filename = $fichier['lien'];
			
			$size = getimagesize($filename);
			$fp = fopen($filename, "rb");
			if ($size && $fp) {
			    header("Content-type: {$size['mime']}");
			    fpassthru($fp);
			    exit;
			} else {
			    echo "erreur d'affichage d'image brut...<br />".$filename;
			}
		}elseif ($outputFormat=='xml') {
			
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("fichier_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("fichier_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"fichier_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}
		} // if format = jpg..

	
	// un groupe de ressources
	}else{
		
		// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
		//http://yopyop.ch/fichier/    => va afficher la liste de toutes les fichiers.
		if (empty($tags)) {
			$tousFichiers = $fichierManager->getFichiers();
			
			$fichiers = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getFichiers()... et array_intersect
			foreach ($tousFichiers as $key => $aFichier) {
				$fichier = $aFichier;
				
				
				// obtients un tableau avec la liste des mots-clés attribué à l'image
				$motCles = $groupeManager->getMotCleElement($aFichier['id_fichier'],'fichier');

				$listeMotCle= '';
				foreach ($motCles as $motCle => $occurence){
					// si le mot clé est un "prénom nom", le découpe et ne prend que le prénom pour des raisons d'anonymat sur google
					$motCleEnpartie = explode(" ", $motCle);
					$prenom = $motCleEnpartie[0];
					$listeMotCle = $listeMotCle.$prenom.' ';
				}

				// fourni pour smarty une chaine de caractère avec la liste des tags (offuscé pour les nom de famille)
				$fichier['listeTags'] = $listeMotCle;
				$fichiers[$aFichier['id_fichier']] = $fichier;		
			}
			
		}else{
		
			 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
			$taggedElements = $groupeManager->getElementByTags($tags,'fichier');
		
			$fichiers = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getFichiers()... et array_intersect
			foreach ($taggedElements as $key => $idFichier) {
				$fichiers[$idFichier] = $fichierManager->getFichier($idFichier);
				
				// externe = 1 => fichier hébergée sur picasaweb
				// externe = 2 => fichier hébergée sur facebook

				if ($fichiers[$idFichier]['externe']=='1') {
					// déduit les chemin d'accès des fichiers moyenne et des vignette et les ajoute à la disposition de smarty
					$fichiers[$idFichier]['lienVignette'] = $fichierManager->getLienVignettePicasa($fichiers[$idFichier]['lien']);
					$fichiers[$idFichier]['lienMoyenne'] = $fichierManager->getLienMoyennePicasa($fichiers[$idFichier]['lien']);
				}elseif ($fichiers[$idFichier]['externe']=='2') {
					// déduit les chemin d'accès des fichiers moyenne et des vignette et les ajoute à la disposition de smarty
					$fichiers[$idFichier]['lienVignette'] = $fichierManager->getLienVignetteFacebook($fichiers[$idFichier]['lien']);
					$fichiers[$idFichier]['lienMoyenne'] = $fichiers[$idFichier]['lien']; // Sur facebook les images en grande taille sont de la même taille que les moyennes habituelles. Donc on donne comme lien de moyenne la grande taille!
				}else{
					// déduit les chemin d'accès des fichiers moyenne et des vignette et les ajoute à la disposition de smarty
					$fichiers[$idFichier]['lienVignette'] = $fichierManager->getLienVignette($fichiers[$idFichier]['lien']);
					$fichiers[$idFichier]['lienMoyenne'] = $fichierManager->getLienMoyenne($fichiers[$idFichier]['lien']);
				}
				

				// obtients un tableau avec la liste des mots-clés attribué à l'image
				$motCles = $groupeManager->getMotCleElement($idFichier,'fichier');

				$listeMotCle= '';
				foreach ($motCles as $motCle => $occurence){
					// si le mot clé est un "prénom nom", le découpe et ne prend que le prénom pour des raisons d'anonymat sur google
					$motCleEnpartie = explode(" ", $motCle);
					$prenom = $motCleEnpartie[0];
					$listeMotCle = $listeMotCle.$prenom.' ';
				}

				// fourni pour smarty une chaine de caractère avec la liste des tags (offuscé pour les nom de famille)
				$fichiers[$idFichier]['listeTags'] = $listeMotCle;
			}
		} // if $tags
		
		// supprime les \
		stripslashes_deep($fichiers);
		
		// transmets les ressources à smarty
		$smarty->assign('fichiers',$fichiers);
		
		// url du flux atom pour suivre les fichiers de cette catégorie
		$urlFlux = "http://".$serveur."/fichier/".trim($ressourceTags,"/")."/flux.xml";

		// <link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME']."/utile/css/lightbox.css\" media=\"screen\" />
		// <script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
		// <script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/dimensions.js\"></script>
		// <script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/prototype.js\"></script>
		// <script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/scriptaculous.js\"></script>
		// <script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/lightbox.js\"></script>

		// quelques scripts utiles
		$additionalHeader = "
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME']."/utile/css/regal.css\" media=\"screen\" />
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME']."/utile/css/regal_print.css\" media=\"print\" />
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/shadowbox.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/global.js\"></script>
			<script type=\"text/javascript\">
			Shadowbox.loadSkin('classic', 'http://".$_SERVER['SERVER_NAME']."/utile/js/shadowbox/src/skin');
			Shadowbox.loadLanguage('fr', 'http://".$_SERVER['SERVER_NAME']."/utile/js/shadowbox/build/lang');
			Shadowbox.loadPlayer(['img', 'flv'], 'http://".$_SERVER['SERVER_NAME']."/utile/js/shadowbox/build/player');
			window.onload = Shadowbox.init;
			</script>
			<link rel=\"alternate\" type=\"application/atom+xml\" title=\"Atom\" href=\"".$urlFlux."\" />";
				
		$smarty->assign('additionalHeader',$additionalHeader);

		if ($outputFormat=='xml') {
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("fichier_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("fichier_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"fichier_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}	
		} // if output = xml

	} //if groupe de ressource
	
////////////////
////  ADD
///////////////
	
}elseif ($action=='add') {
	
	// si l'utilisateur est connu
	if ($_SESSION['id_personne'] != '1') {
	
		// obtient les données de la fichiers qui peuvent apparaître dans formulaire d'ajout de fichier. Si rien est fourni le déduit à partir des données et métadonnées du fichier.
		if(isset($_POST['nom'])){  // non pris en compte pour le moment.. :P
			$nom = $_POST['nom'];
		}else{
			$nom ='';
		}
		if(isset($_POST['description'])){
			$description = $_POST['description'];
		}else{
			$description ='';
		}
	
		// tableau contenant les éventuelles erreur
		$errorMsg = array();
	
		// si un fichier est envoyé d'un formulaire appelé "fichier" et qu'il possède un nom et est de type mime ???/ ....
		if (isset($_FILES['fichier']) && !empty($_FILES['fichier']['name']) ) {  // && preg_match("|^fichier/|i",$_FILES['fichier']['type'])   à voir avec le type mime
			$nomOriginal = $_FILES['fichier']['name'];
			$nomSimplifie = $fichierManager->simplifieNomFichier($nomOriginal);
			$date = date('Y_m_d_H_i_');   // Le string est du type 2003_05_31_21_05_  pour le 31 mai 2003  21h05  ...permet de trier les fichiers par date, mais selon l'ordre alphabétique.
		
			// déplace le fichier de son emplacement temporaire au dossier divers ou il va résider
			// on ajoute la date comme préfixe au fichier pour rendre unique un fichier et éviter d'écraser un autre.
			// on simplifie également le nom des fichier en remplacant les caractères avec des accents par les mêmes sans accents. On remplace aussi les ' et les espaces par des _
			if (!move_uploaded_file($_FILES['fichier']['tmp_name'],"utile/fichiers/".$date.$nomSimplifie)) {
				$errorMsg[] = "Erreur lors du déplacement du fichier de tmp à sa destination";
			}
			if (!file_exists("utile/fichiers/".$date.$nomSimplifie)) {
				$errorMsg[] = "Impossible de trouver le fichier qui vient d'être ajoutée";
			}
		
			// si tout se passe bien continue le boulot et si des erreurs sont arrivées les affiche.
			if (count($errorMsg)>0) {
				print_r($errorMsg);
			}else{
			
				// le chemin d'accès à l'imge est donc:
				$fichierPath = "utile/fichiers/".$date.$nomSimplifie;

				// introduit dans la base un nouveau fichier et retourne le nouvel id créé
				$idFichier = $fichierManager->insertFichier($nomOriginal,$description,$fichierPath,'0','0'); //nom, description, lien, évaluation, externe
			
				echo $idFichier; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax	
			}
		}
	}// utilisateur connu 

////////////////
////  UPDATE
///////////////

}elseif ($action=='update') {
	// il est possible de mettre à jour tous les champs.. mais parfois c'est dangereux, on détruit la cohérence entre la BD et le fichier... faire attention !!
	
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
	if(isset($_POST['lien'])){
		$lien = $_POST['lien'];
	}else{
		$lien ='';
	}
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='';
	}
	
	// si l'utilisateur est admin ou créateur de la fichier
	if ($droitModification) {
	
		// fait la mise à jour
		$fichierManager->updateFichier($idFichier,$nom,$description,$lien,$evaluation,'0');
	
	}

////////////////
////  DELETE
///////////////   /// TODO: attention.. avant de supprimer une ressource et il faut la détaguer !!  .. encore écrire le code !

}elseif ($action=='delete') {
	
	// si l'utilisateur est admin ou créateur de la fichier
	if ($droitModification) {
		$fichierManager->deleteFichier($idFichier);
	}
	
////////////////
////  NEW
///////////////
}elseif ($action=='new') {
	
	//<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/dimensions.js\"></script>
	
	// si l'utilisateur est connu
	if ($_SESSION['id_personne'] != '1') {
	
		// quelques scripts utiles
		$additionalHeader = "
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME']."/utile/css/datePicker.css\" media=\"screen\" />
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>";  //		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/fichier.js\"></script>	
		$smarty->assign('additionalHeader',$additionalHeader);
	
		// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
		if ($theme=="no") {
			$smarty->display("fichier_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
		}else{
			// affiche le formulaire de modification inclu dans le template du thème index.tpl
			$smarty->assign('contenu',"fichier_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
			$smarty->display($theme."index.tpl");
		}
	}

////////////////
////  MODIFY
///////////////
}elseif ($action=='modify') {
	
	// si l'utilisateur est admin ou créateur de la fichier
	if ($droitModification) {
	
		// va chercher les infos sur la ressource demandée
		$fichier = $fichierManager->getFichier($idFichier);
	
		// supprime les \
		stripslashes_deep($fichier);
	
		// obtients un tableau avec la liste des mots-clés attribué au fichier
		$motCles = $groupeManager->getMotCleElement($idFichier,'fichier');
	
		$listeMotCle= '';
		foreach ($motCles as $motCle => $occurence){
			// si le mot clé est un "prénom nom", le découpe et ne prend que le prénom pour des raisons d'anonymat sur google
			$motCleEnpartie = explode(" ", $motCle);
			$prenom = $motCleEnpartie[0];
			$listeMotCle = $listeMotCle.$prenom.' ';
		}
	
		// fourni pour smarty une chaine de caractère avec la liste des tags (offuscé pour les nom de famille)
		$fichier['listeTags'] = $listeMotCle;
	
		// affichage de la ressource
		$smarty->assign('fichier',$fichier);	

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/tiny_mce/tiny_mce.js\"></script>
			<script type=\"text/javascript\">startRichEditor();</script>";	//			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/fichier.js\"></script>
		$smarty->assign('additionalHeader',$additionalHeader);

	
		// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
		if ($theme=="no") {
			$smarty->display("fichier_modify_".LANG.".tpl"); // affichage de l'interface de modification de la ressource
		}else{
			// affiche le formulaire de modification inclu dans le template du thème index.tpl
			$smarty->assign('contenu',"fichier_modify_".LANG.".tpl");
			$smarty->display($theme."index.tpl");
		}
	} // utilisateur connu

////////////////
////  NEW html brut. Cette fonction permet d'ajouter de l'html brut dans le contenu. Ceci permet d'ajouter des vidéos youtube, media.scout.ch, des cartes google, openstreetmap... etc..
///////////////
}elseif ($action=='newhtmlbrut') {

	// quelques scripts utiles
	$additionalHeader = "
		<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME']."/utile/css/yop2.css\" media=\"screen\" />
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.bgiframe.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>";  //<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/fichier.js\"></script>	
	$smarty->assign('additionalHeader',$additionalHeader);

	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("fichier_htmlbrut_embed_new_".LANG.".tpl"); // affichage de l'interface vide qui permet d'indiquer le nom d'un dossier du serveur.
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"fichier_htmlbrut_embed_new_".LANG.".tpl");
		$smarty->display($theme."index.tpl");
	}


////////////////
////  ADD HTML BREUT dans le texte d'un document via tinymce
///////////////

}elseif ($action=='addhtmlbrut') {

	// contenu html brut passé en paramètre
	if(isset($_POST['description'])){
		$description = $_POST['description'];
	}else{
		$description ='';
	}

		$htmlFichier = "'".$description."'";

		// affiche très brièvement une page dont le seul but est d'appeler le script qui va insérer le contenu fourni dans tinymce. Puis le popup se ferme.
		$htmlDebut = <<<END

			<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
				"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

			<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
				<head>
					<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
					<script src="/utile/js/tiny_mce/tiny_mce_popup.js" type="text/javascript" charset="utf-8" ></script>

END;
		echo $htmlDebut;
		echo "<script type=\"text/javascript\">\n";			
		echo "var html = ".$htmlFichier.";";
		echo "tinyMCEPopup.execCommand(\"mceInsertContent\", false, html);";
		echo "tinyMCEPopup.close();";
		echo "</script>\n";
		echo "<title>Ajout d'html brut</title></head>";
		echo "<body>";
		echo "<h3 class=\"ok\"> succès</h3>";
		echo "</body></html>";



////////////////
////  NEW FICHIER formulaire destiné à être intégrer dans un popup de tinymce pour introduire un fichier directement dans le texte
///////////////
}elseif ($action=='newfichier') {

	// quelques scripts utiles
	$additionalHeader = "
		<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$_SERVER['SERVER_NAME']."/utile/css/yop2.css\" media=\"screen\" />
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.bgiframe.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>";  //<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/fichier.js\"></script>	
	$smarty->assign('additionalHeader',$additionalHeader);

	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("fichier_new_".LANG.".tpl"); // affichage de l'interface vide qui permet d'indiquer le nom d'un dossier du serveur.
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"fichier_embed_new_".LANG.".tpl");
		$smarty->display($theme."index.tpl");
	}


////////////////
////  ADD FICHIER dans le texte d'un document via tinymce
///////////////

}elseif ($action=='addfichier') {


	if(isset($_POST['description'])){
		$description = $_POST['description'];
	}else{
		$description ='';
	}
	
	// tableau contenant les éventuelles erreur
	$errorMsg = array();	

	// si un fichier est envoyé d'un formulaire appelé "fichier" et qu'il possède un nom et est de type mime fichier/ ....
	if (isset($_FILES['fichier']) && !empty($_FILES['fichier']['name']) ) { // && preg_match("|^fichier/|i",$_FILES['fichier']['type'])
	
		$nomOriginal = $_FILES['fichier']['name'];
		$nomSimplifie = $fichierManager->simplifieNomFichier($nomOriginal);
		$date = date('Y_m_d_H_i_');   // Le string est du type 2003_05_31_21_05_  pour le 31 mai 2003  21h05  ...permet de trier les fichiers par date, mais selon l'ordre alphabétique.

		// extrait le suffixe et le passe en minuscule
	//	$suffixe = ereg_replace(".*\.([^\.]*)$", "\\1", $nomSimplifie);  // ereg => deprecated in php 5.3
		$pattern = '@\.([a-z A-Z]+)@';
		preg_match($pattern, $nomSimplifie, $matches);
		$suffixe = $matches[1];
		$suffixe = strtolower($suffixe);

		if(($suffixe=="pdf")or($suffixe=="doc")or($suffixe=="rtf")or($suffixe=="ics")or($suffixe=="vcs")or($suffixe=="mp3")or($suffixe=="txt")or($suffixe=="xls")or($suffixe=="svg")){	

			// déplace le fichier de son emplacement temporaire au dossier fichiers ou il va résider
			// on ajoute la date comme préfixe au fichier pour rendre unique un fichier et éviter d'écraser un autre.
			// on simplifie également le nom des fichier en remplacant les caractères avec des accents par les mêmes sans accents. On remplace aussi les ' et les espaces par des _
			if (!move_uploaded_file($_FILES['fichier']['tmp_name'],"utile/fichiers/".$date.$nomSimplifie)) {
				$errorMsg[] = "Erreur lors du déplacement du fichier de tmp à sa destination";
			}
			if (!file_exists("utile/fichiers/".$date.$nomSimplifie)) {
				$errorMsg[] = "Impossible de trouver le fichier qui vient d'être ajoutée";
			}
		
		}else{
			$errorMsg[] = "Ce format de fichier n'est pas accepté !";
		}

		// si tout se passe bien continue le boulot et si des erreurs sont arrivées les affiche.
		if (count($errorMsg)>0) {
			print_r($errorMsg);
		}else{

			// le chemin d'accès à l'imge est donc:
			$fichierPath = "utile/fichiers/".$date.$nomSimplifie;

			// introduit dans la base une nouvelle fichier et retourne le nouvel id créé
			$idFichier = $fichierManager->insertFichier($nomOriginal,$description,$fichierPath,'0','0'); //nom, description, lien, évaluation, externe
			
			// permet de garder une trace de toutes les fichiers qui sont utilisée dans des documents.
			$groupeManager->ajouteMotCle($idFichier,'DansUnDocument','fichier');
							
			$htmlFichier = "'<a href=\"http://".$_SERVER["SERVER_NAME"]."/".$fichierPath."\" title=\"téléchargez le fichier...\">".$nomOriginal."</a>'";
			
			
			// affiche très brièvement une page dont le seul but est d'appeler le script qui va insérer le contenu fourni dans tinymce. Puis le popup se ferme.
			$htmlDebut = <<<END

				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
					"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

				<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
					<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
						<script src="/utile/js/tiny_mce/tiny_mce_popup.js" type="text/javascript" charset="utf-8" ></script>

END;
			echo $htmlDebut;
			echo "<script type=\"text/javascript\">\n";			
			echo "var html = ".$htmlFichier.";";
			echo "tinyMCEPopup.execCommand(\"mceInsertContent\", false, html);";
			echo "tinyMCEPopup.close();";
			echo "</script>\n";
			echo "<title>Ajout d'un fichier</title></head>";
			echo "<body>";
			echo "<h3 class=\"ok\">Fichier uploadée avec succès</h3>";
			echo "<p>";
			echo "<legend>nouvel id </legend>";
			echo $idFichier; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax	
			echo "</p>";
			echo "</body></html>";
			
		}
	}
} // toutes les actions
?>