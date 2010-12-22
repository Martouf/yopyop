<?php
/*******************************************************************************************
 * Nom du fichier		: evenementManager.php
 * Date					: 27 décembre 2007
 * Modif				: 28 novembre 2008 => adaptation à yopyop (préfixe de table etc..)
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Défini ce qu'est un evenement
 *******************************************************************************************
 *
 *
 */
class evenementManager {

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
	 * Retourne les infos sur UN evenement dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la evenement
	 */
	function getEvenement($id_evenement){
		$clauses['id_evenement'] = $id_evenement;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'evenement',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUS les evenements avec possibilité de filtrer ceux-ci
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtres à faire
	 * @param string le nom du champ orderby
	 */
	function getEvenements($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'evenement',$clauses,array('*'),$orderBy);
		return $this->connection->getAssocArrays($result);
	}
	
	/*******************************************************************
	 * INSERT
	 *******************************************************************/

	/**
	 * Ajouter un évéement
	 * Seuls le nom, la date de début et la date de fin sont requis
	 *
	 * @return l'id du nouvel enregistrement
	 * @param string toute les infos de l'événement
	 */
	function insertEvenement($nom,$description='',$date_debut,$date_fin,$jour_entier="false",$lieu='',$evaluation='0',$id_calendrier='',$periodicite='non',$uid='',$delai_inscription='',$type='1',$info='',$state='0',$remarque=''){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['date_debut'] = $date_debut;
		$champs['date_fin'] = $date_fin;
		$champs['jour_entier'] = $jour_entier;
		$champs['lieu'] = $lieu;
		$champs['evaluation'] = $evaluation;
		$champs['id_calendrier'] = $id_calendrier;
		$champs['periodicite'] = $periodicite;
		// permet d'importer un uid provenant d'un autre calendrier importé par un flux ics. Ainsi on n'ajoute pas plusieurs fois le même événement.
		if (!empty($uid)) {
			$champs['uid'] = $uid;
		}else{
			$champs['uid'] = md5($dateCourante)."@".$_SERVER['SERVER_NAME']; // on génére un id unique			
		}
		$champs['delai_inscription'] = $delai_inscription;
		$champs['type'] = $type;
		$champs['info'] = $info;
		$champs['state'] = $state;
		$champs['auteur'] = $_SESSION['id_personne']; // par l'intrermédaire de la session va chercher l'id de l'utilisateur courant du backend de typo3
		$champs['auteur_modif'] = $_SESSION['id_personne'];
		$champs['remarque'] = $remarque;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		
		// crée le nouvel enregistrement et obtient la clé
		$id_evenement = $this->connection->insert($this->tablePrefix.'evenement',$champs);
		
		return $id_evenement;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un evenement
	 * Ne met à jour que les champs pour lesquels une nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos de l' evenement
	 */
	function updateEvenement($id_evenement,$nom,$description,$date_debut,$date_fin,$jour_entier,$lieu,$evaluation,$id_calendrier,$periodicite,$delai_inscription,$type,$info,$state,$remarque,$uid){
		
		if(!empty($nom)){ $champs['nom'] = $nom; }
		if(!empty($description)){ $champs['description'] = $description; }
		if(!empty($date_debut)){ $champs['date_debut'] = $date_debut; }
		if(!empty($date_fin)){ $champs['date_fin'] = $date_fin; }
		if(!empty($jour_entier)){ $champs['jour_entier'] = $jour_entier; }
		if(!empty($lieu)){ $champs['lieu'] = $lieu; }
		if(!empty($periodicite)){ $champs['periodicite'] = $periodicite; }
		if(!empty($evaluation)){ $champs['evaluation'] = $evaluation; }
		if(!empty($id_calendrier)){ $champs['id_calendrier'] = $id_calendrier; }
		
		if(!empty($delai_inscription)){ $champs['delai_inscription'] = $delai_inscription; }
		if(!empty($type)){ $champs['type'] = $type; }
		if(!empty($info)){ $champs['info'] = $info; }
		if(!empty($state)){ $champs['state'] = $state; }
		$champs['auteur_modif'] = $_SESSION['id_personne'];
		if(!empty($remarque)){ $champs['remarque'] = $remarque; }
		
		
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		
		$conditions['id_evenement'] = $id_evenement;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'evenement',$champs,$conditions);
	}

	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime un evenement
	 *
	 * @return -
	 * @param int id de la evenement à supprimer
	 */
	function deleteEvenement ($id_evenement){
		$request = "DELETE FROM `".$this->tablePrefix."evenement` WHERE `id_evenement`='".$id_evenement."' ";
		return $this->connection->query($request);
	}
	
	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/
	
	/**
	 * Importe des événements depuis un fichier vCalendar (.ics)
	 *
	 * ATTENTION .. importe à chaque fois tous les événements ne vérifie pas si ils sont déjà présents dans la base !
	 *
	 * @return array() La liste des id des nouveaux événements ajoutés
	 * @param string url ou nom du fichier .ics du calendrier à importer
	 * @param int id. L'id du calendrier dans lequel on veut placer l'événement
	 */
	function importCalendar($nomFichierIcs, $idGroupe=''){
		// tableau contenant pour chaque événement ics un talbeau des champs reconnu
		$evenements = parse_ical($nomFichierIcs);
		$nomCalendrier="Calendrier";
		
		$listeEvenementsAjoutes = array();
		
	//	print_r($evenements);
		foreach ($evenements as $key => $evenement) {
			$nom= "";
			$description= "";
			$date_debut= "";
			$date_fin= "";
			$jour_entier= "";
			$lieu= "";
			$uid ='';
			
			// détermine si l'entrée est un événement ou les infos à propos du calendrier
			if (isset($evenement['generator'])){
				if (isset($evenement['name']))
					$nomCalendrier = $evenement['name'];  // obtient le nom du calendrier
			}else{
				// traite les événements
				// détermine si l'événment est pour tout la journée ou non
				
				// si l'événement dure toute la journée
				if ($evenement['all_day']=='1'){
					$jour_entier = "true";
					$date_debut = $evenement['start_date']." 00:00:01";
					$date_fin = $evenement['end_date']." 23:59:59";
				}else{
					$jour_entier = "false";
					$date_debut = $evenement['start_date']." ".$evenement['start_time'].":00";
					
					// si la date de fin existe. Parfois la date de fin n'est pas indiquée, mais elle est déduite d'une durée DURATION:PT30M ou DURATION:PT1H30M  (voir rfc2445)
					// iCal utilise parfois ce principe. Mais quand? je n'ai pas compris la logique! J'y ai vu des durée de 30M à 4H45 ... et parfois des événements DTSTART avec DTEND à 15 minutes d'intervale comme 2h ou 12h !
					// En cas de problèmes, un passage par google calendar permet de convertir le fichier à n'utiliser que des événements avec dateheure de début et fin et non plus des durées.
					// Après modification du parser de base et écriture d'une fonction les durées sont reconnues.
					if (isset($evenement['end_date'])){
						$date_fin = $evenement['end_date']." ".$evenement['end_time'].":00";
					
					}elseif(isset($evenement['duration'])){
						// utilise la fonction addDuration pour trouver la date de fin en fonction de la date de début et de la durée
						$date_fin = self::addDuration($date_debut,$evenement['duration']);
						
					}else{ // si pas de duration.. ce qui semble peu probable!
		
						// on ajoute 30 minutes à la date/heure de début
						//converti la date datetime en timestamp
						$timeStamp = strtotime($date_debut); 
						$timeStampFin = $timeStamp + (30*60);  // 30 minutes en seconde
						$date_fin = date('Y-m-d H:i:s',$timeStampFin);  // retrouve la date de fin au format dateTime
					}
				}

				// si disponible trouve le nom de l'événement
				if (isset($evenement['summary']))
					$nom = $evenement['summary'];
					
				// si disponible trouve la description de l'événement
				if (isset($evenement['description']))
					$description = $evenement['description'];
				
				// si disponible trouve le lieu de l'événement
				if (isset($evenement['location']))
					$lieu = $evenement['location'];	
					
				// si disponible trouve l'uid de l'événement
				if (isset($evenement['uid']))
					$uid = $evenement['uid'];
				
				// si l'événement n'existe pas déjà dans la base, l'ajoute
				$evenementExistant = $this->getEvenements(array('uid'=>$uid));
				//print_r($evenementExistant);
				if (count($evenementExistant)< 1) {
					
				//	echo "<br />ajoute evenement: ",$nom,$description,$date_debut,$date_fin,$jour_entier,$lieu,'',$idGroupe,'non',$uid;
					//crée l'événement dans la base de donnée.
					$idNouvelEvenement = $this->insertEvenement($nom,$description,$date_debut,$date_fin,$jour_entier,$lieu,'',$idGroupe,'non',$uid);
					
					// si l'événement existe déjà dans la base vérifie si l'événement en cours d'importation a une date de modification plus récente. Si c'est le cas, met à jour l'événement locale d'après les données du distant.
				}else{
					foreach ($evenementExistant as $key => $evenementDejaPresent) {
					//	echo "<br />evenement: ",$evenementDejaPresent['nom'],"est déjà présent dans la base ";
						
						$timeStampDateModification = strtotime($evenementDejaPresent['date_modification']);
						$timeStampDateModificationEvenementImporte = $evenement['stamp_unix'];
						
						if ($timeStampDateModificationEvenementImporte > $timeStampDateModification) {
						//	echo "<br />on met à jour l'événement déjà présent dans la base: ",$evenementDejaPresent['id_evenement'],$nom,$description,$date_debut,$date_fin,$jour_entier,$lieu,'',$idGroupe,'non','';
							$this->updateEvenement($evenementDejaPresent['id_evenement'],$nom,$description,$date_debut,$date_fin,$jour_entier,$lieu,'',$idGroupe,'non','');
							
							// signal la modification comme si c'était un nouvel événement. Ainsi on a une trance de tout ce qui a été modifié.
							$idNouvelEvenement = $evenementDejaPresent['id_evenement'];
						}
					}
				}
				
				// fait une liste des id des nouveaux evenements
				if (!empty($idNouvelEvenement)) {
					$listeEvenementsAjoutes[] = $idNouvelEvenement;
				}
								
			}// if... else événements
			
		} // foreach evenements		
		return $listeEvenementsAjoutes; // retourne la liste des événements ajouté
	}
	
	/**
	 * Retourne au format mysql datetime la date qui correspond à une date donnée + une durée au format DURATION de la rfc2445
	 * Par rapport à la rfc2445, ne supporte que les durées, jours, heures, minutes, secondes, mais pas les semaines.
	 * 
	 * @param datetime la date de départ au fromat mysql datetime 2008-01-31 15:14:00
	 * @param duration la durée au format duration de la rfc2445 (iCal) 
	 * @return datetime la date augmentée de la durée
	 */
	function addDuration($dateDebut,$duree){

		// décode la durée
		$pattern = "@P([0-9]*D)?[T]?([0-9]*H)?([0-9]*M)?([0-9]*S)?@";

		preg_match($pattern,$duree,$resultat);

		// supprime le 1er élément du tableau qui est la chaine fournie
		$resultat['0']= '';

		$jours = '';
		$heures = '';
		$minutes = '';
		$secondes = '';

		foreach ($resultat as $key => $value) {
			if (!empty($value)) {

				preg_match("@([0-9]*)([DHMS])@",$value,$tabChiffre);

				$duree = $tabChiffre['1'];
				$typeDuree = $tabChiffre['2'];

				if ($typeDuree=='H') {
					$heures = $duree;
				}elseif($typeDuree=='M'){
					$minutes = $duree;
				}elseif ($typeDuree == 'S') {
					$secondes = $duree;
				}elseif ($typeDuree == 'D') {
					$jours = $duree;
				}
			}// if !empty
		}// foreach

		// converti la durée en timestamp unix. (nb de seconde)
		$timeStampDuree = 0;
		$timeStampDuree += $heures * 60 * 60; //nb heures en seconde
		$timeStampDuree += $minutes * 60; //nb minutes en seconde
		$timeStampDuree += $secondes;
		$timeStampDuree += $jours * 60 * 60 * 24; //nb jours en seconde

		// converti la date fournie en timestamp
		$timeStampBase = strtotime($dateDebut);

		$timeStampFin = $timeStampBase + $timeStampDuree;
		return date('Y-m-d H:i:s',$timeStampFin);  // retourne la date de fin au format dateTime
	}
	
	/**
	 * Retourne les id des événements qui sont dans une tranche horaire
	 *
	 * @return array() le tableau contenant la liste de id des événements
	 * @param datetime $trancheDebut le moment de début de la tranche horaire voulue
	 * @param datetime $trancheFin le moment de fin de la tranche horaire voulue
	 * @param string $filtreOptionnel permet d'insérer dans la fonction un morceau de requête suppélémentaire permettant de filtrer le résultat avec des critères supplémentaires.
	 * Par défaut, il y a des valeurs de tranche allant de 2000 à 2050... mais normalement il faut mettre une valeur !!
	 */
	function getListeEvenements($trancheDebut="2000-01-01 00:00:00",$trancheFin="2050-01-01 00:00:00",$orderby="date_debut asc",$filtreOptionnel=''){
		// echo $trancheDebut;
		// echo "<br />".$trancheFin;
		
		$query = "select id_evenement from ".$this->tablePrefix."evenement where date_debut > \"".$trancheDebut."\" and date_debut < \"".$trancheFin."\" ".$filtreOptionnel." order by ".$orderby;
		$result = $this->connection->query($query);
		return $this->connection->getRowArrays($result);
	}
	
	/**
	 * Retourne les id des événements "Jour entier" qui sont dans une tranche horaire
	 *
	 * @return array() le tableau contenant la liste de id des événements
	 * @param datetime $trancheDebut le moment de début de la tranche horaire voulue
	 * @param datetime $trancheFin le moment de fin de la tranche horaire voulue
	 * @param string $filtreOptionnel permet d'insérer dans la fonction un morceau de requête suppélémentaire permettant de filtrer le résultat avec des critères supplémentaires.
	 * Par défaut, il y a des valeurs de tranche allant de 2000 à 2050... mais normalement il faut mettre une valeur !!
	 */
	function getListeEvenementsJourEntier($trancheDebut="2000-01-01 00:00:00",$trancheFin="2050-01-01 00:00:00",$orderby="date_debut asc",$filtreOptionnel=''){
		
		$query = "select id_evenement from ".$this->tablePrefix."evenement where date_debut > \"".$trancheDebut."\" and date_debut < \"".$trancheFin."\" and jour_entier=\"true\" ".$filtreOptionnel." order by ".$orderby;
		$result = $this->connection->query($query);
		return $this->connection->getRowArrays($result);
	}
	
	/**
	 * Retourne les id des événements qui ne sont pas de "Jour entier" qui sont dans une tranche horaire
	 *
	 * @return array() le tableau contenant la liste de id des événements
	 * @param datetime $trancheDebut le moment de début de la tranche horaire voulue
	 * @param datetime $trancheFin le moment de fin de la tranche horaire voulue
	 * @param string $filtreOptionnel permet d'insérer dans la fonction un morceau de requête suppélémentaire permettant de filtrer le résultat avec des critères supplémentaires.
	 * Par défaut, il y a des valeurs de tranche allant de 2000 à 2050... mais normalement il faut mettre une valeur !!
	 */
	function getListeEvenementsSaufJourEntier($trancheDebut="2000-01-01 00:00:00",$trancheFin="2050-01-01 00:00:00",$orderby="date_debut asc",$filtreOptionnel=''){
		
		$query = "select id_evenement from ".$this->tablePrefix."evenement where date_debut > \"".$trancheDebut."\" and date_debut < \"".$trancheFin."\" and jour_entier!=\"true\" ".$filtreOptionnel." order by ".$orderby;
		$result = $this->connection->query($query);
		return $this->connection->getRowArrays($result);
	}
	
	/**
	 * Retourne les id des événements qui commencent avant et finissent après une date donnée.
	 * Il faut choisir les jours entiers ou pas les jours entier.
	 *
	 * @return array() le tableau contenant la liste de id des événements
	 * @param datetime $dateMillieu le moment qui se trouve au milieu des événements.
	 * @param string $filtreOptionnel permet d'insérer dans la fonction un morceau de requête suppélémentaire permettant de filtrer le résultat avec des critères supplémentaires.
	 */
	function getListeEvenementsSurLeMoment($dateMillieu="",$jourEntier="false",$filtreOptionnel=''){
		if (empty($dateMillieu)) {
			$dateMillieu = date('Y-m-d H:i:s'); // la date courante
		}
		if ($jourEntier=="true") {
			$query = "select id_evenement from ".$this->tablePrefix."evenement where date_debut < \"".$dateMillieu."\" and date_fin > \"".$dateMillieu."\" and jour_entier=\"true\" ".$filtreOptionnel." order by date_debut";
		}elseif($jourEntier=="all"){
			$query = "select id_evenement from ".$this->tablePrefix."evenement where date_debut < \"".$dateMillieu."\" and date_fin > \"".$dateMillieu."\" ".$filtreOptionnel." order by date_debut";
		}else{
			$query = "select id_evenement from ".$this->tablePrefix."evenement where date_debut < \"".$dateMillieu."\" and date_fin > \"".$dateMillieu."\" and jour_entier!=\"true\" ".$filtreOptionnel." order by date_debut";
		}

		$result = $this->connection->query($query);
		return $this->connection->getRowArrays($result);
	}
	
	/**
	 * Retourne les événements qui sont liés à un calendrier.
	 * Les événements sont triés par date de début 
	 *
	 * @return array() le tableau contenant la liste de id des événements
	 * @param int $idCalendrier => id du calendrier
	 */
	function getEvenementsCalendrier($idCalendrier){
		$query = "select id_evenement from ".$this->tablePrefix."evenement where id_calendrier = \"".$idCalendrier."\"";
		$result = $this->connection->query($query);
		return $this->connection->getRowArrays($result);
	}
	
	
	/*******************************************************************
	 * gestion de l'affichage calendrier
	 *******************************************************************/
	 /* Ces fonctions sont conçue pour un clendrier de taille fixe en pixel.
	  * Le calendrier à une taille de 767px de large, et 768px de haut. (hauteur donnée par le choix de 15px la demi heure plus les bordures)
	  * La graduation à gauche fait 50px de large. Un jour fait 100px de large.
	  * Un bloc événement ne comporte pas de marge, a un padding de 4px et une bordure de 1px. => pour couvrir les 100px du jour il faut donner une taille de 90px au contenu. (100 = 90 + 2x 1px bordure + 2x 4px de padding)
	  */
	
		/**
		 * Fourni la date du jour au format: dateTime mysql
		 * retourne le nombre de pixels entre l'origine du calendrier et le début de l'événement
		 * @param: datetime $debutEvenement "2007-12-17 14:00:00"
		 * @return: int hauteur en px entre la référence du calendrier à 6h du mat et le bloc représenté
		 */
		function getTopPixel($debutEvenement){
			//sépare la date et l'heure
			$dateComplete = explode(" ",$debutEvenement);
			$partieDate = $dateComplete[0];
			$partieHeure = $dateComplete[1];
			
			$date = explode("-",$partieDate);
			$annee = $date[0];
			$mois = $date[1];
			$jour = $date[2];
			
			$temps = explode(":",$partieHeure);
			$heures = $temps[0];
			$minutes = $temps[1];
			$secondes = $temps[2];
			
			// une heure est représentée par une hauteur de 32px
			$heureEnPixel = 32;
			
			// le nombre d'heures avec les fraction d'heure décimale.
			$heuresDecimales = $heures + ($minutes/60);
			
			
			// depuis l'origine la hauteur = nb heure * hauteur de l'heure en px. Donc Nbheure * 32
			$hauteurTopPx = round($heuresDecimales * $heureEnPixel);  // on s'assure d'obtenir un nombre entier de pixel
			
			return $hauteurTopPx;
		}
		
		/**
		 * @param: l'heure du début et l'heure de fin au format datetime de mysql 2007-12-17 15:40
		 * @return: la durée à afficher en pixel
		 */
		function getDureePixel($debutEvenement,$finEvenement){
			
			// heure de début
			//sépare la date et l'heure
			$dateComplete = explode(" ",$debutEvenement);
			$partieDate = $dateComplete[0];
			$partieHeure = $dateComplete[1];
			
			$date = explode("-",$partieDate);
			$debutAnnee = $date[0];
			$debutMois = $date[1];
			$debutJour = $date[2];
			
			$temps = explode(":",$partieHeure);
			$debutHeures = $temps[0];
			$debutMinutes = $temps[1];
			$debutSecondes = $temps[2];
			
			// heure de fin
			//sépare la date et l'heure
			$dateComplete = explode(" ",$finEvenement);
			$partieDate = $dateComplete[0];
			$partieHeure = $dateComplete[1];
			
			$date = explode("-",$partieDate);
			$finAnnee = $date[0];
			$finMois = $date[1];
			$finJour = $date[2];
			
			$temps = explode(":",$partieHeure);
			$finHeures = $temps[0];
			$finMinutes = $temps[1];
			$finSecondes = $temps[2];
			
			// une heure est représentée par une hauteur de 32px
			$heureEnPixel = 32;
			
			// le nombre d'heures avec les fractions d'heure décimale. On néglige les secondes et un bloc événement ne peut pas excéder la journée.
			$heuresDebutDecimales = $debutHeures + ($debutMinutes/60);
			$heuresFinDecimales = $finHeures + ($finMinutes/60);
			
			$nbHeures = $heuresFinDecimales - $heuresDebutDecimales;  // la différence.
			
			$hauteurDureePx = round($nbHeures * $heureEnPixel); // le padding vertical est de 0px donc on soustrait rien. Si le padding=5px il faut faire -10px ce qui représente la taille du padding et des bordures. Il faut donc la soustraire.
			
			return $hauteurDureePx;
		}
		
		/**
		 * Cette fonction détermine quel jour de la semaine se trouve le bloc
		 *
		 * @param: l'heure du début au format datetime de mysql 2007-12-17 15:40
		 * @return: la distance entre le bloc événement et le bord gauche du calendrier
		 */
		function getLeftPixel($debutEvenement){
			
			// retourne le jour de la semaine de la date donnée. Lundi=>1 ... dimanche=>7
			$jourDeLaSemaine = date('N',strtotime($debutEvenement));
			
			// dans mon calcul j'ai besoin de lundi=>0 ... dimanche=>6
			$jourDeLaSemaine = $jourDeLaSemaine -1;
			
			// la largeur d'un jour vaut 102px et la graduation prend 51px. Le point de départ de chaque jour est donc égal à 102*n°du jour + 51
			$leftPx = $jourDeLaSemaine * 102 + 51;
			
			return $leftPx;
		}
		
		
		 /*  Fonctions pour afficher le calendrier en vue annuelle. 
		  * Le bloc #blocCalendrierAnnee  803px de large sur 649 de haut (d'après mesure)
		  * La hauteur de l'élément .caseJourAnnee est de 20px si on additionne le 1px de sa bordure en haut on a une hauteur de 21px par jour. 31x21 = 651px
		  * La largeur de l'élément .colonneMois est de 66px si on ajoute le 1px de sa bordure de gauche on a une largeur de 67px. 67x12 = 804
		  */

			/**
			 * Fourni la date du jour au format: dateTime mysql
			 * retourne le nombre de pixels entre l'origine du calendrier et le début de l'événement
			 * @param: datetime $debutEvenement "2007-12-17 14:00:00"
			 * @return: int hauteur en px entre la référence du calendrier au jour 1 à 0h00
			 */
			function getTopPixelAnnee($debutEvenement){
				//sépare la date et l'heure
				$dateComplete = explode(" ",$debutEvenement);
				$partieDate = $dateComplete[0];
				$partieHeure = $dateComplete[1];

				$date = explode("-",$partieDate);
				$annee = $date[0];
				$mois = $date[1];
				$jour = $date[2];

				$temps = explode(":",$partieHeure);
				$heures = $temps[0];
				$minutes = $temps[1];
				$secondes = $temps[2];

				// un jour est représenté par une hauteur de 20px + 1px de bordure = 21px
				$jourEnPixel = 21; // todo à transformer en variable
				
				$jourDecimal = $jour - 1; // vu que l'origine n'est pas à 0, mais 1 !
				
				// option avec le placement des blocs suivant l'heure de l'évéenement dans la journée
				$optionHeureVisible = false;  // todo permettre de régler cette option
				if ($optionHeureVisible) {
					$jourDecimal = $jourDecimal + ($heures/24); // on ajoute une variation en fonction de l'heure de début.
				}

				// depuis l'origine la hauteur = nb heure * hauteur de l'heure en px. Donc Nbheure * 32
				$hauteurTopPx = round($jourDecimal * $jourEnPixel);  // on s'assure d'obtenir un nombre entier de pixel

				return $hauteurTopPx;
			}

			/**
			 * @param: l'heure du début et l'heure de fin au format datetime de mysql 2007-12-17 15:40
			 * @return: la durée à afficher en pixel
			 */
			function getDureePixelAnnee($debutEvenement,$finEvenement){

				// heure de début
				//sépare la date et l'heure
				$dateComplete = explode(" ",$debutEvenement);
				$partieDate = $dateComplete[0];
				$partieHeure = $dateComplete[1];

				$date = explode("-",$partieDate);
				$debutAnnee = $date[0];
				$debutMois = $date[1];
				$debutJour = $date[2];

				// $temps = explode(":",$partieHeure);
				// $debutHeures = $temps[0];
				// $debutMinutes = $temps[1];
				// $debutSecondes = $temps[2];

				// heure de fin
				//sépare la date et l'heure
				$dateComplete = explode(" ",$finEvenement);
				$partieDate = $dateComplete[0];
				$partieHeure = $dateComplete[1];

				$date = explode("-",$partieDate);
				$finAnnee = $date[0];
				$finMois = $date[1];
				$finJour = $date[2];

				// $temps = explode(":",$partieHeure);
				// $finHeures = $temps[0];
				// $finMinutes = $temps[1];
				// $finSecondes = $temps[2];
				
				
				
				// un jour est représenté par une hauteur de 20px + 1px de bordure = 21px
				$jourEnPixel = 21; // todo à transformer en variable
				
				// option avec le placement des blocs suivant l'heure de l'évéenement dans la journée
				$optionHeureVisible = false;  // todo permettre de régler cette option
				if ($optionHeureVisible) {
					// calcul le nombre de jour et de fraction de jour en décimal. (pour éventuellement afficher un bout de journée)
					// on applique un décalage vu que l'origine n'est pas à 0, mais 1 !
					$jourDebutDecimal = $debutJour + ($debutJour/24);
					$jourFinDecimal = $finJour+ ($finJour/24);
					$nbJours = $jourFinDecimal - $jourDebutDecimal;
					
				}else{
					$jourDebutDecimal = $debutJour;
					$jourFinDecimal = $finJour;
					$nbJours = $jourFinDecimal - $jourDebutDecimal + 1; // on ajoute 1 car si on veut présenter 2 jours on propse 2 blocs alors que la différence entre 2 bloc fait 1
					
				}

				$hauteurDureePx = round($nbJours * $jourEnPixel);

				return $hauteurDureePx;
			}

			/**
			 * Cette fonction détermine quel jour de la semaine se trouve le bloc
			 *
			 * @param: l'heure du début au format datetime de mysql 2007-12-17 15:40
			 * @return: la distance entre le bloc événement et le bord gauche du calendrier
			 */
			function getLeftPixelAnnee($debutEvenement){

				// retourne le mois
				$mois = date('n',strtotime($debutEvenement)); // de 1.. 12

				// dans mon calcul j'ai besoin de: janvier = 0 et donc décembre = 11
				$mois = $mois -1;

				// l'idée est de proposer une vue avec des blocs qui se partagent l'espace de la case horizontalement. Ce qui permet d'afficher matin et soir de manière plus claire que avec des blocs haut bass.
				$optionVueHeureEnColonne = false; // todo permettre de régler cette option
				$heureLimite = 12; // on choisi arbitrairement midi comme heure de séparation.
				if ($optionVueHeureEnColonne) {
					//sépare la date et l'heure
					$dateComplete = explode(" ",$debutEvenement);
				//	$partieDate = $dateComplete[0];
					$partieHeure = $dateComplete[1];

					// $date = explode("-",$partieDate);
					// $debutAnnee = $date[0];
					// $debutMois = $date[1];
					// $debutJour = $date[2];

					$temps = explode(":",$partieHeure);
					$debutHeures = $temps[0];
					// $debutMinutes = $temps[1];
					// $debutSecondes = $temps[2];
					
					if ($debutHeures < $heureLimite) {
						$leftPx = $mois * 67 + 23;
					}else{
						// la largeur d'un mois vaut 67px + les 23 px de décalage que l'on donne pour ne pas recouvrir le no du jour.
						$leftPx = $mois * 67 + 23 + 22; // La largeur à disposition est de 44 px donc on pousse de la moitié pour l'après midi.
					}
				}else{
					// la largeur d'un mois vaut 67px + les 23 px de décalage que l'on donne pour ne pas recouvrir le no du jour.
					$leftPx = $mois * 67 + 23;
				}

				return $leftPx;
			}
			
			/**
			 * Cette fonction permet de gérer la largeur d'un bloc événement
			 *
			 * @return: la largeur en pixel 
			 */
			function getLargeurPixelAnnee(){

				$largeurBlocEvenement = 44;

				// l'idée est de proposer une vue avec des blocs qui se partagent l'espace de la case horizontalement. Ce qui permet d'afficher matin et soir de manière plus claire que avec des blocs haut bass.
				$optionVueHeureEnColonne = false; // todo permettre de régler cette option
				if ($optionVueHeureEnColonne) {
					$leftPx = $largeurBlocEvenement / 2;
				}else{
					// toute la largeur à disposition
					$leftPx = $largeurBlocEvenement;
				}

				return $leftPx;
			}
		
		
		
		/**
		 * Retourne un tableau avec les id des événements voisin (si ils existent)
		 *
		 * @return array() un tableau qui contient l'id de l'événement précédent et l'id de l'événement suivant
		 * @param int l'id de l'evenement
		 * @param array() le tableau contenant la liste des id
		 */
		function getEvenementsVoisins($id, $liste){
			$key = array_search($id,$liste);
			$keySuivant = $key+1;
			$keyPrecedent = $key-1;
			$idSuivant = $liste[$keySuivant];
			$idPrecedent = $liste[$keyPrecedent];
			return array(0=>$idPrecedent,1=>$idSuivant);
		}
		
		/**
		 * Change l'Etat d'un événement à exporté
		 *
		 * @return true
		 * @param int $idEvenement => id de k'événement
		 */
		function setEvenementExported($idEvenement){
			$query = "update ".$this->tablePrefix."evenement set state='4' where id_evenement = \"".$idEvenement."\"";
			return $this->connection->query($query);
		}
		
		/**
		 * Supprime les événements fantôme. (ceux qui ne sont affilié à aucun calendrier)
		 * Les événements fantôme sont créé si un utilisateur crée un événement, mais qu'il ne l'enregistre pas. Le fait de double cliquer dans le calendrier crée un événement minimal pour que l'on puisse le redimentionner.
		 *
		 * @return true
		 */
		function ghostBuster(){
			$query = "delete from ".$this->tablePrefix."evenement where id_calendrier is NULL";
			return $this->connection->query($query);
		}
		
		/**
		 * test si un evenement existe
		 *
		 * @return true si l'événement existe false sinon.
		 * @param int id de la evenement à tester
		 */
		function evenementExiste ($id_evenement){
			$request = "select id_evenement FROM `".$this->tablePrefix."evenement` WHERE `id_evenement`='".$id_evenement."' ";
			$result = $this->connection->query($request);
			// si le nombre de résultats est >0 le résultat est envoyé, sinon c'est false qui est envoyé.
			if ($result != null) {
				return true;
			}else{
				return false;
			}
		}
	
} // evenementManager
?>
