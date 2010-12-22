<div id="document" <!--{if $utilisateurConnu}--> ondblclick="document.location='<!--{$document.id_document}-->-<!--{$document.nomSimplifie}-->.html?modify';"<!--{/if}-->>
	
	<div class="blocContenu">
		<!--{$document.contenu}-->
	</div>

	
	<!--{if $metadonneeAutorise}-->

	<div class="blocMetaDonnees">
		<p>
			<em><!--{$document.pseudoCreateur}-->: <!--{$document.dateCreation}--></em>
			<!--{if $document.dateCreation != $document.dateModification}-->
			<br />
			<em><!--{$document.pseudoAuteur}-->: <!--{$document.dateModification}--></em>
			<!--{/if}-->
		</p>
	</div>
	<!--{/if}-->
	
	<input type="hidden" name="idDocument" value="<!--{$document.id_document}-->" id="idDocument" />
</div>

<!--{if $commentaireAutorise}-->

<div id="blocGestionCommentaires">
	<!--{include file="commentaire_multi_fr_html.tpl"}-->
	<!--{include file="commentaire_new_fr.tpl"}-->
</div>

<!--{/if}-->
