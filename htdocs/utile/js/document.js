/*******************************************************************************************
 * Nom du fichier		: document.js
 * Date					: 7 août 2008
 * Auteur				: Mathieu Despont
 * Adresse E-mail		: mathieu at marfaux.ch
 * But de ce fichier	: Application javascript de gestion de la ressource de type document
 *******************************************************************************************
 * 
 *
 */

var app;  // variable globale qui représente mon application


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
		
		//rend draggable tous les éléments qui ont la class "drag"
		app.addDraggable($('.drag'));
		
		// ajoute l'autocompétion pour les mots-clés
		$("input#tags").autocomplete(chemin+"groupe/tags.html?list&type=document", {   ///utile/ajax/liste_motcle.php?type=document
			multiple: true,
			minChars: 1
		});
		
		// tags
		$('a#enregistreTag').click(function() {
			app.saveTags();
			return false;
		 });
		
		// create document pour la page de création de document
		$('a#createDocument').click(function() {
			app.createDocument();
			return false;
		 });
		
		// gère l'affichage des commentaires
		$('#bloc_ajouter_commentaire').hide();
		
		$('a#masque_ajouter_commentaire').click(function() {
			$('#bloc_ajouter_commentaire').toggle();  //slideToggle est aussi possible
			return false;
		});
		
		// ajouter un commentaire
		$('a#ajouterCommentaire').click(function() {
			app.addComment();
			return false;
		 });
		
	}); // ready
	
	
	// objet qui contient toutes les fonctions utiles de mon application
	function Application(){
		
		///////////// mise à jour d'un document ///////////
		
		
		/*
		 *  appelé au click du liens de sauvegarde d'un document. Cette fonction met à jour le document.
		 */
		this.saveDocument = function(e){
			
			var idDocument = $('#idDocument').val();
			var nom = $('#nom').val();
			var description = $('#description').val();
			var infoModif = $('#infoModif').val();
			var datePublication = $('#datePublication').val();
			var evaluation = $('#evaluation').val();
			var access = $('#access').val();
			var groupe_autorise = $('#groupe_autorise').val();
			
			// va cherche le contenu via tinyMCE
			var contenu = tinyMCE.activeEditor.getContent();
			
			// met à jour les tags
			var tags = $('#tags').val();
			var url = chemin+"groupe/tag.html?type=document&id="+idDocument+"&tag="+tags;
			$.get(url,{'toto':''});
			
			
			// update le document dans la base de donnée
			var url = chemin+"document/"+idDocument+"-document.html?update";
			
			// url , param, fonction de callback
			$.post(url,{'nom':nom,'description':description,'contenu':contenu,'infoModif':infoModif,'date_publication':datePublication,'evaluation':evaluation,'access':access,'groupe_autorise':groupe_autorise},app.finUpdate);
		}
	
		/*
		 *  callback quand une mise à jour du document est effectuée
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
		
		/*
		 *  fonction d'enregistrement des tags du document
		 */
		this.saveTags = function(){
			var idDocument = $('#idDocument').val();
			var tags = $('#tags').val();
			var url = chemin+"groupe/tag.html?type=document&id="+idDocument+"&tag="+tags;
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
		
		
		///////////////  Création d'un document ///////////
		// Les étapes de la création d'un document sont les suivantes:
		// - récupération des valeurs de nom, description, info de modification et contenu du document
		// - création du document avec ces valeurs
		// - récupération de l'id du document qui vient d'être créé
		// - récupération des tags et enregistrement de ceux-ci pour le nouvel id fourni
		// - redirection de l'utilisateur sur la page de visualisation (ou de modification selon choix de conception) du nouveau document
		
		
		/*
		 *  appelé au clic de la fonction de sauvegarde. Cette fonction permet d'ajouter un document
		 */
		this.createDocument = function(e){
			
			var nom = $('#nom').val();
			var description = $('#description').val();
			var infoModif = $('#infoModif').val();
			var evaluation = $('#evaluation').val();
			var access = $('#access').val();
			var groupe_autorise = $('#groupe_autorise').val();
			
			// va cherche le contenu via tinyMCE
			var contenu = tinyMCE.activeEditor.getContent();
			
			// update le document dans la base de donnée
			var url = chemin+"document/document.html?add";
			
			// url , param, fonction de callback
			$.post(url,{'nom':nom,'description':description,'contenu':contenu,'infoModif':infoModif,'evaluation':evaluation,'access':access,'groupe_autorise':groupe_autorise},app.addTagsForNewDocument);
		}
		
		/*
		 *  callback quand une création document est effectuée
		 *  Cette fonction va récupérer l'id du nouveau document et lui attribuer les tags
		 */
		this.addTagsForNewDocument = function(id){
			//echo('Nouveau documentcréé avec succès');
			//echo (id);
			var idDocument = id; // on récupère l'id fourni en postant le nouveau document
			var tags = $('#tags').val();
			var url = chemin+"groupe/tag.html?type=document&id="+idDocument+"&tag="+tags;
			$.get(url,{'toto':''},app.goToDocument(id));
		}

		/*
		 *  redirige le visiteur sur la page du document dont on passe l'id en paramètre
		 */
		this.goToDocument = function(id){
		//	echo ("redirection....");
			url = chemin+"document/"+id+"-document.html";
		//	echo (url);
			window.location.href = url;
		}

		
		/*
		 *  appelé pour ajouter un commentaire
		 */
		this.addComment = function(e){
			
			// à quoi on ajoute un commentaire ?
			var idDocument = $('#idDocument').val();
			
			var nomCommentaire = $('#nomCommentaire').val();
			var mailCommentaire = $('#mailCommentaire').val();
			var urlCommentaire = $('#urlCommentaire').val();
			var descriptionCommentaire = $('#descriptionCommentaire').val();
			
			var idAuteurCommentaire = $('#idAuteurCommentaire').val();
			
			// ajoute le commentaire dans la base
			var url = chemin+"commentaire/commentaire.html?add";
			
			// url , param, fonction de callback
			$.post(url,{'nom':nomCommentaire,'description':descriptionCommentaire,'id_auteur':idAuteurCommentaire,'mail':mailCommentaire,'url':urlCommentaire,'id_element':idDocument,'table_element':'document'});
		
			commentaire = '<div class="commentaire"><div class="commentaire_texte"><p>'+descriptionCommentaire+'</p></div><div class="commentaire_infos">'+nomCommentaire+' <img src="//' + chemin + '/utile/img/bulle.gif" alt="bulle" /></div></div>';
			$('#bloc_commentaires').append(commentaire);
			
			$('#bloc_ajouter_commentaire').toggle(200);
			
		}
		
		/*
		 *  fonction pour rendre draggable un élément
		 */
		this.addDraggable = function(element){
			element.Draggable(
				{
					zIndex: 300,
					opacity: 1,
				//	grid: [101,16],   // x-y
				//	handle: '.poignee',
					//cursorAt: { top: 0, left: 0 },
				//	containment: 'calendrierMois',
				//	onStop: app.onDropEvenement,
				//	onDrag: app.onDragging
				}
			)
		}

		
	} // Application
	
	
  });
})(jQuery);

// va chercher l'info d'orientation
window.addEventListener("MozOrientation", onMozOrientation, true);

function onMozOrientation(event) {
    var x = event.x;
	var y = event.y;
	var fourmi1 = document.querySelector("#fourmi1");
	var fourmi2 = document.querySelector("#fourmi2");
	var fourmi3 = document.querySelector("#fourmi3");
	var fourmi4 = document.querySelector("#fourmi4");
	var fourmi5 = document.querySelector("#fourmi5");
	
	angle =  Math.floor(x * 90);
	if (x < -0.05 || x > 0.05){
		// var rotate = 'rotate(' + ( -event.y * 30) + 'deg)';
		// 		var scale = 'scale(' + (event.x + 1) + "," + (event.x + 1)  + ')';
		// 		fourmi.style.MozTransform = rotate + " " + scale;
		fourmi1.style.left = (angle*10)+'px';
		fourmi2.style.left = (angle*5)+'px';
		fourmi3.style.left = (angle*4)+'px';
		fourmi4.style.left = (angle*5)+'px';
		fourmi5.style.left = (angle*5)+'px';
	}
 }


function startRichEditor(){
	tinyMCE.init({
		mode : "exact",
	//	mode : "textareas",
		elements : "contenu",
		theme : "advanced",
		plugins : "safari,directionality,ecoimage,style,table,ecofichier,ecohtmlbrut", //table,emotions,media,
		theme_advanced_buttons1 : "bold,italic,underline,|,justifyleft,justifycenter,justifyright,justifyfull,formatselect,styleselect,|,bullist,numlist,|,link,unlink,code,|,hr,image,file,htmlbrut",  //tablecontrols,emotions,media,
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
		content_css : 'http://martouf.ch/utile/css/bigbang.css',
		entity_encoding : "raw"
	});
}

function email(user,domain,label,link) {
				var address = user+'@'+domain;
				var toWrite = '';
				if (link > 0) {toWrite += '<a href="mailto:'+address+'">';}
				if (label != '') {toWrite += label;} else {toWrite += address;}
				if (link > 0) {toWrite += '</a>';}
				document.write(toWrite);
}


function echo(str){
	try{
		console.log(str);
	}
	catch(e){alert(str)}
}