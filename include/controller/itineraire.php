<?php
/*******************************************************************************************
 * Nom du fichier		: itineraire.php
 * Date					: 6 avril 2009
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Ce fichier est le controlleur d'un programme d'aide à la création d'itinéaire de marche.
 *******************************************************************************************
 * Permet d'afficher une carte et d'obtenir des infos d'altitude et de coordonnée.
 * 
 */

// quelques scripts utiles
$additionalHeader = "
	<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.pack.js\"></script>
	<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/interface.js\"></script>
	<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/jquery.autocomplete.js\"></script>
	<script src=\"http://maps.google.com/maps?file=api&v=2.x&key=".$googleMapsKey."\" type=\"text/javascript\"></script>
	<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/wms236.js\"></script>
	<script type=\"text/javascript\" src=\"http://".$_SERVER['SERVER_NAME']."/utile/js/itineraire.js\"></script>";	
$smarty->assign('additionalHeader',$additionalHeader);

// permet de choisir le thème dans lequel on veut inclure le contenu. Si le thème=="no". On affiche que le code html du contenu. Ceci permet de l'inclure par ajax dans un div sans avoir l'entête.
if ($theme=="no") {
	$smarty->display("itineraire_".LANG.".tpl"); // affichage de l'interface vide qui permet d'indiquer le nom d'un dossier du serveur.
}else{
	// affiche le formulaire de modification inclu dans le template du thème index.tpl
	$smarty->assign('contenu',"itineraire_".LANG.".tpl");
	$smarty->display($theme."index.tpl");
}
	
?>