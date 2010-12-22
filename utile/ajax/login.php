<?php
/*******************************************************************************************
 * Nom du fichier		: login.php
 * Date					: 11 novembre 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@ecodev.ch
 * But de ce fichier	: Permet de s'identifier
 *******************************************************************************************
 * Ce fichier est inclu par ajax.
 * http://yopyop.ch/utile/ajax/login.php?logout
 *
 */
	
	require_once('../../include/init/init.php');
	new Init(); //initialise l'application en créant les objets utiles
	
	if (isset($_POST['pseudo']) && isset($_POST['password'])) {
		
		$pseudo = $_POST['pseudo'];
		$motDePasse = $_POST['password'];
		
		// va chercher l'id de la personne qui correspond au login mot de passe donné.
		// retourne '1' si le login échoue. (1 est l'id correspondant à un inconnu)
		// Place dans la session: id_personne, pseudo et rang. (et le mot de passe, pour regénérer un accès avec des droit identique pour générer les pdf)
		// créer un cookie de longue durée pour se réauthentifier.
		$personneManager->getIdUserFromLogin($pseudo,$motDePasse);
	}
	
	// déconnection  // juste après un logout... le paramètre logout est toujours là !!
	if (isset($_REQUEST['logout'])) {
		session_unset();
		session_destroy();
		session_start();
		$_SESSION['id_personne'] = "1";
		// crée un cookie qui se détruit en 3 secondes. Comme ça on est certain d'avoir détruit le cookie
		setcookie("yopyop","1",time()+3,"/");
	}
	
	
			// affiche très brièvement une page dont le seul but est d'appeler le script qui va insérer le contenu fourni dans tinymce. Puis le popup se ferme.
			$htmlDebut = <<<END

				<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
					"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

				<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
					<head>
						<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
						<link type="text/css" rel="stylesheet" href="http://koudou.ch/utile/css/koudou.css" media="screen" />
						

END;
			echo $htmlDebut;
			// echo "<script type=\"text/javascript\">\n";			
			// echo "history.back()";
			// echo "</script>\n";
			echo "<title>Login</title></head><body>";
			echo "<div id=\"boiteDialogue\" style=\"display: block !important;\">";
			if (isset($_REQUEST['logout'])) {
				echo "<h3 class=\"ok\">Au revoir et à bientôt !</h3>";
			}else{
				echo "<h3 class=\"ok\">Identification effectuée avec succès!</h3>";
			}
			echo "<a href=\"javascript:history.back()\">retour à la page</>";
			echo "</div>";
			echo "</body></html>";

?>