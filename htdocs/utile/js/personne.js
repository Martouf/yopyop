/*******************************************************************************************
 * Nom du fichier		: personne.js
 * Date					: 7 août 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu@marfaux.ch
 * But de ce fichier	: Fournir la partie javascript de la page de gestion des groupes
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

	//	$('#boutonAjouterGroupe').click(app.addGroupe);
		app.getListeGroupe();
		
	}); // ready
	
	
	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){
		
		/*
		 *  Va chercher la liste des groupes (seulement ceux de type 1 donc les mots-clés)
		 */
		this.getListeGroupe = function(html){
			var url = chemin+"utile/ajax/liste_groupe_ajax.php";
			
			// url , param, fonction de callback
			$.get(url,{'toto':'toto'},app.afficheListeGroupe);
		}
		
		/*
		 *  Affiche, dans la colonne, le contenu html qui provient de la requête ajax.
		 */
		this.afficheListeGroupe = function(html){
			$('#listeNomGroupe').empty().append(html);
		}
		
		
		/*
		 *  Va chercher la liste des éléments qui sont dans le groupe
		 */
		this.getContenuGroupe = function(id){
			var url = chemin+"utile/ajax/liste_contenu_groupe_ajax.php";
		
			// url , param, fonction de callback
			$.get(url,{'id_groupe':id},app.afficheContenuGroupe);
		}
		/*
		 *  Affiche, dans la 2ème colonne, le contenu html qui provient de la requête ajax.
		 */
		this.afficheContenuGroupe = function(html){
			$('#colonneContenuGroupe').empty().append(html);
		}
			
		/*
		 *  Va chercher le détail des infos pour un élément
		 */
		this.getContenuElement = function(id,type){
			var url = chemin+"utile/ajax/affiche_contenu_element_ajax.php";
			
			// url , param, fonction de callback
			$.get(url,{'id':id,'type':type},app.afficheContenuElement);
		}
		/*
		 *  Affiche, dans la 2ème colonne, le contenu html qui provient de la requête ajax.
		 */
		this.afficheContenuElement = function(html){
			$('#colonneElement').empty().append(html);
			
			// ajoute l'autocompétion pour les mots-clés
			$("input#motscles").autocomplete(chemin+'utile/ajax/liste_motcle.php', {
				multiple: true,
				minChars: 1
			});
			
			$('#boutonSauverMotCle').click(app.tagElement);
		}
		
		/*
		 *  Permet de cliquer sur un mot pour l'ajouter dans le champ texte
		 */
		this.proposeMotCle = function(motCle){
			var chaine = $("input#motscles").val() + motCle + ',';
			$("input#motscles").val(chaine);
		}
		
		
		/*
		 *  Fonction appellée au clic du bouton de sauvegarde des tags
		 */
		this.tagElement = function(){
			//met à jour les tags
			var idElement = $('input#idElement').val();
			var tags = $('input#motscles').val();
			var tableElement = $('input#tableElement').val();
			var urlTag = chemin+"utile/ajax/tag.php";
			
			// url , param, fonction de callback (optionnelle)
			$.get(urlTag,{'type':tableElement,'id':idElement,'tag':tags},app.finTag);
		}
		
		
		/*
		 *  Permet de lancer une action à la fin du tagging
		 */
		this.finTag = function(html){
			app.getListeGroupe(); // rafraichi la liste des groupes au cas où il y aurait un nouveau
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