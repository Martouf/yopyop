<a href="//<!--{$server_name}-->/objet/<!--{$objet.id_objet}-->-<!--{$objet.nomSimplifie}-->.html">Retour à la fiche de l'objet</a>
	
<input type="hidden" name="idObjet" value="<!--{$objet.id_objet}-->" id="idObjet" />
<input type="hidden" name="idImage" value="<!--{$objet.id_image}-->" id="inputIdImage" />

<!--{if $objet.latitude!=''}-->
<input type="hidden" name="latitude" value="<!--{$objet.latitude}-->" id="objetLatitude" />
<input type="hidden" name="longitude" value="<!--{$objet.longitude}-->" id="objetLongitude" />
<!--{else}-->
<input type="hidden" name="latitude" value="46.93244765730184" id="objetLatitude" />
<input type="hidden" name="longitude" value="6.8767547607421875>" id="objetLongitude" />
<!--{/if}-->

	<div id="objet" >
		
		<div id="blocImagePresentation">
			<a href="http://<!--{$server_name}-->/<!--{$imagePresentation.lienMoyenne}-->" title ="<!--{$imagePresentation.nom}-->" rel="shadowbox[album];options={animate:false,continuous:true}">
				<img class="ombre" src="http://<!--{$server_name}-->/<!--{$imagePresentation.lienVignette}-->" alt="<!--{$imagePresentation.nom}-->" title="Cliquez pour agrandir" />
			</a>
		</div>
		
		<p>
			<label for="nom">nom</label>
			<input type="text" name="nom" value="<!--{$objet.nom}-->" id="inputNom" />
		</p>
		
		<div id="blocTags">
			<p>
				<label for="tags" title="séparés par des ,">Tags</label><input type="text" name="tags" id="tags" value="<!--{$tags}-->" /> <a id="enregistreTag" href="#">enregistrer</a>
			</p>
		</div>
		<p>
			<label for="prix">Prix</label>
			<input type="text" name="prix" value="<!--{$objet.prix}-->" id="inputPrix" /> Kong / jour
		</p>
		<p>
			<label for="caution">Caution</label>
			<input type="text" name="caution" value="<!--{$objet.caution}-->" id="inputCaution" /><br />
		</p>
		<p>	
			<label for="caution">Disponibilités</label>
			<span class="valeur"><a title="voir le calendrier des réservations dans une nouvelle fenêtre..." target="blank" href="//<!--{$server_name}-->/calendrier/<!--{$objet.id_calendrier}-->-reservations-<!--{$objet.nomSimplifie}-->.html">Voir le calendrier des réservations...</a>
			</span>
		</p>
		<p>
			<label title="Définit si l'objet est disponible à la location pour d'autres personne, ou si il est temporairement retiré du marché." for="inputEtat">Etat</label>
			<select id="inputEtat" name="inputEtat">
				<!--{if $objet.etat=='0'}--><option selected="selected" value="0">en cours de création</option><!--{/if}-->
				<option <!--{if $objet.etat=='1'}-->selected="selected"<!--{/if}--> value="1">disponible</option>
				<option <!--{if $objet.etat=='2'}-->selected="selected"<!--{/if}--> value="2">privé</option>
			</select>

		</p>
		
		<p id="blocResume">
			<label for="description">Description</label>
			<textarea name="description" id="description" rows="5" cols="130"><!--{$objet.description}--></textarea>
		</p>
		
		<div>
			<div id="map" style="width: 400px; height: 300px"></div>
			<p id="blocEdition">
				
				<label>adresse</label> <input type="text" name="lieu" value="<!--{$objet.lieu}-->" id="inputLieu" /><br />
				
				<label>latitude</label> <input type="text" name="latitude" value="<!--{$objet.latitude}-->" id="inputLatitude" /><a href="#" id="getAdresse">obtenir les coordonnées depuis l'adresse</a><br />
				
				<label>longitude</label> <input type="text" name="longitude" value="<!--{$objet.longitude}-->" id="inputLongitude" /><br />
			</p>
		</div>
		
	</div>
	
	<p>
		<a href="#" onclick="app.saveObjet(); return false;"><img src="http://<!--{$server_name}-->/utile/img/save.png" alt="save" /> enregistrer les données de l'objet</a> &nbsp;|&nbsp; 
		<a href="//<!--{$server_name}-->/objet/<!--{$objet.id_objet}-->-<!--{$objet.nomSimplifie}-->.html" id="cancelObjet" >annuler</a>
	</p>

<div id="loading">
	<img src="http://<!--{$server_name}-->/utile/img/loading.gif" alt="loading"/> loading
</div>
<div id="logAction">
</div>