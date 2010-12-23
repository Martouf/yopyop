<table id="listeReservations" class="tablesorter" cellpadding="0" cellspacing="0" >
	<thead>
		<tr>
			<th>Heure réservation</th>
			<th>Objet</th>
			<th>Personne</th>
			<th>Dates</th>
			<th>Remarque</th>
			<th>Type</th>
			<th>Etat</th>
		</tr>
	</thead>
	
	<!--{foreach from=$reservations key=key item=aReservation}-->	
		<tr>
			<td>
				<!--{$aReservation.date_modification}-->
			</td>
			<td>
				<a href="/reservation/<!--{$aReservation.id_reservation}-->-<!--{$aReservation.nomSimplifie}-->.html" title="détails..."><!--{$aReservation.objet.nom}--></a>
			</td>
			<td>
				<!--{$aReservation.locataire.prenom}--> <!--{$aReservation.locataire.nom}-->
			</td>

			<td>
				<!--{if $aReservation.evenement.jour_entier=="true"}-->
				<p>
					<!--{$aReservation.evenement.jourDebutHumain}--> <!--{if $aReservation.evenement.jourDebut != $aReservation.evenement.jourFin}--> - <!--{$aReservation.evenement.jourFinHumain}--><!--{/if}-->
				</p>
				<!--{else}-->
				<p>
					<!--{$aReservation.evenement.dateDebut}--></span><br />
					<!--{$aReservation.evenement.dateFin}--></span>
				</p>
				<!--{/if}-->
			</td>
			<td>
				<p><!--{$aReservation.description}--></p>
			</td>
			<td>
				<p>
					<!--{if $aReservation.type==1}--> définitive<!--{else}--> provisoire<!--{/if}-->
				</p>
			</td>
			<td>
				<p>
					<a href="/reservation/<!--{$aReservation.id_reservation}-->-<!--{$aReservation.nomSimplifie}-->.html?modify" title="éditer la réservation...">
					<!--{if $aReservation.etat==0}-->
						<img src="http://<!--{$server_name}-->/utile/img/bullet_orange.png" alt="en attente" title="en attente de validation" /> à valider
					<!--{elseif $aReservation.etat==1}-->
						<img src="http://<!--{$server_name}-->/utile/img/bullet_green.png" alt="acceptée" title="acceptée" />acceptée
					<!--{else}-->
						<img src="http://<!--{$server_name}-->/utile/img/bullet_red.png" alt="refusée" title="refusée" />refusée
					<!--{/if}-->
					</a>
				</p>
			</td>

		</tr>
		<!--{/foreach}-->
</table>