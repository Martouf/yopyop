<div id="bloc_commentaires">	
	<!--{foreach from=$commentaires key=key item=aCommentaire}-->	
	
			<div class="commentaire">
				<div class="commentaire_texte">
					<p>
						<!--{$aCommentaire.description}-->
					</p>
				</div>
				<div class="commentaire_infos">
					<!--{if !empty($aCommentaire.url)}--><a href="<!--{$aCommentaire.url}-->"><!--{/if}--><img alt="gravatar" class="gravatar" src="http://www.gravatar.com/avatar/<!--{$aCommentaire.gravatar}-->.jpg?default=identicon" />  <!--{$aCommentaire.nom}--> le <!--{$aCommentaire.dateCreation}--><img src="/utile/img/bulle.gif" alt="bulle" />
				</div>
			</div>
		
	<!--{/foreach}-->
</div>