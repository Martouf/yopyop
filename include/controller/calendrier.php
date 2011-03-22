<?php
/*******************************************************************************************
 * Nom du fichier		: calendrier.php
 * Date					: 9 janvier 2009
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@ecodev.ch
 * But de ce fichier	: Afficher les calendriers et permettre leur modifications. (couleur, nom, libellé, etc..)
 *******************************************************************************************
 * Interface qui permet d'afficher une calendrier ou l'interface de modification d'une calendrier
 * 
 * On utilise ce fichier via la réécriture d'url.
 * URL pour lire, ajouter, modifier ou supprimer une ressource. Les paramètres supplémentaires sont envoyés par POST:
 * http://yopyop.ch/calendrier/28-momo.html  (get)
 * http://yopyop.ch/calendrier/calendrier.html?add
 * http://yopyop.ch/calendrier/28-momo.html?update
 * http://yopyop.ch/calendrier/28-momo.html?delete
 *
 * URL pour afficher des vues utiles aux actions demandées:
 * http://yopyop.ch/calendrier/calendrier.html?new   => fourni une interface vide de création qui peut être utilisée pour soumettre un nouvel élément à l'url add
 * http://yopyop.ch/calendrier/28-momo.html?modify   => fourni une interface de modification avec les données de l'élément qui peut être utilisée pour soumettre les modifications à l'url update
 * 
 * http://yopyop.ch/calendrier/toto.html?vue=liste&datecourante=2008-12-15 => permet d'afficher TOUS les calendriers sous forme de liste (mois,semaine) centré sur une date précise.
 * http://yopyop.ch/calendrier/toto.html?vue=liste&datecourante=2008-12-15&calendrier=1,2,3 => affiche les calendriers dont les id sont fourni
 * http://yopyop.ch/calendrier/2-toto.html?vue=liste&datecourante=2008-12-15 => affiche le calendrier dont l'id est 2
 *
 * De plus, il est possibler de filtrer les événements affichés dans les calenriers selon les tags quis ont passé dans l'url
 *
 */

// on définit le fuseau horaire par défaut. Sinon une erreur est levée dans le log. CF: http://ch2.php.net/manual/fr/function.date-default-timezone-set.php
date_default_timezone_set("Europe/Zurich");

/*
 *  Attention, il faut être identifié pour avoir accès à l'édition du calendrier
 *
 */

if ($_SESSION['id_personne'] == '1') {
	exit(0);
}

// va chercher toutes les infos sur l'utilisateur courant
$utilisateur = $personneManager->getPersonne($_SESSION['id_personne']);

// couleur des types d'événement
$couleurType = array();
$couleurType[1]= "78b5ff";
$couleurType[2]= "5ce091";
$couleurType[3]= "ff5757";
$couleurType[4]= "41d1d1";
$couleurType[5]= "fafa8e";
$couleurType[6]= "f0612d";



// si un id est fourni => affiche le calendrier correspondant
$idCalendrier = $ressourceId; // si il n'y a aucune gestion du calendrier par défaut, uniquement cette ligne reste.

// fourni a smarty une information qui permet de savoir si il faut afficher ou non l'interface graphique permettant de gérer plusieurs calendriers
$smarty->assign('multiCalendriers',true);


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
	$choixTags = "";
}else{
	// les tags
	$tags = explode("/", trim($ressourceTags,"/")); // transforme la chaine séparée par des / en tableau. Au passage supprime les / surnuméraires en début et fin de chaine
	
	// transforme un tableau avec le mot clé et ses occurences en liste séparée par des virgules
	$tagsVirgules = implode(',',$tags);
	$smarty->assign('choixTags',$tagsVirgules);

	$choixTags = $tagsVirgules;
}

////////////////////////////////////////
// Quelques données pour la barre de menu.

// on fourni le nom du serveur pour que le javascript puisse rafraichir la page dans les filtres.
$baseUrl = "http://".$serveur;
$smarty->assign('baseUrl',$baseUrl);

// tableau contenant les années de 2000 à l'années courante+3ans
$annees = array();
for ($i=2005; $i < 2050; $i++) { 
	$anneeCourante = date('Y');
	$annees[] = $i;
	if ($anneeCourante+3 ==$i) {
		break;
	}
}
$smarty->assign('annees',$annees);

$lieux = $lieuManager->getLieux(array(),'commune');
$smarty->assign('lieux',$lieux);


///////////  Filtre /////////////////////////////
// on récupère tous les états courants des filtres
// détermine le type de vue: jour, mois, semaine, année, liste, carte...
// $typeDeVue = $utilisateur['pref_vue_calendrier'];
$typeDeVue = "mois";
if (isset($parametreUrl['vue'])) {
	$typeDeVue = $parametreUrl['vue'];
}

// obtient la date courante à laquelle on présente le calendrier
$dateCourante = date('Y-m-d H:i:s');
if (isset($parametreUrl['datecourante'])) {
	$dateCourante = $parametreUrl['datecourante'];
}

// on recalcule séparément les jours mois année pour mettre à jour les filtres
$choixJour = date('d',strtotime($dateCourante));
$choixMois = date('m',strtotime($dateCourante));
$choixAnnee = date('Y',strtotime($dateCourante));

$choixLieu = '';
if (isset($parametreUrl['filtreLieu'])) {
	$choixLieu = $parametreUrl['filtreLieu'];
}

$choixType = '';
if (isset($parametreUrl['filtreType'])) {
	$choixType = $parametreUrl['filtreType'];
}
$choixCalendrier = '';
if (!empty($idCalendrier)) {
	$choixCalendrier = $idCalendrier;
}
$choixEtat = '';
if (isset($parametreUrl['filtreEtat'])) {
	$choixEtat = $parametreUrl['filtreEtat'];
}

// on fournit à smarty l'etat des filtres
// choixLieu, choixType
$smarty->assign('choixVue',$typeDeVue);
$smarty->assign('choixJour',$choixJour);
$smarty->assign('choixMois',$choixMois);
$smarty->assign('choixAnnee',$choixAnnee);

$smarty->assign('choixLieu',$choixLieu);
$smarty->assign('choixType',$choixType);
$smarty->assign('choixTags',$choixTags);
$smarty->assign('choixCalendrier',$choixCalendrier);
$smarty->assign('choixEtat',$choixEtat);


// on défini ensuite les différentes actions possible.
// Pour l'action get, il y a plusieurs formats de sortie possibles. Le template est choisi en conséqunce. Pour le format pdf, le choix est fait en amont par index.php qui va fournir à princeXML le fichier html correspondant.

////////////////
////  GET
///////////////

if ($action=='get') {
	
		// création du morceau de requête qui va être injecté à plusieurs endroits pour filtrer les événements en fonction des demandes.
		// le where est déjà présent dans toutes les requêtes des fonctions dans lesquelles on injecte ce morceau de requête.
		
		$queryFiltre = '';
		
		if (!empty($choixLieu)) {
			$queryFiltre = " and lieu=".$choixLieu;
		}
		if (!empty($choixType)) {
			$queryFiltre .= " and type=".$choixType;
		}
		if (!empty($choixEtat)) {
			$queryFiltre .= " and state=".$choixEtat;
		}
		
		
		
		// Principe de fonctionnement:
		// - va chercher les infos sur le calendrier lui même.
		// - détermine le format de sortie.
		// Si le format est autre que html on remplit un tableau avec toutes les données on le fournit à smarty.
		// Si le format est html:
		// - va chercher la liste des id des événements concernés par la vue demandée. (il peut y avoir plusieurs itéreation des étapes ci-après si la vue demande d'aller chercher et d'afficher plusieurs types d'événements différentes. Ex: evenements de jour entier ou non)
		// - restreint la liste des événements en fonction des tags et des calendriers sélectionnés
		// - restreint la liste des événements en fonction des droits d'accès
		// - construit des blocs représentants les événements sélectionnés pour la vue demandée.
		// - choisi le bon template d'affichage suivant le format de sortie choisi
	
		/////////////// information à propos du (des) calendriers  /////////////////////////
		$selectionCalendrier = array();
		
		
		// si un calendrier précis à été demandé sous la forme habituelle avec ré-écriture d'url
		if (!empty($idCalendrier)) {
			
			// va chercher les infos sur la ressource demandée
			$calendrier = $calendrierManager->getCalendrier($idCalendrier);
			
			// détermine si l'utilisateur à le droit de voir ce calendrier
			$lectureAutorise = false;
			// obtient la liste des groupes dans lesquels se trouve l'utilisateur
			$listeGroupeUtilisateur = $groupeManager->getGroupeUtilisateur($_SESSION['id_personne']);
			$listeGroupesAutorises = $calendrier['groupe_autorise_lecture']; // une liste séparée par des , 
			
			// si la valeur vaut 0 alors, le calendrier n'a pas de gestion des droits et il est en lecture pour tout le monde.
			if ($listeGroupesAutorises=='0') {
				$lectureAutorise = true;
			}else{
				// si une liste de groupe autorisé à voir ce calendrier existe la compare avec les groupes de l'utilisateur
				$listeGroupeAutorise = explode(",", trim($listeGroupesAutorises,",")); // transforme la chaine séparée par des , en tableau. Au passage supprime les , surnuméraires en début et fin de chaine
				$acces = array_intersect($listeGroupeAutorise,$listeGroupeUtilisateur);
				// si il y a des éléments dans le tableau, alors l'accès est autorisé
				if (count($acces)>0) {
					$lectureAutorise = true;
				}
			}
			
			if ($lectureAutorise) {
				$calendrier['nomSimplifie'] = simplifieNom($calendrier['nom']);

				// supprime les \
				stripslashes_deep($calendrier);
				// affichage de la ressource
				$smarty->assign('calendrier',$calendrier);

				// pour afficher la liste des calendriers avec le même code de template
				$smarty->assign('calendriers',array($calendrier));

				// place l'id du calendrier dans le tableau qui contient les listes de calendrier
				// ainsi on peut utiliser le même tableau dans le cas de la sélection de un ou plusieurs calendriers.
				$selectionCalendrier = array($idCalendrier);
			}else{
				exit(0);
			}
			
		}else{
			
			// aucun calendrier précis n'a été choisi
			// il y a 2 cas possibles:
			// - on affiche tout
			// - le paramètre "calendrier" est dispnible et les id des calendriers à afficher sont fournis. Ex: http://yopyop.ch/calendrier/?calendrier=12,3,43,33
			// le tableau: $selectionCalendrier contient la liste des id de calendriers voulus. Il peut servir à savoir ce qu'il faut afficher.
			
			
			// Obtient la sélection de calendrier voulue par l'utilisateur
			$selectionCalendrierVirgule = '';
			if (isset($parametreUrl['calendrier'])) {
				$selectionCalendrierVirgule = $parametreUrl['calendrier'];
			}
			
			
			// Gestion des droits d'accès sur les calendriers.
			// le but est de fournir le tableau $selectionCalendrier avec uniquement des calendriers autorisés
			
			// obtient la liste des groupes dans lesquels se trouve l'utilisateur
			$listeGroupeUtilisateur = $groupeManager->getGroupeUtilisateur($_SESSION['id_personne']);
			
			// obtiens tous les calendriers
			$tousCalendriers = $calendrierManager->getCalendriers();
			
			$calendriersAutorises = array();
			
			foreach ($tousCalendriers as $key => $aCalendrier) {
				$listeGroupesAutorises = $aCalendrier['groupe_autorise_lecture']; // une liste séparée par des , 
				
				// si la valeur vaut 0 alors, le calendrier n'a pas de gestion des droits et il est en lecture pour tout le monde.
				if ($listeGroupesAutorises=='0') {
					$calendriersAutorises[] = $aCalendrier['id_calendrier']; // ajoute ce calendrier dans la liste des calendriers autorisés
				}else{
					// si une liste de groupe autorisé à voir ce calendrier existe la compare avec les groupes de l'utilisateur
					$listeGroupeAutorise = explode(",", trim($listeGroupesAutorises,",")); // transforme la chaine séparée par des , en tableau. Au passage supprime les , surnuméraires en début et fin de chaine
					$acces = array_intersect($listeGroupeAutorise,$listeGroupeUtilisateur);
					// si il y a des éléments dans le tableau, alors l'accès est autorisé
					if (count($acces)>0) {
						$calendriersAutorises[] = $aCalendrier['id_calendrier']; // ajoute ce calendrier dans la liste des calendriers autorisés
					}
				}
			}
			
			// si une liste de calendrier est fournie
			if (!empty($selectionCalendrierVirgule)) {
				
				// on fait un tableau avec la liste des calendriers fournis séparés par des ,
				$selectionCalendrier = explode(",",$selectionCalendrierVirgule);
				
				// on ne garde que les calendriers qui sont autorisés
				$selectionCalendrier = array_intersect($calendriersAutorises, $selectionCalendrier);
				
				$calendriers = array();
				foreach ($selectionCalendrier as $key => $idCal) {
					$calendriers[$idCal] = $calendrierManager->getCalendrier($idCal);
					$calendriers[$idCal]['nomSimplifie'] = simplifieNom($calendriers[$idCal]['nom']);  // ajoute un champ avec le nom simplifié
				}
				// supprime les \
				stripslashes_deep($calendriers);

				// transmets les ressources à smarty
				$smarty->assign('calendriers',$calendriers);
				
				// si aucune sélections de calendrier est demandée, affiche tous ceux qui sont autorisés
			}else{
				// on ne garde que les calendriers qui sont autorisés
				$selectionCalendrier = $calendriersAutorises; // $selectionCalendrier est utilisé plus loin, il faut donc le définir
								
				$calendriers = array();
				foreach ($calendriersAutorises as $key => $idCal) {
					$calendriers[$idCal] = $calendrierManager->getCalendrier($idCal);
					$calendriers[$idCal]['nomSimplifie'] = simplifieNom($calendriers[$idCal]['nom']);  // ajoute un champ avec le nom simplifié
				}
				// supprime les \
				stripslashes_deep($calendriers);

				// transmets les ressources à smarty
				$smarty->assign('calendriers',$calendriers);
			}
			
		} // ressource unique	
		
		/////////////// suivant le format de sortie on prépare les données utiles à l'affichage /////// 
				
		if ($outputFormat=='html') {
			
			///////////////////// Crée des liens de navigation pour l'application //////////////////////////////////////

			// détermine sur quel jour le calendrier doit être placé, ainsi que les dates dérivées.
			$dateSemaineProchaine = date('Y-m-d',strtotime($dateCourante." +7 day"));
			$dateSemainePassee = date('Y-m-d',strtotime($dateCourante." -7 day"));

			// crée les liens de navigations entre les mois
			$dateMoisProchain = date('Y-m-d',strtotime($dateCourante." +1 month"));
			$dateMoisPasse = date('Y-m-d',strtotime($dateCourante." -1 month"));
			$dateAujourdhui = date('Y-m-d');
			$smarty->assign('dateAujourdhui',$dateAujourdhui);
			
			// fourni juste les dates ce qui est plus utile pour faire une composition javascript d'url
			$smarty->assign('dateMoisPasse',$dateMoisPasse);
			$smarty->assign('dateMoisProchain',$dateMoisProchain);
			$smarty->assign('dateSemainePassee',$dateSemainePassee);
			$smarty->assign('dateSemaineProchaine',$dateSemaineProchaine);

			// url qui a été appelée
			$urlCourante = "http://".$serveur.$_SERVER['REQUEST_URI'];
			$urlSansVue = str_replace(array('&vue=mois','&vue=semaine','&vue=liste','?vue=liste','vue=semaine','vue=mois'),"",$urlCourante); // on supprime les demandes de vue pour ne pas les reprendre en forment une nouvelle url de demande de vue
			
			// $urlSansDate est une urlcourante sans la partie &datecourante=2009-01-19
			$pattern = "/(&)?datecourante=+[0-9]{4}-[0-9]{2}-[0-9]{2}/i";
			$urlSansDate =  preg_replace($pattern,"",$urlCourante);
			
			//todo: => il arrive qu'à la suite d'une suppression de paramètre on se retrouve avec ?& à la suite. le navigateur comprend mais ce serait plus joli sans...
			
			// est ce que l'url contient des paramètres ?  (donc le caractère '?')
			$paramPresent = strpos($urlCourante,'?'); // retourne false si le ? n'est pas trouvé
			
			if ($paramPresent===false) {
				$delimiteur = "?";
			}else{
				$delimiteur = '&amp;';
			}
			
			$smarty->assign('urlSemainePrecedente',$urlSansDate.$delimiteur."datecourante=".$dateSemainePassee);
			$smarty->assign('urlSemaineSuivante',$urlSansDate.$delimiteur."datecourante=".$dateSemaineProchaine);
			$smarty->assign('urlAujourdhui',$urlSansDate);
			
			if ($typeDeVue!='mois') {
				$smarty->assign('urlVueMois',$urlSansVue.$delimiteur."vue=mois");
			}
			if ($typeDeVue!="semaine") {
				$smarty->assign('urlVueSemaine',$urlSansVue.$delimiteur."vue=semaine");
			}
			if ($typeDeVue!="liste") {
				$smarty->assign('urlVueListe',$urlSansVue.$delimiteur."vue=liste");
			}
			
			$alternateUrlPdf = str_replace("html","pdf",$urlCourante);  // todo... ne remplace pas & par &amp; ... prince ne risque de pas aimer trop
			$smarty->assign('urlVueListePdf',$alternateUrlPdf);


			// 
			// /// liens pour la navigation dans le calendrier.  On différentie le cas où il y a un calendrier sélectionné ou tous... todo: mais que faire pour obtenir la liste précise d'un calendrier
			// if (!empty($idCalendrier)) {
			// 	if (empty($ressourceTags)) {
			// 		$smarty->assign('urlSemainePrecedente',"/calendrier/".$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateSemainePassee);
			// 		$smarty->assign('urlSemaineSuivante',"/calendrier/".$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateSemaineProchaine);
			// 		$smarty->assign('urlAujourdhui',"/calendrier/".$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".date('Y-m-d'));
			// 
			// 		// choix de la vue courante
			// 		$smarty->assign('urlVueMois',"/calendrier/".$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateCourante."&amp;vue=mois");
			// 		$smarty->assign('urlVueSemaine',"/calendrier/".$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateCourante."&amp;vue=semaine");
			// 		$smarty->assign('urlVueListe',"/calendrier/".$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateCourante."&amp;vue=liste");
			// 
			// 		// exportation pdf de la liste
			// 		$smarty->assign('urlVueListePdf',"/calendrier/".$idCalendrier."-".$calendrier['nomSimplifie'].".pdf?datecourante=".$dateCourante."&amp;vue=semaine");
			// 	}else{
			// 
			// 		$smarty->assign('urlSemainePrecedente',"/calendrier/".$ressourceTags."".$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateSemainePassee);
			// 		$smarty->assign('urlSemaineSuivante',"/calendrier/".$ressourceTags."".$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateSemaineProchaine);
			// 		$smarty->assign('urlAujourdhui',"/calendrier/".$ressourceTags."".$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".date('Y-m-d'));
			// 
			// 		// choix de la vue courante
			// 		$smarty->assign('urlVueMois',"/calendrier/".$ressourceTags."".$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateCourante."&amp;vue=mois");
			// 		$smarty->assign('urlVueSemaine',"/calendrier/".$ressourceTags."".$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateCourante."&amp;vue=semaine");
			// 		$smarty->assign('urlVueListe',"/calendrier/".$ressourceTags."".$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateCourante."&amp;vue=liste");
			// 
			// 		// exportation pdf de la liste
			// 		$smarty->assign('urlVueListePdf',"/calendrier/".$ressourceTags."".$idCalendrier."-".$calendrier['nomSimplifie'].".pdf?datecourante=".$dateCourante."&amp;vue=semaine");
			// 	}
			// }else{
			// 	if (empty($ressourceTags)) {
			// 		$smarty->assign('urlSemainePrecedente',"/calendrier/calendrier.html?datecourante=".$dateSemainePassee);
			// 		$smarty->assign('urlSemaineSuivante',"/calendrier/calendrier.html?datecourante=".$dateSemaineProchaine);
			// 		$smarty->assign('urlAujourdhui',"/calendrier/calendrier.html?datecourante=".date('Ycalendriermcalendrierd'));
			// 
			// 		// choix de la vue courante
			// 		$smarty->assign('urlVueMois',"/calendrier/calendrier.html?datecourante=".$dateCourante."&amp;vue=mois");
			// 		$smarty->assign('urlVueSemaine',"/calendrier/calendrier.html?datecourante=".$dateCourante."&amp;vue=semaine");
			// 		$smarty->assign('urlVueListe',"/calendrier/calendrier.html?datecourante=".$dateCourante."&amp;vue=liste");
			// 
			// 		// exportation pdf de la liste
			// 		$smarty->assign('urlVueListePdf',"/calendrier/calendrier.pdf?datecourante=".$dateCourante."&amp;vue=semaine");
			// 	}else{
			// 
			// 		$smarty->assign('urlSemainePrecedente',"/calendrier/".$ressourceTags."calendrier.html?datecourante=".$dateSemainePassee);
			// 		$smarty->assign('urlSemaineSuivante',"/calendrier/".$ressourceTags."calendrier.html?datecourante=".$dateSemaineProchaine);
			// 		$smarty->assign('urlAujourdhui',"/calendrier/".$ressourceTags."calendrier.html?datecourante=".date('Ycalendriermcalendrierd'));
			// 
			// 		// choix de la vue courante
			// 		$smarty->assign('urlVueMois',"/calendrier/".$ressourceTags."calendrier.html?datecourante=".$dateCourante."&amp;vue=mois");
			// 		$smarty->assign('urlVueSemaine',"/calendrier/".$ressourceTags."calendrier.html?datecourante=".$dateCourante."&amp;vue=semaine");
			// 		$smarty->assign('urlVueListe',"/calendrier/".$ressourceTags."calendrier.html?datecourante=".$dateCourante."&amp;vue=liste");
			// 
			// 		// exportation pdf de la liste
			// 		$smarty->assign('urlVueListePdf',"/calendrier/".$ressourceTags."calendrier.pdf?datecourante=".$dateCourante."&amp;vue=semaine");
			// 	}
			// }
			
			
			
		
		
			///////////////////// Va chercher les événements en fonction du type de vue //////////////////////////////////////
		
			// On va chercher tous les événements correspondants à la tranche demandée. (qui est fonction du type de vue)
			// Il y a 4 types de vue possibles. Par semaine, par mois, par année ou par liste.
			// On va donc chercher les données en conséquence.
		
			/*********** Semaine ********************/
			if ($typeDeVue=="semaine") {
			
				////////// -------  Evénements qui ne sont PAS sur des jours entiers  ---------- /////////////
			
				//date du lundi de la semaine qui contient la date courante
				if (date('N',strtotime($dateCourante))!='1') { // si le jour n'est pas déjà un lundi
					$dateLundi= date('Y-m-d H:i:s', strtotime($dateCourante.' last Monday'));  // last Monday retourne le lundi de la semaine d'avant quand le jour fourni est déjà un lundi
				}else{
					$dateLundi = $dateCourante;
				}
			
				$jourLundi = date('Y-m-d',strtotime($dateLundi));
				$jourLundiTimestamp = strtotime($dateLundi);
			
		
				// fourni les dates des jours
				$dates = array();
				for ($i=0; $i < 7; $i++) { 
					$dateDuJour = date('Y-m-d H:i:s', strtotime($dateLundi.' +'.$i.'day'));
					$uneDate['dateHumaine'] = __(date('D', strtotime($dateDuJour)))." ".date('j ', strtotime($dateDuJour)). __(date('M', strtotime($dateDuJour)));
					$uneDate['dateJour'] = date('Y-m-d', strtotime($dateDuJour));
					$uneDate['idJour'] = $i;
					$dates[] = $uneDate; 
				}
		
				$smarty->assign('dates',$dates);
			
				// dernier jour du calendrier de la semaine
				$jourDimanche = date('Y-m-d',strtotime($dateLundi." +6 day"));
			
				//--filtre  le paramètre $queryFiltre permet de filtrer les événements à la demande
			
				// liste de id des événements que l'on veut afficher entre le lundi matin à 0h et le dimanche soir à 24h00
				// on ne prend pas les événements qui sont sur des jours entiers. Ceux-ci sont traités différement
				$listeEvenement = $evenementManager->getListeEvenementsSaufJourEntier(date('Y-m-d 00:00:00',strtotime($dateLundi)),date('Y-m-d 24:00:00',strtotime($jourDimanche)),"date_debut asc",$queryFiltre);
				//$listeEvenement = array(8,3,4,5,6);
			
				// va chercher la liste des événements à cheval sur la fenêtre d'affichage.
				$listeEvenementACheval = $evenementManager->getListeEvenementsSurLeMoment(date('Y-m-d 00:00:00',strtotime($dateLundi)),'false',$queryFiltre);
			
				// ajoute les événements à cheval
				$listeEvenement = array_merge($listeEvenement,$listeEvenementACheval);
				
				
				
				//////////////////  on filtre les événements selon le(s) calendriers demandés ////////////
				
				// si des calendriers précis sont fournis. On filtre, sinon on ne filtre rien, donc on prend tout.
				if (!empty($selectionCalendrier)) {
					
					$eventsInCalendar = array();
					foreach ($selectionCalendrier as $key => $idCal) {
						
						// va chercher les id des événements qui correspondent au calendrier voulu
						$events = $evenementManager->getEvenementsCalendrier($idCal);
						$eventsInCalendar = array_merge($eventsInCalendar,$events);
					}
					// ne garde que les id des événements qui sont dans le calendrier
					$listeEvenement = array_intersect($eventsInCalendar,$listeEvenement);
				}

				//////////////// on filtre les événements selon les tags demandés ////////////////

				if (!empty($tags)) {
					 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
					$taggedElements = $groupeManager->getElementByTags($tags,'evenement');
					
					$listeEvenement = array_intersect($taggedElements,$listeEvenement);
				}



				///////////////// on filtre les événements selon les restrictions courantes ///////////
				// .... todo.  Pour l'instant c'est du tout ou rien fixé ici
				$editable = true;  // cette variable sert aux templates à savoir si il faut afficher les liens qui appellent les fonctions javascript d'édition.
				$smarty->assign('editable',$editable);
			
			
				////////// -------   Création des blocs pour des événements qui sont PAS sur des jours entiers  ------- /////////////

				// à partir de la liste des id des événements, crée une liste de bloc à afficher avec leur coordonnées.
				$blocsEvenements = array();
				foreach ($listeEvenement as $key => $idEvenement) {
					$unBloc = array();
					$unBloc['id'] = $idEvenement;

					// obtient l'événement complet... extrait les dates début et fin, puis les converti de manière humainement compréhensible et traduite.
					$evenement = $evenementManager->getEvenement($idEvenement);
					//print_r($evenement);
					
						// va chercher la couleur de l'événement
						// si on affiche qu'un seul calendrier, tous les évéenements auront la même couleur, celle du calendrier, donc on l'attribue directement. Sinon on fait une requête pour chaque événement pour trouver ça couleur, ce qui prend plus de temps.
						if (count($selectionCalendrier)==1 && isset($calendrier)) {  // il peut y avoir 2 cas dans lesquels on a un seul calendrier. Soit c'est un choix et là on connait la couleur. Soit les droits d'accès restreignent jusqu'à n'avoir plus qu'un calendrier !
							$unBloc['color'] = $calendrier['couleur'];
							$unBloc['borderColor'] = getCouleurBordure('#'.$calendrier['couleur']);
						}else{
							$calendrierDeLEvenement = $calendrierManager->getCalendrier($evenement['id_calendrier']);
							$unBloc['color'] = $calendrierDeLEvenement['couleur'];
							$unBloc['borderColor'] = getCouleurBordure('#'.$calendrierDeLEvenement['couleur']);
						}

					
					// détermine si le bloc de l'événement est à cheval sur plusieurs jours ou pas
					$jourDebut = date('Y-m-d',strtotime($evenement['date_debut']));
					$jourFin = date('Y-m-d',strtotime($evenement['date_fin']));
					$jourDebutTimestamp = strtotime($jourDebut);
					$jourFinTimestamp = strtotime($jourFin);

					// détermine si l'événement commence avant la fenêtre visible.
					$aCheval = false;
					if ($jourDebutTimestamp < $jourLundiTimestamp) {
						$aCheval = true;
						$evenement['date_debut'] = $jourLundi." 00:00:01";
						$jourDebutTimestamp = strtotime($jourLundi." 00:00:01");
					}
					// détermine si bloc de l'événement est à cheval sur plusieurs jours ou pas
					if ($jourDebutTimestamp==$jourFinTimestamp) {
						// fourni les infos d'affichage pour un bloc qui se trouve sur le même jour
						$unBloc['nom'] = stripcslashes($evenement['nom']);
						$unBloc['dateDebut'] = dateTime2Humain($evenement['date_debut']);
						$unBloc['heureDebut'] = dateTime2HeureHumain($evenement['date_debut']);
						$unBloc['dateFin'] = dateTime2Humain($evenement['date_fin']);

						$unBloc['top'] = $evenementManager->getTopPixel($evenement['date_debut']);
						$unBloc['left'] = $evenementManager->getLeftPixel($evenement['date_debut']);
						$unBloc['height'] = $evenementManager->getDureePixel($evenement['date_debut'],$evenement['date_fin']);
						$unBloc['nomHeight'] = $unBloc['height'] - 13;
						$unBloc['class'] = "drag ";
						$unBloc['resizeHandle'] = "resizeHandle";

						// ajoute l'élément à la liste
						$blocsEvenements[] = $unBloc;
					}else{
						// crée plusieurs blocs d'affichage pour un événement à cheval sur plusieurs jours
						// crée un bloc entre le début de l'événement et minuit le jour même.  Cet événement est le seul à avoir la date et le nom indiqué.

						// attention bug ! ... crée des blocs inutiles pour des morceaux d'événements hors de la zone visible.
						$unBloc['nom'] = stripcslashes($evenement['nom']);
						$unBloc['dateDebut'] = dateTime2Humain($evenement['date_debut']);
						$unBloc['heureDebut'] = dateTime2HeureHumain($evenement['date_debut']);
						$unBloc['dateFin'] = dateTime2Humain($evenement['date_fin']);

						$unBloc['top'] = $evenementManager->getTopPixel($evenement['date_debut']);
						$unBloc['left'] = $evenementManager->getLeftPixel($evenement['date_debut']);
						$unBloc['height'] = $evenementManager->getDureePixel($evenement['date_debut'],date('Y-m-d 23:59:59',strtotime($evenement['date_debut'])));  // début de l'événement et jour du début à minuit
		 				$unBloc['nomHeight'] = $unBloc['height'] - 13;
						$unBloc['class'] = "";
						$unBloc['resizeHandle'] = "";

						// ajoute l'élément à la liste
						$blocsEvenements[] = $unBloc;

			// seulement si le bloc n'est pas en dehors de la zone visible.
						if (strtotime($evenement['date_fin']) < strtotime($jourDimanche."+ 1 day")) {
							// crée un bloc entre minuit le jour de fin et la fin de l'événement
							$unBloc['nom'] = '';
							$unBloc['dateDebut'] = dateTime2Humain($evenement['date_debut']);
							$unBloc['heureDebut'] = ''; //affiché seulement pour le premier bloc de l'événement
							$unBloc['dateFin'] = dateTime2Humain($evenement['date_fin']);

							$unBloc['top'] = '0'; // à minuit de toute façon
							$unBloc['left'] = $evenementManager->getLeftPixel($evenement['date_fin']);
							$unBloc['height'] = $evenementManager->getDureePixel(date('Y-m-d 00:00:00',strtotime($evenement['date_fin'])),$evenement['date_fin']);  // jour de la fin de l'événement à 0h00 jusqu'à la fin de l'événement
			 				$unBloc['nomHeight'] = $unBloc['height'] - 13;
							$unBloc['class'] = "";
							$unBloc['resizeHandle'] = "";

							// ajoute l'élément à la liste
							$blocsEvenements[] = $unBloc;
						}


						//au besoin crée des jours complets entre le jour de début et le jour de fin.
						$dateBlocLendemain = date('Y-m-d',strtotime($evenement['date_debut']." +1 day")); // on attribue un jour de plus que le début pour le bloc du lendemain

						// attention bug... pose tout l'événement sur la même semaine!! même si la fin de l'événement est invisible car la semaine suivante.

						// si la date du bloc du lendemain n'est pas encore le jour du bloc de fin et que l'on est pas encore hors de la zone visible du calendrier
						while ($dateBlocLendemain!=$jourFin && (strtotime($dateBlocLendemain) < strtotime($jourDimanche."+ 1 day"))) {
							// crée un bloc pour ce jour entier
							$unBloc['nom'] = '';
							$unBloc['dateDebut'] = dateTime2Humain($evenement['date_debut']);
							$unBloc['heureDebut'] = ''; //affiché seulement pour le premier bloc de l'événement
							$unBloc['dateFin'] = dateTime2Humain($evenement['date_fin']);

							$unBloc['top'] = '0'; // à minuit de toute façon
							$unBloc['left'] = $evenementManager->getLeftPixel($dateBlocLendemain." 00:00:00");
							$unBloc['height'] = '768';  // maximum
			 				$unBloc['nomHeight'] = $unBloc['height'] - 13;
							$unBloc['class'] = "";
							$unBloc['resizeHandle'] = "";

							// ajoute l'élément à la liste
							$blocsEvenements[] = $unBloc;

							$dateBlocLendemain = date('Y-m-d',strtotime($dateBlocLendemain." +1 day")); // on met un jour de plus pour le bloc du lendemain
						}//while

					}// if


				} // foreach
				$smarty->assign('evenements',$blocsEvenements);

			
					
				/////////// ----------------- Evénements qui sont sur des jours entiers  ------------- //////////
			
				// bug.. ne voit pas les événements qui commencent à 00:00:00 !!?
			
				// fourni la date du jour comme id du bloc
				// dates de la semaine du calendrier
				$jours = array();
				$jours['0'] = date('Y-m-d',strtotime($dateLundi));  // jour 0 de la semaine 0  => lundi
				$jours['1'] = date('Y-m-d',strtotime($dateLundi." +1 day"));
				$jours['2'] = date('Y-m-d',strtotime($dateLundi." +2 day"));
				$jours['3'] = date('Y-m-d',strtotime($dateLundi." +3 day"));
				$jours['4'] = date('Y-m-d',strtotime($dateLundi." +4 day"));
				$jours['5'] = date('Y-m-d',strtotime($dateLundi." +5 day"));
				$jours['6'] = date('Y-m-d',strtotime($dateLundi." +6 day")); // Dimanche
			
				$smarty->assign('jours',$jours);
			
				// va chercher les événements de jour entier qui commencent avant la zone visible
				$listeEvenementACheval = $evenementManager->getListeEvenementsSurLeMoment(date('Y-m-d 00:00:00',strtotime($dateLundi)),'true',$queryFiltre);

				$blocsEvenementsJourEntier = array();
				
				// parcours le tableau pour chaque jour
				for ($jour=0; $jour < 7; $jour++) { 

					// obtient la date du jour
					$dateDuJour = $jours[$jour];

					// liste des id des événements "Jour entier" qui commencent le jour voulu. 24h00 semble possible comme valeur
					$listeEvenement = $evenementManager->getListeEvenementsJourEntier(date('Y-m-d 00:00:00',strtotime($dateDuJour)),date('Y-m-d 24:00:00',strtotime($dateDuJour)),'date_debut asc',$queryFiltre);
					//--filtre  le paramètre $queryFiltre permet de filtrer les événements à la demande

					// seulement le premier jour, ajoute les événements à cheval
					if ($jour==0) {
						$listeEvenement = array_merge($listeEvenement,$listeEvenementACheval);
					}
					
					
					//////////////////  on filtre les événements selon le(s) calendriers demandés ////////////
					// 
					// si des calendriers précis sont fournis. On filtre, sinon on ne filtre rien, donc on prend tout.
					if (!empty($selectionCalendrier)) {

						$eventsInCalendar = array();
						foreach ($selectionCalendrier as $key => $idCal) {

							// va chercher les id des événements qui correspondent au calendrier voulu
							$events = $evenementManager->getEvenementsCalendrier($idCal);
							$eventsInCalendar = array_merge($eventsInCalendar,$events);
						}
						// ne garde que les id des événements qui sont dans le calendrier
						$listeEvenement = array_intersect($eventsInCalendar,$listeEvenement);
					}

					//////////////// on filtre les événements selon les tags demandés ////////////////

					if (!empty($tags)) {
						 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
						$taggedElements = $groupeManager->getElementByTags($tags,'evenement');

						$listeEvenement = array_intersect($taggedElements,$listeEvenement);
					}

					///////////////// on filtre les événements selon les restrictions courantes ///////////
					// .... todo
					$editable = true;  // cette variable sert aux templates à savoir si il faut afficher les liens qui appellent les fonctions javascript d'édition.
					$smarty->assign('editable',$editable);

						// à partir de la liste des id des événements, crée une liste de bloc à afficher avec leurs coordonnées.
						foreach ($listeEvenement as $key => $idEvenement){
							$unBloc = array();
							$unBloc['id'] = $idEvenement;

							// obtient l'événement complet... extrait les dates début et fin, puis les converti de manière humainement compréhensible et traduite.
							$evenement = $evenementManager->getEvenement($idEvenement);
							
							
								// va chercher la couleur de l'événement
								// si on affiche qu'un seul calendrier, tous les évéenements auront la même couleur, celle du calendrier, donc on l'attribue directement. Sinon on fait une requête pour chaque événement pour trouver ça couleur, ce qui prend plus de temps.
								if (count($selectionCalendrier)==1 && isset($calendrier)) {  // il peut y avoir 2 cas dans lesquels on a un seul calendrier. Soit c'est un choix et là on connait la couleur. Soit les droits d'accès restreignent jusqu'à n'avoir plus qu'un calendrier !
									$unBloc['color'] = $calendrier['couleur'];
									$unBloc['borderColor'] = getCouleurBordure('#'.$calendrier['couleur']);
								}else{
									$calendrierDeLEvenement = $calendrierManager->getCalendrier($evenement['id_calendrier']);
									$unBloc['color'] = $calendrierDeLEvenement['couleur'];
									$unBloc['borderColor'] = getCouleurBordure('#'.$calendrierDeLEvenement['couleur']);
								}

							// détermine si le bloc de l'événement est à cheval sur plusieurs jours ou pas
							$jourDebut = date('Y-m-d',strtotime($evenement['date_debut']));
							$jourFin = date('Y-m-d',strtotime($evenement['date_fin']));
							$jourDebutTimestamp = strtotime($jourDebut);
							$jourFinTimestamp = strtotime($jourFin);

							// détermine si l'événement commence avant la fenêtre visible.
							$aCheval = false;
							if ($jourDebutTimestamp < $jourLundiTimestamp) {
								$aCheval = true;
								$evenement['date_debut'] = $jourLundi." 00:00:01";
								$jourDebutTimestamp = strtotime($jourLundi." 00:00:01");
							}

							if ($jourDebut==$jourFin) {
								// fourni les infos d'affichage pour un bloc qui se trouve sur le même jour
								$unBloc['nom'] = stripcslashes($evenement['nom']);
								$unBloc['dateDebut'] = dateTime2Humain($evenement['date_debut']);
								$unBloc['heureDebut'] = dateTime2HeureHumain($evenement['date_debut']);
								$unBloc['dateFin'] = dateTime2Humain($evenement['date_fin']);
								$unBloc['jourEntier'] = $evenement['jour_entier'];

								// heure de début et de fin utilisées pour composer la nouvelle date de mise à jour quand on change uniquement le jour.
								$unBloc['hDebut'] = date('H:i:s',strtotime($evenement['date_debut']));
								$unBloc['hFin'] = date('H:i:s',strtotime($evenement['date_fin']));

								$unBloc['class'] = "dragJourEntier ";
								$unBloc['width'] = "100";
								$unBloc['nbJour'] = "0";  // différence de jour entre le début et la fin

								// ajoute l'élément à la liste des éléments attribué à la case.
								$blocsEvenementsJourEntier[$jour][] = $unBloc;
							}else{ // évéenements sur plusieurs jours.
								// implique le morcellement du bloc et le calcul des largeurs.
								// $unBloc['height'] = $evenementManager->getDureePixel($evenement['date_debut'],$evenement['date_fin']);

								// inutile ??
								// if ($aCheval) {
								// 	$noSemaine = date('W',strtotime($jourLundi." 00:00:01"));
								// 	echo " semaine"; //ici
								// }else{
								// 	$noSemaine = date('W',strtotime($evenement['date_debut'])); // numéro de la semaine du début de l'événement
								// }

								$nbJour = ceil(($jourFinTimestamp - $jourDebutTimestamp)/86400)+1;  // la différence est donnée en nb de secondes donc on adapte en nombre de jours. On adapte au jour entier supérieur et on ajoute 1 pour obtenir le premier jour.

								// calcul le nombre de jour qu'il reste dans la semaine (la même ligne)
								$nbJourLigne = 7-$jour;

								// si l'événement tient sur la même ligne
								if ($nbJour < ($nbJourLigne+1)) {
									// fourni les infos d'affichage pour un bloc qui se trouve sur la même semaine
									$unBloc['nom'] = stripcslashes($evenement['nom']);
									$unBloc['dateDebut'] = dateTime2Humain($evenement['date_debut']);
									$unBloc['heureDebut'] = dateTime2HeureHumain($evenement['date_debut']);
									$unBloc['dateFin'] = dateTime2Humain($evenement['date_fin']);
									$unBloc['jourEntier'] = $evenement['jour_entier'];

									// heure de début et de fin utilisé pour composer la nouvelle date de mise à jour quand on change uniquement le jour.
									$unBloc['hDebut'] = date('H:i:s',strtotime($evenement['date_debut']));
									$unBloc['hFin'] = date('H:i:s',strtotime($evenement['date_fin']));

									// si l'événement commence avant la fenêtre visible.
									if ($aCheval) {
										$unBloc['class'] = " ";
									}else{
										$unBloc['class'] = "dragJourEntier ";
									}
									$taille = $nbJour * 101;
									$unBloc['width'] = $taille;
									$unBloc['nbJour'] = $nbJour-1; // permet de calculer la date de fin en fonction de la date de début. Ce n'est pas le nombre de cases prise, c'est la différence entre le début et la fin. D'où le nbJour-1

									// ajoute l'élément à la liste des éléments attribué à la case.
									$blocsEvenementsJourEntier[$jour][] = $unBloc;

								}else{ // si l'événement ne tient pas sur la même ligne (donc à cheval sur plusieurs semaine)

									// on crée un bloc sur la semaine de début.. et rien après vu que c'est la semaine suivante.
									$unBloc['nom'] = stripcslashes($evenement['nom']);

									$unBloc['dateDebut'] = dateTime2Humain($evenement['date_debut']);
									$unBloc['heureDebut'] = dateTime2HeureHumain($evenement['date_debut']);
									$unBloc['dateFin'] = dateTime2Humain($evenement['date_fin']);
									$unBloc['jourEntier'] = $evenement['jour_entier'];

									// heure de début et de fin utilisé pour composer la nouvelle date de mise à jour quand on change uniquement le jour.
									$unBloc['hDebut'] = date('H:i:s',strtotime($evenement['date_debut']));
									$unBloc['hFin'] = date('H:i:s',strtotime($evenement['date_fin']));

									// si l'événement commence avant la fenêtre visible.
									if ($aCheval) {
										$unBloc['class'] = " ";
									}else{
										$unBloc['class'] = "dragJourEntier ";
									}
									$taille = $nbJourLigne * 101;
									$unBloc['width'] = $taille;
									$unBloc['nbJour'] = $nbJour-1; // permet de calculer la date de fin en fonction de la date de début. Ce n'est pas le nombre de cases prise, c'est la différence entre le début et la fin. D'où le nbJour-1

									// ajoute l'élément à la liste des éléments attribués à la case.
									$blocsEvenementsJourEntier[$jour][] = $unBloc;

									// ... la suite de l'événement est non visible, donc on ne l'affiche pas !



								} // if sur la même semaine

							} // if sur le même jour

						} // foreach

				}// for jour

				$smarty->assign('evenementsJourEntier',$blocsEvenementsJourEntier);
				//print_r($blocsEvenementsJourEntier);
				
			
		
			/*********** Mois ********************/
			}elseif ($typeDeVue=="mois") {
				
				////////////////  construit le calendrier avec une vue mensuelle  ///////////////
				
				$afficheMoisCourant = __(date('M', strtotime($dateCourante)))." ".__(date('Y', strtotime($dateCourante)));
				$smarty->assign('afficheMoisCourant',$afficheMoisCourant);
				
				// il faut créer le calendrier. Nous avons la date courante.
				// Il faut trouver le premier lundi avant le 1er jour du mois identique à la date courante.
				
				$moisCourant = date('Y-m', strtotime($dateCourante)); // année + mois sous forme de chiffre 01-12  avec les 0 initiaux. EX: 2008-04
				$premierJourDuMois = $moisCourant."-01";  // date du premier jour du mois dans le genre: 2008-04-01
				
				//date du lundi avant le 1er jour du mois
				if (date('N',strtotime($premierJourDuMois))!='1') { // si le jour n'est pas déjà un lundi
					$datePremierJourCalendrier= date('Y-m-d H:i:s', strtotime($premierJourDuMois.' last Monday'));  // last Monday retourne le lundi de la semaine d'avant quand le jour fourni est déjà un lundi
				}else{
					$datePremierJourCalendrier = $premierJourDuMois;
				}
				
				$jourPremierJourCalendrier= date('Y-m-d', strtotime($datePremierJourCalendrier)); 
				$jourPremierJourCalendrierTimestamp = strtotime($jourPremierJourCalendrier);
				
				// dates de la semaine0 du calendrier
				$semaine[] = array();
				$semaine['0'] = array();
				$semaine['0']['0'] = date('d',strtotime($datePremierJourCalendrier));  // jour 0 de la semaine 0  => lundi
				$semaine['0']['1'] = date('d',strtotime($datePremierJourCalendrier." +1 day"));
				$semaine['0']['2'] = date('d',strtotime($datePremierJourCalendrier." +2 day"));
				$semaine['0']['3'] = date('d',strtotime($datePremierJourCalendrier." +3 day"));
				$semaine['0']['4'] = date('d',strtotime($datePremierJourCalendrier." +4 day"));
				$semaine['0']['5'] = date('d',strtotime($datePremierJourCalendrier." +5 day"));
				$semaine['0']['6'] = date('d',strtotime($datePremierJourCalendrier." +6 day")); // Dimanche
				
				// dates de la semaine1 du calendrier
				$semaine['1'] = array();
				$semaine['1']['0'] = date('d',strtotime($datePremierJourCalendrier." +7 day")); // jour 0 de la semaine 1  => lundi
				$semaine['1']['1'] = date('d',strtotime($datePremierJourCalendrier." +8 day"));
				$semaine['1']['2'] = date('d',strtotime($datePremierJourCalendrier." +9 day"));
				$semaine['1']['3'] = date('d',strtotime($datePremierJourCalendrier." +10 day"));
				$semaine['1']['4'] = date('d',strtotime($datePremierJourCalendrier." +11 day"));
				$semaine['1']['5'] = date('d',strtotime($datePremierJourCalendrier." +12 day"));
				$semaine['1']['6'] = date('d',strtotime($datePremierJourCalendrier." +13 day")); // Dimanche
				
				// dates de la semaine2 du calendrier
				$semaine['2'] = array();
				$semaine['2']['0'] = date('d',strtotime($datePremierJourCalendrier." +14 day")); // jour 0 de la semaine 2  => lundi
				$semaine['2']['1'] = date('d',strtotime($datePremierJourCalendrier." +15 day"));
				$semaine['2']['2'] = date('d',strtotime($datePremierJourCalendrier." +16 day"));
				$semaine['2']['3'] = date('d',strtotime($datePremierJourCalendrier." +17 day"));
				$semaine['2']['4'] = date('d',strtotime($datePremierJourCalendrier." +18 day"));
				$semaine['2']['5'] = date('d',strtotime($datePremierJourCalendrier." +19 day"));
				$semaine['2']['6'] = date('d',strtotime($datePremierJourCalendrier." +20 day")); // Dimanche
				
				// dates de la semaine3 du calendrier
				$semaine['3'] = array();
				$semaine['3']['0'] = date('d',strtotime($datePremierJourCalendrier." +21 day")); // jour 0 de la semaine 3  => lundi
				$semaine['3']['1'] = date('d',strtotime($datePremierJourCalendrier." +22 day"));
				$semaine['3']['2'] = date('d',strtotime($datePremierJourCalendrier." +23 day"));
				$semaine['3']['3'] = date('d',strtotime($datePremierJourCalendrier." +24 day"));
				$semaine['3']['4'] = date('d',strtotime($datePremierJourCalendrier." +25 day"));
				$semaine['3']['5'] = date('d',strtotime($datePremierJourCalendrier." +26 day"));
				$semaine['3']['6'] = date('d',strtotime($datePremierJourCalendrier." +27 day")); // Dimanche
				
				// dates de la semaine4 du calendrier
				$semaine['4'] = array();
				$semaine['4']['0'] = date('d',strtotime($datePremierJourCalendrier." +28 day")); // jour 0 de la semaine 4  => lundi
				$semaine['4']['1'] = date('d',strtotime($datePremierJourCalendrier." +29 day"));
				$semaine['4']['2'] = date('d',strtotime($datePremierJourCalendrier." +30 day"));
				$semaine['4']['3'] = date('d',strtotime($datePremierJourCalendrier." +31 day"));
				$semaine['4']['4'] = date('d',strtotime($datePremierJourCalendrier." +32 day"));
				$semaine['4']['5'] = date('d',strtotime($datePremierJourCalendrier." +33 day"));
				$semaine['4']['6'] = date('d',strtotime($datePremierJourCalendrier." +34 day")); // Dimanche
				
				$smarty->assign('semaine',$semaine);
	
				// crée les liens de navigations entre les mois
				$dateMoisProchain = date('Y-m-d',strtotime($dateCourante." +1 month"));
				$dateMoisPasse = date('Y-m-d',strtotime($dateCourante." -1 month"));
				$dateAujourdhui = date('Y-m-d');
				$smarty->assign('dateAujourdhui',$dateAujourdhui);
				
				// fourni juste les dates ce qui est plus utile pour faire une composition javascript d'url
				$smarty->assign('dateMoisPasse',$dateMoisPasse);
				$smarty->assign('dateMoisProchain',$dateMoisProchain);
			
				// url qui a été appelée
				$urlCourante = "http://".$serveur.$_SERVER['REQUEST_URI'];
				$urlSansVue = str_replace(array('&vue=mois','&vue=semaine','&vue=liste','?vue=liste','?vue=semaine','?vue=mois'),"",$urlCourante); // on supprime les demandes de vue pour ne pas les reprendre en forment une nouvelle url de demande de vue

				// $urlSansDate est une urlcourante sans la partie &datecourante=2009-01-19
				$pattern = "/(&)?datecourante=+[0-9]{4}-[0-9]{2}-[0-9]{2}/i";
				$urlSansDate =  preg_replace($pattern,"",$urlCourante);

				//todo: => il arrive qu'à la suite d'une suppression de paramètre on se retrouve avec ?& à la suite. le navigateur comprend mais ce serait plus joli sans...

				// est ce que l'url contient des paramètres ?  (donc le caractère '?')
				$paramPresent = strpos($urlCourante,'?'); // retourne false si le ? n'est pas trouvé

				if ($paramPresent===false) {
					$delimiteur = "?";
				}else{
					$delimiteur = '&amp;';
				}

				$smarty->assign('urlMoisPasse',$urlSansDate.$delimiteur."datecourante=".$dateMoisPasse);
				$smarty->assign('urlMoisProchain',$urlSansDate.$delimiteur."datecourante=".$dateMoisProchain);
				$smarty->assign('urlAujourdhui',$urlSansDate);
				
				// fourni jsute les dates ce qui est plus utile pour faire une composition javascript d'url
				$smarty->assign('dateMoisPasse',$dateMoisPasse);
				$smarty->assign('dateMoisProchain',$dateMoisProchain);

				if ($typeDeVue!='mois') {
					$smarty->assign('urlVueMois',$urlSansVue.$delimiteur."vue=mois");
				}
				if ($typeDeVue!="semaine") {
					$smarty->assign('urlVueSemaine',$urlSansVue.$delimiteur."vue=semaine");
				}
				if ($typeDeVue!="liste") {
					$smarty->assign('urlVueListe',$urlSansVue.$delimiteur."vue=liste");
				}

				$alternateUrlPdf = str_replace("html","pdf",$urlCourante);
				$smarty->assign('urlVueListePdf',$alternateUrlPdf);
			
				
				// if (empty($ressourceTags)) {
				// 					
				// 					$smarty->assign('urlMoisProchain',"/calendrier/".$idCalendrier."-".$calendrier['nomSimplifie'].".html?vue=mois&amp;datecourante=".$dateMoisProchain);
				// 					$smarty->assign('urlMoisPasse',"/calendrier/".$idCalendrier."-".$calendrier['nomSimplifie'].".html?vue=mois&amp;datecourante=".$dateMoisPasse);
				// 					$smarty->assign('urlAujourdhui',"/evenment/".$idCalendrier."-".$calendrier['nomSimplifie'].".html?vue=mois&amp;datecourante=".$dateAujourdhui);
				// 				
				// 					// choix de la vue courante
				// 					$smarty->assign('urlVueMois',"/calendrier/".$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateCourante."&amp;vue=mois");
				// 					$smarty->assign('urlVueSemaine',"/calendrier/".$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateCourante."&amp;vue=semaine");
				// 					$smarty->assign('urlVueListe',"/calendrier/".$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateCourante."&amp;vue=liste");
				// 				
				// 					// exportation pdf de la liste
				// 					$smarty->assign('urlVueListePdf',"/calendrier/".$idCalendrier."-".$calendrier['nomSimplifie'].".pdf?datecourante=".$dateCourante."&amp;vue=liste");
				// 				}else{
				// 				
				// 					$smarty->assign('urlMoisProchain',"/calendrier/".$ressourceTags.$idCalendrier."-".$calendrier['nomSimplifie'].".html?vue=mois&amp;datecourante=".$dateMoisProchain);
				// 					$smarty->assign('urlMoisPasse',"/calendrier/".$ressourceTags.$idCalendrier."-".$calendrier['nomSimplifie'].".html?vue=mois&amp;datecourante=".$dateMoisPasse);
				// 					$smarty->assign('urlAujourdhui',"/calendrier/".$ressourceTags.$idCalendrier."-".$calendrier['nomSimplifie'].".html?vue=mois&amp;datecourante=".$dateAujourdhui);
				// 				
				// 					// choix de la vue courante
				// 					$smarty->assign('urlVueMois',"/calendrier/".$ressourceTags.$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateCourante."&amp;vue=mois");
				// 					$smarty->assign('urlVueSemaine',"/calendrier/".$ressourceTags.$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateCourante."&amp;vue=semaine");
				// 					$smarty->assign('urlVueListe',"/calendrier/".$ressourceTags.$idCalendrier."-".$calendrier['nomSimplifie'].".html?datecourante=".$dateCourante."&amp;vue=liste");
				// 				
				// 					// exportation pdf de la liste
				// 					$smarty->assign('urlVueListePdf',"/calendrier/".$ressourceTags.$idCalendrier."-".$calendrier['nomSimplifie'].".pdf?datecourante=".$dateCourante."&amp;vue=liste");
				// 				}
				
				// fourni les dates des jours	
				$jourDeLaSemaine = array(__('Mon'),__('Tue'),__('Wed'),__('Thu'),__('Fri'),__('Sat'),__('Sun'));
				$smarty->assign('jourDeLaSemaine',$jourDeLaSemaine);
				
				// fourni la date du jour comme id du bloc
				// dates de la semaine0 du calendrier
				$jours[] = array();
				$jours['0'] = array();
				$jours['0']['0'] = date('Y-m-d',strtotime($datePremierJourCalendrier));  // jour 0 de la semaine 0  => lundi
				$jours['0']['1'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +1 day"));
				$jours['0']['2'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +2 day"));
				$jours['0']['3'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +3 day"));
				$jours['0']['4'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +4 day"));
				$jours['0']['5'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +5 day"));
				$jours['0']['6'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +6 day")); // Dimanche
				
				// dates de la semaine1 du calendrier
				$jours['1'] = array();
				$jours['1']['0'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +7 day")); // jour 0 de la semaine 1  => lundi
				$jours['1']['1'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +8 day"));
				$jours['1']['2'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +9 day"));
				$jours['1']['3'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +10 day"));
				$jours['1']['4'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +11 day"));
				$jours['1']['5'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +12 day"));
				$jours['1']['6'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +13 day")); // Dimanche
				
				// dates de la semaine2 du calendrier
				$jours['2'] = array();
				$jours['2']['0'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +14 day")); // jour 0 de la semaine 2  => lundi
				$jours['2']['1'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +15 day"));
				$jours['2']['2'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +16 day"));
				$jours['2']['3'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +17 day"));
				$jours['2']['4'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +18 day"));
				$jours['2']['5'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +19 day"));
				$jours['2']['6'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +20 day")); // Dimanche
				
				// dates de la semaine3 du calendrier
				$jours['3'] = array();
				$jours['3']['0'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +21 day")); // jour 0 de la semaine 3  => lundi
				$jours['3']['1'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +22 day"));
				$jours['3']['2'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +23 day"));
				$jours['3']['3'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +24 day"));
				$jours['3']['4'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +25 day"));
				$jours['3']['5'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +26 day"));
				$jours['3']['6'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +27 day")); // Dimanche
				
				// dates de la semaine4 du calendrier
				$jours['4'] = array();
				$jours['4']['0'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +28 day")); // jour 0 de la semaine 4  => lundi
				$jours['4']['1'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +29 day"));
				$jours['4']['2'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +30 day"));
				$jours['4']['3'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +31 day"));
				$jours['4']['4'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +32 day"));
				$jours['4']['5'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +33 day"));
				$jours['4']['6'] = date('Y-m-d',strtotime($datePremierJourCalendrier." +34 day")); // Dimanche
				
				$smarty->assign('jours',$jours);
				
				//////////////////  on va chercher les événements qui correspondent à la tranche horaire voulue ////////////
				
				// va chercher les événements qui commencent avant la zone visible
				$listeEvenementACheval = $evenementManager->getListeEvenementsSurLeMoment(date('Y-m-d 00:00:00',strtotime($premierJourDuMois)),'all',$queryFiltre);
				
				// on va créer un tableau qui contient tous les événements.
				// Ce tableau est un tableau de tableaux. Les clés de ce tableau sont les numéros des jours suivant les coordonnées semaine[0-4] et jour[0-6].
				// On parcours les 35 tableaux.
				// Pour chacun on obtient la date d'après le tableau: "$jours[][]"
				// Pour cette date on cherche les événements disponibles et on les mets dans un tableau.
				// On assigne ce dernier au tableau de événement en fonction de la clé d'entrée qui correspond à la case.
				
				$blocsEvenements = array();
				for ($semaine=0; $semaine < 5; $semaine++) { 
					
					for ($jour=0; $jour < 7; $jour++) { 
						
						// obtient la date du jour
						$dateDuJour = $jours[$semaine][$jour];
						
						// liste des id des événements qui commencent le jour voulu.
						$listeEvenement = $evenementManager->getListeEvenements(date('Y-m-d 00:00:00',strtotime($dateDuJour)),date('Y-m-d 24:00:00',strtotime($dateDuJour)),'date_debut asc',$queryFiltre);
						//--filtre  le paramètre $queryFiltre permet de filtrer les événements à la demande
						
						// seulement le premier jour du calendrier, ajoute les événements à cheval
						if ($jour==0 && $semaine==0) {
							$listeEvenement = array_merge($listeEvenement,$listeEvenementACheval);
						}					
						
						
						//////////////////  on filtre les événements selon le(s) calendriers demandés ////////////
						//
						// si des calendriers précis sont fournis. On filtre, sinon on ne filtre rien, donc on prend tout.
						if (!empty($selectionCalendrier)) {

							$eventsInCalendar = array();
							foreach ($selectionCalendrier as $key => $idCal) {

								// va chercher les id des événements qui correspondent au calendrier voulu
								$events = $evenementManager->getEvenementsCalendrier($idCal);
								$eventsInCalendar = array_merge($eventsInCalendar,$events);
							}
							// ne garde que les id des événements qui sont dans le calendrier
							$listeEvenement = array_intersect($eventsInCalendar,$listeEvenement);
						}

						//////////////// on filtre les événements selon les tags demandés ////////////////

						if (!empty($tags)) {
							 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
							$taggedElements = $groupeManager->getElementByTags($tags,'evenement');

							$listeEvenement = array_intersect($taggedElements,$listeEvenement);
						}

						///////////////// on filtre les événements selon les restrictions courantes ///////////
						// .... todo
						$editable = true;  // cette variable sert aux templates à savoir si il faut afficher les liens qui appellent les fonctions javascript d'édition.
						$smarty->assign('editable',$editable);
						
							// à partir de la liste des id des événements, crée une liste de bloc à afficher avec leur coordonnées.
							foreach ($listeEvenement as $key => $idEvenement){
								$unBloc = array();
								$unBloc['id'] = $idEvenement;

								// obtient l'événement complet... extrait les dates début et fin, puis les converti de manière humainement compréhensible et traduite.
								$evenement = $evenementManager->getEvenement($idEvenement);
								
									// va chercher la couleur de l'événement
									// si on affiche qu'un seul calendrier, tous les évéenements auront la même couleur, celle du calendrier, donc on l'attribue directement. Sinon on fait une requête pour chaque événement pour trouver ça couleur, ce qui prend plus de temps.
									if (count($selectionCalendrier)==1 && isset($calendrier)) {  // il peut y avoir 2 cas dans lesquels on a un seul calendrier. Soit c'est un choix et là on connait la couleur. Soit les droits d'accès restreignent jusqu'à n'avoir plus qu'un calendrier !
										$unBloc['color'] = $calendrier['couleur'];
										$unBloc['borderColor'] = getCouleurBordure('#'.$calendrier['couleur']);
									}else{
										$calendrierDeLEvenement = $calendrierManager->getCalendrier($evenement['id_calendrier']);
										$unBloc['color'] = $calendrierDeLEvenement['couleur'];
										$unBloc['borderColor'] = getCouleurBordure('#'.$calendrierDeLEvenement['couleur']);
									}

								$jourDebut = date('Y-m-d',strtotime($evenement['date_debut']));
								$jourFin = date('Y-m-d',strtotime($evenement['date_fin']));
								$jourDebutTimestamp = strtotime($jourDebut);
								$jourFinTimestamp = strtotime($jourFin);

								// détermine si l'événement commence avant la fenêtre visible.
								$aCheval = false;
								if ($jourDebutTimestamp < $jourPremierJourCalendrierTimestamp) {
									$aCheval = true;
									$evenement['date_debut'] = $jourPremierJourCalendrier." 00:00:01";
									$jourDebutTimestamp = strtotime($jourPremierJourCalendrier." 00:00:01");
								}

								// détermine si le bloc de l'événement est à cheval sur plusieurs jours ou pas
								if ($jourDebut==$jourFin) {
									// fourni les infos d'affichage pour un bloc qui se trouve sur le même jour
									$unBloc['nom'] = stripcslashes($evenement['nom']);
									$unBloc['dateDebut'] = dateTime2Humain($evenement['date_debut']);
									$unBloc['heureDebut'] = dateTime2HeureHumain($evenement['date_debut']);
									$unBloc['dateFin'] = dateTime2Humain($evenement['date_fin']);
									$unBloc['jourEntier'] = $evenement['jour_entier'];
									
									// heure de début et de fin utilisé pour composer la nouvelle date de mise à jour quand on change uniquement le jour.
									$unBloc['hDebut'] = date('H:i:s',strtotime($evenement['date_debut']));
									$unBloc['hFin'] = date('H:i:s',strtotime($evenement['date_fin']));

									// si l'événement commence avant la fenêtre visible.
									if ($aCheval) {
										$unBloc['class'] = " ";
									}else{
										$unBloc['class'] = "drag ";
									}
									$unBloc['width'] = "99";
									$unBloc['nbJour'] = "0";  // différence de jour entre le début et la fin
					
									// ajoute l'élément à la liste des éléments attribué à la case.
									$blocsEvenements[$semaine][$jour][] = $unBloc;
								}else{ // évéenements sur plusieurs jours.
									// implique le morcellement du bloc et le calcul des largeurs.
									// $unBloc['height'] = $evenementManager->getDureePixel($evenement['date_debut'],$evenement['date_fin']);
									
									// calcul du nombre de jours que recouvre l'événement
									
									// inutile ??
									// $noSemaine = date('W',strtotime($evenement['date_debut'])); // numéro de la semaine du début de l'événement

									$nbJour = ceil(($jourFinTimestamp - $jourDebutTimestamp)/86400)+1;  // la différence est donnée en nb de secondes donc on adapte en nombre de jours. On adapte au jour entier supérieur et on ajoute 1 pour obtenir le premier jour.
									
									// calcul le nombre de jour qu'il reste dans la semaine (la même ligne)
									$nbJourLigne = 7-$jour;
									
									// si l'événement tient sur la même ligne
									if ($nbJour < ($nbJourLigne+1)) {
										// fourni les infos d'affichage pour un bloc qui se trouve sur la même semaine
										$unBloc['nom'] = stripcslashes($evenement['nom']);
										$unBloc['dateDebut'] = dateTime2Humain($evenement['date_debut']);
										$unBloc['heureDebut'] = dateTime2HeureHumain($evenement['date_debut']);
										$unBloc['dateFin'] = dateTime2Humain($evenement['date_fin']);
										$unBloc['jourEntier'] = $evenement['jour_entier'];

										// heure de début et de fin utilisé pour composer la nouvelle date de mise à jour quand on change uniquement le jour.
										$unBloc['hDebut'] = date('H:i:s',strtotime($evenement['date_debut']));
										$unBloc['hFin'] = date('H:i:s',strtotime($evenement['date_fin']));

										// si l'événement commence avant la fenêtre visible.
										if ($aCheval) {
											$unBloc['class'] = " ";
										}else{
											$unBloc['class'] = "drag ";
										}
										$taille = $nbJour * 100.5;
										$unBloc['width'] = $taille;
										$unBloc['nbJour'] = $nbJour-1; // permet de calculer la date de fin en fonction de la date de début. Ce n'est pas le nombre de cases prise, c'est la différence entre le début et la fin. D'où le nbJour-1

										// ajoute l'élément à la liste des éléments attribué à la case.
										$blocsEvenements[$semaine][$jour][] = $unBloc;
									
									}else{ // si l'événement ne tient pas sur la même ligne (donc à cheval sur plusieurs semaine)
									
										// on crée un bloc sur la semaine de début
										$unBloc['nom'] = stripcslashes($evenement['nom']);
										$unBloc['dateDebut'] = dateTime2Humain($evenement['date_debut']);
										$unBloc['heureDebut'] = dateTime2HeureHumain($evenement['date_debut']);
										$unBloc['dateFin'] = dateTime2Humain($evenement['date_fin']);
										$unBloc['jourEntier'] = $evenement['jour_entier'];

										// heure de début et de fin utilisé pour composer la nouvelle date de mise à jour quand on change uniquement le jour.
										$unBloc['hDebut'] = date('H:i:s',strtotime($evenement['date_debut']));
										$unBloc['hFin'] = date('H:i:s',strtotime($evenement['date_fin']));

										// si l'événement commence avant la fenêtre visible.
										if ($aCheval) {
											$unBloc['class'] = " ";
										}else{
											$unBloc['class'] = "drag ";
										}
										$taille = $nbJourLigne * 100.5;
										$unBloc['width'] = $taille;
										$unBloc['nbJour'] = $nbJour-1; // permet de calculer la date de fin en fonction de la date de début. Ce n'est pas le nombre de cases prise, c'est la différence entre le début et la fin. D'où le nbJour-1

										// ajoute l'élément à la liste des éléments attribués à la case.
										$blocsEvenements[$semaine][$jour][] = $unBloc;
										
										// détermine si la fin de l'événement à lieu la semaine d'après ou pas...
										
										// trouve le lundi suivant la date de début de l'événement
										$dateLundiSuivantDebut= date('Y-m-d', strtotime($evenement['date_debut'].' next Monday'));
										
										// trouve la date du lundi de la même semaine que la date de fin de l'événement
										if (date('N',strtotime($evenement['date_fin']))!='1') { // si le jour n'est pas déjà un lundi
											$dateLundiAvantFin= date('Y-m-d', strtotime($evenement['date_fin'].' last Monday'));  // last Monday retourne le lundi de la semaine d'avant quand le jour fourni est déjà un lundi
										}else{
											$dateLundiAvantFin = date('Y-m-d', strtotime($evenement['date_fin']));
										}
										
										// nombre de jours qui sont sur des semaines entières entre le début et la fin d'un événements
										$nbJourSemaineEntiere = 0;
										
										// si le lundi suivant le début d'un événement n'est pas le lundi de la semaine de la fin de l'événement=> place un bloc sur toute la semaine
										while ($dateLundiSuivantDebut != $dateLundiAvantFin) {
											
											// détermine la case dans laquelle la date de ce lundi se trouve.
											// le lundi est de toute façon = 0
											// le numéro de la semaine [0-4]
											$numeroSemaine = abs(ceil((strtotime($dateLundiSuivantDebut) - strtotime($datePremierJourCalendrier))/(7*86400))); // une semaine en seconde
											
										//	$unBloc['id'] = $idEvenement."millieu";
											$taille = 7 * 100.5; // la largeur complète de la semaine
											$unBloc['width'] = $taille;
											$unBloc['class'] = "";

											// ajoute l'élément à la liste des éléments attribués à la case.
											$blocsEvenements[$numeroSemaine]['0'][] = $unBloc;
											
											// on incrémente le nb de jours.
											$nbJourSemaineEntiere += 7;
											
											// crée un bloc d'une durée de 7 jours...
											$dateLundiSuivantDebut = date('Y-m-d', strtotime($dateLundiSuivantDebut.' + 7 day')); // passe à la semaine suivante

											//attention... va remplir le tableau jusqu'à la fin de l'événement... il faut un garde fou pour limiter à la fin de la partie affichée
											if ($dateLundiSuivantDebut > date('Y-m-d',strtotime($datePremierJourCalendrier." +34 day"))) {
												break;
											}
										} // while
										
										// On place le bloc de fin de l'événement.
										
										
										$nbJourDerniereSemaine = $nbJour - $nbJourLigne -$nbJourSemaineEntiere;  // on calcule le nombre de jour restant sur la ligne de fin comme étant le nombre total - la le nombre de jour sur la première ligne. Ceci ne marche que si l'événement est sur max 2 lignes.
										
										//$unBloc['id'] = $idEvenement."fin";
										$taille = $nbJourDerniereSemaine * 100.5;
										$unBloc['width'] = $taille;
										$unBloc['class'] = "";

										// calcul du numéro de la semaine du bloc de fin..
										$numeroSemaineBlocFin = abs(floor((strtotime($evenement['date_fin']) - strtotime($datePremierJourCalendrier))/(7*86400))); // une semaine en seconde
																				
										// ajoute l'élément à la liste des éléments attribués à la case.
										$blocsEvenements[$numeroSemaineBlocFin]['0'][] = $unBloc;
																				
									} // if sur la même semaine
									
								} // if sur le même jour

							} // foreach
						
					}// for jour
				}// for semaine
			//	print_r($blocsEvenements);
				
			$smarty->assign('evenements',$blocsEvenements);
			
			
			/*********** Année ********************/
			}elseif ($typeDeVue=="annee") {
				
				// todo: gérer le bouton année suivante/précédente
				// ex pour le moi:
				// $moisCourant = date('Y-m', strtotime($dateCourante)); // année + mois sous forme de chiffre 01-12  avec les 0 initiaux. EX: 2008-04
				// 			$premierJourDuMois = $moisCourant."-01";  // date du premier jour du mois dans le genre: 2008-04-01
				
				//$anneeBisextile = date('L'); // 1 si bissextile, 0 sinon.
				$anneeCourante = date('Y', strtotime($dateCourante)); // EX: 2010
				
				$datePremierJourAnneeCourante = date('Y', strtotime($dateCourante)).'-01-01 00:00:00'; // premier jour de l'année à 0h00
				$dateDernierJourAnneeCourante = date('Y', strtotime($dateCourante)).'-12-31 24:00:00'; // dernier jour de l'année à 24h
				
				$premierJanvier = $datePremierJourAnneeCourante;
				$premierFevrier = date('Y-m-d H:i:s', strtotime($premierJanvier.' +1 month'));
				$premierMars = date('Y-m-d H:i:s', strtotime($premierFevrier.' +1 month'));
				$premierAvril = date('Y-m-d H:i:s', strtotime($premierMars.' +1 month'));
				$premierMai = date('Y-m-d H:i:s', strtotime($premierAvril.' +1 month'));
				$premierJuin = date('Y-m-d H:i:s', strtotime($premierMai.' +1 month'));
				$premierJuillet = date('Y-m-d H:i:s', strtotime($premierJuin.' +1 month'));
				$premierAout = date('Y-m-d H:i:s', strtotime($premierJuillet.' +1 month'));
				$premierSeptembre = date('Y-m-d H:i:s', strtotime($premierAout.' +1 month'));
				$premierOctobre = date('Y-m-d H:i:s', strtotime($premierSeptembre.' +1 month'));
				$premierNovembre = date('Y-m-d H:i:s', strtotime($premierOctobre.' +1 month'));
				$premierDecembre = date('Y-m-d H:i:s', strtotime($premierNovembre.' +1 month'));
				
				$nbJoursJanvier = date('t',strtotime($premierJanvier));
				$nbJoursFevrier = date('t',strtotime($premierFevrier));
				$nbJoursMars = date('t',strtotime($premierMars));
				$nbJoursAvril = date('t',strtotime($premierAvril));
				$nbJoursMai = date('t',strtotime($premierMai));
				$nbJoursJuin = date('t',strtotime($premierJuin));
				$nbJoursJuillet = date('t',strtotime($premierJuillet));
				$nbJoursAout = date('t',strtotime($premierAout));
				$nbJoursSeptembre = date('t',strtotime($premierSeptembre));
				$nbJoursOctobre = date('t',strtotime($premierOctobre));
				$nbJoursNovembre = date('t',strtotime($premierNovembre));
				$nbJoursDecembre = date('t',strtotime($premierDecembre));
				
				// fourni les dates des jours pour janvier
				$janvier = array();
				for ($i=0; $i < $nbJoursJanvier ; $i++) { 
					$dateDuJour = date('Y-m-d H:i:s', strtotime($premierJanvier.' +'.$i.'day'));
					$uneDate['dateHumaine'] = __(date('D', strtotime($dateDuJour)))." ".date('j ', strtotime($dateDuJour)). __(date('M', strtotime($dateDuJour)));
					$uneDate['dateJour'] = date('Y-m-d', strtotime($dateDuJour));
					$uneDate['noJour'] = $i+1;
					$uneDate['jourSemaine'] = date('N', strtotime($dateDuJour)); // 1 = lundi ... 7= dimanche
					$janvier[] = $uneDate;
				}
				
				// fourni les dates des jours pour fevrier
				$fevrier = array();
				for ($i=0; $i < $nbJoursFevrier ; $i++) { 
					$dateDuJour = date('Y-m-d H:i:s', strtotime($premierFevrier.' +'.$i.'day'));
					$uneDate['dateHumaine'] = __(date('D', strtotime($dateDuJour)))." ".date('j ', strtotime($dateDuJour)). __(date('M', strtotime($dateDuJour)));
					$uneDate['dateJour'] = date('Y-m-d', strtotime($dateDuJour));
					$uneDate['noJour'] = $i+1;
					$uneDate['jourSemaine'] = date('N', strtotime($dateDuJour)); // 1 = lundi ... 7= dimanche
					$fevrier[] = $uneDate;
				}
				
				// fourni les dates des jours pour mars
				$mars = array();
				for ($i=0; $i < $nbJoursMars ; $i++) { 
					$dateDuJour = date('Y-m-d H:i:s', strtotime($premierMars.' +'.$i.'day'));
					$uneDate['dateHumaine'] = __(date('D', strtotime($dateDuJour)))." ".date('j ', strtotime($dateDuJour)). __(date('M', strtotime($dateDuJour)));
					$uneDate['dateJour'] = date('Y-m-d', strtotime($dateDuJour));
					$uneDate['noJour'] = $i+1;
					$uneDate['jourSemaine'] = date('N', strtotime($dateDuJour)); // 1 = lundi ... 7= dimanche
					$mars[] = $uneDate;
				}
				
				// fourni les dates des jours pour avril
				$avril = array();
				for ($i=0; $i < $nbJoursAvril ; $i++) { 
					$dateDuJour = date('Y-m-d H:i:s', strtotime($premierAvril.' +'.$i.'day'));
					$uneDate['dateHumaine'] = __(date('D', strtotime($dateDuJour)))." ".date('j ', strtotime($dateDuJour)). __(date('M', strtotime($dateDuJour)));
					$uneDate['dateJour'] = date('Y-m-d', strtotime($dateDuJour));
					$uneDate['noJour'] = $i+1;
					$uneDate['jourSemaine'] = date('N', strtotime($dateDuJour)); // 1 = lundi ... 7= dimanche
					$avril[] = $uneDate;
				}
				
				// fourni les dates des jours pour mai
				$mai = array();
				for ($i=0; $i < $nbJoursMai ; $i++) { 
					$dateDuJour = date('Y-m-d H:i:s', strtotime($premierMai.' +'.$i.'day'));
					$uneDate['dateHumaine'] = __(date('D', strtotime($dateDuJour)))." ".date('j ', strtotime($dateDuJour)). __(date('M', strtotime($dateDuJour)));
					$uneDate['dateJour'] = date('Y-m-d', strtotime($dateDuJour));
					$uneDate['noJour'] = $i+1;
					$uneDate['jourSemaine'] = date('N', strtotime($dateDuJour)); // 1 = lundi ... 7= dimanche
					$mai[] = $uneDate;
				}
				
				// fourni les dates des jours pour juin
				$juin = array();
				for ($i=0; $i < $nbJoursJuin ; $i++) { 
					$dateDuJour = date('Y-m-d H:i:s', strtotime($premierJuin.' +'.$i.'day'));
					$uneDate['dateHumaine'] = __(date('D', strtotime($dateDuJour)))." ".date('j ', strtotime($dateDuJour)). __(date('M', strtotime($dateDuJour)));
					$uneDate['dateJour'] = date('Y-m-d', strtotime($dateDuJour));
					$uneDate['noJour'] = $i+1;
					$uneDate['jourSemaine'] = date('N', strtotime($dateDuJour)); // 1 = lundi ... 7= dimanche
					$juin[] = $uneDate;
				}
				
				// fourni les dates des jours pour juillet
				$juillet = array();
				for ($i=0; $i < $nbJoursJuillet ; $i++) { 
					$dateDuJour = date('Y-m-d H:i:s', strtotime($premierJuillet.' +'.$i.'day'));
					$uneDate['dateHumaine'] = __(date('D', strtotime($dateDuJour)))." ".date('j ', strtotime($dateDuJour)). __(date('M', strtotime($dateDuJour)));
					$uneDate['dateJour'] = date('Y-m-d', strtotime($dateDuJour));
					$uneDate['noJour'] = $i+1;
					$uneDate['jourSemaine'] = date('N', strtotime($dateDuJour)); // 1 = lundi ... 7= dimanche
					$juillet[] = $uneDate;
				}
				
				// fourni les dates des jours pour aout
				$aout = array();
				for ($i=0; $i < $nbJoursAout ; $i++) { 
					$dateDuJour = date('Y-m-d H:i:s', strtotime($premierAout.' +'.$i.'day'));
					$uneDate['dateHumaine'] = __(date('D', strtotime($dateDuJour)))." ".date('j ', strtotime($dateDuJour)). __(date('M', strtotime($dateDuJour)));
					$uneDate['dateJour'] = date('Y-m-d', strtotime($dateDuJour));
					$uneDate['noJour'] = $i+1;
					$uneDate['jourSemaine'] = date('N', strtotime($dateDuJour)); // 1 = lundi ... 7= dimanche
					$aout[] = $uneDate;
				}
				
				// fourni les dates des jours pour septembre
				$septembre = array();
				for ($i=0; $i < $nbJoursSeptembre ; $i++) { 
					$dateDuJour = date('Y-m-d H:i:s', strtotime($premierSeptembre.' +'.$i.'day'));
					$uneDate['dateHumaine'] = __(date('D', strtotime($dateDuJour)))." ".date('j ', strtotime($dateDuJour)). __(date('M', strtotime($dateDuJour)));
					$uneDate['dateJour'] = date('Y-m-d', strtotime($dateDuJour));
					$uneDate['noJour'] = $i+1;
					$uneDate['jourSemaine'] = date('N', strtotime($dateDuJour)); // 1 = lundi ... 7= dimanche
					$septembre[] = $uneDate;
				}
				
				// fourni les dates des jours pour octobre
				$octobre = array();
				for ($i=0; $i < $nbJoursOctobre ; $i++) { 
					$dateDuJour = date('Y-m-d H:i:s', strtotime($premierOctobre.' +'.$i.'day'));
					$uneDate['dateHumaine'] = __(date('D', strtotime($dateDuJour)))." ".date('j ', strtotime($dateDuJour)). __(date('M', strtotime($dateDuJour)));
					$uneDate['dateJour'] = date('Y-m-d', strtotime($dateDuJour));
					$uneDate['noJour'] = $i+1;
					$uneDate['jourSemaine'] = date('N', strtotime($dateDuJour)); // 1 = lundi ... 7= dimanche
					$octobre[] = $uneDate;
				}
				
				// fourni les dates des jours pour novembre
				$novembre = array();
				for ($i=0; $i < $nbJoursNovembre ; $i++) { 
					$dateDuJour = date('Y-m-d H:i:s', strtotime($premierNovembre.' +'.$i.'day'));
					$uneDate['dateHumaine'] = __(date('D', strtotime($dateDuJour)))." ".date('j ', strtotime($dateDuJour)). __(date('M', strtotime($dateDuJour)));
					$uneDate['dateJour'] = date('Y-m-d', strtotime($dateDuJour));
					$uneDate['noJour'] = $i+1;
					$uneDate['jourSemaine'] = date('N', strtotime($dateDuJour)); // 1 = lundi ... 7= dimanche
					$novembre[] = $uneDate;
				}
				
				// fourni les dates des jours pour decembre
				$decembre = array();
				for ($i=0; $i < $nbJoursDecembre ; $i++) { 
					$dateDuJour = date('Y-m-d H:i:s', strtotime($premierDecembre.' +'.$i.'day'));
					$uneDate['dateHumaine'] = __(date('D', strtotime($dateDuJour)))." ".date('j ', strtotime($dateDuJour)). __(date('M', strtotime($dateDuJour)));
					$uneDate['dateJour'] = date('Y-m-d', strtotime($dateDuJour));
					$uneDate['noJour'] = $i+1;
					$uneDate['jourSemaine'] = date('N', strtotime($dateDuJour)); // 1 = lundi ... 7= dimanche
					$decembre[] = $uneDate;
				}
				
				$datesAnnee = array();
				$datesAnnee[] = $janvier;
				$datesAnnee[] = $fevrier;
				$datesAnnee[] = $mars;
				$datesAnnee[] = $avril;
				$datesAnnee[] = $mai;
				$datesAnnee[] = $juin;
				$datesAnnee[] = $juillet;
				$datesAnnee[] = $aout;
				$datesAnnee[] = $septembre;
				$datesAnnee[] = $octobre;
				$datesAnnee[] = $novembre;
				$datesAnnee[] = $decembre;
				
				
				$smarty->assign('datesAnnee',$datesAnnee);

				//--filtre  le paramètre $queryFiltre permet de filtrer les événements à la demande
				
				$listeEvenement = array();

				$listeEvenementJourEntier = $evenementManager->getListeEvenementsJourEntier($datePremierJourAnneeCourante,$dateDernierJourAnneeCourante,"date_debut asc",$queryFiltre);
				
				$optionUniquementJourEntier = false; // todo à récupérer via un paramtre dans l'url
				
				// permet de choisir si l'on veut une vue détaillée ou non en affichant uniquement les événements de jour entier ou tous les événements
				if (!$optionUniquementJourEntier) {
					// liste des id des événements sur toute l'année
					// ces événements ne sont pas de jours entier
					$listeEvenementsCourts = $evenementManager->getListeEvenementsSaufJourEntier($datePremierJourAnneeCourante,$dateDernierJourAnneeCourante,"date_debut asc",$queryFiltre);
					
					$listeEvenement = array_merge($listeEvenementsCourts,$listeEvenementJourEntier); // on rassemble les tableaux
				}
	
				// va chercher la liste des événements à cheval sur la fenêtre d'affichage. (on simplifie, il prend tous les événements jour entier ou non) // todo => à compléter pour préciser les 2 types d'évéenemnts
				$listeEvenementACheval = $evenementManager->getListeEvenementsSurLeMoment($datePremierJourAnneeCourante,'all',$queryFiltre);

				// ajoute les événements à cheval
				$listeEvenement = array_merge($listeEvenement,$listeEvenementACheval);



				//////////////////  on filtre les événements selon le(s) calendriers demandés ////////////

				// si des calendriers précis sont fournis. On filtre, sinon on ne filtre rien, donc on prend tout.
				if (!empty($selectionCalendrier)) {

					$eventsInCalendar = array();
					foreach ($selectionCalendrier as $key => $idCal) {

						// va chercher les id des événements qui correspondent au calendrier voulu
						$events = $evenementManager->getEvenementsCalendrier($idCal);
						$eventsInCalendar = array_merge($eventsInCalendar,$events);
					}
					// ne garde que les id des événements qui sont dans le calendrier
					$listeEvenement = array_intersect($eventsInCalendar,$listeEvenement);
				}

				//////////////// on filtre les événements selon les tags demandés ////////////////

				if (!empty($tags)) {
					 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
					$taggedElements = $groupeManager->getElementByTags($tags,'evenement');

					$listeEvenement = array_intersect($taggedElements,$listeEvenement);
				}


				///////////////// on filtre les événements selon les restrictions courantes ///////////
				// .... todo.  Pour l'instant c'est du tout ou rien fixé ici
				$editable = true;  // cette variable sert aux templates à savoir si il faut afficher les liens qui appellent les fonctions javascript d'édition.
				$smarty->assign('editable',$editable);


				////////// -------   Création des blocs pour les événements ------- /////////////
				
				// print_r($datesAnnee);
				// print_r($listeEvenement);
				
				// à partir de la liste des id des événements, crée une liste de bloc à afficher avec leur coordonnées.
				$blocsEvenements = array();
				foreach ($listeEvenement as $key => $idEvenement) {
					$unBloc = array();
					$unBloc['id'] = $idEvenement;

					// obtient l'événement complet... extrait les dates début et fin, puis les converti de manière humainement compréhensible et traduite.
					$evenement = $evenementManager->getEvenement($idEvenement);
					//print_r($evenement);
					
					// calcul de la durée de l'événement
					// pour les événement de jour entier, il se peut que l'heure soit renseignée dans la base de donnée, mais il ne faut pas en tenir compte.
					// ce qui importe, c'est l'heure à 0h du jour de début et à 24h le jour de fin.
					if ($evenement['jour_entier']=='true') {
						$dateDebutTimstamp = strtotime(date('Y-m-d 00:00:00',strtotime($evenement['date_debut'])));
						$dateFinTimstamp = strtotime(date('Y-m-d 24:00:00',strtotime($evenement['date_fin'])));
					}else{
						$dateDebutTimstamp = strtotime($evenement['date_debut']);
						$dateFinTimstamp = strtotime($evenement['date_fin']);
					}

					// durée en seconde
					$duree = $dateFinTimstamp - $dateDebutTimstamp;
					// un jour = 86400 s
					// echo "<br />",$evenement['nom']," :",$dateFinTimstamp," - ",$dateDebutTimstamp," = ",$duree;  // ici
					
					
					$optionAfficherUniquementEvenementsLongs = false; // pour n'afficher que les évéenements qui sont plus long qu'une certaine durée.
					if ($optionAfficherUniquementEvenementsLongs) {
						$dureeMinimumAffichage = 86000; // valeur à peine plue petite qu'un jour. Pour afficher les jours. todo: à mettre dans une config.
					}else{
						$dureeMinimumAffichage = 0;
					}
					
					if ($duree>$dureeMinimumAffichage) {
					
						// va chercher la couleur de l'événement
						// si on affiche qu'un seul calendrier, tous les évéenements auront la même couleur, celle du calendrier, donc on l'attribue directement. Sinon on fait une requête pour chaque événement pour trouver ça couleur, ce qui prend plus de temps.
						if (count($selectionCalendrier)==1 && isset($calendrier)) {  // il peut y avoir 2 cas dans lesquels on a un seul calendrier. Soit c'est un choix et là on connait la couleur. Soit les droits d'accès restreignent jusqu'à n'avoir plus qu'un calendrier !
							$unBloc['color'] = $calendrier['couleur'];
							$unBloc['borderColor'] = getCouleurBordure('#'.$calendrier['couleur']);
						}else{
							$calendrierDeLEvenement = $calendrierManager->getCalendrier($evenement['id_calendrier']);
							$unBloc['color'] = $calendrierDeLEvenement['couleur'];
							$unBloc['borderColor'] = getCouleurBordure('#'.$calendrierDeLEvenement['couleur']);
						}

						// ajoute l'information du jour entier dans le bloc
						$unBloc['jour_entier'] = $evenement['jour_entier'];
						if ($evenement['jour_entier']=='true') {
							$unBloc['dateJourEntier'] = "toute la journée du ".date('d.m.Y',strtotime($evenement['date_debut']))." au ".date('d.m.Y',strtotime($evenement['date_fin']));
						}
					
						// détermine si le bloc de l'événement est à cheval sur plusieurs mois ou pas
						$moisDebut = date('Y-m',strtotime($evenement['date_debut']));
						$moisFin = date('Y-m',strtotime($evenement['date_fin']));
						$moisDebutTimestamp = strtotime($moisDebut);
						$moisFinTimestamp = strtotime($moisFin);

						$datePremierJourAnneeCouranteTimestamp = strtotime($datePremierJourAnneeCourante); // nouvel an en timestamp

						// détermine si l'événement commence avant la fenêtre visible. Donc avant le début de l'année.
						// Dans ce cas fait comme si l'événement commencait le 1er janvier à 1s
						$aCheval = false;
					
						if ($moisDebutTimestamp < $datePremierJourAnneeCouranteTimestamp) {
							$aCheval = true;
							$evenement['date_debut'] = date('Y').'-01-01 00:00:01';
							$moisDebutTimestamp = strtotime($evenement['date_debut']);
						}
						// détermine si le bloc de l'événement est à cheval sur plusieurs mois ou pas
						if ($moisDebutTimestamp==$moisFinTimestamp) {
							// fourni les infos d'affichage pour un bloc qui se trouve sur le même mois
							$unBloc['nom'] = stripcslashes($evenement['nom']);
							$unBloc['dateDebut'] = dateTime2Humain($evenement['date_debut']);
							$unBloc['heureDebut'] = dateTime2HeureHumain($evenement['date_debut']);
							$unBloc['dateFin'] = dateTime2Humain($evenement['date_fin']);

							$unBloc['top'] = $evenementManager->getTopPixelAnnee($evenement['date_debut']);
							$unBloc['left'] = $evenementManager->getLeftPixelAnnee($evenement['date_debut']);
							$unBloc['height'] = $evenementManager->getDureePixelAnnee($evenement['date_debut'],$evenement['date_fin']);
							$unBloc['nomHeight'] = $unBloc['height'] - 13;
							$unBloc['class'] = "drag ";
							$unBloc['resizeHandle'] = "resizeHandle";

							// ajoute l'élément à la liste
							$blocsEvenements[] = $unBloc;
						}else{
							// crée plusieurs blocs d'affichage pour un événement à cheval sur plusieurs mois
							// crée un bloc entre le début de l'événement et la fin du mois courant.  Cet événement est le seul à avoir la date et le nom indiqué.

							// attention bug ! ... crée des blocs inutiles pour des morceaux d'événements hors de la zone visible.
							$unBloc['nom'] = stripcslashes($evenement['nom']);
							$unBloc['dateDebut'] = dateTime2Humain($evenement['date_debut']);
							$unBloc['heureDebut'] = dateTime2HeureHumain($evenement['date_debut']);
							$unBloc['dateFin'] = dateTime2Humain($evenement['date_fin']);

							$unBloc['top'] = $evenementManager->getTopPixelAnnee($evenement['date_debut']);
							$unBloc['left'] = $evenementManager->getLeftPixelAnnee($evenement['date_debut']);
							$unBloc['height'] = $evenementManager->getDureePixelAnnee($evenement['date_debut'],date('Y-m-t 23:59:59',strtotime($evenement['date_debut'])));  // début de l'événement et jusqu'au dernier jour du mois courant à 23h59
			 				$unBloc['nomHeight'] = $unBloc['height'] - 13;
							$unBloc['class'] = "";
							$unBloc['resizeHandle'] = "";

							// ajoute l'élément à la liste
							$blocsEvenements[] = $unBloc;

							// seulement si le bloc n'est pas en dehors de la zone visible. (le 1er janvier de l'année suivant à 0h0)
							if (strtotime($evenement['date_fin']) < strtotime(date('Y-m-d 00:00:00',strtotime($datePremierJourAnneeCourante."+ 1 year")))) {
								// crée un bloc entre minuit le jour de fin et la fin de l'événement
								$unBloc['nom'] = stripcslashes($evenement['nom']);
								$unBloc['dateDebut'] = dateTime2Humain($evenement['date_debut']);
								$unBloc['heureDebut'] = ''; //affiché seulement pour le premier bloc de l'événement
								$unBloc['dateFin'] = dateTime2Humain($evenement['date_fin']);

								$unBloc['top'] = '0'; // au début du mois de toute façon
								$unBloc['left'] = $evenementManager->getLeftPixelAnnee($evenement['date_fin']);
								$unBloc['height'] = $evenementManager->getDureePixelAnnee(date('Y-m-01 00:00:00',strtotime($evenement['date_fin'])),$evenement['date_fin']);  // depuis le premier jour du mois de la fin de l'événement jusqu'à la fin de l'événement
				 				$unBloc['nomHeight'] = $unBloc['height'] - 13;
								$unBloc['class'] = "";
								$unBloc['resizeHandle'] = "";

								// ajoute l'élément à la liste
								$blocsEvenements[] = $unBloc;
							}

							//au besoin crée des mois complets entre le mois de début et le mois de fin.
							$dateBlocMoisSuivant = date('Y-m',strtotime($evenement['date_debut']." +1 month")); // on attribue un mois de plus que le début pour le bloc du mois suivant
						
							// si la date du bloc du mois suivant n'est pas encore le mois du bloc de fin et que l'on est pas encore hors de la zone visible du calendrier
							while ($dateBlocMoisSuivant!=date('Y-m',strtotime($moisFin)) && (strtotime($dateBlocMoisSuivant) < strtotime($datePremierJourAnneeCourante."+ 1 year"))) {
								// crée un bloc pour ce jour entier
								$unBloc['nom'] = stripcslashes($evenement['nom']);
								$unBloc['dateDebut'] = dateTime2Humain($evenement['date_debut']);
								$unBloc['heureDebut'] = ''; //affiché seulement pour le premier bloc de l'événement
								$unBloc['dateFin'] = dateTime2Humain($evenement['date_fin']);

								$unBloc['top'] = '0'; // à minuit de toute façon
								$unBloc['left'] = $evenementManager->getLeftPixelAnnee($dateBlocMoisSuivant." 00:00:00");
								$unBloc['height'] = '651';  // maximum
				 				$unBloc['nomHeight'] = $unBloc['height'] - 13;
								$unBloc['class'] = "";
								$unBloc['resizeHandle'] = "";

								// ajoute l'élément à la liste
								$blocsEvenements[] = $unBloc;

								$dateBlocMoisSuivant = date('Y-m',strtotime($dateBlocMoisSuivant." +1 month")); // on met un jour de plus pour le bloc du lendemain
							}//while

						}// if
					} // durée minimum

				} // foreach
				$smarty->assign('largeurBlocEvenement',$evenementManager->getLargeurPixelAnnee());
				$smarty->assign('evenements',$blocsEvenements);
				//print_r($blocsEvenements);
	
			/*********** liste ********************/
			}elseif ($typeDeVue=="liste") {
				
				//////////////////  on vas chercher les événements qui correspondent à la tranche horaire voulue ////////////
				
				$desc = " asc";  // orde chronologique
				
				// va chercher la liste des événements entre aujourd'hui... et plus tard.. (par défaut 1er janvier 2050.. faudra faire gaffe ce jour là...),
				$listeEvenement = $evenementManager->getListeEvenements(date('Y-m-d 00:00:00',strtotime($dateCourante)),"2050-01-01 00:00:00","date_debut ".$desc,$queryFiltre);
				//--filtre  le paramètre $queryFiltre permet de filtrer les événements à la demande
				
				// // utile si l'on veut mettre en évidence les dernières modifications
				// $dateDernierePublication = date('Y-m-d H:i:s',strtotime(date('Y-m-d H:i:s')." -7 day")); // tout ce qui a été modifié dans les 7 derniers jours
				// if (isset($parametreUrl['dateDernierePublication'])) {
				// 	$dateDernierePublication = $parametreUrl['dateDernierePublication'];
				// }
				
				//////////////////  on filtre les événements selon le(s) calendriers demandés ////////////
				//
				// si des calendriers précis sont fournis. On filtre, sinon on ne filtre rien, donc on prend tout.
				if (!empty($selectionCalendrier)) {
					
					$eventsInCalendar = array();
					foreach ($selectionCalendrier as $key => $idCal) {
						
						// va chercher les id des événements qui correspondent au calendrier voulu
						$events = $evenementManager->getEvenementsCalendrier($idCal);
						$eventsInCalendar = array_merge($eventsInCalendar,$events);
					}
					// ne garde que les id des événements qui sont dans le calendrier
					$listeEvenement = array_intersect($eventsInCalendar,$listeEvenement);
				}

				//////////////// on filtre les événements selon les tags demandés ////////////////

				if (!empty($tags)) {
					 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
					$taggedElements = $groupeManager->getElementByTags($tags,'evenement');

					$listeEvenement = array_intersect($taggedElements,$listeEvenement);
				}
		

				///////////////// on filtre les événements selon les restrictions courantes ///////////
				// .... todo
				$editable = true;  // cette variable sert aux templates à savoir si il faut afficher les liens qui appellent les fonctions javascript d'édition.
				$smarty->assign('editable',$editable);
				
				////////////// On remplit les données dans le tableau que l'on fournira à smarty /////////
				
				$evenements = array(); // tableau contenant des tableaux représentant la ressource
				// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getEvenements()... et array_intersect
				foreach ($listeEvenement as $key => $idEvenement) {
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
					
					if (!empty($evenements[$idEvenement]['lieu'])) {
						// on trouve les correspondances entre les id et les vraies valeur des auteurs, info/contact et lieux
						$lieuEvenement = $lieuManager->getLieu($evenements[$idEvenement]['lieu']);
						$evenements[$idEvenement]['lieuNom'] = $lieuEvenement['nom'];
						$evenements[$idEvenement]['lieuCommune'] = $lieuEvenement['commune'];
					}
					
					
					// // va chercher les id des événements précédents et suivants
					// 					$idVoisins = $evenementManager->getEvenementsVoisins($idEvenement, $listeEvenement);
					// 					$evenements[$idEvenement]['idEvenementPrecedent'] = $idVoisins[0];
					// 					$evenements[$idEvenement]['idEvenementSuivant'] = $idVoisins[1];
					// 					//print_r($idVoisins);
				}

				// transmet une liste des id des événements dans le bon ordre pour pouvoir utiliser une navigation par flèche suivant précédent
				$listeId = implode(",", $listeEvenement);
				$smarty->assign('listeId',$listeId);

				// supprime les \
				stripslashes_deep($evenements);

				// transmets les ressources à smarty
				$smarty->assign('evenements',$evenements);
			//	print_r($evenements); //ici
			}
		
			
		}else{ // si le format de sortie est autre que html
			
			///////////////// **** on rempit un tableau avec les infos sur les événements **** ////////////////
			$desc = " asc";  // orde chronologique
			
			// va chercher la liste des événements entre aujourd'hui... et plus tard.. (par défaut 1er janvier 2050.. faudra faire gaffe ce jour là...),
			$listeEvenement = $evenementManager->getListeEvenements(date('Y-m-d 00:00:00',strtotime($dateCourante)),"2050-01-01 00:00:00","date_debut ".$desc,$queryFiltre);
			//--filtre  le paramètre $queryFiltre permet de filtrer les événements à la demande
			
			//////////////////  on filtre les événements selon le(s) calendriers demandés ////////////
			//
			// si des calendriers précis sont fournis. On filtre, sinon on ne filtre rien, donc on prend tout.
			if (!empty($selectionCalendrier)) {
				
				$eventsInCalendar = array();
				foreach ($selectionCalendrier as $key => $idCal) {
					
					// va chercher les id des événements qui correspondent au calendrier voulu
					$events = $evenementManager->getEvenementsCalendrier($idCal);
					$eventsInCalendar = array_merge($eventsInCalendar,$events);
				}
				// ne garde que les id des événements qui sont dans le calendrier
				$listeEvenement = array_intersect($eventsInCalendar,$listeEvenement);
			}

			//////////////// on filtre les événements selon les tags demandés ////////////////

			if (!empty($tags)) {
				// va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
				$taggedElements = $groupeManager->getElementByTags($tags,'evenement');
				
				$listeEvenement = array_intersect($taggedElements,$listeEvenement);
			}


			///////////////// on filtre les événements selon les restrictions courantes ///////////
			// .... todo
			$editable = true;  // cette variable sert aux templates à savoir si il faut afficher les liens qui appellent les fonctions javascript d'édition.
			$smarty->assign('editable',$editable);
			
			
			$evenements = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getEvenements()... et array_intersect
			foreach ($listeEvenement as $key => $idEvenement) {
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
				
				// nom du jour de la semaine
				$evenements[$idEvenement]['dateDebutJourDeLaSemaine'] = jourSemaine($evenements[$idEvenement]['date_debut']);
				
				
				// spécifique au format vcalendar
				$evenements[$idEvenement]['dateDebutVcal'] = date('Ymd', strtotime($evenements[$idEvenement]['date_debut']));
				$evenements[$idEvenement]['dateFinVcal'] = date('Ymd', strtotime($evenements[$idEvenement]['date_fin']."+1 day")); // pour que ical voit un événement sur plusieurs jour il faut indiquer le jour après le jour de fin.
                
				$evenements[$idEvenement]['dateTimeDebutVcal'] = date('Ymd\THis', strtotime($evenements[$idEvenement]['date_debut']));
				$evenements[$idEvenement]['dateTimeFinVcal'] = date('Ymd\THis', strtotime($evenements[$idEvenement]['date_fin']));
                
				$evenements[$idEvenement]['dateTimeCreationVcal'] = date('Ymd\THis', strtotime($evenements[$idEvenement]['date_creation']));
				$evenements[$idEvenement]['dateTimeModificationVcal'] = date('Ymd\THis', strtotime($evenements[$idEvenement]['date_modification']));
				
				// le format vcalendar a besoin d'échapper les ; ainsi que les retours chariot. Le plus simple est d'encoder le tout en quoted printable.
				$descriptionVcalendar = str_replace("\r", "=0D=0A=", $evenements[$idEvenement]['description']);
				$descriptionVcalendar = str_replace("\n", "=0D=0A=", $descriptionVcalendar);
				$descriptionVcalendar = str_replace(";", "\;", $descriptionVcalendar);
				$evenements[$idEvenement]['descriptionVcalendar'] = $descriptionVcalendar;
				
				$evenements[$idEvenement]['tags'] = implode(',',array_keys($groupeManager->getMotCleElement($idEvenement, 'evenement'))); // tags associés à l'événement séparé par des ,
				
				// date utilisée pour l'exportation VP
				// Mercredi 20 mai, 15h
				$evenements[$idEvenement]['dateDebutJourSemaineDateMoisHeureHumaine'] = dateTime2JourSemaineDateMoisHeureHumaine($evenements[$idEvenement]['date_debut']);
				
				// reprise des données de la personne de contact en fonction de son id
				if (isset($evenements[$idEvenement]['info'])) {
					$evenements[$idEvenement]['infoContact'] = $personneManager->getContact($evenements[$idEvenement]['info']);
				}
				// reprise des données d'un lieu en fonction de son id
				$evenements[$idEvenement]['lieuEvenement'] = $lieuManager->getLieu($evenements[$idEvenement]['lieu']);
				
				// encode les caractères qui posent problème en XML (quotes, amps, etc..)
				$evenements[$idEvenement]['description'] = htmlspecialchars($evenements[$idEvenement]['description'],ENT_COMPAT,'UTF-8');

				// reprise des données du calendrier en fonction de son id
				$evenements[$idEvenement]['calendrierEvenement'] = $calendrierManager->getCalendrier($evenements[$idEvenement]['id_calendrier']);
			}
			
			// supprime les \
			stripslashes_deep($evenements);
			
			// trie les événements par date de début
			function sortEvenements($a, $b) {
				$date_a = strtotime($a['date_debut']);
				$date_b = strtotime($b['date_debut']);
				return $date_a > $date_b ? 1 : -1;
			}

			// trie les evenements par ordre chronologique
			uasort($evenements, 'sortEvenements');

			// transmets les ressources à smarty
			$smarty->assign('evenements',$evenements);
			
		//	print_r($evenements); //ici
			
			// utile ???
			// $smarty->assign('dateCourante', dateTime2DateHumain($dateCourante));
			// $smarty->assign('maintenant', dateTime2Humain(date('Y-m-d H:i:s')));
		
		}  // outputFormat == html
		

		
		///////////////////// Choisi le template de sortie suivant le format choisi //////////////////////////////////////

		// quelques scripts utiles
		$additionalHeader = "
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/datePicker.css\" media=\"screen\" />
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/sexy-combo.css\" media=\"screen\" />
			<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/js/skins/sexy/sexy.css\" media=\"screen\" />
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/date.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/date_fr.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.datePicker.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.magicpreview.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.sexy-combo-2.0.6.min.js\"></script>
		";	
		
		
		// scripts utilisés pour trier la liste
		if ($typeDeVue=='liste') {
			// tableSorter
			 $additionalHeader .= "
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.uitablefilter.js\"></script>
					<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.tablesorter.pack.js\"></script>";
			
			// // filterSorter
			// $additionalHeader .= "
			// 	<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/js/tableFilter/includes/tableFilter.css\" media=\"all\" />
			// 	<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/js/tableFilter/includes/tableFilter.aggregator.css\" media=\"all\" />
			// 	<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/tableFilter/_dist/jquery.cookies-packed.js\"></script>
			// 	<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/tableFilter/_dist/prototypes-packed.js\"></script>
			// 	<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/tableFilter/_dist/json-packed.js\"></script>
			// 	<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/tableFilter/_dist/jquery.truemouseout-packed.js\"></script>
			// 	<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/tableFilter/_dist/daemachTools-packed.js\"></script>
			// 	<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/tableFilter/_dist/jquery.tableFilter-packed.js\"></script>
			// 	<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/tableFilter/_dist/jquery.tableFilter.aggregator-packed.js\"></script>
			// 	<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/tableFilter/_dist/jquery.tableFilter.columnStyle-packed.js\"></script>
			// 	";	
		}
		
		// si le calendrier est éditable, ajoute les scripts nécessaires à son édition
		if ($editable) {
			$additionalHeader .= "<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/calendrier_edit_".$typeDeVue.".js\"></script>";
		}else{
			$additionalHeader .= "<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/calendrier_lecture.js\"></script>";	
		}
		
		
		$smarty->assign('additionalHeader',$additionalHeader);

		// certains formats ne sont jamais inclu dans un thème
		if ($outputFormat=='ics') {
			header("Content-Type: text/calendar; charset=UTF-8");
 			
			// le fichier peut concerner un calendrier en particuliers ou un groupe de calendrier.
			if (!empty($idCalendrier)) {
				header("Content-Disposition: inline; filename=".$calendrier['nomSimplifie'].".ics");
			}else{
				header("Content-Disposition: inline; filename=calendriers.ics");
			}
			$smarty->display("calendrier_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}elseif ($outputFormat=='xml') {
						
			header('Content-Type: application/xml; charset=UTF-8');
			
			// le fichier peut concerner un calendrier en particuliers ou un groupe de calendrier.
			if (!empty($idCalendrier)) {
				header("Content-Disposition: attachment; filename=".$calendrier['nomSimplifie'].".xml");
			}else{
				header("Content-Disposition: attachment; filename=calendriers.xml");
			}
			
			$smarty->display("calendrier_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}elseif ($outputFormat=='atom') {

			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("atom","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);

			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("calendrier_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("calendrier_".$typeDeVue."_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"calendrier_".$typeDeVue."_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}
		} // if format = ics
	
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
	if(isset($_POST['couleur'])){
		$couleur = $_POST['couleur'];
	}else{
		$couleur ='';
	}
	if(isset($_POST['distant'])){
		$distant = $_POST['distant'];
	}else{
		$distant ='0';
	}
	if(isset($_POST['url'])){
		$url = $_POST['url'];
	}else{
		$url ='';
	}
	if(isset($_POST['tags'])){
		$tagsDefaut = $_POST['tags'];
	}else{
		$tagsDefaut ='';
	}
	if(isset($_POST['date_last_importation'])){
		$date_last_importation = $_POST['date_last_importation'];
	}else{
		$date_last_importation ='';
	}
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='0';
	}
	
	// ajoute la nouvelle ressource
	$idCalendrier = $calendrierManager->insertCalendrier($nom,$description,$couleur,$distant,$url,$tagsDefaut,$evaluation);
	
	echo $idCalendrier; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax

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
	if(isset($_POST['couleur'])){
		$couleur = $_POST['couleur'];
	}else{
		$couleur ='';
	}
	if(isset($_POST['distant'])){
		$distant = $_POST['distant'];
	}else{
		$distant ='0';
	}
	if(isset($_POST['url'])){
		$url = $_POST['url'];
	}else{
		$url ='';
	}
	if(isset($_POST['tags'])){
		$tagsDefaut = $_POST['tags'];
	}else{
		$tagsDefaut ='';
	}
	if(isset($_POST['date_last_importation'])){
		$date_last_importation = $_POST['date_last_importation'];
	}else{
		$date_last_importation ='';
	}
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='0';
	}
	
	// fait la mise à jour
	$calendrierManager->updateCalendrier($idCalendrier,$nom,$description,$couleur,$distant,$url,$tagsDefaut,$date_last_importation,$evaluation);

////////////////
////  DELETE
///////////////

}elseif ($action=='delete') {
	$calendrierManager->deleteCalendrier($idCalendrier);
	
////////////////
////  NEW
///////////////
}elseif ($action=='new') {
	
	// quelques scripts utiles
	$additionalHeader = "
		<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/colorpicker.css\" media=\"screen\" />
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/colorpic.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/calendrier.js\"></script>";	
	$smarty->assign('additionalHeader',$additionalHeader);
	
	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("calendrier_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"calendrier_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
		$smarty->display($theme."index.tpl");
	}

////////////////
////  MODIFY
///////////////
}elseif ($action=='modify') {
	// va chercher les infos sur la ressource demandée
	$calendrier = $calendrierManager->getCalendrier($idCalendrier);
	
	// supprime les \
	stripslashes_deep($calendrier);
	
	// passe les données de la calendrier à l'affichage
	$smarty->assign('calendrier',$calendrier);
	
	// quelques scripts utiles
	$additionalHeader = "
		<link type=\"text/css\" rel=\"stylesheet\" href=\"http://".$serveur."/utile/css/colorpicker.css\" media=\"screen\" />
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/colorpic.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/calendrier.js\"></script>";	
	$smarty->assign('additionalHeader',$additionalHeader);
	
	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("calendrier_modify_".LANG.".tpl"); // affichage de l'interface de modification de la ressource
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"calendrier_modify_".LANG.".tpl");
		$smarty->display($theme."index.tpl");
	}	
}
?>