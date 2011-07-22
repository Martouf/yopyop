<div id="bloc_commentaires">	
	<!--{foreach from=$commentaires key=key item=aCommentaire}-->	
	
			<div class="commentaire">
				<div class="commentaire_texte">
					<p>
						<!--{$aCommentaire.description}-->
					</p>
				</div>
				<div class="commentaire_infos">
					<a href="http://<!--{$server_name}-->/profile/<!--{$auteurCommentaireEnCours.id_personne}-->-<!--{$auteurCommentaireEnCours.surnom}-->.html"><img alt="gravatar" class="gravatar" src="http://www.gravatar.com/avatar/<!--{$aCommentaire.gravatar}-->.jpg?default=identicon" />  <!--{$aCommentaire.nom}--></a> <span class="dateCommentaire">le <!--{$aCommentaire.dateCreation}--></span><img src="http://<!--{$server_name}-->/utile/img/bulle.gif" alt="bulle" />
				</div>
			</div>
		
	<!--{/foreach}-->
</div>