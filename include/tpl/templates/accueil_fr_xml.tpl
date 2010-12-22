<div id="document" ondblclick="document.location='<!--{$document.id_document}-->-<!--{$document.nomSimplifie}-->.html?modify';">
	
	
	<div id="blocContenu">
		<!--{$document.contenu}-->
	</div>
	
	<div id="blocMetaDonnees">
		<p>
			<em><!--{$document.pseudoAuteur}-->: <!--{$document.date_modification}--></em>
		</p>
	</div>
	<input type="hidden" name="idDocument" value="<!--{$document.id_document}-->" id="idDocument" />
</div>