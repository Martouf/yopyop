<!--{include file="barre_preferences_fr.tpl"}-->
<div id="miseEnPage">
	<div id="calendrier" >	
		<p>
			<label for="nom">Nom</label> <input type="text" name="nom" value="" id="nom" />
		</p>
	
		<div id="blocDescription">
			<p>
				<label for="description">Description</label>
			</p>
			<textarea name="description" id="description" rows="2" cols="40"></textarea>
		</div>
	
		<p>
			<label for="couleur">Couleur</label> <input type="text" name="couleur" value="ff0000" id="couleur" />
		</p>
		
		<p>
			<label for="distant">Calendrier distant</label>
			<input type="checkbox" name="distant" id="distant" />
		</p>
		<div id="blocCalendrierDistant" class="toggleBloc">
			<p>
				<label for="url">URL</label> <input type="text" name="url" value="http://" id="url" />
			</p>
			<div id="blocTags">
				<p>
					<label title="séparés par des ,">Tags attribués automatiquement à l'importation</label><input type="text" name="tags" id="tags" value="" />
				</p>
			</div>
		</div>
	</div>

	<p><a href="#" id="createCalendrier"><img src="/utile/img/save.png" alt="save" /> enregistrer le calendrier</a></p>
</div>