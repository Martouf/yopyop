/*******************************************************************************************
 * Nom du fichier		: historique.js
 * Date					: 5 juin 2008 pour la partie machinerie google
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu (at) ecodev.ch
 * But de ce fichier	: Fournir la partie javascript de manipulation de historiques
 *******************************************************************************************
 * 
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
		
		// charge le tri sur le tableau d'événements. Uniquement si le tableau est présent.
		if (document.getElementById("listeHistoriques") != null) {
			 $("#listeHistoriques").tablesorter(); 
			
			// charge le filtre du tableau
			var theTable = $('#listeHistoriques');

			  // theTable.find("tbody > tr").find("td:eq(1)").mousedown(function(){
			  //   $(this).prev().find(":checkbox").click()
			  // });

			  $("#filter").keyup(function() {
			    $.uiTableFilter(theTable, this.value);
			  })

			  $('#filter-form').submit(function(){
			    theTable.find("tbody > tr:visible > td:eq(1)").mousedown();
			    return false;
			  }).focus(); //Give focus to input field
			
		};
		
		
	}); // ready
	
	
	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){
		
		
	} // Application
  });
})(jQuery);

