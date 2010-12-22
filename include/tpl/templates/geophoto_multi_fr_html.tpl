<div id="galerie">
	<!--{foreach from=$photos key=key item=aPhoto}-->	
		
	<div id="unephoto">
		<input type="hidden" class="infoIdPhoto" value="<!--{$aPhoto.id_photo}-->"></input>
		<!--{if $aPhoto.latitude!=''}-->
		<input type="hidden" name="latitude" value="<!--{$aPhoto.latitude}-->" id="photoLatitude<!--{$aPhoto.id_photo}-->"></input>
		<input type="hidden" name="longitude" value="<!--{$aPhoto.longitude}-->" id="photoLongitude<!--{$aPhoto.id_photo}-->"></input>
		<!--{/if}-->
		
		<div class="cadre_vignette">
			<div class="<!--{if $aPhoto.orientation == 'v' }-->vignette_verticale<!--{else}-->vignette_horizontale<!--{/if}-->">
				<div class="ombre">
					<a href="<!--{if $aPhoto.externe != '1' }-->http://<!--{$server_name}-->/<!--{/if}--><!--{$aPhoto.lienMoyenne}-->" title ="<!--{$aPhoto.listeTags}-->" rel="shadowbox[album];options={animate:false,continuous:true}">
						<img src="<!--{if $aPhoto.externe != '1' }-->http://<!--{$server_name}-->/<!--{/if}--><!--{$aPhoto.lienVignette}-->" alt="<!--{$aPhoto.listeTags}-->" title="Cliquez pour agrandir" />
					</a>
				</div>
			</div>
			<div class="lienIndividuel">
				<a href="http://<!--{$server_name}-->/photo/<!--{$aPhoto.id_photo}-->-<!--{$aPhoto.nom}-->.html" >
					<img src="http://<!--{$server_name}-->/utile/img/plus.png" alt="plus" title="voir la photo seule..." />
				</a>
			</div>
		</div>
		
		<div class="zoneMap" id="map<!--{$aPhoto.id_photo}-->" style="width: 200px; height: 200px"></div>
		
		
		<p class="descriptionPhoto" id="descriptionPhoto<!--{$aPhoto.id_photo}-->">
			<!--{$aPhoto.description}-->
		</p>

		
	</div>
	<!--{/foreach}-->
</div>