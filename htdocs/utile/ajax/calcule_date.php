<?php
	/*******************************************************************************************
	 * Nom du fichier		: cacule_date.php
	 * Date					: 11 avril 2008
	 * Auteur				: Mathieu Despont
	 * Adresse E-mail		: mathieu@ecodev.ch
	 * But de ce fichier	: assurer service qui fournit des caclul sur des dates
	 *******************************************************************************************
	 * Ce fichier est inclu par ajax.
	 *
	 */
	
	require_once('../../../include/init/init.php');
	new Init(); //initialise l'application en créant les objets utiles
	
	// obtient la date sur laquelle on va travailler
	$date = date('Y-m-d H:i:s'); // par défaut, aujourd'hui
	if (isset($_GET['date'])) {
		$date = $_GET['date'];
	}
	
	// obtient le format de sortie désiré
	// par défaut le format de sortie est au type datetime de mysql
	$formatSortie = 'Y-m-d H:i:s';
	if (isset($_GET['format'])) {
		$formatSortie = $_GET['format'];
	}
	
	// obtient la requête désirée
	// Ce sont des calculs effectués avec la fonction strtotime.
	// Ex: last Monday, + 3 day, + 4 Month, next Friday
	$requete = '';
	if (isset($_GET['requete'])) {
		$requete = $_GET['requete'];
	}

	// calcule une date en fonction de la requête et fourni le résultat sous forme de string au format demandé
	$dateSortie = date($formatSortie, strtotime($date.' '.$requete));
	echo $dateSortie;

?>