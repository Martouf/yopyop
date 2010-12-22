<a href="statut/?import-facebook-status">Mettre à jour les statuts</a>
<div id="listeStatut">	
	<!--{foreach from=$statuts key=key item=aStatut}-->	
	
			<div id="statut_<!--{$aStatut.id_statut}-->" style="background-color:#<!--{$aStatut.color}-->; float:left; width:100px; height:50px;" >				
				<p><a href="<!--{$aStatut.guid}-->" title="<!--{$aStatut.datePublication}-->: <!--{$aStatut.description}-->"><!--{$aStatut.auteur_texte}--></a></p>
			</div>
	<!--{/foreach}-->
</div>


