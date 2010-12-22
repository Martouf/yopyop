<?php
/*******************************************************************************************
 * Nom du fichier		: event.php
 * Date					: 26 juin 2009 basé sur evenement.php créé le 28 novembre 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Permet de gérer des evenements uniquement en mode lecture seule.
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
 *  Attention, il faut encore mettre en place la gestion des permissions !!!
 *
 */
// va chercher toutes les infos sur l'utilisateur courant
$utilisateur = $personneManager->getPersonne($_SESSION['id_personne']);

// fourni a smarty une information qui permet de savoir si il faut afficher ou non l'interface graphique permettant de gérer plusieurs calendriers
$smarty->assign('multiCalendriers',true);


// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idEvenement = $ressourceId;

// détermine l'action demandée... ici c'est le mode lecture seule. Donc pas de add, update, delete.
$action = "get";

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
	
	// va chercher les calendriers disponibles pour que l'utilisateurs puisse lier son événement à un calendrier
	$calendriers = $calendrierManager->getCalendriers();
	$smarty->assign('calendriers',$calendriers);
	
	// va chercher les lieux disponible pour que l'utilisateur puisse lier son événement à un lieu
	$lieux = $lieuManager->getLieux(array(),'commune');  // todo passer en paramètre la catégorie, donc le nom de la paroisse à laquelle le lieu est rattaché: array('categorie'=>'La Barc')
	$smarty->assign('lieux',$lieux);
	
	// va chercher la liste de gens pour faire un liste de contacts potentiel
	$contacts = $personneManager->getPersonnes();
	$smarty->assign('contacts',$contacts);
	
	// une ressource unique
	if (!empty($idEvenement)) {
		
		// va chercher les infos sur la ressource demandée
		$evenement = $evenementManager->getEvenement($idEvenement);
		$evenement['nomSimplifie'] = $photoManager->simplifieNomFichier($evenement['nom']);
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
		
		$evenement['description'] = nl2br($evenement['description']);
		$evenement['lieu'] =  $lieuManager->getLieu($evenement['lieu']);
		$evenement['contact'] =  $personneManager->getPersonne($evenement['info']);
		
		
		// supprime les \
		stripslashes_deep($evenement);
		
	//	print_r($evenement); //ici
		
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
				$smarty->display("event_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"event_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
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
		} // if output = ics

	} //if groupe de ressource
	
	
} // fin de l'action get
?>