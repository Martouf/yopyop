<?php
/*******************************************************************************************
 * Nom du fichier		: personneManager.php
 * Date					: 7 août 2008
 * Modif				: 1 nov 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Défini ce qu'est une personne
 *******************************************************************************************
 *  modification du modèle de la table et définition d'un préfixe pour les tables
 *
 */
class personneManager {

	/*******************************************************************
	 * PARAMETERS
	 *******************************************************************/
	protected $connection;
	protected $tablePrefix;
	

	/*******************************************************************
	 * CONSTRUCTOR
	 *******************************************************************/
	function __construct($connection){
		//connexion à la base de donnée
		$this->connection=$connection;
		$this->tablePrefix="yop_";  // prefix que l'on met devant le nom de chaque table. Permet de faire cohabiter plusieurs fois l'application dans la même base sql.
	}
	
	/*******************************************************************
	 * GET ..... les infos d'un élément en particulier, ou les infos de tous les éléments.
	 *******************************************************************/
	
	/**
	 * Retourne les infos sur UNE personne dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la personne
	 */
	function getPersonne($id_personne){
		$clauses['id_personne'] = $id_personne;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'personne',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUTE les personnes avec possibilité de filtrer celle-ci
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getPersonnes($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'personne',$clauses,array('*'),$orderBy);
		return $this->connection->getAssocArrays($result);
	}
	
	/*******************************************************************
	 * INSERT
	 *******************************************************************/

	/**
	 * Ajouter un élément
	 * Seul le prénom est requis
	 *
	 * @return l'id du nouvel enregistrement
	 * @param string toute les infos de l'événement
	 */
	function insertPersonne($prenom,$nom='',$surnom='',$description='',$date_naissance='',$photo='',$motDePasse='',$rue='',$npa='',$lieu='',$pays='',$tel='',$email='',$rang='',$url='',$fortune='0',$evaluation='0'){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['prenom'] = $prenom;
		$champs['nom'] = $nom;
		$champs['surnom'] = $surnom;
		$champs['description'] = $description;
		$champs['date_naissance'] = $date_naissance."  00:00:00";
		$champs['photo'] = $photo;
		$champs['mot_de_passe'] = md5($motDePasse);
		$champs['rue'] = $rue;
		$champs['npa'] = $npa;
		$champs['lieu'] = $lieu;
		$champs['pays'] = $pays;
		$champs['tel'] = $tel;
		$champs['email'] = $email;
		$champs['rang'] = $rang;
		$champs['url'] = $url;
		$champs['fortune'] = $fortune;
		$champs['evaluation'] = $evaluation;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		
		// crée le nouvel enregistrement et obtient la clé
		$id_personne = $this->connection->insert($this->tablePrefix.'personne',$champs);
		
		return $id_personne;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un personne
	 * Ne met à jour que les champs pour lesquels une nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos de la personne
	 */
	function updatePersonne($id_personne,$prenom,$nom,$surnom,$description,$date_naissance,$photo,$motDePasse,$rue,$npa,$lieu,$pays,$tel,$email,$rang,$url,$fortune,$evaluation){
				
		if(!empty($prenom)){ $champs['prenom'] = $prenom; }
		if(!empty($nom)){ $champs['nom'] = $nom; }
		if(!empty($surnom)){ $champs['surnom'] = $surnom; }
		if(!empty($description)){ $champs['description'] = $description; }
		if(!empty($date_naissance)){ $champs['date_naissance'] = $date_naissance; }
		if(!empty($photo)){ $champs['photo'] = $photo; }
		if(!empty($motDePasse)){ $champs['mot_de_passe'] = $motDePasse; }
		if(!empty($rue)){ $champs['rue'] = $rue; }
		if(!empty($npa)){ $champs['npa'] = $npa; }
		if(!empty($lieu)){ $champs['lieu'] = $lieu; }
		if(!empty($pays)){ $champs['pays'] = $pays; }
		if(!empty($tel)){ $champs['tel'] = $tel; }
		if(!empty($email)){ $champs['email'] = $email; }
		if(!empty($rang)){ $champs['rang'] = $rang; }
		if(!empty($url)){ $champs['url'] = $url; }
		if(!empty($fortune)){ $champs['fortune'] = $fortune; }
		if(!empty($evaluation)){ $champs['evaluation'] = $evaluation; }
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		
		$conditions['id_personne'] = $id_personne;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'personne',$champs,$conditions);
	}

//////////////  champs externes /////////
// 
// /*******************************************************************
//  * GET ..... les infos d'un élément en particulier, ou les infos de tous les éléments.
//  *******************************************************************/
// 
// /**
//  * Retourne les infos sur un champs en particulier pour une personne
//  *
//  * @return array() le tableau contenant le résultat de la requête ou false si échec
//  * @param int l'id de la personne
//  */
// function getChampPersonne($id_personne,$type){
// 	$clauses['id_personne'] = $id_personne;
// 	$clauses['type'] = $type;
// 	// select(table,clauses,fields)
// 	$result = $this->connection->select($this->tablePrefix.'champ',$clauses);
// 	return $this->connection->getAssocArray($result);
// }
// 
// /**
//  * Retourne les infos sur TOUS les champs associés à une personne
//  *
//  * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
//  * @param array() un tableau contenant les éventuels filtre à faire
//  * @param string le nom du champ orderby
//  */
// function getChampsPersonne($filtre = array(),$orderBy = ''){
// 	$clauses = $filtre;
// 	
// 	// select(table,clauses,fields,orderBy)
// 	$result = $this->connection->select($this->tablePrefix.'champ',$clauses,array('*'),$orderBy);
// 	return $this->connection->getAssocArrays($result);
// }
// 
// /*******************************************************************
//  * INSERT
//  *******************************************************************/
// 
// /**
//  * Ajouter un élément
//  *
//  * @return true
//  * @param string toutes les infos de l'événement
//  */
// function insertChamp($id_personne,$type,$label='',$valeur,$description='',$utile=''){
// 	$dateCourante = date('Y-m-d H:i:s',time());
// 	$champs['id_personne'] = $id_personne;
// 	$champs['type'] = $type;
// 	$champs['label'] = $label;
// 	$champs['valeur'] = $valeur;
// 	$champs['description'] = $description;
// 	$champs['utile'] = $utile;
// 	$champs['date_creation'] = $dateCourante;
// 	$champs['date_modification'] = $dateCourante;
// 	
// 	// crée le nouvel enregistrement et retourne l'id
// 	$id_champs = $this->connection->insert($this->tablePrefix.'champ',$champs);
// 	
// 	return $id_champs;
// }
// 
// /*******************************************************************
//  * UPDATE
//  *******************************************************************/
// 
// /**
//  * Met à jour un champ
//  * Ne met à jour que les champs pour lesquels une nouvelle valeur est fournie
//  *
//  * @return boolean
//  * @param string toutes les infos sur le champ
//  */
// function updateChamp($id_champ,$id_personne,$type,$label,$valeur,$description,$utile){
// 	
// 	if(!empty($id_champ)){ $champs['id_champ'] = $id_champ; }
// 	if(!empty($id_personne)){ $champs['id_personne'] = $id_personne; }
// 	if(!empty($type)){ $champs['type'] = $type; }
// 	if(!empty($label)){ $champs['label'] = $label; }
// 	if(!empty($valeur)){ $champs['valeur'] = $valeur; }
// 	if(!empty($description)){ $champs['description'] = $description; }
// 	if(!empty($utile)){ $champs['utile'] = $utile; }
// 	$champs['date_modification'] = date('Y-m-d H:i:s',time());
// 	
// 	$conditions['id_champ'] = $id_champ;
// 	
// 	// table, champs(array), conditions(array)
// 	return $this->connection->update($this->tablePrefix.'champ',$champs,$conditions);
// }



///// commun ////
	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime une personne et ses champs externes
	 *
	 * @return -
	 * @param int id de la personne à supprimer
	 */
	function deletePersonne ($id_personne){
		// // supprime les champs dans la table externe
		// 	$req = "DELETE FROM `champ` WHERE `id_personne`='".$id_personne."' ";
		// 	$this->connection->query($req);
		
		$request = "DELETE FROM `".$this->tablePrefix."personne` WHERE `id_personne`='".$id_personne."' ";
		return $this->connection->query($request);
	}
	// 
	// /**
	//  * supprime un champs
	//  *
	//  * @return -
	//  * @param int id du champ à supprimer
	//  */
	// function deleteChamp ($id_champ){
	// 	
	// 	$request = "DELETE FROM `".$this->tablePrefix."champ` WHERE `id_champ`='".$id_champ."' ";
	// 	return $this->connection->query($request);
	// }
	
	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/
	
	/* Fonction vérifiant si un couple pseudo-mot de passe existe dans les personnes.
	 * 
	 * @return: l'id de l'utilisateur ou '1' si il est inconnu !
	 * @param: $pseudo => le pseudo
	 * @param: $password => le mot de passe
	 * @param: $placeCookie => si true, place un cookie chez le client avec ses paramètres.
	 */
	function getIdUserFromLogin($pseudo,$motDePasse,$placeCookie=true){
		
		// on ne permet que les chiffres et les lettres dans le login mot de passe.. todo => changer l'expression régulière pour élargir au caractères spéciaux. (le _ fonctionne)
		if (preg_match('/^[a-z0-9_]+$/i',$pseudo) && preg_match('/^[a-z0-9_]+$/i',$motDePasse)) {
			
			$request = "select * from ".$this->tablePrefix."personne where surnom='".$pseudo."' and mot_de_passe=MD5('".$motDePasse."')";
			$resultat = $this->connection->query($request);			
						
			if ($resultat!=false) {
				$row = $this->connection->getAssocArray($resultat);

				$_SESSION['id_personne'] = $row['id_personne'];
				$_SESSION['pseudo'] = $row['surnom'];
				$_SESSION['rang'] = $row['rang'];
				$_SESSION['motDePasse'] = $motDePasse;  // on stoque le mot de passe pour pouvoir générer un cookie au moment de la génération du pdf.
				
				if ($placeCookie) {
					//Le pseudo et le mot de passe sont codés et mis dans un cookie longue durée. Ce qui permettra un authentification automatique.
					//le pseudo et le mot de passe sont concaténés séparés par un point et encodé en base 64. Ce qui donne par exemple toto.blurp = dG90by5ibHVycA==  Attention le = est codé %3D dans la base de cookie de safari!
					$chaine = $pseudo.".".$motDePasse;
					$chaine = base64_encode($chaine);

					setcookie("yopyop",$chaine,time()+60*60*24*90,"/"); // valable 90 jours
				}
				
				// historique => login ok
				$dateCourante = date('Y-m-d H:i:s',time());
				$champs['nom'] = $pseudo." est passé le login";
				$champs['url'] = $_SERVER['REQUEST_URI'];
				$champs['ip'] = $_SERVER['REMOTE_ADDR'];
				$champs['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
				$champs['date_creation'] = $dateCourante;
				$champs['date_modification'] = $dateCourante;

				// crée le nouvel enregistrement et obtient la clé
				$id_historique = $this->connection->insert($this->tablePrefix.'historique',$champs);
				
				// si le login foire !
			}else{
				$_SESSION['id_personne'] = '1';
				$_SESSION['pseudo'] = 'inconnu';
				$_SESSION['rang'] = '1000';
				$_SESSION['motDePasse'] = '';
				
				// historique login KO
				$dateCourante = date('Y-m-d H:i:s',time());
				$champs['nom'] = $pseudo." a échoué au login";
				$champs['url'] = $_SERVER['REQUEST_URI'];
				$champs['ip'] = $_SERVER['REMOTE_ADDR'];
				$champs['user_agent'] = $_SERVER['HTTP_USER_AGENT'];
				$champs['date_creation'] = $dateCourante;
				$champs['date_modification'] = $dateCourante;
				
				// crée le nouvel enregistrement et obtient la clé
				$id_historique = $this->connection->insert($this->tablePrefix.'historique',$champs);
			}
		}
		return $_SESSION['id_personne'];
	}
	
	/* Fonction retournant le pseudo d'une personne en fonction de son id
	 * 
	 * @return: le pseudo d'une personne
	 * @param: $idPersonne => l'id de la personne
	 */
	function getPseudo($idPersonne){
		$clauses['id_personne'] = $idPersonne;
		$champs = array('surnom');

		// select(table,clauses,fields, order by)
		$result = $this->connection->select($this->tablePrefix."personne",$clauses,$champs);
			$tab = $this->connection->getRowArrays($result);
			if(!empty($tab)){
				return $tab[0]; // retourne un vrai string et pas un tableau associatif
			}else{
				return '';
			}	
	}
	
	/* Fonction cherchant a identifier plein de gens via plein de moyen différent
	 * 
	 * @return: rien... fait juste que de créer et remplir des variable de session
	 * @param: $parametreUrl => comme ça le script peut voir tout ce qu'on lui donne à manger via l'url
	 */
	function getIdentity($parametreUrl){
		
		// Si l'id de l'utilisateur n'est pas déjà dans la session, tente de le déterminer.
		if(!isset($_SESSION['id_personne'])){

			// Tente de trouver si un cookie contenant le login/mot de passe existe
			if(isset($_COOKIE['yopyop'])){
				
				//login encodé à décoder
				$chaine = base64_decode($_COOKIE['yopyop']);
				$couple = explode(".",$chaine);
				$pseudo = $couple[0];
				$motDePasse = $couple[1];

				// va chercher l'id de la personne qui correspond au login mot de passe donné.
				// retourne '1' si le login échoue. (1 est l'id correspondant à un inconnu)
				// Place dans la session: id_personne, pseudo et rang.
				// créer un cookie de longue durée pour se réauthentifier.
				$this->getIdUserFromLogin($pseudo,$motDePasse);

			// tente de trouver le paramètre get: "baba" (comme... ali baba :P)
			// le parmètre baba peut contenir le même genre de chaine login/mot de passe que le cookie. Ce qui donne par exemple toto.blurp = "dG90by5ibHVycA=="
			// Anisi il est possible d'accéder à des flux atom avec une url du genre: http://yopyop.ch/document/lapin/liste.xml?baba=dG90by5ibHVycA==
			// Attention, avec curl tout va bien, avec rss menu aussi, mais netnewswire, ainsi que safari et firefox ne comprenne pas les paramètres dans une url avec des un .xml !! 
			}elseif (isset($parametreUrl['baba'])) {
				//login encodé à décoder
				$chaine = base64_decode($parametreUrl['baba']);
				$couple = explode(".",$chaine);
				$pseudo = $couple[0];
				$motDePasse = $couple[1];

				// va chercher l'id de la personne qui correspond au login mot de passe donné.
				// retourne '1' si le login échoue. (1 est l'id correspondant à un inconnu)
				// Place dans la session: id_personne, pseudo et rang.
				$this->getIdUserFromLogin($pseudo,$motDePasse,false); // ne crée pas de cookie
			}else{
				
				// si l'utilisateur est un inconnu, on lui donne l'id '1' qui correspond à un invité
				$_SESSION['id_personne'] = '1';
				$_SESSION['pseudo'] = 'inconnu';
				$_SESSION['rang'] = '1000';
				$_SESSION['motDePasse'] = '';
			}
		} // if session id_personne
		
		// si une session avec un utilisateur incconnu existe déjà, mais que l'on aimerait utiliser la clé!
		if (isset($parametreUrl['baba'])) {
			//login encodé à décoder
			$chaine = base64_decode($parametreUrl['baba']);
			$couple = explode(".",$chaine);
			$pseudo = $couple[0];
			$motDePasse = $couple[1];

			// va chercher l'id de la personne qui correspond au login mot de passe donné.
			// retourne '1' si le login échoue. (1 est l'id correspondant à un inconnu)
			// Place dans la session: id_personne, pseudo et rang.
			$this->getIdUserFromLogin($pseudo,$motDePasse,false); // ne crée pas de cookie
		}

	} // function getIdentity
	
	
	/* Fonction retournant une clé encodée formée du login et mot de passe
	 * 
	 * @return: string la clé encodée
	 *
	 */
	function getCle(){
		$login = $_SESSION['pseudo'];
		$motDePasse =	$_SESSION['motDePasse'];
		
		// Le pseudo et le mot de passe sont codés et mis dans un cookie longue durée. Ce qui permettra un authentification automatique. Il est possible également de fournir la clé directement dans l'url avec le paramètre &baba=...
		// le pseudo et le mot de passe sont concaténés séparés par un point et encodé en base 64. Ce qui donne par exemple toto.blurp = dG90by5ibHVycA==  Attention le = est codé %3D dans la base de cookie de safari!
		$cle = $login.".".$motDePasse;
		return base64_encode($cle);
	}
	
	
	///////////  fonctions pour gérer la monnaie ///////////////
	
	
	/* Fonction retournant la fortune d'une personne en fonction de son id
	 * 
	 * @return: la fortune d'une personne
	 * @param: $idPersonne => l'id de la personne
	 */
	function getFortune($idPersonne){
		$clauses['id_personne'] = $idPersonne;
		$champs = array('fortune');

		// select(table,clauses,fields, order by)
		$result = $this->connection->select($this->tablePrefix."personne",$clauses,$champs);
			$tab = $this->connection->getRowArrays($result);
			if(!empty($tab)){
				return $tab[0]; // retourne un vrai string et pas un tableau associatif
			}else{
				return '';
			}	
	}
	
	/* Fonction ajoutant une somme à la fortune d'une personne en fonction de son id
	 * 
	 * @return: la fortune d'une personne
	 * @param: $idPersonne => l'id de la personne
	 */
	function augmenteFortune($idPersonne,$montant){
		$champs = array();
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		$conditions['id_personne'] = $id_personne;
		
		$montantActuel = $this->getFortune($idPersonne);
		
		// nouveau solde
		$champs['fortune'] = $montantActuel + $montant;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'personne',$champs,$conditions);
	}
	
	/* Fonction diminuant d'unsomme à la fortune d'une personne en fonction de son id
	 * 
	 * @return: la fortune d'une personne
	 * @param: $idPersonne => l'id de la personne
	 */
	function diminueFortune($idPersonne,$montant){
		$champs = array();
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		$conditions['id_personne'] = $id_personne;
		
		$montantActuel = $this->getFortune($idPersonne);
		
		// nouveau solde
		$champs['fortune'] = $montantActuel - $montant;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'personne',$champs,$conditions);
	}
	
	/******************************************************************************/
	/*                                                                            */
	/*                       __        ____                                       */
	/*                 ___  / /  ___  / __/__  __ _____________ ___               */
	/*                / _ \/ _ \/ _ \_\ \/ _ \/ // / __/ __/ -_|_-<               */
	/*               / .__/_//_/ .__/___/\___/\_,_/_/  \__/\__/___/               */
	/*              /_/       /_/                                                 */
	/*                                                                            */
	/*                                                                            */
	/******************************************************************************/
	/*                                                                            */
	/* Titre          : Génération de mot de passe prononçable facile à retenir...*/
	/*                                                                            */
	/* URL            : http://www.phpsources.org/scripts555-PHP.htm              */
	/* Auteur         : mercier133                                                */
	/* Date édition   : 16 Jan 2010                                               */
	/* Website auteur : http://www.servicesgratis.net                             */
	/*                                                                            */
	/******************************************************************************/
	// adaptation martouf le 13 juillet 2011 pour ajouter un peu de hasard au tableau de sons de base:
	// - ajout d'un nombre aléatoire à la fin du mot
	// - ajout du nom du serveur local
	// va chercher la base de mot-clés dans le fichier de config si elle existe
	
	/* 
	 * @return: string le mot de passe
	 */
	static function generatePassword($listeMots){
		
		// utilise la liste du fichier de config si elle est présente.
		if (empty($listeMots)) {
			//Liste de mots, pensez à choisir des mots avec des sons qui se pronnoncent facilement !
			$mots = array("bleu","blanc","rouge","jaune","vert","violet","affichera",
				"chaine","genre","retourne","fonction","commentaire","lapin","renard","image",
				"mathematique","aleatoire","hasard","source","chat","souris","chapeau","langue",
				"arbre","generer","livre","supposon","tout","vecteur","construction","violon",
				"flute","fuite","zebre","zoro","xylophone","deux","trois","quatre","cinq","sept"
				,"huit","neuf","douze","treize","magnifique","magistral","malin","marrant","mature","merveilleux","minutieux","mignon","modeste","moral");
		}else{
			$mots = $listeMots;
		}
		
	$mots[] = $_SERVER['SERVER_NAME'];

	    //Prononcabilité : 
	    $p = 1; 
	// c'est le nombre de lettre commune qu'il prendra en compte pour assembler 2
	// mots. 1 est conseillé, 2 risque de donner de temps en temps le même mot (sauf
	// si la liste de $mots est longue et variée). 3,4... est à éviter !


	    $m1 = $mots[rand(0,count($mots)-1)];
	    $result=substr($m1,0,rand(2,strlen($m1)-1));

	    for($i=0;$i<rand(3,4);$i++){ //boucle d'initialisation
	        $pasOk=true;
	        $x =0;    
	        while($pasOk && $x<100){

	            $m = $mots[rand(0,count($mots)-1)];
	            while($m==$m1){
	                $m = $mots[rand(0,count($mots)-1)];
	            }

	            if(eregi(substr($result,-$p),$m)){
	                $pasOk=false;
	                $m2 = split(substr($result,-1),$m);
	                $result .= substr($m2[1],0,rand(2,strlen($m2[1])-1));
	            }
	            $x++;
	        } if($x==100){ return personneManager::generatePassword(array());} // utilise la liste par défaut
	//si on n'y arrive pas on réessaye depuis le début ;)
	    }
	    if(strlen($result)<6) return personneManager::generatePassword(array()); // utilise la liste par défaut
		if(strlen($result)>10) return personneManager::generatePassword(array()); // utilise la liste par défaut

		$nbAlea = rand(0,99); // génère un nombre aléatoire entre 0 et 99
	    return $result.$nbAlea;
	}
	
} // personneManager
?>
