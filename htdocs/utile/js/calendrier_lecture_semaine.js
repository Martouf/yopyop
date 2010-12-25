/*******************************************************************************************
 * Nom du fichier		: evenement.js
 * Date					: 18 mars 2008
 * Modification			: 19 janvier 2009
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu at ecodev.ch
 * But de ce fichier	: Permet de modifier les événements d'un calendrier en vue semaine
 *******************************************************************************************
 * 
 *
 */

var app;  // variable globale qui représente mon application
var chemin = "/" ;

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
		
		// va à la semaine suivante.
		$('a#boutonSuivant').click(function() {
			app.semaineSuivante();
			return false;
		 });
		
		// va à la semaine précédente
		$('a#boutonPasse').click(function() {
			app.semainePrecedente();
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
			
			this.showMasque();
			
			// url , param, fonction de callback
			$.get(url,{'theme':"no"},app.afficheFormulaireUpdate);
		}
		
		
		/*
		 *  Affiche, dans le div en question le contenu html qui provient de la requête ajax.
		 */
		this.afficheFormulaireUpdate = function(html){
			$('#boiteDialogue').empty().append(html);
			
			$('#blocNavigationFleche').hide(); // masque les flèches qui ne fonctionne qu'en mode liste
			
			// charge le datePicker
			Date.format = 'dd-mm-yyyy';
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
			
				// écouteurs sur les liens de l'interface de mofification de l'événement
				// tags
				// $('a#enregistreTag').click(function() {
				// 				app.saveTags();
				// 				return false;
				// 			 });

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
				
				// bouton supprimer qui supprime l'événement
				$('a#suprrimeEvenement').click(function() {
					if(confirm('Voulez vous réellement supprimer cet événement?')){
						app.deleteEvenement();
					}
					return false;
				 });

				// active le toggle de l'interface des heures au click de la checkbox du jour entier
				$('#jourComplet').change(function(){
					$('.interfaceHeure').toggle()
					return false;
				});
				
				// masque le bloc
				$('#blocAjoutLieu').hide();
				
				// active le toggle de l'interface d'ajout d'un lieu au clic du +
				$('#lienAddLieu').click(function(){
					$('#blocAjoutLieu').toggle()
					return false;
				});
				
				// bouton ajouter un lieu
				$('#addLieu').click(function() {
					app.createLieu();
					return false;
				 });

				var jourComplet = $('input[name=jourComplet][checked]').val();  // on ou nul
				if (jourComplet=="on") {			
					$('.interfaceHeure').toggle();
				}

				// ajoute l'autocompétion pour les mots-clés
				$("input#tags").autocomplete(chemin+"groupe/tags.html?list&type=evenement", {
					multiple: true,
					minChars: 1
				});
				
				// // auto complétion des lieux avec un filtre sur la catégorie contenue dans un champ caché
				// 				var categorie = $("input#categorieLieuDetail").val();
				// 
				// 				// ajoute l'autocompétion pour les lieux
				// 				$("input#lieuDetail").autocomplete(chemin+"/lieu/lieu.html?list&categorie="+categorie, {
				// 					multiple: true,
				// 					minChars: 1
				// 				});
				var idLieu = $('#lieuDetail').val();
				
				// met en place le code de l'aperçu avec le plugin: http://rikrikrik.com/jquery/magicpreview/
				$('#nomDetail').magicpreview('mp_');
				// $('#lieuDetail').change(function() {
				// 	app.previewNomLieu();
				//  });	
				$('#descriptionDetail').magicpreview('mp_');
				
				$("#lieuDetail").sexyCombo({
					triggerSelected:true,
					autofill:true,
					textChangeCallback: function() {
					    var nom = this.getTextValue();
						$('#mp_lieuDetail').empty().append(nom);
					}
				});
				
				$("#infoEvenement").sexyCombo({
					triggerSelected:true,
					autofill:true,
					textChangeCallback: function() {
					    var nom = this.getTextValue();
						$('#mp_infoEvenement').empty().append(nom);
					}
				});
				
				// état initial
				if($('#periodiciteEvenement').val()!="non"){
					$('#createurEvenementMultiple').show();
				}
				
				// suivant l'action
				$('#periodiciteEvenement').change(function(){
					if($('#periodiciteEvenement').val()!="non"){
						$('#createurEvenementMultiple').show();
					}else{
						$('#createurEvenementMultiple').hide();
					}
				});
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
			// console.log(tagsVirgule);
			
			var url = chemin+"agenda/";
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
				url = url+idCalendrier+"-calendrier.html";
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
		 *  Fonction qui compose une url pour aller à la semaine suivante en tenant compte des valeurs actuelles des filtres.
		 */
		this.semaineSuivante = function(){
			
			var baseUrl = $('#baseUrl').val(); // http://erenweb.ecodev.ch
			var vue = $('#choixVue').val();
			var dateSemaineProchaine = $('#dateSemaineProchaine').val();
			
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
			if (dateSemaineProchaine) {
				filtre = filtre+'&datecourante='+dateSemaineProchaine;
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
		 *  Fonction qui compose une url pour aller à la semaine précédente en tenant compte des valeurs actuelles des filtres.
		 */
		this.semainePrecedente = function(){
			
			var baseUrl = $('#baseUrl').val(); // http://erenweb.ecodev.ch
			var vue = $('#choixVue').val();
			var dateSemainePassee = $('#dateSemainePassee').val();
			
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
			if (dateSemainePassee) {
				filtre = filtre+'&datecourante='+dateSemainePassee;
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