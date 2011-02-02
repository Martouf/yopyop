<?php
/*******************************************************************************************
 * Nom du fichier		: blog.php
 * Date					: 21 décembre 2008
 * Modif				: 9 février 2009 => affichage selon la date de publication
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Afficher des documents sous la forme d'un blog
 *******************************************************************************************
 * Interface qui permet d'afficher un blog
 * 
 * Le principe est le même que pour les document, mais comme un blog n'est PAS la même chose qu'un document dans l'utilisation que l'on veut en faire on distingue blog et document. (un blog c'est perso, donc pas éditable par un inconnu)
 * Concrètement l'interface blog est juste un affichage différent d'un document. Le blog va privilégier l'interface anti-chronologique et faire une pagination.
 * Le blog n'est destiné à être utilisé qu'en lecture. Ainsi pour éditer un billet de blog, on édite le document qui est le même.
 * Quand on veut ajouter un billet dans le blog, il suffit de créer un document et de lui associer le tag qui correspond au blog que l'on veut.
 *
 * ex d'url du blog d'un utilisateur: http://yopyopy.ch/blog/martouf/  ou en url complète:  http://yopyopy.ch/blog/martouf/index.html et avec le flux associé: http://yopyopy.ch/blog/martouf/index.xml
 *
 * Rapidement avec un nombre grandissant de billets, la page ne devient plus lisible, il faut utiliser une pagination:
 * http://yopyopy.ch/blog/martouf/index.html?b=12    => b comme billet.. on compte le nombre de billet pas le nombre de page!
 */

/*
 *  Attention, il faut encore terminer mettre en place la gestion des permissions !!!
 *
 */

// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idDocument = $ressourceId;

// Obtient les restrictions courantes pour le visiteur courant sur l'élément courant si une ressources précise est demandée... sinon détermine plus loin au moment d'afficher une liste de ressources
$restrictionsCourantes = array();
if (!empty($idDocument)) {
	
	// obtient le type de gestion des droits d'accès. (restriction ou exclusivité) (0 ou 1)
	$typeAcces = $documentManager->getAccessType($idDocument);
	
	// si l'accès est géré avec des exclusivités détermine les droits d'accès du visiteur
	if ($typeAcces=='1') {
		// obtient les groupes pour lesquels l'accès est autorisés pour ce document
		$listeGroupeAutorise = $documentManager->getGroupeAutorise($idDocument);
		
		// obtient la liste des groupes dans lesquels se trouve l'utilisateur
		$listeGroupeUtilisateur = $groupeManager->getGroupeUtilisateur($_SESSION['id_personne']);
		
		// si l'intersection entre les 2 listes est nul c'est que l'utilisateur n'as pas d'autorisation. On lui place des restrictions sur tout !
		// si l'intersection donne un résultat. C'est que l'utilisateur a une exclusivité et donc accès à tout. On ne place pas de restriction !
		$acces = array_intersect($listeGroupeAutorise,$listeGroupeUtilisateur);
		if (count($acces)==0) {
			$restrictionsCourantes = array_fill_keys(array('1','2','3','4','5','6'), ''); // crée un tableau ave les clés 1,2,3,4,5,6 et une valeur nulle
		}
	}else{
		//$listeRestrictionsCourantes = $restrictionManager->getRestrictionsList($idDocument,'document', $_SESSION['id_personne']);
		$listeRestrictionsCourantes = array(); // todo: supprimer le management des droits par restriction
		// on crée un tableau avec les restrictions placées comme clés. Le tri sur des clés avec isset est 50x plus rapide que d'utiliser la focntion in_array
		$restrictionsCourantes = array_fill_keys($listeRestrictionsCourantes, ''); //Ne pas remplir de null car isset() retournerai FALSE même si la clé existe
	}
}

// détermine l'action demandée
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
$motsClesElement = $groupeManager->getMotCleElement($idDocument,'document'); // todo: il y a certainement moyen d'optimiser tout ça... on a pas besoin d'avoir le nombre d'oocurence de l'utilisation du mot clé !! Peut être mettre une autre fonction !!

// transforme un tableau avec le mot clé et ses occurences en liste séparée par des virgules
$tagsVirgules = implode(',',array_keys($motsClesElement));
$smarty->assign('tags',$tagsVirgules);
$smarty->assign('tagsTableau',$motsClesElement);

// on défini ensuite les différentes actions possible.
// Pour l'action get, il y a plusieurs formats de sortie possibles. Le template est choisi en conséqunce. Pour le format pdf, le choix est fait en amont par index.php qui va fournir à princeXML le fichier html correspondant.

////////////////
////  GET
///////////////

if ($action=='get') {
	// il y a 2 cas possibles qui peuvent être demandé. Une ressource unique bien précise, ou un groupe de ressource.
	
	// une ressource unique
	if (!empty($idDocument)) {
		
		// si l'utilisateur a le droit de lire cette ressource.
		if (!isset($restrictionsCourantes['1'])) {  // équivalent à !in_array('1',$restrictionsCourantes) mais plus rapide !

			// va chercher les infos sur la ressource demandée
			$document = $documentManager->getDocument($idDocument);
			$document['nomSimplifie'] = simplifieNom($document['nom']);
			$document['moisCreation'] = __(date('M',strtotime($document['date_publication'])));
			$document['jourCreation'] = __(date('j',strtotime($document['date_publication'])));
			$document['dateModifHumaine'] = dateTime2Humain($document['date_modification']);
			$document['tags'] = $groupeManager->getMotCleElement($idDocument,'document');
		
			// supprime les \
			stripslashes_deep($document);
			
			// affichage de la ressource
			$smarty->assign('document',$document);
			
			// Va chercher le code pour afficher des documents similaires			
			$documentsSimilaires = $documentManager->getSimilarsDocsFromTable($document['similaire']);
			$smarty->assign('documentsSimilaires',$documentsSimilaires);
			
			$urlFluxCommentairesDuBillet = "http://".$serveur."/commentaire/flux.php?id_element=".$idDocument;

			// quelques scripts utiles
			$additionalHeader = "
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/global.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/document.js\"></script>
				<link rel=\"alternate\" type=\"application/atom+xml\" title=\"Flux Atom des commentaires de ce billets\" href=\"".$urlFluxCommentairesDuBillet."\" />";	
			$smarty->assign('additionalHeader',$additionalHeader);

			// va chercher les commentaires qui sont associés à la ressource
			$tousCommentaires = $commentaireManager->getCommentaireElement($idDocument,'document');
			$commentaires = array();
			foreach ($tousCommentaires as $key => $aCommentaire) {
				$commentaires[$key] = $aCommentaire;
				$commentaires[$key]['description'] = nl2br($aCommentaire['description']);  // mise en forme basique des commentaire avec des retours chariots
				$commentaires[$key]['dateCreation'] = dateTime2Humain($aCommentaire['date_creation']);
				$commentaires[$key]['auteur'] = $personneManager->getPseudo($aCommentaire['id_auteur']); // pseudo de l'auteur plutôt que id
				$commentaires[$key]['gravatar'] = md5($aCommentaire['mail']);
			}
			
			// supprime les \  et transmet l'affichage à smarty
			stripslashes_deep($commentaires);
			$smarty->assign('commentaires',$commentaires);
			
			// info sur l'utilisateur qui va poster un commentaire
			$smarty->assign('idAuteurCommentaire',$_SESSION['id_personne']);
			$smarty->assign('pseudoUtilisateur',$_SESSION['pseudo']);

			// certains formats ne sont jamais inclus dans un thème
			if ($outputFormat=='xml') {
			
				// ajoute une entrée dans l'historique
				$historiqueManager->insertHistorique('lecture flux blog unique');
				$historiqueManager->purge(); // efface l'historique plus vieux qu'une semaine ou que la date fournie (datetime mysql)
	
				// calcule le nom de la même ressource, mais en page html
				$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
				$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
				header('Content-Type: application/atom+xml; charset=UTF-8');
				$smarty->display("blog_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
			
				// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
				if ($theme=="no") {
					$smarty->display("blog_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
				}else{
					// affiche la ressource inclue dans le template du thème index.tpl
					$smarty->assign('contenu',"blog_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
					$smarty->display($theme."index.tpl");
				}
			} // if format = xml
		
		}// restrictions de lecture
	
	// un groupe de ressources
	}else{
		// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
		//http://yopyop.ch/document/    => va afficher la liste de toutes les documents.
		if (empty($tags)) {
			// ne voit pas à quoi ça sert, il y a déjà document qui fait ça !!
			// on ferait mieux d'afficher un raccourci sur un blog en particulier. (le blog principal du site ?)
			//$documents = $documentManager->getDocumentsByPublicationDate();  // il s'agit de tous les documents dont la date de publication est dans le passé... mais pas du contenu du document... on ne pioche pas dans la table version. Le contenu peut être trop énorme.	
			header("location: http://".$serveur."/blog/news/");
			exit(0);
		}else{
		
			 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
			$lesDocumentsBillet = $groupeManager->getElementByTags($tags,'document');
		
			// en prenant les éléments par tags, ils sont triés par id (donc par ordre chronologique de création).
			// si l'on veut trier les billets du blog par ordre de publication, on demande la liste des documents triée et on fait l'intersection du sous ensemble pour avoir la bonne liste mais dans l'ordre
			// ceci nous permet d'écrire un document, puis d'un jour le publier sur le blog à une date récente, sinon il risquerait d'être un nouveau billet s'affichant loin dans le blog.
			$tousLesDocuments = $documentManager->getDocumentsIdByPublicationDate();
			$tousLesBillets = array_intersect($tousLesDocuments,$lesDocumentsBillet);
			
			// on obtient le nombre d'éléments que l'on veut afficher par page. 5 est un bon compromis si rien n'est choisi (idem sur skyblog et 4 sur le blog de sugus)
			if (isset($parametreUrl['n'])) {
				$nbElementParPage = $parametreUrl['n'];
			}else{
				$nbElementParPage = '5';
			}
			
			// la version xml du blog comporte plus de billets
			if ($outputFormat=='xml'){
				$nbElementParPage = '15';
			}
			
			$nbTotalBillet = count($tousLesBillets);
			
			// si le nombre de billet est plus grand que le nombre de billet prévu par page, on s'occupe de la pagination
			if ($nbTotalBillet > $nbElementParPage) {
				
					// ensuite, suivant la pagination que l'on désire, on sort du tableau les éléments que l'on ne veut pas affichier.
					// obtient la position du billet que l'on veut afficher en premier
					if (isset($parametreUrl['p'])) {
						$premierBillet = $parametreUrl['p'];
					}else{
						$premierBillet = '1';
					}
					// on vide le tableau des éléments avant le premier que l'on veut afficher
					for ($i=1; $i < $premierBillet; $i++) { 
						array_pop($tousLesBillets);  // on privilégie le array_pop au array_shift, car ainsi il n'est pas nécessaire que php réécrive toutes les clés pour les décaler, donc c'est plus rapide. On fait donc le array_reverse aprs la séection. Des éléments que l'on garde. De fait array_reverse à moins de boulot vu qu'il y a moins d'éléments !
					}

					// pour une présentation anti-chronologique, on inverse l'ordre des entrées dans le tableaux. Logiquemenet les id sont dans l'ordre chronologique.
					$billetsInverses = array_reverse($tousLesBillets);

					// on sélectionne les éléments qui vont sur la page courante
					for ($i=0; $i < $nbElementParPage; $i++) { 
						if (isset($billetsInverses[$i])) {
							$taggedElements[] = $billetsInverses[$i];
						}
					}
					
					// prépare une règle de navigation dans la pagination
					$nbPage = ceil($nbTotalBillet/$nbElementParPage);
					$liensPagination = array();
					$noBillet = '1';
					for ($i=1; $i < $nbPage+1; $i++) { 
						$liensPagination[$i] = "/blog/".trim($ressourceTags,"/")."/?p=".$noBillet;
						$noBillet = $noBillet + $nbElementParPage;
					}
					$smarty->assign("pagination",$liensPagination);
					$smarty->assign("nbTotalElement",$nbTotalBillet);
					$pageCourante = ceil($premierBillet/$nbElementParPage);
					$smarty->assign("pageCourante",$pageCourante);
			}else{
				// si le nombre de billet du blog est inférieur ou égal à la quantité désirée par page, on propose tous les billets.
				$taggedElements = $tousLesBillets;
				
				// à pour conséquence de mettre les billets dans l'ordre chronologique.
				// todo: modifier ce code pour résoudre le problème des petits blog de moins de billet que prévu par page
			}

			$documents = array(); // tableau contenant des tableaux représentant la ressource
			foreach ($taggedElements as $key => $idDocument) {
				
				// obtient le type de gestion des droits d'accès. (restriction ou exclusivité) (0 ou 1)
				$typeAcces = $documentManager->getAccessType($idDocument);

				// si l'accès est géré avec des exclusivités détermine les droits d'accès du visiteur
				if ($typeAcces=='1') {
					// obtient les groupes pour lesquels l'accès est autorisés pour ce document
					$listeGroupeAutorise = $documentManager->getGroupeAutorise($idDocument);

					// obtient la liste des groupes dans lesquels se trouve l'utilisateur
					$listeGroupeUtilisateur = $groupeManager->getGroupeUtilisateur($_SESSION['id_personne']);

					// si l'intersection entre les 2 listes est nul c'est que l'utilisateur n'as pas d'autorisation. On lui place des restrictions sur tout !
					// si l'intersection donne un résultat. C'est que l'utilisateur a une exclusivité et donc accès à tout. On ne place pas de restriction !
					$acces = array_intersect($listeGroupeAutorise,$listeGroupeUtilisateur);
					if (count($acces)==0) {
						$restrictionsCourantes = array_fill_keys(array('1','2','3','4','5','6'), ''); // crée un tableau ave les clés 1,2,3,4,5,6 et une valeur nulle
					}
				}else{
		
					// va cherche le contenu de chaque document du groupe pour autant que le visiteur ait le droit de le voir
					//$listeRestrictionsCourantes = $restrictionManager->getRestrictionsList($idDocument,'document', $_SESSION['id_personne']);
					$listeRestrictionsCourantes = array(); // todo: supprimer le management des droits par restriction
					// on crée un tableau avec les restrictions placées comme clés. Le tri sur des clés avec isset est 50x plus rapide que d'utiliser la focntion in_array
					$restrictionsCourantes = array_fill_keys($listeRestrictionsCourantes, ''); //Ne pas remplir de null car isset() retournerai FALSE même si la clé existe
				}
				if (!isset($restrictionsCourantes['1'])) {  // équivalent à !in_array('1',$restrictionsCourantes) mais 50x plus rapide !
					$document = $documentManager->getDocument($idDocument);
					$document['nomSimplifie'] = simplifieNom($document['nom']);
					$document['moisCreation'] = __(date('M',strtotime($document['date_publication'])));
					$document['jourCreation'] = __(date('j',strtotime($document['date_publication'])));
					$document['dateModifHumaine'] = dateTime2Humain($document['date_modification']);
					$document['nbCommentaire'] = $commentaireManager->getNbCommentaireElement($idDocument,'document'); // obtient le nombre de commentaire dispponible
					$document['tags'] = $groupeManager->getMotCleElement($idDocument,'document');
					$documents[$idDocument] = $document;
				}
			}
		} // if $tags
		
		// supprime les \
		stripslashes_deep($documents);
		
		// transmets les ressources à smarty
		$smarty->assign('documents',$documents);
		
		// url du flux atom
		$urlFlux = "http://".$serveur."/blog/".trim($ressourceTags,"/")."/flux.xml";
		$urlFluxCommentaires = "http://".$serveur."/commentaire/flux.xml";

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
		
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/global.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/document.js\"></script>
			<link rel=\"alternate\" type=\"application/atom+xml\" title=\"Flux Atom des billets\" href=\"".$urlFlux."\" />
			<link rel=\"alternate\" type=\"application/atom+xml\" title=\"Flux Atom des commentaires\" href=\"".$urlFluxCommentaires."\" />";
				
		$smarty->assign('additionalHeader',$additionalHeader);

		if ($outputFormat=='xml' | $outputFormat=='php') {  // le format de sortie php ainsi que le tpl associé est juste là pour faire des url du genre: http://yopyop.ch/document/lapin/toto.php?baba=fasdkfndsfnj. Ce type d'url est acceptée par netnewswire et les navigateurs web alors que si l'extension est .xml... ça ne va pas, les paramètres perturbent tout !
		
			// ajoute une entrée dans l'historique
			$historiqueManager->insertHistorique('lecture flux blog');
			$historiqueManager->purge(); // efface l'historique plus vieux qu'une semaine ou que la date fournie (datetime mysql)
		
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("blog_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("blog_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"blog_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}	
		} // if output = xml

	} //if groupe de ressource
	
} // action get

?>
