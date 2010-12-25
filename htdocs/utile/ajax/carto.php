<?php
	/*******************************************************************************************
	 * Nom du fichier		: carto.php
	 * Date					: 11 février 2008
	 * modif				: 5 février 2009 => intégration dans yopyop
	 * Auteur				: Mathieu Despont
	 * Adresse E-mail		: mathieu@marfaux.ch
	 * But de ce fichier	: fournir quelque services de conversion et de calcul
	 *******************************************************************************************
	 * Ce fichier est conçu pour être un service ajax 
	 * Il sert à:
	 *  - convertir des coordonnées WGS84 et CH1903 dans un sens et dans l'autre.
	 *  - fournir la distance entre 2 points fourni au format WGS84
	 *  - fournir l'altitude d'un point selon le service web de geonames.org sous licence creative Commons cc-by.
	 * 
	 * exemple avec les coordonnées de la maison...
	 *  Latitude: 46.94684921584373
	 *	Longitude: 6.831650733947754
	 *
	 */
	
	require_once('../../../include/init/init.php');
	new Init(); //initialise l'application en créant les objets utiles
	
	// récupère les coordonnées lat lng
	if (isset($_GET['lat'])) {
		$latitude = $_GET['lat'];
	}else{
		$latitude = '';
	}
	
	if (isset($_GET['lng'])) {
		$longitude = $_GET['lng'];
	}else{
		$longitude = '';
	}
	
	// récupère une seconde coordonnée
	if (isset($_GET['lat2'])) {
		$latitude2 = $_GET['lat2'];
	}else{
		$latitude2 = '';
	}
	
	if (isset($_GET['lng2'])) {
		$longitude2 = $_GET['lng2'];
	}else{
		$longitude2 = '';
	}
	
	if (isset($_GET['x'])) {
		$x = $_GET['x'];
	}else{
		$x = '';
	}
	
	if (isset($_GET['y'])) {
		$y = $_GET['y'];
	}else{
		$y = '';
	}

	if (isset($_GET['action'])) {
		$action = $_GET['action'];
	}else{
		$action = '';
	}
	
	// demande l'altitude
	if ($action == 'altitude') {
		echo $lieuManager->getAltitude($latitude,$longitude);
		
	}elseif ($action == 'wgs84toch1903') {
		$coordonneeCH = $lieuManager->getCoordonneeCH1903($latitude,$longitude);
		echo $coordonneeCH['y']." / ".$coordonneeCH['x'];

	}elseif ($action == 'ch1903towgs84') {
		$coordonneeWGS = $lieuManager->getCoordonneeWGS84($x,$y);
		echo $coordonneeWGS['lat'].",".$coordonneeWGS['long'];
	
	}elseif ($action == 'distance') {
		// distance PP => sablons http://mathieu.ecodev.ch/map/carto.php?action=distance&lat=46.9897086391609&lng=6.929326057434082&lat2=46.99559235540579&lng2=6.931257247924805
		echo  round($lieuManager->getDistance($latitude,$longitude,$latitude2,$longitude2));
	}

?>