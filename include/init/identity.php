<?php
/*******************************************************************************************
 * Nom du fichier     : identity.php
 * Date		     	  : 18 novembre 2008
 * @author	     	  : Mathieu Despont
 * Adresse E-mail     : mathieu@marfaux.ch
 * But de ce fichier  : détection de l'utilisateur
 *******************************************************************************************
 * 
 *
 *   
 * 
 */
	// identification de l'utilisateur
	$personneManager->getIdentity($parametreUrl);
	$smarty->assign('sessionIdPersonne',$_SESSION['id_personne']);
	
	// si ce n'est pas un inconnu
	if ($_SESSION['id_personne']!=1) {
		$smarty->assign('sessionPseudo',$_SESSION['pseudo']);
		$smarty->assign('sessionRang',$_SESSION['rang']);
	}
	

?>