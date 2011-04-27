<div id="blocCentre">
	<div>
		<form id="filter-form"><label>&nbsp;&nbsp;&nbsp;Rechercher</label> <input name="filter" id="filter" value="" maxlength="30" size="30" type="text">
		</form>
	</div>
	<br />
	
	<table id="listeNotifications" class="tablesorter" cellpadding="0" cellspacing="0" >
		<thead>
			<tr>
				<th>Nom</th>
				<th>description</th>
				<th>type</th>
				<th>etat</th>
				<th>date</th>
			</tr>
		</thead>
		<tbody>
			<!--{foreach from=$notifications key=key item=aNotification}-->	
			<tr>
				<td><!--{$aNotification.nom}--></td>
				<td><!--{$aNotification.description}--></td>
				<td><!--{$aNotification.type}--></td>
				<td><!--{$aNotification.etat}--></td>
				<td><!--{$aNotification.dateCreation}--></td>					
			</tr>
			<!--{/foreach}-->
		</tbody>
	</table>
</div>