/*******************************************************************************************
 * Nom du fichier		: mesure.js
 * Date					: 19 avril 2011
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu (at) ecodev.ch
 * But de ce fichier	: Fournir la partie javascript de manipulation de mesures
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
		if (document.getElementById("listeMesures") != null) {
			 $("#listeMesures").tablesorter(); 
			
			// charge le filtre du tableau
			var theTable = $('#listeMesures');

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
		
		// create mesure pour la page de création de mesure
		$('a#createMesure').click(function() {
			app.createMesure();
			return false;
		 });
		
	}); // ready
	
	
	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){
		///////////////  Création d'une mesure ///////////
		
		/*
		 *  appelé au clic de la fonction de sauvegarde. Cette fonction permet d'ajouter une mesure
		 */
		this.createMesure = function(e){
			
			var nom = $('#inputNom').val();
			var valeur = $('#inputValeur').val();
			var lieu = $('#inputLieu').val();
			var date = $('#inputDate').val();
			var type = $('#inputType').val();
			
			// url de création d'un événement
			var url = chemin+"mesure/mesure.html?add";
			
			// url , param, fonction de callback
			$.post(url,{'nom':nom,'valeur':valeur,'nom_lieu':lieu,'date_mesure':date,'type':type},app.goToMesure);
			
		}

		/*
		 *  redirige le visiteur sur la page du mesure dont on passe l'id en paramètre
		 */
		this.goToMesure = function(id){
			//echo ("redirection....");
			url = "/mesure/"+id+"-mesure.html";
			//echo (url);
			window.location.href = url;
		}


		///////////// mise à jour d'un mesure ///////////

		/*
		 *  appelé au click du liens de sauvegarde d'un mesure. Cette fonction met à jour la mesure.
		 */
		this.saveMesure = function(e){

			var idMesure = $('#idMesure').val();
			var nom = $('#inputNom').val();
	
			// update l'évenement dans la base de donnée
			var url = chemin+"mesure/"+idMesure+"-mesure.html?update";
			
			// url , param, fonction de callback
			$.post(url,{'id':idMesure,'nom':nom,});
		}
		
	} // Application
  });
})(jQuery);

