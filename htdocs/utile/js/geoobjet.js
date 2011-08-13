/*******************************************************************************************
 * Nom du fichier		: geoobjet.js
 * Date					: 5 juin 2008 pour la partie machinerie google
 * Modif				: 4 avril 2009 pour la gestion d'un objet
 * 						: 1 mai pour adapter une galerie multicarte.
 *						: 11.12.9 pour remplacer les multicartes par une grande
 * 						: 13.08.11 pour faire un geoobjet basé sur geophoto
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu (at) marfaux.ch
 * But de ce fichier	: Fournir la partie javascript de manipulation de objets
 *******************************************************************************************
 * 
 *
 */

var app;  // variable globale qui représente mon application
//var chemin="/";

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
		
		// crée une carte de base
		app.addGMapNeuchatel();
		
		// url des données json
		url = chemin+"geoobjet/galerie.json";
				
		// va chercher les données
		app.fetchJsonSource(url);
		
	}); // ready
	
	
	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){
		
		/*
		 *  va chercher les données JSON
		 */
		this.fetchJsonSource = function(url){
		//	console.log("va chercher: "+url);
			GDownloadUrl(url, app.addJsonMarker);
		}
		
		/*
		 *  Demande la création de marker en fonction d'une liste json
		 */
		this.addJsonMarker = function(jsonSource){
			//console.log("parse json");
			// parse les données json pour en faire un vrai tableau
			var jsonData = eval('(' + jsonSource + ')');
			
		//	console.log("données json parsée");
			
			// crée un marker infobulle pour chaque événement
	        for (var i=0; i<jsonData.objets.length; i++) {
				var lat = parseFloat(jsonData.objets[i].latLieu);
				var lng = parseFloat(jsonData.objets[i].longLieu);
				if (lat!=0 && lng!=0) {
					var point = new GLatLng(lat,lng);
					//var idEvent = jsonData.objets[i].id;
					var description = jsonData.objets[i].description;
					var lienVignette = jsonData.objets[i].lienVignette;
					var contenuImage = '<img src="'+lienVignette+'" alt="girafe"/>';
				//	var html = contenuImage+description; 
					var html = contenuImage;
					// var nom = '<h2>'+jsonData.evenements[i].nom+'</h2>';
					// 					var dateDebut = jsonData.evenements[i].dateDebut+', '+jsonData.evenements[i].heureDebut;
					// 					var dateFin = jsonData.evenements[i].dateFin+', '+jsonData.evenements[i].heureFin;
					// 					var contact = jsonData.evenements[i].infoPrenom+' '+jsonData.evenements[i].infoNom+' '+jsonData.evenements[i].infoTel;
					// 					var nomLieu = jsonData.evenements[i].nomLieu;
					// 					var html = nom+'du '+dateDebut+'<br />au '+dateFin+'<br /><br />lieu: '+nomLieu+'<br />contact: '+contact+'<br />';
					var marker = app.createInfoMarker(point,html);
					map.addOverlay(marker);
				};
			}
		}
		
		/*
		 *  crée un marker avec une info bulle
		 */
		this.createInfoMarker = function(point,html){
			var marker = new GMarker(point);
			GEvent.addListener(marker, "click", function() {
				marker.openInfoWindowHtml(html);
			});
			return marker;
		}
		
		
		/*
		 *  Fonction qui ajoute une carte avec des données neuchâteloises
		 */
		this.addGMapNeuchatel = function(option){
			if (GBrowserIsCompatible()) {
				map = new GMap2(document.getElementById("map"));

				// Create tile layers
		
				// une couche holistic ortho
				// var tileHolistic = new GTileLayer(new GCopyrightCollection("sitn"),15,19);
				// tileHolistic.myLayers ='orthos_2006_0';
				// tileHolistic.myFormat ='image/jpeg'; // image/png; mode=24bit
				// tileHolistic.myBaseURL ='http://sitn.ne.ch/ogc-sitn-ecoparc/wms?';
				// 		    	tileHolistic.getTileUrl = CustomGetTileUrl;
				// 		
				// var tileHolisticPlan = new GTileLayer(new GCopyrightCollection("sitn"),15,15);
				// tileHolisticPlan.myLayers ='plan_ville_15000';
				// tileHolisticPlan.myFormat ='image/jpeg';
				// tileHolisticPlan.myBaseURL ='http://sitn.ne.ch/ogc-sitn-ecoparc/wms?';
				// 		    	tileHolisticPlan.getTileUrl = CustomGetTileUrl;
				// 		
				// var tileHolisticPlan10000 = new GTileLayer(new GCopyrightCollection("sitn"),16,17);
				// tileHolisticPlan10000.myLayers ='plan_ville_10000';
				// tileHolisticPlan10000.myFormat ='image/jpeg';
				// tileHolisticPlan10000.myBaseURL ='http://sitn.ne.ch/ogc-sitn-ecoparc/wms?';
				// 		    	tileHolisticPlan10000.getTileUrl = CustomGetTileUrl;
		
				var tileOrtho= new GTileLayer(new GCopyrightCollection("sitn"),1,17);
				tileOrtho.myLayers='ortho'; // ombrage_mnt25,ombrage_laser_terrain,plan_ensemble,ortho,communes
				tileOrtho.myFormat='image/jpeg';
				tileOrtho.myBaseURL='http://sitn.ne.ch/ogc-sitn-open/wms?';
			//	tileOrtho.myBaseURL='http://sitn.ne.ch/ogc-sitn-plantes-invasives/wms?';
		    	tileOrtho.getTileUrl=CustomGetTileUrl;
		
				var layer1=[G_NORMAL_MAP.getTileLayers()[0]]; 
				var layer2=[tileOrtho]; 
				var layer3=[G_HYBRID_MAP.getTileLayers()[0]];
				// var layer6=[tileHolistic];
				// var layer7=[tileHolisticPlan];
				// var layer8=[tileHolisticPlan10000];
				
				
				var cartePlanGoogle = new GMapType(layer1, G_SATELLITE_MAP.getProjection(), "Plan", G_SATELLITE_MAP);
				var carteNeuch = new GMapType(layer2, G_SATELLITE_MAP.getProjection(), "Photos Neuchâtel", G_SATELLITE_MAP);
		    	var carteOrthoGoogle = new GMapType(layer3, G_SATELLITE_MAP.getProjection(), "Photos Google", G_SATELLITE_MAP);
				// var carteHolistic = new GMapType(layer6, G_SATELLITE_MAP.getProjection(), "Holistic", G_SATELLITE_MAP);
				// 			var carteHolisticPlan = new GMapType(layer7, G_SATELLITE_MAP.getProjection(), "Plan Neuch", G_SATELLITE_MAP);
				// 			var carteHolisticPlan10000 = new GMapType(layer8, G_SATELLITE_MAP.getProjection(), "Plan Neuch2", G_SATELLITE_MAP);
				
				
		
				map.getMapTypes().length = 0;
		    	map.addMapType(cartePlanGoogle);
				map.addMapType(carteNeuch);
		    	map.addMapType(carteOrthoGoogle);
				// map.addMapType(carteHolistic);
				// map.addMapType(carteHolisticPlan);
				// map.addMapType(carteHolisticPlan10000);

				map.setCenter(new GLatLng(47.00601167615606,6.77032470703125), 10,carteOrthoGoogle);
				
				// ajoute les contrôles
				map.addControl(new GLargeMapControl());
				map.addControl(new GMapTypeControl());
			}else{
				alert("Sorry, the Google Maps API is not compatible with this browser");
			}
		}
		
		
		
		
		
		
		
		
		
		
		
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

				map.setCenter(new GLatLng(46.995394778431226,6.9428551197052), 10,carteOrthoGoogle); // 17 tout près
				
				// ajoute les contrôles
				map.addControl(new GSmallMapControl());
				map.addControl(new GMapTypeControl());
			}else{
				alert("Sorry, the Google Maps API is not compatible with this browser");
			}
			
			
			var latitude = parseFloat($('#objetLatitude'+id).val());
			var longitude = parseFloat($('#objetLongitude'+id).val());
			//console.log(longitude+','+latitude);
			
			var point = new GLatLng(latitude,longitude);
			map.addOverlay(new GMarker(point));
			map.panTo(point);
		}
		
		
	} // Application
  });
})(jQuery);