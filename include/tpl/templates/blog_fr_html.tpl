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
	</div>
	<input type="hidden" name="idDocument" value="<!--{$document.id_document}-->" id="idDocument" />
</div>

<div id="blocGestionCommentaires">
	<!--{include file="commentaire_multi_fr_html.tpl"}-->
	<!--{include file="commentaire_new_fr.tpl"}-->
</div>