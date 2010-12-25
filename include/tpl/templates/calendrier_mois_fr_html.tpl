
<!--{include file="barre_navigation_fr.tpl"}-->	
<hr />

<div id="blocCalendrier">
	<div id="dateSemaine">
		<!--{section loop=$jourDeLaSemaine name=aJour}-->
			<div class="dateDuJour">
				<!--{$jourDeLaSemaine[aJour]}-->
			</div>
		<!--{/section}-->
	</div>

	<div id="calendrierMois">

		<!--{section name=semaine start=0 loop=5}-->
			<div id="semaine<!--{$smarty.section.semaine.index}-->" class="semaine">

				<!--{section name=jour start=0 loop=7}-->
					<div id="<!--{$jours[$smarty.section.semaine.index][$smarty.section.jour.index]}-->" class="blocJour <!--{if $jours[$smarty.section.semaine.index][$smarty.section.jour.index]==$dateAujourdhui}-->aujourdhui<!--{/if}-->">
						<span class="noDuJour"><!--{$semaine[$smarty.section.semaine.index][$smarty.section.jour.index]}--></span>

						<!--{section loop=$evenements[$smarty.section.semaine.index][$smarty.section.jour.index] name=aEvenement}-->
							<div id="<!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].id}-->" class="moisEvenement <!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].class}-->" style="width:<!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].width}-->px; background-color: #<!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].color}-->; border-color: <!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].borderColor}-->; color: <!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].borderColor}-->;" title="<!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].nom}-->" <!--{if $editable}--> onclick="app.showModifierEvenement(<!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].id}-->)" <!--{/if}--> >
								<input type="hidden" class="hDebut" value="<!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].hDebut}-->" id="hDebut<!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].id}-->" name="hDebut<!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].id}-->" />
								<input type="hidden" class="hFin" value="<!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].hFin}-->" id="hFin<!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].id}-->" name="hFin<!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].id}-->" />
								<input type="hidden" class="nbJour" value="<!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].nbJour}-->" id="nbJour<!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].id}-->" name="nbJour<!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].id}-->" />
								<span id="nomEvenement<!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].id}-->" class="nomEvenement"><!--{$evenements[$smarty.section.semaine.index][$smarty.section.jour.index][aEvenement].nom}--></span>
							</div>
						<!--{/section}-->

					</div>
				<!--{/section}-->
			</div>
		<!--{/section}-->
	</div>
	<hr />
	<p title="exporter la page au format pdf..." id="blocLienPdf">&nbsp;&nbsp;&nbsp;<img src="http://<!--{$server_name}-->/utile/img/page_white_acrobat.png" alt="export pdf" /> <a id="lienExportPdf" href="//<!--{$server_name}-->/agenda/calendrier.pdf">pdf</a></p>
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
</div>


<div id="masque">
&nbsp;
</div>
<div id="boiteDialogue">
	<img src="http://<!--{$server_name}-->/utile/img/loading.gif" alt="loading"/> loading
</div>