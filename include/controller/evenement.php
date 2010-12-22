<?php
/*******************************************************************************************
 * Nom du fichier		: evenement.php
 * Date					: 28 novembre 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Permet de gérer des evenements. C'est l'interface CRUD de l'événement. Pour la vue calendrier, le controlleur "calendrier" a été fait pour séparer.
 *******************************************************************************************
 * Interface qui permet d'afficher une evenement ou l'interface de modification d'une evenement
 * 
 * On utilise ce fichier via la réécriture d'url.
 * URL pour lire, ajouter, modifier ou supprimer une ressource. Les paramètres supplémentaires sont envoyés par POST:
 * http://yopyop.ch/evenement/28-vacances.html  (get)
 * http://yopyop.ch/evenement/evenement.html?add
 * http://yopyop.ch/evenement/28-vacances.html?update
 * http://yopyop.ch/evenement/28-vacances.html?delete
 *
 * URL pour afficher des vues utiles aux actions demandées:
 * http://yopyop.ch/evenement/evenement.html?new   => fourni une interface vide de création qui peut être utilisée pour soumettre un nouvel élément à l'url add
 * http://yopyop.ch/evenement/28-momo.html?modify   => fourni une interface de modification avec les données de l'élément qui peut être utilisée pour soumettre les modifications à l'url update
 * http://yopyop.ch/evenement/?list fourni la liste des nom des ressources
 *
 * Les formats html, pdf, xml (atom) et ics(vcalendar) sont possible en sortie. (quand tout est implémenté)
 *
 * Todo:
 * - étudier la possibilité de gérer les .ifb  (free et busy). C'est un vcalendar qui ne contient que les plages libres et occupées, mais pas le détail des événements.
 * - gérer les restrictions d'accès sur les différents formats. (j'autorise la vue web mais pas ical !! ... peut être mais c'est de loin pas le but de ce travail)
 * - la gestion des todo list, bien que dans la même rfc 2445 se fera dans un fichier à part.
 */

/*
 *  Attention, il faut être identifié pour avoir accès à l'édition d'un événement
 *
 */

if ($_SESSION['id_personne'] == '1') {
	exit(0);
}

// va chercher toutes les infos sur l'utilisateur courant
$utilisateur = $personneManager->getPersonne($_SESSION['id_personne']);

// fourni a smarty une information qui permet de savoir si il faut afficher ou non l'interface graphique permettant de gérer plusieurs calendriers
$smarty->assign('multiCalendriers',true);


// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idEvenement = $ressourceId;

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
if (isset($parametreUrl['import'])) {   // permet d'importer un calendrier distant en donnant l'url dans le paramètre import=http://yopyop.ch/evenement/fêtes/calendrier-des-fêtes.ics
	$action = 'import';
}
if (isset($parametreUrl['addmulti'])) {
	$action = 'addmulti';
}
if (isset($parametreUrl['duplicate'])) {
	$action = 'duplicate';
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
$motsClesElement = $groupeManager->getMotCleElement($idEvenement,'evenement'); // il y a certainement moyen d'optimiser tout ça... on a pas besoin d'avoir le nombre d'oocurence de l'utilisation du mot clé !! Peut être mettre une autre fonction !!

// transforme un tableau avec le mot clé et ses occurences en liste séparée par des virgules
$tagsVirgules = implode(',',array_keys($motsClesElement));
$smarty->assign('tags',$tagsVirgules);

// on défini ensuite les différentes actions possible.
// Pour l'action get, il y a plusieurs formats de sortie possibles. Le template est choisi en conséqunce. Pour le format pdf, le choix est fait en amont par index.php qui va fournir à princeXML le fichier html correspondant.

////////////////
////  GET
///////////////

if ($action=='get') {
	// il y a 2 cas possibles qui peuvent être demandés. Une ressource unique bien précise, ou un groupe de ressource.
	
	// une ressource unique
	if (!empty($idEvenement)) {
		
		// va chercher les infos sur la ressource demandée
		$evenement = $evenementManager->getEvenement($idEvenement);
		$evenement['nomSimplifie'] = simplifieNom($evenement['nom']);
		$evenement['dateDebut'] = dateTime2Humain($evenement['date_debut']);
		$evenement['dateFin'] = dateTime2Humain($evenement['date_fin']);
		
		// Le date picker ne gère que la date, donc on gère l'heure séparément
		// volontairement on n'utilise pas les secondes
		$evenement['jourDebut'] = date('Y-m-d', strtotime($evenement['date_debut']));
		$evenement['heureDebut'] = date('H', strtotime($evenement['date_debut']));
		$evenement['minuteDebut'] = date('i', strtotime($evenement['date_debut']));

		$evenement['jourFin'] = date('Y-m-d', strtotime($evenement['date_fin']));
		$evenement['heureFin'] = date('H', strtotime($evenement['date_fin']));
		$evenement['minuteFin'] = date('i', strtotime($evenement['date_fin']));
		
		$evenement['jourDebutHumain'] = dateTime2DateHumain($evenement['jourDebut']);
		$evenement['jourFinHumain'] = dateTime2DateHumain($evenement['jourFin']);
		
		// spécifique au format vcalendar
		$evenement['dateDebutVcal'] = date('Ymd', strtotime($evenement['date_debut']));
		$evenement['dateFinVcal'] = date('Ymd', strtotime($evenement['date_fin']."+1 day")); // pour que ical voit un événement sur plusieurs jour il faut indiquer le jour après le jour de fin.
		
		$evenement['dateTimeDebutVcal'] = date('Ymd\THis', strtotime($evenement['date_debut']));
		$evenement['dateTimeFinVcal'] = date('Ymd\THis', strtotime($evenement['date_fin']));
		
		$evenement['dateTimeCreationVcal'] = date('Ymd\THis', strtotime($evenement['date_creation']));
		$evenement['dateTimeModificationVcal'] = date('Ymd\THis', strtotime($evenement['date_modification']));
		
		// transmet les tags séparés par des virgules dans le champs catégories
		$evenement['tags'] = $tagsVirgules;
		
		// si l'événement est périodique
		if ($evenement['periodicite']!='non') {
			// va chercher tous les événements qui ont le même uid que l'événement courant fourni. Donc ce sont tous les événements de la même série périodique.
			// Au minimum ce tableau de tableau contient l'événement courant qui est fourni.
			$serieEvenementsPeriodiques = $evenementManager->getEvenements(array('uid'=>$evenement['uid']),'date_debut');
			$nbOccurrence = count($serieEvenementsPeriodiques);
			
			$evenement['nbOccurrence'] = $nbOccurrence;
		}else{
			$evenement['nbOccurrence'] = '1';
		}
		
		// supprime les \
		stripslashes_deep($evenement);
		
		// affichage de la ressource
		$smarty->assign('evenement',$evenement);

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
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/evenement.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		// certains formats ne sont jamais inclu dans un thème
		if ($outputFormat=='ics') {
			header("Content-Type: text/calendar");
			header("Content-Disposition: inline; filename=".$evenement['nomSimplifie'].".ics");  // parfois aussi vcs
			$smarty->display("evenement_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}elseif ($outputFormat=='xml') {
			
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("evenement_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("evenement_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"evenement_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}
		} // if format = ics

	
	// un groupe de ressources
	}else{
		
		// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
		//http://yopyop.ch/evenement/    => va afficher la liste de toutes les evenements.
		if (empty($tags)) {
			$evenements = $evenementManager->getEvenements();
		}else{
		
			 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
			$taggedElements = $groupeManager->getElementByTags($tags,'evenement');
		
			$evenements = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getEvenements()... et array_intersect
			foreach ($taggedElements as $key => $idEvenement) {
				$evenements[$idEvenement] = $evenementManager->getEvenement($idEvenement);
				$evenements[$idEvenement]['nomSimplifie'] = simplifieNom($evenements[$idEvenement]['nom']);
				
				// il y a plusieurs fonctions pour obtenir:
				// juste l'heure:   dateTime2HeureHumain
				// juste la date:   dateTime2DateHumain
				// la date et l'heure: dateTime2Humain
				$evenements[$idEvenement]['dateDebutComplete'] = dateTime2Humain($evenements[$idEvenement]['date_debut']);
				$evenements[$idEvenement]['dateFinComplete'] = dateTime2Humain($evenements[$idEvenement]['date_fin']);
				
				$evenements[$idEvenement]['dateDebut'] = dateTime2DateHumain($evenements[$idEvenement]['date_debut']);
				$evenements[$idEvenement]['dateFin'] = dateTime2DateHumain($evenements[$idEvenement]['date_fin']);
				
				$evenements[$idEvenement]['heureDebut'] = dateTime2HeureHumain($evenements[$idEvenement]['date_debut']);
				$evenements[$idEvenement]['heureFin'] = dateTime2HeureHumain($evenements[$idEvenement]['date_fin']);
				
				
				// spécifique au format vcalendar
				$evenements[$idEvenement]['dateDebutVcal'] = date('Ymd', strtotime($evenements[$idEvenement]['date_debut']));
				$evenements[$idEvenement]['dateFinVcal'] = date('Ymd', strtotime($evenements[$idEvenement]['date_fin']."+1 day")); // pour que ical voit un événement sur plusieurs jour il faut indiquer le jour après le jour de fin.
                
				$evenements[$idEvenement]['dateTimeDebutVcal'] = date('Ymd\THis', strtotime($evenements[$idEvenement]['date_debut']));
				$evenements[$idEvenement]['dateTimeFinVcal'] = date('Ymd\THis', strtotime($evenements[$idEvenement]['date_fin']));
                
				$evenements[$idEvenement]['dateTimeCreationVcal'] = date('Ymd\THis', strtotime($evenements[$idEvenement]['date_creation']));
				$evenements[$idEvenement]['dateTimeModificationVcal'] = date('Ymd\THis', strtotime($evenements[$idEvenement]['date_modification']));
			}
			
		} // if $tags
		
		// supprime les \
		stripslashes_deep($evenements);
		
		// transmets les ressources à smarty
		$smarty->assign('evenements',$evenements);

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
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.tablesorter.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/evenement.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		if ($outputFormat=="ics") {
			header("Content-Type: text/calendar");  // x-vCalendar pour vcs => avec safari donne un fichier ics.vcs qui est illisible par ical !!
			header("Content-Disposition: inline; filename=evenement.ics");  // parfois aussi vcs
			$smarty->display("evenement_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
			
		}elseif ($outputFormat=='xml' | $outputFormat=='php') {
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("evenement_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("evenement_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"evenement_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}	
		} // if output = vcf

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
	if(isset($_POST['periodicite'])){
		$periodicite = $_POST['periodicite'];
	}else{
		$periodicite ='non';
	}
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='0';
	}
	if(isset($_POST['uid'])){
		$uid = $_POST['uid'];
	}else{
		$uid ='';
	}
	if(isset($_POST['delai_inscription'])){
		$delai_inscription = $_POST['delai_inscription'];
	}else{
		$delai_inscription ='';
	}
	if(isset($_POST['type'])){
		$type = $_POST['type'];
	}else{
		$type ='1';
	}
	if(isset($_POST['info'])){
		$info = $_POST['info'];
	}else{
		$info ='';
	}
	if(isset($_POST['state'])){
		$state = $_POST['state'];
	}else{
		$state ='0';
	}
	if(isset($_POST['remarque'])){
		$remarque = $_POST['remarque'];
	}else{
		$remarque ='';
	}
	
	
	// ajoute la nouvelle ressource
	$idEvenement = $evenementManager->insertEvenement($nom,$description,$date_debut,$date_fin,$jour_entier,$lieu,$evaluation,$id_calendrier,$periodicite,$uid,$delai_inscription,$type,$info,$state,$remarque);
		
	echo $idEvenement; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax

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
	if(isset($_POST['lieu'])){
		$lieu = trim($_POST['lieu'],',');
	}else{
		$lieu ='';
	}
	if(isset($_POST['id_calendrier'])){
		$id_calendrier = $_POST['id_calendrier'];
	}else{
		$id_calendrier ='';
	}
	if(isset($_POST['periodicite'])){
		$periodicite = $_POST['periodicite'];
	}else{
		$periodicite ='non';
	}
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='';
	}
    // le uid passé en paramètre est celui de l'événement de base. Tous les événements qui composent une suite périodique ont le même uid. Ceci permet de les filtrer dans le cas d'exportation dans un format où un seul événement de type périodique est créé.	
	if(isset($_POST['uid'])){
		$uid = $_POST['uid'];
	}else{
		$uid ='';
	}
	if(isset($_POST['delai_inscription'])){
		$delai_inscription = $_POST['delai_inscription'];
	}else{
		$delai_inscription ='';
	}
	if(isset($_POST['type'])){
		$type = $_POST['type'];
	}else{
		$type ='1';
	}
	if(isset($_POST['info'])){
		$info = $_POST['info'];
	}else{
		$info ='';
	}
	if(isset($_POST['state'])){
		$state = $_POST['state'];
	}else{
		$state ='0';
	}
	if(isset($_POST['remarque'])){
		$remarque = $_POST['remarque'];
	}else{
		$remarque ='';
	}
	if(isset($_POST['occurrence'])){
		$occurrence = $_POST['occurrence'];
	}else{
		$occurrence = '';
	}
	if(isset($_POST['periodiqueAutonome'])){
		$choixPeriodiqueAutonome = $_POST['periodiqueAutonome'];
		if ($choixPeriodiqueAutonome=='1') {
			$evenementPeriodiqueAutonome = true;
		}else{
			$evenementPeriodiqueAutonome = false;
		}
	}else{
		$evenementPeriodiqueAutonome = false;
	}
	
	// A choix suivant le fonctionnement de l'application. (peut être à placer dans des préférences utilisateur)
	// lors de la modification d'un événement périodique:
	// - soit on assure la cohérence de tous les événments et tous sont modifiés
	// - soit on considère que l'événement est autonome et que l'on considère que la création d'événement périodique n'est qu'une interface pour créer un grand nombre d'événement.
	
	if ($evenementPeriodiqueAutonome) {
		
		// fait la mise à jour
		$evenementManager->updateEvenement($idEvenement,$nom,$description,$date_debut,$date_fin,$jour_entier,$lieu,$evaluation,$id_calendrier,$periodicite,$delai_inscription,$type,$info,$state,$remarque,$uid);
		
	}else{  // on assure la cohérence des événements périodique
		// TODO: les événements de type périodique sont gérés ici de manière différentes, cependant quand on drag drop un tel événement aucune info n'est envoyée qui nous renseigne sur la périodicité de l'événement. Donc l'événement est traité comme un événement normal et il PERD sa périodicité tout en gardant un lien avec ses ex compagnons via l'uid. Il faut donc clarifier cette situation !

		// si l'événement n'est pas un événement multiple avec une périodicité fait une simple mise à jour.
		if ($periodicite=='non') {
			
			// fait la mise à jour
			$evenementManager->updateEvenement($idEvenement,$nom,$description,$date_debut,$date_fin,$jour_entier,$lieu,$evaluation,$id_calendrier,$periodicite,$delai_inscription,$type,$info,$state,$remarque,$uid);

		}else{ // c'est un événement périodique.
			
			// - obtenir les nouvelles infos fournies pour modifier l'événement
			// - obtenir les événements qui font partie de la série. (triés par ordre chronologique)
			// on boucle $occurence fois
			// - si il existe encore un événement dans la série périodique que l'on est allé chercher
			// -- prend le premier événement de la liste et le met à jour avec les nouvelles données
			// - si il n'existe pas (plus) d'événement dans la liste
			// -- crée l'événement avec les nouvelles données fournies
			// - ajoute une periode à la date du premier élément de la série pour placer le suivant.


			// va chercher tous les événements qui ont le même uid que l'événement courant fourni. Donc ce sont tous les événements de la même série périodique.
			// Au minimum ce tableau de tableau contient l'événement courant qui est fourni.
			$serieEvenementsPeriodiques = $evenementManager->getEvenements(array('uid'=>$uid),'date_debut');

			// va chercher les tags qui sont attribués à l'événement courrant pour pouvoir les appliquer aux autres événements de la série.
			// on suppose que le code javacript va d'abord faire la requête ajax de mise à jour des tags pour l'événement courrant. Ainsi on peut récupérer les tags directement dans la base de donnée pour les appliquer aux autres événement de la série.
			// @return array  un tableau avec tous les motclés.  motClé => nombre d'occurences.
			$motsClesActuels = array_keys($groupeManager->getMotCleElement($idEvenement, 'evenement'));

			// on considère que lors de l'édition d'un événement périodique l'interface affiche le premier événement de la série. (elle l'affiche pas, mais c'est la référence de calcul de la date)
			// Donc la date du premier événement de la série périodique doit se calculer.
			// Pour ce faire, on doit savoir la place de l'événement cliqué dans la série périodique
			// on doit revenir d'autant de périodes que de place depuis la date fournie. (id serieEvenementsPeriodiques = 0 => nb de période = 0 . id serieEvenementsPeriodiques = 2 => nb de périodes = 2 ...)
			
			$positionEvenementPeriodique = 0;
			foreach ($serieEvenementsPeriodiques as $key => $evenementPeriodique) {
				if ($evenementPeriodique['id_evenement']==$idEvenement) {
					$positionEvenementPeriodique = $key;
					break;
				}
			}
			
			// inverser la periode pour retourner dans le passé
			$periodeVersLePasse = str_replace("+","-",$periodicite); // "+1 week" devient "-1 week"
			
			// par défaut le format de sortie est du type datetime de mysql
			$formatSortie = 'Y-m-d H:i:s';
			
			// initialisation dans le cas ou l'événement cliqué est déjà le premier de la série
			$date_debutPremierEvenementPeriodique = $date_debut;
			$date_finPremierEvenementPeriodique = $date_fin;
			
			// retourne en arrière d'autant de période vers le passé que de position dans le tableau depuis où se trouve l'événement cliqué
			for ($i=0; $i < $positionEvenementPeriodique; $i++) { 
				$date_debutPremierEvenementPeriodique = date($formatSortie, strtotime($date_debutPremierEvenementPeriodique.' '.$periodeVersLePasse)); // ex: 2009-04-02 12:13:36 -1 week  => strotime calcul la date une semaine avant
				$date_finPremierEvenementPeriodique = date($formatSortie, strtotime($date_finPremierEvenementPeriodique.' '.$periodeVersLePasse));
			}
			
			for ($i=0; $i < $occurrence; $i++) {

				// si il y a encore au moins un événement dans la série d'événements périodiques, le met à jour avec les données courrantes.
				if (isset($serieEvenementsPeriodiques[$i])) {
					$evenementPeriodiqueCourrant = $serieEvenementsPeriodiques[$i];
					$evenementManager->updateEvenement($evenementPeriodiqueCourrant['id_evenement'],$nom,$description,$date_debutPremierEvenementPeriodique,$date_finPremierEvenementPeriodique,$jour_entier,$lieu,$evaluation,$id_calendrier,$periodicite,$delai_inscription,$type,$info,$state,$remarque,$uid);

					$idEvenementPeriodiqueCourrant = $evenementPeriodiqueCourrant['id_evenement'];
				}else{ // si il ne reste plus d'événement dans la série, on doit en créer de nouveaux pour arriver au bon nombre d'occurrence.
					$idEvenementPeriodiqueCourrant = $evenementManager->insertEvenement($nom,$description,$date_debutPremierEvenementPeriodique,$date_finPremierEvenementPeriodique,$jour_entier,$lieu,$evaluation,$id_calendrier,$periodicite,$uid,$delai_inscription,$type,$info,$state,$remarque);
				}

				// attribue les mêmes tags que ceux de l'élément de base. TODO: supprimer les tags en trop de l'événement.
				foreach($motsClesActuels as $mot){
						$groupeManager->ajouteMotCle($idEvenementPeriodiqueCourrant, $mot, 'evenement');
				} // foreach

				// calcul des dates de l'événement suivant en fonction de la periodicité. A chaque itération la date est incrémentée d'une période
				$date_debutPremierEvenementPeriodique = date($formatSortie, strtotime($date_debutPremierEvenementPeriodique.' '.$periodicite)); // ex: 2009-04-02 12:13:36 +1 week  => strotime calcul la date une semaine après
				$date_finPremierEvenementPeriodique = date($formatSortie, strtotime($date_finPremierEvenementPeriodique.' '.$periodicite));

			} // for

			// TODO: supprimer automatiquement les événements en trop. Dans le cas présent, si un événement à 12 occurences est modifié en indiquant un nombre d'occurrences inférieur. Les $occurrence premier événements sont modifiés, mais rien ne supprime ceux qui sont en trop. Il faut le faire à la "main".
		}// if periodique
	}
		

////////////////
////  DELETE
///////////////

}elseif ($action=='delete') {
	
	// TODO: lors de la suppression d'un événement périodique, supprimer
	
	// motsClesActuels contient les mots qui sont actuellement associés à l'element
	$motsClesActuels = array_keys($groupeManager->getMotCleElement($idEvenement, $type));
	
	// si il y a des tags associés. Supprime les liaisons tag-événement
	if (count($motsClesActuels)>0) {
		// suprimer les mots à supprimer
		foreach( $motsClesActuels as $key => $mot ){
			if(!empty($mot)){
				$groupeManager->supprimerMotCle($idEvenement, $mot, 'evenement');
			}
		}
	}
	// supprime l'événement
	$evenementManager->deleteEvenement($idEvenement);
	
////////////////
////  NEW
///////////////
}elseif ($action=='new') {
	
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
	
	// va chercher les calendriers disponibles pour que l'utilisateurs puisse lier sont événement à un calendrier
	$calendriers = $calendrierManager->getCalendriers();
	$smarty->assign('calendriers',$calendriers);
	
	// va chercher les lieux disponible pour que l'utilisateur puisse lier son événement à un lieu
	$lieux = $lieuManager->getLieux();  // todo passer en paramètre la catégorie, donc le nom de la paroisse à laquelle le lieu est rattaché: array('categorie'=>'La Barc')
	$smarty->assign('lieux',$lieux);
	
	// va chercher la liste de gens pour faire un liste de contacts potentiel
	$contacts = $personneManager->getPersonnes();
	$smarty->assign('contacts',$contacts);
	
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
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/evenement.js\"></script>";	
	$smarty->assign('additionalHeader',$additionalHeader);
	
	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("evenement_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"evenement_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
		$smarty->display($theme."index.tpl");
	}

////////////////
////  MODIFY
///////////////
}elseif ($action=='modify') {
	
	// va chercher les infos sur la ressource demandée
	$evenement = $evenementManager->getEvenement($idEvenement);
	$evenement['nomSimplifie'] = simplifieNom($evenement['nom']);
	$evenement['dateDebut'] = dateTime2Humain($evenement['date_debut']);
	$evenement['dateFin'] = dateTime2Humain($evenement['date_fin']);
	
	// Le date picker ne gère que la date, donc on gère l'heure séparément
	// volontairement on n'utilise pas les secondes
	$evenement['jourDebut'] = date('Y-m-d', strtotime($evenement['date_debut']));
	$evenement['jourDebutEurope'] = date('d-m-Y', strtotime($evenement['date_debut']));
	$evenement['heureDebut'] = date('H', strtotime($evenement['date_debut']));
	$evenement['minuteDebut'] = date('i', strtotime($evenement['date_debut']));

	$evenement['jourFin'] = date('Y-m-d', strtotime($evenement['date_fin']));
	$evenement['jourFinEurope'] = date('d-m-Y', strtotime($evenement['date_fin']));
	$evenement['heureFin'] = date('H', strtotime($evenement['date_fin']));
	$evenement['minuteFin'] = date('i', strtotime($evenement['date_fin']));
	
	$evenement['jourDebutHumain'] = dateTime2DateHumain($evenement['jourDebut']);
	$evenement['jourFinHumain'] = dateTime2DateHumain($evenement['jourFin']);
	
	
	if (!empty($lieu)) {
		// va chercher le nom du lieu en fonction de l'id
		$lieu = $lieuManager->getLieu($evenement['lieu']);
		$evenement['lieuNomCommune'] =  $lieu['nom'].", ".$lieu['commune']; // dans le genre: temple de serrières, Neuchâtel
	}
	
	// va chercher les informations sur le créateur et le dernier modificateur de l'événement
	$createur = $personneManager->getPersonne($evenement['auteur']);
	$modificateur = $personneManager->getPersonne($evenement['auteur_modif']);
	
	$evenement['emailCreateur'] = $createur['email'];
	
	$modificateur = $personneManager->getPersonne($evenement['auteur_modif']);
	$evenement['emailModificateur'] = $modificateur['email'];
	
	// si l'événement est périodique
	if ($evenement['periodicite']!='non') {
		// va chercher tous les événements qui ont le même uid que l'événement courant fourni. Donc ce sont tous les événements de la même série périodique.
		// Au minimum ce tableau de tableau contient l'événement courant qui est fourni.
		$serieEvenementsPeriodiques = $evenementManager->getEvenements(array('uid'=>$evenement['uid']),'date_debut');
		$nbOccurrence = count($serieEvenementsPeriodiques);
		
		$evenement['nbOccurrence'] = $nbOccurrence;
	}else{
		$evenement['nbOccurrence'] = '1';
	}
	
	// supprime les \
	stripslashes_deep($evenement);
	
	// passe les données de l'evenement à l'affichage
	$smarty->assign('evenement',$evenement);
	
	
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
	
	// va chercher les calendriers disponibles pour que l'utilisateurs puisse lier son événement à un calendrier
	$calendriers = $calendrierManager->getCalendriers();
	$smarty->assign('calendriers',$calendriers);
	
	// va chercher les lieux disponible pour que l'utilisateur puisse lier son événement à un lieu
	$lieux = $lieuManager->getLieux(array(),'commune');  // todo passer en paramètre la catégorie, donc le nom de la paroisse à laquelle le lieu est rattaché: array('categorie'=>'La Barc')
	$smarty->assign('lieux',$lieux);
	
	// va chercher la liste de gens pour faire un liste de contacts potentiel
	$contacts = $personneManager->getPersonnes();
	$smarty->assign('contacts',$contacts);
		
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
		<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/evenement.js\"></script>";	
	$smarty->assign('additionalHeader',$additionalHeader);
	
	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("evenement_modify_".LANG.".tpl"); // affichage de l'interface de modification de la ressource
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"evenement_modify_".LANG.".tpl");
		$smarty->display($theme."index.tpl");
	}
	
////////////////
////  IMPORT EXTERNAL CALENDAR
///////////////
}elseif ($action=='import') {

	$url = $parametreUrl['import'];
	
	if (!empty($url)) {
		$evenementManager->importCalendar($url);
		echo "<p class=\"ok\">Evenements importés avec succès</p>";
		
	}
	
////////////////
////  DUPLICATE EVENEMENT
///////////////
}elseif ($action=='duplicate') {

	// va chercher les infos sur l'événement à dupliquer
	$evenement = $evenementManager->getEvenement($idEvenement);
	
	// motsClesActuels contient les mots qui sont actuellement associés à l'element
	$motsClesActuels = array_keys($groupeManager->getMotCleElement($idEvenement, 'evenement'));
	
	
	$idNouvelEvenement = $evenementManager->insertEvenement($evenement['nom'],$evenement['description'],$evenement['date_debut'],$evenement['date_fin'],$evenement['jour_entier'],$evenement['lieu'],$evenement['evaluation'],$evenement['id_calendrier'],$evenement['periodicite'],$evenement['uid'],$evenement['delai_inscription'],$evenement['type'],$evenement['info'],$evenement['state'],$evenement['remarque']);
	
	// associe au nouvel événement les mêmes tags que pour le premier.
	if (count($motsClesActuels)>0) {
		// suprimer les mots à supprimer
		foreach( $motsClesActuels as $key => $mot ){
			if(!empty($mot)){
				$groupeManager->ajouteMotCle($idNouvelEvenement, $mot, 'evenement');
			}
		}
	}
	echo $idNouvelEvenement; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax
	
} // app
?>