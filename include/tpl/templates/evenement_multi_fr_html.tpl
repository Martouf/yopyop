<table id="listeEvenements" class="tablesorter" cellpadding="0" cellspacing="0" >
	<thead>
		<tr>
			<th>Date</th>
			<th>Nom</th>
			<th>Lieu</th>
			<th>Horaire</th>
		</tr>
	</thead>
	
	<!--{foreach from=$evenements key=key item=aEvenement}-->	
		<tr>
			<td>
				<!--{if $aEvenement.dateDebut!=$aEvenement.dateFin}-->
					<!--{$aEvenement.dateDebut}--> -<br />
				<!--{/if}-->
			 	<!--{$aEvenement.dateFin}-->
			</td>
			<td>
				<a href="//<!--{$server_name}-->/evenement/<!--{$aEvenement.id_evenement}-->-<!--{$aEvenement.nomSimplifie}-->.html" title="détails..."><!--{$aEvenement.nom}--></a>
			</td>
			<td><!--{$aEvenement.lieu}--></td>
			<td>
				<!--{if $aEvenement.jour_entier =='true'}-->
					Toute la journée
				<!--{elseif $aEvenement.dateDebut==$aEvenement.dateFin}-->
					<!--{$aEvenement.heureDebut}--> - <!--{$aEvenement.heureFin}-->
				<!--{else}-->
					<!--{$aEvenement.dateDebut}--> <!--{$aEvenement.heureDebut}--> <br />
					<!--{$aEvenement.dateFin}--> <!--{$aEvenement.heureFin}-->
				<!--{/if}-->
			</td>
		</tr>
		<!--{/foreach}-->
</table>