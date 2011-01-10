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
		
		// ajout d'un événement au clic sur une heure
		$('.blocJour').dblclick(app.addEvenement);
		
		// ajout d'un événement au clic du bouton
		$('a#boutonNewEvenement').click(function() {
			app.newEvenement();
			return false;
		 });
		
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
		$('#filtreEtat').change(app.refreshFilter);

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
			
			app.showMasque();
			
			// url , param, fonction de callback
			$.get(url,{'theme':"no"},app.afficheFormulaireUpdate);
		}
		
		
		/*
		 *  affiche le formulaire de modification d'un événement
		 */
		this.showModifierEvenement = function(id){
			var url = chemin+"evenement/"+id+"-evenement.html";
			
			app.showMasque();  // optionnel si édition en 2 étapes
			
			// url , param, fonction de callback
			$.get(url,{'modify':id,'theme':"no"},app.afficheFormulaireUpdate);
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

				// update evenement
				$('a#saveEvenement').click(function() {
					app.saveEvenement();
					return false;
				 });
				
				// sauvegarde via le bouton entre les flèches
				$('a#boutonSauverFleche').click(function() {
					app.saveEvenementSansRefresh();
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

				var jourComplet = $('input[#jourComplet][checked]').val();  // on ou nul
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
		
	
		//////////////////////////////////////////////////////////////////////////////////////////
		/////  Formulaire de modification d'un événement (fonctions adaptées de evenement.js) ////
		//////////////////////////////////////////////////////////////////////////////////////////

		/*
		 *  appelé au click du liens de sauvegarde d'un evenement. Cette fonction met à jour l'évenement.
		 */
		this.saveEvenement = function(e){

			var idEvenement = $('#idEvenement').val();
			var nom = $('#nomDetail').val();
			var uidEvenement = $('#uidEvenement').val();
			var periodiqueAutonome = $('#periodiqueAutonome').val();
			//var lieu = $('#lieuDetail').val();
			var lieu = $('input[name=lieuDetail__sexyComboHidden]').val();

			var jourDebut = $('#jourDebutDetail').val();
			var heureDebut = $('#heureDebutDetail').val();
			var minuteDebut = $('#minuteDebutDetail').val();
			
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
			
			// transforme une date au format dd-mm-yyyy en une date au format mysql datetime yyyy-mm-dd
			var jourFinTab = jourFin.split('-');
			var jourFinMysql = jourFinTab[2]+'-'+jourFinTab[1]+'-'+jourFinTab[0];
			var dateFin = jourFinMysql+' '+heureFin+':'+minuteFin+':00';
			var dateFinTimeStamp = Date.parse(jourFinTab[2]+'/'+jourFinTab[1]+'/'+jourFinTab[0]);
			
			// ajoute l'heure au timestamp
			dateFinTimeStamp = dateFinTimeStamp + (60*minuteFin) + (60*60*heureFin);
			
			var idCalendrierEvenement = $('#idCalendrierEvenement').val();

			var description = $('#descriptionDetail').val();
			var periodicite = $('#periodiciteEvenement').val();
			var occurrence = $('#nbOccurrence').val();
			var type = $('#typeEvenement').val();
		//	var info = $('#infoEvenement').val();
			var info = $('input[name=infoEvenement__sexyComboHidden]').val();

			var state = $('#stateEvenement').val();
			var remarque = $('#remarqueEvenement').val();

			var jourComplet = $('input[#jourComplet][checked]').val();  // on ou nul
			if (jourComplet=="on") {			
				// dateDebut = jourDebut+' 00:00:01';
				// dateFin = jourFin+' 23:59:59';
				jourComplet = true;
			}else{
				jourComplet = false;
			}

			// vérifie que la date de fin soit plus grande ou égale que la date de début avant de sauver
			if (dateFinTimeStamp>=dateDebutTimeStamp) {
				// met à jour les tags
				var tags = $('#tags').val();
				var urlTag = chemin+"groupe/tag.html?type=evenement&id="+idEvenement+"&tag="+tags;
				$.get(urlTag,{'toto':''});

				// update l'évenement dans la base de donnée
				var url = chemin+"evenement/"+idEvenement+"-evenement.html?update";

				// url , param, fonction de callback
				$.post(url,{'id':idEvenement,'uid':uidEvenement,'nom':nom,'lieu':lieu,'date_debut':dateDebut,'date_fin':dateFin,'description':description,'jour_entier':jourComplet,'id_calendrier':idCalendrierEvenement,'periodicite':periodicite,'occurrence':occurrence,'periodiqueAutonome':periodiqueAutonome,'type':type,'info':info,'state':state,'remarque':remarque},app.refreshPage);
				
				// on va sur la page d'affichage de l'événement.
			}else{
				alert('La date de fin est plus ancienne que la date de début!');
			}
		}

		/*
		 *  appelé au click du liens de sauvegarde d'un evenement. Cette fonction met à jour l'évenement mais ne recharge pas la page.
		 */
		this.saveEvenementSansRefresh = function(e){

			var idEvenement = $('#idEvenement').val();
			var uidEvenement = $('#uidEvenement').val();
			var periodiqueAutonome = $('#periodiqueAutonome').val();
			var nom = $('#nomDetail').val();
			//var lieu = $('#lieuDetail').val();
			var lieu = $('input[name=lieuDetail__sexyComboHidden]').val();

			var jourDebut = $('#jourDebutDetail').val();
			var heureDebut = $('#heureDebutDetail').val();
			var minuteDebut = $('#minuteDebutDetail').val();
			
			// transforme une date au format dd-mm-yyyy en une date au format mysql datetime yyyy-mm-dd
			var jourDebutTab = jourDebut.split('-');
			var jourDebutMysql = jourDebutTab[2]+'-'+jourDebutTab[1]+'-'+jourDebutTab[0];
			var dateDebut = jourDebutMysql+' '+heureDebut+':'+minuteDebut+':00';
			var dateDebutTimeStamp = Date.parse(jourDebutTab[2]+'/'+jourDebutTab[1]+'/'+jourDebutTab[0]);

			var jourFin = $('#jourFinDetail').val();
			var heureFin = $('#heureFinDetail').val();
			var minuteFin = $('#minuteFinDetail').val();
			
			// transforme une date au format dd-mm-yyyy en une date au format mysql datetime yyyy-mm-dd
			var jourFinTab = jourFin.split('-');
			var jourFinMysql = jourFinTab[2]+'-'+jourFinTab[1]+'-'+jourFinTab[0];
			var dateFin = jourFinMysql+' '+heureFin+':'+minuteFin+':00';
			var dateFinTimeStamp = Date.parse(jourFinTab[2]+'/'+jourFinTab[1]+'/'+jourFinTab[0]);
			
			var idCalendrierEvenement = $('#idCalendrierEvenement').val();

			var description = $('#descriptionDetail').val();
			var periodicite = $('#periodiciteEvenement').val();
			var occurrence = $('#nbOccurrence').val();
			var type = $('#typeEvenement').val();
		//	var info = $('#infoEvenement').val();
			var info = $('input[name=infoEvenement__sexyComboHidden]').val();

			var state = $('#stateEvenement').val();
			var remarque = $('#remarqueEvenement').val();

			var jourComplet = $('input[#jourComplet][checked]').val();  // on ou nul
			if (jourComplet=="on") {			
				// dateDebut = jourDebut+' 00:00:01';
				// dateFin = jourFin+' 23:59:59';
				jourComplet = true;
			}else{
				jourComplet = false;
			}

			// vérifie que la date de fin soit plus grande ou égale que la date de début avant de sauver
			if (dateFinTimeStamp>=dateDebutTimeStamp) {
				// met à jour les tags
				var tags = $('#tags').val();
				var urlTag = chemin+"groupe/tag.html?type=evenement&id="+idEvenement+"&tag="+tags;
				$.get(urlTag,{'toto':''});

				// update l'évenement dans la base de donnée
				var url = chemin+"evenement/"+idEvenement+"-evenement.html?update";

				// url , param, fonction de callback
				$.post(url,{'id':idEvenement,'uid':uidEvenement,'nom':nom,'lieu':lieu,'date_debut':dateDebut,'date_fin':dateFin,'description':description,'jour_entier':jourComplet,'id_calendrier':idCalendrierEvenement,'periodicite':periodicite,'occurrence':occurrence,'periodiqueAutonome':periodiqueAutonome,'type':type,'info':info,'state':state,'remarque':remarque});
				// on va sur la page d'affichage de l'événement.
			}else{
				alert('La date de fin est plus ancienne que la date de début!');
			}
		}


		/*
		 *  fonction d'enregistrement des tags de l'événement
		 */
		this.saveTags = function(){
			var idEvenement = $('#idEvenement').val();
			var tags = $('#tags').val();
			var url = chemin+"groupe/tag.html?type=evenement&id="+idEvenement+"&tag="+tags;
			$.get(url,{'toto':''});
		}
		
		/*
		 *  permet de remplir un champ texte avec un mot clé
		 */
		this.proposeMotCle = function(mot){
			var tags = $('#tags').val();
			nouveauxTags = tags+','+mot;
			$('#tags').val(nouveauxTags);
		}
		
		/*
		 *  Cette fonction permet d'ajouter un lieu. Les infos son moins complètes que dans la page dédiée. C'est poru dépanner.
		 */
		this.createLieu = function(e){

			var nom = $('#inputNom').val();
			var categorie = $('#inputCategorie').val();
			var commune = $('#inputCommune').val();

			// url de création d'un événement
			var url = chemin+"lieu/lieu.html?add";

			// url , param, fonction de callback
			$.post(url,{'nom':nom,'categorie':categorie,'commune':commune},app.finAjoutLieu);
		}
		
		/*
		 *  fonction appellée après avoir créer un lieu
		 */
		this.finAjoutLieu = function(id){
			
		}
		
		/*
		 *  appelé au clic du bouton "nouvel événement". Permet d'ajouter un événement
		 */
		this.newEvenement = function(e){
			var mois = $('#choixMois').val();
			var annee = $('#choixAnnee').val();
			var jour = $('#choixJour').val();
			
			
			var dateDebut = annee+'-'+mois+'-'+jour+' '+'12:00:00'; // choix arbitraire d'une heure de début.
			var dateFin = annee+'-'+mois+'-'+jour+' '+'13:00:00'; // choix arbitraire une heure après l'heure de début
			
			// crée l'événement dans la base de donnée
			var url = chemin+"evenement/evenement.html?add";

			// url , param, fonction de callback
			$.post(url,{'nom':'nouvel événement','date_debut':dateDebut,'date_fin':dateFin},app.showModifierEvenement);
		}		
		
		/*
		 *  Fonction qui compose une url à partir des valeurs actuelles des filtres.
		 */
		this.refreshFilter = function(){
			
			
			
			var vue = $('#choixVue').val();
			var mois = $('#choixMois').val();
			var annee = $('#choixAnnee').val();
			var jour = $('#choixJour').val();
			var idType = $('#filtreTye').val();
			var idEtat = $('#filtreEtat').val();
			var noVp = $('#filtreNoVp').val();
			var tagsVirgule = $('#filtreTags').val();
			var idCalendrier = $('#filtreCalendrier').val();
			var idLieu = $('#filtreLieu').val();
			var format = $('#choixFormat').val();
			// console.log(tagsVirgule);
			
			var url = chemin+"calendrier/";
			
			if (format!='html') {
				url = chemin+"calendrier/calendrier."+format;
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
			if (idEtat) {
				filtre = filtre+'&filtreEtat='+idEtat;
			};
			
			//console.log(url+filtre);
			// recharge la page avec les bons filtres.
			window.location.href = url+filtre;
		}
		
		/*
		 *  Fonction qui compose une url à partir des valeurs actuelles des filtres.
		 */
		this.refreshFilterPdf = function(){
			
			
			
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
			
			//console.log(url+filtre);
			// recharge la page avec les bons filtres.
			window.location.href = url+filtre;
		}
		
		/*
		 *  refresh de la page
		 */
		this.refreshPage = function(){
			url = window.location.href;
			window.location.href = url;
		}
		
		/*
		 *  supprime l'événement
		 */
		this.deleteEvenement = function(id){
			var id = $('#idEvenement').val();
			this.hideMasque();
			
			var url = chemin+"evenement/"+id+"-evenement.html?delete";
			
			// url , param, fonction de callback
			$.post(url,{'id':id},app.refreshPage);
		}
		
		/*
		 *  Fonction qui compose une url pour aller au mois suivant en tenant compte des valeurs actuelles des filtres.
		 */
		this.moisSuivant = function(){
			
			
			var vue = $('#choixVue').val();
			var dateMoisSuivant = $('#dateMoisProchain').val();
			
			var idType = $('#filtreTye').val();
			var idEtat = $('#filtreEtat').val();
			var noVp = $('#filtreNoVp').val();
			var tagsVirgule = $('#filtreTags').val();
			var idCalendrier = $('#filtreCalendrier').val();
			var idLieu = $('#filtreLieu').val();
			
			var url = chemin+"calendrier/";
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
			if (idEtat) {
				filtre = filtre+'&filtreEtat='+idEtat;
			};
						
			// recharge la page avec les bons filtres.
			window.location.href = url+filtre;
		}
		
		/*
		 *  Fonction qui compose une url pour aller au mois précédent en tenant compte des valeurs actuelles des filtres.
		 */
		this.moisPrecedent = function(){
			
			var vue = $('#choixVue').val();
			var dateMoisPasse = $('#dateMoisPasse').val();
			
			var idType = $('#filtreTye').val();
			var idEtat = $('#filtreEtat').val();
			var noVp = $('#filtreNoVp').val();
			var tagsVirgule = $('#filtreTags').val();
			var idCalendrier = $('#filtreCalendrier').val();
			var idLieu = $('#filtreLieu').val();
			
			var url = chemin+"calendrier/";
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
			if (idEtat) {
				filtre = filtre+'&filtreEtat='+idEtat;
			};
						
			// recharge la page avec les bons filtres.
			window.location.href = url+filtre;
		}
		
		
		/*
		 *  Permet d'ajouter un nouvel événement avec les données de l'événement dont l'id est passé en paramètre
		 */
		this.dupliquerEvenement = function(id){
			
			// url de l'événement à dupliquer
			var url = chemin+"evenement/"+id+"-evenement.html?duplicate";

			// url , param, fonction de callback
			$.post(url,{'toto':'toto'},app.showModifierEvenement);
		}

	} // Application


  });
// })(jQuery);


function echo(str){
	try{
		console.log(str);
	}
	catch(e){alert(str)}
}