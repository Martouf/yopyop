<div id="galerie">
	<!--{foreach from=$photos key=key item=aPhoto}-->	
	
				<div class="cadre_vignette">
					<div class="<!--{if $aPhoto.orientation == 'v' }-->vignette_verticale<!--{else}-->vignette_horizontale<!--{/if}-->">
						<div class="ombre">
							<a href="<!--{if $aPhoto.externe == '0' }-->http://<!--{$server_name}-->/<!--{/if}--><!--{$aPhoto.lienMoyenne}-->" title ="<!--{$aPhoto.listeTags}-->" rel="shadowbox[album];options={animate:false,continuous:true}">
								<img src="<!--{if $aPhoto.externe == '0' }-->http://<!--{$server_name}-->/<!--{/if}--><!--{$aPhoto.lienVignette}-->" alt="<!--{$aPhoto.listeTags}-->" title="Cliquez pour agrandir" />
							</a>
						</div>
					</div>
					<div class="lienIndividuel">
						<a href="http://<!--{$server_name}-->/photo/<!--{$aPhoto.id_photo}-->-<!--{$aPhoto.nom}-->.html" >
							<img src="http://<!--{$server_name}-->/utile/img/plus.png" alt="plus" title="ajouter un commentaire pour cette photo..." />
						</a>
					</div>
				</div>
	<!--{/foreach}-->
</div>