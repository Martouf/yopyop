<?php
/*******************************************************************************************
 * Nom du fichier		: commentaireManager.php
 * Date					: 21 janvier 2009
 * modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@ecodev.ch
 * But de ce fichier	: Fourni toutes les méthodes utiles pour manipuler les commentaires
 *******************************************************************************************
 * Un commentaire est un objet qui peut être associé avec n'importe quelle ressource. Pour ce faire on a une table de liaison commentaire-element.
 * L'anti-spam est une priorité pour l'utilisation de commentaire. On utilise pour se protéger un système de ticket qui envoie des cookies caché dans des images.
 * Ce principe est basé sur la constatation que les moteurs de spam ne lisent pas les images pour éviter de faire trop de trafic.
 * 
 */

class commentaireManager {

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
	 * Retourne les infos sur TOUS les commentaires avec possibilité de filtrer ceux-ci.
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() champs un tableau contenant les champs voulus
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getCommentaires($champs=array('*'),$filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'commentaire',$clauses,$champs,$orderBy);
		return $this->connection->getAssocArrays($result);
	}
	
	/**
	 * Retourne les infos sur une commentaire dont l'id est passé en paramètre.
	 * Possibilité de choisir les champs voulu.
	 *
	 * @param array() champs un tableau contenant les champs voulus
	 * @return array() un tableau associatif (key=>value) contenant le résultat de la requête ou false si échec
	 * @param int l'id du commentaire
	 */
	function getCommentaire($id_commentaire,$champs=array('*')){
		$clauses['id_commentaire'] = $id_commentaire;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'commentaire',$clauses,$champs);
		return $this->connection->getAssocArray($result);
	}
	
	
	/*******************************************************************
	 * INSERT
	 *******************************************************************/
	
	/**
	 * Ajouter un commentaire
	 *
	 * @return l'id de l'élément ajouté
	 * @param string toutes les infos de le commentaire
	 */
	function insertCommentaire($nom,$description='',$id_auteur='1',$mail='',$url='',$evaluation='0'){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['id_auteur'] = $id_auteur;
		$champs['mail'] = $mail;
		$champs['url'] = $url;
		$champs['evaluation'] = $evaluation;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		
		// crée le nouvel enregistrement et obtient la clé
		$id_commentaire = $this->connection->insert($this->tablePrefix.'commentaire',$champs);
		
		return $id_commentaire;
	}
	

	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un commentaire
	 *
	 * @return boolean
	 * @param string tout ce que l'on veut modifier
	 */
	function updateCommentaire($id_commentaire,$nom,$description='',$id_auteur='1',$mail='',$url='',$evaluation='0'){
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['id_auteur'] = $id_auteur;
		$champs['mail'] = $mail;
		$champs['url'] = $url;
		$champs['evaluation'] = $evaluation;
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		
		$conditions['id_commentaire'] = $id_commentaire;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'commentaire',$champs,$conditions);
	}


	/*******************************************************************
	 * DELETE
	 *******************************************************************/
	
	/**
	 * supprime un commentaire
	 *
	 * @return true
	 * @param int id_commentaire, l'id de l'commentaire à supprimer
	 */
	function deleteCommentaire ($id_commentaire){
		$request = "DELETE FROM `".$this->tablePrefix."commentaire` WHERE `id_commentaire`='".$id_commentaire."' ";
		return $this->connection->query($request);
	}

	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/

	// Après la création et manipulation pur d'un objet commentaire tout seul, on va s'occuper de faire des fonction permettant de lier un commentaire à une ressources.
	
	/**
	 * Permet d'ajouter un commentaire a une ressource
	 * 
	 * @param id_commentaire => id du commentaire que l'on veut associer
	 * @param int $idElement => id de l'élément au quel on ajoute le commentaire
	 * @param string $tableElement => type de l'élément au quel on ajoute le mot clé (le nom de la table dans lequel il est, ce nom est ensuite converti en int)
	 *
	 * Si une liaison mot-clé élément identique existe déjà, ne la recrée pas.
	 * 
	 */
	function associerCommentaire($idCommentaire,$idElement, $tableElement='document',$evaluation='0'){
		
		// Correspondance entre une version texte et une version chiffrée des types d'objet.
		// la version texte est plus agréable à manipuler dans le code, mais la version chiffrée dans la base et 2 fois plus rapide pour faire les jointures.
		$typeObjet = array('personne'=>1,'document'=>2,'photo'=>3,'evenement'=>4,'calendrier'=>5,'lieu'=>6,'statut'=>7,'commentaire'=>8,'liste'=>9,'element'=>10,'restriction'=>11,'commentaire'=>12);
		$tableElement = $typeObjet[$tableElement];
		
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['id_commentaire'] = $idCommentaire;
		$champs['id_element'] = $idElement;
		$champs['table_element'] = $tableElement;
		$champs['evaluation'] = $evaluation;
		$champs['date_creation'] = $dateCourante;
		
		// crée le nouvel enregistrement
		$this->connection->insert($this->tablePrefix.'commentaire-element',$champs);
		
		return true;
	}

	
	/**
	 * Permet de supprimer l'association d'un commentaire à un élément
	 *
	 * @return boolean
	 * @param int $idElement => id de l'élément au quel on supprime le commentaire
	 * @param string $motcle => la chaine de caractère qui est le mot-clé !
	 * @param string $tableElement => type de l'élément au quel on supprime le mot clé (le nom de la table dans lequel il est)
	 *
	 * Cette fonction supprime les liaisons mot-clé éléments mais pas le commentaire. Il peut donc y avoir des mot-clés orphelins.
	 * (amélioration: cette fonction pourrait supprimer les motc-clés inutilisés!)
	 */
	function dissocierCommentaire($idCommentaire,$idElement, $tableElement='document'){
		
		// Correspondance entre une version texte et une version chiffrée des types d'objet.
		// la version texte est plus agréable à manipuler dans le code, mais la version chiffrée dans la base et 2 fois plus rapide pour faire les jointures.
		$typeObjet = array('personne'=>1,'document'=>2,'photo'=>3,'evenement'=>4,'calendrier'=>5,'lieu'=>6,'statut'=>7,'commentaire'=>8,'liste'=>9,'element'=>10,'restriction'=>11,'commentaire'=>12);
		$tableElement = $typeObjet[$tableElement];
		
		$query = "delete from `".$this->tablePrefix."commentaire-element` where id_element='".$idElement."' and id_commentaire='".$idCommentaire."' and table_element='".$tableElement."'";
		return $this->connection->query($query);
	}
	
	/**
	 * Permet d'obtenir un tableau avec tous les commentaires liés a un élément donné.
	 *
	 * @return array(array)  un tableau de tableau contenant le détail des commentaires d'un élément triés par ordre chronologique
	 * @param int  $id_element => l'id de l'élément cible
	 * @param string $table_element => le nom de la table dans lequel se trouve l'élément (son type)
	 * 
	 */
	function getCommentaireElement($id_element='', $table_element='document'){
		// Correspondance entre une version texte et une version chiffrée des types d'objet.
		// la version texte est plus agréable à manipuler dans le code, mais la version chiffrée dans la base et 2 fois plus rapide pour faire les jointures.
		$typeObjet = array('personne'=>1,'document'=>2,'photo'=>3,'evenement'=>4,'calendrier'=>5,'lieu'=>6,'statut'=>7,'commentaire'=>8,'liste'=>9,'element'=>10,'restriction'=>11,'commentaire'=>12);
		$table_element = $typeObjet[$table_element];
			
		$query = "select * from ".$this->tablePrefix."commentaire, `".$this->tablePrefix."commentaire-element` where ".$this->tablePrefix."commentaire.id_commentaire=`".$this->tablePrefix."commentaire-element`.id_commentaire and `".$this->tablePrefix."commentaire-element`.id_element='".$id_element."' and `".$this->tablePrefix."commentaire-element`.table_element='".$table_element."' order by ".$this->tablePrefix."commentaire.date_creation";
		$result = $this->connection->query($query);
		$liste = $this->connection->getAssocArrays($result);
		
		return $liste;
	}
	
	/**
	 * Permet d'obtenir l'id et le type de l'élément au quel est associé un commentaire
	 *
	 * @return array(array)  un tableau contenant l'id et le type de l'élément commenté
	 * @param int  $id_commentaire => l'id du commentaire
	 * 
	 */
	function getElementCommentaire($id_commentaire=''){
			
		$query = "select id_element, table_element from ".$this->tablePrefix."commentaire, `".$this->tablePrefix."commentaire-element` where ".$this->tablePrefix."commentaire.id_commentaire=`".$this->tablePrefix."commentaire-element`.id_commentaire and `".$this->tablePrefix."commentaire-element`.id_commentaire='".$id_commentaire."' order by ".$this->tablePrefix."commentaire.date_creation";
		$result = $this->connection->query($query);
		$tab = $this->connection->getRowArrays($result);

		// Correspondance entre une version texte et une version chiffrée des types d'objet.
		// la version texte est plus agréable à manipuler dans le code, mais la version chiffrée dans la base et 2 fois plus rapide pour faire les jointures.
		$typeObjet = array(1=>'personne',2=>'document',3=>'photo',4=>'evenement',5=>'calendrier',6=>'lieu',7=>'statut',8=>'commentaire',9=>'liste',10=>'element');
		
		$tab[1] = $typeObjet[$tab[1]];
		return $tab;
	}
	
	/**
	 * Permet d'obtenir le nombre de commenraire qui sont disponible pour un élément donné
	 *
	 * @return int => nb de commentaires disponibles
	 * @param int  $id_element => l'id de l'élément cible
	 * @param string $table_element => le nom de la table dans lequel se trouve l'élément (son type)
	 * 
	 */
	function getNbCommentaireElement($id_element='', $table_element='document'){
		// Correspondance entre une version texte et une version chiffrée des types d'objet.
		// la version texte est plus agréable à manipuler dans le code, mais la version chiffrée dans la base et 2 fois plus rapide pour faire les jointures.
		$typeObjet = array('personne'=>1,'document'=>2,'photo'=>3,'evenement'=>4,'calendrier'=>5,'lieu'=>6,'statut'=>7,'commentaire'=>8,'liste'=>9,'element'=>10,'restriction'=>11,'commentaire'=>12);
		$table_element = $typeObjet[$table_element];
			
		$query = "select count(".$this->tablePrefix."commentaire.id_commentaire) from ".$this->tablePrefix."commentaire, `".$this->tablePrefix."commentaire-element` where ".$this->tablePrefix."commentaire.id_commentaire=`".$this->tablePrefix."commentaire-element`.id_commentaire and `".$this->tablePrefix."commentaire-element`.id_element='".$id_element."' and `".$this->tablePrefix."commentaire-element`.table_element='".$table_element."'";
		$result = $this->connection->query($query);
		
		$tab = $this->connection->getRowArrays($result);
		
		return $tab[0];
	}
	
	//////////  gestion des tickets anti-spam ////////
	
	/**
	 * Ajoute un ticket dans la base de donnée.
	 * Le ticket est créé pour le nom de domaine courant et pour le temps actuel.
	 * 
	 * @return int tiket_id => l'id du ticket créé
	 */
	function addTicket(){
		$ticket_id = md5(time());
		$remote_addr = $_SERVER['REMOTE_ADDR'];
		
		$domain = $_SERVER['HTTP_HOST'];
		if (preg_match("/([^\.]+\.[a-z]{2,4})$/",$domain,$match)) {
		        $domain = $match[1]; // ne conserve que le domaine de second niveau (mondomaine.ch)
		}
		$domain = '.'.$domain; // notation ".mondomaine.ch" pour couvrir tous les sous-domaines
	
		$query = "insert into `".$this->tablePrefix."ticket` (ticket_id,domain,time,remote_ip,used) values ('".$ticket_id."','".$domain."',NOW(),'".$remote_addr."',0)";
		
		$this->connection->query($query);
		
		// retourne l'id du ticket histoire de le retrouver
		return $ticket_id;
	}
	
	/**
	 * Vérifie si un ticket existe pour l'id fourni
	 *
	 * @return array => un tableau contenant toutes les infos sur le ticket recherché
	 */
	function checkTicket($ticket_id){
		$query = "select ticket_id, domain, unix_timestamp(time) as time, remote_ip, used from `".$this->tablePrefix."ticket` where ticket_id='".$ticket_id."' and used=0";
        
		$result = $this->connection->query($query);
		$liste = $this->connection->getAssocArray($result);
		return $liste;
	}
	
	
	/**
	 * marque le ticket comme utilisé
	 *
	 * @param $ticket_id => l'id du ticket
	 */
	function putTicketToTrash($ticket_id){
		$query = "update `".$this->tablePrefix."ticket` set used=1 where ticket_id='".$ticket_id."'";
        
		$this->connection->query($query);
	}
	
    
	/**
	 * supprime les tickets plus vieux de 4h
	 *
	 */
	function ticketGarbageCollector(){
		$query = "delete from `".$this->tablePrefix."ticket` where ".time()."-unix_timestamp(time)>14400";
        
		$this->connection->query($query);
	}
	
}
?>
