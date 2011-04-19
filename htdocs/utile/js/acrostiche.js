/*******************************************************************************************
 * Nom du fichier		: acrostiche.js
 * Date					: 31 mars 2011
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu at martouf.ch
 * But de ce fichier	: pilote le service ajax de création d'acrostiche
 *******************************************************************************************
 * 
 *
 */

var app;  // variable globale qui représente mon application
var chemin = "/";

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
		
		// bouton de génération de l'acrostiche
		$('a#boutonGenererAcrostiche').click(function() {
			app.generateAcrostiche();
			return false;
		 });
			
	}); // ready
	
	
	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){
				
		/*
		 *  fonction de lancement de la génération d'acrostiche
		 */
		this.generateAcrostiche = function(){
			var texte = $('#prenom').val();
			var url = chemin+"/utile/ajax/acrostiche.php";
			$.post(url,{'texte':texte},app.updatedAcrostiche);
		}

		/*
		 *  ajoute le nouvel acrostiche généré à la suite.
		 */
		this.updatedAcrostiche = function(acro){

			message = acro+'<hr />';
			$('#resultatAcrostiche').append(message);
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