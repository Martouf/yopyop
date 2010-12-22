/*******************************************************************************************
 * Nom du fichier		: calendrier_edit_liste.js
 * Date					: 13 janvier 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu at ecodev.ch
 * But de ce fichier	: s'occupe d'activer le tri sur la liste
 *******************************************************************************************
 * Le code de la vue calendrier n'est pas dans ce fichier.
 */

var app;  // variable globale qui représente mon application
var chemin = "/";

// jQuery.noConflict();
// (function($) { 
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
		if (document.getElementById("listeEvenements") != null) {
			 $("#listeEvenements").tablesorter(); 
			//$("#listeEvenements").tableFilter(); 
			
			// charge le filtre du tableau
			var theTable = $('#listeEvenements');

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
		
		// va au mois suivant.
		$('a#boutonSuivant').click(function() {
			app.moisSuivant();
			return false;
		 });
		
		// va au mois précédent.
		$('a#boutonPasse').click(function() {
			app.moisPrecedent();
			return false;
		 });

		// exportation pdf
		$('a#lienExportPdf').click(function() {
			app.refreshFilterPdf();
			return false;
		 });
		
		
		//////////// filtres //////////
		$('#choixVue').change(app.refreshFilter);
		$('#choixJour').change(app.refreshFilter);
		$('#choixMois').change(app.refreshFilter);
		$('#choixAnnee').change(app.refreshFilter);
		$('#choixFormat').change(app.refreshFilter);
		
		$('#filtreTye').change(app.refreshFilter);
		$('#filtreNoVp').change(app.refreshFilter);
		$('#sendTags').click(app.refreshFilter);
		$('#filtreCalendrier').change(app.refreshFilter);
		$('#filtreLieu').change(app.refreshFilter);

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
			var url = chemin+"event/"+id+"-evenement.html";
			
			app.showMasque();
			
			// url , param, fonction de callback
			$.get(url,{'theme':"no"},app.afficheFormulaireUpdate);
		}
		

		/*
		 *  Affiche, dans le div en question le contenu html qui provient de la requête ajax.
		 */
		this.afficheFormulaireUpdate = function(html){
			$('#boiteDialogue').empty().append(html);
			
			// charge le datePicker
			Date.format = 'dd-mm-yyyy'; // yyyy-mm-dd
			$('.date-pick').datePicker();
		
			// empêche de créer des événements avec des dates de fin plus vieilles que des dates de début et inversément
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
			
				// va chercher les id des événements suivant et précédent.
				var previousEventId = 0;
				var nextEventId = 0;
				var length = arrayId.length;
				var idEvenement = $('#idEvenement').val();
				var isFoundEvent = false;
				for(var index = 0; index < length && isFoundEvent != true; index ++){
					if(arrayId[index] == idEvenement){
						//define the previous eventId
						if(typeof(arrayId[index - 1]) != 'undefined'){
							previousEventId = arrayId[index - 1];
						}
						else{
							previousEventId = 0;
						}

						//define the next eventId
						if(typeof(arrayId[index + 1]) != 'undefined'){
							nextEventId = arrayId[index + 1];
						}
						else{
							nextEventId = 0;
						}
						isFoundEvent = true;
					}
				}
				// écouteurs pour les flèches
				$('a#FlecheEvenementSuivant').click(function() {
					app.showModifierEvenement(nextEventId);
					return false;
				 });
				
				$('a#FlecheEvenementPrecedent').click(function() {
					app.showModifierEvenement(previousEventId);
					return false;
				 });
				
				// si l'élément suivant ou précédent n'existe pas, masque la flèche
				if (nextEventId == 0) {
					$('a#FlecheEvenementSuivant').hide();
				};
				if (previousEventId == 0) {
					$('a#FlecheEvenementPrecedent').hide();
				};

				// bouton cancel qui ferme la fenêtre modale
				$('a#cancelEvenement').click(function() {
					app.hideMasque();
					return false;
				 });
				
				var idLieu = $('#lieuDetail').val();
		}
		
	
	
		/*
		 *  Fonction qui compose une url à partir des valeurs actuelles des filtres.
		 */
		this.refreshFilter = function(){
			
			var baseUrl = $('#baseUrl').val(); // http://erenweb.ecodev.ch
			
			var vue = $('#choixVue').val();
			var mois = $('#choixMois').val();
			var annee = $('#choixAnnee').val();
			var jour = $('#choixJour').val();
			var idType = $('#filtreTye').val();
			var noVp = $('#filtreNoVp').val();
			var tagsVirgule = $('#filtreTags').val();
			var idCalendrier = $('#filtreCalendrier').val();
			var idLieu = $('#filtreLieu').val();
			var format = $('#choixFormat').val();
			// console.log(tagsVirgule);
			
			var url = chemin+"agenda/";
			
			if (format!='html') {
				url = chemin+"agenda/calendrier."+format;
			};
			
			
			var tagsUrl = tagsVirgule.split(',');
			// console.log(tagsUrl);
			
			for (var i=0; i < tagsUrl.length; i++) {
				if (tagsUrl[i]) {
					url = url+tagsUrl[i]+'/';
				};
			};
			
			if (noVp) {
				url = url+noVp+'/';
			};
			
			if (idCalendrier!=0) {
				url = url+idCalendrier+"-calendrier."+format;
			};
			
			var filtre = '?';

			if (vue) {
				filtre = filtre+'vue='+vue;
			};
			if (annee) {
				filtre = filtre+'&datecourante='+annee;
			};
			if (mois) {
				filtre = filtre+'-'+mois;
			};
			if (jour) {
				filtre = filtre+'-'+jour;
			};
			
			if (idType) {
				filtre = filtre+'&filtreType='+idType;
			};
			
			if (idLieu) {
				filtre = filtre+'&filtreLieu='+idLieu;
			};
			
			//console.log(baseUrl+url+filtre);
			// recharge la page avec les bons filtres.
			window.location.href = baseUrl+url+filtre;
		}
		
		
		/*
		 *  refresh de la page
		 */
		this.refreshPage = function(){
			url = window.location.href;
			window.location.href = url;
		}
		
		
		/*
		 *  Fonction qui compose une url à partir des valeurs actuelles des filtres.
		 */
		this.refreshFilterPdf = function(){
			
			var baseUrl = $('#baseUrl').val(); // http://erenweb.ecodev.ch
			
			var vue = $('#choixVue').val();
			var mois = $('#choixMois').val();
			var annee = $('#choixAnnee').val();
			var jour = $('#choixJour').val();
			var idType = $('#filtreTye').val();
			var noVp = $('#filtreNoVp').val();
			var tagsVirgule = $('#filtreTags').val();
			var idCalendrier = $('#filtreCalendrier').val();
			var idLieu = $('#filtreLieu').val();
			var format = 'pdf'
			// console.log(tagsVirgule);
			
			var url = chemin+"agenda/";
			
			if (format!='html') {
				url = chemin+"agenda/calendrier."+format;
			};
			
			
			var tagsUrl = tagsVirgule.split(',');
			// console.log(tagsUrl);
			
			for (var i=0; i < tagsUrl.length; i++) {
				if (tagsUrl[i]) {
					url = url+tagsUrl[i]+'/';
				};
			};
			
			if (noVp) {
				url = url+noVp+'/';
			};
			
			if (idCalendrier!=0) {
				url = url+idCalendrier+"-calendrier."+format;
			};
			
			var filtre = '?';

			if (vue) {
				filtre = filtre+'vue='+vue;
			};
			if (annee) {
				filtre = filtre+'&datecourante='+annee;
			};
			if (mois) {
				filtre = filtre+'-'+mois;
			};
			if (jour) {
				filtre = filtre+'-'+jour;
			};
			
			if (idType) {
				filtre = filtre+'&filtreType='+idType;
			};
			
			if (idLieu) {
				filtre = filtre+'&filtreLieu='+idLieu;
			};
			
			//console.log(baseUrl+url+filtre);
			// recharge la page avec les bons filtres.
			window.location.href = baseUrl+url+filtre;
		}
		
		
		
		/*
		 *  Fonction qui compose une url pour aller au mois suivant en tenant compte des valeurs actuelles des filtres.
		 */
		this.moisSuivant = function(){
			
			var baseUrl = $('#baseUrl').val(); // http://erenweb.ecodev.ch
			var vue = $('#choixVue').val();
			var dateMoisSuivant = $('#dateMoisProchain').val();
			
			var idType = $('#filtreTye').val();
			var noVp = $('#filtreNoVp').val();
			var tagsVirgule = $('#filtreTags').val();
			var idCalendrier = $('#filtreCalendrier').val();
			var idLieu = $('#filtreLieu').val();
			
			var url = chemin+"agenda/";
			var tagsUrl = tagsVirgule.split(',');
			
			for (var i=0; i < tagsUrl.length; i++) {
				if (tagsUrl[i]) {
					url = url+tagsUrl[i]+'/';
				};
			};
			
			if (noVp) {
				url = url+noVp+'/';
			};
			
			if (idCalendrier!=0) {
				url = url+idCalendrier+"-calendrier.html";
			};
			
			var filtre = '?';

			if (vue) {
				filtre = filtre+'vue='+vue;
			};
			if (dateMoisSuivant) {
				filtre = filtre+'&datecourante='+dateMoisSuivant;
			};
			if (idType) {
				filtre = filtre+'&filtreType='+idType;
			};
			
			if (idLieu) {
				filtre = filtre+'&filtreLieu='+idLieu;
			};
						
			// recharge la page avec les bons filtres.
			window.location.href = baseUrl+url+filtre;
		}
		
		/*
		 *  Fonction qui compose une url pour aller au mois précédent en tenant compte des valeurs actuelles des filtres.
		 */
		this.moisPrecedent = function(){
			var baseUrl = $('#baseUrl').val(); // http://erenweb.ecodev.ch
			var vue = $('#choixVue').val();
			var dateMoisPasse = $('#dateMoisPasse').val();
			
			var idType = $('#filtreTye').val();
			var idEtat = $('#filtreEtat').val();
			var noVp = $('#filtreNoVp').val();
			var tagsVirgule = $('#filtreTags').val();
			var idCalendrier = $('#filtreCalendrier').val();
			var idLieu = $('#filtreLieu').val();
			
			var url = chemin+"agenda/";
			var tagsUrl = tagsVirgule.split(',');
			
			for (var i=0; i < tagsUrl.length; i++) {
				if (tagsUrl[i]) {
					url = url+tagsUrl[i]+'/';
				};
			};
			
			if (noVp) {
				url = url+noVp+'/';
			};
			
			if (idCalendrier!=0) {
				url = url+idCalendrier+"-calendrier.html";
			};
			
			var filtre = '?';

			if (vue) {
				filtre = filtre+'vue='+vue;
			};
			if (dateMoisPasse) {
				filtre = filtre+'&datecourante='+dateMoisPasse;
			};
			if (idType) {
				filtre = filtre+'&filtreType='+idType;
			};
			
			if (idLieu) {
				filtre = filtre+'&filtreLieu='+idLieu;
			};
						
			// recharge la page avec les bons filtres.
			window.location.href = baseUrl+url+filtre;
		}

	} // Application


  });
// })(jQuery);

function email(user,domain,label,link) {
				var address = user+'@'+domain;
				var toWrite = '';
				if (link > 0) {toWrite += '<a href="mailto:'+address+'">';}
				if (label != '') {toWrite += label;} else {toWrite += address;}
				if (link > 0) {toWrite += '</a>';}
				document.write(toWrite);
}

function echo(str){
	try{
		console.log(str);
	}
	catch(e){alert(str)}
}