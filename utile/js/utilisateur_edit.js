/*******************************************************************************************
 * Nom du fichier		: utilisateur_edit.js
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
		
		// ajoute l'autocompétion pour les mots-clés
		$("input#tags").autocomplete(chemin+"groupe/tags.html?list&type=personne", {
			multiple: true,
			minChars: 1
		});

		// update personne
		$('a#savePersonne').click(function() {
			app.savePersonne();
			return false;
		 });
		
		// create personne
		$('a#addPersonne').click(function() {
			app.createPersonne();
			return false;
		 });
		
		// tags
		$('a#enregistreTag').click(function() {
			app.saveTags();
			return false;
		 });
		
		// navigation
		$('#listeUtilisateurs').click(function() {
			app.selectUrlPersonne();
		 });

		$('#groupeUtilisateur').change(function() {
			app.selectUrlGroupe();
		 });
	
		// charge le datePicker
		Date.format = 'yyyy-mm-dd';
		$('.date-pick').datePicker();
		
	}); // ready
	
	
	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){
		
		
		///////////////  Création d'une personne ///////////

		/*
		 *  appelé au clic de la fonction de création. Cette fonction permet d'ajouter un personne
		 */
		this.createPersonne = function(e){

			var prenom = $('#inputPrenom').val();
			var nom = $('#inputNom').val();
			var surnom = $('#inputSurnom').val();
			var naissance = $('#inputNaissance').val();
			var motDePasse = $('#inputMotDePasse').val();
			var rang = $('#inputRang').val();
			var rue = $('#inputRue').val();
			var npa = $('#inputNpa').val();
			var lieu = $('#inputLieu').val();
			var tel = $('#inputTel').val();
			var email = $('#inputEmail').val();
			var remarque = $('#inputRemarque').val();

			// url de création d'un événement
			var url = chemin+"personne/personne.html?add";

			// url , param, fonction de callback
			$.post(url,{'nom':nom,'prenom':prenom,'surnom':surnom,'naissance':naissance,'mot_de_passe':motDePasse,'rang':rang,'rue':rue,'npa':npa,'lieu':lieu,'tel':tel,'email':email,'remarque':remarque},app.addTagsForNewPersonne);

		}

		/*
		 *  callback quand une création de personne est effectuée
		 *  Cette fonction va récupérer l'id de la nouvelle personne et lui attribuer les tags
		 */
		this.addTagsForNewPersonne = function(id){
			var idPersonne = id; // on récupère l'id fourni en postant la nouvelle personne
			var tags = $('#tags').val();
			var url = chemin+"groupe/tag.html?type=personne&id="+idPersonne+"&tag="+tags;
			$.get(url,{'toto':''},app.goToPersonne(id));
		}

		/*
		 *  redirige le visiteur sur la page du personne dont on passe l'id en paramètre
		 */
		this.goToPersonne = function(id){
			//echo ("redirection....");
			url = "/utilisateur/"+id+"-personne.html";
			//echo (url);
			window.location.href = url;
		}


		///////////// mise à jour d'une personne ///////////

		/*
		 *  appelé au click du lien de sauvegarde d'un personne. Cette fonction met à jour le personne.
		 */
		this.savePersonne = function(e){

			var idPersonne = $('#idPersonne').val();
			var prenom = $('#inputPrenom').val();
			var nom = $('#inputNom').val();
			var surnom = $('#inputSurnom').val();
			var naissance = $('#inputNaissance').val();
			var rue = $('#inputRue').val();
			var npa = $('#inputNpa').val();
			var lieu = $('#inputLieu').val();
			var tel = $('#inputTel').val();
			var email = $('#inputEmail').val();
			var remarque = $('#inputRemarque').val();
			
			// met à jour les tags
			var tags = $('#tags').val();
			var urlTag = chemin+"groupe/tag.html?type=personne&id="+idPersonne+"&tag="+tags;
			$.get(urlTag,{'toto':''});
			

			// update l'évenement dans la base de donnée
			var url = chemin+"personne/"+idPersonne+"-personne.html?update";

			// url , param, fonction de callback
			$.post(url,{'id':idPersonne,'nom':nom,'prenom':prenom,'surnom':surnom,'naissance':naissance,'rue':rue,'npa':npa,'lieu':lieu,'tel':tel,'email':email,'remarque':remarque},app.finUpdate);
		}
		
		/*
		 *  callback quand une mise à jour du personne est effectuée
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
			var idPersonne = $('#idPersonne').val();
			var tags = $('#tags').val();
			var url = chemin+"groupe/tag.html?type=personne&id="+idPersonne+"&tag="+tags;
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
		
		
		// navigation
		/*
		 *  redirige le visiteur sur la personne qu'il a choisi dans le select
		 */
		this.selectUrlPersonne = function(id){
		//	console.log("redirection....");
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