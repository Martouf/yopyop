<div id="bloc_commentaires">	
	<!--{foreach from=$commentaires key=key item=aCommentaire}-->	
	
			<div class="commentaire">
				<div class="commentaire_texte">
					<p>
						<!--{$aCommentaire.description}-->
					</p>
				</div>
				<div class="commentaire_infos">
					<!--{if !empty($aCommentaire.url)&&($aCommentaire.url!="http://")}--><a href="<!--{$aCommentaire.url}-->"><!--{/if}--><img alt="gravatar" class="gravatar" src="http://www.gravatar.com/avatar/<!--{$aCommentaire.gravatar}-->.jpg?default=identicon" />  <!--{$aCommentaire.nom}--><!--{if !empty($aCommentaire.url)&&($aCommentaire.url!="http://")}--></a><!--{/if}--> le <!--{$aCommentaire.dateCreation}--><img src="http://<!--{$server_name}-->/utile/img/bulle.gif" alt="bulle" />
				</div>
			</div>
		
	<!--{/foreach}-->
</div>