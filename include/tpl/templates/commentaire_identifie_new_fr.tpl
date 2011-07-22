<hr />
<input type="hidden" name="idAuteurCommentaire" id="idAuteurCommentaire" value="<!--{$idAuteurCommentaire}-->" />

<p class="center">
	<img src="http://<!--{$server_name}-->/utile/img/edit.gif" alt="edit" /><a id="masque_ajouter_commentaire" href="#">Ajouter un commentaire</a>
</p>	
	
	<div id="bloc_ajouter_commentaire">
				<input type="hidden" name="nomCommentaire" id="nomCommentaire" <!--{if $idAuteurCommentaire!=1}-->value="<!--{$pseudoUtilisateur}-->"<!--{/if}-->  />
				<input type="hidden" name="mailCommentaire" id="mailCommentaire" value="<!--{$auteurCommentaireEnCours.email}-->" />
				<input type="hidden" name="urlCommentaire" id="urlCommentaire" value="<!--{$auteurCommentaireEnCours.url}-->" />
				
			<p>
				<textarea rows="5" cols="130" name="descriptionCommentaire" id="descriptionCommentaire"></textarea>
			</p>
			
			<p class="center"><a href="#" id="ajouterCommentaire">envoyer le commentaire</a></p>
			
			<img src="http://<!--{$server_name}-->/utile/ajax/ticket.php" width="1" height="1" alt="transparent" />
	</div>

