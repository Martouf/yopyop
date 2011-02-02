<div id="document">
	
	<div class="dateBlog">
		<p class="moisCreation"><!--{$document.moisCreation}--></p>
		<p class="jourCreation"><!--{$document.jourCreation}--></p>
	</div>
	<div class="blocNomBlog">
		<h1><!--{$document.nom}--></h1>
	</div>
	
	<div id="blocContenu">
		<!--{$document.contenu}-->
	</div>
	
	<div id="blocMetaDonnees">
		<p>
			<em><!--{$document.pseudoAuteur}-->: <!--{$document.dateModifHumaine}--></em>
		</p>
		<p class="relatedTags">
			tags: 
			<!--{foreach from=$document.tags key=tag item=occurence}-->
				<span class="tagLink"><a href="http://<!--{$server_name}-->/document/<!--{$tag}-->/?summary"><!--{$tag}--></a>,</span> 
			<!--{/foreach}-->
		</p>
		<div id="proposedDocuments">
			<h2>D'autres articles intéressants à lire...</h2>
			<!--{$documentsSimilaires}-->
		</div>
	</div>
	<input type="hidden" name="idDocument" value="<!--{$document.id_document}-->" id="idDocument" />
</div>

<div id="blocGestionCommentaires">
	<!--{include file="commentaire_multi_fr_html.tpl"}-->
	<!--{include file="commentaire_new_fr.tpl"}-->
</div>