<div id="listeDocument">	
	<!--{foreach from=$documents key=key item=aDocument}-->	
	
			<div id="document_<!--{$aDocument.id_document}-->" >				

				<div class="dateBlog">
					<p class="moisCreation"><!--{$aDocument.moisCreation}--></p>
					<p class="jourCreation"><!--{$aDocument.jourCreation}--></p>
				</div>
				<div class="blocNomBlog">
					<h1><!--{$aDocument.nom}--></h1>
				</div>
				<div class="blocContenuBlog">
					<!--{$aDocument.contenu}-->
				</div>

				<div class="blocMetaDonneesBlog">
					<em><!--{$aDocument.pseudoAuteur}-->: <!--{$aDocument.dateModifHumaine}--></em>
				</div>
				<p class="relatedTags">
					tags: 
					<!--{foreach from=$aDocument.tags key=tag item=occurence}-->
						<span class="tagLink"><a href="http://<!--{$server_name}-->/document/<!--{$tag}-->/?summary"><!--{$tag}--></a>,</span> 
					<!--{/foreach}-->
				</p>
				<p><a href="//<!--{$server_name}-->/blog/<!--{$aDocument.id_document}-->-<!--{$aDocument.nomSimplifie}-->.html" title="voir ou ajouter un commentaire"><!--{$aDocument.nbCommentaire}--> commentaires</a></p>
			</div>
	<!--{/foreach}-->
	
	<hr />
	
	<div id="pagination">
		pages suivantes... 
		<!--{foreach from=$pagination key=noPage item=lienPage}-->
			<a href="<!--{$lienPage}-->" <!--{if $noPage==$pageCourante}-->style="font-weight:bold;"<!--{/if}--> > <!--{$noPage}--></a>... 
		<!--{/foreach}-->
	</div>
</div>

