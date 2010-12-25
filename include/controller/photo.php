<?php
/*******************************************************************************************
 * Nom du fichier		: photo.php
 * Date					: 16 novembre 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Permet de gérer des photos. Ce fichier génère des vues pour gérer des photos ou des albums
 *******************************************************************************************
 * Interface qui permet d'afficher une photo ou l'interface de modification d'une photo
 * 
 * On utilise ce fichier via la réécriture d'url.
 * URL pour lire, ajouter, modifier ou supprimer une ressource. Les paramètres supplémentaires sont envoyés par POST:
 * http://yopyop.ch/photo/28-momo.html  (get)
 * http://yopyop.ch/photo/photo.html?add
 * http://yopyop.ch/photo/28-momo.html?update
 * http://yopyop.ch/photo/28-momo.html?delete
 *
 * URL pour afficher des vues utiles aux actions demandées:
 * http://yopyop.ch/photo/photo.html?new   => fourni une interface vide de création qui peut être utilisée pour soumettre un nouvel élément à l'url add
 * http://yopyop.ch/photo/28-momo.html?modify   => fourni une interface de modification avec les données de l'élément qui peut être utilisée pour soumettre les modifications à l'url update
 * http://yopyop.ch/photo/?name  => (à voir si cette url fonctionne vraiment) fourni la liste des nom des ressources
 */

/*
 *  Attention, il faut encore mettre en place la gestion des permissions !!!
 *
 */

// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idPhoto = $ressourceId;

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

// actions particulières pour ajouter des photos en masse depuis un dossier local du serveur.
if (isset($parametreUrl['newalbum'])) {
	$action = 'newalbum';
}
if (isset($parametreUrl['addalbum'])) {
	$action = 'addalbum';
}

// actions particulières pour ajouter des photos depuis tinymce
if (isset($parametreUrl['newimage'])) {
	$action = 'newimage';
}
if (isset($parametreUrl['addimage'])) {
	$action = 'addimage';
}

// action pour ajouter une galerie hébergée sur picasa web
if (isset($parametreUrl['newalbumpicasa'])) {
	$action = 'newalbumpicasa';
}
if (isset($parametreUrl['addpicasa'])) {
	$action = 'addpicasa';
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
$motsClesElement = $groupeManager->getMotCleElement($idPhoto,'photo'); // il y a certainement moyen d'optimiser tout ça... on a pas besoin d'avoir le nombre d'oocurence de l'utilisation du mot clé !! Peut être mettre une autre fonction !!

// transforme un tableau avec le mot clé et ses occurences en liste séparée par des virgules
$tagsVirgules = implode(',',array_keys($motsClesElement));
$smarty->assign('tags',$tagsVirgules);


// on défini ensuite les différentes actions possible.
// Pour l'action get, il y a plusieurs formats de sortie possibles. Le template est choisi en conséqunce. Pour le format pdf, le choix est fait en amont par index.php qui va fournir à princeXML le fichier html correspondant.

$droitModification = false;

if (!empty($idPhoto)) {
	// pour modifier une photo, il faut être le créateur de celle-ci ou un admin.
	$photoAModifier = $photoManager->getPhoto($idPhoto);
	$createur = $photoAModifier['createur'];
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
	if (!empty($idPhoto)) {
		
		// va chercher les infos sur la ressource demandée
		$photo = $photoManager->getPhoto($idPhoto);
		
		// supprime les \
		stripslashes_deep($photo);
		
		// externe = 1 => photo hébergée sur picasaweb
		// externe = 2 => photo hébergée sur facebook
		
		if ($photo['externe']=='1') {
			// déduit les chemin d'accès des photos moyenne et des vignette et les ajoute à la disposition de smarty
			$photo['lienVignette'] = $photoManager->getLienVignettePicasa($photo['lien']);
			$photo['lienMoyenne'] = $photoManager->getLienMoyennePicasa($photo['lien']);
		}elseif ($photo['externe']=='2') {
			// déduit les chemin d'accès des photos moyenne et des vignette et les ajoute à la disposition de smarty
			$photo['lienVignette'] = $photoManager->getLienVignetteFacebook($photo['lien']);
			$photo['lienMoyenne'] = $photo['lien']; // Sur facebook les images en grande taille sont de la même taille que les moyennes habituelles. Donc on donne comme lien de moyenne la grande taille!
		}else{
			// déduit les chemin d'accès des photos moyenne et des vignette et les ajoute à la disposition de smarty
			$photo['lienVignette'] = $photoManager->getLienVignette($photo['lien']);
			$photo['lienMoyenne'] = $photoManager->getLienMoyenne($photo['lien']);
		}

		
		// détail de l'exif
		// $exif = $photoManager->getExif($photo['lien']);
		// 
		// $exifHtml = "<ul id=\"exif\">";
		// foreach ($exif as $key => $value) {
		// 	$exifHtml.= "<li>".$key.": ".$value."</li>";
		// }
		// $photo['exif'] = $exifHtml."</ul>";
	
		
		// obtients un tableau avec la liste des mots-clés attribué à l'image
		$motCles = $groupeManager->getMotCleElement($idPhoto,'photo');
		
		$listeMotCle= '';
		foreach ($motCles as $motCle => $occurence){
			// si le mot clé est un "prénom nom", le découpe et ne prend que le prénom pour des raisons d'anonymat sur google
			$motCleEnpartie = explode(" ", $motCle);
			$prenom = $motCleEnpartie[0];
			$listeMotCle = $listeMotCle.$prenom.' ';
		}
		
		// fourni pour smarty une chaine de caractère avec la liste des tags (offuscé pour les nom de famille)
		$photo['listeTags'] = $listeMotCle;
		
		// affichage de la ressource
		$smarty->assign('photo',$photo);	
		
		// si l'utilisateur a le droit de modification on lui fourni une icon vers la page de modification
		if ($droitModification) {
			$smarty->assign('utilisateurConnu',true);
		}else{
			$smarty->assign('utilisateurConnu',false);
		}

		// quelques scripts utiles
		$additionalHeader = "
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/regal.css\" media=\"screen\" />
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/regal_print.css\" media=\"print\" />
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
			<script src=\"http://maps.google.com/maps?file=api&v=2.x&key=".$googleMapsKey."\" type=\"text/javascript\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/wms236.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/photo_seule.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/global.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/photo.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		// certains formats ne sont jamais inclu dans un thème
		// affiche l'image brute
		if ($outputFormat=='jpg' or $outputFormat=='gif' or $outputFormat=='png' or $outputFormat=='JPG') {
			
			$filename = $photo['lien'];
			
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
			$smarty->display("photo_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("photo_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"photo_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}
		} // if format = jpg..

	
	// un groupe de ressources
	}else{
		
		// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
		//http://yopyop.ch/photo/    => va afficher la liste de toutes les photos.
		if (empty($tags)) {
			$tousPhotos = $photoManager->getPhotos();
			
			$photos = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getPhotos()... et array_intersect
			foreach ($tousPhotos as $key => $aPhoto) {
				$photo = $aPhoto;
				
				// externe = 1 => photo hébergée sur picasaweb
				// externe = 2 => photo hébergée sur facebook

				if ($aPhoto['externe']=='1') {
					// déduit les chemin d'accès des photos moyenne et des vignette et les ajoute à la disposition de smarty
					$photo['lienVignette'] = $photoManager->getLienVignettePicasa($aPhoto['lien']);
					$photo['lienMoyenne'] = $photoManager->getLienMoyennePicasa($aPhoto['lien']);
				}elseif ($aPhoto['externe']=='2') {
					// déduit les chemin d'accès des photos moyenne et des vignette et les ajoute à la disposition de smarty
					$photo['lienVignette'] = $photoManager->getLienVignetteFacebook($aPhoto['lien']);
					$photo['lienMoyenne'] = $aPhoto['lien']; // Sur facebook les images en grande taille sont de la même taille que les moyennes habituelles. Donc on donne comme lien de moyenne la grande taille!
				}else{
					// déduit les chemin d'accès des photos moyenne et des vignette et les ajoute à la disposition de smarty
					$photo['lienVignette'] = $photoManager->getLienVignette($aPhoto['lien']);
					$photo['lienMoyenne'] = $photoManager->getLienMoyenne($aPhoto['lien']);
				}
				
				
				
				// obtients un tableau avec la liste des mots-clés attribué à l'image
				$motCles = $groupeManager->getMotCleElement($aPhoto['id_photo'],'photo');

				$listeMotCle= '';
				foreach ($motCles as $motCle => $occurence){
					// si le mot clé est un "prénom nom", le découpe et ne prend que le prénom pour des raisons d'anonymat sur google
					$motCleEnpartie = explode(" ", $motCle);
					$prenom = $motCleEnpartie[0];
					$listeMotCle = $listeMotCle.$prenom.' ';
				}

				// fourni pour smarty une chaine de caractère avec la liste des tags (offuscé pour les nom de famille)
				$photo['listeTags'] = $listeMotCle;
				$photos[$aPhoto['id_photo']] = $photo;		
			}
			
		}else{
		
			 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
			$taggedElements = $groupeManager->getElementByTags($tags,'photo');
		
			$photos = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getPhotos()... et array_intersect
			foreach ($taggedElements as $key => $idPhoto) {
				$photos[$idPhoto] = $photoManager->getPhoto($idPhoto);
				
				// externe = 1 => photo hébergée sur picasaweb
				// externe = 2 => photo hébergée sur facebook

				if ($photos[$idPhoto]['externe']=='1') {
					// déduit les chemin d'accès des photos moyenne et des vignette et les ajoute à la disposition de smarty
					$photos[$idPhoto]['lienVignette'] = $photoManager->getLienVignettePicasa($photos[$idPhoto]['lien']);
					$photos[$idPhoto]['lienMoyenne'] = $photoManager->getLienMoyennePicasa($photos[$idPhoto]['lien']);
				}elseif ($photos[$idPhoto]['externe']=='2') {
					// déduit les chemin d'accès des photos moyenne et des vignette et les ajoute à la disposition de smarty
					$photos[$idPhoto]['lienVignette'] = $photoManager->getLienVignetteFacebook($photos[$idPhoto]['lien']);
					$photos[$idPhoto]['lienMoyenne'] = $photos[$idPhoto]['lien']; // Sur facebook les images en grande taille sont de la même taille que les moyennes habituelles. Donc on donne comme lien de moyenne la grande taille!
				}else{
					// déduit les chemin d'accès des photos moyenne et des vignette et les ajoute à la disposition de smarty
					$photos[$idPhoto]['lienVignette'] = $photoManager->getLienVignette($photos[$idPhoto]['lien']);
					$photos[$idPhoto]['lienMoyenne'] = $photoManager->getLienMoyenne($photos[$idPhoto]['lien']);
				}
				

				// obtients un tableau avec la liste des mots-clés attribué à l'image
				$motCles = $groupeManager->getMotCleElement($idPhoto,'photo');

				$listeMotCle= '';
				foreach ($motCles as $motCle => $occurence){
					// si le mot clé est un "prénom nom", le découpe et ne prend que le prénom pour des raisons d'anonymat sur google
					$motCleEnpartie = explode(" ", $motCle);
					$prenom = $motCleEnpartie[0];
					$listeMotCle = $listeMotCle.$prenom.' ';
				}

				// fourni pour smarty une chaine de caractère avec la liste des tags (offuscé pour les nom de famille)
				$photos[$idPhoto]['listeTags'] = $listeMotCle;
			}
		} // if $tags
		
		// supprime les \
		stripslashes_deep($photos);
		
		// transmets les ressources à smarty
		$smarty->assign('photos',$photos);
		
		// url du flux atom pour suivre les photos de cette catégorie
		$urlFlux = "http://".$serveur."/photo/".trim($ressourceTags,"/")."/flux.xml";

		// <link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/lightbox.css\" media=\"screen\" />
		// <script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
		// <script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/prototype.js\"></script>
		// <script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/scriptaculous.js\"></script>
		// <script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/lightbox.js\"></script>

		// quelques scripts utiles
		$additionalHeader = "
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/regal.css\" media=\"screen\" />
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/regal_print.css\" media=\"print\" />
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/shadowbox.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/photo.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/global.js\"></script>
			<script type=\"text/javascript\">
			Shadowbox.loadSkin('classic', 'http://".$serveur."/utile/js/shadowbox/src/skin');
			Shadowbox.loadLanguage('fr', 'http://".$serveur."/utile/js/shadowbox/build/lang');
			Shadowbox.loadPlayer(['img', 'flv'], 'http://".$serveur."/utile/js/shadowbox/build/player');
			window.onload = Shadowbox.init;
			</script>
			<link rel=\"alternate\" type=\"application/atom+xml\" title=\"Atom\" href=\"".$urlFlux."\" />";
				
		$smarty->assign('additionalHeader',$additionalHeader);

		if ($outputFormat=='xml') {
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("photo_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("photo_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"photo_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
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
	
		// obtient les données de la photos qui peuvent apparaître dans formulaire d'ajout de photo. Si rien est fourni le déduit à partir des données et métadonnées du fichier.
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
		// lat long peut être utile si l'on propose une carte où indiquer le lieu de prise de vue de l'image dans le formulaire d'ajout.
		if(isset($_POST['latitude'])){
			$latitudePost = $_POST['latitude'];
		}else{
			$latitudePost ='';
		}
		if(isset($_POST['longitude'])){
			$longitudePost = $_POST['longitude'];
		}else{
			$longitudePost ='';
		}
	
		// tableau contenant les éventuelles erreur
		$errorMsg = array();
	
		// si un fichier est envoyé d'un formulaire appelé "image" et qu'il possède un nom et est de type mime image/ ....
		if (isset($_FILES['image']) && !empty($_FILES['image']['name']) && preg_match("|^image/|i",$_FILES['image']['type'])) {
			$nomOriginal = $_FILES['image']['name'];
			$nomSimplifie = $photoManager->simplifieNomFichier($nomOriginal);
			$date = date('Y_m_d_H_i_');   // Le string est du type 2003_05_31_21_05_  pour le 31 mai 2003  21h05  ...permet de trier les fichiers par date, mais selon l'ordre alphabétique.
		
			// déplace le fichier de son emplacement temporaire au dossier divers ou il va résider
			// on ajoute la date comme préfixe au fichier pour rendre unique un fichier et éviter d'écraser un autre.
			// on simplifie également le nom des fichier en remplacant les caractères avec des accents par les mêmes sans accents. On remplace aussi les ' et les espaces par des _
			if (!move_uploaded_file($_FILES['image']['tmp_name'],"utile/images/divers/".$date.$nomSimplifie)) {
				$errorMsg[] = "Erreur lors du déplacement du fichier de tmp à sa destination";
			}
			if (!file_exists("utile/images/divers/".$date.$nomSimplifie)) {
				$errorMsg[] = "Impossible de trouver le fichier qui vient d'être ajoutée";
			}
		
			// si tout se passe bien continue le boulot et si des erreurs sont arrivées les affiche.
			if (count($errorMsg)>0) {
				print_r($errorMsg);
			}else{
			
				// le chemin d'accès à l'imge est donc:
				$imagePath = "utile/images/divers/".$date.$nomSimplifie;

				// obtient diverses infos
				$orientation = $photoManager->getOrientationFromFile($imagePath);
				$datePriseDeVue = $photoManager->getExifDateFromFile($imagePath); // si aucun exif disponible retourne la date courante
				$position = $photoManager->getExifLatLongFromFile($imagePath); // si lat et long ne sont pas disponible retourne des string ''
				$latitude = $position['latitude'];
				$longitude = $position['longitude'];
				// si aucune valeur n'est présente dans le fichier, on prend celle fournie par post
				if (empty($latitude)) {
					$latitude = $latitudePost;
				}
				if (empty($longitude)) {
					$longitude = $longitudePost;
				}

				$photoManager->creeVignette($imagePath,250,188);
				$photoManager->creeMoyenne($imagePath); // et la taille et qualité par défaut 640,480,85

				// introduit dans la base une nouvelle photo et retourne le nouvel id créé
				$idPhoto = $photoManager->insertPhoto($nomOriginal,$description,$imagePath,'0',$orientation,$datePriseDeVue,$latitude,$longitude,'0');
			
				// obtient les mots clé IPTC dans le fichier de la photo et les utilises pour taguer celle-ci.
				$tags =  $photoManager->getIptcKeywordsFromFile($imagePath);
				foreach ($tags as $key => $motCle) {
					$groupeManager->ajouteMotCle($idPhoto, $motCle,'photo');
				}
			
				echo $idPhoto; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax	
			}
		}
			
		// redirige sur l'interface de modification de la photo pour ajouter le détail de celle-ci.
		$urlModif = "http://".$serveur."/photo/".$idPhoto."-".$nomSimplifie.".html?modify";
		header("Location: ".$urlModif);
		break;
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
	if(isset($_POST['compteur'])){
		$compteur = $_POST['compteur'];
	}else{
		$compteur ='';
	}
	if(isset($_POST['orientation'])){
		$orientation = $_POST['orientation'];
	}else{
		$orientation ='';
	}
	if(isset($_POST['date_prise_de_vue'])){
		$date_prise_de_vue = $_POST['date_prise_de_vue'];
	}else{
		$date_prise_de_vue ='';
	}
	// lat long peut être utile si l'on propose une carte où indiquer le lieu de prise de vue de l'image dans le formulaire d'ajout.
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
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='';
	}
	
	// si l'utilisateur est admin ou créateur de la photo
	if ($droitModification) {
	
		// fait la mise à jour
		$photoManager->updatePhoto($idPhoto,$nom,$description,$lien,$compteur,$orientation,$date_prise_de_vue,$latitude,$longitude,$evaluation);
	
	}

////////////////
////  DELETE
///////////////   /// TODO: attention.. avant de supprimer une ressource et il faut la détaguer !!  .. encore écrire le code !

}elseif ($action=='delete') {
	
	// si l'utilisateur est admin ou créateur de la photo
	if ($droitModification) {
		$photoManager->deletePhoto($idPhoto);
	}
	
////////////////
////  NEW
///////////////
}elseif ($action=='new') {
	
	
	// si l'utilisateur est connu
	if ($_SESSION['id_personne'] != '1') {
	
		// quelques scripts utiles
		$additionalHeader = "
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/datePicker.css\" media=\"screen\" />
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/photo.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);
	
		// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
		if ($theme=="no") {
			$smarty->display("photo_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
		}else{
			// affiche le formulaire de modification inclu dans le template du thème index.tpl
			$smarty->assign('contenu',"photo_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
			$smarty->display($theme."index.tpl");
		}
	}

////////////////
////  MODIFY
///////////////
}elseif ($action=='modify') {
	
	// si l'utilisateur est admin ou créateur de la photo
	if ($droitModification) {
	
		// va chercher les infos sur la ressource demandée
		$photo = $photoManager->getPhoto($idPhoto);
	
		// supprime les \
		stripslashes_deep($photo);
			
		// externe = 1 => photo hébergée sur picasaweb
		// externe = 2 => photo hébergée sur facebook
		
		if ($photo['externe']=='1') {
			// déduit les chemin d'accès des photos moyenne et des vignette et les ajoute à la disposition de smarty
			$photo['lienVignette'] = $photoManager->getLienVignettePicasa($photo['lien']);
			$photo['lienMoyenne'] = $photoManager->getLienMoyennePicasa($photo['lien']);
		}elseif ($photo['externe']=='2') {
			// déduit les chemin d'accès des photos moyenne et des vignette et les ajoute à la disposition de smarty
			$photo['lienVignette'] = $photoManager->getLienVignetteFacebook($photo['lien']);
			$photo['lienMoyenne'] = $photo['lien']; // Sur facebook les images en grande taille sont de la même taille que les moyennes habituelles. Donc on donne comme lien de moyenne la grande taille!
		}else{
			// déduit les chemin d'accès des photos moyenne et des vignette et les ajoute à la disposition de smarty
			$photo['lienVignette'] = $photoManager->getLienVignette($photo['lien']);
			$photo['lienMoyenne'] = $photoManager->getLienMoyenne($photo['lien']);
		}
	
		// détail de l'exif
		// $exif = $photoManager->getExif($photo['lien']);
		// 
		// $exifHtml = "<ul id=\"exif\">";
		// foreach ($exif as $key => $value) {
		// 	$exifHtml.= "<li>".$key.": ".$value."</li>";
		// }
		// $photo['exif'] = $exifHtml."</ul>";

	
		// obtients un tableau avec la liste des mots-clés attribué à l'image
		$motCles = $groupeManager->getMotCleElement($idPhoto,'photo');
	
		$listeMotCle= '';
		foreach ($motCles as $motCle => $occurence){
			// si le mot clé est un "prénom nom", le découpe et ne prend que le prénom pour des raisons d'anonymat sur google
			$motCleEnpartie = explode(" ", $motCle);
			$prenom = $motCleEnpartie[0];
			$listeMotCle = $listeMotCle.$prenom.' ';
		}
	
		// fourni pour smarty une chaine de caractère avec la liste des tags (offuscé pour les nom de famille)
		$photo['listeTags'] = $listeMotCle;
	
		// affichage de la ressource
		$smarty->assign('photo',$photo);	

		// quelques scripts utiles
		$additionalHeader = "
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/regal.css\" media=\"screen\" />
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/regal_print.css\" media=\"print\" />
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/tiny_mce/tiny_mce.js\"></script>
			<script src=\"http://maps.google.com/maps?file=api&v=2.x&key=".$googleMapsKey."\" type=\"text/javascript\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/wms236.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/photo.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/photo_edit.js\"></script>
			<script type=\"text/javascript\">startRichEditor();</script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

	
		// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
		if ($theme=="no") {
			$smarty->display("photo_modify_".LANG.".tpl"); // affichage de l'interface de modification de la ressource
		}else{
			// affiche le formulaire de modification inclu dans le template du thème index.tpl
			$smarty->assign('contenu',"photo_modify_".LANG.".tpl");
			$smarty->display($theme."index.tpl");
		}
	} // utilisateur connu

////////////////
////  NEW IMAGE formulaire destiné à être intégrer dans un popup de tinymce pour introduire une image directement dans le texte
///////////////
}elseif ($action=='newimage') {

	// quelques scripts utiles
	$additionalHeader = "
		<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/yop2.css\" media=\"screen\" />
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/photo.js\"></script>";	
	$smarty->assign('additionalHeader',$additionalHeader);

	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("photo_image_new_".LANG.".tpl"); // affichage de l'interface vide qui permet d'indiquer le nom d'un dossier du serveur.
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"photo_image_new_".LANG.".tpl");
		$smarty->display($theme."index.tpl");
	}


////////////////
////  ADD IMAGE dans le texte d'un document via tinymce
///////////////

}elseif ($action=='addimage') {

	if(isset($_POST['position'])){ 
		$position = $_POST['position'];
	}else{
		$position ='';
	}
	if(isset($_POST['description'])){
		$description = $_POST['description'];
	}else{
		$description ='';
	}
	if(isset($_POST['largeur'])){
		$largeur = $_POST['largeur'];
	}else{
		$largeur ='250';
	}
	if(isset($_POST['hauteur'])){
		$hauteur = $_POST['hauteur'];
	}else{
		$hauteur ='180';
	}
	
	// tableau contenant les éventuelles erreur
	$errorMsg = array();

	// si un fichier est envoyé d'un formulaire appelé "image" et qu'il possède un nom et est de type mime image/ ....
	if (isset($_FILES['image']) && !empty($_FILES['image']['name']) && preg_match("|^image/|i",$_FILES['image']['type'])) {
		$nomOriginal = $_FILES['image']['name'];
		$nomSimplifie = $photoManager->simplifieNomFichier($nomOriginal);
		$date = date('Y_m_d_H_i_');   // Le string est du type 2003_05_31_21_05_  pour le 31 mai 2003  21h05  ...permet de trier les fichiers par date, mais selon l'ordre alphabétique.

		// déplace le fichier de son emplacement temporaire au dossier divers ou il va résider
		// on ajoute la date comme préfixe au fichier pour rendre unique un fichier et éviter d'écraser un autre.
		// on simplifie également le nom des fichier en remplacant les caractères avec des accents par les mêmes sans accents. On remplace aussi les ' et les espaces par des _
		if (!move_uploaded_file($_FILES['image']['tmp_name'],"utile/images/divers/".$date.$nomSimplifie)) {
			$errorMsg[] = "Erreur lors du déplacement du fichier de tmp à sa destination";
		}
		if (!file_exists("utile/images/divers/".$date.$nomSimplifie)) {
			$errorMsg[] = "Impossible de trouver le fichier qui vient d'être ajoutée";
		}

		// si tout se passe bien continue le boulot et si des erreurs sont arrivées les affiche.
		if (count($errorMsg)>0) {
			print_r($errorMsg);
		}else{

			// le chemin d'accès à l'imge est donc:
			$imagePath = "utile/images/divers/".$date.$nomSimplifie;

			// obtient diverses infos
			$orientation = $photoManager->getOrientationFromFile($imagePath);
			$datePriseDeVue = $photoManager->getExifDateFromFile($imagePath); // si aucun exif disponible retourne la date courante
			$lieu = $photoManager->getExifLatLongFromFile($imagePath); // si lat et long ne sont pas disponible retourne des string ''
			$latitude = $lieu['latitude'];
			$longitude = $lieu['longitude'];

			$photoManager->creePerso($imagePath,$largeur,$hauteur);  // taille personnalisée. On la mets là dedans histoire de ne pas mélanger les tailles dans les autres dossiers. On a ainsi des galeries photos avce un design propre.
			$photoManager->creeVignette($imagePath,'250','188');  // 250x188 par défaut
			$photoManager->creeMoyenne($imagePath); // et la taille et qualité par défaut 640,480,85

			// introduit dans la base une nouvelle photo et retourne le nouvel id créé
			$idPhoto = $photoManager->insertPhoto($nomOriginal,$description,$imagePath,'0',$orientation,$datePriseDeVue,$latitude,$longitude,'');

			// si des mots clé iptc existent
			// obtient les mots clé IPTC dans le fichier de la photo et les utilises pour taguer celle-ci.  todo  .. à vérifier cette fonction avec attention
			$tags =  $photoManager->getIptcKeywordsFromFile($imagePath);
			if (!empty($tags)) {
				foreach ($tags as $key => $motCle) {
					$groupeManager->ajouteMotCle($idPhoto, $motCle,'photo');
				}
			}
			
			// permet de garder une trace de toutes les images qui sont utilisée dans des documents.
			$groupeManager->ajouteMotCle($idPhoto,'DansUnDocument','photo');
			
			
			$lienVignette = $photoManager->getLienPerso($imagePath);
			
			if ($position=='d') {
				$htmlImage = "'<a href=\"http://".$_SERVER["SERVER_NAME"]."/".$imagePath."\"><img src=\"http://".$_SERVER["SERVER_NAME"]."/".$lienVignette."\" alt=\"".$nomOriginal."\" style=\"float:right; margin: 0 0 1em 1em;\" /></a>'";
			}elseif ($position=='g') {
				$htmlImage = "'<a href=\"http://".$_SERVER["SERVER_NAME"]."/".$imagePath."\"><img src=\"http://".$_SERVER["SERVER_NAME"]."/".$lienVignette."\" alt=\"".$nomOriginal."\" style=\"float:left; margin: 0 1em 1em 0;\" /></a>'";
			}elseif ($position=='c') {
				$htmlImage = "'<a href=\"http://".$_SERVER["SERVER_NAME"]."/".$imagePath."\"><img src=\"http://".$_SERVER["SERVER_NAME"]."/".$lienVignette."\" alt=\"".$nomOriginal."\" style=\"display:block; margin:0 auto;\" /></a>'";
			}else{
				$htmlImage = "'<a href=\"http://".$_SERVER["SERVER_NAME"]."/".$imagePath."\"><img src=\"http://".$_SERVER["SERVER_NAME"]."/".$lienVignette."\" alt=\"".$nomOriginal."\"/></a>'";
			}
			
			
			// affiche très brièvement une page dont le seul but est d'appeler le script qui va insérer le contenu fourni dans tinymce. Puis le popup se ferme.
			$htmlDebut = <<<END

				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
					"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

				<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
					<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
						<script src="//$serveur/utile/js/tiny_mce/tiny_mce_popup.js" type="text/javascript" charset="utf-8" ></script>

END;
			echo $htmlDebut;
			echo "<script type=\"text/javascript\">\n";			
			echo "var html = ".$htmlImage.";";
			echo "tinyMCEPopup.execCommand(\"mceInsertContent\", false, html);";
			echo "tinyMCEPopup.close();";
			echo "</script>\n";
			echo "<title>Ajout d'image</title></head>";
			echo "<body>";
			echo "<h3 class=\"ok\">Image uploadée avec succès</h3>";
			echo "<p>";
			echo "<legend>nouvel id </legend>";
			echo $idPhoto; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax	
			echo "</p>";
			echo "</body></html>";
			
			
		}
	}



////////////////
////  NEW ALBUM  Fourni une interface pour indiquer un album contenant des photos a indexer en masse
///////////////
}elseif ($action=='newalbum') {

	// si l'utilisateur est connu
	if ($_SESSION['id_personne'] != '1') {
		
		// quelques scripts utiles
		$additionalHeader = "
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/datePicker.css\" media=\"screen\" />
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/photo.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
		if ($theme=="no") {
			$smarty->display("photo_album_new_".LANG.".tpl"); // affichage de l'interface vide qui permet d'indiquer le nom d'un dossier du serveur.
		}else{
			// affiche le formulaire de modification inclu dans le template du thème index.tpl
			$smarty->assign('contenu',"photo_album_new_".LANG.".tpl");
			$smarty->display($theme."index.tpl");
		}
	}// utilisateur connu

////////////////
////  ADD ALBUM  Permet d'ajouter des photos en masse depuis un dossier sur le serveur.
///////////////

}elseif ($action=='addalbum') {
	
	// si l'utilisateur est connu
	if ($_SESSION['id_personne'] != '1') {
	
		// chemin d'accès depuis la racine du site, du dossier qui contient les photos
		if(isset($_POST['folderpath'])){
			$folderPath = $_POST['folderpath'];
		}else{
			$folderPath ='';
		}

		// tag que l'on peut attribuer a chaque photo, ceci sert de nom d'album. Par ex: coursCP2008
		if(isset($_POST['albumtag'])){
			$albumTag = $_POST['albumtag'];
		}else{
			$albumTag ='';
		}

		if (!empty($folderPath)) {
			// ajoute un / à la fin du chemin du dossier s'il n'y a pas. 
			if (($folderPath[strlen($folderPath)-1])!='/'){
				$folderPath .= '/';
			}
			// arrête tout si le dossier n'est pas accessible en ériture
			if(!is_writable($folderPath)){
				echo "<div id=\"cadre_admin\">";
				echo "<p class=\"erreur\">Le dossier <em>".$folderPath."</em> n'est pas accessible en écriture !</p>";
				echo "<a href=\"javascript:history.back()\">retour au formulaire</a>";
				echo "</div>"; // cadre_admin
				exit(0);
			}
		
			// Place tous les noms des fichiers jpg, png et gif dans le tableau $fichiers
			$tasDeFichiers = opendir($folderPath);  // Ouverture du dossier contenant les photos
	    	while ($fichier=readdir($tasDeFichiers)){ 
    		 	
	    		set_time_limit(300);  // défini une limite de temps à l'exécution du script. Remis à 0 à chaque appel de la fonction. Ainsi globalement le script peut s'executer pendant plusieurs minutes !
	    		$extension = strrchr(strtolower($fichier), '.');
    		 	
	    		// Filtre les fichiers par extension. Ne prend que les fichiers jpg, png et gif
	   			if (( $extension == '.jpg') or( $extension == '.gif') or( $extension == '.png')){
	    			$fichiers[] = $fichier;   // tous les noms de fichiers sont dans le tableau $fichiers
	    		}
	    	} 
	    	closedir($tasDeFichiers);
		
			// Continue l'exécution du script même si le client s'est déconnecté.
			// Permet de lancer l'indexation d'un dossier et de se déconnecter avant que tous le dossier ai été traité !
			ignore_user_abort(1);
		
			// traite tous les fichiers
			foreach ($fichiers as $fichier){
				set_time_limit(300);  // défini une limite de temps à l'exécution du script. Remis à 0 à chaque appel de la fonction
    	 	
				// Renome le fichier avec un nom simplifié
				// crée une vignette et une moyenne
				// va chercher les info d'orientation date de création, latitude longitude dans le fichier
				// Crée un nouvel enregistrement dans la base avec toutes ces infos et retourne l'id de cet enregistrement.
				$idPhoto = $photoManager->indexPhotoFromFile($folderPath,$fichier);  // on transmet le chemin d'accès du dossier et le nom original du fichier.  ex: utile/images/divers/ et "vache à l'alpage.jpg"
			
				// obtient les mots clés IPTC dans le fichier de la photo et les utilises pour taguer celle-ci.
				$tags =  $photoManager->getIptcKeywordsFromFile($folderPath.$fichier);
			
				if (!empty($tags)) {
					foreach ($tags as $key => $motCle) {
						$groupeManager->ajouteMotCle($idPhoto, $motCle,'photo');
					}
				}
			
				// si celui-ci n'est pas vide, ajoute le tag qui va désigner l'album photo
				if (!empty($albumTag)) {
					$groupeManager->ajouteMotCle($idPhoto,$albumTag,'photo');
				}
			
				// comme le script est long, on permet de vérifier l'avancement du boulot en temps réel.
				echo "<br />Insertion réussie de l'image: ".$folderPath.$fichier." avec l'id: ".$idPhoto;
				print str_repeat(" ", 4096);	// force un flush  (permet d'afficher en temps réel le déroulement du programme)    	 	    		
			
			} // foreach fichier
			echo "<br />Indexation réussie du dossier !";
			print str_repeat(" ", 4096);	// force un flush  (permet d'afficher en temps réel le déroulement du programme) todo.. ne marche pas certainement à cause de la compression faite avec le tampon de sortie
		
		}else{
			echo "aucun chemin d'accès fourni";
		}
	}// utilisateur connu



////////////////
////  NEW ALBUM PICASA  Fourni une interface pour ajouter un album de photo hébergé sur picasaweb
///////////////
}elseif ($action=='newalbumpicasa') {

	// si l'utilisateur est connu
	if ($_SESSION['id_personne'] != '1') {

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/photo.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
		if ($theme=="no") {
			$smarty->display("photo_album_picasa_new_".LANG.".tpl"); // affichage de l'interface vide qui permet d'indiquer le nom d'un dossier du serveur.
		}else{
			// affiche le formulaire de modification inclu dans le template du thème index.tpl
			$smarty->assign('contenu',"photo_album_picasa_new_".LANG.".tpl");
			$smarty->display($theme."index.tpl");
		}
	}// utilisateur connu


////////////////
////  ADD galerie picasa
///////////////

}elseif ($action=='addpicasa') {

	// si l'utilisateur est connu
	if ($_SESSION['id_personne'] != '1') {

		// url du flux rss d'un album picasaweb
		if(isset($_POST['urlpicasa'])){
			$urlPicasa = $_POST['urlpicasa'];
		}else{
			$urlPicasa ='';
		}

		// tag que l'on peut attribuer a chaque photo, ceci sert de nom d'album. Par ex: coursCP2008
		if(isset($_POST['albumtag'])){
			$albumTag = $_POST['albumtag'];
		}else{
			$albumTag ='';
		}

		if (!empty($urlPicasa)) {
			
			// va chercher les données du flux
			$xml = simplexml_load_file($urlPicasa);
			
			$vignetteChoisie = false;
			
			// pour chaque item du flux crée une photo
			foreach ($xml->channel->item as $item) {
				
				$url = (string) $item->enclosure['url'];
				$nomOriginal = (string) $item->title;
				$date = (string) $item->pubDate;
				$datePriseDeVue = date('Y-m-d H:i:s', strtotime($date)); // la date est la date de publication de la photo sur picasa
				
				// introduit dans la base une nouvelle photos et retourne le nouvel id créé
				// on précise que la photo est externe
				$idPhoto = $photoManager->insertPhoto($nomOriginal,'',$url,'0','h',$datePriseDeVue,'','','0','1');
			//	echo "<br />ajoute: ",$nomOriginal," ",''," ",$url," ",'0'," ",'h'," ",$datePriseDeVue," ",''," ",''," ",'0'," ",'1';
				
				// si celui-ci n'est pas vide, ajoute le tag qui va désigner l'album photo
				if (!empty($albumTag)) {
					$groupeManager->ajouteMotCle($idPhoto,$albumTag,'photo');
				//	echo "<br />ajoute tag: ",$albumTag; //ici
				}
				
				// si l'on a pas encore choisi la vignette de la galerie il faut le faire. On prend la première photo de la galerie.
				if (!$vignetteChoisie) {
					// obtient l'url de la vignette à partir de l'url de la photo
					
					$urlVignetteGalerie = $photoManager->getLienVignettePicasa($url);
					$vignetteChoisie = true;
				}
				
			}
			
			echo "<p class=\"ok\">Indexation réussie de la galerie picasa !</p>";
			echo "<p><a href=\"//" . $serveur . "/photo/".$albumTag."/\">Voir la galerie...</a></p>";
			
			///////////////////////
			// Ajout d'un lien et vignette sur la galerie de photo sur la page prévue pour qui est visible sur la page d'accueil
			
			$idPageGalerie = 91;
			$urlGalerie = "http://".$serveur."/photo/".$albumTag."/";
			
			// va chercher le contenu de la page
			$documentGalerie = $documentManager->getDocument($idPageGalerie);
			$contenuExistant = stripcslashes($documentGalerie['contenu']);
			
			// Dans le nouveu contenu on ajoute une vignette et un lien sur la galerie au début du contenu existant.
			$nouveauContenu = "<div class=\"lienGaleriePicasa\"><a href=\"".$urlGalerie."\"><img src=\"".$urlVignetteGalerie."\" alt=\"vignette galerie\" title=\"voir la galerie...\" /></a></div>".$contenuExistant;
			
			// on crée une nouvelle version de la page galerie (nom et description son vide.. comme ça on ne les modifie pas.. et les params suivants aussi)
			$documentManager->updateDocument($idPageGalerie,'','',$nouveauContenu,'','','','','');
			
		}else{
			echo "aucune adresse de flux picasa fournie";
		}
	}// utilisateur connu	
		
} // toutes les actions
?>