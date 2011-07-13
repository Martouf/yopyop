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

if ($action=='get') {
	
	// si aucune indication de profile est indiquée, c'est son propre profile qui est affiché.
	// Ceci permet de faire des url simple pour aller directe sur ça page: http://yopyop.ch/profile
	if (!empty($idProfile)) {
		$idPersonne = $idProfile;
	}else{
		$idPersonne = $_SESSION['id_personne'];
	}
	
	
	// va chercher les infos sur la personne
	$personne = $personneManager->getPersonne($idPersonne);
	
	$personne['nomSimplifie'] = simplifieNom($personne['surnom']); // on utilise le pseudo comme nom
	
	// supprime les \
	stripslashes_deep($personne);
	
	// obtient la date de naissance dans un format lisible pour les humains et sans l'heure.
	$personne['dateNaissance'] = dateTime2DateHumain($personne['date_naissance']);
	
	// obtient la clé gravatar d'un e-mail, ainsi l'image de profile est le gravatar
	$personne['gravatar'] = md5($personne['email']);
	
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