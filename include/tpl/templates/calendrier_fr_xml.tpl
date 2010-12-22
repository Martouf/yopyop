<?xml version="1.0" encoding="utf-8" standalone="yes"?>
<root>
<!--{foreach from=$calendriers key=key item=aCalendrier}-->
<sec-<!--{$aCalendrier.nomSimplifie}-->>
<Evenement>
<!--{foreach from=$evenements key=key item=aEvenement}-->
<!--{if $aCalendrier.id_calendrier==$aEvenement.id_calendrier && $aEvenement.type==1}-->
		<annonce id="<!--{$aEvenement.id_evenement}-->"><objet><!--{$aEvenement.nom}-->#*</objet><description><!--{$aEvenement.description}-->#*</description><date><!--{$aEvenement.dateDebutJourSemaineDateMoisHeureHumaine}-->.</date>#*<location><!--{$aEvenement.lieuEvenement.nom}--></location><locality>, <!--{$aEvenement.lieuEvenement.commune}-->.</locality>#*<info><!--{$aEvenement.infoContact.first_name}--> <!--{$aEvenement.infoContact.last_name}-->, <!--{$aEvenement.infoContact.tel}-->.#*</info></annonce>
<!--{/if}-->
<!--{/foreach}-->
</Evenement>
<Nota-bene>
<!--{foreach from=$evenements key=key item=aEvenement}-->
<!--{if $aCalendrier.id_calendrier==$aEvenement.id_calendrier && $aEvenement.type==2}-->
	<annonce id="<!--{$aEvenement.id_evenement}-->"><objet><!--{$aEvenement.nom}-->#*</objet><description><!--{$aEvenement.description}-->#*</description><date><!--{$aEvenement.dateDebutJourSemaineDateMoisHeureHumaine}-->.</date>#*<location><!--{$aEvenement.lieuEvenement.nom}--></location><locality>, <!--{$aEvenement.lieuEvenement.commune}-->.</locality>#*<info><!--{$aEvenement.infoContact.first_name}--> <!--{$aEvenement.infoContact.last_name}-->, <!--{$aEvenement.infoContact.tel}-->#*</info></annonce>			
<!--{/if}-->
<!--{/foreach}-->
</Nota-bene>
<Mémo>
<!--{foreach from=$evenements key=key item=aEvenement}-->
<!--{if $aCalendrier.id_calendrier==$aEvenement.id_calendrier && $aEvenement.type==3}-->
	<!--{if $aEvenement.periodicite=="+1 week"}-->
		<annonce id="<!--{$aEvenement.id_evenement}-->"><objet><!--{$aEvenement.nom}-->#*</objet><date>Chaque <!--{$aEvenement.dateDebutJourDeLaSemaine}-->.</date>#*<location><!--{$aEvenement.lieuEvenement.nom}--></location><locality>, <!--{$aEvenement.lieuEvenement.commune}-->.</locality>#*<description><!--{$aEvenement.description}-->#*</description><info>Info: <!--{$aEvenement.infoContact.first_name}--> <!--{$aEvenement.infoContact.last_name}-->, <!--{$aEvenement.infoContact.tel}-->#*</info></annonce>				
	<!--{else}-->
		<annonce id="<!--{$aEvenement.id_evenement}-->"><objet><!--{$aEvenement.nom}-->#*</objet><date><!--{$aEvenement.dateDebutJourSemaineDateMoisHeureHumaine}-->.</date>#*<location><!--{$aEvenement.lieuEvenement.nom}--></location><locality>, <!--{$aEvenement.lieuEvenement.commune}-->.</locality>#*<description><!--{$aEvenement.description}-->#*</description><info>Info: <!--{$aEvenement.infoContact.first_name}--> <!--{$aEvenement.infoContact.last_name}-->, <!--{$aEvenement.infoContact.tel}-->#*</info></annonce>				
	<!--{/if}-->
<!--{/if}-->
<!--{/foreach}-->
</Mémo>
<Home>
<!--{foreach from=$evenements key=key item=aEvenement}-->
<!--{if $aCalendrier.id_calendrier==$aEvenement.id_calendrier && $aEvenement.type==4}-->
	<annonce id="<!--{$aEvenement.id_evenement}-->"><location><!--{$aEvenement.lieuEvenement.nom}--></location><locality>, <!--{$aEvenement.lieuEvenement.commune}--></locality>#*<description><!--{$aEvenement.description}--></description><date><!--{$aEvenement.dateDebutJourSemaineDateMoisHeureHumaine}-->.</date>#*</annonce>
<!--{/if}-->
<!--{/foreach}-->
</Home>
<Culte>
<!--{foreach from=$evenements key=key item=aEvenement}-->
<!--{if $aCalendrier.id_calendrier==$aEvenement.id_calendrier && $aEvenement.type==5}-->
	<annonce id="<!--{$aEvenement.id_evenement}-->"><date><!--{$aEvenement.dateDebutJourSemaineDateMoisHeureHumaine}--></date>, <location> <!--{$aEvenement.lieuEvenement.nom}--></location><locality>, <!--{$aEvenement.lieuEvenement.commune}--></locality>.<description> <!--{$aEvenement.description}-->#*</description></annonce>
<!--{/if}-->
<!--{/foreach}-->
</Culte>
<Rédaction>
<!--{foreach from=$evenements key=key item=aEvenement}-->
<!--{if $aCalendrier.id_calendrier==$aEvenement.id_calendrier && $aEvenement.type==6}-->
	<annonce id="<!--{$aEvenement.id_evenement}-->"><objet><!--{$aEvenement.nom}-->#*</objet><description><!--{$aEvenement.description}-->#*</description><info><!--{$aEvenement.infoContact.first_name}--> <!--{$aEvenement.infoContact.last_name}-->#*</info><date><!--{$aEvenement.dateDebutJourSemaineDateMoisHeureHumaine}-->.</date>#*<location><!--{$aEvenement.lieuEvenement.nom}--></location><locality>, <!--{$aEvenement.lieuEvenement.commune}--></locality></annonce>
<!--{/if}-->
<!--{/foreach}-->
</Rédaction>

</sec-<!--{$aCalendrier.nomSimplifie}-->>
<!--{/foreach}-->
</root>