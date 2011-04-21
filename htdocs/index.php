<?php
	/*******************************************************************************************
	 * Nom du fichier		: index.php
	 * Date					: 7 mars 2007 - novembre 2008
	 * Auteur				: Mathieu Despont
	 * Adresse E-mail		: mathieu@marfaux.ch
	 * But de ce fichier	: Pose le cadre de base de la mise en page avec le système d'inclusion.
	 *******************************************************************************************
	 * Cette page permet de faire un système de navigation de pages par inclusion de fichier .php
	 *
	 */	

	require('../include/init/init.php');
	new Init(); //initialise l'application en créant les objets utiles
	include('../include/init/identity.php'); // détecte l'identité de l'utilisteur
	
	// les infos utiles sur la ressource son accessible de partout
	global $ressourceType, $ressourceTags, $ressourceId, $ressourceOutput;
	
	$ressourceType = '';
	if (isset($_GET['page']) && preg_match("/[a-zA-Z0-9]+/",$_GET['page'])) {
		$ressourceType = $_GET['page'];
	}
	$ressourceTags = '';
	if (isset($_GET['keywords'])) {
		$ressourceTags = $_GET['keywords'];
	}
	$ressourceId = '';
	if (isset($_GET['id'])) {
		$ressourceId = $_GET['id'];
	}
	$ressourceOutput = '';
	if (isset($_GET['output'])) {
		$ressourceOutput = $_GET['output'];
	}
	// echo "<br />Page: ".$ressourceType;
	// echo "<br />Tags: ".$ressourceTags;
	// echo "<br />Id: ".$ressourceId;
	// echo "<br />output: ".$ressourceOutput;
	
	// choix du thème que l'utilisateur veut appliquer.
	// Le thème permet de modifier la structure html de la page. (plus que le css) Le thème "index" inclu le contenu dans le template index.tpl.
	// Le thème "no" permet d'afficher uniquement le contenu. Il est utilisé inclure du contenu par ajax sans être parasité par le doctype ou des entêtes et pieds de page.
	// Ensuite, on peut imaginer faire d'autre thème. Par ex: iPhone, iLiad etc..
	// Quand on change de thème, en fait on remplace le cadre autour, donc le fichier index.tpl dans lequel on place le contenu. Le contenu lui même n'est donc pas modifié
	// le nom du thème par défaut est définit dans le fichier de config.
	// Si l'on voulait le thème basic, on aurait le fichier basicindex.tpl
	
	if (isset($parametreUrl['theme'])) {  // $parametreUrl => tableau global qui contient les paramètres get
		$theme = $parametreUrl['theme'];
	}
	
	// va chercher le menu
	// Le menu est le document avec l'id. Pour modifier le menu, il suffit de modifier ce document.
	$document = $documentManager->getDocument('1');
	$smarty->assign('menu',stripcslashes($document['contenu']));
	
	// fourni le nom du serveur pour pouvoir contourner la réécriture d'url
	$serveur = str_replace(realpath($_SERVER['DOCUMENT_ROOT']), $_SERVER['SERVER_NAME'], dirname(__FILE__));
	$smarty->assign('server_name',$serveur);
	$smarty->assign('request_uri',$_SERVER['REQUEST_URI']); // fourni aussi l'uri complète demandée (avec les paramètres). La concaténation de server_name et request_uri fourni l'adress qui est affichée dans le navigateur web
	
	// trouve le nom du fichier. Parfois utile pour généré un nom de fichier.
	$pattern = "@(.*/)?([0-9]+-)?(.*)\.[a-z]+@"; // que le nom du fichier
	preg_match($pattern,$_SERVER['REQUEST_URI'],$resultat);
	if(isset($resultat[3])){
		$ressourceName = $resultat[3];
	}else{
		$ressourceName = "Yop";
	}
	$smarty->assign('file_name',$ressourceName);
	
	// on demande une sortie pdf !!
	if ($ressourceOutput=='pdf') {
		
 		// détermine le moteur pdf utilisé: prince ou wkhtmltopdf
		if ($pdfEngine=='prince') {
			$requete = $_SERVER['REQUEST_URI'];

			 header('Content-Type: application/pdf');
			 header('Content-Disposition: inline; filename="'.$ressourceName.'.pdf"');
			 include("../include/lib/prince.php");

			// va cherche le même contenu mais en html
			$urlSource = "http://".$serveur.$requete;
			$newUrl = str_replace("pdf","html",$urlSource);

			function curl_get_file_contents($URL){
			        $c = curl_init();
			        curl_setopt($c, CURLOPT_RETURNTRANSFER, 1);
			        curl_setopt($c, CURLOPT_URL, $URL);
			        $contents = curl_exec($c);
			        curl_close($c);

			        if ($contents) return $contents;
			            else return FALSE;
			}

			// fetch html version
			$html = curl_get_file_contents($newUrl);

			// path to prince
			$prince = new Prince($princeXmlPath); // en local

			// convert html2pdf
			$prince->convert_string_to_passthru($html);
		}else{
			header('Content-Type: application/pdf');
			header('Content-Disposition: inline; filename="'.$ressourceName.'.pdf"');
			
			$requete = $_SERVER['REQUEST_URI'];
			// va cherche le même contenu mais en html
			$urlSource = "http://".$serveur.$requete;
			$newUrl = str_replace("pdf","html",$urlSource);

			// On utilise la feuille de style print et on sort le résultat dans la sortie standard qui est relayé dans le navigateur web via passthru
			$cmd = $wkhtmltopdfPath.' "'.$newUrl.'" --print-media-type'.' -';   // ex: wkhtmltopdf "http://martouf.ch/document/234-la-decroissance.html" --print-media-type  -
		//	$cmd = $wkhtmltopdfPath.' "'.$newUrl.'"'.' -';   // ex: wkhtmltopdf "http://martouf.ch/document/234-la-decroissance.html" -
		
			passthru($cmd);
		}
		
		// pour les autres format (souvent html)
	}else{
			
		if (empty($ressourceType)) {$ressourceType = $defaultController;}  // page par défaut => defaultController peut être choisi dans le fichier de config.
		
		if (file_exists('../include/controller/'.$ressourcesSwitch[$ressourceType].'.php')) {
			// inclut une page PHP dynamique si elle existe (elle se charge de l'affichage via Smarty)
			include('../include/controller/'.$ressourcesSwitch[$ressourceType].'.php');
		} else {
			// affiche un message d'erreur "page introuvable"
			$smarty->assign('contenu',"pageinconnue.tpl");
			$smarty->display('index.tpl');
		}
	}// ressourceOutput
	
?>
