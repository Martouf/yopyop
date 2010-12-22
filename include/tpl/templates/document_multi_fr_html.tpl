<!--{if $verbosity=="normal"}-->

	<div id="listeDocument">	
		<!--{foreach from=$documents key=key item=aDocument}-->	
	
				<div id="document_<!--{$aDocument.id_document}-->"  ondblclick="document.location='<!--{$aDocument.id_document}-->-<!--{$aDocument.nomSimplifie}-->.html?modify';" >				

					<div class="blocNom">
						<!--{$aDocument.nom}-->
					</div>

					<div class="blocContenu">
						<!--{$aDocument.contenu}-->
					</div>

					<div class="blocMetaDonnees">
						<p>
							<em><!--{$aDocument.pseudoAuteur}-->: <!--{$aDocument.dateModification}--></em>
						</p>
					</div>

				</div>
		<!--{/foreach}-->
	</div>

<!--{elseif $verbosity=="resume"}-->

	<div id="listeDocument">	
		<!--{foreach from=$documents key=key item=aDocument}-->	
	
				<div id="document_<!--{$aDocument.id_document}-->" >				

					<div class="blocNom">
						<!--{$aDocument.nom}-->
					</div>

					<div class="blocContenu">
						<!--{$aDocument.description}-->
						<br /><a href="<!--{$aDocument.id_document}-->-<!--{$aDocument.nomSimplifie}-->.html">lire la suite...</a>
					</div>

					<div class="blocMetaDonnees">
						<p>
							<em><!--{$aDocument.dateModification}--></em>
						</p>
					</div>

				</div>
		<!--{/foreach}-->
	</div>
	
<!--{else}-->

 <h2>Liste des documents disponibles</h2>
	<ul id="listeDocument">	
		<!--{foreach from=$documents key=key item=aDocument}-->	
			<li id="document_<!--{$aDocument.id_document}-->"><a href="<!--{$aDocument.id_document}-->-<!--{$aDocument.nomSimplifie}-->.html"><!--{$aDocument.nom}--></a></li>
		<!--{/foreach}-->
	</ul>
<!--{/if}-->