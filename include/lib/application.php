<?php

/**
 * Gère la localisation
 *  
 * Suivant la langue détectée ou voulue, il y a un fichier correspondant à la langue de type lang_fr.php qui est chargé.
 * Ce fichier contient un tableau global avec les chaines de caractères localisées. 
 * EX: L'appel à cette fonction  __('merci') retourne thanks ou danke...
 *
 */
function __($string){
	if (isset($GLOBALS['__l10n'][$string]))
		return $GLOBALS['__l10n'][$string];
	else
		return $string;
}
/**
 * Gère la localisation
 *  Cette fonction a un usage identique à __ à la différence qu'elle n'est utilisée que par la fonction traduit() qui traduit uniquement
 *  le contenu de tableau entier grâce à la fonction array_recursive_walk
 *
 */
function traduction(&$string){
	if (isset($GLOBALS['__l10n'][$string]))
		$string = $GLOBALS['__l10n'][$string];
}

/**
 * retourne le nom du jour de la semaine pour la date fournie au format datetime ou date
 * 2007-01-07 20:47:50 => jeudi ?
 * puis... traduit le nom du jour 
 */
function jourSemaine($date){
	if (!empty($date)) {
		$date = __(date('l',strtotime($date)));
		return $date;
	}else{
		return '';
	}
}

/**
 * Converti une date au format mysql datetime dans un format compréhensible pour l'humain
 * 2007-01-07 20:47:50 => 7 jan 2007 : 20:47
 * puis... traduit le nom du mois 
 */
function dateTime2Humain($date){
	if (!empty($date)) {
		$date = date('j ',strtotime($date)).__(date('M',strtotime($date))).date(' Y : H:i',strtotime($date));
		return $date;
	}else{
		return '';
	}
}

/** ===
 * Converti une date au format mysql datetime dans un format compréhensible pour l'humain et en format texte
 * 2007-01-07 20:47:50 => 7 jan 2007 à 20h47  // d=07 j=7
 * puis... traduit le nom du mois 
 */
function dateTime2HumainTexte($date){
	if (!empty($date)) {
		$date = date('d ',strtotime($date)).__(date('M',strtotime($date))).date(' Y à H\hi',strtotime($date));
		return $date;
	}else{
		return '';
	}
}

/**
 * retourne uniquement l'heure
 * Converti une date au format mysql datetime dans un format compréhensible pour l'humain
 * 2007-01-07 20:47:50 => 20:47
 * puis... traduit le nom du mois 
 */
function dateTime2HeureHumain($date){
	if (!empty($date)) {
		$date = date('H:i',strtotime($date));
		return $date;
	}else{
		return '';
	}
}

/**
 * retourne uniquement la date
 * Converti une date au format mysql datetime dans un format compréhensible pour l'humain
 * 2007-01-07 20:47:50 => 7 jan 2007
 * puis... traduit le nom du mois 
 */
function dateTime2DateHumain($date){
	if (!empty($date)) {
		$date = date('j ',strtotime($date)).__(date('M',strtotime($date))).date(' Y',strtotime($date));
		return $date;
	}else{
		return '';
	}
}

	/**===
	 * retourne une date avec le jour de la semaine, le mois et l'heure humaine.
	 * 2009-05-07 17:00:00 => Je 7 mai, 15h   (on affiche pas le 00 des heures pleines et on ne met pas de 0 avant les chiffre)
	 * puis... traduit le nom du mois et le jour de la semaine
	 * pour le jour de la semaine entier =>l => D pour seulement le début
	 */
	function dateTime2JourSemaineDateMoisHeureHumaine($date){
		if (!empty($date)) {
			$newDate = __(date('D',strtotime($date))).date(' j ',strtotime($date)).__(date('F',strtotime($date))).date(', G',strtotime($date)).'h';
			$minutes = date('i',strtotime($date));
			if ($minutes!='00') {
				$newDate.= $minutes;
			}
			return $newDate;
		}else{
			return '';
		}
	}


	/**===
	 * retourne une date humaine avec le jour de la semaine, le mois mais PAS l'heure.
	 * 2009-05-07 17:00:00 => Jeudi 7 mai
	 * puis... traduit le nom du mois et le jour de la semaine
	 * pour le jour de la semaine entier =>l => D pour seulement le début
	 */
	function dateTime2JourSemaineDateMoisHumaine($date){
		if (!empty($date)) {
			$newDate = __(date('l',strtotime($date))).date(' j ',strtotime($date)).__(date('F',strtotime($date)));
			return $newDate;
		}else{
			return '';
		}
	}

	/**===
	 * retourne une date humaine avec le jour de la semaine en minuscule et 2 lettre, le mois mais PAS l'heure.
	 * 2009-05-07 17:00:00 => je 7 mai
	 * puis... traduit le nom du mois et le jour de la semaine
	 * pour le jour de la semaine entier =>l => D pour seulement le début
	 */
	function dateTime2JourSemaineSimpleDateMoisHumaine($date){
		if (!empty($date)) {
			$newDate = strtolower(__(date('D',strtotime($date)))).date(' j ',strtotime($date)).__(date('F',strtotime($date)));
			return $newDate;
		}else{
			return '';
		}
	}

	/**===
	 * retourne uniquement l'heure humaine simplifiée
	 * 2009-05-07 17:00:00 => 15h   (on affiche pas le 00 des heures pleines et on ne met pas de 0 avant les chiffre)
	 */
	function dateTime2HeureHumaineSimplifiee($date){
		if (!empty($date)) {
			$newDate = date('G',strtotime($date)).'h';
			$minutes = date('i',strtotime($date));
			if ($minutes!='00') {
				$newDate.= $minutes;
			}
			return $newDate;
		}else{
			return '';
		}
	}


// convertit un datetime (type 'date') de MySQL en un int unix utilisable par date() de PHP
// 2007-12-17 10:35:00 => 1197884100
function datetime2unixstamp($datetime) {
	//sépare la date et l'heure
	$dateComplete = explode(" ",$datetime);
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
	
	// mktime = heure, minutes, secondes, mois, jour, année
	return mktime($heures,$minutes,$secondes,$mois,$jour,$annee);
}


/**
 * va récursivement supprimer les \ des tableaux.
 * 
 */
function stripslashes_deep(&$value){
    $value = is_array($value) ?
                array_map('stripslashes_deep', $value) :
                stripslashes($value);

    return $value;
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
function simplifieNom($nomFichier){
	// enlève les accents
	$a = 'ÀÁÂÃÄÅÆÇÈÉÊËÌÍÎÏÐÑÒÓÔÕÖØÙÚÛÜÝÞßàáâãäåæçèéêëìíîïðñòóôõöøùúûýýþÿŔŕ'; 
    $b = 'aaaaaaaceeeeiiiidnoooooouuuuybsaaaaaaaceeeeiiiidnoooooouuuyybyRr'; 
    $nomFichier = utf8_decode($nomFichier);     
    $nomFichier = strtr($nomFichier, utf8_decode($a), $b); 
    $nomFichier = strtolower($nomFichier); 
    $nomFichier = utf8_encode($nomFichier);	

	// remplace les espaces par des _
	$nomFichier = preg_replace("/\s/","-",$nomFichier);
	// supprim les antislashes d'échappement des '
	$nomFichier = stripslashes($nomFichier);
	// Remplace les apostrophes par des _
	$nomFichier = preg_replace("/\'/","-",$nomFichier);

	return $nomFichier;
}


/* 
 * Protège les e-mail d'être aspiré par des spammeurs
 * cette fonction recherche tout ce qui ressemble à un email en clair et le transforme
 * en fonction javascript qui sera semblable à un mailto mais qui n'est pas détectable
 * facilement par les robots.
 *
 */

function parseEmail($chaineSource){
	$mailPattern = "/(\<a href=\"mailto:[^\>]+\>)?([a-z0-9\._-]+)\@([a-z\.0-9-]+\.[a-z]{2,4})(\<\/a\>)?/i";
	
	$email = "<script type=\"text/javascript\" language=\"javascript\">email('$2','$3','',1);</script>";
	
   return  preg_replace($mailPattern,$email,$chaineSource);
}
	

	/**
	 *  Permet de créer une vignette avec gd
	 *
	 *  $imgpath => le chemin d'accès de l'image
	 *  $thpath => le chemin d'accès de la vignette
	 *  $new_w => la largeur maximale de la vignette
	 *  $new_h => la hauteur maximale de la vignette
	 *
	 * Les proportions de l'image originales sont gardées lors de la création de la vignette.
	 * Suivant l'orientation de la vignette, la surface de la photo ne va pas être la même.
	 * C'est la hauteur qui sera la même afin de pouvoir aligner des vignettes dans une galerie photo.
	 */
 function createThumb($imgpath,$thpath,$new_w=160,$new_h=120,$quality=85){
		// echo "traitement de l'image: ".$imgpath." pour ".$thpath;
		// echo "<br />taille: ".$new_w."x".$new_h; //ici

		$system=explode(".",$imgpath);
		if (preg_match("/jpg|jpeg/",$system[1])){$src_img=imagecreatefromjpeg($imgpath);}
		if (preg_match("/png/",$system[1])){$src_img=imagecreatefrompng($imgpath);}
		if (preg_match("/gif/",$system[1])){$src_img=imagecreatefromgif($imgpath);}
		$old_w=imageSX($src_img);
		$old_h=imageSY($src_img);
		if ($old_w > $old_h){
			$thumb_w=$new_w;
			$facteurEchelle = $old_h / $old_w;
			$thumb_h = round($new_w * $facteurEchelle);
		}
		if ($old_w < $old_h){
			$thumb_h=$new_h;
			$facteurEchelle = $old_w / $old_h;
			$thumb_w = round($new_h * $facteurEchelle);
		}
		if ($old_w == $old_h){
			$thumb_w=$new_w;
			$thumb_h=$new_h;
		}
		$dst_img=ImageCreateTrueColor($thumb_w,$thumb_h);
		imagecopyresampled($dst_img,$src_img,0,0,0,0,$thumb_w,$thumb_h,$old_w,$old_h);
		if (preg_match("/jpg/",$system[1])){
			imagejpeg($dst_img,$thpath,$quality); // la qualité par défaut est 75 mais ici on préfère 85
		}else if(preg_match("/png/",$system[1])) {
			imagepng($dst_img,$thpath);
		}else{
			imagegif($dst_img,$thpath);
		}
		imagedestroy($dst_img);
		imagedestroy($src_img);
	}

function creeVignette($imgpath, $thpath) {

		$quality=85;
		$imageInfo = getimagesize($imgpath);
		$width = $imageInfo[0] * THUMBNAIL_HEIGHT / $imageInfo[1];
		$height = THUMBNAIL_HEIGHT;
		$command = "/usr/local/bin/convert -thumbnail ".$width."x".$height." -quality $quality \"$imgpath\" \"$thpath\"";
		exec($command);
	}
	
	/**
	* @return string[]
	* @param string $url
	* @param string[] $parametres //liste des paramètres à extraire
	* @desc Retourne un tableau contenant les paramètres de l'URL avec pour indice le nom du paramètre. Si le paramètre demandé n'existe pas la valeur à son indice sera une chaîne vide.
	* http://www.phpcs.com/codes/RECUPERER-VARIABLES-URL-DANS-TABLEAU_36289.aspx
	* 1 nov 2008 modif: la fonction détecte aussi les paramètres vides. Elle retrourne le nom du paramètre. Ainsi on peut utiliser de url comme toto.php?update
	*/
	function parseUrl($url,$parametres=null){
		$i=0;
		$params=null;
		//on ne garde que les paramètres de l'URL
		$tmp_params=explode("?",$url);
		if (count($tmp_params)>=2)
			$tmp_params=$tmp_params[1];
		else
			$tmp_params="";
		if ($tmp_params!=""){
			$ancre=explode("#",$tmp_params);
			if (count($ancre)==2){
				$tmp_params=$ancre[0];
				$ancre=$ancre[1];
			}
			else
				$ancre="";
			//on isole les parametres
			$tmp_params=explode('&',$tmp_params);
			//On stocke dans un tableau les variables comprises dans l'URL.
			//format du tableau :
			//['id_lang'] => 1
			//['id_rub'] => 27
			//['id_art'] => 5
			foreach ($tmp_params as $param){
				$value=explode('=',$param);
				if (count($value)>=2){
					$params[$value[0]]=$value[1];
				}else{
					$params[$value[0]]='';  // supporte aussi les paramètres qui ont une valeur vide
				}
			}
			if ($parametres!=null){
				$tmp_params=null;
				//On stocke dans un tableau les variables comprises dans l'URL, demandées dans le tableau
				//passé en paramètre.
				//format du tableau :
				//['id_lang'] => 1
				//['id_rub'] => 27
				//['id_art'] => 5
				foreach ($parametres as $param){
					//si il y a une ancre de demandée on l'ajoute
					if ($param=='#')
						$params["#"]=$ancre;
					//si le parametre demandé n'existe pas on lui affecte une chaine vide.
					if (!isset($params[$param]))
						$params[$param]="";
					$tmp_params[$param]=$params[$param];
				}
				$params=$tmp_params;
			}

		}
	return $params;
}

///******************* Gestion des couleurs *************************//
// repris et adapté de: http://latosensu.org/articles/core/gestion-des-couleurs/index.php?part=6

/**
 * retourne une couleur rgb hexadécimal à partir de 3 valeurs décimale
 *
 * @return string la couleur en notation hexadécimale
 * @param int $r la valeur du rouge
 * @param int $r la valeur du vert
 * @param int $r la valeur du bleu
 */
function COLOR_rgb2hex( $r,$g = null,$b = null ){
    if ( is_array( $r ) && isset( $r['r'] ) ){
        $g = $r['g'];
        $b = $r['b'];
        $r = $r['r'];
    }

    return ( '#' . sprintf('%02x',$r). 
                   sprintf('%02x',$g). 
                   sprintf('%02x',$b));
}

/**
 * retourne un tableau avec les valeurs décimale des composante rgb d'une couleur à partir d'une notation hexadécimale
 *
 * @return array() un tableau avec les valeurs décimales de rgb
 * @param string la couleur en hexadécimal: (#ffaacc)
 */
function COLOR_hex2rgb( $h ){
    return ( array( 'r' => hexdec(substr($h, 1, 2 )),
                    'g' => hexdec(substr($h, 3, 2 )),
                    'b' => hexdec(substr($h, 5, 2 )) 
                  ) );
}

/**
 * retourne une couleur hsl décimal à partir de 3 valeurs rgb décimales
 *
 * @return array() un tableau avec les valeurs décimales de hsl
 * @param int $r la valeur du rouge
 * @param int $r la valeur du vert
 * @param int $r la valeur du bleu
 */
function COLOR_rgb2hsl( $r,$g = null,$b = null ){
    if ( is_array($r) && isset( $r['r'] )){
        $g = $r['g'];
        $b = $r['b'];
        $r = $r['r'];
    }

    $r = (int) $r / 255;              /* Let's first divide the values into percentages */
    $g = (int) $g / 255;              /* Let's first divide the values into percentages */
    $b = (int) $b / 255;              /* Let's first divide the values into percentages */

    $iMin   = min( $r,$g,$b );        /* What's the minimum of all three pigments */
    $iMax   = max( $r,$g,$b );        /* What's the maximum of all three pigments */
    $delta  = $iMax - $iMin;          /* Calculate delta */

    $h = $s = $l = 0;                 /* Store default values (starting from black) */

    $l = ( $iMin + $iMax) / 2;

    if ( $l > 0 ){
        $delta = $iMax - $iMin;

        $s = $delta;

        if ( $s > 0 ){
            $s /= ( $l <= 0.5 ) ? ( $iMax + $iMin ) : ( 2 - $iMax - $iMin );

            $r2 = ( $iMax - $r ) / $delta;
            $g2 = ( $iMax - $g ) / $delta;
            $b2 = ( $iMax - $b ) / $delta;

            if ( $r == $iMax ){
                $h = ( $g == $iMin ? 5 + $b2 : 1 - $g2 );
            
			}else if ( $g == $iMax){
                $h = ( $b == $iMin ? 1 + $r2 : 3 - $b2 );
            }else{
                $h = ( $r == $iMin ? 3 + $g2 : 5 - $r2 );
            }
            $h /= 6;
        }
    }

    if ( $h < 0 ){
        $h += 1;
    }

    if ( $h > 1 ){
        $h -= 1;
    }

    return (array( 'h' => round($h,3),
                   's' => round($s,3),
                   'l' => round($l,3)
                  ));
}

/**
 * retourne une couleur rgb décimale à partir de 3 valeurs hsl décimales
 *
 * @return array() un tableau avec les valeurs décimales de hsl
 * @param int $r la valeur de hue
 * @param int $r la valeur de saturation
 * @param int $r la valeur de luminosity
 */
function COLOR_hsl2rgb( $h,$s = null,$l = null ){
    if ( is_array( $h ) && isset( $h['h'] ) ){
        $s = $h['s'];
        $l = $h['l'];
        $h = $h['h'];
    }

    if ( $s == 0 ){
        $r = $l * 255;
        $g = $l * 255;
        $b = $l * 255;
    }else{
        if ( $l < 0.5 ){
            $var_2 = $l * ( 1 + $s );
        }else{
            $var_2 = ($l + $s) - ($s * $l);
        }

        $var_1 = 2 * $l - $var_2;
        $r = 255 * hue_2_rgb( $var_1,$var_2,$h + (1 / 3) );
        $g = 255 * hue_2_rgb( $var_1,$var_2,$h           );
        $b = 255 * hue_2_rgb( $var_1,$var_2,$h - (1 / 3) );
    }

    return ( array( 'r' => round( $r ),
                    'g' => round( $g ),
                    'b' => round( $b )
                  ) );
}

/**
 * crée une valeur utilisable en rgb à partir de la teinte d'une couleur hsl
 *
 * @return int la nouvelle valeur 
 * @param int $v1
 * @param int $v2
 * @param int $vh
 */
function hue_2_rgb( $v1,$v2,$vh ){
    if ( $vh < 0 ){
        $vh += 1;
    }

    if ( $vh > 1 ){
        $vh -= 1;
    }

    if (( 6*$vh ) < 1 ){
        return ($v1 + ( $v2 - $v1 ) * 6 * $vh );
    }

    if (( 2 * $vh ) < 1 ){
        return ( $v2 );
    }

    if (( 3 * $vh ) < 2 ){
        return ( $v1 + ( $v2 - $v1 ) * ((2 / 3 - $vh ) * 6 ));
    }

    return ( $v1 );
}

/**
 * retourne la couleur fournie avec une luminosité différente
 *
 * @return string la couleur rgb en notation hexadécimal à 6 digit. (#ff0033)
 * @param string $couleur la couleur de base en rgb notation hexadécimal à 6 digit. (#ff0033)
 * @param int $luminosite la quantité de lumière voulue dans la couleur. (entre 0 et 1)
 */
function getCouleurBordure($couleur, $luminosite=0.2){
	$couleurRgbDecimal = COLOR_hex2rgb($couleur);
	$couleurHsl = COLOR_rgb2hsl($couleurRgbDecimal);

	// on change la luminosité de la couleur
	$couleurHsl['l'] = $luminosite;

	// on change le mode de représentation des couleurs. On passe de hsl à rgb en notation hexadédimale.
	return COLOR_rgb2hex(COLOR_hsl2rgb($couleurHsl));
}	
	
	
?>
