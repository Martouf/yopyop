/*******************************************************************************************
 * Nom du fichier		: geophoto.js
 * Date					: 5 juin 2008 pour la partie machinerie google
 * Modif				: 4 avril 2009 pour la gestion d'une photo
 * 						: 1 mai pour adapter une galerie multicarte.
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu (at) marfaux.ch
 * But de ce fichier	: Fournir la partie javascript de manipulation de photos
 *******************************************************************************************
 * 
 *
 */

var app;  // variable globale qui représente mon application
var chemin="/";

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
		
		$(".infoIdPhoto").each(function (i) {
			app.markerSurNouvelleCarte(this.value);
		});
		
	}); // ready
	
	
	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){
		
		
		/*
		 * Crée une carte et affiche un marker sur celle-ci.
		 * L'id permettant de sélectionner la carte et les données est fournie en paramètre.
		 */
		this.markerSurNouvelleCarte = function(id){
			
			if (GBrowserIsCompatible()) {
				map = new GMap2(document.getElementById("map"+id));
				
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

				map.setCenter(new GLatLng(46.995394778431226,6.9428551197052), 16,carteOrthoGoogle); // 17 tout près
				
				// ajoute les contrôles
				map.addControl(new GSmallMapControl());
				map.addControl(new GMapTypeControl());
			}else{
				alert("Sorry, the Google Maps API is not compatible with this browser");
			}
			
			
			var latitude = parseFloat($('#photoLatitude'+id).val());
			var longitude = parseFloat($('#photoLongitude'+id).val());
			//console.log(longitude+','+latitude);
			
			var point = new GLatLng(latitude,longitude);
			map.addOverlay(new GMarker(point));
			map.panTo(point);
		}
		
		
	} // Application
  });
})(jQuery);