/*******************************************************************************************
 * Nom du fichier		: statut.js
 * Date					: 1er janvier 2009
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu at ecodev.ch
 * But de ce fichier	: Fournir les fonctions utiles pour l'utilisation des formulaires d'ajout et modification de statut
 *******************************************************************************************
 * 
 */

var app;  // variable globale qui représente mon application


jQuery.noConflict();
(function($) { 
  $(function() {

	// morceau de script qui est évalué à chaque requête. Il permet de lier du code javascript avec les éléments html
	$(document).ready(function(){
		app = new Application();

		// montre ou cache un loading en bas à droite de la page
		$("#loading").ajaxStart(function(){
		  $(this).show('fast');
		});
		$("#loading").ajaxStop(function(){
		  $(this).slideUp('slow');
		});

		// create statut pour la page de création de statut
		$('a#createStatut').click(function() {
			app.createStatut();
			return false;
		 });
		
		// update statut
		$('a#saveStatut').click(function() {
			app.saveStatut();
			return false;
		 });
		

	}); // ready


	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){


		///////////////  Création d'un statut ///////////
		
		/*
		 *  appelé au clic de la fonction de sauvegarde. Cette fonction permet d'ajouter un statut
		 */
		this.createStatut = function(e){
			
			var nom = $('#inputNom').val();
			
			// url de création d'un événement
			var url = chemin+"statut/statut.html?add";
			
			// url , param, fonction de callback
			$.post(url,{'nom':nom},app.goToStatut);
			
		}

		/*
		 *  redirige le visiteur sur la page du statut dont on passe l'id en paramètre
		 */
		this.goToStatut = function(id){
			//echo ("redirection....");
			url = "/statut/"+id+"-statut.html";
			//echo (url);
			window.location.href = url;
		}


		///////////// mise à jour d'un statut ///////////

		/*
		 *  appelé au click du liens de sauvegarde d'un statut. Cette fonction met à jour le statut.
		 */
		this.saveStatut = function(e){

			var idStatut = $('#idStatut').val();
			var nom = $('#inputNom').val();
	
			// update l'évenement dans la base de donnée
			var url = chemin+"statut/"+idStatut+"-statut.html?update";
			
			// url , param, fonction de callback
			$.post(url,{'id':idStatut,'nom':nom,});
		}


	} // Application


  });
})(jQuery);


function echo(str){
	try{
		console.log(str);
	}
	catch(e){alert(str)}
}