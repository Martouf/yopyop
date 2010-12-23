<?php
/*******************************************************************************************
 * Nom du fichier		: commentaire.php
 * Date					: 21 janvier 2009
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@ecodev.ch
 * But de ce fichier	: Permet de gérer les commentaires qui sont appliquéss sur des ressources.
 *******************************************************************************************
 * Interface qui permet d'afficher une commentaire ou l'interface de modification d'une commentaire.
 * les actions add et delete ne sont pas totalement identique à ce que l'on peut voir pour les autres ressources.
 * En effet, add, ne fait pas que créer le commentaire, mais cette action crée et lie le commentaire à une ressource.
 * L'action delete, supprime la liaison entre une ressource et un commentaire avec de supprimer bêtement le commentaire en laissant une liaison orpheline.
 *
 * 
 * On utilise ce fichier via la réécriture d'url.
 * URL pour lire, ajouter, modifier ou supprimer une ressource. Les paramètres supplémentaires sont envoyés par POST:
 * http://yopyop.ch/commentaire/28-momo.html  (get)
 * http://yopyop.ch/commentaire/commentaire.html?add
 * http://yopyop.ch/commentaire/28-momo.html?update
 * http://yopyop.ch/commentaire/28-momo.html?delete
 *
 * URL pour afficher des vues utiles aux actions demandées:
 * http://yopyop.ch/commentaire/commentaire.html?new   => fourni une interface vide de création qui peut être utilisée pour soumettre un nouvel élément à l'url add
 * http://yopyop.ch/commentaire/28-momo.html?modify   => fourni une interface de modification avec les données de l'élément qui peut être utilisée pour soumettre les modifications à l'url update
 *
 * URL pour afficher ou suivre les commentaires associés à une ressource précise
 * http://yopyop.ch/commentaire/?id_element=21&type_element=document   => obtient les commentaires du document 21
 * http://yopyop.ch/commentaire/?id_element=21 => url raccourcies, car par défaut va voir le document
 * http://yopyop.ch/commentaire/ => affiche tous les commentaires.
 *
 * http://yopyop.ch/commentaire/flux.php?id_element=21&type_element=document => obtient le flux atom des commentaire du document 21. php et xml fonctionnent, mais php est mieux supporté par les egrégateurs.
 */


  // Il faut encore ajouter la gestion des restrictions. Pour l'instant, c'est juste l'affichage qui ne se fait pas... mais par injection dans le formulaire c'est facile d'envoyer un commentaire. !


// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idCommentaire = $ressourceId;

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

// on défini ensuite les différentes actions possible.
// Pour l'action get, il y a plusieurs formats de sortie possibles. Le template est choisi en conséqunce. Pour le format pdf, le choix est fait en amont par index.php qui va fournir à princeXML le fichier html correspondant.

////////////////
////  GET
///////////////

if ($action=='get') {
	// il y a 2 cas possibles qui peuvent être demandé. Une ressource unique bien précise, ou un groupe de ressource.
	
	// une ressource unique
	if (!empty($idCommentaire)) {
		
		// va chercher les infos sur la ressource demandée
		$commentaire = $commentaireManager->getCommentaire($idCommentaire);
		
		// supprime les \
		stripslashes_deep($commentaire);
				
		// affichage de la ressource
		$smarty->assign('commentaire',$commentaire);	

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/commentaire.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		// certains formats ne sont jamais inclu dans un thème
		if ($outputFormat=='xml'|$outputFormat=='php') {
			
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("commentaire_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("commentaire_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"commentaire_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}
		} // if format = xml

	
	// un groupe de ressources
	}else{
		
		// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
		//http://yopyop.ch/commentaire/    => va afficher la liste de toutes les commentaires.
		if (empty($tags)) {
			
			if (isset($parametreUrl['id_element'])) {  // $parametreUrl => tableau global qui contient les paramètres get
				$idElementCommente = $parametreUrl['id_element'];
			}else{
				$idElementCommente = '';
			}
			
			if (isset($parametreUrl['type_element'])) {  // $parametreUrl => tableau global qui contient les paramètres get
				$typeElement = $parametreUrl['type_element'];
			}else{
				$typeElement = 'document';
			}
			
			// otient tous les commentaire qui sont lié à une ressources
			if (!empty($idElementCommente)) {
				
				$tousCommentaires = $commentaireManager->getCommentaireElement($idElementCommente,$typeElement);
			}else{
				
				// va chercher TOUS les commentaire disponibles
				$tousCommentaires = $commentaireManager->getCommentaires();	
			}
			
			$commentaires = array();
			foreach ($tousCommentaires as $key => $aCommentaire) {
				$commentaires[$key] = $aCommentaire;
				$commentaires[$key]['dateCreation'] = dateTime2Humain($aCommentaire['date_creation']);
				$commentaires[$key]['auteur'] = $personneManager->getPseudo($aCommentaire['id_auteur']); // pseudo de l'auteur plutôt que id
				$infoElementCommente = $commentaireManager->getElementCommentaire($aCommentaire['id_commentaire']);
				$commentaires[$key]['id_element_commente'] = $infoElementCommente[0] ;
				$commentaires[$key]['type_element_commente'] = $infoElementCommente[1];
			}
			
		}else{
		
			// Les commentaires n'étant pas tagués....  cette partie est inutile ! .. mais on ne sais jamais, un jour peut être.. on taguerra les commentaires !!!
			 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
			$taggedElements = $groupeManager->getElementByTags($tags,'commentaire');
		
			$commentaires = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getCommentaires()... et array_intersect
			foreach ($taggedElements as $key => $idCommentaire) {
				$commentaires[$idCommentaire] = $commentaireManager->getCommentaire($idCommentaire);
				
				// $commentaires[$idCommentaire]['nomCommentaire'] = $nomCommentaire;
			}
		} // if $tags
		
		// supprime les \
		stripslashes_deep($commentaires);
		
		// transmets les ressources à smarty
		$smarty->assign('commentaires',$commentaires);
		
		// info sur l'utilisateur qui va poster un commentaire
		$smarty->assign('idAuteurCommentaire',$_SESSION['id_personne']);
		$smarty->assign('pseudoUtilisateur',$_SESSION['pseudo']);

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/commentaire.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		if ($outputFormat=='xml'|$outputFormat=='php') {
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("commentaire_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("commentaire_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"commentaire_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}	
		} // if output = xml

	} //if groupe de ressource
	
////////////////
////  ADD
///////////////
	
}elseif ($action=='add') {
	// obtient les données du commentaire
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
	if(isset($_POST['id_auteur'])){
		$id_auteur = $_POST['id_auteur'];
	}else{
		$id_auteur ='1';
	}
	if(isset($_POST['mail'])){
		$mail = $_POST['mail'];
	}else{
		$mail ='';
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
	
	// obtient les données de l'élément que l'on commente
	if(isset($_POST['id_element'])){
		$id_element = $_POST['id_element'];
	}else{
		$id_element ='';
	}
	if(isset($_POST['table_element'])){
		$table_element = $_POST['table_element'];
	}else{
		$table_element ='document';
	}
	
	/////// anti-spam //////
	$domain = $_SERVER['HTTP_HOST'];
    if (preg_match("/([^\.]+\.[a-z]{2,4})$/",$domain,$match)) {
            $domain = $match[1]; // ne conserve que le domaine de second niveau (mondomaine.ch)
    }
    $domain = '.'.$domain; // notation ".mondomaine.ch" pour couvrir tous les sous-domaines

    // nom du cookie d'authentification
    $authCookieName = 'ticket_commentaire';
	// validité en secondes du cookie d'authentification
	$authCookieLifetime = 3600;
	
	$errorMsg = array();


	if (isset($_COOKIE[$authCookieName]) && preg_match("/^[0-9A-Z]{32}$/i",$_COOKIE[$authCookieName])) {
		// vérifie la validité du cookie d'authentification
		$ticket_id = $_COOKIE[$authCookieName];
		
		// obtient le ticket qui correspond à l'id fourni
		$ticket = $commentaireManager->checkTicket($ticket_id);
		
		if (!empty($ticket)) {
			if (time() - $ticket['time'] > $authCookieLifetime) {
                    $errorMsg[] = "Votre session a expiré, veuillez remplir à nouveau le formulaire";
            } elseif ($domain !== $ticket['domain']) {
                    $errorMsg[] = "La session n'est valable que pour le domaine ".$ticket['domain'];
            } elseif ($_SERVER['REMOTE_ADDR'] !== $ticket['remote_ip']) {
                    $errorMsg[] = "La session n'est valable que pour l'adresse IP ".$ticket['remote_ip'];
            } else {
                    // marque le ticket comme utilisé (le conserve quelques temps à des fins d'historique)
					$commentaireManager->putTicketToTrash($ticket_id);
					
                    // efface le cookie du navigateur
                    setcookie($authCookieName,false,time()-1,'/',$ticket['domain']);

                    // purge les tickets délivrés depuis plus de 4h (utilisés ou non)
                    $commentaireManager->ticketGarbageCollector();
            }
		}else{
			$errorMsg[] = "Votre session n'est plus valable, veillez remplir à nouveau le formulaire";
		}
    } else {
            $errorMsg[] = "L'usage du script est interdit aux robots et pages externes (erreur d'authentification ou de flooding)";
    }
	
	// si aucune erreur d'anti-spam n'est survenue, alors on peut ajouter le commentaire. Sinon on retourne l'erreur
	if (count($errorMsg) == 0) {
		
		// ajoute le nouveau commentaire
		$idCommentaire = $commentaireManager->insertCommentaire($nom,$description,$id_auteur,$mail,$url,$evaluation);

		// lie ce commentaire avec la ressources désirée
		$commentaireManager->associerCommentaire($idCommentaire,$id_element, $table_element,$evaluation);

		echo $idCommentaire; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax
	}else{
		// affiche l'erreur
		echo $errorMsg[0];
	}
	


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
	if(isset($_POST['id_auteur'])){
		$id_auteur = $_POST['id_auteur'];
	}else{
		$id_auteur ='1';
	}
	if(isset($_POST['mail'])){
		$mail = $_POST['mail'];
	}else{
		$mail ='';
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
	
	// fait la mise à jour
	$commentaireManager->updateCommentaire($idCommentaire,$nom,$description,$id_auteur,$mail,$url,$evaluation);

////////////////
////  DELETE
///////////////

}elseif ($action=='delete') {
	
	// n'autorise que les admin de rang 1 (le plus haut rang) à pouvoir supprimer des commentaires
	if ($_SESSION['rang']!='1') {
		header("Location: /");
	}
	
	// obtient les données de l'élément lié au commentaire que l'on veut supprimer
	if(isset($_POST['id_element'])){
		$id_element = $_POST['id_element'];
	}else{
		$id_element ='';
	}
	if(isset($_POST['table_element'])){
		$table_element = $_POST['table_element'];
	}else{
		$table_element ='';
	}
	
	echo "effacer..", $idCommentaire,$id_element, $table_element;
	
	$commentaireManager->dissocierCommentaire($idCommentaire,$id_element, $table_element);
	$commentaireManager->deleteCommentaire($idCommentaire);
	
////////////////
////  NEW
///////////////
}elseif ($action=='new') {
	
	// quelques scripts utiles
	$additionalHeader = "
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/commentaire.js\"></script>";	
	$smarty->assign('additionalHeader',$additionalHeader);
	
	
	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("commentaire_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"commentaire_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
		$smarty->display($theme."index.tpl");
	}

////////////////
////  MODIFY
///////////////
}elseif ($action=='modify') {
	// va chercher les infos sur la ressource demandée
	$commentaire = $commentaireManager->getCommentaire($idCommentaire);
	
	// supprime les \
	stripslashes_deep($commentaire);
	
	// passe les données de la commentaire à l'affichage
	$smarty->assign('commentaire',$commentaire);
	
	// quelques scripts utiles
	$additionalHeader = "
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/commentaire.js\"></script>";	
	$smarty->assign('additionalHeader',$additionalHeader);
	
	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("commentaire_modify_".LANG.".tpl"); // affichage de l'interface de modification de la ressource
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"commentaire_modify_".LANG.".tpl");
		$smarty->display($theme."index.tpl");
	}		
} // actions
?>
