/*******************************************************************************************
 * Nom du fichier		: utilisateur.js
 * Date					: 12 avril 2009
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu (at) ecodev.ch
 * But de ce fichier	: Fournir la partie javascript de manipulation de personnes
 *******************************************************************************************
 * 
 *
 */

var app;  // variable globale qui représente mon application
var chemin="/";

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
		
		// navigation
		$('#listeUtilisateurs').click(function() {
			app.selectUrlPersonne();
		 });
		
		$('#groupeUtilisateur').change(function() {
			app.selectUrlGroupe();
		 });
		
	}); // ready
	
	
	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){
			
		// navigation
		/*
		 *  redirige le visiteur sur la personne qu'il a choisi dans le select
		 */
		this.selectUrlPersonne = function(id){
			//console.log("redirection bleu....");
			var idPersonne = $('#listeUtilisateurs').val();
			var groupe = $('#groupeUtilisateur').val();
			
			if (groupe!='') {
				url = chemin+"utilisateur/"+groupe+"/"+idPersonne+"-personne.html";
			}else{
				url = chemin+"utilisateur/"+idPersonne+"-personne.html";
			}
			
			//console.log(url);
			window.location.href = url;
		}
		/*
		 *  redirige le visiteur sur la personne qu'il a choisi dans le select
		 */
		this.selectUrlGroupe = function(id){
		//	 console.log("redirection....");
			var groupe = $('#groupeUtilisateur').val();
			
			if (groupe!='') {
				url = chemin+"utilisateur/"+groupe+"/";
			}else{
				url = chemin+"utilisateur/";
			}
			
			//echo (url);
			window.location.href = url;
		}
		
		
	} // Application
  });
})(jQuery);