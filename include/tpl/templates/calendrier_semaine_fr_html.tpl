
<!--{include file="barre_navigation_fr.tpl"}-->

<div id="blocCalendrier">
	<input type="hidden" name="idCalendrier" value="<!--{$calendrier.id_calendrier}-->" id="idCalendrier" />

		<div id="dateSemaine">
			<div class="legendeHeure">&nbsp;</div>
			<!--{section loop=$dates name=aDate}-->
				<div class="dateDuJour">
					<!--{$dates[aDate].dateHumaine}-->
					<input type="hidden" name="jour<!--{$dates[aDate].idJour}-->" value="<!--{$dates[aDate].dateJour}-->" id="jour<!--{$dates[aDate].idJour}-->" />
				</div>
			<!--{/section}-->
		</div>

		<div id="jourEntier">
			<div class="legendeHeure">&nbsp;</div>

			<!--{section name=jour start=0 loop=7}-->
				<div id="<!--{$jours[$smarty.section.jour.index]}-->" class="jourEntier <!--{if $jours[$smarty.section.jour.index]==$dateAujourdhui}-->aujourdhui<!--{/if}-->">

					<!--{section loop=$evenementsJourEntier[$smarty.section.jour.index] name=aEvenement}-->
						<div id="<!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].id}-->" class="moisEvenement <!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].class}-->" style="width:<!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].width}-->px; background-color: #<!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].color}-->; border-color: <!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].borderColor}-->; color: <!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].borderColor}-->;" title="<!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].nom}-->" <!--{if $editable}--> onclick="app.showModifierEvenement(<!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].id}-->)" <!--{/if}--> >
							<input type="hidden" class="hDebut" value="<!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].hDebut}-->" id="hDebut<!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].id}-->" name="hDebut<!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].id}-->" />
							<input type="hidden" class="hFin" value="<!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].hFin}-->" id="hFin<!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].id}-->" name="hFin<!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].id}-->" />
							<input type="hidden" class="nbJour" value="<!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].nbJour}-->" id="nbJour<!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].id}-->" name="nbJour<!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].id}-->" />
							<span id="nomEvenement<!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].id}-->" class="nomEvenement"><!--{$evenementsJourEntier[$smarty.section.jour.index][aEvenement].nom}--></span>
						</div>
					<!--{/section}-->

				</div>
			<!--{/section}-->

		</div>

	<div id="fenetreCalendrier">
		<div id="calendrier">

			<div id="graduation">
				<!--{section name=graduation start=0 loop=24}-->
					<div id="heure<!--{$smarty.section.graduation.index}-->" class="heure">
						<p class="demiHeure">&nbsp;<!--{$smarty.section.graduation.index}-->:00</p>
						<p class="demiHeure ligneMillieu">&nbsp;</p>
					</div>
				<!--{/section}-->
			</div>

			<!--{section name=jour start=0 loop=7}-->
				<div id="jour<!--{$smarty.section.jour.index}-->" class="jour">
					<!--{section name=heure start=0 loop=24}-->
						<div id="jour<!--{$smarty.section.jour.index}-->_heure<!--{$smarty.section.heure.index}-->" class="heure">
							<p class="demiHeure" id="<!--{$smarty.section.jour.index}-->_<!--{$smarty.section.heure.index}-->_0">&nbsp;</p>
							<p class="demiHeure ligneMillieu" id="<!--{$smarty.section.jour.index}-->_<!--{$smarty.section.heure.index}-->_30">&nbsp;</p>
						</div>
					<!--{/section}-->
				</div>
			<!--{/section}-->

			<!--{section loop=$evenements name=aEvenement}-->
				<div id="<!--{$evenements[aEvenement].id}-->" class="evenement <!--{$evenements[aEvenement].class}-->" style="top:<!--{$evenements[aEvenement].top}-->px; left:<!--{$evenements[aEvenement].left}-->px; height:<!--{$evenements[aEvenement].height}-->px; background-color: #<!--{$evenements[aEvenement].color}-->; border-color: <!--{$evenements[aEvenement].borderColor}-->; color: <!--{$evenements[aEvenement].borderColor}-->;" title="<!--{$evenements[aEvenement].nom}-->" >
					<h3 class="poignee" id="heureEvenement<!--{$evenements[aEvenement].id}-->"><!--{$evenements[aEvenement].heureDebut}--></h3>
					<p id="nomEvenement<!--{$evenements[aEvenement].id}-->" class="nomEvenement" style="height:<!--{$evenements[aEvenement].nomHeight}-->px;" <!--{if $editable}--> onclick="app.showModifierEvenement(<!--{$evenements[aEvenement].id}-->)" <!--{else}--> onclick="app.showEvenementDetail(<!--{$evenements[aEvenement].id}-->)"<!--{/if}--> ><!--{$evenements[aEvenement].nom}--></p>
					<div class="<!--{$evenements[aEvenement].resizeHandle}-->" id="resizeHandle<!--{$evenements[aEvenement].id}-->">
						&nbsp;
					</div>
				</div>
			<!--{/section}-->

		</div>
	</div>

	<hr />
	<p title="exporter la page au format pdf..." id="blocLienPdf">&nbsp;&nbsp;&nbsp;<img src="/utile/img/page_white_acrobat.png" alt="export pdf" /> <a id="lienExportPdf" href="/agenda/calendrier.pdf">pdf</a></p>
	
	<ul id="listeCalendrier">
		<!--{foreach from=$calendriers key=key item=aCalendrier}-->	
		<li>
			<span style="background-color:#<!--{$aCalendrier.couleur}-->">
				&nbsp;&nbsp;
			</span>
			<a href="/calendrier/<!--{$aCalendrier.id_calendrier}-->-<!--{$aCalendrier.nomSimplifie}-->.html"><!--{$aCalendrier.nom}--></a>
			<a href="/calendrier/<!--{$aCalendrier.id_calendrier}-->-<!--{$aCalendrier.nomSimplifie}-->.html?modify"><img src="/utile/img/edit.gif" alt="edit" title="modifier les propriétés du calendrier..." /></a>
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