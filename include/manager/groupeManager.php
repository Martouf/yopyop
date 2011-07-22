<?php
/*******************************************************************************************
 * Nom du fichier		: groupeManager.php
 * Date					: 5 janvier 2008
 * modif				: 1 novembre 2008 ajout d'un préfixe pour les tables
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Fourni toutes les méthodes utiles pour manipuler les données d'un groupe
 *******************************************************************************************
 * Un groupe de type 1 est un tag normal. Une groupe de type 2 est un groupe associés à des restrictions.
 * pour tagguer un élément:
 *  http://yopyop.ch/utile/ajax/tag.php?type=personne&id=1&add=lapin
 *
 * Attention, il y a des type d'éléments qui sont défini dans un tableau qui se retrouve dans plusieurs fonctions:
 * 	$typeObjet = array('personne'=>1,'document'=>2,'photo'=>3,'evenement'=>4,'calendrier'=>5,'lieu'=>6,'statut'=>7,'commentaire'=>8,'liste'=>9,'restriction'=>11,'groupe'=>12,'fichier'=>13,'objet'=>14,'reservation'=>15,'transaction'=>16,'meta'=>17);
 * => si des novueaux type d'éléments à taguer sont créer, il ne faut pas oublier de les ajouter dans ce tableau.
 */

class groupeManager {

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
	 * Retourne les infos sur TOUS les groupes avec possibilité de filtrer ceux-ci.
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() champs un tableau contenant les champs voulus
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getGroupes($champs=array('*'),$filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'groupe',$clauses,$champs,$orderBy);
		return $this->connection->getAssocArrays($result);
	}
	
	/**
	 * Retourne les infos sur une groupe dont l'id est passé en paramètre.
	 * Possibilité de choisir les champs voulu.
	 *
	 * @param array() champs un tableau contenant les champs voulus
	 * @return array() un tableau associatif (key=>value) contenant le résultat de la requête ou false si échec
	 * @param int l'id du groupe
	 */
	function getGroupe($id_groupe,$champs=array('*')){
		$clauses['id_groupe'] = $id_groupe;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'groupe',$clauses,$champs);
		return $this->connection->getAssocArray($result);
	}
	
	
	/*******************************************************************
	 * INSERT
	 *******************************************************************/
	
	/**
	 * Ajouter un groupe
	 *
	 * @return l'id de l'élément ajouté
	 * @param string toutes les infos de le groupe
	 */
	function insertGroupe($nom,$description='',$type=''){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['type'] = $type;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		
		// crée le nouvel enregistrement et obtient la clé
		$id_groupe = $this->connection->insert($this->tablePrefix.'groupe',$champs);
		
		return $id_groupe;
	}
	

	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un groupe
	 *
	 * @return boolean
	 * @param string toutes à modifier
	 */
	function updateGroupe($id_groupe,$nom,$description,$type){
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['type'] = $type;
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		
		$conditions['id_groupe'] = $id_groupe;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'groupe',$champs,$conditions);
	}


	/*******************************************************************
	 * DELETE
	 *******************************************************************/
	
	/**
	 * supprime un groupe
	 *
	 * @return true
	 * @param int id_groupe, l'id de l'groupe à supprimer
	 */
	function deleteGroupe ($id_groupe){
		$request = "DELETE FROM `".$this->tablePrefix."groupe` WHERE `id_groupe`='".$id_groupe."' ";
		return $this->connection->query($request);
	}

	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/

	// Toute ce qui est relatif à la gestion des mots-clés (qui sont des groupes !!)

	
	/**
	 * Permet d'ajouter un mot clé à un élément.
	 * Un mot-clé est un groupe dont le champ type=1
	 * Les tags associés à des restrictions sont de type 2, uniquement pour qu'ils n'apparaissent pas dans certaines situations comme les nuages de mots.
	 * 
	 * @param int $idElement => id de l'élément au quel on ajoute le mot clé
	 * @param string $motcle => la chaine de caractère qui est le mot-clé !
	 * @param string $tableElement => type de l'élément au quel on ajoute le mot clé (le nom de la table dans lequel il est)
	 *
	 * Si une liaison mot-clé élément identique existe déjà, ne la recrée pas.
	 * 
	 */
	function ajouteMotCle($idElement='', $motCle='', $tableElement='evenement'){
		// recherche si un motclé avec le même nom existe déjà ?
		$idMotCle = $this->getIdMotCle($motCle);
		
		// si idMotCle est nul, c'est que le motclé n'existe pas donc le crée 
		if (empty($idMotCle)) {
			// crée un nouveau groupe de type 1 (nom, description, type)
			$idMotCle = $this->insertGroupe($motCle,'','1');
		}
		
		// Correspondance entre une version texte et une version chiffrée des types d'element.
		// la version texte est plus agréable à manipuler dans le code, mais la version chiffrée dans la base et 2 fois plus rapide pour faire les jointures.
		$typeObjet = array('personne'=>1,'document'=>2,'photo'=>3,'evenement'=>4,'calendrier'=>5,'lieu'=>6,'statut'=>7,'commentaire'=>8,'liste'=>9,'element'=>10,'restriction'=>11,'groupe'=>12,'fichier'=>13,'objet'=>14,'reservation'=>15,'transaction'=>16,'meta'=>17);
	
		$tableElement = $typeObjet[$tableElement];
		
		// teste si une liaison mot-clé<->élement existe déjà ?			
		$clauses['id_element'] = $idElement;
		$clauses['id_groupe'] = $idMotCle;
		$clauses['table_element'] = $tableElement;
		$champs = array('*');

		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'groupe-element',$clauses,$champs);
		$tab = $this->connection->getRowArrays($result);
		
		$champs = array(); // réinistialisation après la requête précédente
		// si la liaison n'existe pas déjà, la crée
		if(empty($tab)){
			$champs['id_groupe'] = $idMotCle;
			$champs['id_element'] = $idElement;
			$champs['table_element'] = $tableElement;
			$champs['type'] = '1'; // aussi !
			$champs['date_creation'] = date('Y-m-d H:i:s',time());

			$this->connection->insert($this->tablePrefix.'groupe-element',$champs);
		}
	}

	
	/**
	 * Permet de supprimer l'association d'un mot clé à un élément
	 * Un mot-clé est un groupe dont le champ type=1
	 *
	 * @return boolean
	 * @param int $idElement => id de l'élément au quel on supprime le mot clé
	 * @param string $motcle => la chaine de caractère qui est le mot-clé !
	 * @param string $tableElement => type de l'élément au quel on supprime le mot clé (le nom de la table dans lequel il est)
	 *
	 * Cette fonction supprime les liaisons mot-clé éléments mais pas le groupe. Il peut donc y avoir des mot-clés orphelins.
	 * (amélioration: cette fonction pourrait supprimer les motc-clés inutilisés!)
	 */
	function supprimerMotCle($idElement='', $motCle='', $tableElement='evenement'){
		// obtient l'id du mot-clé
		$id_groupe = $this->getIdMotCle($motCle);
		
		// Correspondance entre une version texte et une version chiffrée des types d'objet.
		// la version texte est plus agréable à manipuler dans le code, mais la version chiffrée dans la base et 2 fois plus rapide pour faire les jointures.
		$typeObjet = array('personne'=>1,'document'=>2,'photo'=>3,'evenement'=>4,'calendrier'=>5,'lieu'=>6,'statut'=>7,'commentaire'=>8,'liste'=>9,'element'=>10,'restriction'=>11,'groupe'=>12,'fichier'=>13,'objet'=>14,'reservation'=>15,'transaction'=>16,'meta'=>17);
		
		$tableElement = $typeObjet[$tableElement];
		
		$query = "delete from `".$this->tablePrefix."groupe-element` where id_element='".$idElement."' and id_groupe='".$id_groupe."' and table_element='".$tableElement."'";
		return $this->connection->query($query);
	}

	/**
	 * Obtient l'id d'un mot-clé à partir de son nom
	 * Un mot-clé est un groupe dont le champ type=1
	 *
	 * @return int id_groupe, l'id du groupe de type 1 qui correspond au nom fourni
	 * @param string $motCle => le nom du motclé dont on veut l'id
	 * 
	 */
	function getIdMotCle($motCle=''){
		// obtient l'id du mot-clé
	//	$clauses['type'] = '1';  // pas besoin de discriminier les types
		$clauses['nom'] = $motCle;
		$champs = array('id_groupe');
		
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'groupe',$clauses,$champs);
		$tab = $this->connection->getRowArrays($result);
		if(!empty($tab)){
			return $tab[0]; // retourne un vrai nombre et pas un tableau associatif
		}else{
			return '';
		}
	}
	
	
	/**
	 * Permet d'obtenir un tableau avec tous les mots-clés lié a un élément donné. C'est la bibliothèque de mot-clé de l'élément.
	 * Le tableau est composé du mot-clé ainsi que du nombre de fois que le mot clé a été utilisé.
	 *
	 * @return array  un tableau avec tous les motclés.  motClé => nombre d'occurences
	 * @param int  $id_element => l'id de l'élément cible
	 * @param string $table_element => le nom de la table dans lequel se trouve l'élément (son type)
	 * 
	 */
	function getMotCleElement($id_element='', $table_element='evenement'){
		// Correspondance entre une version texte et une version chiffrée des types d'objet.
		// la version texte est plus agréable à manipuler dans le code, mais la version chiffrée dans la base et 2 fois plus rapide pour faire les jointures.
		$typeObjet = array('personne'=>1,'document'=>2,'photo'=>3,'evenement'=>4,'calendrier'=>5,'lieu'=>6,'statut'=>7,'commentaire'=>8,'liste'=>9,'element'=>10,'restriction'=>11,'groupe'=>12,'fichier'=>13,'objet'=>14,'reservation'=>15,'transaction'=>16,'meta'=>17);
		$table_element = $typeObjet[$table_element];
		
		// requête qui ne sélectionne que les groupes de type 1 !
		//$query = "select nom, count(id_element) from ".$this->tablePrefix."groupe, `".$this->tablePrefix."groupe-element` where ".$this->tablePrefix."groupe.id_groupe=`".$this->tablePrefix."groupe-element`.id_groupe and `".$this->tablePrefix."groupe-element`.id_element='".$id_element."' and `".$this->tablePrefix."groupe-element`.table_element='".$table_element."' and ".$this->tablePrefix."groupe.type='1' group by ".$this->tablePrefix."groupe.id_groupe";
		
		$query = "select nom, count(id_element) from ".$this->tablePrefix."groupe, `".$this->tablePrefix."groupe-element` where ".$this->tablePrefix."groupe.id_groupe=`".$this->tablePrefix."groupe-element`.id_groupe and `".$this->tablePrefix."groupe-element`.id_element='".$id_element."' and `".$this->tablePrefix."groupe-element`.table_element='".$table_element."' group by ".$this->tablePrefix."groupe.id_groupe";
		$result = $this->connection->query($query);
		$liste = $this->connection->getAssocArrays($result);
		
		if(!empty($liste)){
			$listeMotsCles = array();
			foreach ($liste as $key => $entree) {
				$motCle = $entree['nom'];
				$occurences = $entree['count(id_element)'];
			
				// construit le tableau qui stock les occurences en fonction du mot-clé
				$listeMotsCles[$motCle] = $occurences;
			}
			return $listeMotsCles;
		}else{
			return array();  // si aucun motclé n'est associé à cet élément.
		}
	}
	
	/**
	 * Permet d'obtenir un tableau avec tous les mots-clés. C'est la bibliothèque globale de mot-clé.
	 * Le tableau est composé du mot-clé ainsi que du nombre de fois que le mot clé a été utilisé. Cette fonction est utilisée pour faire des nuages de mots.
	 * Les groupes de type 2 qui sont les groupes associés à des restrictions ne sont pas fourni.
	 *
	 * @param string type, nom de la table du type d'événement pour lesquels on veut les tags associés. ainsi on peut avoir que les tag des photos, ou des articles.. ou événement.
	 * @return array  un tableau avec tous les motclés.  motClé => nombre d'occurences
	 * 
	 */
	function getMotCle($type="evenement"){
		// Correspondance entre une version texte et une version chiffrée des types d'objet.
		// la version texte est plus agréable à manipuler dans le code, mais la version chiffrée dans la base et 2 fois plus rapide pour faire les jointures.
		$typeObjet = array('personne'=>1,'document'=>2,'photo'=>3,'evenement'=>4,'calendrier'=>5,'lieu'=>6,'statut'=>7,'commentaire'=>8,'liste'=>9,'element'=>10,'restriction'=>11,'groupe'=>12,'fichier'=>13,'objet'=>14,'reservation'=>15,'transaction'=>16,'meta'=>17);
		
		$type = $typeObjet[$type];
		
		// la requête ne prend en compte que les groupes de type 1. Donc les vrais tags. Les groupes de types 2 sont des groupes destinés à être utilisé comme groupe de restriction d'accès. On ne veut donc pas les montrer dans le nuage de mots.
		$query = "select nom, count(id_element) from ".$this->tablePrefix."groupe, `".$this->tablePrefix."groupe-element` where ".$this->tablePrefix."groupe.id_groupe=`".$this->tablePrefix."groupe-element`.id_groupe and ".$this->tablePrefix."groupe.type='1' and table_element='".$type."' group by ".$this->tablePrefix."groupe.id_groupe order by nom";  // order by nom (au besoin)
		$result = $this->connection->query($query);
		$liste = $this->connection->getAssocArrays($result);
		
		if(!empty($liste)){
			$listeMotsCles = array();
			foreach ($liste as $key => $entree) {
				$motCle = $entree['nom'];
				$occurences = $entree['count(id_element)'];
			
				// construit le tableau qui stock les occurences en fonction du mot-clé
				$listeMotsCles[$motCle] = $occurences;
			}
			return $listeMotsCles;
		}else{
			return array();  // si aucun motclé n'est associé à cet élément.
		}
	}
	
	
	/**
	 * Permet d'obtenir un tableau avec tous les mots-clés associé à un type d'élément. C'est la bibliothèque des tag utilisés pour les articles.. ou les événement, ou les personne.. etc..
	 *
	 * @param string type, nom de la table du type d'événement pour lesquels on veut les tags associés. ainsi on peut avoir que les tag des photos, ou des articles.. ou événement.
	 * @return array  un tableau avec tous les motclés.  id => nom
	 * 
	 */
	function getMotCleParTypeElement($type="evenement"){
		// Correspondance entre une version texte et une version chiffrée des types d'objet.
		// la version texte est plus agréable à manipuler dans le code, mais la version chiffrée dans la base et 2 fois plus rapide pour faire les jointures.
		$typeObjet = array('personne'=>1,'document'=>2,'photo'=>3,'evenement'=>4,'calendrier'=>5,'lieu'=>6,'statut'=>7,'commentaire'=>8,'liste'=>9,'element'=>10,'restriction'=>11,'groupe'=>12,'fichier'=>13,'objet'=>14,'reservation'=>15,'transaction'=>16,'meta'=>17);
		
		$type = $typeObjet[$type];
		
		$query = "select ".$this->tablePrefix."groupe.id_groupe, nom from ".$this->tablePrefix."groupe, `".$this->tablePrefix."groupe-element` where ".$this->tablePrefix."groupe.id_groupe=`".$this->tablePrefix."groupe-element`.id_groupe and table_element='".$type."' group by ".$this->tablePrefix."groupe.id_groupe";  // order by nom (au besoin)
		$result = $this->connection->query($query);
		$liste = $this->connection->getAssocArrays($result);
		
		if(!empty($liste)){
			$listeMotsCles = array();
			foreach ($liste as $key => $entree) {
				$motCle = $entree['nom'];
				$id_groupe = $entree['id_groupe'];
			
				// construit le tableau qui stock les occurences en fonction du mot-clé
				$listeMotsCles[$id_groupe] = $motCle;
			}
			return $listeMotsCles;
		}else{
			return array();  // si aucun motclé n'est associé à cet élément.
		}
	}
	
	

	/**
	 * Permet d'obtenir un tableau de tableau avec tous les éléments qui sont assoccié à un mot-clé
	 * Le tableau est composé d'un tableau qui contient l'id et la table de l'élément.
	 *
	 * @return array  array  un tableau avec tous éléments => id_element, table_element
	 * @param int  $id_groupe => l'id du groupe du mot-clé
	 * 
	 */
	function getElementsParMotCle($id_groupe){
		$query = "select id_element, table_element from `".$this->tablePrefix."groupe-element`, ".$this->tablePrefix."groupe where ".$this->tablePrefix."groupe.id_groupe=`".$this->tablePrefix."groupe-element`.id_groupe and `".$this->tablePrefix."groupe-element`.id_groupe='".$id_groupe."'"; //  and ".$this->tablePrefix."groupe.type='1'
		$result = $this->connection->query($query);
		return $this->connection->getAssocArrays($result);
	}
	
	/**
	 * Permet d'obtenir le nom d'un élément, en fonction de son id et de son type (table)
	 *
	 * @return string le nom de l'élément
	 * @param int  $id_element => l'id de l'élément
	 * @param string  $table_element => le nom de la table dans laquelle se trouve l'élément
	 * 
	 */
	function getNomElement($id_element,$table_element='evenement'){
		$clauses['id_'.$table_element] = $id_element;
		$champs = array('nom');

		// select(table,clauses,fields, order by)
		$result = $this->connection->select($this->tablePrefix.$table_element,$clauses,$champs);
		$tab = $this->connection->getRowArrays($result);
		if(!empty($tab)){
			return $tab[0]; // retourne un vrai string et pas un tableau associatif
		}else{
			return '';
		}
	}
	
	/**
	 * Permet d'obtenir l'id de tous les éléments d'un type donné qui sont associés à tous les éléments de la liste de tag fournie.
	 *
	 * @return array un tableau de tous les id des éléments qui correspondent
	 * @param array $tags tableau qui contient la liste des tags
	 * @param string  $table_element => le nom de la table dans laquelle se trouve l'élément
	 * 
	 */
	function getElementByTags($tags=array(),$table_element="evenement"){
		// Correspondance entre une version texte et une version chiffrée des types d'objet.
		// la version texte est plus agréable à manipuler dans le code, mais la version chiffrée dans la base et 2 fois plus rapide pour faire les jointures.
		$typeObjet = array('personne'=>1,'document'=>2,'photo'=>3,'evenement'=>4,'calendrier'=>5,'lieu'=>6,'statut'=>7,'commentaire'=>8,'liste'=>9,'element'=>10,'restriction'=>11,'groupe'=>12,'fichier'=>13,'objet'=>14,'reservation'=>15,'transaction'=>16,'meta'=>17);
		
		$table_element = $typeObjet[$table_element];
				
		$listeElements = array(); // tableau qui contient la liste des id des éléments correspondants à TOUS les tags
		foreach ($tags as $key => $tag) {

			$elementsGroupe =  $this->getElementsParMotCle($this->getIdMotCle($tag));  // pour optimiser on pourrait peut être faire le teste de type dans cette fonction plutôt que de faire un foreach juste après???
			
			// liste des éléments 
			$elements = array();
			foreach ($elementsGroupe as $key => $element) {
				if ($element['table_element']==$table_element) {  // filtre par type
					$elements[] = $element['id_element'];
				}
			}
			
			if (!empty($listeElements)) {
				$listeElements = array_intersect($elements,$listeElements);
			}else{
				$listeElements = $elements;
			}
		}// pour chaque tag
		
		return $listeElements;
	}
	
	/**
	 * Retourne la liste des groupes dans lesquels se trouve une personne.
	 *
	 * @return array() un tableau contenant la liste des id des groupes dans lesquels se trouve une personne.
	 * @param int id de la personne
	 */
	function getGroupeUtilisateur($idVisiteur){
		
		// Obtient la liste des groupes dans lesquels se trouve le visiteur. (la requête ne regarde pas le type qui est toujours =1 si ceci devait changer, revoir la requête)
		$query = "select id_groupe from `".$this->tablePrefix."groupe-element` where `".$this->tablePrefix."groupe-element`.id_element='".$idVisiteur."' and `".$this->tablePrefix."groupe-element`.table_element='1'"; // 1 => type ressource=personne
		$result = $this->connection->query($query);
		$listeGroupeVisiteur = $this->connection->getRowArrays($result);

		 // print_r($listeGroupeVisiteur);

		return $listeGroupeVisiteur;
	}
}
?>
