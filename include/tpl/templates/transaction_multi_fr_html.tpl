<div id="blocCentre">
	<div>
		<form id="filter-form"><label>&nbsp;&nbsp;&nbsp;Rechercher</label> <input name="filter" id="filter" value="" maxlength="30" size="30" type="text">
		</form>
	</div>
	<br />
	
	<table id="listeTransactions" class="tablesorter" cellpadding="0" cellspacing="0" >
		<thead>
			<tr>
				<th>Nom</th>
				<th>description</th>
				<th>source</th>
				<th>destinataire</th>
				<th>montant</th>
				<th>ip</th>
				<th>date</th>
			</tr>
		</thead>
		<tbody>
			<!--{foreach from=$transactions key=key item=aTransaction}-->	
			<tr>
				<td><!--{$aTransaction.nom}--></td>
				<td><!--{$aTransaction.description}--></td>
				<td><!--{$aTransaction.id_source}--></td>
				<td><!--{$aTransaction.id_destinataire}--></td>
				<td><!--{$aTransaction.montant}--></td>
				<td><!--{$aTransaction.ip}--></td>
				<td><!--{$aTransaction.dateCreation}--></td>					
			</tr>
			<!--{/foreach}-->
		</tbody>
	</table>
</div>