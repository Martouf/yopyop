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
		
		
		//rend draggable tous les éléments qui ont la class "drag"
		app.addDraggable($('.drag'));
		
		// on crée les zone droppable sur les demiHeure
		// $('.demiHeure').Droppable(
		// 		{
		// 			accept : 'drag',  // la zone droppable réagit aux bloc de class 'drag'
		// 			
		// 			//permet de modifier les styles suivant l'etat drop ou non
		// 			//activeclass:    'activeEmplacement',
		// 			//hoverclass:        'hoverEmplacement',
		// 		
		// 			//Dans les tolérances possible (pointer, fit, intersect) le drop va réagir à l'intersection des zone
		// 			tolerance: 'pointer',
		// 			//on définit ce qu'il va se passer lorsqu'on drop un élément dans la zone. On passe l'élément draggé en paramètre.
		// 			ondrop:  app.onDropDemiHeure,
		// 		fit: true
		// 	}
		// );
		
		//  Cette fonction préactive le redimensionnement.
		// l'événement mousedown impose étrangement un double clic.
		// donc cette fonction préactive le redimensionnement
		$(".resizeHandle").each( function(event){ 
			//id = $(this).parent().attr("id");
			$(this).parent().Resizable({
				minHeight: 16, // ne semble pas respecté... on obtient 13 !!
				maxHeight: 768,
				handlers: {s: this},
				onStop: function(){	},
				onResize: function(size){}
			});
		});
		
		
		// active le redimensionnement d'un bloc événement lors du clic
		$(".resizeHandle").mousedown( function(event){ 
			id = $(this).parent().attr("id");
			
		//	console.log("id evenement:"+id);
			
			$(this).parent().Resizable({
				minHeight: 13,
				maxHeight: 768,
				handlers: {s: this},
				onStop: app.onStopResizeEvenement,
				onResize: app.onResizeEvenement
			});
		});
		
		// ajout d'un événement au clic sur une heure
		$('.heure').click(app.addEvenement);
		
		
		////////// gestion des jours entiers //////////
		app.addDraggableJourEntier($('.dragJourEntier'));
		
		$('.jourEntier').Droppable(
				{
					accept : 'dragJourEntier',  // la zone droppable réagit aux blocs de class 'drag'
					
					//permet de modifier les styles suivant l'etat drop ou non
					//activeclass:    'activeEmplacement',
					//hoverclass:        'hoverEmplacement',
				
					//Dans les tolérances possible (pointer, fit, intersect) le drop va réagir à l'intersection des zone
					tolerance: 'intersect',
					//on définit ce qu'il va se passer lorsqu'on drop un élément dans la zone. On passe l'élément draggé en paramètre.
					ondrop:  app.onDropEvenementJourEntier,
				fit: true
			}
		);
		
		// ajout d'un événement au double clic dans la zone des jours entiers
		$('.jourEntier').dblclick(app.addEvenementJourEntier);
		
		// ajout d'un événement au clic du bouton
		$('a#boutonNewEvenement').click(function() {
			app.newEvenement();
			return false;
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
			
			this.showMasque();
			
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
		

		
		/*
		 *  supprime l'événement
		 */
		this.deleteEvenement = function(id){
			var id = $('#idEvenement').val();
			app.hideMasque();
			
			var url = chemin+"evenement/"+id+"-evenement.html?delete";
			
			// url , param, fonction de callback
			$.post(url,{'id':id});
			
			//met à jour l'affichage du nom
			$("#"+id).hide('slow');
		}
		
		/*
		 *  refresh de la page
		 */
		this.refreshPage = function(){
			url = window.location.href;
			window.location.href = url;
		}
		
		/////  Drag and Drop  ////
		
		/*
		 *  fonction pour rendre draggable un élément
		 */
		this.addDraggable = function(element){
			element.Draggable(
				{
					zIndex: 100,
					opacity: 0.7,
					grid: [102,8],   // x-y
				//	handle: '.poignee',
					//cursorAt: { top: 0, left: 0 },
					containment: 'calendrier',
					onStop: app.onDropEvenement,
					onDrag: app.onDragging
				}
			)
		}
		
		/*
		 *  fonction qui contient le comportement en cas de Drop d'un bloc événement (ou plutôt, fin de drag)
		 */
		this.onDropEvenement = 	function(drag){
				//on récupère l'id de l'évéenement dropé
				var id_evenement=$(this).attr('id');
				//console.log("drop de:"+id_evenement);
				
				//obtient le positionnement en pixel du bloc événement
				var top = $(this).css('top'); 
				top = top.replace("px",""); 
				top = parseInt(top);
				
				var heureDebut = app.height2time(top);
			//	console.log('Debut:'+heureDebut.heure+':'+heureDebut.minute);
				$("#heureEvenement"+id_evenement).empty().append(heureDebut.heure+':'+heureDebut.minute);
				
				// on obtient la date du jour à partir de la distance left
				var left = $(this).css('left'); 
				left = left.replace("px",""); 
				left = parseInt(left);
				dateJour = app.getJourFromLeft(left);
				dateDebut = dateJour+" "+heureDebut.heure+':'+heureDebut.minute+':00';
				
				var height = $(this).height(); 
				var heureFin = app.height2time(top+height);
				dateFin = dateJour+" "+heureFin.heure+':'+heureFin.minute+':00';
				
				// console.log(dateDebut);
				// console.log(dateFin);
				
				// met à jour la base de donnée avec les modifications sur l'événement
				var url = chemin+"evenement/"+id_evenement+"-evenement.html?update";

				// url , param, fonction de callback
				$.post(url,{'id':id_evenement,'date_debut':dateDebut,'date_fin':dateFin});
		}
		
		/*
		 *  fonction qui contient le comportement pendant le dragging d'un bloc événement
		 */
		this.onDragging = 	function(coordonnees){
				//on récupère l'id de l'évéenement dropé
				var id_evenement=$(this).attr('id');
				
				//console.log("dragging :"+id_evenement);
				
				//obtient le positionnement en pixel du bloc événement
				var top = $(this).css('top'); 
				top = top.replace("px",""); 
				top = parseInt(top);
				
				var heureDebut = app.height2time(top);
			//	console.log('Debut:'+heureDebut.heure+':'+heureDebut.minute);
				$("#heureEvenement"+id_evenement).empty().append(heureDebut.heure+':'+heureDebut.minute);
		}
		
		
		/*
		 *  Applique les actions à faire pendant un redimensionnement d'un événement
		 */
		this.onResizeEvenement = function(size){						
				
			// redimensionne la hauteur du contenu de l'événement pour que la poignée de redimensionnement soit toujours en bas du bloc événement
			var height = $(this).height(); 
			nomHeight = height - 13;
			$(this).children('.nomEvenement').height(nomHeight);
		//	console.log("#nomEvenement"+id);
			
			//obtient le positionnement en pixel du bloc événement
			var top = $(this).css('top'); 
			top = top.replace("px",""); 
			top = parseInt(top);
			
			var left = $(this).css('left'); 
			left = left.replace("px",""); 
			left = parseInt(left);
			

		}
		
		/*
		 *  Applique les actions à faire pendant un redimensionnement d'un événement
		 */
		this.onStopResizeEvenement = function(){						
			var height = $(this).height(); // hauteur du bloc (donc durée)
			
			// obtient le positionnement en pixel du bloc événement
			var top = $(this).css('top'); 
			top = top.replace("px",""); 
			top = parseInt(top);
			
			var left = $(this).css('left'); 
			left = left.replace("px",""); 
			left = parseInt(left);
			
			// contraint le redimentionnement vertical sur une grille
			var grilleHoraire = 8; // quart d'heure 		
			hauteur = Math.round(height/grilleHoraire);
			hauteur = hauteur * grilleHoraire;
					
			if(height < grilleHoraire){ hauteur = grilleHoraire; } // contraint la taille minimale
			
			// applique la hauteur au bloc
			$(this).height(hauteur);
			
			// redimensionne la hauteur du contenu de l'événement pour que la poignée de redimensionnement soit toujours en bas du bloc événement
			nomHeight = hauteur - 13;
			$(this).children('.nomEvenement').height(nomHeight);
			
			//on récupère l'id de l'évéenement dropé
			var id_evenement=$(this).attr('id');
			//console.log("drop de:"+id_evenement);
			
			var heureDebut = app.height2time(top);
			
			// on obtient la date du jour à partir de la distance left
			var left = $(this).css('left'); 
			left = left.replace("px",""); 
			left = parseInt(left);
			dateJour = app.getJourFromLeft(left);
			dateDebut = dateJour+" "+heureDebut.heure+':'+heureDebut.minute+':00';
			
			var heureFin = app.height2time(top+hauteur); // ici la hauteur vient d'être modifiée
			dateFin = dateJour+" "+heureFin.heure+':'+heureFin.minute+':00';
			
			// console.log(dateDebut);
			// console.log(dateFin);
			
			// met à jour la base de donnée avec les modifications sur l'événement
			var url = chemin+"evenement/"+id_evenement+"-evenement.html?update";

			// url , param, fonction de callback
			$.post(url,{'id':id_evenement,'date_debut':dateDebut,'date_fin':dateFin});
			
			
		}
		
		/*
		 *  Converti une hauteur de pixel en un objet {heure,minute}
		 */
		this.height2time = function(height){
			// on converti une hauteur de pixel en nombre d'heure au format décimal
			heureDecimale = height/32;
			
			// on converti le nombre d'heure en seconde
			secondes = heureDecimale*3600;
			
			// on obtient le nombre d'heure entière et de minutes. On néglige les secondes vu la grille de mouvement imposée.
			heures = Math.floor(secondes / 3600);
			minutes = Math.floor((secondes - (heures * 3600)) / 60);
			
			 // affiche toujours les heures et les minutes avec 2 chiffres
			if(minutes==0){minutes='00'};
			
			heuresString =  heures.toString()
			longueur = heuresString.length;
			if (longueur<2) {
				heures = "0"+heures;
			}
			
			temps = {'heure':heures,'minute':minutes};
			return temps;
		}
		
		/*
		 *  obtient le jour au format mysql date: 2008-03-27 à partir de la valeur en pixel de l'attribut css left
		 */
		this.getJourFromLeft = function(left){
			// on détermine le jour à partir de données connues donc le calcul donne toujours un entier entre 0-6
			var jour = (left -51)/102;
			dateJour = $('#jour'+jour).val();
			return dateJour;
		}
		
		/*
		 *  appelé au clic d'une heure. Permet d'ajouter un événement
		 */
		this.addEvenement = function(e){
			
			// on va obtenir la date de début et fin d'un nouvel événement ajouté à l'endroit cliqué		
			var idHeure = $(this).attr('id');
			
			// découpage d'un string dans le genre: jour3_heure13
			moment = idHeure.split('_');
			jour = moment[0];
			heure= moment[1];
			jour = jour.replace("jour",""); 
			heure = heure.replace("heure",""); 
			
			nbHeure = parseInt(heure);
			nbJour = parseInt(jour);
			
			dateJour = $('#jour'+jour).val();
			
			// par défaut un nouvel événement dure une heure
			heureFin = parseInt(heure);
			heureFin = heureFin + 1;
			heureFin = heureFin.toString();
			
			longueur = heure.length;
			if (longueur<2) {
				heure = "0"+heure;
			}
			longueur = heureFin.length;
			if (longueur<2) {
				heureFin = "0"+heureFin;
			}
			
			dateDebut = dateJour+" "+heure+':00:00';
			dateFin = dateJour+" "+heureFin+':00:00';
			
			// console.log("debut: "+dateDebut);
			// console.log("Fin: "+dateFin);
			
			// crée l'événement dans la base de donnée
			var url = chemin+"evenement/evenement.html?add";

			// url , param, fonction de callback
			$.post(url,{'nom':'nouvel événement','date_debut':dateDebut,'date_fin':dateFin},app.addIdBlocEvenement);
			
			
			var top = 32*nbHeure;
			var left = 51+ (102*nbJour);
			var idEvenementTempo = 0; // id temporaire avant que l'on reçoivent le véritable id
			
			// ajoute un bloc événement. Etrange il me semble mettre le onclick dans le div.. et après il n'est utilisable que dans le p du div !!!??  ... mais ça marche alors on touche plus !
			$("#calendrier").append("<div id=\""+idEvenementTempo+"\" class=\"evenement drag divers\" style=\"top:"+top+"px; left:"+left+"px; height:32px;\"  onclick=\"\" ><h3 class=\"poignee\" id=\"heureEvenement"+idEvenementTempo+"\">"+heure+":00</h3><p id=\"nomEvenement"+idEvenementTempo+"\" class=\"nomEvenement\" style=\"height:19px;\" >Nouvel evenement</p><div class=\"resizeHandle\" id=\"resizeHandle"+idEvenementTempo+"\">&nbsp;</div></div>");

		}
		
		/*
		 *  modifie dynamiquement le bloc idEvenement0 nouvellement créer pour le mettre des références sur le bon id.
		 */
		this.addIdBlocEvenement = function(id){
			// modifie l'id générique du nouveau bloc créé
			$('#0').attr('id',id);
		//	$('#0').attr('onclick',"app.showModifierEvenement("+id+")");
			$('#heureEvenement0').attr('id',"heureEvenement"+id);
			$('#nomEvenement0').attr('onclick',"app.showModifierEvenement("+id+")");
		//	$('#nomEvenement0').attr('rel',"app.showModifierEvenement("+id+")");// => ok.. et onclick marche pas :-(
			$('#nomEvenement0').attr('id',"nomEvenement"+id);
			$('#resizeHandle0').attr('id',"resizeHandle"+id);
			
			// rend l'événement draggable
			app.addDraggable($("#"+id));


			//  Cette fonction préactive le redimensionnement.
			// l'événement mousedown impose étrangement un double clic.
			// donc cette fonction préactive le redimensionnement
			$(".resizeHandle").each( function(event){ 
				//id = $(this).parent().attr("id");
				$(this).parent().Resizable({
					minHeight: 16, // ne semble pas respecté... on obtient 13 !!
					maxHeight: 768,
					handlers: {s: this},
					onStop: function(){	},
					onResize: function(size){}
				});
			});
			
			
			$('#resizeHandle'+id).mousedown( function(event){ 
				id = $(this).parent().attr("id");
				$(this).parent().Resizable({
					minHeight: 13,
					maxHeight: 768,
					handlers: {s: this},
					onStop: app.onStopResizeEvenement,
					onResize: app.onResizeEvenement
				});
			});
		}
		
		
		/////  Jours Entiers  ////
		
		/*
		 *  fonction pour rendre draggable un élément
		 */
		this.addDraggableJourEntier = function(element){
			element.Draggable(
				{
					zIndex: 100,
					opacity: 0.7,
					grid: [102,16],   // x-y
				//	handle: '.poignee',
					//cursorAt: { top: 0, left: 0 },
					containment: 'jourEntier'
				//	onStop: app.onDropEvenement,
				//	onDrag: app.onDragging
				}
			)
		}
		
		/*
		 *  fonction qui contient le comportement en cas de Drop d'un bloc événement.
		 */
		this.onDropEvenementJourEntier = function(drag){

			// on récupère les id de l'événement draggé et sur quel jour
			var id_evenement= $(drag).attr('id');
			var id_jour=$(this).attr('id');

			// console.log("drop de : "+id_evenement);
			// console.log("drop sur : "+id_jour);

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
		 *  appelé au doubleclic d'une case de jourEntier. Permet d'ajouter un événement de jouur entier
		 */
		this.addEvenementJourEntier = function(e){
			
			// on va obtenir la date de début et fin d'un nouvel événement ajouté à l'endroit cliqué		
			var idJour = $(this).attr('id');
			console.log("ajouter un événement ici: "+idJour);
			
			var dateDebut = idJour+' '+'12:00:00'; // choix arbitraire d'une heure de début.
			var dateFin = idJour+' '+'13:00:00'; // choix arbitraire une heure après l'heure de début
			
			// crée l'événement dans la base de donnée
			var url = chemin+"evenement/evenement.html?add";

			// url , param, fonction de callback
			$.post(url,{'nom':'nouvel événement','date_debut':dateDebut,'date_fin':dateFin,'jour_entier':'true'},app.addIdBlocEvenementJourEntier);
			

			var idEvenementTempo = 0; // id temporaire avant que l'on reçoivent le véritable id
			 
			// ajoute un bloc événement
			$('#'+idJour).append("<div id=\""+idEvenementTempo+"\" class=\"moisEvenement dragJourEntier repas\" style=\"width:100px;\" title=\"Nouvel événement\" onclick=\"\" ><input type=\"hidden\" class=\"hDebut\" value=\"12:00:00\" id=\"hDebut"+idEvenementTempo+"\" name=\"hDebut"+idEvenementTempo+"\" /><input type=\"hidden\" class=\"hFin\" value=\"13:00:00\" id=\"hFin"+idEvenementTempo+"\" name=\"hFin"+idEvenementTempo+"\" /><span id=\"nomEvenement"+idEvenementTempo+"\" class=\"nomEvenement\">Nouvel événement</span>");
		}
		
		/*
		 *  modifie dynamiquement le bloc idEvenement=0 nouvellement créé pour le mettre des références sur le bon id.
		 */
		this.addIdBlocEvenementJourEntier = function(id){
			// modifie l'id générique du nouveau bloc créé
			$('#0').attr('id',id);
			$('#nomEvenement0').attr('onclick',"app.showModifierEvenement("+id+")");
			$('#nomEvenement0').attr('id',"nomEvenement"+id);
						
			// rend l'événement draggable
			app.addDraggableJourEntier($("#"+id));
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
			var type = $('#typeEvenement').val();
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
		 *  Fonction qui compose une url pour aller à la semaine suivante en tenant compte des valeurs actuelles des filtres.
		 */
		this.semaineSuivante = function(){
			
			
			var vue = $('#choixVue').val();
			var dateSemaineProchaine = $('#dateSemaineProchaine').val();
			
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
			if (dateSemaineProchaine) {
				filtre = filtre+'&datecourante='+dateSemaineProchaine;
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
		 *  Fonction qui compose une url pour aller à la semaine précédente en tenant compte des valeurs actuelles des filtres.
		 */
		this.semainePrecedente = function(){
			
			
			var vue = $('#choixVue').val();
			var dateSemainePassee = $('#dateSemainePassee').val();
			
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
			if (dateSemainePassee) {
				filtre = filtre+'&datecourante='+dateSemainePassee;
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
				
		
	} // Application
  });
// })(jQuery);

