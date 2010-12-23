<table id="listeReservations" class="tablesorter" cellpadding="0" cellspacing="0" >
	<thead>
		<tr>
			<th>Objet</th>
			<th>Personne</th>
			<th>Dates</th>
			<th>remarque</th>
			<th>type</th>
			<th>etat</th>
		</tr>
	</thead>
	
	<!--{foreach from=$reservations key=key item=aReservation}-->	
		<tr>
			<td>
				<a href="/reservation/<!--{$aReservation.id_reservation}-->-<!--{$aReservation.nomSimplifie}-->.html" title="détails..."><!--{$aReservation.objet.nom}--></a>
			</td>
			<td>
				<!--{$aReservation.locataire.surnom}-->
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
					<!--{if $aReservation.etat==0}-->
						<img src="http://<!--{$server_name}-->/utile/img/bullet_orange.png" alt="en attente" title="en attente de validation" /> à valider
					<!--{elseif $aReservation.etat==1}-->
						<img src="http://<!--{$server_name}-->/utile/img/bullet_green.png" alt="acceptée" title="acceptée" />acceptée
					<!--{else}-->
						<img src="http://<!--{$server_name}-->/utile/img/bullet_red.png" alt="refusée" title="refusée" />refusée
					<!--{/if}-->
				</p>
			</td>

		</tr>
		<!--{/foreach}-->
</table>