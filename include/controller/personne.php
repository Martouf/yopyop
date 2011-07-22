<?php
/*******************************************************************************************
 * Nom du fichier		: personne.php
 * Date					: 1 novembre 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Permet de gérer des personnes. Ce fichier génère des vues utilisée dans un carnet d'adresse
 *******************************************************************************************
 * Interface qui permet d'afficher une personne ou l'interface de modification d'une personne
 * 
 * On utilise ce fichier via la réécriture d'url.
 * URL pour lire, ajouter, modifier ou supprimer une ressource. Les paramètres supplémentaires sont envoyés par POST:
 * http://yopyop.ch/personne/28-momo.html  (get)
 * http://yopyop.ch/personne/personne.html?add
 * http://yopyop.ch/personne/28-momo.html?update
 * http://yopyop.ch/personne/28-momo.html?delete
 *
 * URL pour afficher des vues utiles aux actions demandées:
 * http://yopyop.ch/personne/personne.html?new   => fourni une interface vide de création qui peut être utilisée pour soumettre un nouvel élément à l'url add
 * http://yopyop.ch/personne/28-momo.html?modify   => fourni une interface de modification avec les données de l'élément qui peut être utilisée pour soumettre les modifications à l'url update
 * http://yopyop.ch/personne/?name  => (à voir si cette url fonctionne vraiment) fourni la liste des nom des ressources
 */

	// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
	$idPersonne = $ressourceId;

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

/*
 *  Attention, il faut encore mettre en place la gestion des permissions !!!
 * Pour l'instant on limite l'accès en lecture et en écriture aux utilisateurs connus.
 */

if ($_SESSION['id_personne'] != '1') {


	// on défini ensuite les différentes actions possible.
	// Pour l'action get, il y a plusieurs formats de sortie possibles. Le template est choisi en conséqunce. Pour le format pdf, le choix est fait en amont par index.php qui va fournir à princeXML le fichier html correspondant.

	////////////////
	////  GET
	///////////////

	if ($action=='get') {
		// il y a 2 cas possibles qui peuvent être demandés. Une ressource unique bien précise, ou un groupe de ressource.
	
		// une ressource unique
		if (!empty($idPersonne)) {
		
			// va chercher les infos sur la ressource demandée
			$personne = $personneManager->getPersonne($idPersonne);
		
			// supprime les \
			stripslashes_deep($personne);
			
			// obtient la clé gravatar d'un e-mail, ainsi l'image de profile est le gravatar
			$personne['gravatar'] = md5($personne['email']);
				
			// affichage de la ressource
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
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/personne.js\"></script>";	
			$smarty->assign('additionalHeader',$additionalHeader);

			// certains formats ne sont jamais inclu dans un thème
			if ($outputFormat=='vcf') {
				header('Content-Type: text/x-vcard');
				$smarty->display("personne_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}elseif ($outputFormat=='xml') {
			
				// calcule le nom de la même ressource, mais en page html
				$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
				$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
				header('Content-Type: application/atom+xml; charset=UTF-8');
				$smarty->display("personne_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
			
				// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
				if ($theme=="no") {
					$smarty->display("personne_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
				}else{
					// affiche la ressource inclue dans le template du thème index.tpl
					$smarty->assign('contenu',"personne_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
					$smarty->display($theme."index.tpl");
				}
			} // if format = vcf

	
		// un groupe de ressources
		}else{
		
			// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
			//http://yopyop.ch/personne/    => va afficher la liste de toutes les personnes.
			if (empty($tags)) {
				$personnes = $personneManager->getPersonnes();
			}else{
		
				 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
				$taggedElements = $groupeManager->getElementByTags($tags,'personne');
		
				$personnes = array(); // tableau contenant des tableaux représentant la ressource
				// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getPersonnes()... et array_intersect
				foreach ($taggedElements as $key => $idPersonne) {
					$personnes[$idPersonne] = $personneManager->getPersonne($idPersonne);
				}
			} // if $tags
		
			// supprime les \
			stripslashes_deep($personnes);
		
			// transmets les ressources à smarty
			$smarty->assign('personnes',$personnes);

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
				<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/personne.js\"></script>";	
			$smarty->assign('additionalHeader',$additionalHeader);

			if ($outputFormat=="vcf") {
				header('Content-Type: text/x-vcard');
				$smarty->display("personne_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
			
			}elseif ($outputFormat=='xml') {
				// calcule le nom de la même ressource, mais en page html
				$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
				$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
				header('Content-Type: application/atom+xml; charset=UTF-8');
				$smarty->display("personne_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
			
				// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
				if ($theme=="no") {
					$smarty->display("personne_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
				}else{
					// affiche la ressource inclue dans le template du thème index.tpl
					$smarty->assign('contenu',"personne_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
					$smarty->display($theme."index.tpl");
				}	
			} // if output = vcf

		} //if groupe de ressource
	
	
	////////////////
	////  UPDATE
	///////////////

	}elseif ($action=='update') {
		// obtient les données
		if(isset($_POST['prenom'])){
			$prenom = $_POST['prenom'];
		}else{
			$prenom ='';
		}
		if(isset($_POST['nom'])){
			$nom = $_POST['nom'];
		}else{
			$nom ='';
		}
		if(isset($_POST['surnom'])){
			$surnom = $_POST['surnom'];
		}else{
			$surnom ='';
		}
		if(isset($_POST['description'])){
			$description = $_POST['description'];
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
		}else{
			$mot_de_passe ='';
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
			$evaluation ='';
		}
		$fortune = ''; // on ne peut pas changer la fortune ainsi.. sinon ce serait facile !
	
		// fait la mise à jour
		$personneManager->updatePersonne($idPersonne,$prenom,$nom,$surnom,$description,$date_naissance,$photo,$mot_de_passe,$rue,$npa,$lieu,$pays,$tel,$email,$rang,$url,$fortune,$evaluation);

		// redirige sur l'interface de profil de la personne
		$urlProfile = "http://".$serveur."/profile/".$idPersonne."-".$surnom.".html";
		header("Location: ".$urlProfile);

	////////////////
	////  DELETE
	///////////////

	}elseif ($action=='delete') {
		$personneManager->deletePersonne($idPersonne);
	

	////////////////
	////  MODIFY
	///////////////
	}elseif ($action=='modify') {
		// va chercher les infos sur la ressource demandée
		$personne = $personneManager->getPersonne($idPersonne);
	
		// supprime les \
		stripslashes_deep($personne);
		
		// obtient la clé gravatar d'un e-mail, ainsi l'image de profile est le gravatar
		$personne['gravatar'] = md5(strtolower(trim($personne['email'])));
			
		// passe les données de la personne à l'affichage
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
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/personne.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);
	
		// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
		if ($theme=="no") {
			$smarty->display("personne_modify_".LANG.".tpl"); // affichage de l'interface de modification de la ressource
		}else{
			// affiche le formulaire de modification inclu dans le template du thème index.tpl
			$smarty->assign('contenu',"personne_modify_".LANG.".tpl");
			$smarty->display($theme."index.tpl");
		}	
	}
} // if utilisateur connu

// les ation add et new sont accessible aux utilisateurs inconnus pour pouvoir créer des comptes utilisateurs


	////////////////
	////  ADD
	///////////////
	
	if ($action=='add') {
		// obtient les données
		if(isset($_POST['prenom'])){
			$prenom = $_POST['prenom'];
		}else{
			$prenom ='';
		}
		if(isset($_POST['nom'])){
			$nom = $_POST['nom'];
		}else{
			$nom ='';
		}
		if(isset($_POST['surnom'])){
			$surnom = $_POST['surnom'];
		}else{
			$surnom ='';
		}
		if(isset($_POST['description'])){
			$description = $_POST['description'];
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
		}else{
			$mot_de_passe ='';
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
		$fortune = '0';
	
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
		
			// ajoute la nouvelle ressource
			$idPersonne = $personneManager->insertPersonne($prenom,$nom,$surnom,$description,$date_naissance,$photo,$mot_de_passe,$rue,$npa,$lieu,$pays,$tel,$email,$rang,$url,$fortune,$evaluation);
			
			$messageBienvenue = "<p>Bienvenue sur yopyop.ch <br />Vous venez de créer un compte utilisateur avec le pseudo: ".$surnom." et le mot de passe: ".$mot_de_passe."</p><p><href=\"http://yopyop.ch/objet/?new\">Venez partager vos objets yopyop.ch</a></p>";
			$envoiOk = $notificationManager->notificationMail($email,$messageBienvenue,'Notification yopyop.ch');
			
			// notification
			$notificationManager->insertNotification('Bienvenue','Bienvenue <em>'.$surnom.'</em>.','1','0',$idPersonne);
			
		//	echo $idPersonne; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax
			
			// redirige sur l'interface de profil de la nouvelle personne
			$urlProfile = "http://".$serveur."/profile/".$idPersonne."-.html"; // url très simple => todo: améliorer l'url en plaçant le nom mais c'est juste pour l'esthétique.
			header("Location: ".$urlProfile);
		
		}else{
			// affiche l'erreur
			echo $errorMsg[0];
		}
	
	////////////////
	////  NEW
	///////////////
	}elseif ($action=='new') {

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
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/personne.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
		if ($theme=="no") {
			$smarty->display("personne_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
		}else{
			// affiche le formulaire de modification inclu dans le template du thème index.tpl
			$smarty->assign('contenu',"personne_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
			$smarty->display($theme."index.tpl");
		}
	}


?>
