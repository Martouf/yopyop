
<!--{include file="barre_navigation_public_fr.tpl"}-->


<input type="hidden" name="idCalendrier" value="<!--{$calendrier.id_calendrier}-->" id="idCalendrier" />
<script language="JavaScript" type="text/javascript">
//<![CDATA[	
	var arrayId = [<!--{$listeId}-->];
//]]>
</script>


<div>
	<form id="filter-form" title="Rechercher dans les événements de la sélections courante"><label>&nbsp;Rechercher dans la sélection</label> <input name="filter" id="filter" value="" maxlength="30" size="30" type="text" /></form>
</div>
<br />

<table id="listeEvenements" class="tablesorter" cellpadding="0" cellspacing="0" >
	<thead>
		<tr>
			<th>Date</th>
			<th>Nom</th>
			<th>Lieu</th>
			<th>Horaire</th>
		</tr>
	</thead>
	<tbody>
	<!--{foreach from=$evenements key=key item=aEvenement}-->	
		<tr>
			<td>
				<!--{if $aEvenement.dateDebut!=$aEvenement.dateFin}-->
					<!--{$aEvenement.dateDebut}--> -<br />
				<!--{/if}-->
			 	<!--{$aEvenement.dateFin}-->
			</td>
			<td>
				<a href="//<!--{$server_name}-->/evenement/<!--{$aEvenement.id_evenement}-->-<!--{$aEvenement.nomSimplifie}-->.html" title="détails..." onclick="app.showEvenementDetail(<!--{$aEvenement.id_evenement}-->); return false;"><!--{$aEvenement.nom}--></a>
			</td>
			<td><!--{$aEvenement.lieuNom}-->, <!--{$aEvenement.lieuCommune}--></td>
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
		</tbody>
</table>

<p>
	<span id="blocChoixFormat">
		&nbsp;&nbsp;<label>exportation</label>
		<select name="choixFormat" id="choixFormat">
			<option <!--{if $choixFormat=='html'}-->selected="selected"<!--{/if}--> value="html">choisir le format</option>
			<option <!--{if $choixFormat=='xml'}-->selected="selected"<!--{/if}--> value="xml">xml</option>
			<option <!--{if $choixFormat=='ics'}-->selected="selected"<!--{/if}--> value="ics">ics</option>	
			<option <!--{if $choixFormat=='pdf'}-->selected="selected"<!--{/if}--> value="pdf">pdf</option>			
		</select>
	</span>
</p>

<ul id="listeCalendrier">
	<!--{foreach from=$calendriers key=key item=aCalendrier}-->	
	<li>
		<span style="background-color:#<!--{$aCalendrier.couleur}-->">
			&nbsp;&nbsp;
		</span>
		<a href="//<!--{$server_name}-->/agenda/<!--{$aCalendrier.id_calendrier}-->-<!--{$aCalendrier.nomSimplifie}-->.html"><!--{$aCalendrier.nom}--></a>
	</li>
	<!--{/foreach}-->
</ul>

<hr />


<div id="masque">
&nbsp;
</div>
<div id="boiteDialogue">
	&nbsp;
</div>