<div id="blocCentre">
	<div>
		<form id="filter-form"><label>&nbsp;&nbsp;&nbsp;Rechercher</label> <input name="filter" id="filter" value="" maxlength="30" size="30" type="text">
		</form>
	</div>
	<br />
	
	<table id="listeHistoriques" class="tablesorter" cellpadding="0" cellspacing="0" >
		<thead>
			<tr>
				<th>Nom</th>
				<th>url</th>
				<th>ip</th>
				<th>user agent</th>
				<th>date</th>
			</tr>
		</thead>
		<tbody>
			<!--{foreach from=$historiques key=key item=aHistorique}-->	
			<tr>
				<td><!--{$aHistorique.nom}--></td>
				<td><!--{$aHistorique.url}--></td>
				<td><!--{$aHistorique.ip}--></td>
				<td><!--{$aHistorique.user_agent}--></td>
				<td><!--{$aHistorique.date_creation}--></td>					
			</tr>
			<!--{/foreach}-->
		</tbody>
	</table>
</div>