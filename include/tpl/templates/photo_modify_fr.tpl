<a href="/photo/<!--{$photo.id_photo}-->-<!--{$photo.nomSimplifie}-->.html">retour à la photo</a>
	
<input type="hidden" name="idPhoto" value="<!--{$photo.id_photo}-->" id="idPhoto" />
<input type="hidden" name="latitude" value="<!--{$photo.latitude}-->" id="photoLatitude"></input>
<input type="hidden" name="longitude" value="<!--{$photo.longitude}-->" id="photoLongitude"></input>

	<div id="photo" >
		
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
		
		<p>
			<label for="nom">nom</label>
			<input type="text" name="nom" value="<!--{$photo.nom}-->" id="inputNom" />
		</p>
		
		<div id="blocTags">
			<p>
				<label for="tags" title="séparés par des ,">tags</label><input type="text" name="tags" id="tags" value="<!--{$tags}-->" /> <a id="enregistreTag" href="#">enregistrer</a>
			</p>
		</div>
	
		<div id="blocResume">
			<label for="description">Description</label>
			<textarea name="description" id="description" rows="5" cols="130"><!--{$photo.description}--></textarea>
		</div>
		
		<div id="blocCarte">
			<div id="map" style="width: 500px; height: 400px"></div>
			<div id="blocEdition">
				<p><label>adresse</label> <input type="text" name="adresse" value="" id="inputAdresse"></input></p>
				<p><label>latitude</label> <input type="text" name="latitude" value="<!--{$photo.latitude}-->" id="inputLatitude"></input><a href="#" id="getAdresse">obtenir les coordonnées depuis l'adresse</a></p>
				<p><label>longitude</label> <input type="text" name="longitude" value="<!--{$photo.longitude}-->" id="inputLongitude"></input></p>
				<p><label>altitude</label> <input type="text" name="altitude" value="" id="inputAltitude"></input> <a href="#" id="getAltitude">obtenir l'altitude via geonames.org</a></p>	
			</div>
		</div>
	</div>
	
	<p>
		<a href="#" onclick="app.savePhoto(); return false;"><img src="http://<!--{$server_name}-->/utile/img/save.png" alt="save" /> enregistrer les données de la photo</a> &nbsp;|&nbsp; 
		<a href="/photo/<!--{$photo.id_photo}-->-<!--{$photo.nomSimplifie}-->.html" id="cancelPhoto" >annuler</a>
	</p>

<div id="loading">
	<img src="http://<!--{$server_name}-->/utile/img/loading.gif" alt="loading"/> loading
</div>
<div id="logAction">
</div>