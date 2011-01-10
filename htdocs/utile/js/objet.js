/*******************************************************************************************
 * Nom du fichier		: objet.js
 * Date					: 29 juillet 2010
 * Modif				: 
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu (at) marfaux.ch
 * But de ce fichier	: Fournir la partie javascript de manipulation des objets. (modification)
 *******************************************************************************************
 * 
 *
 */

var app;  // variable globale qui représente mon application

// pour Gmap
var clickHandler;
var map;
var lat;
var lng;
var locations;
var bounds;
var geo;

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
		$("input#tags").autocomplete(chemin+"groupe/tags.html?list&type=objet", {
			multiple: true,
			minChars: 1
		});

		// crée une carte de base
		app.addGMapNeuchatel();
		
		//ajoute un écouteur
		GEvent.addListener(map, "click", function(overlay,point){app.ajouteClicMarker(overlay,point)});

		// update objet
		$('a#saveObjet').click(function() {
			app.saveObjet();
			return false;
		 });
		
		// tags
		$('a#enregistreTag').click(function() {
			app.saveTags();
			return false;
		 });
		
		// obtient les coordonnées depuis l'adresse via google
		$('a#getAdresse').click(function() {
			app.setMarkerFromAddress();
			return false;
		 });

		
		// marque le point courant sur la carte.
		app.markCurrentLatLong();
		
		
	}); // ready
	
	
	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){
		
		/*
		 *  Fonction qui ajoute une carte avec des données neuchâteloises
		 */
		this.addGMapNeuchatel = function(option){
			if (GBrowserIsCompatible()) {
				map = new GMap2(document.getElementById("map"));
				
				// Create tile layers
				
				var tileOrtho= new GTileLayer(new GCopyrightCollection("sitn"),1,17);
				tileOrtho.myLayers='ortho'; // ombrage_mnt25,ombrage_laser_terrain,plan_ensemble,ortho,communes
				tileOrtho.myFormat='image/jpeg';
				tileOrtho.myBaseURL='http://sitn.ne.ch/ogc-sitn-open/wms?';
		    	tileOrtho.getTileUrl=CustomGetTileUrl;
		
				var layer1=[G_NORMAL_MAP.getTileLayers()[0]]; 
				var layer2=[tileOrtho]; 
				var layer3=[G_HYBRID_MAP.getTileLayers()[0]];

				var cartePlanGoogle = new GMapType(layer1, G_SATELLITE_MAP.getProjection(), "Plan", G_SATELLITE_MAP);
				var carteNeuch = new GMapType(layer2, G_SATELLITE_MAP.getProjection(), "Neuchâtel", G_SATELLITE_MAP);
		    	var carteOrthoGoogle = new GMapType(layer3, G_SATELLITE_MAP.getProjection(), "Photos", G_SATELLITE_MAP);
		
				map.getMapTypes().length = 0;
		    	map.addMapType(cartePlanGoogle);
				map.addMapType(carteNeuch);
		    	map.addMapType(carteOrthoGoogle);

				map.setCenter(new GLatLng(46.995394778431226,6.9428551197052), 14,carteOrthoGoogle); // 17 tout près
				
				// ajoute les contrôles
				map.addControl(new GSmallMapControl());
				map.addControl(new GMapTypeControl());
			}else{
				alert("Sorry, the Google Maps API is not compatible with this browser");
			}
		}
		
		/*
		 *  Fonction appellée au clic de la carte
		 */
		this.ajouteClicMarker = function(overlay, point){
			map.clearOverlays();
			if (point) {
				map.addOverlay(new GMarker(point));
				map.panTo(point);
				latitude = point.lat();
				longitude = point.lng();
				
				// met à jour la coordonnée				
				$('#inputLatitude').val(latitude);
				$('#inputLongitude').val(longitude);
			}
		}

		/*
		 *  déplace le marker
		 */
		this.ajouteMarker = function(latitude, longitude){
				
				// met à jour le marker sur la carte
				map.clearOverlays();
			//	var latitude = parseFloat($('#placeLatitude').val());
			//	var longitude = parseFloat($('#placeLongitude').val());

				var point = new GLatLng(latitude,longitude);
				map.addOverlay(new GMarker(point));
				map.panTo(point);
		}

		
		/*
		 *  Affiche, un marqeur pour la latitude, longitude courante dans le formulaire.
		 */
		this.markCurrentLatLong = function(html){
			
			var latitude = parseFloat($('#objetLatitude').val());
			var longitude = parseFloat($('#objetLongitude').val());
			//console.log(longitude+','+latitude);
			
			// si latitude n'est pas défini, redirige au bord du lac de neuch
			if (longitude=='NaN') {
				app.ajouteMarker(46.93244765730184,6.8767547607421875);
			}else{
				app.ajouteMarker(latitude,longitude);
			}
		}
		
		
		/*
		 *  va chercher l'adresse dans le formulaire et demande à google via un géocodeur de créer une marker
		 */
		this.setMarkerFromAddress = function(){
			// efface la carte la carte
			map.clearOverlays();

			// on crée un Client Geocoder
			var geo = new GClientGeocoder();
			
			var adresse = $('#inputLieu').val();
			geo.getLatLng(adresse, app.setMarker);
		}
		
		/*
		 *  efface la carte et ajoute un marker à partir d'un poit GLatLng
		 */
		this.setMarker = function(point){
				
			// efface la carte
			map.clearOverlays();

			map.addOverlay(new GMarker(point));
			map.panTo(point);
			latitude = point.lat();
			longitude = point.lng();
			
			// met à jour la coordonnée				
			$('#inputLatitude').val(latitude);
			$('#inputLongitude').val(longitude);
		}
		

		///////////// mise à jour d'une objet ///////////

		/*
		 *  appelé au click du lien de sauvegarde d'une objet. Cette fonction met à jour la objet.
		 */
		this.saveObjet = function(e){

			var idObjet = $('#idObjet').val();
			var nom = $('#inputNom').val();
			var latitude = $('#inputLatitude').val();
			var longitude = $('#inputLongitude').val();
			var idImage = $('#inputIdImage').val();
			var prix = $('#inputPrix').val();
			var caution = $('#inputCaution').val();
			var lieu = $('#inputLieu').val();
			var etat = $('#inputEtat').val();
						
			// va cherche le contenu via tinyMCE
			var description = tinyMCE.activeEditor.getContent();
			
			// met à jour les tags
			var tags = $('#tags').val();
			var urlTag = chemin+"groupe/tag.html?type=objet&id="+idObjet+"&tag="+tags;
			$.get(urlTag,{'toto':''});

			// update l'évenement dans la base de donnée
			var url = chemin+"objet/"+idObjet+"-objet.html?update";

			// url , param, fonction de callback
			$.post(url,{'id':idObjet,'nom':nom,'latitude':latitude,'longitude':longitude,'description':description,'prix':prix,'caution':caution,'lieu':lieu,'etat':etat,'idImage':idImage},app.finUpdate);
		}
		
		/*
		 *  callback quand une mise à jour d'un objet est effectuée
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
			var idObjet = $('#idObjet').val();
			var tags = $('#tags').val();
			var url = chemin+"groupe/tag.html?type=objet&id="+idObjet+"&tag="+tags;
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

function startRichEditor(){
	tinyMCE.init({
		mode : "exact",
	//	mode : "textareas",
		elements : "description",
		theme : "advanced",
		plugins : "safari,directionality,ecoimage,style,table", //table,emotions,media,
		theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,styleselect,|,bullist,numlist,|,link,unlink,code,|,hr,image",  //tablecontrols,emotions,media,
		theme_advanced_buttons2 : "removeformat,tablecontrols,styleprops",
		theme_advanced_buttons3 : "",
		theme_advanced_buttons4 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_blockformats:"p,h1,h2,h3,h4,h5,h6,div",
		verify_html : true,
		inline_styles : false,
		relative_urls : false,  // par defaut à true
		convert_urls : false,
		apply_source_formatting: true, // indentation originale du code
//		browsers : "msie,gecko,opera",
		extended_valid_elements : "iframe[src|width|height|name|align]",
		content_css : 'http://martouf.ch/utile/css/bigbang.css',
		entity_encoding : "raw"
	});
}
