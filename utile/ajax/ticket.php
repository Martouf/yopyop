<?php
	/*******************************************************************************************
	 * Nom du fichier		: ticket.php
	 * Date					: 22 janvier 2009
	 * Auteur				: Mathieu Despont
	 * Adresse E-mail		: mathieu@ecodev.ch
	 * But de ce fichier	: fournit un ticket pour le système anti-spam du formulaire
	 *******************************************************************************************
	 * Ce script fourni une image qui contient un cookie servant de ticket à l'anti-spam.
	 * Cet anti-spam se base sur le principe qu'un robot ne prend pas la peine de charger les images vu qu'il est aveugle. Donc il ne prend pas non plus le ticket!
	 *
	 * L'image est a inclure avec le code <img src="/utile/ajax/ticket.php?get" width="1" height="1" alt="transparent" />
	 * Cette image charge un cookie avec le numéro d'un ticket placé dans la base de donnée. Le formulaire vérifie l'existence de ce ticket avant d'accepter le POST.
	 *
	 */
	
	require_once('../../include/init/init.php');
	new Init(); //initialise l'application en créant les objets utiles
	
	// nom du cookie d'authentification
	$authCookieName = 'ticket_commentaire';
	
	$domain = $_SERVER['HTTP_HOST'];
	if (preg_match("/([^\.]+\.[a-z]{2,4})$/",$domain,$match)) {
	        $domain = $match[1]; // ne conserve que le domaine de second niveau (mondomaine.ch)
	}
	$domain = '.'.$domain; // notation ".mondomaine.ch" pour couvrir tous les sous-domaines
	
	// Domain cannot be ".localhost" because it must contains at least two dots, see http://www.faqs.org/rfcs/rfc2109.html
	if (substr_count($domain, '.') < 2)
		$domain = null;
	
	// validité en secondes du cookie d'authentification
	$authCookieLifetime = 3600;
	
	// crée le ticket dans la base de donnée
	$ticket_id = $commentaireManager->addTicket();
	
	// crée un cookie avec les infos crées ci-dessus
	setcookie($authCookieName,$ticket_id,time()+$authCookieLifetime,'/',$domain);
	
	// envoie une image
	
	header('Content-Type: image/png');
	header('Content-Length: '.filesize('../img/transparent.png'));
	readfile('../img/transparent.png');


?>