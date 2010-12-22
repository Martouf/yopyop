<a href="statut/?import-facebook-status">Mettre à jour les statuts</a>
<div id="listeStatut">	
	<!--{foreach from=$statuts key=key item=aStatut}-->	
	
			<div id="statut_<!--{$aStatut.id_statut}-->"  ondblclick="document.location='<!--{$aStatut.id_statut}-->-statut.html?modify';" >				

				<p class="corpsStatut" style="background-color:#<!--{$aStatut.color}-->;"> <strong><!--{$aStatut.auteur_texte}--></strong> <a href="<!--{$aStatut.guid}-->"><!--{$aStatut.description}--></a></p>
				<p class="dateStatut">
					<!--{$aStatut.datePublication}-->
				</p>

			</div>
	<!--{/foreach}-->
</div>


