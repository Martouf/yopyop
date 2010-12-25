jQuery.noConflict();
(function($) { 
  $(function() {

	// morceau de script qui est évalué à chaque requête. Il permet de lier du code javascript avec les éléments html
	$(document).ready(function(){
		
		// formulaire identifiacation
		// masque le formulaire le plus vite possible
		 $('#form_identification').hide();

		// toggles le div des infos suivant sa position
		 $('a#toggleCoccinelle').click(function() {
			$('#form_identification').toggle(400);  //slideToggle est aussi possible
			return false;
		 });

	}); // ready

  });
})(jQuery);