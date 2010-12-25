
<!--{include file="barre_navigation_fr.tpl"}-->

<div id="blocCalendrier">
	<input type="hidden" name="idCalendrier" value="<!--{$calendrier.id_calendrier}-->" id="idCalendrier" />


	<div id="blocLegendeMois">
		<div class="legendeMois" id="legendeJanvier">
			janvier
		</div>
		<div class="legendeMois" id="legendeFevrier">
			février
		</div>
		<div class="legendeMois" id="legendeMars">
			mars
		</div>
		<div class="legendeMois" id="legendeAvril">
			avril
		</div>
		<div class="legendeMois" id="legendeMai">
			mai
		</div>
		<div class="legendeMois" id="legendeJuin">
			juin
		</div>
		<div class="legendeMois" id="legendeJuillet">
			juillet
		</div>
		<div class="legendeMois" id="legendeAout">
			août
		</div>
		<div class="legendeMois" id="legendeSeptembre">
			septembre
		</div>
		<div class="legendeMois" id="legendeOctobre">
			octobre
		</div>
		<div class="legendeMois" id="legendeNovembre">
			novembre
		</div>
		<div class="legendeMois" id="legendeDecembre">
			décembre
		</div>
	</div>


	<div id="blocCalendrierAnnee">
		<div class="colonneMois" id="colonneJanvier">
			<!--{foreach from=$datesAnnee[0] key=key item=aDate}-->
				<div class="caseJourAnnee" id="<!--{$aDate.dateJour}-->">
					<div class="noJourAnnee<!--{if $aDate.jourSemaine==7}--> jourDimancheAnnee<!--{/if}-->" title="<!--{$aDate.dateHumaine}-->"><!--{$aDate.noJour}--></div>
				</div>
			<!--{/foreach}-->
		</div>
		<div class="colonneMois" id="colonneFevrier">
			<!--{foreach from=$datesAnnee[1] key=key item=aDate}-->
				<div class="caseJourAnnee" id="<!--{$aDate.dateJour}-->">
					<div class="noJourAnnee<!--{if $aDate.jourSemaine==7}--> jourDimancheAnnee<!--{/if}-->" title="<!--{$aDate.dateHumaine}-->"><!--{$aDate.noJour}--></div>
				</div>
			<!--{/foreach}-->
		</div>
		<div class="colonneMois" id="colonneMars">
			<!--{foreach from=$datesAnnee[2] key=key item=aDate}-->
				<div class="caseJourAnnee" id="<!--{$aDate.dateJour}-->">
					<div class="noJourAnnee<!--{if $aDate.jourSemaine==7}--> jourDimancheAnnee<!--{/if}-->" title="<!--{$aDate.dateHumaine}-->"><!--{$aDate.noJour}--></div>
				</div>
			<!--{/foreach}-->
		</div>
		<div class="colonneMois" id="colonneAvril">
			<!--{foreach from=$datesAnnee[3] key=key item=aDate}-->
				<div class="caseJourAnnee" id="<!--{$aDate.dateJour}-->">
					<div class="noJourAnnee<!--{if $aDate.jourSemaine==7}--> jourDimancheAnnee<!--{/if}-->" title="<!--{$aDate.dateHumaine}-->"><!--{$aDate.noJour}--></div>
				</div>
			<!--{/foreach}-->
		</div>
		<div class="colonneMois" id="colonneMai">
			<!--{foreach from=$datesAnnee[4] key=key item=aDate}-->
				<div class="caseJourAnnee" id="<!--{$aDate.dateJour}-->">
					<div class="noJourAnnee<!--{if $aDate.jourSemaine==7}--> jourDimancheAnnee<!--{/if}-->" title="<!--{$aDate.dateHumaine}-->"><!--{$aDate.noJour}--></div>
				</div>
			<!--{/foreach}-->
		</div>
		<div class="colonneMois" id="colonneJuin">
			<!--{foreach from=$datesAnnee[5] key=key item=aDate}-->
				<div class="caseJourAnnee" id="<!--{$aDate.dateJour}-->">
					<div class="noJourAnnee<!--{if $aDate.jourSemaine==7}--> jourDimancheAnnee<!--{/if}-->" title="<!--{$aDate.dateHumaine}-->"><!--{$aDate.noJour}--></div>
				</div>
			<!--{/foreach}-->
		</div>
		<div class="colonneMois" id="colonneJuillet">
			<!--{foreach from=$datesAnnee[6] key=key item=aDate}-->
				<div class="caseJourAnnee" id="<!--{$aDate.dateJour}-->">
					<div class="noJourAnnee<!--{if $aDate.jourSemaine==7}--> jourDimancheAnnee<!--{/if}-->" title="<!--{$aDate.dateHumaine}-->"><!--{$aDate.noJour}--></div>
				</div>
			<!--{/foreach}-->
		</div>
		<div class="colonneMois" id="colonneAout">
			<!--{foreach from=$datesAnnee[7] key=key item=aDate}-->
				<div class="caseJourAnnee" id="<!--{$aDate.dateJour}-->">
					<div class="noJourAnnee<!--{if $aDate.jourSemaine==7}--> jourDimancheAnnee<!--{/if}-->" title="<!--{$aDate.dateHumaine}-->"><!--{$aDate.noJour}--></div>
				</div>
			<!--{/foreach}-->
		</div>
		<div class="colonneMois" id="colonneSeptembre">
			<!--{foreach from=$datesAnnee[8] key=key item=aDate}-->
				<div class="caseJourAnnee" id="<!--{$aDate.dateJour}-->">
					<div class="noJourAnnee<!--{if $aDate.jourSemaine==7}--> jourDimancheAnnee<!--{/if}-->" title="<!--{$aDate.dateHumaine}-->"><!--{$aDate.noJour}--></div>
				</div>
			<!--{/foreach}-->
		</div>
		<div class="colonneMois" id="colonneOctobre">
			<!--{foreach from=$datesAnnee[9] key=key item=aDate}-->
				<div class="caseJourAnnee" id="<!--{$aDate.dateJour}-->">
					<div class="noJourAnnee<!--{if $aDate.jourSemaine==7}--> jourDimancheAnnee<!--{/if}-->" title="<!--{$aDate.dateHumaine}-->"><!--{$aDate.noJour}--></div>
				</div>
			<!--{/foreach}-->
		</div>
		<div class="colonneMois" id="colonneNovembre">
			<!--{foreach from=$datesAnnee[10] key=key item=aDate}-->
				<div class="caseJourAnnee" id="<!--{$aDate.dateJour}-->">
					<div class="noJourAnnee<!--{if $aDate.jourSemaine==7}--> jourDimancheAnnee<!--{/if}-->" title="<!--{$aDate.dateHumaine}-->"><!--{$aDate.noJour}--></div>
				</div>
			<!--{/foreach}-->
		</div>
		<div class="colonneMois" id="colonneDecembre">
			<!--{foreach from=$datesAnnee[11] key=key item=aDate}-->
				<div class="caseJourAnnee" id="<!--{$aDate.dateJour}-->">
					<div class="noJourAnnee<!--{if $aDate.jourSemaine==7}--> jourDimancheAnnee<!--{/if}-->" title="<!--{$aDate.dateHumaine}-->"><!--{$aDate.noJour}--></div>
				</div>
			<!--{/foreach}-->
		</div>
		
		<!--{section loop=$evenements name=aEvenement}-->
			<div id="<!--{$evenements[aEvenement].id}-->" class="evenementAnnee <!--{$evenements[aEvenement].class}-->" style="top:<!--{$evenements[aEvenement].top}-->px; left:<!--{$evenements[aEvenement].left}-->px; height:<!--{$evenements[aEvenement].height}-->px; width:<!--{$largeurBlocEvenement}-->px; background-color: #<!--{$evenements[aEvenement].color}-->; border-color: <!--{$evenements[aEvenement].borderColor}-->; color: <!--{$evenements[aEvenement].borderColor}-->;" title=<!--{if $evenements[aEvenement].jour_entier=='true'}-->"<!--{$evenements[aEvenement].nom}-->: <!--{$evenements[aEvenement].dateJourEntier}-->"<!--{else}-->"<!--{$evenements[aEvenement].nom}-->: <!--{$evenements[aEvenement].dateDebut}--> au <!--{$evenements[aEvenement].dateFin}-->"<!--{/if}--> >
				<p  id="nomEvenement<!--{$evenements[aEvenement].id}-->" class="nomEvenement" style="height:<!--{$evenements[aEvenement].nomHeight}-->px;" <!--{if $editable}--> onclick="app.showModifierEvenement(<!--{$evenements[aEvenement].id}-->)" <!--{else}--> onclick="app.showEvenementDetail(<!--{$evenements[aEvenement].id}-->)"<!--{/if}--> ></p>
			</div>
		<!--{/section}-->
		
	</div> <!--blocCalendrierAnnee --!>


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