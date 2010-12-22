<input type="hidden" name="idDetail" value="<!--{$evenement.id_evenement}-->" id="idDetail" />

<h3><!--{$evenement.nom}--></h3>

<p>
	<label>Lieu</label><span><!--{$evenement.lieu}--></span>
</p>
<!--{if $evenement.jour_entier=="true"}-->
<p>
	<label>Date</label> <!--{$evenement.jourDebutHumain}--> <!--{if $evenement.jourDebut != $evenement.jourFin}--> - <!--{$evenement.jourFinHumain}--><!--{/if}-->
</p>
<!--{else}-->
<p>
	<label>Début</label><span><!--{$evenement.dateDebut}--></span>
</p>
<p>
	<label>Fin</label><span><!--{$evenement.dateFin}--></span>
</p>
<!--{/if}-->
<label>Description</label>
<p><!--{$evenement.description}--></p>

<a href="/evenement/<!--{$evenement.id_evenement}-->-<!--{$evenement.nomSimplifie}-->.html?modify" id="modifyEvenement">modifier</a>