<!--{include file="barre_preferences_fr.tpl"}-->

<input type="hidden" name="idCalendrier" value="<!--{$calendrier.id_calendrier}-->" id="idCalendrier" />

<div id="miseEnPage">
	<div id="calendrierEdit" >	
		<p>
			<label for="nom">Nom</label> <input type="text" name="nom" value="<!--{$calendrier.nom}-->" id="nom" />
		</p>

		<div id="blocDescription">
			<p>
				<label for="description">Description</label>
			</p>
			<textarea name="description" id="description" rows="2" cols="40"><!--{$calendrier.description}--></textarea>
		</div>

		<p>
			<label for="couleur">Couleur</label> <input type="text" name="couleur" value="<!--{$calendrier.couleur}-->" style="background-color: #<!--{$calendrier.couleur}-->;" id="couleur" />
		</p>

		<p>
			<label for="distant">Calendrier distant</label>
			<input type="checkbox" name="distant" id="distant" <!--{if $calendrier.distant=="1"}-->checked="checked"<!--{/if}--> />
		</p>
		<div id="blocCalendrierDistant" class="toggleBloc">
			<p>
				<label for="url">URL</label> <input type="text" name="url" value="<!--{$calendrier.url}-->" id="url" />
			</p>
			<div id="blocTags">
				<p>
					<label title="séparés par des ,">Tags attribués automatiquement à l'importation</label><input type="text" name="tags" id="tags" value="<!--{$calendrier.tags}-->" />
				</p>
			</div>
		</div>
	</div>

	<p>
		<a href="#" id="saveCalendrier"><img src="http://<!--{$server_name}-->/utile/img/save.png" alt="save" /> enregistrer le calendrier</a>  |  
		<a href="//<!--{$server_name}-->/calendrier/<!--{$calendrier.id_calendrier}-->-<!--{$calendrier.nomSimplifie}-->.html" >retour au calendrier</a>
	</p>

	<div id="logAction">
	</div>
</div>