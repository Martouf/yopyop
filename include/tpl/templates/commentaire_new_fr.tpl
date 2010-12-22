<hr />
<input type="hidden" name="idAuteurCommentaire" id="idAuteurCommentaire" value="<!--{$idAuteurCommentaire}-->" />

<p class="center">
	<img src="/utile/img/edit.gif" alt="edit" /><a id="masque_ajouter_commentaire" href="#">Ajouter un commentaire</a>
</p>	
	
	<div id="bloc_ajouter_commentaire">
			<p>
				<label for="nomCommentaire">Nom</label>
				<input type="text" name="nomCommentaire" id="nomCommentaire" <!--{if $idAuteurCommentaire!=1}-->value="<!--{$pseudoUtilisateur}-->"<!--{/if}-->  />
			</p>
			<p>
				<label for="mailCommentaire" title="n'est pas publié">e-mail</label>
				<input type="text" name="mailCommentaire" id="mailCommentaire" />
			</p>
			<p>
				<label for="urlCommentaire">Site web</label>
				<input type="text" name="urlCommentaire" id="urlCommentaire" value="http://" />
			</p>
			<p>
				<label for="descriptionCommentaire">Commentaire</label>
			</p>
			<p>
				<textarea rows="5" cols="130" name="descriptionCommentaire" id="descriptionCommentaire"></textarea>
			</p>
			
			<p class="center"><a href="#" id="ajouterCommentaire">envoyer le commentaire</a></p>
			
			<img src="/utile/ajax/ticket.php" width="1" height="1" alt="transparent" />
	</div>

