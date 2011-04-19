<div id="blocCentre">
	<div>
		<form id="filter-form"><label>&nbsp;&nbsp;&nbsp;Rechercher</label> <input name="filter" id="filter" value="" maxlength="30" size="30" type="text">
			<span style="margin-left: 20px;">
				<a href="/mesure/?import" />Importer des données fraiches...</a>
			</span>
		</form>
	</div>
	<br />
	
	<table id="listeMesures" class="tablesorter" cellpadding="0" cellspacing="0" >
		<thead>
			<tr>
				<th>Nom</th>
				<th>valeur</th>
				<th>type</th>
				<th>lieu</th>
				<th>date de la mesure</th>
			</tr>
		</thead>
		<tbody>
			<!--{foreach from=$mesures key=key item=aMesure}-->	
			<tr>
				<td><!--{$aMesure.nom}--></td>
				<td><!--{$aMesure.valeur}--></td>
				<td><!--{$aMesure.type}--></td>
				<td><!--{$aMesure.nom_lieu}--></td>
				<td><!--{$aMesure.date_mesure}--></td>					
			</tr>
			<!--{/foreach}-->
		</tbody>
	</table>
</div>