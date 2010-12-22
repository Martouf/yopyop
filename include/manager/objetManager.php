<?php
/*******************************************************************************************
 * Nom du fichier		: objetManager.php
 * Date					: 28 juillet 2010
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@ecodev.ch
 * But de ce fichier	: Défini ce qu'est un objet (destiné à la location)
 *******************************************************************************************
 *  
 *
 */
class objetManager {

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
	 * Retourne les infos sur UN objet dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la objet
	 */
	function getObjet($id_objet){
		$clauses['id_objet'] = $id_objet;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'objet',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUS les objets avec possibilité de filtrer ceux-cis
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getObjets($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'objet',$clauses,array('*'),$orderBy);
		return $this->connection->getAssocArrays($result);
	}
	
	/*******************************************************************
	 * INSERT
	 *******************************************************************/


	/**
	 * Ajouter un élément
	 *
	 * @return l'id du nouvel enregistrement
	 * @param string toute les infos du objet
	 */
	function insertObjet($nom='',$description='',$url='',$id_proprietaire='',$id_image='',$id_calendrier='',$prix='0',$caution='0',$latitude='',$longitude='',$etat='0',$duree_max='0',$duree_min='0',$evaluation='0',$groupeAutoriseLecture='0',$groupeAutoriseEcriture='0',$groupeAutoriseCommentaire='0'){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['url'] = $url;
		if (empty($id_proprietaire)) {
			$id_proprietaire = $_SESSION['id_personne'];  // si le propriétaire n'est pas indiqué attribue l'utilisateur courrant comme propriétaire
		}
		$champs['id_proprietaire'] = $id_proprietaire;
		$champs['id_image'] = $id_image;
		$champs['id_calendrier'] = $id_calendrier;
		$champs['prix'] = $prix;
		$champs['caution'] = $caution;
		$champs['latitude'] = $latitude;
		$champs['longitude'] = $longitude;
		$champs['lieu'] = $lieu;
		$champs['etat'] = $etat;  // 0 = inactif, 1 = actif
		$champs['duree_max'] = $duree_max;
		$champs['duree_min'] = $duree_min;
		$champs['evaluation'] = $evaluation;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		$champs['groupe_autorise_lecture'] = $groupeAutoriseLecture;
		$champs['groupe_autorise_ecriture'] = $groupeAutoriseEcriture;
		$champs['groupe_autorise_commentaire'] = $groupeAutoriseCommentaire;
		$champs['createur'] = $_SESSION['id_personne'];
		$champs['modificateur'] = $_SESSION['id_personne'];
		
		// crée le nouvel enregistrement et obtient la clé
		$id_objet = $this->connection->insert($this->tablePrefix.'objet',$champs);
		
		return $id_objet;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un objet
	 * Ne met à jour que les champs pour lesquels une nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos du objet
	 */
	function updateObjet($id_objet,$nom,$description,$url,$id_proprietaire,$id_image,$id_calendrier,$prix,$caution,$latitude,$longitude,$lieu,$etat,$duree_max,$duree_min,$evaluation,$groupeAutoriseLecture,$groupeAutoriseEcriture,$groupeAutoriseCommentaire){
		if(!empty($nom)){$champs['nom'] = $nom; }
		if(!empty($description)){$champs['description'] = $description; }
		if(!empty($url)){$champs['url'] = $url; }
		if(!empty($id_proprietaire)){$champs['id_proprietaire'] = $id_proprietaire; }
		if(!empty($id_image)){$champs['id_image'] = $id_image; }
		if(!empty($id_calendrier)){$champs['id_calendrier'] = $id_calendrier; }
		if(!empty($prix)){$champs['prix'] = $prix; }
		if(!empty($caution)){$champs['caution'] = $caution; }
		if(!empty($latitude)){$champs['latitude'] = $latitude; }
		if(!empty($longitude)){$champs['longitude'] = $longitude; }
		if(!empty($lieu)){$champs['lieu'] = $lieu; }
		if(!empty($etat)){$champs['etat'] = $etat; }
		if(!empty($duree_max)){$champs['duree_max'] = $duree_max; }
		if(!empty($duree_min)){$champs['duree_min'] = $duree_min; }
		if(!empty($evaluation)){$champs['evaluation'] = $evaluation; }
		if(!empty($groupeAutoriseLecture)){$champs['groupe_autorise_lecture'] = $groupeAutoriseLecture; }
		if(!empty($groupeAutoriseEcriture)){$champs['groupe_autorise_ecriture'] = $groupeAutoriseEcriture; }
		if(!empty($groupeAutoriseCommentaire)){$champs['groupe_autorise_commentaire'] = $groupeAutoriseCommentaire; }
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		$champs['modificateur'] = $_SESSION['id_personne'];
		
		$conditions['id_objet'] = $id_objet;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'objet',$champs,$conditions);
	}

	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime un objet et ses champs externes
	 *
	 * @return -
	 * @param int id de la objet à supprimer
	 */
	function deleteObjet ($id_objet){
		
		$request = "DELETE FROM `".$this->tablePrefix."objet` WHERE `id_objet`='".$id_objet."' ";
		return $this->connection->query($request);
	}

	
	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/

	
	
} // objetManager
?>
