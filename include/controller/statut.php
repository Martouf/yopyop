<?php
/*******************************************************************************************
 * Nom du fichier		: statut.php
 * Date					: 1 janvier 2009
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Permet de gérer des statuts. Pour afficher un message genre MSN ou facebook
 *******************************************************************************************
 * Pour différentier les status des gens
 * 
 * On utilise ce fichier via la réécriture d'url.
 * URL pour lire, ajouter, modifier ou supprimer une ressource. Les paramètres supplémentaires sont envoyés par POST:
 * http://yopyop.ch/statut/28-momo.html  (get)
 * http://yopyop.ch/statut/statut.html?add
 * http://yopyop.ch/statut/28-momo.html?update
 * http://yopyop.ch/statut/28-momo.html?delete
 *
 * URL pour afficher des vues utiles aux actions demandées:
 * http://yopyop.ch/statut/statut.html?new   => fourni une interface vide de création qui peut être utilisée pour soumettre un nouvel élément à l'url add
 * http://yopyop.ch/statut/28-momo.html?modify   => fourni une interface de modification avec les données de l'élément qui peut être utilisée pour soumettre les modifications à l'url update
 * http://yopyop.ch/statut/martouf/statut.xml => permet de s'abonner à toute la liste des statuts. D'un utilisateurs. 
 */

/*
 *  Attention, il faut encore mettre en place la gestion des permissions !!!
 *
 */

// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idStatut = $ressourceId;

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
if (isset($parametreUrl['import-facebook-status'])) {
	$action = 'import-facebook-status';
}
$mode = "liste";
if (isset($parametreUrl['mode'])) {
	$mode = $parametreUrl['mode']; // liste, couleur
}

// seul le mode nuage est public pour garantir la confidentialité des flux facebook
if ($_SESSION['id_personne'] == '1') {
	$mode = "nuage";
}

$triNuage = "";
if (isset($parametreUrl['trinuage'])) {
	$triNuage = $parametreUrl['trinuage']; // alphabet, popularite
}
$filtreTailleMinimalMot = 2;
if (isset($parametreUrl['taille_minimum'])) {
	$filtreTailleMinimalMot = $parametreUrl['taille_minimum']; // alphabet, popularite
}
// filtre les mots qui sont dans une liste
$tabou = false;
if (isset($parametreUrl['tabou'])) {
	$tabou = true;
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
	if (!empty($idStatut)) {
		
		// va chercher les infos sur la ressource demandée
		$statut = $statutManager->getStatut($idStatut);
		$statut['pseudo'] = $personneManager->getPseudo($statut['id_auteur']);
		$statut['dateModification'] = dateTime2Humain($statut['date_modification']);
		$statut['datePublication'] = dateTime2Humain($statut['date_publication']);
		$statut['nomBrut'] = strip_tags($statut['nom']);	 // converti le html en texte.
		
		
		// supprime les \
		stripslashes_deep($statut);
		
		// affichage de la ressource
		$smarty->assign('statut',$statut);	

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/statut.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		// certains formats ne sont jamais inclu dans un thème
		if ($outputFormat=='xml' or $outputFormat=='php' ) {
			
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("statut_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("statut_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"statut_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}
		} // if format = xml

	
	// un groupe de ressources
	}else{
		
		// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
		//http://yopyop.ch/statut/    => va afficher la liste de tous les statuts.
		if (empty($tags)) {
			$tousStatuts = $statutManager->getStatuts();
			
			$statMots = array(); // stocke les mots avec leur occurences pour l'ensemble des statuts (avec pour clé le mots)
			
			$statuts = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getStatuts()... et array_intersect
			foreach ($tousStatuts as $key => $aStatut) {
				$statut = $aStatut;
				$statut['dateModification'] = dateTime2Humain($aStatut['date_modification']); // va chercher la date de modification humaine du statuts
				$statut['datePublication'] = dateTime2Humain($statut['date_publication']);
				$statut['pseudo'] = $personneManager->getPseudo($aStatut['id_auteur']);	 // va chercher le pseudo de l'auteur du statut
				$statut['nomBrut'] = strip_tags($aStatut['nom']);	 // converti le html en texte et entités.
				$statut['description'] = strip_tags($aStatut['description']);
				$hashAuteur = md5($aStatut['auteur_texte']);
				$r = substr($hashAuteur, 0, 2); 
				$g = substr($hashAuteur, 2, 2); 
				$b = substr($hashAuteur, 4, 2);
				$statut['color'] = $r.$g.$b;
				$statut['nbchar'] = strlen($aStatut['description']); // nombre de caractères de la chaine de contenu
				$statut['width'] = floor(40*log($statut['nbchar']));
				$statut['height'] = "120";
			
				if ($mode=="nuage") {
					// on simplifie le string pour virer les trucs inutiless.... (html, ponctuation, déterminant,')
					$statutSimplifie = $statut['description'];
					$statutSimplifie = strtolower($statutSimplifie);
					$statutSimplifie = preg_replace("/\'/"," ",$statutSimplifie); // remplace les apostrophes par des espaces.
					$statutSimplifie = preg_replace("/\’/"," ",$statutSimplifie); // remplace les apostrophes par des espaces.
					$statutSimplifie = preg_replace("/\./","",$statutSimplifie); // supprime les .
					$statutSimplifie = preg_replace("/\!/"," !",$statutSimplifie); // remplace les "!" par de " !"
					$statutSimplifie = preg_replace("/\?/"," ?",$statutSimplifie); // remplace les "?" par de " ?"
					$statutSimplifie = preg_replace("/\;/","",$statutSimplifie); // supprime les ;
					$statutSimplifie = preg_replace("/\,/","",$statutSimplifie); // supprime les ,
					$statutSimplifie = preg_replace("/\"/","",$statutSimplifie); // supprime les "
					$mots = explode(' ',$statutSimplifie);
					foreach ($mots as $key => $mot) {
						if (isset($statMots[$mot])) {
								$statMots[$mot] = $statMots[$mot]+1;
						}else{
							if ($tabou) {
								$motsTabou4lettres = "pour avec dans vous tous mais elle nous parce ceux votre vers même était fait";
								$motsTabou3lettres = "les est que pas des que qui une sur aux moi ses mon";
								$motsTabou2lettres = "de la le et à les un en je qu il qui ce du me au on ça se ne sa ai si";
								$motsTabou1lettre = "! à a l d c : y n t s j i";

								$motsTabou = explode(" ",$motsTabou4lettres." ".$motsTabou3lettres." ".$motsTabou2lettres." ".$motsTabou1lettre);
								
								// filtre les mots tabou.
								if (!in_array($mot, $motsTabou)) {
									if (strlen($mot)>($filtreTailleMinimalMot-1)) {  // filtre les mots trop petits.
										$statMots[$mot] = 1;
									}
								}
							}else{
								if (strlen($mot)>($filtreTailleMinimalMot-1)) {  // filtre les mots trop petits.
									$statMots[$mot] = 1;
								}
							}
						}
					}
				} // if nuage
				$statuts[$aStatut['id_statut']] = $statut;
			}
			$statuts = array_reverse($statuts);
			
		}else{
		
			 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
			$taggedElements = $groupeManager->getElementByTags($tags,'statut');
			
			$taggedElements = array_reverse($taggedElements);  // met les posts dans l'ordre chronologique inverse
		
			$statuts = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getStatuts()... et array_intersect
			foreach ($taggedElements as $key => $idStatut) {
				$statuts[$idStatut] = $statutManager->getStatut($idStatut);
				$statuts[$idStatut]['dateModification'] = dateTime2Humain($statuts[$idStatut]['date_modification']); // va chercher la date de modification humaine du statuts
				$statut['datePublication'] = dateTime2Humain($statut['date_publication']);
				$statuts[$idStatut]['pseudo'] = $personneManager->getPseudo($statuts[$idStatut]['id_auteur']);	 // va chercher le pseudo de l'auteur du statut
				$statut['nomBrut'] = htmlentities($aStatut['nom']);	 // converti le html en texte et entités. TODO: virer carrément le code html
				$hashAuteur = md5($aStatut['auteur_texte']);
				$r = substr($hashAuteur, 0, 2); 
				$g = substr($hashAuteur, 2, 2); 
				$b = substr($hashAuteur, 4, 2);
				$statut['color'] = $r.$g.$b;
				$statut['width'] = "100";
				$statut['height'] = "20";
			}
		} // if $tags
		
		// supprime les \
		stripslashes_deep($statuts);
		
		function sortStatuts($a, $b) {
                $date_a = strtotime($a['date_publication']);
                $date_b = strtotime($b['date_publication']);
                return $date_a > $date_b ? 1 : -1;
        }

        // trie les evenements par ordre chronologique, inverse
        uasort($statuts, 'sortStatuts');
		$statuts = array_reverse($statuts);
		
		// transmets les ressources à smarty
		$smarty->assign('statuts',$statuts);

		if ($mode=="nuage") {
		
				// statistiques sur le nombre d'occurence des mots en nuages de tags
				// mots les plus courants..
				// ! de la le à et a les pour un est en l je d pas que des avec c qu qui ♥ ce dans il une plus s me ? du au fait on vous : j se ça mais toi tous :d paris sa elle neige ne y n nous sur ai soir mon bonne aux trop you t faire merci journée aime is vers bon moi si quand :-) -- tu être va ma 2 :) bien ravis très		
		
				if ($triNuage=="alphabet") {
					ksort($statMots); // trie par ordre alphabétique
				}else{
					arsort($statMots); // trie du plus grand au plus petit.
				}
				$contenuStatsMots = "";
				foreach ($statMots as $mot => $occurrence) {
					$hashMot = md5($mot);
					$r = substr($hashMot, 0, 2); 
					$g = substr($hashMot, 2, 2); 
					$b = substr($hashMot, 4, 2);
					$couleur = $r.$g.$b;
					$contenuStatsMots .= "<span class=\"nuage\" rel=\"tag\" title=\"".$occurrence."\" style=\"font-size:".(100+floor(100*log($occurrence)))."%; color:#".$couleur."\" >".$mot."</span> ";
				}
				$smarty->assign('contenuStatsMots',$contenuStatsMots);
		}
		// url du flux atom
		$urlFlux = "http://".$serveur."/statut/".trim($ressourceTags,"/")."/statuts.xml";

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/statut.js\"></script>
			<link rel=\"alternate\" type=\"application/atom+xml\" title=\"Atom\" href=\"".$urlFlux."\" />";
				
		$smarty->assign('additionalHeader',$additionalHeader);


		if ($outputFormat=='xml' or $outputFormat=='php') {
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("statut_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("statut_multi_".$mode."_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"statut_multi_".$mode."_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}	
		} // if output = xml

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
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='0';
	}
	
	$id_auteur = $_SESSION['id_personne'];

	// ajoute la nouvelle ressource
	$idStatut = $statutManager->insertStatut($nom,$id_auteur,$description,$evaluation);
	
	// simplifie le pseudo pour éviter d'avoir un tag et donc une url trop bizarre.
	$pseudoSimple = simplifieNom($_SESSION['pseudo']);
	
	// tag le statut avec le pseudo simplifié de l'auteur
	$groupeManager->ajouteMotCle($idStatut,$pseudoSimple,'statut');
	
	echo $idStatut; // est utilisé pour transmettre l'id du nouvel élément lors d'une communication ajax

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
	if(isset($_POST['evaluation'])){
		$evaluation = $_POST['evaluation'];
	}else{
		$evaluation ='';
	}
	
	$id_auteur = $_SESSION['id_personne'];
	
	// fait la mise à jour
	$statutManager->updateStatut($idStatut,$nom,$id_auteur,$description,$evaluation);

////////////////
////  DELETE
///////////////

}elseif ($action=='delete') {
	// avant de supprimer, il faut toujours enlever les tags d'abord. Sinon il reste des ressources fantômes...
	// ceci n'est pas fait ici. Il faut donc le faire rapidement via un appel ajax.
	
	$statutManager->deleteStatut($idStatut);
	
////////////////
////  NEW
///////////////
}elseif ($action=='new') {
	// on fourni le pseudo... pour pouvoir écrire... toto mange une glace.
	$smarty->assign('pseudo',$_SESSION['pseudo']);
	
	// quelques scripts utiles
	$additionalHeader = "
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/statut.js\"></script>";	
	$smarty->assign('additionalHeader',$additionalHeader);
	
	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("statut_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"statut_new_".LANG.".tpl"); // affichage de l'interface vide qui permet de créer une ressource
		$smarty->display($theme."index.tpl");
	}

////////////////
////  MODIFY
///////////////
}elseif ($action=='modify') {
	
	// on fourni le pseudo... pour pouvoir écrire... toto mange une glace.
	$smarty->assign('pseudo',$_SESSION['pseudo']);
	
	// va chercher les infos sur la ressource demandée
	$statut = $statutManager->getStatut($idStatut);
	$statut['nomSimplifie'] = simplifieNom($statut['nom']);
	
	// supprime les \
	stripslashes_deep($statut);
	
	// passe les données de la statut à l'affichage
	$smarty->assign('statut',$statut);
	
	// quelques scripts utiles
	$additionalHeader = "
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.pack.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/interface.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.bgiframe.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/jquery.autocomplete.js\"></script>
		<script type=\"text/javascript\" src=\"http://".$serveur."/utile/js/statut.js\"></script>";	
	$smarty->assign('additionalHeader',$additionalHeader);
	
	// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
	if ($theme=="no") {
		$smarty->display("statut_modify_".LANG.".tpl"); // affichage de l'interface de modification de la ressource
	}else{
		// affiche le formulaire de modification inclu dans le template du thème index.tpl
		$smarty->assign('contenu',"statut_modify_".LANG.".tpl");
		$smarty->display($theme."index.tpl");
	}

////////////////
////  IMPORT FACEBOOK STATUTS
///////////////
}elseif ($action=='import-facebook-status') {

	$url = "http://www.facebook.com/feeds/friends_status.php?id=684590465&key=dfeaeb4901&format=rss20&flid=0";
//	$url = "http://martouf.ch/statut/feed.xml";

	if (!empty($url)) {
	    // * obtenir le fichier rss (régulièrement en fonction des visites)
	    // * créer un tableau avec pour index le hash md5 du contenu et de la date.
	    // * créer un tableau avec tout les hash déjà présent dans la base. (limiter aux statuts récents)
	    // * pour chaque élément du tableau tester si son hash est déjà présent dans la base
	    // * => si ce n'est pas le cas, ajouter l'élément
	
		// // cas simple..
		// $url = "http://martouf.ch/statut/feed.xml";
		// $xml = simplexml_load_file($url);
		// print_r($xml);
	
		// facebook n'est pas assez cool pour accepter d'autre navigateur web que Firefox, safari et IE => http://www.facebook.com/common/browser.php
		// donc il faut pouvoir préciser un autre useragent !!
		
		// cas avec curl en exec		
		// $cmd = 'exec "'.$url.'" > toto.xml';
		// curl "http://www.facebook.com/feeds/friends_status.php?id=684590465&key=dfeaeb4901&format=rss20&flid=0" --user-agent Mozilla/4.0 > toto.xml
		// exec($cmd);
		
		// cas avec curl dans php
		function curl_get_file_contents($URL){
			$userAgent = 'Mozilla/4.0';
	        $c = curl_init();
			curl_setopt($c, CURLOPT_USERAGENT, $userAgent);
	        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
	        curl_setopt($c, CURLOPT_URL, $URL);
	        $contents = curl_exec($c);
	        curl_close($c);
	        		
	        if ($contents) return $contents;
	            else return FALSE;
		}
		
		$xmlFeed = curl_get_file_contents($url);
		$xml = simplexml_load_string($xmlFeed);
	//	print_r($xml);  // debug
		
		// exemple de résultat:
		// [guid] => http://www.facebook.com/profile.php?id=1216222216&story_fbid=123381601058272
		// [title] => Ariane Wilhem [ Wahouuuu :D Merci à tous, c'était une soirée de pure bonheur!! <3 INOUBLIABLE!  ]
		// [link] => http://www.facebook.com/profile.php?id=1216222216&story_fbid=123381601058272
		// [description] => [ Wahouuuu :D Merci à tous, c'était une soirée de pure bonheur!! ♥ INOUBLIABLE!  ]
		// [pubDate] => Sun, 05 Dec 2010 11:50:49 -0100
		// [author] => Ariane Wilhem
		
		// comparaison champ fb et ma table:
		// [guid] => guid
		// [title] => nom
		// [link] => -
		// [description] => description
		// [pubDate] => date_publication
		// [author] => auteur_texte
		
		// obtient les guids des statuts déjà présent
		$dateCourante = date('Y-m-d H:i:s',time());
		$dateLimite = date('Y-m-d H:i:s',strtotime($dateCourante." -1 week")); // date d'ili y a une semaine
		$lastGuids = $statutManager->getGuids($dateLimite);
		//print_r($lastGuids); /// debug
		
		echo "<p class=\"ok\"><a href=\"//" . $serveur . "/statut/\">retour à la liste</a></p>";
		
		foreach ($xml->channel->item as $statut) {
			$datePublication = date('Y-m-d H:i:s',strtotime($statut->pubDate));
			
			// echo "<h2>",dateTime2Humain($datePublication),"- <a href=",$statut->guid,">",$statut->author,"</a></h2>";
			// echo "<p>",$statut->description,'</p>';
			
			if (!in_array($statut->guid,$lastGuids)) {
				// $statutManager->insertStatut($nom,$id_auteur,$description,$evaluation,$guid,$datePublicatio,$auteurTexte);
				$statutManager->insertStatut($statut->title,'1',$statut->description,'0',$statut->guid,$datePublication,$statut->author);
				echo "<br />ajout dans la bd de...".$statut->title; // debug
			}else{
				echo "<br />ok!";
			}
		}
		echo "<p class=\"ok\"><a href=\"//" . $serveur . "/statut/\">retour à la liste</a></p>";
	}else{
		echo "<p class=\"erreur\">veuillez fournir une url de flux facebook</p>";
	}
	
}// class

?>
