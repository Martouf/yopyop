/*******************************************************************************************
 * Nom du fichier		: rando.js
 * Date					: 16 mai 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu @ marfaux.ch
 * But de ce fichier	: La logique javascript d'attribution de gestion de la carte
 *******************************************************************************************
 *  en allemand: Formular fü Marschzeitberechnung und Streckenprofil
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

		// $("#loading").ajaxStart(function(){
		//   $(this).show('fast');
		// });
		// $("#loading").ajaxStop(function(){
		//   $(this).slideUp('slow');
		// });
		
		// crée une carte de base
		app.addGMapNeuchatel();
		
		//ajout un écouteur
		GEvent.addListener(map, "click", function(overlay,point){app.ajouteMarker(overlay,point)});
		
		// obtient les coordonnées depuis l'adresse via google
		$('a#getAdresse').click(function() {
			app.setMarkerFromAddress();
			return false;
		 });

				
	}); // ready
	
	
	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){
		
		
		/*
		 *  Fonction qui initialise une carte
		 */
		this.addGMap = function(option){
			if (GBrowserIsCompatible()) {
		        map = new GMap2(document.getElementById("map"));
		        map.addControl(new GLargeMapControl());
		        map.addControl(new GMapTypeControl());
		        map.setCenter(new GLatLng(46.99559235540579,6.931241154670715), 15);
				map.setMapType(G_SATELLITE_TYPE);
		    }
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
		
				var tileOrtho= new GTileLayer(new GCopyrightCollection("sitn"),1,19);
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

				map.setCenter(new GLatLng(46.995394778431226,6.9428551197052), 16,carteNeuch);
				
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
		this.ajouteMarker = function(overlay, point){
		//	map.clearOverlays();
			if (point) {
				map.addOverlay(new GMarker(point));
				map.panTo(point);
				latitude = point.lat();
				longitude = point.lng();
				// echo(latitude);
				// echo(longitude);
				
				// avant d'ajouter la nouvelle valeur du point sauve l'ancienne
				var lat = $('#latitude').val();
				var lng = $('#longitude').val();
				$('#latitude2').val(lat);
				$('#longitude2').val(lng);
				
				// met à jour la coordonnée
				$('#latitude').val(latitude);
				$('#longitude').val(longitude);
				// délègue la recherche et l'affichage de l'atlitude
				app.getSwissCoord(point);
				app.getAltitude(point);
				app.logParcours(point);
			}
		}
		
		
		/*
		 *  Fonction qui permet d'obtenir l'altitude via un script local qui va piocher chez geoname.org
		 */
		this.getAltitude = function(point){
			//carto.php?action=altitude&lat=46.94952985143932&lng=6.834204196929932
			var url = chemin+"utile/ajax/carto.php";
			
			// appelle un script local
			// url , param, fonction de callback
			$.get(url,{'action':'altitude','lat':point.lat(),'lng':point.lng()},app.rafraichitAltitude);
		}
		
		/*
		 *  Fonction qui affiche l'altitude
		 */
		this.rafraichitAltitude = function(html){
			$('#altitude').val(html);
		}
		
	
		/*
		 *  Fonction qui permet d'obtenir l'altitude via un script local qui va piocher chez geoname.org
		 */
		this.getSwissCoord = function(point){
			//carto.php?action=altitude&lat=46.94952985143932&lng=6.834204196929932
			var url = chemin+"utile/ajax/carto.php";
			
			// appelle un script local
			// url , param, fonction de callback
			$.get(url,{'action':'wgs84toch1903','lat':point.lat(),'lng':point.lng()},app.rafraichitSwissCoord);
		}
		
		/*
		 *  Fonction qui affiche les coordonnée suisse
		 */
		this.rafraichitSwissCoord = function(html){
			$('#swissCoord').val(html);
		}
		
		/*
		 *  Fonction qui permet d'obtenir l'altitude via un script local qui va piocher chez geoname.org
		 */
		this.logParcours = function(point){
			//carto.php?action=altitude&lat=46.94952985143932&lng=6.834204196929932
			var url = chemin+"utile/ajax/carto.php";
			// point précédent
			var oldLat = $('#latitude2').val();
			var oldLng = $('#longitude2').val();
			
			// url , param, fonction de callback
			$.get(url,{'action':'wgs84toch1903','lat':point.lat(),'lng':point.lng()},app.rafraichitLogCoord);
			$.get(url,{'action':'altitude','lat':point.lat(),'lng':point.lng()},app.rafraichitLogAltitude);
			
			// va chercher la distance entre le point courant et le précédent. sauf si ce dernier = 0  (donc pas de point précédent)
			if (oldLat!=0) {
				$.get(url,{'action':'distance','lat':point.lat(),'lng':point.lng(),'lat2':oldLat,'lng2':oldLng},app.rafraichitLogDistance);
			};
			
			app.ajouteContenuLog('<br />');
			
		}
		
		/*
		 *  Fonction fait le log
		 */
		this.rafraichitLogAltitude = function(html){
			$('#parcours').append('&nbsp;&nbsp;&nbsp;<span class="altitude">'+html+'</span>');
		}
		this.rafraichitLogCoord = function(html){
			$('#parcours').append('&nbsp;&nbsp;&nbsp;<span class="coord">'+html+'</span>');
		}
		this.rafraichitLogDistance = function(html){
			$('#parcours').append('&nbsp;&nbsp;&nbsp;<span class="distance">'+html+'</span>');
		}
		
		/*
		 *  Fonction fait permettant d'ajouter du contenu au log.. des <br /> par exemple..
		 */
		this.ajouteContenuLog = function(html){
			$('#parcours').append(html);
		}
		
		/*
		 *  reset. efface les points et les logs  ...  ne semble pas fonctionner.. pourquoi ??
		 */
		this.reset = function(){
			$('#parcours').empty();
			map.clearOverlays();
		}
		
		
		///////////////  geocoding ///////
		/*
		 *  va chercher l'adresse dans le formulaire et demande à google via un géocodeur de créer une marker
		 */
		
		this.setMarkerFromAddress = function(){
			// efface la carte la carte
		//	map.clearOverlays();

			// on crée un Client Geocoder
			var geo = new GClientGeocoder();
			
			var adresse = $('#inputAdresse').val();
			
			geo.getLatLng(adresse, app.setMarker);
		}
		
		
	/*
	 *  ajoute un marker
	 */
		this.setMarker = function(point){
		//	map.clearOverlays();
			if (point) {
				map.addOverlay(new GMarker(point));
				map.panTo(point);
				latitude = point.lat();
				longitude = point.lng();
				// echo(latitude);
				// echo(longitude);
				
				// avant d'ajouter la nouvelle valeur du point sauve l'ancienne
				var lat = $('#latitude').val();
				var lng = $('#longitude').val();
				$('#latitude2').val(lat);
				$('#longitude2').val(lng);
				
				// met à jour la coordonnée
				$('#latitude').val(latitude);
				$('#longitude').val(longitude);
				// délègue la recherche et l'affichage de l'atlitude
				app.getSwissCoord(point);
				app.getAltitude(point);
				app.logParcours(point);
			}
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