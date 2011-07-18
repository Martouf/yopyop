<?php
/*******************************************************************************************
 * Nom du fichier		: objet.php
 * Date					: 28 juilett 2010
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Permet de gérer des objets. Ce fichier génère des vues pour gérer des objets
 *******************************************************************************************
 * Interface qui permet d'afficher une objet ou l'interface de modification d'une objet
 * 
 * On utilise ce fichier via la réécriture d'url.
 * URL pour lire, ajouter, modifier ou supprimer une ressource. Les paramètres supplémentaires sont envoyés par POST:
 * http://yopyop.ch/objet/28-momo.html  (get)
 * http://yopyop.ch/objet/objet.html?add
 * http://yopyop.ch/objet/28-momo.html?update
 * http://yopyop.ch/objet/28-momo.html?delete
 *
 * URL pour afficher des vues utiles aux actions demandées:
 * http://yopyop.ch/objet/objet.html?new   => fourni une interface vide de création qui peut être utilisée pour soumettre un nouvel élément à l'url add
 * http://yopyop.ch/objet/28-momo.html?modify   => fourni une interface de modification avec les données de l'élément qui peut être utilisée pour soumettre les modifications à l'url update
 * http://yopyop.ch/objet/?name  => (à voir si cette url fonctionne vraiment) fourni la liste des nom des ressources
 */


/*
 *  Attention, il faut encore mettre en place la gestion des permissions !!!
 *
 */

// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idObjet = $ressourceId;

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

// va chercher les tags qui sont liés à l'objet courant !
$motsClesElement = $groupeManager->getMotCleElement($idObjet,'objet'); // il y a certainement moyen d'optimiser tout ça... on a pas besoin d'avoir le nombre d'oocurence de l'utilisation du mot clé !! Peut être mettre une autre fonction !!

// transforme un tableau avec le mot clé et ses occurences en liste séparée par des virgules
$tagsVirgules = implode(',',array_keys($motsClesElement));
$smarty->assign('tags',$tagsVirgules);


// on défini ensuite les différentes actions possible.
// Pour l'action get, il y a plusieurs formats de sortie possibles. Le template est choisi en conséqunce. Pour le format pdf, le choix est fait en amont par index.php qui va fournir à princeXML le fichier html correspondant.

$droitModification = false;
if (!empty($idObjet)) {
	// pour modifier une objet, il faut être le créateur de celui-ci ou un admin.
	$objetAModifier = $objetManager->getObjet($idObjet);
	$createur = $objetAModifier['createur'];
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
	if (!empty($idObjet)) {
		
		// va chercher les infos sur la ressource demandée
		$objet = $objetManager->getObjet($idObjet);
		$objet['nomSimplifie'] = simplifieNom($objet['nom']);
		
		// supprime les \
		stripslashes_deep($objet);
		
		// // obtients un tableau avec la liste des mots-clés attribué à l'objet
		// 		$motCles = $groupeManager->getMotCleElement($idObjet,'objet');
		// 		
		// 		$listeMotCle= '';
		// 		foreach ($motCles as $motCle => $occurence){
		// 			// si le mot clé est un "prénom nom", le découpe et ne prend que le prénom pour des raisons d'anonymat sur google
		// 			$motCleEnpartie = explode(" ", $motCle);
		// 			$prenom = $motCleEnpartie[0];
		// 			$listeMotCle = $listeMotCle.$prenom.' ';
		// 		}
		// 		
		// 		// fourni pour smarty une chaine de caractère avec la liste des tags (offuscé pour les nom de famille)
		// 		$objet['listeTags'] = $listeMotCle;
		
		// obtients un tableau avec la liste des mots-clés attribué à l'objet
		$motCles = $groupeManager->getMotCleElement($idObjet,'objet');

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
		$objet['listeTags'] = $listeMotCle;
		
		// infos à propos du propriétaire
		$proprietaire = $personneManager->getPersonne($objet['id_proprietaire']);
		$objet['proprietaire'] = $proprietaire;
				
		// affichage de la ressource
		$smarty->assign('objet',$objet);
		
		// fourni les infos sur l'image de présentation.
		$imagePresentation = $photoManager->getPhoto($objet['id_image']);
		$imagePresentation['lienVignette'] = $photoManager->getLienVignette($imagePresentation['lien']);
		$imagePresentation['lienMoyenne'] = $photoManager->getLienMoyenne($imagePresentation['lien']);
		$smarty->assign('imagePresentation',$imagePresentation);	
		
		// si l'utilisateur a le droit de modification on lui fourni une icon vers la page de modification
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
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/shadowbox.js\"></script>
			<script src=\"http://maps.google.com/maps?file=api&v=2.x&key=".$googleMapsKey."\" type=\"text/javascript\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/wms236.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/photo_seule.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/global.js\"></script>
			<script type=\"text/javascript\">
			Shadowbox.loadSkin('classic', 'http://".$serveur."/utile/js/shadowbox/src/skin');
			Shadowbox.loadLanguage('fr', 'http://".$serveur."/utile/js/shadowbox/build/lang');
			Shadowbox.loadPlayer(['img', 'flv'], 'http://".$serveur."/utile/js/shadowbox/build/player');
			window.onload = Shadowbox.init;
			</script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/objet.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		// certains formats ne sont jamais inclu dans un thème
		if ($outputFormat=='xml') {			
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("objet_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("objet_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"objet_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}
		} // if format = xml

	
	// un groupe de ressources
	}else{
		
		// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
		//http://yopyop.ch/objet/    => va afficher la liste de toutes les objets.
		if (empty($tags)) {
			
			// on ne publie au public que les objets qui sont disponibles (etat=1) donc pas les objets encore en cours de création ou les objets privés
			if ($_SESSION['rang']=='1') {
				$filtreObjets = array(); // on ne veut que les objets de la personne dont on affiche le profile
			}else{
				$filtreObjets = array('etat'=>'1'); // on ne veut que les objets de la personne dont on affiche le profile qui sont publié (etat=1)
			}
				
			$tousObjets = $objetManager->getObjets($filtreObjets,'nom'); // tous
		//	$tousObjets = $objetManager->getObjets(array(),'nom desc limit 1'); // seulement 1 et filtré par nom inverses.. (bref un peu les possibilités de la chose)
			
			$objets = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getObjets()... et array_intersect
			foreach ($tousObjets as $key => $aObjet) {
				$objet = $aObjet;
					
				// obtients un tableau avec la liste des mots-clés attribué à l'image
				$motCles = $groupeManager->getMotCleElement($aObjet['id_objet'],'objet');

				$listeMotCle= '';
				$premier = true;
				foreach ($motCles as $motCle => $occurence){
					if (!$premier) {
						$listeMotCle .=', ';
					}
				//	$listeMotCle .= $motCle;
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
			
		}else{
		
			 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
			$taggedElements = $groupeManager->getElementByTags($tags,'objet');
		
			$objets = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getObjets()... et array_intersect
			foreach ($taggedElements as $key => $idObjet) {
				$newObjet = $objetManager->getObjet($idObjet);
				// on affiche que les objets qui sont publiés
				if ($newObjet['etat']==1) {
					$objets[$idObjet] = $newObjet;
					$objets[$idObjet]['nomSimplifie'] = simplifieNom($objets[$idObjet]['nom']);

					// obtients un tableau avec la liste des mots-clés attribué à l'image
					$motCles = $groupeManager->getMotCleElement($idObjet,'objet');

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
					$image = $photoManager->getPhoto($objets[$idObjet]['id_image']);
					$image['lienVignette'] = $photoManager->getLienVignette($image['lien']);
					$image['lienMoyenne'] = $photoManager->getLienMoyenne($image['lien']);
					$objets[$idObjet]['image'] = $image;

					// infos à propos du propriétaire
					$proprietaire = $personneManager->getPersonne($objets[$idObjet]['createur']);
					$objets[$idObjet]['proprietaire'] = $proprietaire;

					// fourni pour smarty une chaine de caractère avec la liste des tags
					$objets[$idObjet]['listeTags'] = $listeMotCle;
				} // si etat=1
			}
		} // if $tags
		
		// supprime les \
		stripslashes_deep($objets);
		
		// transmets les ressources à smarty
		$smarty->assign('objets',$objets);
		
		// url du flux atom pour suivre les objets de cette catégorie
		$urlFlux = "http://".$serveur."/objet/".trim($ressourceTags,"/")."/flux.xml";

		// <link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/lightbox.css\" media=\"screen\" />
		// <script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
		// <script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/prototype.js\"></script>
		// <script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/scriptaculous.js\"></script>
		// <script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/lightbox.js\"></script>

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/shadowbox.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
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
			$smarty->display("objet_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("objet_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"objet_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
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
	
		// obtient les données de l'objet qui peuvent apparaître dans formulaire d'ajout d'objet.
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
		if(isset($_POST['url'])){
			$url = $_POST['url'];
		}else{
			$url ='';
		}
		if(isset($_POST['id_proprietaire'])){
			$id_proprietaire = $_POST['id_proprietaire'];
		}else{
			$id_proprietaire ='';
		}
		// si aucun calendrier n'est passé en paramètre, crée un nouveau calendrier pour les réservations du nouvel objet
		if(isset($_POST['id_calendrier'])){
			$id_calendrier = $_POST['id_calendrier'];
		}else{
			$couleurCalendrier = substr(md5($nom),0,6); // obtient une chaine hexadécimal de 6 caractère qui est unique en fonction du nom.
			$id_calendrier = $calendrierManager->insertCalendrier($nom,"Calendrier des réservations de ".$nom,$couleurCalendrier,'0','','reservation');
		}
		if(isset($_POST['prix'])){
			$prix = $_POST['prix'];
		}else{
			$prix ='0';
		}
		if(isset($_POST['caution'])){
			$caution = $_POST['caution'];
		}else{
			$caution ='0';
		}
		// lat long peut être utile si l'on propose une carte où indiquer le lieu
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
		if(isset($_POST['lieu'])){
			$lieu = $_POST['lieu'];
		}else{
			$lieu ='';
		}
		if(isset($_POST['etat'])){
			$etat = $_POST['etat'];
		}else{
			$etat ='0'; // 1 => disponible / 0=> objet inactif, plus disponible // à la création l'objet est inactif pour avoir le temps de le finaliser.
		}
		if(isset($_POST['duree_max'])){
			$duree_max = $_POST['duree_max'];
		}else{
			$duree_max ='0';
		}
		if(isset($_POST['duree_min'])){
			$duree_min = $_POST['duree_min'];
		}else{
			$duree_min ='0';
		}
	
		// tableau contenant les éventuelles erreur
		$errorMsg = array();
		
		if(isset($_POST['id_image'])){
			$id_image = $_POST['id_image'];
		}else{
			$id_image ='';
			
			// si un fichier est envoyé d'un formulaire appelé "image" et qu'il possède un nom et est de type mime image/ ....
			if (isset($_FILES['image']) && !empty($_FILES['image']['name']) && preg_match("|^image/|i",$_FILES['image']['type'])) {
				$nomOriginal = $_FILES['image']['name'];
				$nomSimplifie = $photoManager->simplifieNomFichier($nomOriginal);
				$date = date('Y_m_d_H_i_');   // Le string est du type 2003_05_31_21_05_  pour le 31 mai 2003 à 21h05  ...permet de trier les fichiers par date, mais selon l'ordre alphabétique.

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
					$latitudeExif = $position['latitude'];
					$longitudeExif = $position['longitude'];
					// si aucune valeur n'est présente dans le fichier, on prend celle fournie par post
					if (!empty($latitudeExif)) {
						$latitude = $latitudeExif;
					}
					if (!empty($longitudeExif)) {
						$longitude = $longitudeExif;
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

					$id_image = $idPhoto;
				} // si pas d'erreur
			} // si un fichier est envoyé			
		} // if post id_image
		
		// ajoute l'objet dans la base le tout lié avec les objets calendrier et image créé au besoin pour l'occasion.
		$idNewObjet = $objetManager->insertObjet($nom,$description,$url,$id_proprietaire,$id_image,$id_calendrier,$prix,$caution,$latitude,$longitude,$etat,$duree_max,$duree_min);
		echo $idNewObjet; // au cas où
		
		$nomSimplifieObjet = simplifieNom($nom);
		
		// redirige sur l'interface de modification de l'objet pour ajouter le détail de celui-ci.
		$urlModif = "http://".$serveur."/objet/".$idNewObjet."-".$nomSimplifieObjet.".html?modify";
		header("Location: ".$urlModif);
		break;
		
	}// utilisateur connu 

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
	if(isset($_POST['url'])){
		$url = $_POST['url'];
	}else{
		$url ='';
	}
	if(isset($_POST['id_proprietaire'])){
		$id_proprietaire = $_POST['id_proprietaire'];
	}else{
		$id_proprietaire ='';
	}
	if(isset($_POST['id_image'])){
		$id_image = $_POST['id_image'];
	}else{
		$id_image ='';
	}
	if(isset($_POST['id_calendrier'])){
		$id_calendrier = $_POST['id_calendrier'];
	}else{
		$id_calendrier ='';
	}
	if(isset($_POST['prix'])){
		$prix = $_POST['prix'];
	}else{
		$prix ='';
	}
	if(isset($_POST['caution'])){
		$caution = $_POST['caution'];
	}else{
		$caution ='';
	}
	// lat long peut être utile si l'on propose une carte où indiquer le lieu
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
	if(isset($_POST['lieu'])){
		$lieu = $_POST['lieu'];
	}else{
		$lieu ='';
	}
	if(isset($_POST['etat'])){
		$etat = $_POST['etat'];
	}else{
		$etat ='';
	}
	if(isset($_POST['duree_max'])){
		$duree_max = $_POST['duree_max'];
	}else{
		$duree_max ='';
	}
	if(isset($_POST['duree_min'])){
		$duree_min = $_POST['duree_min'];
	}else{
		$duree_min ='';
	}
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='';
	}
	
	// si l'utilisateur est admin ou créateur de l'objet
	if ($droitModification) {
		// fait la mise à jour
		$objetManager->updateObjet($idObjet,$nom,$description,$url,$id_proprietaire,$id_image,$id_calendrier,$prix,$caution,$latitude,$longitude,$lieu,$etat,$duree_max,$duree_min,$evaluation);
		echo "ok";		
	}else{
		echo "vous n'avez pas les droits nécessaire pour modifier cet objet.";
	}

////////////////
////  DELETE
///////////////   /// TODO: attention.. avant de supprimer une ressource et il faut la détaguer !!  .. encore écrire le code !

}elseif ($action=='delete') {
	
	// si l'utilisateur est admin ou créateur de la objet
	if ($droitModification) {
		$objetManager->deleteObjet($idObjet);
	}
	
////////////////
////  NEW
///////////////
}elseif ($action=='new') {
	
	
	// si l'utilisateur est connu
	if ($_SESSION['id_personne'] != '1') {
		
		// // crée un objet de base et redirige sur l'interface de modification de cet objet. (il faudra juste faire attention aux objet fantome des gens qui ont testé l'interface et annulé !)
		// 		// par défaut l'état de l'objet est mis à inactif !
		// 		$idNewObjet = $objetManager->insertObjet('mon objet','un superbe objet');
		// 		
		// 		$url = "http://".$serveur."/objet/".$idNewObjet."-mon-nouvel-objet.html?modify";
		// 		header("Location: ".$url);
		// 		break;
	
		// quelques scripts utiles
		$additionalHeader = "
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/datePicker.css\" media=\"screen\" />
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);
	
		// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
		if ($theme=="no") {
			$smarty->display("objet_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
		}else{
			// affiche le formulaire de modification inclu dans le template du thème index.tpl
			$smarty->assign('contenu',"objet_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
			$smarty->display($theme."index.tpl");
		}
	}

////////////////
////  MODIFY
///////////////
}elseif ($action=='modify') {
	
	// si l'utilisateur est admin ou créateur de l'objet
	if ($droitModification) {
	
		// va chercher les infos sur la ressource demandée
		$objet = $objetManager->getObjet($idObjet);
		$objet['nomSimplifie'] = simplifieNom($objet['nom']);
	
		// supprime les \
		stripslashes_deep($objet);
	
		// obtients un tableau avec la liste des mots-clés attribué à l'image
		$motCles = $groupeManager->getMotCleElement($idObjet,'objet');
	
		$listeMotCle= '';
		foreach ($motCles as $motCle => $occurence){
			// si le mot clé est un "prénom nom", le découpe et ne prend que le prénom pour des raisons d'anonymat sur google
			$motCleEnpartie = explode(" ", $motCle);
			$prenom = $motCleEnpartie[0];
			$listeMotCle = $listeMotCle.$prenom.' ';
		}
	
		// fourni pour smarty une chaine de caractère avec la liste des tags (offuscé pour les nom de famille)
		$objet['listeTags'] = $listeMotCle;
	
		// affichage de la ressource
		$smarty->assign('objet',$objet);
		
		// fourni les infos sur l'image de présentation.
		$imagePresentation = $photoManager->getPhoto($objet['id_image']);
		$imagePresentation['lienVignette'] = $photoManager->getLienVignette($imagePresentation['lien']);
		$imagePresentation['lienMoyenne'] = $photoManager->getLienMoyenne($imagePresentation['lien']);
		$smarty->assign('imagePresentation',$imagePresentation);

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/tiny_mce/tiny_mce.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/shadowbox.js\"></script>
			<script src=\"http://maps.google.com/maps?file=api&v=2.x&key=".$googleMapsKey."\" type=\"text/javascript\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/wms236.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/objet.js\"></script>
			<script type=\"text/javascript\">startRichEditor();</script>
				<script type=\"text/javascript\">
				Shadowbox.loadSkin('classic', 'http://".$serveur."/utile/js/shadowbox/src/skin');
				Shadowbox.loadLanguage('fr', 'http://".$serveur."/utile/js/shadowbox/build/lang');
				Shadowbox.loadPlayer(['img', 'flv'], 'http://".$serveur."/utile/js/shadowbox/build/player');
				window.onload = Shadowbox.init;
				</script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

	
		// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
		if ($theme=="no") {
			$smarty->display("objet_modify_".LANG.".tpl"); // affichage de l'interface de modification de la ressource
		}else{
			// affiche le formulaire de modification inclu dans le template du thème index.tpl
			$smarty->assign('contenu',"objet_modify_".LANG.".tpl");
			$smarty->display($theme."index.tpl");
		}
	} // utilisateur connu		
} // toutes les actions
?>