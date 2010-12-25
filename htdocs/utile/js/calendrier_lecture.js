/*******************************************************************************************
 * Nom du fichier		: calendrier_lecture.js
 * Date					: 29 janvier 2009
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu at ecodev.ch
 * But de ce fichier	: Fournir des fonctions javascript pour le calendrier en mode lecture seule
 *******************************************************************************************
 * 
 *
 */

var app;  // variable globale qui représente mon application
var chemin = "/" ;

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
		
		
	}); // ready
	
	
	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){
		
		
		/*
		 *  masque la boite de dialogue
		 */
		this.hideMasque = function(html){
			$('#boiteDialogue').hide();
			$('#masque').hide();
		}
		
		/*
		 *  affiche la boite de dialogue
		 */
		this.showMasque = function(html){
			$('#masque').show();
			$('#boiteDialogue').show();
		}
		
		
		/*
		 *  affiche le détail d'un événement dans une boite en avant plan
		 */
		this.showEvenementDetail = function(id){
			var url = chemin+"evenement/"+id+"-evenement.html";
			
			this.showMasque();
			
			// url , param, fonction de callback
			$.get(url,{'theme':"no"},app.afficheFormulaireUpdate);
		}
		
		/*
		 *  Affiche, dans le div en question le contenu html qui provient de la requête ajax.
		 */
		this.afficheFormulaireUpdate = function(html){
			$('#boiteDialogue').empty().append(html);
			
			// charge le datePicker
			Date.format = 'yyyy-mm-dd';
			$('.date-pick').datePicker();
		
			// empêche de créer un événement avec une date de fin plus ancienne que la date de début et inversément
			$('#jourDebutDetail').bind(
					'dpClosed',
					function(e, selectedDates)
					{
						var d = selectedDates[0];
						if (d) {
							d = new Date(d);
							$('#jourFinDetail').dpSetStartDate(d.asString());
						}
					}
				);
				$('#jourFinDetail').bind(
					'dpClosed',
					function(e, selectedDates)
					{
						var d = selectedDates[0];
						if (d) {
							d = new Date(d);
							$('#jourDebutDetail').dpSetEndDate(d.asString());
						}
					}
				);
				
			// écouteurs sur les liens de l'interface de mofification de l'événement
			// tags
			$('a#enregistreTag').click(function() {
				app.saveTags();
				return false;
			 });

			// update evenement
			$('a#saveEvenement').click(function() {
				app.saveEvenement();
				return false;
			 });
			
			// bouton cancel qui ferme la fenêtre modale
			$('a#cancelEvenement').click(function() {
				app.hideMasque();
				return false;
			 });
				
			// active le toggle de l'interface des heures au click de la checkbox du jour entier
			$('#jourComplet').click(function(){ $('.interfaceHeure').toggle()} );
			
			var jourComplet = $('input[name=jourComplet][checked]').val();  // on ou nul
			if (jourComplet=="on") {			
				$('.interfaceHeure').toggle();
			}
			
			// ajoute l'autocompétion pour les mots-clés
			$("input#tags").autocomplete(chemin+"groupe/tags.html?list&type=evenement", {
				multiple: true,
				minChars: 1
			});
		}
		
		
		/*
		 *  refresh de la page
		 */
		this.refreshPage = function(){
			url = window.location.href;
			window.location.href = url;
		}
		
	} // Application
	
  });
})(jQuery);