/*******************************************************************************************
 * Nom du fichier		: reservation.js
 * Date					: 30 juillet 2010
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu at ecodev.ch
 * But de ce fichier	: 
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

		// // ajoute l'autocompétion pour les mots-clés
		// 	$("input#tags").autocomplete(chemin+"groupe/tags.html?list&type=reservation", {
		// 		multiple: true,
		// 		minChars: 1
		// 	});
		// 
		// 	// tags
		// 	$('a#enregistreTag').click(function() {
		// 		app.saveTags();
		// 		return false;
		// 	 });

		// create reservation pour la page de création de reservation
		$('a#createReservation').click(function() {
			app.createReservation();
			return false;
		 });
		
		// update reservation
		$('a#saveReservation').click(function() {
			app.saveReservation();
			return false;
		 });
		
		// active le toggle de l'interface des heures au click de la checkbox du jour entier
		$('#jourComplet').click(function(){ $('.interfaceHeure').toggle()} );

		var jourComplet = $('input[#jourComplet][checked]').val();  // on ou nul
		if (jourComplet=="on") {			
			$('.interfaceHeure').toggle();
		}

		// active le datePicker
		app.showDatePicker();
		
		// charge le tri sur le tableau d'événements. Uniquement si le tableau est présent.
		if (document.getElementById("listeReservations") != null) {
			$("#listeReservations").tablesorter(); 
		};

	}); // ready


	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){


		///////////////  Création d'une reservation ///////////
		// Les étapes de la création d'une reservation sont les suivantes:
		// - récupération des valeurs de nom, description, info de modification et contenu de la reservation
		// - On balance tout au controlleur qui se démerde pour la création de l'évenement dans le calendrier de l'objet avec ces valeurs
		// - redirection de l'utilisateur sur la page de visualisation (ou de modification selon choix de conception) du nouvel évenement
		
		
		/*
		 *  appelé au clic de la fonction de sauvegarde. Cette fonction permet d'ajouter un reservation
		 */
		this.createReservation = function(e){
			
			var nom = $('#objetNom').val(); // on reprend le nom de l'objet comme nom de réservation
			var description = $('#descriptionDetail').val();
			// var lieu = $('#lieuDetail').val();
			
			var heureMinuteDebut = $('#heureMinuteDebutDetail').val();
			var heureMinuteDebutTab = heureMinuteDebut.split(':');  // on transforme l'heure fournie 09:00 en un tableau
			
			var jourDebut = $('#jourDebutDetail').val();
			// 		var heureDebut = $('#heureDebutDetail').val();
			// 		var minuteDebut = $('#minuteDebutDetail').val();
			
			var heureDebut = heureMinuteDebutTab[0];
			var minuteDebut = heureMinuteDebutTab[1];
			var dateDebut = jourDebut+' '+heureDebut+':'+minuteDebut+':00';
			
			// transforme une date au format dd-mm-yyyy en une date au format mysql datetime yyyy-mm-dd
			var jourDebutTab = jourDebut.split('-');
			var jourDebutMysql = jourDebutTab[2]+'-'+jourDebutTab[1]+'-'+jourDebutTab[0];
			var dateDebut = jourDebutMysql+' '+heureDebut+':'+minuteDebut+':00';
			var dateDebutTimeStamp = Date.parse(jourDebutTab[2]+'/'+jourDebutTab[1]+'/'+jourDebutTab[0]);
			
			// ajoute l'heure au timestamp
			dateDebutTimeStamp = dateDebutTimeStamp + (60*minuteDebut) + (60*60*heureDebut);
			
			var heureMinuteFin = $('#heureMinuteFinDetail').val();
			var heureMinuteFinTab = heureMinuteFin.split(':');  // on transforme l'heure fournie 17:30 en un tableau
			
			var jourFin = $('#jourFinDetail').val();
//			var heureFin = $('#heureFinDetail').val();
//			var minuteFin = $('#minuteFinDetail').val();
			var heureFin = heureMinuteFinTab[0];
			var minuteFin = heureMinuteFinTab[1];
			var dateFin = jourFin+' '+heureFin+':'+minuteFin+':00';
			
			// transforme une date au format dd-mm-yyyy en une date au format mysql datetime yyyy-mm-dd
			var jourFinTab = jourFin.split('-');
			var jourFinMysql = jourFinTab[2]+'-'+jourFinTab[1]+'-'+jourFinTab[0];
			var dateFin = jourFinMysql+' '+heureFin+':'+minuteFin+':00';
			var dateFinTimeStamp = Date.parse(jourFinTab[2]+'/'+jourFinTab[1]+'/'+jourFinTab[0]);
			
			// ajoute l'heure au timestamp
			dateFinTimeStamp = dateFinTimeStamp + (60*minuteFin) + (60*60*heureFin);
			
			var idCalendrierReservation = $('#objetIdCalendrier').val();
			var typeReservation = $('#inputType').val();
			var idObjet = $('#idObjet').val();

			var jourComplet = $('input[#jourComplet][checked]').val();  // on ou nul
			if (jourComplet=="on") {			
				// dateDebut = jourDebut+' 00:00:01';
				// dateFin = jourFin+' 23:59:59';
				jourComplet = true;
			}else{
				jourComplet = false;
			}
			
			/// création d'une personne
			
			var personnePrenom = $('#inputPrenom').val();
			var personneNom = $('#inputNom').val();
			// var personneSurnom = $('#inputSurnom').val();
			// var personneNaissance = $('#inputNaissance').val();
			// var personneMotDePasse = $('#inputMotDePasse').val();
			// var personneMotDePasseRepete = $('#inputMotDePasseRepete').val();
			var personneSurnom = '';
			var personneNaissance = '01-01-2000';
			var personneMotDePasse = '';
			var personneMotDePasseRepete = '';
			var personneRue = $('#inputRue').val();
			var personneNpa = $('#inputNpa').val();
			var personneLieu = $('#inputLieu').val();
			var personneTel = $('#inputTel').val();
			var personneEmail = $('#inputEmail').val();
			var personneDescription = $('#inputDescription').val();	
			
			// // transforme une date au format dd-mm-yyyy en une date au format mysql datetime yyyy-mm-dd
			// var personneNaissanceTab = personneNaissance.split('-');
			// var personneNaissanceMysql = personneNaissanceTab[2]+'-'+personneNaissanceTab[1]+'-'+personneNaissanceTab[0];
			// var personneNaissance = personneNaissanceMysql;
			
			// vérifie que la date de fin soit plus grande ou égale que la date de début avant de sauver
			if (dateFinTimeStamp>=dateDebutTimeStamp) {
				
					// url de création d'une réservation
					var url = chemin+"reservation/reservation.html?add";

					// url , param, fonction de callback
					$.post(url,{'nom':nom,'date_debut':dateDebut,'date_fin':dateFin,'description':description,'jour_entier':jourComplet,'id_calendrier':idCalendrierReservation,'type':typeReservation,'id_objet':idObjet,'prenom':personnePrenom,'nom_personne':personneNom,'surnom':personneSurnom,'description_personne':personneDescription,'date_naissance':personneNaissance,'mot_de_passe':personneMotDePasse,'rue':personneRue,'npa':personneNpa,'lieu':personneLieu,'tel':personneTel,'email':personneEmail},app.addTagsForNewReservation);
					
			}else{
				alert('La date de fin est plus ancienne que la date de début!');
			}
			
			
		}
		
		/*
		 *  callback quand une création d'évenement est effectuée
		 *  Cette fonction va récupérer l'id du nouvel évenement et lui attribuer les tags
		 */
		this.addTagsForNewReservation = function(id){
		//	echo('Nouvel reservation créé avec succès');
		//	echo (id);
			var idReservation = id; // on récupère l'id fourni en postant le nouvel reservation
			var tags = $('#tags').val();
			var url = chemin+"groupe/tag.html?type=reservation&id="+idReservation+"&tag="+tags;
			$.get(url,{'toto':''},app.goToReservation(id));
		}

		/*
		 *  redirige le visiteur sur la page du reservation dont on passe l'id en paramètre
		 */
		this.goToReservation = function(id){
			//echo ("redirection....");
			url = "/reservation/"+id+"-reservation.html";
			//echo (url);
			window.location.href = url;
		}


		/*
		 *  Charge le datePicker
		 */
		this.showDatePicker = function(){
			// charge le datePicker
			Date.format = 'dd-mm-yyyy';
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

		///////////// mise à jour d'une reservation ///////////

		/*
		 *  appelé au clic du liens de sauvegarde d'une reservation. Cette fonction met à jour le reservation.
		 */
		this.saveReservation = function(e){

			var idReservation = $('#idReservation').val();
			var nom = $('#objetNom').val();
			var lieu = $('#lieuDetail').val();
			
			var jourDebut = $('#jourDebutDetail').val();
			var heureDebut = $('#heureDebutDetail').val();
			var minuteDebut = $('#minuteDebutDetail').val();
			var dateDebut = jourDebut+' '+heureDebut+':'+minuteDebut+':00';
			
			// transforme une date au format dd-mm-yyyy en une date au format mysql datetime yyyy-mm-dd
			var jourDebutTab = jourDebut.split('-');
			var jourDebutMysql = jourDebutTab[2]+'-'+jourDebutTab[1]+'-'+jourDebutTab[0];
			var dateDebut = jourDebutMysql+' '+heureDebut+':'+minuteDebut+':00';
			var dateDebutTimeStamp = Date.parse(jourDebutTab[2]+'/'+jourDebutTab[1]+'/'+jourDebutTab[0]);
			
			// ajoute l'heure au timestamp
			dateDebutTimeStamp = dateDebutTimeStamp + (60*minuteDebut) + (60*60*heureDebut);
			
			var jourFin = $('#jourFinDetail').val();
			var heureFin = $('#heureFinDetail').val();
			var minuteFin = $('#minuteFinDetail').val();
			var dateFin = jourFin+' '+heureFin+':'+minuteFin+':00';
			
			// transforme une date au format dd-mm-yyyy en une date au format mysql datetime yyyy-mm-dd
			var jourFinTab = jourFin.split('-');
			var jourFinMysql = jourFinTab[2]+'-'+jourFinTab[1]+'-'+jourFinTab[0];
			var dateFin = jourFinMysql+' '+heureFin+':'+minuteFin+':00';
			var dateFinTimeStamp = Date.parse(jourFinTab[2]+'/'+jourFinTab[1]+'/'+jourFinTab[0]);
			
			// ajoute l'heure au timestamp
			dateFinTimeStamp = dateFinTimeStamp + (60*minuteFin) + (60*60*heureFin);
			
			var idCalendrierReservation = $('#objetIdCalendrier').val();
			var idEvenementReservation = $('#idEvenement').val();
			var typeReservation = $('#inputType').val();
			var etatReservation = $('#inputEtat').val();
			
			var jourComplet = $('input[#jourComplet][checked]').val();  // on ou nul
	
			if (jourComplet=="on") {			
				// dateDebut = jourDebut+' 00:00:01';
				// dateFin = jourFin+' 23:59:59';
				jourComplet = true;
			}else{
				jourComplet = false;
			}
			var description = $('#descriptionDetail').val();
			

			// vérifie que la date de fin soit plus grande ou égale que la date de début avant de sauver
			if (dateFinTimeStamp>=dateDebutTimeStamp) {
				// met à jour les tags
				var tags = $('#tags').val();
				var urlTag = chemin+"groupe/tag.html?type=reservation&id="+idReservation+"&tag="+tags;
				$.get(urlTag,{'toto':''});
	
				// update l'évenement dans la base de donnée
				var url = chemin+"reservation/"+idReservation+"-reservation.html?update";
			
				// url , param, fonction de callback
				$.post(url,{'id':idReservation,'nom':nom,'date_debut':dateDebut,'date_fin':dateFin,'description':description,'jour_entier':jourComplet,'id_calendrier':idCalendrierReservation,'type':typeReservation,'etat':etatReservation,'id_evenement':idEvenementReservation},app.finUpdate);
			}else{
				alert('La date de fin est plus ancienne que la date de début!');
			}
		}
		
		
		/*
		 *  callback quand une mise à jour du reservation est effectuée
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
			var idReservation = $('#idReservation').val();
			var tags = $('#tags').val();
			var url = chemin+"groupe/tag.html?type=reservation&id="+idReservation+"&tag="+tags;
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