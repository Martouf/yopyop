<?php
/*******************************************************************************************
 * Nom du fichier		: reservation.php
 * Date					: 30 juillet 2010
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Permet de gérer des reservations. Ce fichier génère des vues pour gérer des reservations d'objet
 *******************************************************************************************
 * Interface qui permet d'afficher une reservation ou l'interface de modification d'une reservation
 * 
 * On utilise ce fichier via la réécriture d'url.
 * URL pour lire, ajouter, modifier ou supprimer une ressource. Les paramètres supplémentaires sont envoyés par POST:
 * http://yopyop.ch/reservation/28-momo.html  (get)
 * http://yopyop.ch/reservation/reservation.html?add
 * http://yopyop.ch/reservation/28-momo.html?update
 * http://yopyop.ch/reservation/28-momo.html?delete
 *
 * URL pour afficher des vues utiles aux actions demandées:
 * http://yopyop.ch/reservation/reservation.html?new   => fourni une interface vide de création qui peut être utilisée pour soumettre un nouvel élément à l'url add
 * http://yopyop.ch/reservation/28-momo.html?modify   => fourni une interface de modification avec les données de l'élément qui peut être utilisée pour soumettre les modifications à l'url update
 * http://yopyop.ch/reservation/?name  => (à voir si cette url fonctionne vraiment) fourni la liste des nom des ressources
 */

/*
 *  Attention, il faut encore mettre en place la gestion des permissions !!!
 *
 */

// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idReservation = $ressourceId;

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

// va chercher les tags qui sont liés à la reservation courante !
$motsClesElement = $groupeManager->getMotCleElement($idReservation,'reservation'); // il y a certainement moyen d'optimiser tout ça... on a pas besoin d'avoir le nombre d'oocurence de l'utilisation du mot clé !! Peut être mettre une autre fonction !!

// transforme un tableau avec le mot clé et ses occurences en liste séparée par des virgules
$tagsVirgules = implode(',',array_keys($motsClesElement));
$smarty->assign('tags',$tagsVirgules);


// on défini ensuite les différentes actions possibles.
// Pour l'action get, il y a plusieurs formats de sortie possibles. Le template est choisi en conséqence. Pour le format pdf, le choix est fait en amont par index.php qui va fournir à princeXML le fichier html correspondant.

$droitModification = false;

if (!empty($idReservation)) {
	// pour modifier une reservation, il faut être le créateur de celle-ci, un admin, ou le propriétaire de l'objet lié à la réservation.
	$reservationAModifier = $reservationManager->getReservation($idReservation);
	$createur = $reservationAModifier['createur'];
	if ($_SESSION['id_personne']== $createur) {
		$droitModification = true;
	}

	// si le visiteur est admin
	$visiteur = $personneManager->getPersonne($_SESSION['id_personne']);
	if ($visiteur['rang']=='1') {
		$droitModification = true;
	}
	
	// si le visiteur est le propriétaire de l'objet lié à la réservation
	$objetDeLaReservation = $objetManager->getObjet($reservationAModifier['id_objet']);
	$proprietaireObjet = $objetDeLaReservation['createur'];
	if ($_SESSION['id_personne']==$proprietaireObjet) {
		$droitModification = true;
	}
}


////////////////
////  GET
///////////////

if ($action=='get') {
	
	// il y a 2 cas possibles qui peuvent être demandé. Une ressource unique bien précise, ou un groupe de ressource.
	
	// une ressource unique
	if (!empty($idReservation)) {
		
		// va chercher les infos sur la ressource demandée
		$reservation = $reservationManager->getReservation($idReservation);
		$reservation['nomSimplifie'] = simplifieNom($reservation['nom']);
		
		// supprime les \
		stripslashes_deep($reservation);
		
		// obtients un tableau avec la liste des mots-clés attribué à l'reservation
		$motCles = $groupeManager->getMotCleElement($idReservation,'reservation');
		
		$listeMotCle= '';
		foreach ($motCles as $motCle => $occurence){
			// si le mot clé est un "prénom nom", le découpe et ne prend que le prénom pour des raisons d'anonymat sur google
			$motCleEnpartie = explode(" ", $motCle);
			$prenom = $motCleEnpartie[0];
			$listeMotCle = $listeMotCle.$prenom.' ';
		}
		
		// fourni pour smarty une chaine de caractère avec la liste des tags (offuscé pour les nom de famille)
		$reservation['listeTags'] = $listeMotCle;
		
		// obtient des infos sur le propriétaire, l'objet
		$objet = $objetManager->getObjet($reservation['id_objet']);
		$proprietaire = $personneManager->getPersonne($objet['id_proprietaire']);
		
		$reservation['proprietaire'] = $proprietaire;
		$reservation['objet'] = $objet;
		
		// affichage de la ressource
		$smarty->assign('reservation',$reservation);
		
		// va chercher les infos à propos de l'objet pour en faire un petit résumé dans le formulaire de réservation
		$objetReserve = $objetManager->getObjet($reservation['id_objet']);
		$smarty->assign('objetReserve',$objetReserve);
		
		// va chercher les infos à propos de l'événement lié
		$evenementReserve = $evenementManager->getEvenement($reservation['id_evenement']);
		
		$evenementReserve['dateDebut'] = dateTime2Humain($evenementReserve['date_debut']);
		$evenementReserve['dateFin'] = dateTime2Humain($evenementReserve['date_fin']);
		
		$evenementReserve['jourDebut'] = date('Y-m-d', strtotime($evenementReserve['date_debut']));
		$evenementReserve['heureDebut'] = date('H', strtotime($evenementReserve['date_debut']));
		$evenementReserve['minuteDebut'] = date('i', strtotime($evenementReserve['date_debut']));

		$evenementReserve['jourFin'] = date('Y-m-d', strtotime($evenementReserve['date_fin']));
		$evenementReserve['heureFin'] = date('H', strtotime($evenementReserve['date_fin']));
		$evenementReserve['minuteFin'] = date('i', strtotime($evenementReserve['date_fin']));
		
		$evenementReserve['jourDebutHumain'] = dateTime2DateHumain($evenementReserve['jourDebut']);
		$evenementReserve['jourFinHumain'] = dateTime2DateHumain($evenementReserve['jourFin']);
		
		$smarty->assign('evenementReserve',$evenementReserve);
		
		// fourni les infos sur l'image de présentation.
		$imagePresentation = $photoManager->getPhoto($objetReserve['id_image']);
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
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/reservation_lecture.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/global.js\"></script>
			<script type=\"text/javascript\">
			Shadowbox.loadSkin('classic', 'http://".$serveur."/utile/js/shadowbox/src/skin');
			Shadowbox.loadLanguage('fr', 'http://".$serveur."/utile/js/shadowbox/build/lang');
			Shadowbox.loadPlayer(['img', 'flv'], 'http://".$serveur."/utile/js/shadowbox/build/player');
			window.onload = Shadowbox.init;
			</script>";	
		$smarty->assign('additionalHeader',$additionalHeader);
		
		// si aucune restriction sur les commentaires n'existe..
		$commentairesReservationAutorise = true; // on accepte dans tous les cas
		// affiche les commentaires courants et propose une interface pour en ajouter
		if ($commentairesReservationAutorise) {
		
			// va chercher les commentaires qui sont associés à la ressource
			$tousCommentaires = $commentaireManager->getCommentaireElement($idReservation,'reservation');
			$commentaires = array();
			foreach ($tousCommentaires as $key => $aCommentaire) {
				$commentaires[$key] = $aCommentaire;
				$commentaires[$key]['description'] = nl2br($aCommentaire['description']);  // mise en forme basique des commentaire avec des retours chariots
				$commentaires[$key]['dateCreation'] = dateTime2Humain($aCommentaire['date_creation']);
				$commentaires[$key]['auteur'] = $personneManager->getPseudo($aCommentaire['id_auteur']); // pseudo de l'auteur plutôt que id
				$commentaires[$key]['auteurCommentaire'] = $personneManager->getPersonne($aCommentaire['id_auteur']); // toutes les infos de la personne
				$commentaires[$key]['gravatar'] = md5($aCommentaire['mail']);
			}
		
			// supprime les \  et transmet l'affichage à smarty
			stripslashes_deep($commentaires);
			$smarty->assign('commentaires',$commentaires);
		
			// info sur l'utilisateur qui va poster un commentaire
			$smarty->assign('idAuteurCommentaire',$_SESSION['id_personne']);
			$smarty->assign('pseudoUtilisateur',@$_SESSION['pseudo']);
			
			$smarty->assign('auteurCommentaireEnCours',$personneManager->getPersonne($_SESSION['id_personne']));
		
			// on autorise le template à afficher les commentaires
			$smarty->assign('commentaireAutorise',true);
		}else{
			// on autorise pas les commentaires
			$smarty->assign('commentaireAutorise',false);
		}
		
		
		

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
				$smarty->display("reservation_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"reservation_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}
		} // if format = xml

	
	// un groupe de ressources
	}else{
		
		// affichage réservé aux admin
		if ($_SESSION['rang']!='1') {
			echo "<h2>Vous n'avez pas le droit de voir cette page !</h2>";
			exit(0);
		}
		
		// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
		//http://yopyop.ch/reservation/    => va afficher la liste de toutes les reservations.
		if (empty($tags)) {
			$tousReservations = $reservationManager->getReservations();
			
			$reservations = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getReservations()... et array_intersect
			foreach ($tousReservations as $key => $aReservation) {
				$reservation = $aReservation;
				
				$reservation['nomSimplifie'] = simplifieNom($aReservation['nom']);
					
				// obtients un tableau avec la liste des mots-clés attribué à l'élément
				$motCles = $groupeManager->getMotCleElement($aReservation['id_reservation'],'reservation');

				$listeMotCle= '';
				foreach ($motCles as $motCle => $occurence){
					// si le mot clé est un "prénom nom", le découpe et ne prend que le prénom pour des raisons d'anonymat sur google
					$motCleEnpartie = explode(" ", $motCle);
					$prenom = $motCleEnpartie[0];
					$listeMotCle = $listeMotCle.$prenom.' ';
				}
				
				// infos à propos du locataire
				$locataire = $personneManager->getPersonne($aReservation['id_locataire']);
				$reservation['locataire'] = $locataire;
				
				// infos à propos de l'événement lié
				$evenement = $evenementManager->getEvenement($aReservation['id_evenement']);
				
				$evenement['dateDebut'] = dateTime2Humain($evenement['date_debut']);
				$evenement['dateFin'] = dateTime2Humain($evenement['date_fin']);
				
				$evenement['jourDebut'] = date('Y-m-d', strtotime($evenement['date_debut']));
				$evenement['heureDebut'] = date('H', strtotime($evenement['date_debut']));
				$evenement['minuteDebut'] = date('i', strtotime($evenement['date_debut']));

				$evenement['jourFin'] = date('Y-m-d', strtotime($evenement['date_fin']));
				$evenement['heureFin'] = date('H', strtotime($evenement['date_fin']));
				$evenement['minuteFin'] = date('i', strtotime($evenement['date_fin']));

				$evenement['jourDebutHumain'] = dateTime2DateHumain($evenement['jourDebut']);
				$evenement['jourFinHumain'] = dateTime2DateHumain($evenement['jourFin']);
				
				$reservation['evenement'] = $evenement;
				
				// infos à propos de l'objet
				$objet = $objetManager->getObjet($aReservation['id_objet']);
				$reservation['objet'] = $objet;
				
				// fourni les infos sur l'image de présentation.
				$imagePresentation = $photoManager->getPhoto($objet['id_image']);
				$imagePresentation['lienVignette'] = $photoManager->getLienVignette($imagePresentation['lien']);
				$imagePresentation['lienMoyenne'] = $photoManager->getLienMoyenne($imagePresentation['lien']);
				$reservation['imagePresentation'] = $imagePresentation;
				
				// fourni pour smarty une chaine de caractère avec la liste des tags (offuscé pour les noms de famille)
				$reservation['listeTags'] = $listeMotCle;
				$reservations[$aReservation['id_reservation']] = $reservation;		
			}
			
		}else{
		
			 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
			$taggedElements = $groupeManager->getElementByTags($tags,'reservation');
		
			$reservations = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getReservations()... et array_intersect
			foreach ($taggedElements as $key => $idReservation) {
				$reservations[$idReservation] = $reservationManager->getReservation($idReservation);
				$reservations[$idReservation]['nomSimplifie'] = simplifieNom($reservations[$idReservation]['nom']);

				// obtients un tableau avec la liste des mots-clés attribué à l'image
				$motCles = $groupeManager->getMotCleElement($idReservation,'reservation');

				$listeMotCle= '';
				foreach ($motCles as $motCle => $occurence){
					// si le mot clé est un "prénom nom", le découpe et ne prend que le prénom pour des raisons d'anonymat sur google
					$motCleEnpartie = explode(" ", $motCle);
					$prenom = $motCleEnpartie[0];
					$listeMotCle = $listeMotCle.$prenom.' ';
				}
				
				// infos à propos du locataire
				$locataire = $personneManager->getPersonne($reservations[$idReservation]['id_locataire']);
				$reservations[$idReservation]['locataire'] = $locataire;
				
				// infos à propos de l'événement lié
				$evenement = $evenementManager->getEvenement($reservations[$idReservation]['id_evenement']);
				$evenement['dateDebut'] = dateTime2Humain($evenement['date_debut']);
				$evenement['dateFin'] = dateTime2Humain($evenement['date_fin']);
				
				$evenement['jourDebut'] = date('Y-m-d', strtotime($evenement['date_debut']));
				$evenement['heureDebut'] = date('H', strtotime($evenement['date_debut']));
				$evenement['minuteDebut'] = date('i', strtotime($evenement['date_debut']));

				$evenement['jourFin'] = date('Y-m-d', strtotime($evenement['date_fin']));
				$evenement['heureFin'] = date('H', strtotime($evenement['date_fin']));
				$evenement['minuteFin'] = date('i', strtotime($evenement['date_fin']));

				$evenement['jourDebutHumain'] = dateTime2DateHumain($evenement['jourDebut']);
				$evenement['jourFinHumain'] = dateTime2DateHumain($evenement['jourFin']);
				
				$reservations[$idReservation]['evenement'] = $evenement;
				
				// infos à propos de l'objet
				$objet = $objetManager->getObjet($reservations[$idReservation]['id_objet']);
				$reservations[$idReservation]['objet'] = $objet;
				
				// fourni les infos sur l'image de présentation.
				$imagePresentation = $photoManager->getPhoto($objet['id_image']);
				$imagePresentation['lienVignette'] = $photoManager->getLienVignette($imagePresentation['lien']);
				$imagePresentation['lienMoyenne'] = $photoManager->getLienMoyenne($imagePresentation['lien']);
				$reservations[$idReservation]['imagePresentation'] = $imagePresentation;

				// fourni pour smarty une chaine de caractère avec la liste des tags (offuscé pour les nom de famille)
				$reservations[$idReservation]['listeTags'] = $listeMotCle;
			}
		} // if $tags
		
		// supprime les \
		stripslashes_deep($reservations);
		
		// place les plus ancien en bas comme dans les couches géologiques
		$reservations = array_reverse($reservations);
		
		// transmets les ressources à smarty
		$smarty->assign('reservations',$reservations);
		
		// url du flux atom pour suivre les reservations de cette catégorie
		$urlFlux = "http://".$serveur."/reservation/".trim($ressourceTags,"/")."/flux.xml";

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
			$smarty->display("reservation_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("reservation_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"reservation_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}	
		} // if output = xml

	} //if groupe de ressource
	
////////////////
////  ADD
///////////////
	
}elseif ($action=='add') {
		
	// si l'utilisateur est inconnu
	if ($_SESSION['id_personne'] == '1') {
		
		// va chercher les infos nécessaires à la création d'une nouvelle personne.
		if(isset($_POST['prenom'])){
			$prenom = $_POST['prenom'];
		}else{
			$prenom ='';
		}
		if(isset($_POST['nom_personne'])){
			$nomPersonne = $_POST['nom_personne'];
		}else{
			$nomPersonne ='';
		}
		if(isset($_POST['surnom'])){
			$surnom = $_POST['surnom'];
		}else{
			$surnom ='';
		}
		if(isset($_POST['description_personne'])){
			$description = $_POST['description_personne'];
		}else{
			$description ='';
		}
		if(isset($_POST['date_naissance'])){
			$date_naissance = $_POST['date_naissance'];
		}else{
			$date_naissance ='';
		}
		if(isset($_POST['mot_de_passe'])){
			$mot_de_passe = $_POST['mot_de_passe'];
			if (empty($mot_de_passe)) {
				$mot_de_passe = $personneManager->generatePassword($motsPrononcables);
			}
		}else{
			$mot_de_passe = $personneManager->generatePassword($motsPrononcables);
		}
		if(isset($_POST['photo'])){
			$photo = $_POST['photo'];
		}else{
			$photo ='';
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
		if(isset($_POST['lieu'])){
			$lieu = $_POST['lieu'];
		}else{
			$lieu ='';
		}
		if(isset($_POST['pays'])){
			$pays = $_POST['pays'];
		}else{
			$pays ='';
		}
		if(isset($_POST['tel'])){
			$tel = $_POST['tel'];
		}else{
			$tel ='';
		}
		if(isset($_POST['email'])){
			$email = $_POST['email'];
		}else{
			$email ='';
		}
		if(isset($_POST['rang'])){
			$rang = $_POST['rang'];
		}else{
			$rang ='';
		}
		if(isset($_POST['url'])){
			$url = $_POST['url'];
		}else{
			$url ='';
		}
		if(isset($_POST['evaluation'])){
			$evaluation = $_POST['evaluation'];
		}else{
			$evaluation ='0';
		}
	
		$rang = '12'; // pour les nouvelles personne ainsi crée on ne donne pas beaucoup de confiance.
		$fortune = '0';
		
		// crée une nouvelle personne dans la base
		$idPersonne = $personneManager->insertPersonne($prenom,$nomPersonne,$surnom,$description,$date_naissance,$photo,$mot_de_passe,$rue,$npa,$lieu,$pays,$tel,$email,$rang,$url,$fortune,$evaluation);
		
		/*
			TODO : éventuellement notification par e-mail (ou atom) de la création d'un nouveau compte. A l'admin pour contrôle et au nouvel inscrit pour qu'il garde se paramètres. (mot de passe arbitraire)
		*/
		$notificationManager->insertNotification('Bienvenue','Un nouveau compte utilisateur pour: <em>'.$surnom.'</em> avec le mot de passe: <em>'.$mot_de_passe.'</em> a été créé.','1','0',$idPersonne);
		
		$idLocataire = $idPersonne;  // la location est attribuée à cette nouvelle personne
	}else{
		$idLocataire = $_SESSION['id_personne']; // la location est attribuée à l'utilisateur courant. Il n'est donc pas possible de réserver pour un autre sans être loggué comme lui.
	} // si l'utilisateur est inconnu
	
	// obtient les données de la réservation qui peuvent apparaître dans formulaire d'ajout de reservation.
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
	if(isset($_POST['date_debut'])){
		$date_debut = $_POST['date_debut'];
	}else{
		$date_debut ='';
	}
	if(isset($_POST['date_fin'])){
		$date_fin = $_POST['date_fin'];
	}else{
		$date_fin ='';
	}
	if(isset($_POST['jour_entier'])){
		$jour_entier = $_POST['jour_entier'];
	}else{
		$jour_entier ='non';
	}
	if(isset($_POST['lieu'])){
		$lieu = $_POST['lieu'];
	}else{
		$lieu ='';
	}
	if(isset($_POST['id_calendrier'])){
		$id_calendrier = $_POST['id_calendrier'];
	}else{
		$id_calendrier ='';
	}
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='0';
	}
	if(isset($_POST['type'])){
		$type = $_POST['type'];
	}else{
		$type ='1'; // 1 = définitif, 2 = préréservation
	}
	if(isset($_POST['id_objet'])){
		$id_objet = $_POST['id_objet'];
	}else{
		$id_objet ='';  // l'objet à réserver
	}
	
	// obtient le détail des infos à propos du locataire
	$futurLocataire = $personneManager->getPersonne($idLocataire);
	
	// crée un nouvel événement dans le calendrier de l'objet
	
	// les paramètres utilisés sont:
	if (!empty($futurLocataire['surnom'])) {
		$nomEvenement = "Réservation pour ".$futurLocataire['surnom']; // le pseudo du futur locataire. ça donne une info et ça anonymise en même temps. Toutefois ce champ n'est pas remplit lors de la création d'une nouvelle personne.
	}else{
		$nomEvenement = "Réservé";
	}
	
	$descriptionEvenement = '';
	$date_debutEvenement = $date_debut;
	$date_finEvenement = $date_fin;
	$jour_entierEvenement = $jour_entier;
	$lieuEvenement = '';
	$evaluationEvenement = '';
	$id_calendrierEvenement = $id_calendrier;
	
	$idEvenement = $evenementManager->insertEvenement($nomEvenement,$descriptionEvenement,$date_debutEvenement,$date_finEvenement,$jour_entierEvenement,$lieuEvenement,$evaluationEvenement,$id_calendrierEvenement);
	
	// ajoute la réservation dans la base. Le tout lié avec les éléments événement et personne créé au besoin pour l'occasion.
	$idNewReservation = $reservationManager->insertReservation($nom,$description,$idLocataire,$id_objet,$idEvenement,$type,'0'); //  0 => état en attente de validation
	
	// obtient des infos sur le propriétaire, l'objet (et le locataire on a déjà plus haut)
	$objet = $objetManager->getObjet($id_objet);
	$proprietaire = $personneManager->getPersonne($objet['id_proprietaire']);
	
	// envoie des notifications
	$messageNotificationFuturLocataire = "Vous avez fait une <a href=\"http://".$serveur."/reservation/".$idNewReservation."-".$objet['nom'].".html\">demande de réservation</a> de l'objet: <a title=\"Voir le détail de l'objet...\" href=\"http://".$serveur."/objet/".$objet['id_objet']."-".$objet['nom'].".html\">".$objet['nom']."</a>";
	$messageNotificationProprietaire = "<a href=\"http://".$serveur."/profile/".$futurLocataire['id_personne']."-".$futurLocataire['surnom'].".html\">".$futurLocataire['surnom']."</a> a fait une demande de réservation pour l'objet: <a title=\"Voir le détail de l'objet...\" href=\"http://".$serveur."/objet/".$objet['id_objet']."-".$objet['nom'].".html\">".$objet['nom']."</a>. Veuillez <a href=\"http://".$serveur."/reservation/".$idNewReservation."-".$objet['nom'].".html?modify\"> accepter cette demande de réservation</a>.";
	
	// notifie le locataire
	$notificationManager->insertNotification('Demande de réservation',$messageNotificationFuturLocataire,'2','0',$idLocataire);
	
	// notifie le propriétaire
	$notificationManager->insertNotification('Demande de réservation',$messageNotificationProprietaire,'3','0',$proprietaire['id_personne']);
	
	// notification par email aux gens
	$messageNotificationFuturLocataire .="<p>Le propriétaire peut être contacté à l'adresse ".$proprietaire['email']." en cas de besoin.</p>";	
	$envoiOk = $notificationManager->notificationMail($futurLocataire['email'],$messageNotificationFuturLocataire,'Notification yopyop.ch'); // le locataire qui fait une réservation sait ce qu'il fait ! Pas besoin de le notifier. => mais il peut recevori le mail de l'autre.

	$messageNotificationProprietaire .="<p>Le locataire peut être contacté à l'adresse ".$futurLocataire['email']." en cas de besoin.</p>";	
	$envoiOk = $notificationManager->notificationMail($proprietaire['email'],$messageNotificationProprietaire,'Notification yopyop.ch');
	
	echo $idNewReservation; // au cas où

	// /////// anti-spam //////
	// $domain = $_SERVER['HTTP_HOST'];
	//     if (preg_match("/([^\.]+\.[a-z]{2,4})$/",$domain,$match)) {
	//             $domain = $match[1]; // ne conserve que le domaine de second niveau (mondomaine.ch)
	//     }
	//     $domain = '.'.$domain; // notation ".mondomaine.ch" pour couvrir tous les sous-domaines
	// 
	//     // nom du cookie d'authentification
	//     $authCookieName = 'ticket_commentaire';
	// // validité en secondes du cookie d'authentification
	// $authCookieLifetime = 3600;
	// 
	// $errorMsg = array();
	// 
	// 
	// if (isset($_COOKIE[$authCookieName]) && preg_match("/^[0-9A-Z]{32}$/i",$_COOKIE[$authCookieName])) {
	// 	// vérifie la validité du cookie d'authentification
	// 	$ticket_id = $_COOKIE[$authCookieName];
	// 
	// 	// obtient le ticket qui correspond à l'id fourni
	// 	$ticket = $commentaireManager->checkTicket($ticket_id);
	// 
	// 	if (!empty($ticket)) {
	// 		if (time() - $ticket['time'] > $authCookieLifetime) {
	//                     $errorMsg[] = "Votre session a expiré, veuillez remplir à nouveau le formulaire";
	//             } elseif ($domain !== $ticket['domain']) {
	//                     $errorMsg[] = "La session n'est valable que pour le domaine ".$ticket['domain'];
	//             } elseif ($_SERVER['REMOTE_ADDR'] !== $ticket['remote_ip']) {
	//                     $errorMsg[] = "La session n'est valable que pour l'adresse IP ".$ticket['remote_ip'];
	//             } else {
	//                     // marque le ticket comme utilisé (le conserve quelques temps à des fins d'historique)
	// 				$commentaireManager->putTicketToTrash($ticket_id);
	// 
	//                     // efface le cookie du navigateur
	//                     setcookie($authCookieName,false,time()-1,'/',$ticket['domain']);
	// 
	//                     // purge les tickets délivrés depuis plus de 4h (utilisés ou non)
	//                     $commentaireManager->ticketGarbageCollector();
	//             }
	// 	}else{
	// 		$errorMsg[] = "Votre session n'est plus valable, veillez remplir à nouveau le formulaire";
	// 	}
	//     } else {
	//             $errorMsg[] = "L'usage du script est interdit aux robots et pages externes (erreur d'authentification ou de flooding)";
	//     }
	// 
	// // si aucune erreur d'anti-spam n'est survenue, alors on peut ajouter le commentaire. Sinon on retourne l'erreur
	// if (count($errorMsg) == 0) {
	// 
	// 	// ajoute le nouveau commentaire
	// 	$idReservation = $commentaireManager->insertReservation($nom,$description,$id_auteur,$mail,$url,$evaluation);
	// 
	// 	// lie ce commentaire avec la ressources désirée
	// 	$commentaireManager->associerCommentaire($idReservation,$id_element, $table_element,$evaluation);
	// 
	// 	echo $idReservation; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax
	// }else{
	// 	// affiche l'erreur
	// 	echo $errorMsg[0];
	// }
	

////////////////
////  UPDATE
///////////////

}elseif ($action=='update') {
	
	// obtient les données d'une réservation
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
	if(isset($_POST['id_locataire'])){
		$id_locataire = $_POST['id_locataire'];
	}else{
		$id_locataire ='';
	}
	if(isset($_POST['id_objet'])){
		$id_objet = $_POST['id_objet'];
	}else{
		$id_objet ='';
	}
	if(isset($_POST['id_evenement'])){
		$id_evenement = $_POST['id_evenement'];
	}else{
		$id_evenement ='';
	}
	if(isset($_POST['type'])){
		$type = $_POST['type'];
	}else{
		$type ='';
	}
	if(isset($_POST['etat'])){
		$etat = $_POST['etat'];
	}else{
		$etat ='';
	}
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='';
	}
	if(isset($_POST['id_calendrier'])){
		$id_calendrier = $_POST['id_calendrier'];
	}else{
		$id_calendrier ='';
	}
	
	// obtient les données utiles pour l'événement lié à la réservation
	if(isset($_POST['date_debut'])){
		$date_debut = $_POST['date_debut'];
	}else{
		$date_debut ='';
	}
	if(isset($_POST['date_fin'])){
		$date_fin = $_POST['date_fin'];
	}else{
		$date_fin ='';
	}
	if(isset($_POST['jour_entier'])){
		$jour_entier = $_POST['jour_entier'];
	}else{
		$jour_entier ='';
	}
	
	// si l'utilisateur est admin, créateur de la réservation, ou créateur de l'objet lié à la réservation
	if ($droitModification) {
		
		// pour l'événement lié, il peut y avoir 3 cas:
		// - l'événement est mis à jour suivant les nouvelles indications
		// - la réservation est refusée. Donc l'événement est supprimé.
		// - l'événement a déjà été supprimé. Il n'est donc plus possible d'agir dessus. => ce cas n'est pas possible selon l'idée de base de l'application. On peut cependant imaginer par erreur quelqu'un qui revient sur cette page ! => donc on recrée un événement.
		
		// la réservation est refusée, donc on supprime l'évènement lié. (on garde juste pour info les dates dans la description)
		if ($etat=='2') { // si la réservation est refusée
			$evenementManager->deleteEvenement($id_evenement);
			$description .= $description."/n".$date_debut."/n".$date_fin;
		}else{
			$evenementExiste = $evenementManager->evenementExiste($id_evenement);

			if ($evenementExiste) {
				
				// l'événement existe, donc on le modifie selon les nouvelles données.
				$evenementManager->updateEvenement($id_evenement,'','',$date_debut,$date_fin,$jour_entier,'','','','','','','','','','');
				
			}else{ // l'evenement n'existe pas
				// donc on recrée un événement qui correspond au donnée fournies.
				
				// les paramètres utilisés sont:
				if (!empty($_SESSION['id_personne']['surnom'])) {
					$nomEvenement = "Réservation pour ".$_SESSION['id_personne']['surnom'];
				}else{
					$nomEvenement = "Réservé";
				}
				
				$descriptionEvenement = '';
				$date_debutEvenement = $date_debut;
				$date_finEvenement = $date_fin;
				$jour_entierEvenement = $jour_entier;
				$lieuEvenement = '';
				$evaluationEvenement = '';
				$id_calendrierEvenement = $id_calendrier;

				$id_evenement = $evenementManager->insertEvenement($nomEvenement,$descriptionEvenement,$date_debutEvenement,$date_finEvenement,$jour_entierEvenement,$lieuEvenement,$evaluationEvenement,$id_calendrierEvenement);
				
			}
		}

		// va chercher les infos sur la réservation avant modification.
		$reservationAvantModif = $reservationManager->getReservation($idReservation);
		$reservationAvantModif['nomSimplifie'] = simplifieNom($reservationAvantModif['nom']);
		
		// supprime les \
		stripslashes_deep($reservationAvantModif);
		
		// est ce que la réservation a été validée ?
		// Etat: 0 = attente, 1=acceptée, 2=refusée
		$etatAvant = $reservationAvantModif['etat'];

	//	echo "id:",$idReservation," nom:",$nom," descr:",$description," loc:",$id_locataire," id obj:",$id_objet," id evenement:",$id_evenement," type:",$type," etat:",$etat," eval:",$evaluation;
		// fait la mise à jour
		$reservationManager->updateReservation($idReservation,$nom,$description,$id_locataire,$id_objet,$id_evenement,$type,$etat,$evaluation,'','','');  // les 3 derniers paramètres sont: $groupeAutoriseLecture,$groupeAutoriseEcriture,$groupeAutoriseCommentaire et ne sont pas gérer actuellement
		
		// obtient des infos sur le propriétaire, l'objet (et le locataire on a déjà plus haut)
		$objet = $objetManager->getObjet($reservationAvantModif['id_objet']);
		$proprietaire = $personneManager->getPersonne($objet['id_proprietaire']);
		$locataire = $personneManager->getPersonne($reservationAvantModif['id_locataire']); // on obtient le locataire par l'ancienne valeur de la réservation. C'est possible car le formulaire ne propose pas de changer de locataire.

		// envoie des notifications
		$lienProfileMofificateur = "<a href=\"//".$serveur."/profile/".$_SESSION['id_personne']."-".$_SESSION['pseudo'].".html\">".$_SESSION['pseudo']."</a> a ";		
		
		
		// si l'état a changé
		if ($etatAvant != $etat) {
			if ($etat == 1) {  // si l'etat a été validé (forcément par le propriétaire)
				$messageNotificationProprietaire = "Vous avez <strong>accepté</strong> la <a href=\"http://".$serveur."/reservation/".$idReservation."-".$objet['nom'].".html\"> réservation</a> de votre objet: <a title=\"Voir le détail de votre objet...\" href=\"http://".$serveur."/objet/".$objet['id_objet']."-".$objet['nom'].".html\">".$objet['nom']."</a> pour <a href=\"http://".$serveur."/profile/".$locataire['id_personne']."-".$locataire['surnom'].".html\">".$locataire['surnom']."</a>";				
				$messageNotificationLocataire = $lienProfileMofificateur." <strong>accepté</strong> votre demande de <a href=\"http://".$serveur."/reservation/".$idReservation."-".$objet['nom'].".html\"> réservation</a> de l'objet: <a title=\"Voir le détail de l'objet...\" href=\"http://".$serveur."/objet/".$objet['id_objet']."-".$objet['nom'].".html\">".$objet['nom']."</a>";
			}else{
				$messageNotificationProprietaire = "Vous avez <strong>refusé</strong> la <a href=\"http://".$serveur."/reservation/".$idReservation."-".$objet['nom'].".html\"> réservation</a> de votre objet: <a title=\"Voir le détail de votre objet...\" href=\"http://".$serveur."/objet/".$objet['id_objet']."-".$objet['nom'].".html\">".$objet['nom']."</a> pour <a href=\"http://".$serveur."/profile/".$locataire['id_personne']."-".$locataire['surnom'].".html\">".$locataire['surnom']."</a>";
				$messageNotificationLocataire = $lienProfileMofificateur." <strong>refusé</strong> votre demande de <a href=\"http://".$serveur."/reservation/".$idReservation."-".$objet['nom'].".html\"> réservation</a> de l'objet: <a title=\"Voir le détail de l'objet...\" href=\"http://".$serveur."/objet/".$objet['id_objet']."-".$objet['nom'].".html\">".$objet['nom']."</a>";
			}
			// envoi uniquement au locataire. Le propriétaire sait ce qu'il fait !
			$messageNotificationLocataireMail = "<p>".$messageNotificationLocataire."</p>";
			$envoiOk = $notificationManager->notificationMail($locataire['email'],$messageNotificationLocataireMail,'Notification yopyop.ch');
			
		}else{  // c'est une autre modification que l'etat
			
			// si c'est le locataire qui modifie
			if ($_SESSION['id_personne']==$locataire['id_personne']) {
				$messageNotificationLocataire = "Vous avez modifié la <a href=\"http://".$serveur."/reservation/".$idReservation."-".$objet['nom'].".html\"> réservation</a> de l'objet: <a title=\"Voir le détail de l'objet...\" href=\"http://".$serveur."/objet/".$objet['id_objet']."-".$objet['nom'].".html\">".$objet['nom']."</a>";
				$messageNotificationProprietaire = $lienProfileMofificateur." modifié la <a href=\"http://".$serveur."/reservation/".$idReservation."-".$objet['nom'].".html\"> réservation</a> de l'objet: <a title=\"Voir le détail de votre objet...\" href=\"http://".$serveur."/objet/".$objet['id_objet']."-".$objet['nom'].".html\">".$objet['nom']."</a>";
				
				// notifie le propriétaire
				$messageNotificationProprietaireMail = "<p>".$messageNotificationProprietaire."</p>";
				$messageNotificationProprietaireMail .="<p>Le locataire peut être contacté à l'adresse ".$locataire['email']." en cas de besoin.</p>";
				$envoiOk = $notificationManager->notificationMail($proprietaire['email'],$messageNotificationProprietaireMail,'Notification yopyop.ch');
				
			}else{ // si c'est le propriétaire qui modifie
				$messageNotificationLocataire = $lienProfileMofificateur." modifié la <a href=\"http://".$serveur."/reservation/".$idReservation."-".$objet['nom'].".html\"> réservation</a> de l'objet: <a title=\"Voir le détail de l'objet...\" href=\"http://".$serveur."/objet/".$objet['id_objet']."-".$objet['nom'].".html\">".$objet['nom']."</a>";
				$messageNotificationProprietaire = "Vous avez modifié la <a href=\"http://".$serveur."/reservation/".$idReservation."-".$objet['nom'].".html\"> réservation</a> de votre objet: <a title=\"Voir le détail de votre objet...\" href=\"http://".$serveur."/objet/".$objet['id_objet']."-".$objet['nom'].".html\">".$objet['nom']."</a>";
			
				// envoi uniquement au locataire le propriétaire sait ce qu'il fait !
				$messageNotificationLocataireMail = "<p>".$messageNotificationLocataire."</p>";
				$messageNotificationLocataireMail .="<p>Le propriétaire peut être contacté à l'adresse ".$proprietaire['email']." en cas de besoin.</p>";
				
				$envoiOk = $notificationManager->notificationMail($locataire['email'],$messageNotificationLocataireMail,'Notification yopyop.ch');
			}
		}

		// notifie le locataire
		$notificationManager->insertNotification('Mise à jour',$messageNotificationLocataire,'4','0',$locataire['id_personne']); 

		// notifie le propriétaire
		$notificationManager->insertNotification('Mise à jour',$messageNotificationProprietaire,'5','0',$proprietaire['id_personne']);
		
		echo "ok";		
	}else{
		echo "vous n'avez pas les droits nécessaires pour modifier cet réservation.";
	}

////////////////
////  DELETE
///////////////   /// TODO: attention.. avant de supprimer une ressource et il faut la détaguer !!  .. encore écrire le code !

}elseif ($action=='delete') {
	
	// si l'utilisateur est admin ou créateur de la reservation
	if ($droitModification) {
		$reservationManager->deleteReservation($idReservation);
	}
	
////////////////
////  NEW
///////////////
}elseif ($action=='new') {
	
	// l'url est de la forme: http://yopyop.ch/reservation/?new&id_objet=2
	// si id_objet n'est pas fourni... pas de réservation possible !
	
	if(isset($parametreUrl['id_objet'])){
		$idObjetAReserver = $parametreUrl['id_objet'];
	}else{
		$idObjetAReserver ='';
	}
	
	if (!empty($idObjetAReserver)) {
		
		// va chercher les infos à propos de l'objet pour en faire un petit résumé dans le formulaire de réservation
		$objetAReserver = $objetManager->getObjet($idObjetAReserver);
		$smarty->assign('objetAReserver',$objetAReserver);
		
		// fourni les infos sur l'image de présentation.
		$imagePresentation = $photoManager->getPhoto($objetAReserver['id_image']);
		$imagePresentation['lienVignette'] = $photoManager->getLienVignette($imagePresentation['lien']);
		$imagePresentation['lienMoyenne'] = $photoManager->getLienMoyenne($imagePresentation['lien']);
		$smarty->assign('imagePresentation',$imagePresentation);
		
		// crée une base pour afficher les heures et minutes.
		$minutes = array();
		$j = '';
		for ($i=0; $i < 60 ; $i++) {
			$j = $i;
			if (strlen($j) < 2) {
				$j = "0".$j;
			}
			$minutes[] = $j;
		}
		$heures = array();
		$j = '';
		for ($i=0; $i < 24 ; $i++) { 
			$j = $i;
			if (strlen($j) < 2) {
				$j = "0".$j;
			}
			$heures[] = $j;
		}

		$smarty->assign('heures',$heures);
		$smarty->assign('minutes',$minutes);
		
		// le script utilisé n'est pas le même si une personne doit être crée à la volée ou non !
		$avecPersonne = '';
		if ($_SESSION['id_personne'] == '1') {
			$avecPersonne = '_avec_personne';
		}
		
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
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/reservation".$avecPersonne.".js\"></script>
			<script type=\"text/javascript\">
			Shadowbox.loadSkin('classic', 'http://".$serveur."/utile/js/shadowbox/src/skin');
			Shadowbox.loadLanguage('fr', 'http://".$serveur."/utile/js/shadowbox/build/lang');
			Shadowbox.loadPlayer(['img', 'flv'], 'http://".$serveur."/utile/js/shadowbox/build/player');
			window.onload = Shadowbox.init;
			</script>";	
		$smarty->assign('additionalHeader',$additionalHeader);


		// si l'utilisateur est connu on lui fourni un formulaire de réservation simple. Sinon, on lui fourni en plus un formulaire de création d'une personne.
		if ($_SESSION['id_personne'] != '1') {
			
			// va chercher les infos sur l'utilisateur courrant afin de l'afficher dans le formulaire de réservation.
			$futurLocataire = $personneManager->getPersonne($_SESSION['id_personne']);
			$smarty->assign('futurLocataire',$futurLocataire);
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("reservation_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
			}else{
				// affiche le formulaire de modification inclu dans le template du thème index.tpl
				$smarty->assign('contenu',"reservation_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
				$smarty->display($theme."index.tpl");
			}
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("reservation_lien_nouveau_compte_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource et une personne
			//	$smarty->display("reservation_avec_personne_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource et une personne
			}else{
				// affiche le formulaire de modification inclu dans le template du thème index.tpl
				$smarty->assign('contenu',"reservation_lien_nouveau_compte_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource et une personne
				//$smarty->assign('contenu',"reservation_avec_personne_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource et une personne
				
				$smarty->display($theme."index.tpl");
			}
		}
	}else{
		echo "si id_objet n'est pas fourni je ne sais pas quoi réserver... donc pas de réservation possible !";
	} // id_objet !empty



////////////////
////  MODIFY
///////////////
}elseif ($action=='modify') {
	
	// si l'utilisateur est admin ou créateur de la reservation
	if ($droitModification) {
	
		// va chercher les infos sur la ressource demandée
		$reservation = $reservationManager->getReservation($idReservation);
		$reservation['nomSimplifie'] = simplifieNom($reservation['nom']);
	
		// supprime les \
		stripslashes_deep($reservation);
	
		// obtients un tableau avec la liste des mots-clés attribué à l'image
		$motCles = $groupeManager->getMotCleElement($idReservation,'reservation');
	
		$listeMotCle= '';
		foreach ($motCles as $motCle => $occurence){
			// si le mot clé est un "prénom nom", le découpe et ne prend que le prénom pour des raisons d'anonymat sur google
			$motCleEnpartie = explode(" ", $motCle);
			$prenom = $motCleEnpartie[0];
			$listeMotCle = $listeMotCle.$prenom.' ';
		}
	
		// fourni pour smarty une chaine de caractère avec la liste des tags (offuscé pour les nom de famille)
		$reservation['listeTags'] = $listeMotCle;
	
		// affichage de la ressource
		$smarty->assign('reservation',$reservation);
		
		// va chercher les infos à propos de l'objet pour en faire un petit résumé dans le formulaire de réservation
		$objetReserve = $objetManager->getObjet($reservation['id_objet']);
		$smarty->assign('objetReserve',$objetReserve);
		
		// va chercher les infos à propos de l'événement lié
		$evenementReserve = $evenementManager->getEvenement($reservation['id_evenement']);
		
		$evenementReserve['dateDebut'] = dateTime2Humain($evenementReserve['date_debut']);
		$evenementReserve['dateFin'] = dateTime2Humain($evenementReserve['date_fin']);
		
		$evenementReserve['jourDebut'] = date('Y-m-d', strtotime($evenementReserve['date_debut']));
		$evenementReserve['heureDebut'] = date('H', strtotime($evenementReserve['date_debut']));
		$evenementReserve['minuteDebut'] = date('i', strtotime($evenementReserve['date_debut']));

		$evenementReserve['jourFin'] = date('Y-m-d', strtotime($evenementReserve['date_fin']));
		$evenementReserve['heureFin'] = date('H', strtotime($evenementReserve['date_fin']));
		$evenementReserve['minuteFin'] = date('i', strtotime($evenementReserve['date_fin']));
		
		$evenementReserve['jourDebutHumain'] = dateTime2DateHumain($evenementReserve['jourDebut']);
		$evenementReserve['jourFinHumain'] = dateTime2DateHumain($evenementReserve['jourFin']);
		$evenementReserve['jourDebutEurope'] = date('d-m-Y', strtotime($evenementReserve['date_debut']));
		$evenementReserve['jourFinEurope'] = date('d-m-Y', strtotime($evenementReserve['date_fin']));
		
		$smarty->assign('evenementReserve',$evenementReserve);
		
		// fourni les infos sur l'image de présentation.
		$imagePresentation = $photoManager->getPhoto($objetReserve['id_image']);
		$imagePresentation['lienVignette'] = $photoManager->getLienVignette($imagePresentation['lien']);
		$imagePresentation['lienMoyenne'] = $photoManager->getLienMoyenne($imagePresentation['lien']);
		$smarty->assign('imagePresentation',$imagePresentation);
		
		// si l'utilisateur courant est le propriétaire de l'objet, il a le droit de décider de l'état de la réservation. 0 = en attente, 1 = acceptée, 2 = refusée.
		if ($_SESSION['id_personne']==$objetReserve['id_proprietaire']) {
			$smarty->assign('proprietaireObjet',true);
		}else{
			$smarty->assign('proprietaireObjet',false);
		}
		
		// crée une base pour afficher les heures et minutes.
		$minutes = array();
		$j = '';
		for ($i=0; $i < 60 ; $i++) {
			$j = $i;
			if (strlen($j) < 2) {
				$j = "0".$j;
			}
			$minutes[] = $j;
		}
		$heures = array();
		$j = '';
		for ($i=0; $i < 24 ; $i++) { 
			$j = $i;
			if (strlen($j) < 2) {
				$j = "0".$j;
			}
			$heures[] = $j;
		}

		$smarty->assign('heures',$heures);
		$smarty->assign('minutes',$minutes);

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
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/shadowbox.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/global.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/reservation.js\"></script>
			<script type=\"text/javascript\">
			Shadowbox.loadSkin('classic', 'http://".$serveur."/utile/js/shadowbox/src/skin');
			Shadowbox.loadLanguage('fr', 'http://".$serveur."/utile/js/shadowbox/build/lang');
			Shadowbox.loadPlayer(['img', 'flv'], 'http://".$serveur."/utile/js/shadowbox/build/player');
			window.onload = Shadowbox.init;
			</script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

	
		// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
		if ($theme=="no") {
			$smarty->display("reservation_modify_".LANG.".tpl"); // affichage de l'interface de modification de la ressource
		}else{
			// affiche le formulaire de modification inclu dans le template du thème index.tpl
			$smarty->assign('contenu',"reservation_modify_".LANG.".tpl");
			$smarty->display($theme."index.tpl");
		}
	} // utilisateur connu		
} // toutes les actions
?>