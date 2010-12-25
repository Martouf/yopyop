/*******************************************************************************************
 * Nom du fichier		: photo.js
 * Date					: 16 septembre 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Fournir la partie javascript pour l'affichage d'une photo
 *******************************************************************************************
 * Gère l'affichage et le rafraichissement par ajax des différentes colonnes.
 *
 */

var app;  // variable globale qui représente mon application


jQuery.noConflict();
(function($) { 
  $(function() {
	
	// morceau de script qui est évalué à chaque requête. Il permet de lier du code javascript avec les éléments html
	$(document).ready(function(){
		app = new Application();
	}); // ready
	
	
	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){
		
		
	} // Application
	
	
  });
})(jQuery);

function metAJourTaille() {
	var largeur = document.getElementById('largeur').value;
	var hauteur = document.getElementById('hauteur').value;

	var taille =  document.getElementById('taille');
	taille.style.width= largeur+'px';
	taille.style.height= hauteur+'px';
}

function echo(str){
	try{
		console.log(str);
	}
	catch(e){alert(str)}
}