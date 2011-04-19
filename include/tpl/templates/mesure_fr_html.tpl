<p>
	<a href="/mesure/" />Voir la liste des mesures</a>
</p>

<div id="mesure">
	<dl id="mesure_<!--{$mesures[key].id_mesure}-->" >
		<dt>Nom</dt>
		<dd><!--{$mesure.nom}--></dd>
		<dt>Valeur</dt>
		<dd><!--{$mesure.valeur}--></dd>
		<dt>Type</dt>
		<dd><!--{$mesure.type}--></dd>
		<dt>Lieu</dt>
		<dd><!--{$mesure.nom_lieu}--></dd>
		<dt>Date de la mesure</dt>
		<dd><!--{$mesure.date_mesure}--></dd>					
	</dl>
	<input type="hidden" name="idMesure" value="<!--{$mesure.id_mesure}-->" id="idMesure" />
</div>

<div id="blocGestionCommentaires">
	<!--{include file="commentaire_multi_fr_html.tpl"}-->
	<!--{include file="commentaire_new_fr.tpl"}-->
</div>