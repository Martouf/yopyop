<a href="statut/?import-facebook-status">Mettre à jour les statuts</a>
<div id="listeStatut">	
	<!--{foreach from=$statuts key=key item=aStatut}-->	
	
			<div id="statut_<!--{$aStatut.id_statut}-->" style="background-color:#<!--{$aStatut.color}-->; float:left; width:<!--{$aStatut.width}-->px; height:<!--{$aStatut.height}-->px; padding: 3px;" >				
				<p><a href="<!--{$aStatut.guid}-->" title="<!--{$aStatut.datePublication}-->"><!--{$aStatut.auteur_texte}-->: <!--{$aStatut.description}--></a></p>
			</div>
	<!--{/foreach}-->
</div>


