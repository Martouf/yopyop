/*******************************************************************************************
 * Nom du fichier		: evenement_mois.js
 * Date					: 7 avril 2008
 * modif				: 5 août 2009 => adaptation du calendrier vp à un calendrier générique
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Fournir la partie javascript de la page des evenements
 *******************************************************************************************
 * 
 *
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
		
		//rend draggable tous les éléments qui ont la class "drag"
		app.addDraggable($('.drag'));
		
	//	on crée les zones droppable sur les jours pour les événements d'une durée infiérieure au jour
		$('.blocJour').Droppable(
				{
					accept : 'drag',  // la zone droppable réagit aux blocs de class 'drag'
					
					//permet de modifier les styles suivant l'etat drop ou non
					//activeclass:    'activeEmplacement',
					//hoverclass:        'hoverEmplacement',
				
					//Dans les tolérances possible (pointer, fit, intersect) le drop va réagir à l'intersection des zone
					tolerance: 'intersect',
					//on définit ce qu'il va se passer lorsqu'on drop un élément dans la zone. On passe l'élément draggé en paramètre.
					ondrop:  app.onDropEvenement,
				fit: true
			}
		);
				
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
		
		// $('#filtreTye').change(app.refreshFilter);
		// $('#filtreEtat').change(app.refreshFilter);
		// $('#filtreNoVp').change(app.refreshFilter);
		$('#sendTags').click(app.refreshFilter);
		$('#filtreCalendrier').change(app.refreshFilter);
		$('#filtreLieu').change(app.refreshFilter);
		
	}); // ready
	
	
	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){
		
		// variables globales utilisées en cas de drag&drop de longs événements 
		this.id_evenement = '';
		this.id_jour = '';
		this.drag = '';
		
		
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
			
			$('#blocNavigationFleche').hide(); // masque les flèches qui ne fonctionne qu'en mode liste
			
			// charge le datePicker
			Date.format = 'dd-mm-yyyy'; //yyyy-mm-dd
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
				// 					app.saveTags();
				// 					return false;
				// 				 });
				
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
		
		// /*
		//  *  va chercher le nom du lieu qui correspond à l'id sélectionné et l'affiche dans le preview
		//  */
		// this.previewNomLieu = function(){
		// 	idSelected = $('#lieuDetail').val();
		// 	// url , param, fonction de callback (optionnelle)
		// 	$.get(chemin+"lieu/"+idSelected+"-lieu.html",{'nom':''},app.afficheNomLieu);
		// }
		// 
		// 	/*
		// 	 *  affiche le nom d'un lieu
		// 	 */
		// 	this.afficheNomLieu = function(nom){
		// 		$('#mp_lieuDetail').empty().append(nom);
		// 	}
		
		
		/*
		 *  met à jour l'événement.. utilisé ??   => non... pas utilisé !!
		 */
		this.updateEvenement = function(id){
			var id = $('#idDetail').val();
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
			
			var jourComplet = $('input[#jourComplet][checked]').val();  // on ou nul
			if (jourComplet=="on") {			
				// dateDebut = jourDebut+' 00:00:01';
				// dateFin = jourFin+' 23:59:59';
				jourComplet = true;
			}else{
				jourComplet = false;
			}
			var description = $('#descriptionDetail').val();
			var utile = '';
			app.hideMasque();
			
			//met à jour les tags
			var tags = $('#tags').val();
			var urlTag = chemin+"utile/ajax/tag.php";
			
			// url , param, fonction de callback (optionnelle)
			$.get(urlTag,{'type':'evenement','id':id,'tag':tags});
			
			
			var url = chemin+"evenement/"+id+"-evenement.html?update";
			
			// url , param, fonction de callback
			$.post(url,{'id':id,'nom':nom,'lieu':lieu,'date_debut':dateDebut,'date_fin':dateFin,'description':description,'jour_entier':jourComplet,'utile':utile},app.refreshPage);
			
			
			//met à jour l'affichage du nom
		//	$("#nomEvenement"+id).empty().append(nom);
		}
		
		
		/*
		 *  supprime l'événement
		 */
		this.deleteEvenement = function(id){
		//	var id = $('#idDetail').val();
			var id = $('#idEvenement').val();
			this.hideMasque();
			
			var url = chemin+"evenement/"+id+"-evenement.html?delete";
			
			// url , param, fonction de callback
			$.post(url,{'id':id});
			
			//met à jour l'affichage du nom
			$("#"+id).hide('slow');
		}
		
		
		/////  Drag and Drop  ////
		
		/*
		 *  fonction pour rendre draggable un élément
		 */
		this.addDraggable = function(element){
			element.Draggable(
				{
					zIndex: 300,
					opacity: 0.7,
					grid: [101,16],   // x-y
				//	handle: '.poignee',
					//cursorAt: { top: 0, left: 0 },
					containment: 'calendrierMois',
				//	onStop: app.onDropEvenement,
				//	onDrag: app.onDragging
				}
			)
		}
		
		/*
		 *  fonction qui contient le comportement en cas de Drop d'un bloc événement.
		 */
		this.onDropEvenement = 	function(drag){

				// on récupère les id de l'événement draggé et sur quel jour
				var id_evenement= $(drag).attr('id');
				var id_jour=$(this).attr('id');
				
				 // console.log("drop de : "+id_evenement);
				 // 				 console.log("drop sur : "+id_jour);
				
				// on sauve de manière globale ces infos
				app.id_evenement = id_evenement;
				app.id_jour = id_jour;
				app.drag = drag;
				
				// l'événement change de date. La date de début et la date de fin sont actualisées, mais pas l'heure.
				// on obtient l'heure de début et l'heure de fin.
				var hDebut = $(drag).children('.hDebut').val();
				var hFin = $(drag).children('.hFin').val();
				var nbJour = $(drag).children('.nbJour').val(); // obtient le nb de jour de différence entre la date de début et la date de fin
				
				if (nbJour>0) {
					// Obtient calcule la nouvelle date de fin via un service ajax.. vu l'asychronité de la requête. La fonction s'oocupe aussi de la mise à jour de l'événement
					var url = chemin+"utile/ajax/calcule_date.php?requete=+%20"+nbJour+"%20day&format=Y-m-d";

					// url , param, fonction de callback
					$.get(url,{'date':id_jour},app.updateEvenementMultiJour);
					
				}else{
					var newDateDebut = id_jour+' '+hDebut;
					var newDateFin = id_jour+' '+hFin;
					
					// console.log("nouvelle date debut: "+newDateDebut);
					// 	console.log("nouvelle date fin: "+newDateFin);

					// met à jour la base de donnée avec les modifications sur l'événement
					var url = chemin+"evenement/"+id_evenement+"-evenement.html?update";

					// url , param, fonction de callback
					$.post(url,{'id':id_evenement,'date_debut':newDateDebut,'date_fin':newDateFin});
				}		
		}
		
		/*
		 *  met à jour un événement qui est sur plusieurs jours.
		 */
		this.updateEvenementMultiJour = function(dateFin){
			
			// l'événement change de date. La date de début et la date de fin sont actualisées, mais pas l'heure.
			// on obtient l'heure de début et l'heure de fin.
			var hDebut = $(app.drag).children('.hDebut').val();
			var hFin = $(app.drag).children('.hFin').val();
			
			var newDateDebut = app.id_jour+' '+hDebut;
			var newDateFin = dateFin+' '+hFin;
			
			// console.log("nouvelle date debut: "+newDateDebut);
			// 	console.log("nouvelle date fin: "+newDateFin);
	
			// met à jour la base de donnée avec les modifications sur l'événement
			var url = chemin+"evenement/"+app.id_evenement+"-evenement.html?update";

			// url , param, fonction de callback
			$.post(url,{'id':app.id_evenement,'date_debut':newDateDebut,'date_fin':newDateFin},app.refreshPage);
		}

		
		/*
		 *  refresh de la page
		 */
		this.refreshPage = function(){
			url = window.location.href;
			window.location.href = url;
		}
	
		// /*
		// 	 *  fonction qui contient le comportement pendant le dragging d'un bloc événement
		// 	 */
		// 	this.onDragging = 	function(coordonnees){
		// 			console.log('dragging ');
		// 			
		// 			//on récupère l'id de l'évéenement dropé
		// 			var id_evenement=$(this).attr('id');
		// 			
		// 			//console.log("dragging :"+id_evenement);
		// 	}
		// 	
		
		/*
		 *  appelé au clic d'une heure. Permet d'ajouter un événement
		 */
		this.addEvenement = function(e){
			
			// on va obtenir la date de début et fin d'un nouvel événement ajouté à l'endroit cliqué		
			var idJour = $(this).attr('id');
			//console.log("ajouter un événement ici: "+idJour);
			
			var dateDebut = idJour+' '+'12:00:00'; // choix arbitraire d'une heure de début.
			var dateFin = idJour+' '+'13:00:00'; // choix arbitraire une heure après l'heure de début
			
			// crée l'événement dans la base de donnée
			var url = chemin+"evenement/evenement.html?add";

			// url , param, fonction de callback
			$.post(url,{'nom':'nouvel événement','date_debut':dateDebut,'date_fin':dateFin},app.addIdBlocEvenement);
			

			var idEvenementTempo = 0; // id temporaire avant que l'on reçoivent le véritable id
			 
			// ajoute un bloc événement
			$('#'+idJour).append("<div id=\""+idEvenementTempo+"\" class=\"moisEvenement drag repas\" style=\"width:100px;\" title=\"Nouvel événement\" onclick=\"\" ><input type=\"hidden\" class=\"hDebut\" value=\"12:00:00\" id=\"hDebut"+idEvenementTempo+"\" name=\"hDebut"+idEvenementTempo+"\" /><input type=\"hidden\" class=\"hFin\" value=\"13:00:00\" id=\"hFin"+idEvenementTempo+"\" name=\"hFin"+idEvenementTempo+"\" /><span id=\"nomEvenement"+idEvenementTempo+"\" class=\"nomEvenement\">Nouvel événement</span>");
		}
		
		/*
		 *  modifie dynamiquement le bloc idEvenement=0 nouvellement créé pour le mettre des références sur le bon id.
		 */
		this.addIdBlocEvenement = function(id){
			// modifie l'id générique du nouveau bloc créé
			$('#0').attr('id',id);
			$('#nomEvenement0').attr('onclick',"app.showModifierEvenement("+id+")");
			$('#nomEvenement0').attr('id',"nomEvenement"+id);
						
			// rend l'événement draggable
			app.addDraggable($("#"+id));
		}
		
		//////////////////////////////////////////////////////////////////////////////////////////
		/////  Formulaire de modification d'un événement (fonctions adaptées de evenement.js) ////
		//////////////////////////////////////////////////////////////////////////////////////////
		
		/*
		 *  appelé au click du lien de sauvegarde d'un evenement. Cette fonction met à jour l'évenement.
		 */
		this.saveEvenement = function(e){
			
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
		//	var type = $('#typeEvenement').val();
			var type ='';
			var info = $('input[name=infoEvenement__sexyComboHidden]').val();
			
		//	var state = $('#stateEvenement').val();
			var state = '';
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
		 *  Fonction qui compose une url à partir des valeurs actuelles des filtres.
		 */
		this.refreshFilter = function(){
			
			var baseUrl = $('#baseUrl').val(); // http://erenweb.ecodev.ch
			
			var vue = $('#choixVue').val();
			var mois = $('#choixMois').val();
			var annee = $('#choixAnnee').val();
			var jour = $('#choixJour').val();
		//	var idType = $('#filtreTye').val();
		//	var idEtat = $('#filtreEtat').val();
		//	var noVp = $('#filtreNoVp').val();
			var idType = '';
			var idEtat ='';
			var noVp = '';
			var tagsVirgule = $('#filtreTags').val();
			var idCalendrier = $('#filtreCalendrier').val();
			var idLieu = $('#filtreLieu').val();
			// console.log(tagsVirgule);
			
			var url = chemin+"calendrier/";
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
			if (idEtat) {
				filtre = filtre+'&filtreEtat='+idEtat;
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
			//	var idType = $('#filtreTye').val();
			//	var noVp = $('#filtreNoVp').val();
				var idType = '';
				var noVp = '';
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
			
			// solution où l'on ajoute un bloc dans le DOM
			// // url , param, fonction de callback
			// 		$.post(url,{'nom':'nouvel événement','date_debut':dateDebut,'date_fin':dateFin},app.addIdBlocEvenement);
			// 		
			// 
			// 		var idEvenementTempo = 0; // id temporaire avant que l'on reçoivent le véritable id
			// 		 
			// 		// ajoute un bloc événement (TODO: plante si la vue courante ne nous permet pas de voir aujourd'hui !)
			// 		$('#'+annee+'-'+mois+'-'+jour).append("<div id=\""+idEvenementTempo+"\" class=\"moisEvenement drag repas\" style=\"width:100px;\" title=\"Nouvel événement\" onclick=\"\" ><input type=\"hidden\" class=\"hDebut\" value=\"12:00:00\" id=\"hDebut"+idEvenementTempo+"\" name=\"hDebut"+idEvenementTempo+"\" /><input type=\"hidden\" class=\"hFin\" value=\"13:00:00\" id=\"hFin"+idEvenementTempo+"\" name=\"hFin"+idEvenementTempo+"\" /><span id=\"nomEvenement"+idEvenementTempo+"\" class=\"nomEvenement\">Nouvel événement</span>");
				}
		
		/*
		 *  Fonction qui compose une url pour aller au mois suivant en tenant compte des valeurs actuelles des filtres.
		 */
		this.moisSuivant = function(){
			
			var baseUrl = $('#baseUrl').val(); // http://erenweb.ecodev.ch
			var vue = $('#choixVue').val();
			var dateMoisSuivant = $('#dateMoisProchain').val();
			

			//	var idType = $('#filtreTye').val();
			//	var idEtat = $('#filtreEtat').val();
			//	var noVp = $('#filtreNoVp').val();
				var idType = '';
				var idEtat = '';
				var noVp = '';
			
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
			window.location.href = baseUrl+url+filtre;
		}
		
		/*
		 *  Fonction qui compose une url pour aller au mois précédent en tenant compte des valeurs actuelles des filtres.
		 */
		this.moisPrecedent = function(){
			
			var baseUrl = $('#baseUrl').val(); // http://erenweb.ecodev.ch
			var vue = $('#choixVue').val();
			var dateMoisPasse = $('#dateMoisPasse').val();
			
			//	var idType = $('#filtreTye').val();
			//	var idEtat = $('#filtreEtat').val();
			//	var noVp = $('#filtreNoVp').val();
				var idType = '';
				var idEtat = '';
				var noVp = '';
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
			window.location.href = baseUrl+url+filtre;
		}
		
		
	} // Application
  });
// })(jQuery);