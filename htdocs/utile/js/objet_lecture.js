/*******************************************************************************************
 * Nom du fichier		: objet_lecture.js
 * Date					: 22 juillet 2011
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu at ecodev.ch
 * But de ce fichier	: 
 *******************************************************************************************
 * 
 */

var app2;  // variable globale qui représente mon application


jQuery.noConflict();
(function($) { 
  $(function() {

	// morceau de script qui est évalué à chaque requête. Il permet de lier du code javascript avec les éléments html
	$(document).ready(function(){
		app2 = new Application();

		// montre ou cache un loading en bas à droite de la page
		$("#loading").ajaxStart(function(){
		  $(this).show('fast');
		});
		$("#loading").ajaxStop(function(){
		  $(this).slideUp('slow');
		});
		
		// gère l'affichage des commentaires
		$('#bloc_ajouter_commentaire').hide();
		
		$('a#masque_ajouter_commentaire').click(function() {
			$('#bloc_ajouter_commentaire').toggle();  //slideToggle est aussi possible
			return false;
		});
		
		// ajouter un commentaire
		$('a#ajouterCommentaire').click(function() {
			app2.addComment();
			return false;
		 });

	}); // ready


	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){


		/*
		 *  appelé pour ajouter un commentaire
		 */
		this.addComment = function(e){
			
			// à quoi on ajoute un commentaire ?
			var idObjet = $('#idObjet').val();
			
			var nomCommentaire = $('#nomCommentaire').val();
			var mailCommentaire = $('#mailCommentaire').val();
			var urlCommentaire = $('#urlCommentaire').val();
			var descriptionCommentaire = $('#descriptionCommentaire').val();
			
			var idAuteurCommentaire = $('#idAuteurCommentaire').val();
			
			// ajoute le commentaire dans la base
			var url = chemin+"commentaire/commentaire.html?add";
			
			// url , param, fonction de callback
			$.post(url,{'nom':nomCommentaire,'description':descriptionCommentaire,'id_auteur':idAuteurCommentaire,'mail':mailCommentaire,'url':urlCommentaire,'id_element':idObjet,'table_element':'objet'},app2.refreshPage);
		
			
			// Permet d'afficher le commentaire directement sans recharger la page... Effet, sympa.. mais pas tout le contenu est disponible et ne permet pas de savoir si le commentaire a vraiment été pris... Donc on revient au refresh
			// commentaire = '<div class="commentaire"><div class="commentaire_texte"><p>'+descriptionCommentaire+'</p></div><div class="commentaire_infos">'+nomCommentaire+' <img src="//' + chemin + '/utile/img/bulle.gif" alt="bulle" /></div></div>';
			// 			$('#bloc_commentaires').append(commentaire);
			// 			
			// 			$('#bloc_ajouter_commentaire').toggle(200);
			
		}

		/*
		 *  refresh de la page
		 */
		this.refreshPage = function(){
			url = window.location.href;
			window.location.href = url;
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