	<input type="hidden" name="categorieLieuDetail" value="" id="categorieLieuDetail" />
	<input type="hidden" name="idEvenement" value="<!--{$evenement.id_evenement}-->" id="idEvenement" />
	<input type="hidden" name="uidEvenement" value="<!--{$evenement.uid}-->" id="uidEvenement" />
	<input type="hidden" name="idEvenementSuivant" value="" id="idEvenementSuivant" />
	<input type="hidden" name="idEvenementPrecedent" value="" id="idEvenementPrecedent" />
	
	<div id="blocEvenement" >
		
		<div id="blocHeader">
				<div id="blocSave">
					<a href="//<!--{$server_name}-->/event/<!--{$evenement.id_evenement}-->-<!--{$evenement.nomSimplifie}-->.html" id="cancelEvenement" title="fermer"  ><img src="http://<!--{$server_name}-->/utile/img/action_stop.gif" alt="save" /> fermer</a>  
				</div>
		</div>
		
		<hr />
		<fieldset>
		
		<h2><!--{$evenement.nom}--></h2>
		
				<!--{if $evenement.jour_entier=="true"}-->
				<p>
					<label>Date</label> <!--{$evenement.jourDebutHumain}--> <!--{if $evenement.jourDebut != $evenement.jourFin}--> - <!--{$evenement.jourFinHumain}--><!--{/if}-->
				</p>
				<!--{else}-->
				<p>
					<label>du</label><span><!--{$evenement.dateDebut}--></span><br />
					<label>au</label><span><!--{$evenement.dateFin}--></span>
				</p>
				<!--{/if}-->
				
				<!--{if $evenement.periodicite=='+1 day'}-->
					<p>à lieu chaque jour</p>
				<!--{elseif $evenement.periodicite=='+1 week'}-->
					<p>à lieu chaque semaine</p>
				<!--{elseif $evenement.periodicite=='+1 month'}-->
					<p>à lieu chaque mois</p>
				<!--{elseif $evenement.periodicite=='+1 year'}-->
					<p>à lieu chaque année</p>
				<!--{/if}-->
				
				<p>
				<label>Lieu</label>
					<!--{if !empty($evenement.lieu)}-->
						<a href="//<!--{$server_name}-->/lieu/<!--{$evenement.lieu.id_lieu}-->-lieu.html" target="blank" title="voir le lieu sur la carte..."><!--{$evenement.lieu.nom}-->,  <!--{$evenement.lieu.commune}--></a>
					<!--{/if}-->
				</p>
				
				<p>
					<label>Description</label>
					<!--{$evenement.description}-->
				</p>
				
			
			<p>
				<label title="Personne responsable de l'événement">Contact</label>
				<!--{if !empty($evenement.contact)}-->
					<!--{$evenement.contact.prenom}-->&nbsp;<!--{$evenement.contact.nom}-->, <a href="mailto:<!--{$evenement.contact.email}-->"><!--{$evenement.contact.email}--></a>
				<!--{/if}-->
			</p>
			
			
			<!--{if $multiCalendriers}-->
			<p>
				<label for="idCalendrierEvenement">Calendrier</label>

					<!--{foreach from=$calendriers key=id item=calendrier}-->
						<!--{if $evenement.id_calendrier==$calendrier.id_calendrier}--><!--{$calendrier.nom}--><!--{/if}-->
					<!--{/foreach}-->
			</p>
			<!--{/if}-->
			
		</fieldset>
		
		<div id="blocMetadonnees">
			<p>
				<label>Création</label><!--{$evenement.date_creation|date_format:'%d-%m-%Y à %H:%M'}-->&nbsp;&nbsp;&nbsp;&nbsp;
				<label>Modification</label><!--{$evenement.date_modification|date_format:'%d-%m-%Y à %H:%M'}-->
			</p>
		</div>

	</div> <!-- div blocEvenement -->
	
	<div id="logAction">
	</div>