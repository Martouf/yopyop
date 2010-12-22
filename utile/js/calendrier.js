/*******************************************************************************************
 * Nom du fichier		: calendrier.js
 * Date					: 12 janvier 2009
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu at ecodev.ch
 * But de ce fichier	: gérer la création d'un calendrier
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

		// create calendrier pour la page de création de calendrier
		$('a#createCalendrier').click(function() {
			app.createCalendrier();
			return false;
		 });
		
		// update calendrier
		$('a#saveCalendrier').click(function() {
			app.saveCalendrier();
			return false;
		 });
		
		// active le toggle de l'interface des heures au click de la checkbox du jour entier
		$('#distant').click(function(){ $('.toggleBloc').toggle()} );

		var distant = $('input[name=distant][checked]').val();  // on ou nul
		if (distant!="on") {			
			$('.toggleBloc').toggle();
		}
		
		// colorpicker
		$('#couleur').ColorPicker({
			color: $('#couleur').val(),
			onShow: function (colpkr) {
				$(colpkr).fadeIn(500);
				return false;
			},
			onHide: function (colpkr) {
				$(colpkr).fadeOut(500);
				return false;
			},
			onChange: function (hsb, hex, rgb) {
				$('#couleur').css('backgroundColor', '#' + hex);
				$('#couleur').val(hex);
			},
			livePreview: true
		});

	}); // ready


	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){


		///////////////  Création d'un calendrier ///////////
		// Les étapes de la création d'un calendrier sont les suivantes:
		// - récupération des valeurs de nom, description, info de modification et contenu du calendrier
		// - création du calendrier avec ces valeurs
		// - récupération de l'id du calendrier qui vient d'être créé
		// - récupération des tags et enregistrement de ceux-ci pour le nouvel id fourni
		// - redirection de l'utilisateur sur la page de visualisation (ou de modification selon choix de conception) du nouvel évenement
		
		
		/*
		 *  appelé au clic de la fonction de sauvegarde. Cette fonction permet d'ajouter un calendrier
		 */
		this.createCalendrier = function(e){
			
			var nom = $('#nom').val();
			var description = $('#description').val();
			var couleur = $('#couleur').val();
			var distant = $('input[name=distant][checked]').val();  // on ou nul
			if (distant=="on") {			
				distant = '1';
			}else{
				distant = '0';
			}
			
			if (distant=='1') {
				var urlCal = $('#url').val();
				var tags = $('#tags').val();
			}else{
				var urlCal = '';
				var tags = '';
			}

			// url de création d'un événement
			var url = chemin+"calendrier/calendrier.html?add";
			
			// url , param, fonction de callback
			$.post(url,{'nom':nom,'couleur':couleur,'distant':distant,'url':urlCal,'description':description,'tags':tags},app.goToCalendrier);
			
		}

		/*
		 *  redirige le visiteur sur la page du calendrier dont on passe l'id en paramètre
		 */
		this.goToCalendrier = function(id){
			//echo ("redirection....");
			url = chemin+"calendrier/"+id+"-calendrier.html";
		//	echo (url);
			window.location.href = url;
		}

		///////////// mise à jour d'un calendrier ///////////

		/*
		 *  appelé au click du liens de sauvegarde d'un calendrier. Cette fonction met à jour le calendrier.
		 */
		this.saveCalendrier = function(e){

			var idCalendrier = $('#idCalendrier').val();
			var nom = $('#nom').val();
			var description = $('#description').val();
			var couleur = $('#couleur').val();
			var distant = $('input[name=distant][checked]').val();  // on ou nul
			if (distant=="on") {			
				distant = '1';
			}else{
				distant = '0';
			}
			
			if (distant=='1') {
				var urlCal = $('#url').val();
				var tags = $('#tags').val();
			}else{
				var urlCal = '';
				var tags = '';
			}
			
	
			// update l'évenement dans la base de donnée
			var url = chemin+"calendrier/"+idCalendrier+"-calendrier.html?update";
			
			// url , param, fonction de callback
			$.post(url,{'id':idCalendrier,'nom':nom,'couleur':couleur,'url':urlCal,'tags':tags,'description':description,'distant':distant},app.finUpdate);
		}
		
		
		/*
		 *  callback quand une mise à jour du calendrier est effectuée
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

	} // Application


  });
})(jQuery);


function echo(str){
	try{
		console.log(str);
	}
	catch(e){alert(str)}
}