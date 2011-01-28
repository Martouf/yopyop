<?php
/*******************************************************************************************
 * Nom du fichier     : init.php
 * Date		     	  : 22 décembre 2007
 * modifs			  : novmbre 2008, gestion de l'identification des utilisateurs.
 * @author	     	  : Mathieu Despont
 * Adresse E-mail     : mathieu@marfaux.ch
 * But de ce fichier  : Initialisation de l'application
 *******************************************************************************************
 * Initialise les variables, charge les managers, initialise smarty, détecte la langue préférée
 * 7.3.8 modif: la classe attribuée à l'application est supprimée. Le fichier s'appelle application. Toute les fonctions sont statiques pas besoin de classe.
 *       require_once est supprimé.. require suffit.. et il est beaucoup plus rapide !
 * 
 */


ob_start("ob_gzhandler");
session_start();
header('Content-Type: text/html; charset=UTF-8');
date_default_timezone_set('Europe/Zurich');

class Init{
	private $connection;

	function __construct(){
		// langue préférée du navigateur. Si celle-ci est fournie.
		if (!empty($_SERVER['HTTP_ACCEPT_LANGUAGE'])) {
			$langPref = substr($_SERVER['HTTP_ACCEPT_LANGUAGE'], 0, 2);
		}else{
			$langPref = "fr"; // si rien de défini on choisi le fr
		}
		
		// si la langue est fournie par GET crée un cookie et une constante LANG qui la contient
		if(isset($_GET['lang']) && preg_match("/^[a-z]{2}$/",$_GET['lang'])){
			define('LANG',$_GET['lang']);
			setcookie('LANG',$_GET['lang'],time()+60*60*24*90,'/');
		}else{
			// si rien est fourni, mais qu'un cookie existe avec une langue, la constante LANG contient la langue
			if (isset($_COOKIE['LANG']) && preg_match("/^[a-z]{2}$/",$_COOKIE['LANG'])) {
				define('LANG',$_COOKIE['LANG']);
			} else {
				// premier passage: pas de cookie => attribue la langue préférée du navigateur
			//	if (isset($langPref) && (($langPref=='fr')or($langPref=='de')or($langPref=='it')or($langPref=='es')or($langPref=='zh'))) {   // liste des langues supportées par le site
				if (isset($langPref) && ($langPref=='fr')) {   // se trouve dans les langues supportée par le site
					define('LANG',$langPref);
					setcookie('LANG',$langPref,time()+60*60*24*90,'/');
				}else{ // si rien de tangible ne permet de trouver la langue préférée, fourni le site en francais
					setcookie('LANG','fr',time()+60*60*24*90,'/');
					define('LANG','fr');				
				}
			}
		}	
		
		require('config.php'); // de la base de donnée
		$path = dirname(dirname(__FILE__)) . "/";

		// inclu le fichier de langue
		require($path.'l10n/lang_'.LANG.'.php');

		$this->includeAllFiles($path);
		$this->initGPVariables();
		$this->connection = self::getConnection($DBHost, $DBUser, $DBPwd, $DBName);
		$this->includeAllObjects($path);
		$this->getUrlParams();   // va chercher les paramètres get qui sont bloqués par la réécriture d'url. Les place dans le tableau: $parametreUrl
		
	}

	/**
	 * Make sure the GET / POST variables are OK.
	 *
	 * @return void
	 */
	function initGPVariables(){
		array_walk($_REQUEST, array('self','processGPVariables'));
		array_walk($_POST, array('self','processGPVariables'));
		array_walk($_GET, array('self','processGPVariables'));
	}

	/**
	 * Process the GET / POST variables.
	 *
	 * @return void
	 */
	function processGPVariables(&$str){
		$str = trim($str);
	}

	function includeAllFiles($path) {
		//inclu quelques biliothèque
		require ($path.'lib/application.php');
		require ($path.'lib/dbMysql.php');
		require ($path.'lib/parse_ics.php');  // fonction qui parse les fichiers calendrier ics. Utilisé par evenementManager
		require ($path.'lib/smarty/Smarty.class.php');

		// inclu les fichiers de description des managers
		require ($path.'manager/groupeManager.php');
		require ($path.'manager/versionManager.php');
		require ($path.'manager/documentManager.php');
		require ($path.'manager/restrictionManager.php');
		require ($path.'manager/evenementManager.php');
		require ($path.'manager/photoManager.php');
		require ($path.'manager/personneManager.php');
		require ($path.'manager/statutManager.php');
		require ($path.'manager/calendrierManager.php');
		require ($path.'manager/commentaireManager.php');
		require ($path.'manager/historiqueManager.php');
		require ($path.'manager/fichierManager.php');
		require ($path.'manager/lieuManager.php');
		require ($path.'manager/objetManager.php');
		require ($path.'manager/reservationManager.php');
		require ($path.'manager/transactionManager.php');
		require ($path.'manager/metaManager.php');
	}

	function includeAllObjects($path) {
		global $groupeManager;
		global $photoManager;
		global $restrictionManager;
	 	global $evenementManager;
		global $versionManager;
		global $documentManager;
		global $personneManager;
		global $statutManager;
		global $calendrierManager;
		global $commentaireManager;
		global $lieuManager;
		global $historiqueManager;
		global $fichierManager;
		global $objetManager;
		global $reservationManager;
		global $transactionManager;
		global $metaManager;
		global $smarty;
		
		//instancie les objets
		$groupeManager = new groupeManager($this->connection);
		$photoManager = new photoManager($this->connection);
		$evenementManager = new evenementManager($this->connection);
		$calendrierManager = new calendrierManager($this->connection);
		$versionManager = new versionManager($this->connection);
		$personneManager = new personneManager($this->connection);
		$restrictionManager = new restrictionManager($this->connection);
		$statutManager = new statutManager($this->connection);
		$commentaireManager = new commentaireManager($this->connection);
		$lieuManager = new lieuManager($this->connection);
		$historiqueManager = new historiqueManager($this->connection);
		$fichierManager = new fichierManager($this->connection);
		$objetManager = new objetManager($this->connection);
		$reservationManager = new reservationManager($this->connection);
		$transactionManager = new transactionManager($this->connection);
		$metaManager = new metaManager($this->connection);
		$documentManager = new documentManager($this->connection,$versionManager,$personneManager,$groupeManager); // transmet les manager pour que le documentManager puisse les utiliser
		
		//instantiate and configure smarty
		$smarty = new Smarty;
		$smarty->compile_check = true;
		$smarty->debugging = false;
		$smarty->template_dir = $path.'tpl/templates';
		$smarty->compile_dir = $path.'tpl/templates_c';
		$smarty->cache_dir = $path.'tpl/cache';
		// $smarty->config_dir = '../includes/tpl/configs';
		$smarty->left_delimiter = '<!--{';
		$smarty->right_delimiter = '}-->';
	}

	function getConnection($DBHost, $DBUser, $DBPwd, $DBName) {
		$aConnection = new DBMysql($DBHost, $DBUser, $DBPwd);
		$aConnection->connect($DBName);
		return $aConnection;
	}
	
	// obtient les paramètres passé dans l'url. La réécriture d'url semble me cours-circuiter les paramètres GET d'ou cette fonction. (le ? ne passe pas la réécriture ?)
	function getUrlParams(){
		global $parametreUrl;  // tableau contenant les valeurs envoyées par get
		$parametreUrl = parseUrl($_SERVER['REQUEST_URI']);
	}
	
}
?>
