<!--{include file="barre_preferences_fr.tpl"}-->

<div id="blocCentre">

	<div id="blocCarte">
		<div id="map" style="width: 350px; height: 400px"></div>
		<div id="blocEdition">

			<div id="lieu" >
				
				<fieldset id="infos_de_base" >
					<legend>Infos de base</legend>
					<p><label>nom</label> <input type="text" name="nom" value="<!--{$lieu.nom}-->" id="inputNom"></input></p>
					<p><label>localité</label> <input type="text" name="commune" value="<!--{$lieu.commune}-->" id="inputCommune"></input></p>
					<p><label>npa</label> <input type="text" name="npa" value="<!--{$lieu.npa}-->" id="inputNpa"></input></p>
					<p><label>rue</label> <input type="text" name="rue" value="<!--{$lieu.rue}-->" id="inputRue"></input></p>
					<p><label>carte</label> <a href="#" id="getAdresse"><img src="http://<!--{$server_name}-->/utile/img/flag_red.png" alt="flag" title="Placer un marker sur la carte..."/> montrer le lieu</a></p>
				</fieldset>
				<fieldset id="précisions">
					<legend>Infos supplémentaires</legend>
					
					<p><input type="hidden" name="latitude" value="<!--{$lieu.latitude}-->" id="inputLatitude"></input></p>
					<p><input type="hidden" name="longitude" value="<!--{$lieu.longitude}-->" id="inputLongitude"></input></p>
					<p><input type="hidden" name="altitude" value="<!--{$lieu.altitude}-->" id="inputAltitude"></input> <!-- <a href="#" id="getAltitude">obtenir l'altitude via geonames.org</a> --></p>			
					<p>
						<label for=description>Description</label>
					</p>
					<textarea id="inputDescription" name="description" rows="4" cols="40"><!--{$lieu.description}--></textarea>

					<div id="blocTags">
						<p>
							<label title="séparés par des ,">tags</label><input type="text" name="tags" id="tags" value="<!--{$tags}-->" /> <!-- <a id="enregistreTag" href="#">enregistrer</a> -->
						</p>
					</div>

					<input type="hidden" name="pays" value="suisse" id="inputPays"></input>
				</fieldset>
			</div>

			
			<p>
				<a href="#" id="saveLieu"><img src="http://<!--{$server_name}-->/utile/img/save.png" alt="save" title="enregistrer le lieu"/> enregistrer</a>  |  
				<a href="/lieu/" id="cancelLieu" ><img src="http://<!--{$server_name}-->/utile/img/action_stop.gif" alt="annuler" /> annuler</a>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="/lieu/?new" id="newLieu" ><img src="http://<!--{$server_name}-->/utile/img/add.png" alt="ajouter" /> ajouter un lieu...</a> 
			</p>
		</div>
	</div>

	<hr />

	<input type="hidden" name="latitude" value="<!--{$lieu.latitude}-->" id="lieuLatitude"></input>
	<input type="hidden" name="longitude" value="<!--{$lieu.longitude}-->" id="lieuLongitude"></input>
	<input type="hidden" name="idLieu" value="<!--{$lieu.id_lieu}-->" id="idLieu" />

	
		<div id="logAction">
		</div>


</div>