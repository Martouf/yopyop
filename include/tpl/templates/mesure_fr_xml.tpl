<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<id>tag:<!--{$server_name}-->,<!--{$smarty.now|date_format:'%Y-%m-%d'}-->:<!--{$mesure.nomSimplifie}--></id>
	<title>Mesure: <!--{$mesure.nomSimplifie}--></title>
	<updated><!--{$smarty.now|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
	<link rel="alternate" type="text/html" href="http://<!--{$server_name}-->/mesure/<!--{$mesure.id_mesure}-->-<!--{$mesure.nomSimplifie}-->.hml"/>
	<link rel="self" type="text/html" href="http://<!--{$server_name}-->/mesure/<!--{$mesure.id_mesure}-->-<!--{$mesure.nomSimplifie}-->.xml"/>
	<author>
		<name>Martouf</name>
	</author>
	<entry>
		<id>tag:<!--{$server_name}-->,<!--{$mesure.date_creation|date_format:'%Y-%m-%d'}-->:mesure-<!--{$mesure.id_mesure}--></id>
		<published><!--{$mesure.date_creation|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</published>
		<updated><!--{$mesure.date_modification|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
		<title type="text"><!--{$mesure.nom}--></title>
		<link rel="alternate" type="text/html" href="<!--{$alternateUrl}-->" title="<!--{$mesure.nom}-->"/>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/bigbang.css" media="all" />
				<div id="mesure" >
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
				
			</div>
		</content>
		<author>
			<name><!--{$mesure.nom_lieu}--></name>
		</author>
	</entry>
</feed>