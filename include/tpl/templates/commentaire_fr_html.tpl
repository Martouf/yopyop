<input type="hidden" name="idCommentaire" value="<!--{$commentaire.id_commentaire}-->" id="idCommentaire" />

<div id="bloc_commentaires">
	<div class="commentaire">
		<div class="commentaire_texte">
			<p>
				<!--{$commentaire.description}-->
			</p>
		</div>
		<div class="commentaire_infos">
			<!--{$commentaire.nom}--> le <!--{$commentaire.dateCreation}--><img src="http://<!--{$server_name}-->/utile/img/bulle.gif" alt="bulle" />
		</div>
	</div>
</div>