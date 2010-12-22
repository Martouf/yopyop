/*******************************************************************************************
 * Nom du fichier		: evenement.js
 * Date					: 29 décembre 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu at ecodev.ch
 * But de ce fichier	: Fournir les fonctions utiles pour l'utilisation des formulaires d'ajout et modification d'événements.
 *******************************************************************************************
 * Le code de la vue calendrier n'est pas dans ce fichier.
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

		// ajoute l'autocompétion pour les mots-clés
		$("input#tags").autocomplete(chemin+"groupe/tags.html?list&type=evenement", {
			multiple: true,
			minChars: 1
		});

		// tags
		$('a#enregistreTag').click(function() {
			app.saveTags();
			return false;
		 });

		// create evenement pour la page de création de evenement
		$('a#createEvenement').click(function() {
			app.createEvenement();
			return false;
		 });
		
		// update evenement
		$('a#saveEvenement').click(function() {
			app.saveEvenement();
			return false;
		 });
		
		// active le toggle de l'interface des heures au click de la checkbox du jour entier
		$('#jourComplet').click(function(){ $('.interfaceHeure').toggle()} );

		var jourComplet = $('input[name=jourComplet][checked]').val();  // on ou nul
		if (jourComplet=="on") {			
			$('.interfaceHeure').toggle();
		}

		// active le datePicker
		app.showDatePicker();
		
		// charge le tri sur le tableau d'événements. Uniquement si le tableau est présent.
		if (document.getElementById("listeEvenements") != null) {
			$("#listeEvenements").tablesorter(); 
		};

	}); // ready


	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){


		///////////////  Création d'un evenement ///////////
		// Les étapes de la création d'un evenement sont les suivantes:
		// - récupération des valeurs de nom, description, info de modification et contenu du evenement
		// - création de l'évenement avec ces valeurs
		// - récupération de l'id de l'évenement qui vient d'être créé
		// - récupération des tags et enregistrement de ceux-ci pour le nouvel id fourni
		// - redirection de l'utilisateur sur la page de visualisation (ou de modification selon choix de conception) du nouvel évenement
		
		
		/*
		 *  appelé au clic de la fonction de sauvegarde. Cette fonction permet d'ajouter un evenement
		 */
		this.createEvenement = function(e){
			
			var nom = $('#nomDetail').val();
			var description = $('#descriptionDetail').val();
			var lieu = $('#lieuDetail').val();
			var jourDebut = $('#jourDebutDetail').val();
			var heureDebut = $('#heureDebutDetail').val();
			var minuteDebut = $('#minuteDebutDetail').val();
			var dateDebut = jourDebut+' '+heureDebut+':'+minuteDebut+':00';

			var jourFin = $('#jourFinDetail').val();
			var heureFin = $('#heureFinDetail').val();
			var minuteFin = $('#minuteFinDetail').val();
			var dateFin = jourFin+' '+heureFin+':'+minuteFin+':00';
			var idCalendrierEvenement = $('#idCalendrierEvenement').val();

			var jourComplet = $('input[name=jourComplet][checked]').val();  // on ou nul
			if (jourComplet=="on") {			
				// dateDebut = jourDebut+' 00:00:01';
				// dateFin = jourFin+' 23:59:59';
				jourComplet = true;
			}else{
				jourComplet = false;
			}

			// url de création d'un événement
			var url = chemin+"evenement/evenement.html?add";
			
			// url , param, fonction de callback
			$.post(url,{'nom':nom,'lieu':lieu,'date_debut':dateDebut,'date_fin':dateFin,'description':description,'jour_entier':jourComplet,'id_calendrier':idCalendrierEvenement},app.addTagsForNewEvenement);
			
		}
		
		/*
		 *  callback quand une création d'évenement est effectuée
		 *  Cette fonction va récupérer l'id du nouvel évenement et lui attribuer les tags
		 */
		this.addTagsForNewEvenement = function(id){
		//	echo('Nouvel evenement créé avec succès');
		//	echo (id);
			var idEvenement = id; // on récupère l'id fourni en postant le nouvel evenement
			var tags = $('#tags').val();
			var url = chemin+"groupe/tag.html?type=evenement&id="+idEvenement+"&tag="+tags;
			$.get(url,{'toto':''},app.goToEvenement(id));
		}

		/*
		 *  redirige le visiteur sur la page du evenement dont on passe l'id en paramètre
		 */
		this.goToEvenement = function(id){
			//echo ("redirection....");
			url = "/evenement/"+id+"-evenement.html";
			//echo (url);
			window.location.href = url;
		}


		/*
		 *  Charge le datePicker
		 */
		this.showDatePicker = function(){
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
		} // fonction showDatePicker

		///////////// mise à jour d'un evenement ///////////

		/*
		 *  appelé au click du liens de sauvegarde d'un evenement. Cette fonction met à jour le evenement.
		 */
		this.saveEvenement = function(e){

			var idEvenement = $('#idEvenement').val();
			var nom = $('#nomDetail').val();
			var lieu = $('#lieuDetail').val();
			
			var jourDebut = $('#jourDebutDetail').val();
			var heureDebut = $('#heureDebutDetail').val();
			var minuteDebut = $('#minuteDebutDetail').val();
			var dateDebut = jourDebut+' '+heureDebut+':'+minuteDebut+':00';
			
			var jourFin = $('#jourFinDetail').val();
			var heureFin = $('#heureFinDetail').val();
			var minuteFin = $('#minuteFinDetail').val();
			var dateFin = jourFin+' '+heureFin+':'+minuteFin+':00';
			var idCalendrierEvenement = $('#idCalendrierEvenement').val();
			
			var jourComplet = $('input[name=jourComplet][checked]').val();  // on ou nul
			if (jourComplet=="on") {			
				// dateDebut = jourDebut+' 00:00:01';
				// dateFin = jourFin+' 23:59:59';
				jourComplet = true;
			}else{
				jourComplet = false;
			}
			var description = $('#descriptionDetail').val();
			

			// met à jour les tags
			var tags = $('#tags').val();
			var urlTag = chemin+"groupe/tag.html?type=evenement&id="+idEvenement+"&tag="+tags;
			$.get(urlTag,{'toto':''});
	
			// update l'évenement dans la base de donnée
			var url = chemin+"evenement/"+idEvenement+"-evenement.html?update";
			
			// url , param, fonction de callback
			$.post(url,{'id':idEvenement,'nom':nom,'lieu':lieu,'date_debut':dateDebut,'date_fin':dateFin,'description':description,'jour_entier':jourComplet,'id_calendrier':idCalendrierEvenement},app.finUpdate);
			// on va sur la page d'affichage de l'événement.
		}
		
		
		/*
		 *  callback quand une mise à jour du evenement est effectuée
		 */
		this.finUpdate = function(){
			//echo('mise à jour réussie');
			var url = chemin+"utile/ajax/calcule_date.php";
			$.get(url,{'format':'d M Y H:i:s'},app.updateLog);
		}
		
		/*
		 *  affiche dans le log la date de la sauvegarde
		 */
		this.updateLog = function(date){

			message = '<br />'+date+' sauvegarde effectuée';
			$('#logAction').append(message);
		}

		////////// mise à jour séparée des tags //////
		
		/*
		 *  fonction d'enregistrement des tags du document
		 */
		this.saveTags = function(){
			var idEvenement = $('#idEvenement').val();
			var tags = $('#tags').val();
			var url = chemin+"groupe/tag.html?type=evenement&id="+idEvenement+"&tag="+tags;
			$.get(url,{'toto':''},app.tagSaved);
		}
		
		/*
		 *  callback quand une mise à jour des tags est effectuée
		 */
		this.tagSaved = function(){
			//echo('mise à jour des tags réussie');
			var url = chemin+"utile/ajax/calcule_date.php";
			$.get(url,{'format':'d M Y H:i:s'},app.updateLogTag);
		}
		
		/*
		 *  affiche dans le log la date de la sauvegarde
		 */
		this.updateLogTag = function(date){

			message = '<br />'+date+' mise à jour des tags';
			$('#logAction').append(message);
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