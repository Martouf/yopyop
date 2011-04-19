<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<id>tag:<!--{$server_name}-->,<!--{$smarty.now|date_format:'%Y-%m-%d'}-->:<!--{$file_name}--></id>
	<title>Mesure: <!--{$file_name}--></title>
	<updated><!--{$smarty.now|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
	<link rel="alternate" type="text/html" href="<!--{$alternateUrl}-->"/>
	<author>
		<name>Martouf</name>
	</author>
	<!--{foreach from=$mesures key=key item=aMesure}-->
	<entry>
		<id>tag:<!--{$server_name}-->,<!--{$aMesure.date_creation|date_format:'%Y-%m-%d'}-->:mesure-<!--{$aMesure.id_mesure}--></id>
		<published><!--{$aMesure.date_publication|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</published>
		<updated><!--{$aMesure.date_modification|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
		<!--{foreach from=$aMesure.tags key=tag item=occurence}-->
		<category term="<!--{$tag}-->" scheme="http://<!--{$server_name}-->/mesure/<!--{$tag}-->/" />
		<!--{/foreach}-->
		<title type="text"><!--{$aMesure.nom}--></title>
		<summary type="html"><![CDATA[<!--{$aMesure.description}-->]]></summary>
		<link rel="alternate" type="text/html" href="http://<!--{$server_name}-->/mesure/<!--{$aMesure.id_mesure}-->-<!--{$aMesure.nomSimplifie}-->.html" title="<!--{$aMesure.nom}-->"/>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/bigbang.css" media="all" />
					
					<dl id="mesure_<!--{$mesures[key].id_mesure}-->" >
						<dt>Nom</dt>
						<dd><!--{$aMesure.nom}--></dd>
						<dt>Valeur</dt>
						<dd><!--{$aMesure.valeur}--></dd>
						<dt>Type</dt>
						<dd><!--{$aMesure.type}--></dd>
						<dt>Lieu</dt>
						<dd><!--{$aMesure.nom_lieu}--></dd>
						<dt>Date de la mesure</dt>
						<dd><!--{$aMesure.date_mesure}--></dd>					
					</dl>
			</div>
		</content>
		<value>
			<!--{$aMesure.valeur}-->
		</value>
		<author>
			<name><!--{$aMesure.nom_lieu}--></name>
		</author>
	</entry>
	<!--{/foreach}-->
</feed>