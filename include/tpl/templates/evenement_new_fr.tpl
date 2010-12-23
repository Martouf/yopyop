	<div id="evenement" >
			
		<p>
			<label>Nom</label>
		</p>
		<p>
			<input type="text" name="nomDetail" value="" id="nomDetail">
		</p>
		<p>
			<label>Lieu</label>
		</p>
		<p>
			<select id="lieuDetail" name="lieuDetail">
				<!--{foreach from=$lieux key=id item=lieu}-->
					<option <!--{if $evenement.lieu==$lieu.id_lieu}-->selected="selected"<!--{/if}-->  value="<!--{$lieu.id_lieu}-->"><!--{$lieu.commune}-->: <!--{$lieu.nom}--></option>
				<!--{/foreach}-->
			</select>

			<a href="/lieu/?new" id="lienAddLieu" target="blank">ajouter un lieu</a>
		</p>
		<p>
			<label>Dates</label>
		</p>
		
		<div>
			<input type="text" name="jourDebutDetail" value="" id="jourDebutDetail" class="date-pick" />

				<label class="interfaceHeure" for="heureDebutDetail">heure</label>

				<select class="interfaceHeure" id="heureDebutDetail" name="heureDebutDetail">
					<!--{foreach from=$heures key=id item=heure}-->
						<option <!--{if $heure==$evenement.heureDebut}-->selected="selected"<!--{/if}-->  value="<!--{$heure}-->"><!--{$heure}--></option>
					<!--{/foreach}-->
				</select>
				<select class="interfaceHeure" id="minuteDebutDetail" name="minuteDebuDetail">
					<!--{foreach from=$minutes key=id item=minute}-->
						<option <!--{if $minute==$evenement.minuteDebut}-->selected="selected"<!--{/if}-->  value="<!--{$minute}-->"><!--{$minute}--></option>
					<!--{/foreach}-->
				</select>
		</div>
		<div>
			<input type="text" name="jourFinDetail" value="" id="jourFinDetail" class="date-pick" />


				<label class="interfaceHeure" for="heureFinDetail">heure</label>

				<select class="interfaceHeure" id="heureFinDetail" name="heureFinDetail">
					<!--{foreach from=$heures key=id item=heure}-->
						<option <!--{if $heure==$evenement.heureFin}-->selected="selected"<!--{/if}-->  value="<!--{$heure}-->"><!--{$heure}--></option>
					<!--{/foreach}-->
				</select>
				<select class="interfaceHeure" id="minuteFinDetail" name="minuteFinDetail">
					<!--{foreach from=$minutes key=id item=minute}-->
						<option <!--{if $minute==$evenement.minuteFin}-->selected="selected"<!--{/if}-->  value="<!--{$minute}-->"><!--{$minute}--></option>
					<!--{/foreach}-->
				</select>

		</div>
		<p>
			<label for="jourComplet">Jour entier</label>
			<input type="checkbox" name="jourComplet" id="jourComplet" <!--{if $evenement.jourEntier=="true"}-->checked="checked"<!--{/if}-->  />
		</p>
		<p>
			<label>Description</label>
		</p>
		<textarea id="descriptionDetail" name="descriptionDetail" rows="8" cols="40"></textarea>
		
		<p>
			<label for="idCalendrierEvenement">Calendrier</label>
			
			<select id="idCalendrierEvenement" name="idCalendrierEvenement">
				<!--{foreach from=$calendriers key=id item=calendrier}-->
					<option value="<!--{$calendrier.id_calendrier}-->"><!--{$calendrier.nom}--></option>
				<!--{/foreach}-->
			</select>
		</p>
		
	</div>
	
	<div id="blocTags">
		<p>
			<label title="séparés par des ,">tags</label><input type="text" name="tags" id="tags" value="" />
		</p>
	</div>
	<p><a href="#" id="createEvenement"><img src="http://<!--{$server_name}-->/utile/img/save.png" alt="save" /> enregistrer l'évenement</a></p>
