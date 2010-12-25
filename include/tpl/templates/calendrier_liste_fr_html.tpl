
<!--{include file="barre_navigation_fr.tpl"}-->


<input type="hidden" name="idCalendrier" value="<!--{$calendrier.id_calendrier}-->" id="idCalendrier" />
<script language="JavaScript" type="text/javascript">
//<![CDATA[	
	var arrayId = [<!--{$listeId}-->];
//]]>
</script>


<div>
	<form id="filter-form" title="Rechercher dans les événements de la sélections courante">&nbsp;Rechercher dans la sélection: <input name="filter" id="filter" value="" maxlength="30" size="30" type="text"></form>
</div>
<br />

<table id="listeEvenements" class="tablesorter" cellpadding="0" cellspacing="0" >
	<thead>
		<tr>
			<th>Etat</th>
			<th>Date</th>
			<th>Nom</th>
			<th>Lieu</th>
			<th>Horaire</th>
			<th>&nbsp;</th>
		</tr>
	</thead>
	<tbody>
	<!--{foreach from=$evenements key=key item=aEvenement}-->	
		<tr>
			<td>
				&nbsp;
				<!--{if $aEvenement.state=='3'}-->
					<img src="http://<!--{$server_name}-->/utile/img/bullet_green.png" alt="ok" title="Etat ok"/>
				<!--{elseif $aEvenement.state=='2'}-->
					<img src="http://<!--{$server_name}-->/utile/img/bullet_blue.png" alt="vérifier" title="Etat contenu à vérifier"/>
				<!--{elseif $aEvenement.state=='4'}-->
					<img src="http://<!--{$server_name}-->/utile/img/export.png" alt="exported" title="Evénement déjà exporté en xml"/>
				<!--{else}-->
					<img src="http://<!--{$server_name}-->/utile/img/bullet_red.png" alt="pas ok" title="Etat pas ok"/>
				<!--{/if}-->
			</td>
			<td>
				<!--{if $aEvenement.dateDebut!=$aEvenement.dateFin}-->
					<!--{$aEvenement.dateDebut}--> -<br />
				<!--{/if}-->
			 	<!--{$aEvenement.dateFin}-->
			</td>
			<td>
				<a href="//<!--{$server_name}-->/evenement/<!--{$aEvenement.id_evenement}-->-<!--{$aEvenement.nomSimplifie}-->.html" title="détails..." onclick="app.showModifierEvenement(<!--{$aEvenement.id_evenement}-->); return false;"><!--{$aEvenement.nom}--></a>
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
			<td><a id="boutonDupliquer" href="#" onclick="app.dupliquerEvenement(<!--{$aEvenement.id_evenement}-->); return false;"><img src="http://<!--{$server_name}-->/utile/img/copy2.png" alt="duplicate" title="Dupliquer l'événement..." /></a></td>
		</tr>
		<!--{/foreach}-->
		</tbody>
</table>

<p>
	&nbsp;&nbsp;<label>exportation</label>
	<span id="blocChoixFormat">
		<select name="choixFormat" id="choixFormat">
			<option <!--{if $choixFormat=='html'}-->selected="selected"<!--{/if}--> value="html">choisir le format</option>
			<option <!--{if $choixFormat=='xml'}-->selected="selected"<!--{/if}--> value="xml">xml</option>
			<option <!--{if $choixFormat=='ics'}-->selected="selected"<!--{/if}--> value="ics">ics</option>		
		</select>
	</span>
	
	<span title="exporter la page au format pdf..." id="blocLienPdf">&nbsp;&nbsp;&nbsp;<img src="http://<!--{$server_name}-->/utile/img/page_white_acrobat.png" alt="export pdf" /> <a id="lienExportPdf" href="//<!--{$server_name}-->/agenda/calendrier.pdf">pdf</a></span>
	
</p>

<ul id="listeCalendrier">
	<!--{foreach from=$calendriers key=key item=aCalendrier}-->	
	<li>
		<span style="background-color:#<!--{$aCalendrier.couleur}-->">
			&nbsp;&nbsp;
		</span>
		<a href="//<!--{$server_name}-->/calendrier/<!--{$aCalendrier.id_calendrier}-->-<!--{$aCalendrier.nomSimplifie}-->.html"><!--{$aCalendrier.nom}--></a>
		<a href="//<!--{$server_name}-->/calendrier/<!--{$aCalendrier.id_calendrier}-->-<!--{$aCalendrier.nomSimplifie}-->.html?modify"><img src="http://<!--{$server_name}-->/utile/img/edit.gif" alt="edit" title="modifier les propriétés du calendrier..." /></a>
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