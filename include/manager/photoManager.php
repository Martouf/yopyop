<?php
/*******************************************************************************************
 * Nom du fichier		: photoManager.php
 * Date					: 15 novembre 2008
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Défini ce qu'est une photo
 *******************************************************************************************
 *  modification du modèle de la table et définition d'un préfixe pour les tables
 *
 */
class photoManager {

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
	 * Retourne les infos sur UNE photo dont l'id est passé en paramètre
	 *
	 * @return array() le tableau contenant le résultat de la requête ou false si échec
	 * @param int l'id de la photo
	 */
	function getPhoto($id_photo){
		$clauses['id_photo'] = $id_photo;
		// select(table,clauses,fields)
		$result = $this->connection->select($this->tablePrefix.'photo',$clauses);
		return $this->connection->getAssocArray($result);
	}
	
	/**
	 * Retourne les infos sur TOUTE les photos avec possibilité de filtrer celle-ci
	 *
	 * @return array(array()) le tableau contenant le résultat de la requête ou false si échec
	 * @param array() un tableau contenant les éventuels filtre à faire
	 * @param string le nom du champ orderby
	 */
	function getPhotos($filtre = array(),$orderBy = ''){
		$clauses = $filtre;
		
		// select(table,clauses,fields,orderBy)
		$result = $this->connection->select($this->tablePrefix.'photo',$clauses,array('*'),$orderBy);
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
	function insertPhoto($nom='',$description='',$lien='',$compteur='0',$orientation='',$date_prise_de_vue='',$latitude='',$longitude='',$evaluation='0',$externe='0'){
		$dateCourante = date('Y-m-d H:i:s',time());
		$champs['nom'] = $nom;
		$champs['description'] = $description;
		$champs['lien'] = $lien;
		$champs['compteur'] = $compteur;
		$champs['orientation'] = $orientation;
		$champs['date_prise_de_vue'] = $date_prise_de_vue;
		$champs['latitude'] = $latitude;
		$champs['longitude'] = $longitude;
		$champs['evaluation'] = $evaluation;
		$champs['externe'] = $externe;
		$champs['date_creation'] = $dateCourante;
		$champs['date_modification'] = $dateCourante;
		
		// crée le nouvel enregistrement et obtient la clé
		$id_photo = $this->connection->insert($this->tablePrefix.'photo',$champs);
		
		return $id_photo;
	}
	
	/*******************************************************************
	 * UPDATE
	 *******************************************************************/

	/**
	 * Met à jour un photo
	 * Ne met à jour que les champs pour lesquels une nouvelle valeur est fournie
	 *
	 * @return boolean
	 * @param string toutes les infos de la photo
	 */
	function updatePhoto($id_photo,$nom,$description,$lien,$compteur,$orientation,$date_prise_de_vue,$latitude,$longitude,$evaluation,$externe){
				
		if(!empty($nom)){ $champs['nom'] = $nom; }
		if(!empty($description)){ $champs['description'] = $description; }
		if(!empty($lien)){ $champs['lien'] = $lien; }
		if(!empty($compteur)){ $champs['compteur'] = $compteur; }
		if(!empty($orientation)){ $champs['orientation'] = $orientation; }
		if(!empty($date_prise_de_vue)){ $champs['date_prise_de_vue'] = $date_prise_de_vue; }
		if(!empty($latitude)){ $champs['latitude'] = $latitude; }
		if(!empty($longitude)){ $champs['longitude'] = $longitude; }
		if(!empty($evaluation)){ $champs['evaluation'] = $evaluation; }
		if(!empty($externe)){ $champs['externe'] = $externe; }
		$champs['date_modification'] = date('Y-m-d H:i:s',time());
		
		$conditions['id_photo'] = $id_photo;
		
		// table, champs(array), conditions(array)
		return $this->connection->update($this->tablePrefix.'photo',$champs,$conditions);
	}


	/*******************************************************************
	 * DELETE
	 *******************************************************************/

	/**
	 * supprime une photo et ses champs externes
	 *
	 * @return -
	 * @param int id de la photo à supprimer
	 */
	function deletePhoto ($id_photo){
		
		// TODO: virer les mots clés avant de supprimer la photo..
		
		$request = "DELETE FROM `".$this->tablePrefix."photo` WHERE `id_photo`='".$id_photo."' ";
		return $this->connection->query($request);
	}

	
	/*******************************************************************
	 * spécifique à la classe
	 *******************************************************************/
	
	
	
	/* Retourne un tableau contenant les mots-clé IPTC contenu dans le fichier de la photos
	 * 
	 * @return: array() un tableau de string contenant les mots-clé IPTC de la photos
	 * @param: $imagePath => le chemin d'accès sur le serveur de la photo. (ou une url)
	 */
	function getIptcKeywordsFromFile($imagePath){
		
		$size = getimagesize ($imagePath, $info);
		if(is_array($info)) {
			if (isset($info["APP13"])) {
				$iptc = iptcparse($info["APP13"]);
				if (isset($iptc['2#025'])) {
			   		return $iptc['2#025'];
				}else{
					return '';
				}
			}else{
				return '';
			}          
		}
	}

	/* Retourne l'auteur de la photo selon le champ IPTC credit
	 * 
	 * @return: la valeur du champ iptc credit
	 * @param: $imagePath => le chemin d'accès sur le serveur de la photo. (ou une url)
	 */
	function getIptcCreditFromFile($imagePath){
		
		$size = getimagesize ($imagePath, $info);
		if(is_array($info)) {    
		   $iptc = iptcparse($info["APP13"]);
   			return $iptc['2#110'][0];             
		}
	}
	
	
	/* Retourne la date de prise de vue depuis l'exif du fichier de la photo. Si aucun exif n'est présent retourne la date courante
	 * 
	 * @return: la date de prise de vue au format mysql dateTime 2008-11-15 12:32:12
	 * @param: $imagePath => le chemin d'accès sur le serveur de la photo. (ne fonctionne pas avec une url)
	 */
	function getExifDateFromFile($imagePath){
		$mime = $this->getTypeMime($imagePath);  // dans le genre image/jpeg ou image/png
		
		if ($mime!="image/jpeg") {
			$exif = false;
		}else{
			// vérifie que cette installation de php supporte la lecture des informations exif
			if (function_exists('exif_read_data')) {
				$exif = exif_read_data($imagePath, 0, true);  //todo bug quand l'exif n'existe pas
			}else{
				$exif = false;
			}
		}
		
		if ($exif===false) {
			return $date = date('Y-m-d H:i:s'); // retourne la date du moment
		}else{
			if (isset($exif['EXIF'])) { // parfois le tableau exif existe, mais pas son contenu !
				$dateDePriseDeVue = $exif['EXIF']['DateTimeOriginal'];
			}else{
				return $date = date('Y-m-d H:i:s'); // retourne la date du moment
			}
			//la date retournée est de forme 2006:01:13 20:38:49
			//on va donc la transformer en type dateTime de mysql
			// d'abord séparer la date de l'heure
			$temps = explode(" ",$dateDePriseDeVue);
			$date = $temps[0];
			$heure = $temps[1];
			//l'heure est juste mais les : de la date vont être converti en -
			$date = str_replace(":","-",$date);
			// on reconstruit une date au format dateTime de mysql
			$dateTime = $date." ".$heure;
			return $dateTime;
		}
	}
	
	/* Retourne un tableau contenant la latitude et la longitude qui se trouve dans l'exif de la photo
	 * 
	 * @return: array() => avec les champs latitude et longitude
	 * @param: $imagePath => le chemin d'accès de la photo sur le serveur. (ne fonctionne pas avec une url)
	 */
	function getExifLatLongFromFile($imagePath){
		$mime = $this->getTypeMime($imagePath);  // dans le genre image/jpeg ou image/png
		
		if ($mime!="image/jpeg") {
			$exif = false;
		}else{
			// vérifie que cette installation de php supporte la lecture des informations exif
			if (function_exists('exif_read_data')) {
				$exif = exif_read_data($imagePath, 0, true);  //todo: bug si pas d'exif dans le jpg
			}else{
				$exif = false;
			}
		}
		
		if($exif===false){
			return array('latitude'=>'','longitude'=>'');
		}else{
			
			// si le champ latitude existe: (on suppose que s'il n'existe pas, même si longitude existe ça ne sert à rien de continuer)
			if(isset($exif['GPS'])){
				if (array_key_exists('GPSLatitude',$exif['GPS'])){
					$latitude = $this->convertDMSToDecimal($exif['GPS']['GPSLatitude']);
					$longitude = $this->convertDMSToDecimal($exif['GPS']['GPSLongitude']);

					// retourne un tableau contenant latitude et longitude
					$position['latitude'] = $latitude;
					$position['longitude'] = $longitude;
					return $position;
				}else{
					return array('latitude'=>'','longitude'=>'');
				}
			}
		}
	}
	
	/**
	* Convert a degree/minute/second value to a decimal value
	* source: lacot.org
	*
	* @param  $dms two dimensionnal array
	* @return  decimal value
	*/
	function convertDMSToDecimal($dms){
		$degree = $dms[0];
		$minutes = $dms[1];
		$seconds = $dms[2];

		// Les nombres fourni dans l'iptc sont des fractions du genre: 6/1 ou 5954/100. Il faut donc les séparer sinon le calcul se fait faux !
		$deg = explode("/", $degree);
		$min = explode("/", $minutes);
		$sec = explode("/", $seconds);
		$degree = $deg[0] / $deg[1];
		$minutes = $min[0] / $min[1]; 
		$seconds = $sec[0] / $sec[1];

		$dms = $degree + $minutes/60 + $seconds/3600;
		return sprintf('%01.4f', $dms);
  }
	
	/* Retourne l'information si la photo est verticale ou horizontale. Calculé en fonction des dimensions de l'image.
	 * 
	 * @return: string v ou h. (horizontal ou vertical)
	 * @param: $imagePath => le chemin d'accès de la photo sur le serveur. (ou une url)
	 */
	function getOrientationFromFile($imagePath){
		// Extrait les dimensions et le type de l'image source.
		$info = getimagesize($imagePath); // ne requiert pas la bibliothèque GD
		$imagewidth = $info[0]; 
		$imageheight = $info[1];
		
		// Si la photo est verticale
		if ($imageheight > $imagewidth){
			$orientation = "v";	
		}else{  // Si la photo est horizontale
			$orientation = "h";
		}
		
		return $orientation;
	}
	
	/* Obtient un tableau avec les données exif de la photo.
	 * La clé du tableau est le nom du champ et la valeur est la valeur correspondante.
	 * 
	 * @return: array() les infos exifs
	 * @param: $imagePath => le chemin d'accès de la photo sur le serveur. (pas une url)
	 */
	function getExif($imagePath){
		return read_exif_data($imagePath);
	}
	
	/* Obtient le type mime de l'image. Ce qui permet de trier les fonctions qui ne vont chercher des choses dans l'exif ou l'iptc des jpg
	 * 
	 * @return: string le type mime de l'image:   image/jpeg ou image/png  etc..
	 * @param: $imagePath => le chemin d'accès de la photo sur le serveur. (une url peut aller)
	 */
	function getTypeMime($imagePath){
		$size = getimagesize ($imagePath, $info);
		if(is_array($info)) {
			return $size['mime'];
		}   
	}
	
	 /* Permet d'obtenir le chemin d'accès de la vignette en fonction du chemin d'accès de l'image.  Ex:  photos/2006/toto/vignettes/toto.jpg
	  * Fonctionne aussi avec des url
	  */
	 function getLienVignette($imagePath){
	 	$nomSimplifie = basename($imagePath);
		$nomDossier = dirname($imagePath);  // obtient le nom du dossier. Ex: photos/2006/toto/toto.jpg devient photos/2006/toto
	 	$thpath = $nomDossier.'/vignettes/'.$nomSimplifie; // chemin d'accès de la vignette: photos/2006/toto + /vignettes/ + toto.jpg
	 	return $thpath;
	 }
	
	 /* Permet d'obtenir le chemin d'accès de la moyenne.  Ex:  photos/2006/toto/vignettes/toto.jpg
	  *
	  */
	 function getLienMoyenne($imagePath){
	 	$nomSimplifie = basename($imagePath);
		$nomDossier = dirname($imagePath);  // obtient le nom du dossier. Ex: photos/2006/toto/toto.jpg devient photos/2006/toto
	 	$thpath = $nomDossier.'/moyennes/'.$nomSimplifie; // chemin d'accès de la vignette: photos/2006/toto + /vignettes/ + toto.jpg
	 	return $thpath;
	 }
	
	 /* Permet d'obtenir le chemin d'accès de la photo de taille perso.  Ex:  photos/2006/toto/perso/toto.jpg
	  *
	  */
	 function getLienPerso($imagePath){
	 	$nomSimplifie = basename($imagePath);
		$nomDossier = dirname($imagePath);  // obtient le nom du dossier. Ex: photos/2006/toto/toto.jpg devient photos/2006/toto
	 	$thpath = $nomDossier.'/perso/'.$nomSimplifie; // chemin d'accès de la vignette: photos/2006/toto + /perso/ + toto.jpg
	 	return $thpath;
	 }
	
	
	 /* Permet d'obtenir le chemin d'accès de la vignette en fonction du chemin d'accès de l'image.  Ex:  photos/2006/toto/vignettes/toto.jpg
	  * Fonctionne avec les images stockée sur picasa web
	  */
	 function getLienVignettePicasa($imagePath){
	 	$nomSimplifie = basename($imagePath);
		$nomDossier = dirname($imagePath);  // obtient le nom du dossier. Ex: http://lh6.ggpht.com/_KyCIynYq9F8/RlUt4lqSpvI/AAAAAAAAAE8/PE6tmStyQI0/IMG_2688.JPG devient http://lh6.ggpht.com/_KyCIynYq9F8/RlUt4lqSpvI/AAAAAAAAAE8/PE6tmStyQI0
	 	$thpath = $nomDossier.'/s200/'.$nomSimplifie; // chemin d'accès de la vignette: http://lh6.ggpht.com/_KyCIynYq9F8/RlUt4lqSpvI/AAAAAAAAAE8/PE6tmStyQI0 + /s200/ + IMG_2688.JPG
	 	return $thpath;
	 }

	 /* Permet d'obtenir le chemin d'accès de la moyenne.  Ex:  photos/2006/toto/vignettes/toto.jpg
	  * Fonctionne avec les images stockée sur picasa web
	  */
	 function getLienMoyennePicasa($imagePath){
	 	$nomSimplifie = basename($imagePath);
		$nomDossier = dirname($imagePath);  // obtient le nom du dossier. Ex: photos/2006/toto/toto.jpg devient photos/2006/toto
	 	$thpath = $nomDossier.'/s640/'.$nomSimplifie; // chemin d'accès de la vignette: photos/2006/toto + /vignettes/ + toto.jpg
	 	return $thpath;
	 }
	
	/* Permet d'obtenir le chemin d'accès de la vignette.
	 * Fonctionne avec les images stockées sur facebook
	 * Dans la doc on trouve... Ex:  http://photos-e.ak.fbcdn.net/hphotos-ak-snc1/hs009.snc1/2867_91126615465_684590465_2452060_3051061_n.jpg  // _t.jpg _n.jpg _s.jpg
	 * Mais en fait... c'est pas ça !
	 * On a: http://photos-e.ak.fbcdn.net/photos-ak-snc1/v1103/81/65/684590465/n684590465_1702260_2953.jpg  // et c'est le /n.... .jpg ou /t...jpg  ou /s....jpg
	 *
	 * début septembre, il semble que le format de nom de fichier facebook a changé !
	 * http://photos-a.ak.fbcdn.net/hphotos-ak-snc1/hs225.snc1/7222_147871525465_684590465_3402912_667163_n.jpg
	 * http://photos-a.ak.fbcdn.net/hphotos-ak-snc1/hs225.snc1/7222_147871525465_684590465_3402912_667163_s.jpg
	 */
	 function getLienVignetteFacebook($imagePath){
	 //	$thpath = str_replace("/n", "/s", $imagePath);
		$thpath = str_replace("_n", "_s", $imagePath);
	 	return $thpath;
	 }
	
	/* Permet de créer une vignette dans le dossier vignettes/ se trouvant à la même hauteur que l'image source.
	 * 
	 * @return: rien de particulier.. :P
	 * @param: $imagePath => le chemin d'accès de la photo sur le serveur. (pas une url)
	 */
	function creeVignette($imagePath,$width=160, $height=120, $quality=85){
		$nomDossier = dirname($imagePath);  // obtient le nom du dossier. Ex: images/photos/2006/toto/toto.jpg devient images/photos/2006/toto  ou  utile/divers/2008_11_16_22_07_toto.jpg  => utile/divers
		// si le dossier "vignettes" n'existe pas le crée
		if (!file_exists($nomDossier.'/vignettes')){
        	mkdir($nomDossier.'/vignettes', 0777);
 		}
		$thpath = $this->getLienVignette($imagePath);
		
		$commande = "/usr/local/bin/convert -thumbnail ".$width."x".$height." -quality ".$quality." \"".$imagePath."\" \"".$thpath."\"";
		exec($commande);
	}
	
	
	/* Permet de créer une moyenne dans le dossier moyennes/ se trouvant à la même hauteur que l'image source.
	 * 
	 * 
	 * @return: rien de particulier.. :P
	 * @param: $imagePath => le chemin d'accès de la photo sur le serveur. (pas une url)
	 */
	function creeMoyenne($imagePath,$width=640, $height=480, $quality=85){
		$nomDossier = dirname($imagePath);  // obtient le nom du dossier. Ex: images/photos/2006/toto/toto.jpg devient images/photos/2006/toto
		// si le dossier "moyennes" n'existe pas le crée
		if (!file_exists($nomDossier.'/moyennes')){
        	mkdir($nomDossier.'/moyennes', 0777);
 		}
		$thpath = $this->getLienMoyenne($imagePath);
		
		$commande = "/usr/local/bin/convert -thumbnail ".$width."x".$height." -quality ".$quality." \"".$imagePath."\" \"".$thpath."\"";
		exec($commande);
	}
	
	/* Permet de créer une vignette de taille perso dans le dossier perso/ se trouvant à la même hauteur que l'image source.
	 * Cette fonction est principalement utilisée pour insérer des images dans des documents. Ainsi on garanti que les dossiers vignettes et moyennes contiennent toujours des images de même taille. C'est plus facile pour afficher une galerie
	 * 
	 * @return: rien de particulier.. :P
	 * @param: $imagePath => le chemin d'accès de la photo sur le serveur. (pas une url)
	 */
	function creePerso($imagePath,$width=320, $height=240, $quality=85){
		$nomDossier = dirname($imagePath);  // obtient le nom du dossier. Ex: images/photos/2006/toto/toto.jpg devient images/photos/2006/toto
		// si le dossier "perso" n'existe pas le crée
		if (!file_exists($nomDossier.'/perso')){
        	mkdir($nomDossier.'/perso', 0777);
 		}
		$thpath = $this->getLienPerso($imagePath);
		
		// si la taille n'est pas choisie fourni un taille par défaut.
		if (empty($width)) {
			$width=320;
			$height=240;
		}
		$commande = "/usr/local/bin/convert -thumbnail ".$width."x".$height." -quality ".$quality." \"".$imagePath."\" \"".$thpath."\"";
		exec($commande);
	}
	
	/* Retourne les données d'une photo jpg en fonction du flag exif orientation.
	 *
	 * Utilise jpgetran pour la rotation sans perte de la photo
	 * Utilise exiftool pour modifier le flag exif orientation après avoir effectué la rotation. http://www.sno.phy.queensu.ca/~phil/exiftool/
	 * 
	 * Cette fonction effectue une rotation sans perte. Les données sont réarrangée, mais pas recompressée.
	 * Cette fonction conserve les métadonnées. (exif, iptc, commentaires, jffif, xmp, etc..)
	 * Cette fonction ne retourne PAS la vignette exif. Elle reste dans la position originale. Cela influence la vue des vignettes dans graphicConverter, l'aperçu en mode colonne du finder.
	 * Cette fonction recrée un fichier, donc elle modifie la date de création et modification du fichier. (c'est à cause de jpegtran. Exiftool est souple à ce niveau)
	 *
	 * Les commandes utilisées sont sur le principe:
	 * jpegtran -copy all -rotate 270 -verbose -outfile girafe2.jpg girafe.jpg
     * exiftool -orientation=1 -n -overwrite_original_in_place girafe.jpg
	 *
	 * @return: rien de particulier.. :P
	 * @param: $imagePath => le chemin d'accès de la photo sur le serveur. (pas une url)
	 */
	function rotateJpgFromExifOrientation($imagePath){
		
		// vérifie que cette installation de php supporte la lecture des informations exif
		if (function_exists('exif_read_data')) {
			$exif = exif_read_data($imagePath);  //todo ne fonctionne qu'avec le jpg et le tif.. donc bug avec le png
			 // todo: bug quand exif n'est pas là... Warning: exif_read_data(dsc_1783.jpg) [exif_read_data]: Process tag(x0000=UndefinedTa): Illegal pointer offset(x4E20434F + x4E494B4F = x9C698E9E > x02C3) in /Users/mdespont/Sites/yopyop/include/manager/photoManager.php on line 468
		}else{
			$exif = false;
		}		
		
		if ($exif!=false) {
			
			$orientation = ''; // pour tout les cas non prévu, ou la cas orientatio=1 qui ne demande pas de modification.
			$orientation = $exif['Orientation'];
									
			// si orientation = 8 => retourne de 270
			if ($orientation=='8') {
				$angle = "270";
			// si le champ = 6 => retourne de 90
			}elseif ($orientation=='6') {
				$angle = '90';
			}else {
				$angle = '';
			}
						
			if (!empty($angle)) {
			
				$nomDossier = dirname($imagePath);  // obtient le nom du dossier. Ex: images/photos/2006/toto/toto.jpg devient images/photos/2006/toto
				$nomFichier = basename($imagePath);
				$outpoutPath = $nomDossier."/temp_".$nomFichier;

				// retourne la photo
				$commandeRotation = "/usr/local/bin/jpegtran -copy all -rotate ".$angle." -outfile ".$outpoutPath." ".$imagePath; // on suppose que jpegtran est dans le path

			//	echo "<br> commande rotation: ", $commandeRotation;

				$output = array();
				$return_var = 0;
				exec($commandeRotation, $output, $return_var);

				// print_r($output);
				// 		echo "statut: ",$return_var;

				// place le flag orientation à 1. Ce qui signifie que le flux de donnée est correct. Pas besoin de modifier l'orientation à la volée
				// cette option ne sert à rien pour le site web en lui même, mais elle est utile si des gens téléchargent les photos depuis le site web pour les utiliser en local.

				$commandeFlagExif = "/usr/bin/exiftool -orientation=1 -n -overwrite_original_in_place ".$outpoutPath;  // -overwrite_original_in_place permet de garder l'icon et les ressources Mac. Si on est sur linux, utiliser: overwrite_original qui est moins gourmand

		//		echo "<br> commande flag: ", $commandeFlagExif;


				$output = array();
				$return_var = 0;
				exec($commandeFlagExif, $output, $return_var);

				// print_r($output);
				// 		echo "statut: ",$return_var;

				// renommer le fichier temp_photo.jpg  en photo.jpg
				rename($outpoutPath,$nomDossier."/".$nomFichier);	
			}
		}
	}
	
	
	/* Permet de supprimer ou modifier tout les caractères qui pourraient poser des
	 * problèmes lors de leur utilisation comme nom de fichier.
	 * Il s'agit du remplacement des caractère accentué par leur équivalent non accentué. (fonctionne en utf-8)
	 * Du remplacement des espaces par des _
	 * Du remplacement des ' par des _
	 *
	 * @return: string le nom du fichier simplifié
	 * @param: $nomFichier string le nom du fichier que l'on veut simplifier
	 */
	function simplifieNomFichier($nomFichier){
		// enlève les accents
		$a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ'; 
	    $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr'; 
	    $nomFichier = utf8_decode($nomFichier);     
	    $nomFichier = strtr($nomFichier, utf8_decode($a), $b); 
	    $nomFichier = strtolower($nomFichier); 
	    $nomFichier = utf8_encode($nomFichier);	

		// remplace les espaces par des _
		$nomFichier = preg_replace("/\s/","_",$nomFichier);
		// supprim les antislashes d'échappement des '
		$nomFichier = stripslashes($nomFichier);
		// Remplace les apostrophes par des _
		$nomFichier = preg_replace("/\'/","_",$nomFichier);

		return $nomFichier;
	}
	
	
	/* Indexe une photo dans la base de donnée
	 * Cette fonction regroupe plusieurs autres pour aller chercher nombre d'information dans le fichier de la photo
	 * et introduit les données récoltées pour en créer un nouvel objet "photo".
	 * De plus crée les vignette et moyenne.
	 * (ne s'occupe pas de tagger la photo, car il faudrait que photoManager puisse avoir accès à une instance de groupeManager.. ce qui complique pour rien)
	 *
	 * Attentin de le chemin d'accès du dossier fourni doit être accessible en écriture... la fonction ne vérifie pas cette condition
	 * @return: l'id de la novuelle photo crée
	 * @param: $folderPath string chemin du dossier de la photo. Doit être terminé par un /
	 */
	function indexPhotoFromFile($folderPath,$nomOriginal){

		// simplifie le nom du fichier
		$nomSimplifie = $this->simplifieNomFichier($nomOriginal);
		
		// renome le fichier dans le dossier avec un nom simplifié
		// ici on part du principe que 2 fichiers ne peuvent avoir le même nom donc on n'ajoute pas de préfixe date au nom de fichier. Cependant, dans des cas tordus bien précis. Il peut arriver qu'après renommage les noms de fichier soit les même. Ex. là bas.jpg et la bas.jpg deviennent les 2: la_bas.jpg après renommage !!. Cependant, le risque est faible vu que c'est principalement mes propres photos qui vont être indexées de la sorte et que je ne les renomme pas. Dans un même dossier nous aurons donc que des photos du type: IMG_7774.jpg
		rename($folderPath.$nomOriginal,$folderPath.$nomSimplifie);
		$imagePath = $folderPath.$nomSimplifie;
		
		$mime = $this->getTypeMime($imagePath);  // dans le genre image/jpeg ou image/png
		if ($mime=="image/jpeg") {
			// Au besoin, retourne la photo et modifie le flag exif pour que la photo brute affichée par le navigateur soit orientée correctement.
			$this->rotateJpgFromExifOrientation($imagePath);
		}
		
		// obtient diverses infos
		$datePriseDeVue = $this->getExifDateFromFile($imagePath);
		$position = $this->getExifLatLongFromFile($imagePath);
		$latitude = $position['latitude'];
		$longitude = $position['longitude'];
		
		$orientation = $this->getOrientationFromFile($imagePath);  // en fonction des dimension de la photo
		
		
		$this->creeVignette($imagePath,250,188);
		$this->creeMoyenne($imagePath); // et la taille et qualité par défaut 640,480,85
		
		// introduit dans la base une nouvelle photos et retourne le nouvel id créé
		$idPhoto = $this->insertPhoto($nomOriginal,'',$imagePath,'0',$orientation,$datePriseDeVue,$latitude,$longitude,'0');
		return $idPhoto;
	}
	
} // photoManager
?>
