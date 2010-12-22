<!--{include file="barre_preferences_fr.tpl"}-->

<div id="blocCarte">
	<div id="map" style="width: 450px; height: 400px"></div>
	
	<div id="blocEdition">
		<div id="lieu" >
			<input type="hidden" name="idLieu" value="<!--{$lieu.id_lieu}-->" id="idLieu" />
			<p><label>Nom</label> <!--{$lieu.nom}--></p>
			<p><label>rue</label> <!--{$lieu.rue}--></p>
			<p><label>npa</label> <!--{$lieu.npa}--></p>
			<p><label>localité</label> <!--{$lieu.commune}--></p>
			<p><label>pays</label> <!--{$lieu.pays}--></p>
			<p><label>latitude</label> <!--{$lieu.latitude}--></p>
			<p><label>longitude</label> <!--{$lieu.longitude}--></p>
			<p><label>altitude</label> <!--{$lieu.altitude}--></p>
			<p><label>description</label> <!--{$lieu.description}--></p>
		</div>
	</div>
	
</div>


<input type="hidden" name="latitude" value="<!--{$lieu.latitude}-->" id="lieuLatitude"></input>
<input type="hidden" name="longitude" value="<!--{$lieu.longitude}-->" id="lieuLongitude"></input>