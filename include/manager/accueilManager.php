<?php
/*******************************************************************************************
 * Nom du fichier		: accueilManager.php
 * Date					: 19 décembre 2008
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Fournir des fonctions utile pour obtenir les infos d'une page d'accueil
 *******************************************************************************************
 * La page d'accueil est composé de plusieurs boites qui peuvent avoir des contenu différents.
 * Cette classe propose les méthodes pour obtenir les contenus de ces boites.
 * Ex de contenu:
 * - Aperçu de 3 documents qui correspondent aux tags fourni
 * - Aperçu (champ description) des trois derniers documents publié... ou mis à jour.
 * - Aperçu de la dernière galerie de photo
 * - contenu d'un flux atom dont l'url est fournie
 */
class accueilManager {

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
	 * spécifique à la classe
	 *******************************************************************/
	
	/* Fonction obtenant un nombre donné de documents 
	 * 
	 * @return: l'id de l'utilisateur ou '1' si il est inconnu !
	 * @param: $pseudo => le pseudo
	 * @param: $password => le mot de passe
	 * @param: $placeCookie => si true, place un cookie chez le client avec ses paramètres.
	 */
	function getIdUserFromLogin($pseudo,$motDePasse,$placeCookie=true){
		
		if (preg_match('/^[a-z0-9]+$/i',$pseudo) && preg_match('/^[a-z0-9]+$/i',$motDePasse)) {
			
			$request = "select * from ".$this->tablePrefix."accueil where surnom='".$pseudo."' and mot_de_passe=MD5('".$motDePasse."')";
			$resultat = $this->connection->query($request);			
						
			if ($resultat!=false) {
				$row = $this->connection->getAssocArray($resultat);

				$_SESSION['id_accueil'] = $row['id_accueil'];
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
				
				// si le login foire !
			}else{
				$_SESSION['id_accueil'] = '1';
				$_SESSION['pseudo'] = 'inconnu';
				$_SESSION['rang'] = '1000';
				$_SESSION['motDePasse'] = '';
			}
		}
	}
	
	
} // accueilManager
?>
