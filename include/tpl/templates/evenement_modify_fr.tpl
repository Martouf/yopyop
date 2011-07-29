<input type="hidden" name="categorieLieuDetail" value="" id="categorieLieuDetail" />
<input type="hidden" name="idEvenement" value="<!--{$evenement.id_evenement}-->" id="idEvenement" />
<input type="hidden" name="uidEvenement" value="<!--{$evenement.uid}-->" id="uidEvenement" />
<input type="hidden" name="idEvenementSuivant" value="" id="idEvenementSuivant" />
<input type="hidden" name="idEvenementPrecedent" value="" id="idEvenementPrecedent" />

<div id="blocEvenement" >
	
	<div id="blocHeader">
			<div id="blocSave">
				<a href="#" id="saveEvenement"><img src="http://<!--{$server_name}-->/utile/img/save.png" alt="save" title="enregistrer l'événement courant" /> enregistrer</a>  |  
				<a href="//<!--{$server_name}-->/evenement/<!--{$evenement.id_evenement}-->-<!--{$evenement.nomSimplifie}-->.html" id="cancelEvenement" title="fermer sans enregistrer"  ><img src="http://<!--{$server_name}-->/utile/img/close_gray.gif" alt="close" /> fermer</a>  |  
				<a href="#" id="suprrimeEvenement" ><img src="http://<!--{$server_name}-->/utile/img/delete.gif" alt="supprimer" title="supprimer l'événement" /> supprimer</a>
			</div>

			<span id="blocNavigationFleche">
				<a href="#" id="FlecheEvenementPrecedent" ><img src="http://<!--{$server_name}-->/utile/img/arrow_left.png" class="" alt="previous" title="événement précédent"/></a>
				<a href="#" id="boutonSauverFleche" ><img src="http://<!--{$server_name}-->/utile/img/save.png" alt="sauver" title="enregistrer l'événement courant" /></a>
				<a href="#" id="FlecheEvenementSuivant" ><img src="http://<!--{$server_name}-->/utile/img/arrow_right.png" class=""  alt="next" title="événement suivant"/></a>
			</span>
	</div>
	
	<hr />
	<fieldset>
	
	<div id="blocColonneGauche">
		
			<label>Nom de l'événement</label>

			<textarea name="nomDetail" rows="2" cols="40" id="nomDetail" ><!--{$evenement.nom}--></textarea>
		<p>
			<label title="Indiquez les dates et heures de début et de fin de l'événement">Dates</label>
		</p>

		<div class="blocDateHeure">
			<input type="text" name="jourDebutDetail" value="<!--{$evenement.jourDebutEurope}-->" id="jourDebutDetail" class="date-pick" />

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
		<div class="blocDateHeure">
			<input type="text" name="jourFinDetail" value="<!--{$evenement.jourFinEurope}-->" id="jourFinDetail" class="date-pick" />


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
		<p id="blocJourComplet">
			<label for="jourComplet" title="Cochez la case pour les événements qui durent toute la journée">Jour entier</label>
			<input type="checkbox" name="jourComplet" id="jourComplet" <!--{if $evenement.jour_entier=="true"}-->checked="checked"<!--{/if}-->  />
		</p>
		
		<p id="blocPeriode">
			<label title="Indiquez la fréquence de répétition de l'événement">répéter</label>
			<select id="periodiciteEvenement" name="periodiciteEvenement">
				<option <!--{if $evenement.periodicite=='non'}-->selected="selected"<!--{/if}--> value="non">jamais</option>
				<option <!--{if $evenement.periodicite=='+1 day'}-->selected="selected"<!--{/if}--> value="+1 day">tous les jours</option>
				<option <!--{if $evenement.periodicite=='+1 week'}-->selected="selected"<!--{/if}--> value="+1 week">toutes les semaines</option>
				<option <!--{if $evenement.periodicite=='+1 month'}-->selected="selected"<!--{/if}--> value="+1 month">tous les mois</option>
				<option <!--{if $evenement.periodicite=='+1 year'}-->selected="selected"<!--{/if}--> value="+1 year">tous les ans</option>
			</select>
		</p>
		<div id="createurEvenementMultiple">
			<label for="nbOccurrence" title="Indiquez le nombre de fois que l'événement se répéte">Nombre d'occurrences</label> <input type="text" name="nbOccurrence" value="<!--{$evenement.nbOccurrence}-->" id="nbOccurrence" maxlength=2 />
			<p>
				<label title="Par défaut une modification agit sur tous les événements d'une série périodique">agir sur </label>
				<select id="periodiqueAutonome" name="periodiqueAutonome">
					<option selected="selected" value="0">tous les événements de la série périodique</option>
					<option value="1">uniquement l'événement sélectionné</option>
				</select>
			</p>
		</div>
	
	
	<!--{if $modeEvenementSimple}-->
		<input type="hidden" name="lieuDetail" value="0" id="lieuDetail" />
		<input type="hidden" name="infoEvenement" value="<!--{$evenement.auteur}-->" id="infoEvenement" />
		<input type="hidden" name="idCalendrierEvenement" value="<!--{$evenement.id_calendrier}-->" id="idCalendrierEvenement" />
		
	<!--{else}-->	
		
		<p>
			<label title="Sélectionnez le lieu où se déroule l'événement en tapant la LOCALITE, puis le LIEU EXACT. Si le lieu n'existe pas, ajoutez le dans le menu: lieux">Lieu</label>
		</p>
		<div id="comboLieu">
			<select id="lieuDetail" name="lieuDetail">
				<option value="0"></option>
				<!--{foreach from=$lieux key=id item=lieu}-->
					<option <!--{if $evenement.lieu==$lieu.id_lieu}-->selected="selected"<!--{/if}-->  value="<!--{$lieu.id_lieu}-->"><!--{$lieu.commune}-->: <!--{$lieu.nom}--></option>
				<!--{/foreach}-->
			</select>
		</div>
		
		<br />
		
		<p>
			<label title="Sélectionnez la personne responsable de l'événement en tapant sont NOM, puis son PRENOM">Personne de contact</label>
		</p>
		
		<select id="infoEvenement" name="infoEvenement">
				<option value="0"></option>
			<!--{foreach from=$contacts key=id item=contact}-->
				<option <!--{if $evenement.info==$contact.id_personne}-->selected="selected"<!--{/if}-->  value="<!--{$contact.id_personne}-->"><!--{$contact.nom}-->&nbsp;<!--{$contact.prenom}--></option>
			<!--{/foreach}-->
		</select>

		<!--{if $multiCalendriers}-->
		<p>
			<label for="idCalendrierEvenement">Calendrier</label>

			<select id="idCalendrierEvenement" name="idCalendrierEvenement">
				<!--{foreach from=$calendriers key=id item=calendrier}-->
					<option <!--{if $evenement.id_calendrier==$calendrier.id_calendrier}-->selected="selected"<!--{/if}-->  value="<!--{$calendrier.id_calendrier}-->"><!--{$calendrier.nom}--></option>
				<!--{/foreach}-->
			</select>
		</p>
		<!--{else}-->
			<input type="hidden" name="idCalendrierEvenement" value="<!--{$calendrierParDefaut}-->" id="idCalendrierEvenement" />
		<!--{/if}-->
		
	<!--{/if}-->	
		
	</div>
	<div id="blocColonneDroite">
		
		<div id="blocTags">
			<p>
				<label title="tags séparé les tags par des ,">Tags</label><input type="text" name="tags" id="tags" value="<!--{$tags}-->" />
			</p>
		</div>
		
		<p>
			<label>Description</label>
		</p>
		<textarea id="descriptionDetail" name="descriptionDetail" rows="8" cols="60"><!--{$evenement.description}--></textarea>
		
		<p>
			<label>Notes internes</label>
		</p>
		<textarea id="remarqueEvenement" name="remarqueEvenement" rows="3" cols="40"><!--{$evenement.remarque}--></textarea>
		
		
		<!--{if $modeEvenementSimple==false}-->
		<p>
			voir le lieu sur la carte: <span><a href="//<!--{$server_name}-->/lieu/<!--{$evenement.lieu.id_lieu}-->-lieu.html" target="blank" title="voir le lieu dans une nouvelle fenêtre..."><img src="http://<!--{$server_name}-->/utile/img/flag_red.png" alt="flag" /></a></span>
		</p>
		<!--{/if}-->
		
		<div id="blocMetadonnees">
			<p>
				<!--{if $modeEvenementSimple}-->
					<label>Création</label><!--{$evenement.date_creation|date_format:'%d-%m-%Y à %H:%M'}-->, <!--{$evenement.pseudoCreateur}--> <br />
					<label>Modification</label><!--{$evenement.date_modification|date_format:'%d-%m-%Y à %H:%M'}-->, <!--{$evenement.pseudoModificateur}-->
					
				<!--{else}-->
					<label>Création</label><!--{$evenement.date_creation|date_format:'%d-%m-%Y à %H:%M'}-->, <a href="mailto:<!--{$evenement.emailCreateur}-->"><!--{$evenement.emailCreateur}--></a> <br />
					<label>Modification</label><!--{$evenement.date_modification|date_format:'%d-%m-%Y à %H:%M'}-->, <a href="mailto:<!--{$evenement.emailModificateur}-->"><!--{$evenement.emailModificateur}--></a>
				
				<!--{/if}-->
			</p>
		</div>
		
	</div>
	</fieldset>

</div> <!-- div blocEvenement -->

<div id="logAction">
</div>