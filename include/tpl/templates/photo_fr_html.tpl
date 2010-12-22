<div id="outilsModif">
	<!--{if $utilisateurConnu}--> <a href="<!--{$photo.id_photo}-->-<!--{$photo.nomSimplifie}-->.html?modify" title="modifier les données de la photo..."><img src="http://<!--{$server_name}-->/utile/img/edit.gif" /></a><!--{/if}-->
</div>

<div id="bloc_moyenne">
	<div class="cadre_moyenne">
		<div class="<!--{if $photo.orientation == 'v' }-->moyenne_verticale<!--{else}-->moyenne_horizontale<!--{/if}-->">
			<div class="ombre">
				<a href="<!--{if $photo.externe == '0' }-->http://<!--{$server_name}-->/<!--{/if}--><!--{$photo.lien}-->" title="Cliquez pour agrandir">
					<img src="<!--{if $photo.externe == '0' }-->http://<!--{$server_name}-->/<!--{/if}--><!--{$photo.lienMoyenne}-->" alt="<!--{$photo.listeTags}-->" title="cliquez pour agrandir" />
				</a>
			</div>
		</div>
		<hr />
	</div>
</div>

<p id="descriptionPhoto">
	<!--{$photo.description}-->
</p>
<div id="map" style="width: 500px; height: 400px"></div>

<input type="hidden" name="idPhoto" value="<!--{$photo.id_photo}-->" id="idPhoto" />
<!--{if $photo.latitude!=''}-->
<input type="hidden" name="latitude" value="<!--{$photo.latitude}-->" id="photoLatitude"></input>
<input type="hidden" name="longitude" value="<!--{$photo.longitude}-->" id="photoLongitude"></input>
<!--{/if}-->

