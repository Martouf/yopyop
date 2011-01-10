/*******************************************************************************************
 * Nom du fichier		: photo_seule.js
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

		// crée une carte de base
		app.addGMapNeuchatel();
		
		//ajoute un écouteur
	//	GEvent.addListener(map, "click", function(overlay,point){app.ajouteClicMarker(overlay,point)});
	
		// si une latitude existe
		if (document.getElementById('photoLatitude')!=null) {
			// marque le point courant sur la carte.
			app.markCurrentLatLong();
		};
		
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

				map.setCenter(new GLatLng(46.995394778431226,6.9428551197052), 17,carteNeuch); // 17 tout près
				
				// ajoute les contrôles
				map.addControl(new GLargeMapControl());
				map.addControl(new GMapTypeControl());
			}else{
				alert("Sorry, the Google Maps API is not compatible with this browser");
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
			
			app.ajouteMarker(latitude,longitude);
		}
		
	} // Application
  });
})(jQuery);