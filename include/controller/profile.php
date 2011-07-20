<?php
	/*******************************************************************************************
	 * Nom du fichier		: profile.php
	 * Date					: 12 juillet 2011
	 * Auteur				: Mathieu Despont
	 * Adresse E-mail		: mathieu@martouf.ch
	 * But de ce fichier	: Ce script gère la page de profile d'une personne. C'est une sorte de tableau de bord.
	 *******************************************************************************************
	 * Cette page affiche le profile d'une personne.
	 * Ex de contenu:
	 * - pédigré... avatar, site web, date de naissance, badges, fortune, réputation...
	 * - Dernières activités (notifications en tous genres)
	 * - dernières réservations
	 * - liste des objets appartenant à l'utilisateur
	 * 
	 */
	
// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idProfile = $ressourceId;  // idProfile = idPersonne ... c'est pareil, on affiche le profile d'une personne.

// détermine l'action demandée
$action = "get";
if (isset($parametreUrl['update'])) {
	$action = 'update';
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

// si aucune indication de profile est indiquée, c'est son propre profile qui est affiché.
// Ceci permet de faire des url simple pour aller directe sur ça page: http://yopyop.ch/profile
// vu qu'il n'y a pas l'id dans l'url (http://yopyop.ch/profile/2-martouf.html) ... on va la chercher dans la session
if (empty($idProfile)) {
	$idProfile = $_SESSION['id_personne'];
}


// va chercher les tags qui sont liés au profile courant ! (un utilisateur dans un groupe ou un autre.)
$motsClesElement = $groupeManager->getMotCleElement($idProfile,'personne'); // il y a certainement moyen d'optimiser tout ça... on a pas besoin d'avoir le nombre d'oocurence de l'utilisation du mot clé !! Peut être mettre une autre fonction !!

// transforme un tableau avec le mot clé et ses occurences en liste séparée par des virgules
$tagsVirgules = implode(',',array_keys($motsClesElement));
$smarty->assign('tags',$tagsVirgules);

// qui peut modifier un profile:
// - le propriétaire du profile peut l'éditer.
$droitModification = false;

if ($idProfile == $_SESSION['id_personne']) {
	$droitModification = true;
}
$smarty->assign('droitModification',$droitModification);

// donne l'info à smarty si l'utilsiateur fait partie du système ou non.
// ainsi le profile public est au strict minimum, les gens du système voient un profile plus étendu.
// le propriétaire voit tout.
$utilisateurConnu = false;
if ($_SESSION['id_personne'] != '1') {
	$utilisateurConnu = true;
}
$smarty->assign('utilisateurConnu',$utilisateurConnu);

////////////////
////  GET
///////////////

// todo: faire un cas spéciale si le profile demandé est celui de l'utilisateur inconnu !

if ($action=='get') {
	
	$idPersonne = $idProfile;
	
	// va chercher les infos sur la personne
	$personne = $personneManager->getPersonne($idPersonne);
	
	$personne['nomSimplifie'] = simplifieNom($personne['surnom']); // on utilise le pseudo comme nom
	
	// supprime les \
	stripslashes_deep($personne);
	
	// obtient la date de naissance dans un format lisible pour les humains et sans l'heure.
	$personne['dateNaissance'] = dateTime2DateHumain($personne['date_naissance']);
	
	// obtient la clé gravatar d'un e-mail, ainsi l'image de profile est le gravatar
	$personne['gravatar'] = md5(strtolower(trim($personne['email'])));
	
	$smarty->assign('personne',$personne);
	
	// quelques scripts utiles
	$additionalHeader = "
		<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/datePicker.css\" media=\"screen\" />
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/date.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/date_fr.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.datePicker.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/global.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/shadowbox.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/profile.js\"></script>
		<script type=\"text/javascript\">
		Shadowbox.loadSkin('classic', 'http://".$serveur."/utile/js/shadowbox/src/skin');
		Shadowbox.loadLanguage('fr', 'http://".$serveur."/utile/js/shadowbox/build/lang');
		Shadowbox.loadPlayer(['img', 'flv'], 'http://".$serveur."/utile/js/shadowbox/build/player');
		window.onload = Shadowbox.init;
		</script>";	
	$smarty->assign('additionalHeader',$additionalHeader);
	
	////////////////// Activité récente //////////
	
	$filtreMesNotifications = array('evaluation'=>$idPersonne); // on ne veut que les objets de la personne dont on affiche le profile
	
	$tousNotifications = $notificationManager->getNotifications($filtreMesNotifications,'date_creation desc limit 50');
	$notifications = array(); // tableau contenant des tableaux représentant la ressource
	// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getNotifications()... et array_intersect
	foreach ($tousNotifications as $key => $aNotification) {
		$notification = $aNotification;
		
		$notification['nomSimplifie'] = simplifieNom($aNotification['nom']);
		$notification['dateCreation'] = dateTime2Humain($aNotification['date_creation']);			
		$notifications[$aNotification['id_notification']] = $notification;		
	}
	
	// supprime les \
	stripslashes_deep($objets);
	
	// transmets les ressources à smarty
	$smarty->assign('notifications',$notifications);
	
	
	////////////////// Mes Objets ///////////////
	// todo: pagination
	
	// on ne publie au public que les objets qui sont disponibles (etat=1) donc pas les objets encore en cours de création ou les objets privés
	if ($droitModification) {
		$filtreMesObjets = array('id_proprietaire'=>$idPersonne); // on ne veut que les objets de la personne dont on affiche le profile
	}else{
		$filtreMesObjets = array('id_proprietaire'=>$idPersonne, 'etat'=>'1'); // on ne veut que les objets de la personne dont on affiche le profile qui sont publié (etat=1)
	}
	
	$tousObjets = $objetManager->getObjets($filtreMesObjets,'nom'); // avec 'nom desc limit 1' => seulement 1 et filtré par nom inverses.. (bref un peu les possibilités de la chose)
		
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
		
		// fourni pour smarty une chaine de caractère avec la liste des tags
		$objet['listeTags'] = $listeMotCle;
		$objets[$aObjet['id_objet']] = $objet;		
	}


	// supprime les \
	stripslashes_deep($objets);
	
	// transmets les ressources à smarty
	$smarty->assign('objets',$objets);
	
	
	/////////// Format de sortie ////////////
	
	// certains formats ne sont jamais inclu dans un thème
	if ($outputFormat=='xml') {			
		// calcule le nom de la même ressource, mais en page html
		$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
		$smarty->assign('alternateUrl',"http://".$alternateUrl);
		
		header('Content-Type: application/atom+xml; charset=UTF-8');
		$smarty->display("reservation_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
	}else{
		
		// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
		if ($theme=="no") {
			$smarty->display("profile_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}else{
			// affiche la ressource inclue dans le template du thème index.tpl
			$smarty->assign('contenu',"profile_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
			$smarty->display($theme."index.tpl");
		}
	} // if format = xml
	
	
} // action get
	
?>