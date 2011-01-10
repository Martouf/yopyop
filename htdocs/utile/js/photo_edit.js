/*******************************************************************************************
 * Nom du fichier		: photo_edit.js
 * Date					: 5 juin 2008 pour la partie machinerie google
 * Modif				: 4 avril 2009 pour la gestion d'une photo
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu (at) marfaux.ch
 * But de ce fichier	: Fournir la partie javascript de manipulation de photos
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
		$("input#tags").autocomplete(chemin+"groupe/tags.html?list&type=photo", {
			multiple: true,
			minChars: 1
		});

		// crée une carte de base
		app.addGMapNeuchatel();
		
		//ajoute un écouteur
		GEvent.addListener(map, "click", function(overlay,point){app.ajouteClicMarker(overlay,point)});

		// update photo
		$('a#savePhoto').click(function() {
			app.savePhoto();
			return false;
		 });
		
		// tags
		$('a#enregistreTag').click(function() {
			app.saveTags();
			return false;
		 });
		
		// obtient l'altitude via geonames.org
		$('a#getAltitude').click(function() {
			app.getAltitude();
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
		
				// une couche holistic ortho
				var tileHolistic = new GTileLayer(new GCopyrightCollection("sitn"),15,19);
				tileHolistic.myLayers ='orthos_2006_0';
				tileHolistic.myFormat ='image/jpeg'; // image/png; mode=24bit
				tileHolistic.myBaseURL ='http://sitn.ne.ch/ogc-sitn-ecoparc/wms?';
		    	tileHolistic.getTileUrl = CustomGetTileUrl;
		
				var tileHolisticPlan = new GTileLayer(new GCopyrightCollection("sitn"),15,15);
				tileHolisticPlan.myLayers ='plan_ville_15000';
				tileHolisticPlan.myFormat ='image/jpeg';
				tileHolisticPlan.myBaseURL ='http://sitn.ne.ch/ogc-sitn-ecoparc/wms?';
		    	tileHolisticPlan.getTileUrl = CustomGetTileUrl;
		
				var tileHolisticPlan10000 = new GTileLayer(new GCopyrightCollection("sitn"),16,17);
				tileHolisticPlan10000.myLayers ='plan_ville_10000';
				tileHolisticPlan10000.myFormat ='image/jpeg';
				tileHolisticPlan10000.myBaseURL ='http://sitn.ne.ch/ogc-sitn-ecoparc/wms?';
		    	tileHolisticPlan10000.getTileUrl = CustomGetTileUrl;
		
				var tileOrtho= new GTileLayer(new GCopyrightCollection("sitn"),1,17);
				tileOrtho.myLayers='ortho'; // ombrage_mnt25,ombrage_laser_terrain,plan_ensemble,ortho,communes
				tileOrtho.myFormat='image/jpeg';
				tileOrtho.myBaseURL='http://sitn.ne.ch/ogc-sitn-plantes-invasives/wms?';
		    	tileOrtho.getTileUrl=CustomGetTileUrl;
		
				var layer1=[G_NORMAL_MAP.getTileLayers()[0]]; 
				var layer2=[tileOrtho]; 
				var layer3=[G_HYBRID_MAP.getTileLayers()[0]];
				var layer6=[tileHolistic];
				var layer7=[tileHolisticPlan];
				var layer8=[tileHolisticPlan10000];
				
				
				var cartePlanGoogle = new GMapType(layer1, G_SATELLITE_MAP.getProjection(), "Plan", G_SATELLITE_MAP);
				var carteNeuch = new GMapType(layer2, G_SATELLITE_MAP.getProjection(), "Photos Neuchâtel", G_SATELLITE_MAP);
		    	var carteOrthoGoogle = new GMapType(layer3, G_SATELLITE_MAP.getProjection(), "Photos Google", G_SATELLITE_MAP);
				var carteHolistic = new GMapType(layer6, G_SATELLITE_MAP.getProjection(), "Holistic", G_SATELLITE_MAP);
				var carteHolisticPlan = new GMapType(layer7, G_SATELLITE_MAP.getProjection(), "Plan Neuch", G_SATELLITE_MAP);
				var carteHolisticPlan10000 = new GMapType(layer8, G_SATELLITE_MAP.getProjection(), "Plan Neuch2", G_SATELLITE_MAP);
				
				
		
				map.getMapTypes().length = 0;
		    	map.addMapType(cartePlanGoogle);
				map.addMapType(carteNeuch);
		    	map.addMapType(carteOrthoGoogle);
				map.addMapType(carteHolistic);
				map.addMapType(carteHolisticPlan);
				map.addMapType(carteHolisticPlan10000);

				map.setCenter(new GLatLng(46.995394778431226,6.9428551197052), 14,carteNeuch); // 17 tout près
				
				// ajoute les contrôles
				map.addControl(new GLargeMapControl());
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
			
			var latitude = parseFloat($('#photoLatitude').val());
			var longitude = parseFloat($('#photoLongitude').val());
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
			
			var adresse = $('#inputAdresse').val();
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
		

		///////////// mise à jour d'une photo ///////////

		/*
		 *  appelé au click du lien de sauvegarde d'une photo. Cette fonction met à jour la photo.
		 */
		this.savePhoto = function(e){

			var idPhoto = $('#idPhoto').val();
			var nom = $('#inputNom').val();
			var latitude = $('#inputLatitude').val();
			var longitude = $('#inputLongitude').val();
			var altitude = $('#inputAltitude').val();
						
			// va cherche le contenu via tinyMCE
			var description = tinyMCE.activeEditor.getContent();
			
			// met à jour les tags
			var tags = $('#tags').val();
			var urlTag = chemin+"groupe/tag.html?type=photo&id="+idPhoto+"&tag="+tags;
			$.get(urlTag,{'toto':''});

			// update l'évenement dans la base de donnée
			var url = chemin+"photo/"+idPhoto+"-photo.html?update";

			// url , param, fonction de callback
			$.post(url,{'id':idPhoto,'nom':nom,'latitude':latitude,'longitude':longitude,'description':description},app.finUpdate);
		}
		
		/*
		 *  callback quand une mise à jour d'un photo est effectuée
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
			var idPhoto = $('#idPhoto').val();
			var tags = $('#tags').val();
			var url = chemin+"groupe/tag.html?type=photo&id="+idPhoto+"&tag="+tags;
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

		/*
		 *  Va chercher l'altitude via une fonction qui interroge le services geonames.org
		 */
		this.getAltitude = function(){
			//echo('va chercher l'altitude');
			var url = chemin+"utile/ajax/carto.php"; //?action=altitude&lat=46.94952985143932&lng=6.834204196929932
			
			var latitude = $('#inputLatitude').val();
			var longitude = $('#inputLongitude').val();
			$.get(url,{'action':'altitude','lat':latitude,'lng':longitude},app.updateAltitude);
		}
		
		/*
		 *  affiche l'altitude fournie
		 */
		this.updateAltitude = function(altitude){
			$('#inputAltitude').val(altitude);
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
		content_css : 'http://yopyop.ch/utile/css/bigbang.css',
		entity_encoding : "raw"
	});
}
