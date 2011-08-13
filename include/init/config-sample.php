<?php
//**********************************************************
// Database connection settings
//**********************************************************
$DBHost= 'localhost';
$DBUser= 'root';
$DBPwd = '';
$DBName= 'yopyop';

//**********************************************************
// Local settings
//**********************************************************

global $googleMapsKey;

//$googleMapsKey = "ABQIAAAAsyuA7bSIBFzUGUhfQJbrIRRXei5AM1mHoFPtGIH-DAFRL_BIIhQRx2aJ0K5GDB44ShMhg8XqdysUpQ";  // ecodev.ch
$googleMapsKey = "ABQIAAAAsyuA7bSIBFzUGUhfQJbrIRSgySnZCpt_ZDIBPsG-wFu5a0oU6BSi8TdLywJh5fSfPHkead_uQZ4lSQ";  //yopyop.ch
//$googleMapsKey = "ABQIAAAAsyuA7bSIBFzUGUhfQJbrIRRSMAugEfcv_MMHwK_fk7DalYdq0xT3z4lmg7IJaljNWbgDs1DlyX9MgA";  // martouf.ch
//$googleMapsKey = "ABQIAAAAsyuA7bSIBFzUGUhfQJbrIRToDFy_CfwiqU-h2cB1nNY3fiLA3xRdox0xI5V4o-w9oEv54-aaGr5nyg"; // ou-est-la-girafe.ch
//$googleMapsKey = "ABQIAAAAsyuA7bSIBFzUGUhfQJbrIRRy64MGyowFDpaFJTB3c2JhdnHFKhTPMrcJQk0SIeJg2wEqC6jxUpspeQ"; // koudou.ch

//**********************************************************
// Default theme
//**********************************************************
// Si la chaine est vide: '' va inclure le fichier index.tpl
global $theme;

$theme = ''; // koudou, yop, girafe, menhir, chateau, basic, no

//**********************************************************
// Comment system
//**********************************************************
// Permet de choisir si l'on va utiliser le système de commentaire natif ou un système externe comme disqus.
global $commentSystem;
$commentSystem = 'yop'; // yop, disqus

$commentSystemOn = true; // est ce que le système de commentaire est activé

//**********************************************************
// Default homepage
//**********************************************************
// Permet de choisir le nom du controleur de la page par défaut (c'est souvent la page accueil.php)
// Souvent la page d'accueil est personnalisée à un site. Permet également de personnaliser celle-ci sans dépendre de la version standard.
global $defaultController;
$defaultController = 'accueil'; // accueil

//**********************************************************
// PDF Engine
//**********************************************************
// Détermine le moteur pdf utilisé: prince ou wkhtmltopdf
global $pdfEngine;

	$pdfEngine = 'wkhtmltopdf';
//	$pdfEngine = 'prince';
	
//	$prince = new Prince('/usr/local/prince-6.0r6-macosx/lib/prince/bin/prince');
//	$prince = new Prince('/usr/local/bin/prince');
//	$prince = new Prince('/usr/local/prince-7.0-macosx/lib/prince/bin/prince'); // sur le serveur en prod
//	$prince = new Prince('/usr/local/prince-7.1-macosx/lib/prince/bin/prince'); // en local
//	$prince = new Prince('/usr/local/prince-7.1-macosx/prince');  // utilisable en ligne de commande (prince --no-author-style -s http://www.princexml.com/howcome/2008/wikipedia/wiki2.css     http://fr.scoutwiki.org/Sarrasine_santiano -o ~/Desktop/sarrasine.pdf)
global $princeXmlPath;
global $wkhtmltopdfPath;

//$princeXmlPath = '/usr/local/prince-7.0-macosx/lib/prince/bin/prince'; // serveur en prod
$princeXmlPath = '/usr/local/prince-7.1-macosx/lib/prince/bin/prince'; // local
$wkhtmltopdfPath = '/usr/local/bin/wkhtmltopdf';

//**********************************************************
// custom URL
//**********************************************************
// Relie le nom de la ressources utilisée dans l'url et le controleur utilisé.
global $ressourcesSwitch;

$ressourcesSwitch = array();

// le nom dans l'url => le controleur a appeller

// controleurs de base lié à des manager
	$ressourcesSwitch['groupe'] = 'groupe';
	$ressourcesSwitch['photo'] = 'photo';
	$ressourcesSwitch['restriction'] = 'restriction';
	$ressourcesSwitch['evenement'] = 'evenement';
	$ressourcesSwitch['version'] = 'version';
	$ressourcesSwitch['document'] = 'document';
	$ressourcesSwitch['personne'] = 'personne';
	$ressourcesSwitch['statut'] = 'statut';
	$ressourcesSwitch['calendrier'] = 'calendrier';
	$ressourcesSwitch['commentaire'] = 'commentaire';
	$ressourcesSwitch['lieu'] = 'lieu';
	$ressourcesSwitch['historique'] = 'historique';
	$ressourcesSwitch['fichier'] = 'fichier';
	$ressourcesSwitch['objet'] = 'objet';
	$ressourcesSwitch['geoobjet'] = 'geoobjet';
	$ressourcesSwitch['reservation'] = 'reservation';
	$ressourcesSwitch['transaction'] = 'transaction';
	$ressourcesSwitch['meta'] = 'meta';
	$ressourcesSwitch['mesure'] = 'mesure';
	$ressourcesSwitch['notification'] = 'notification';

// controleur partageants des managers
	$ressourcesSwitch['accueil'] = 'accueil';
	$ressourcesSwitch['blog'] = 'blog';
	$ressourcesSwitch['geophoto'] = 'geophoto';
	$ressourcesSwitch['agenda'] = 'agenda';
	$ressourcesSwitch['event'] = 'event';
	$ressourcesSwitch['itineraire'] = 'itineraire';
	$ressourcesSwitch['utilisateur'] = 'utilisateur';
	$ressourcesSwitch['profile'] = 'profile';
	$ressourcesSwitch['profil'] = 'profile'; // en français !
	
// pluriels de quelques ressources
	$ressourcesSwitch['groupes'] = 'groupe';
	$ressourcesSwitch['photos'] = 'photo';
	$ressourcesSwitch['restrictions'] = 'restriction';
	$ressourcesSwitch['evenements'] = 'evenement';
	$ressourcesSwitch['versions'] = 'version';
	$ressourcesSwitch['documents'] = 'document';
	$ressourcesSwitch['personnes'] = 'personne';
	$ressourcesSwitch['statuts'] = 'statut';
	$ressourcesSwitch['calendriers'] = 'calendrier';
	$ressourcesSwitch['commentaires'] = 'commentaire';
	$ressourcesSwitch['lieux'] = 'lieu';
	$ressourcesSwitch['historiques'] = 'historique';
	$ressourcesSwitch['fichiers'] = 'fichier';
	$ressourcesSwitch['objets'] = 'objet';
	$ressourcesSwitch['reservations'] = 'reservation';
	$ressourcesSwitch['transactions'] = 'transaction';
	$ressourcesSwitch['metas'] = 'meta';
	$ressourcesSwitch['mesures'] = 'mesure';
	$ressourcesSwitch['geophotos'] = 'geophoto';
	$ressourcesSwitch['agendas'] = 'agenda';
	$ressourcesSwitch['events'] = 'event';
	$ressourcesSwitch['utilisateurs'] = 'utilisateur';
	$ressourcesSwitch['notifications'] = 'notification';
	
// 	Chaine de caractère à utiliser dans la balise title quand aucun nom de ressources n'est fourni. C'est le cas du nom du site brut: yopyop.ch
global $defaultTitle;
$defaultTitle = "Yoyop ! Partageons nos ressources !";
	
//**********************************************************
// Related documents
//**********************************************************
// Permet de choisir si l'on va utiliser l'affichage des documents liés ou non
global $relatedDocumentsSystemOn;

$relatedDocumentsSystemOn = true; // on affiche des documents

//**********************************************************
// Importation de statuts facebook
//**********************************************************
// l'url comporte une clé sensée rester secrète !
global $urlFacebookFriendFeed;
$urlFacebookFriendFeed = "http://www.facebook.com/feeds/friends_status.php?id=684590465&key=xxxxxxxxxx&format=rss20&flid=0";

// sur la page: http://www.facebook.com/feeds/friends_status.php
// on a le joli message suivant:
// This feed URL is no longer valid. Visit this page to find the new URL, if you have access, http://www.facebook.com/statusupdates/.
// "if you have access"... je ne suis pas certain de faire partie de cette catégorie de gens... je suis redirigé sur le home.. :(

//**********************************************************
// Importation de données météo
//**********************************************************
// l'url est dédiée à la mesure à neuchâtel
global $urlCvn;
$urlCvn = "http://www.cvn.ch/cms/meteoris/affichage/donneesXML.php";

//**********************************************************
// Initialisation du générateur de mot de passes aléatoires prononcables.
//**********************************************************
// ces mots sont utilisée pour générer des mots de passe prononcables. Ils doivent donc correspondres à la langue locale. Par défaut le français

global $motsPrononcables;
$motsPrononcables = array("bleu","blanc","rouge","jaune","vert","violet","affichera",
"chaine","genre","retourne","fonction","commentaire","lapin","renard","image",
"mathematique","aleatoire","hasard","source","chat","souris","chapeau","langue",
"arbre","generer","livre","supposon","tout","vecteur","construction","violon",
"flute","fuite","zebre","zoro","xylophone","deux","trois","quatre","cinq","sept"
,"huit","neuf","douze","treize","magnifique","magistral","malin","marrant","mature","merveilleux","minutieux","mignon","modeste","moral");

//**********************************************************
// Personnalisation de divers comportements
//**********************************************************
global $customOption;
$customOption = array();

// Choix de l'utilisation de shadowbox ou non pour l'affichage des images intégrées dans les pages via le popup
$customOption['shadowBox'] = true; // true, false
?>