<!--{include file="barre_preferences_fr.tpl"}-->

<div id="blocCentre">
	<div>
		<form id="filter-form"><label>&nbsp;&nbsp;&nbsp;Rechercher</label> <input name="filter" id="filter" value="" maxlength="30" size="30" type="text">
		&nbsp;&nbsp;&nbsp;<a href="/lieu/?new" id="boutonNewLieu"><img src="http://<!--{$server_name}-->/utile/img/add.png" alt="add" /> ajouter un lieu</a>
		</form>
	</div>
	<br />
	
	<table id="listeLieux" class="tablesorter" cellpadding="0" cellspacing="0" >
		<thead>
			<tr>
				<th>Nom</th>
				<th>Localité</th>
				<th>Rue</th>
				<th>Paroisse</th>
			</tr>
		</thead>
		<tbody>
			<!--{foreach from=$lieux key=key item=aLieu}-->	
			<tr>
				<td><a href="/lieu/<!--{$aLieu.id_lieu}-->-<!--{$aLieu.nomSimplifie}-->.html?modify"><!--{$aLieu.nom}--></a></td>
				<td><!--{$aLieu.commune}--></td>
				<td><!--{$aLieu.rue}--></td>
				<td><!--{$aLieu.paroisse}--></td>					
			</tr>
			<!--{/foreach}-->
		</tbody>
	</table>
</div>