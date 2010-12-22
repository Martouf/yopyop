<?php
/*******************************************************************************************
 * Nom du fichier		: version.php
 * Date					: 5 mai 2009
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Permet d'afficher les différentes version d'un version
 *******************************************************************************************
 * 
 * On utilise ce fichier via la réécriture d'url.
 * http://yopyop.ch/version/28-momo.html => une version en particulier
 * http://yopyop.ch/version/ => toutes les versions de tous les versions... c'est tellement lourd que l'on va limiter la taille..
 * http://yopyop.ch/version/version.php?id_version=24 => toute les versions du versions 24
 * http://yopyop.ch/version/?id_version=24&important=3 => fourni le niveau d'importance minimum à prendre en compte
 * http://yopyop.ch/version/?id_version=32&summary => n'affiche pas le contenu, mais que le nom et la description de la modification
 * http://yopyop.ch/version/?id_version=23&name => n'affiche que le nom et pas le contenu.
 * http://yopyop.ch/version/28-momo.html?diff=23 => affiche la différence entre la version sélectionnée et la version dont l'id est fourni par diff=
 */

/*
 *  Attention, il faut encore terminer mettre en place la gestion des permissions !!!
 *
 */

// obtient l'id de l'élément si celui-ci existe, sinon, obtient une chaine vide.
$idVersion = $ressourceId;

// Obtient les restrictions courantes pour le visiteur courant sur l'élément courant si une ressources précise est demandée... sinon détermine plus loin au moment d'afficher une liste de ressources
$restrictionsCourantes = array();

//print_r($listeRestrictionsCourantes);

// détermine la verbosité de l'affichage. Il est ainsi possible d'affiche le contenu complet, le résumé ou juste le nom des versions. C'est le template qui s'occupe de l'affichage
// maximum, normal, resume, nom
$verbosity = "normal";

// détermine l'action demandée (add, update, delete, par défaut on suppose que c'est get, donc on ne l'indique pas)
$action = "get";
if (isset($parametreUrl['summary'])) {  // affiche uniquement le nom et le résumé du version
	$action = 'get';
	$verbosity = "resume";
}
if (isset($parametreUrl['name'])) { // affiche uniquement le nom du version
	$action = 'get';
	$verbosity = "nom";
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
$motsClesElement = $groupeManager->getMotCleElement($idVersion,'version'); // il y a certainement moyen d'optimiser tout ça... on a pas besoin d'avoir le nombre d'oocurence de l'utilisation du mot clé !! Peut être mettre une autre fonction !!

// transforme un tableau avec le mot clé et ses occurences en liste séparée par des virgules
$tagsVirgules = implode(',',array_keys($motsClesElement));
$smarty->assign('tags',$tagsVirgules);

// on défini ensuite les différentes actions possible.
// Pour l'action get, il y a plusieurs formats de sortie possibles. Le template est choisi en conséqunce. Pour le format pdf, le choix est fait en amont par index.php qui va fournir à princeXML le fichier html correspondant.

////////////////
////  GET
///////////////

if ($action=='get') {
	// il y a 2 cas possibles qui peuvent être demandés. Une ressource unique bien précise, ou un groupe de ressources.
	
	// une ressource unique
	if (!empty($idVersion)) {
		
		// si l'utilisateur a le droit de lire cette ressource.
		if (!isset($restrictionsCourantes['1'])) {  // équivalent à !in_array('1',$restrictionsCourantes) mais plus rapide !

			// va chercher les infos sur la ressource demandée
			$version = $versionManager->getVersion($idVersion);
			$version['nomSimplifie'] = simplifieNom($version['nom']);  // remplace $photoManager->simplifieNomFichier($version['nom']);
			$version['contenu'] = parseEmail($version['contenu']); // protège les adresses e-mail avec un javascript
			$version['dateModification'] = dateTime2Humain($version['date_modification']);
		
			// supprime les \
			stripslashes_deep($version);
			
			// affichage de la ressource
			$smarty->assign('version',$version);	
			
			// si l'utilisateur est inconnu il n'as pas le droit de modifier le version, donc on descative le double click
			if ($_SESSION['id_personne'] == '1') {
				$smarty->assign('utilisateurConnu',false);
			}else{
				$smarty->assign('utilisateurConnu',true);
			}

			// quelques scripts utiles
			
			$additionalHeader = "
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
				<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/version.js\"></script>";	
			$smarty->assign('additionalHeader',$additionalHeader);
			

			// certains formats ne sont jamais inclus dans un thème
			if ($outputFormat=='xml'|$outputFormat=='php') {
			
				// calcule le nom de la même ressource, mais en page html
				$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
				$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
				header('Content-Type: application/atom+xml; charset=UTF-8');
				$smarty->display("version_".LANG."_".$outputFormat.".tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
			}else{
			
				// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
				if ($theme=="no") {
					$smarty->display("version_".LANG."_html.tpl"); // affichage de la ressource brute dans la langue demandée et le format demandé
				}else{
					// affiche la ressource inclue dans le template du thème index.tpl
					$smarty->assign('contenu',"version_".LANG."_html.tpl"); // va chercher le template de la ressource demandée, dans la langue demandée et dans le format de sortie demandé.
					$smarty->display($theme."index.tpl");
				}
			} // if format = xml
		
		}// restrictions de lecture
	
	// un groupe de ressources
	}else{
		// si aucun tag est passé en paramètre, on affiche la liste complète de toutes les ressources.
		//http://yopyop.ch/version/    => va afficher la liste de toutes les versions.
		if (empty($tags)) {
			$tousVersions = $versionManager->getVersions();
		
			$versions = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getVersions()... et array_intersect
			foreach ($tousVersions as $key => $aVersion) {
				$version = $aVersion;
				
				if (!isset($restrictionsCourantes['1'])) {  // équivalent à !in_array('1',$restrictionsCourantes) mais 50x plus rapide !
					$version['nomSimplifie'] = simplifieNom($aVersion['nom']);
					$version['dateModification'] = dateTime2Humain($aVersion['date_modification']);
					$versions[$aVersion['id_version']] = $version;
				}
			}
			
			// demande de n'afficher que le résumé des versions.
			$verbosity = "resume";
		
		}else{
		
			 // va chercher les id des éléments qui correspondent aux groupes et sous-groupes fait avec les tags
			$taggedElements = $groupeManager->getElementByTags($tags,'version');
		
			$versions = array(); // tableau contenant des tableaux représentant la ressource
			// le tri est effectué par id. Donc par ordre chronologique. Si l'on veut trier autrement, il faut utiliser la fonction getVersions()... et array_intersect
			foreach ($taggedElements as $key => $idVersion) {
				
				if (!isset($restrictionsCourantes['1'])) {  // équivalent à !in_array('1',$restrictionsCourantes) mais 50x plus rapide !
					$version = $versionManager->getVersion($idVersion);
					$version['nomSimplifie'] = simplifieNom($version['nom']);
					$version['dateModification'] = dateTime2Humain($version['date_modification']);
					$versions[$idVersion] = $version;
				}
			}
		} // if $tags
		
		// supprime les \
		stripslashes_deep($versions);
		
		// transmets les ressources à smarty
		$smarty->assign('versions',$versions);

		// quelques scripts utiles
		$additionalHeader = "
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/global.js\"></script>
			<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/version.js\"></script>";	
		$smarty->assign('additionalHeader',$additionalHeader);

		// fourni à smarty le niveau de détail à afficher
		$smarty->assign("verbosity",$verbosity);

		if ($outputFormat=='xml' | $outputFormat=='php') {  // le format de sortie php ainsi que le tpl associé est juste là pour faire des url du genre: http://yopyop.ch/version/lapin/toto.php?baba=fasdkfndsfnj. Ce type d'url est accepét par netnewswire et les navigateurs web alors que si l'extension est .xml... ça ne va pas, les paramètres perturbent tout !
			// calcule le nom de la même ressource, mais en page html
			$alternateUrl = str_replace("xml","html",$serveur.$_SERVER['REQUEST_URI']);
			$smarty->assign('alternateUrl',"http://".$alternateUrl);
			
			header('Content-Type: application/atom+xml; charset=UTF-8');
			$smarty->display("version_multi_".LANG."_".$outputFormat.".tpl"); // affiche les ressources qui correspondent aux tags.
		}else{
			
			// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
			if ($theme=="no") {
				$smarty->display("version_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags.
			}else{
				// affiche la ressource inclue dans le template du thème index.tpl
				$smarty->assign('contenu',"version_multi_".LANG."_html.tpl"); // affiche les ressources qui correspondent aux tags. On va chercher le template dans la langue demandée et dans le format de sortie demandé.
				$smarty->display($theme."index.tpl");
			}	
		} // if output = xml

	} //if groupe de ressource
	
////////////////
////  DELETE
///////////////

}elseif ($action=='delete') {
	
	// on décrète que por supprimer un version il faut être un utilisateur connu
	if ($_SESSION['id_personne'] != '1') {
		
		// si aucune restriction en écriture éxiste
		if (!isset($restrictionsCourantes['2'])) {
			$versionManager->deleteVersion($idVersion);
		}
	}
}

?>
