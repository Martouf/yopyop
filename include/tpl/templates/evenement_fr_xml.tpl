<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<id>tag:<!--{$server_name}-->,<!--{$smarty.now|date_format:'%Y-%m-%d'}-->:<!--{$evenement.nomSimplifie}--></id>
	<title><!--{$file_name}--></title>
	<updated><!--{$smarty.now|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
	<link rel="alternate" type="text/html" href="http://<!--{$server_name}-->/evenement/<!--{$evenement.id_evenement}-->-<!--{$evenement.nomSimplifie}-->.hml"/>
	<link rel="self" type="text/html" href="http://<!--{$server_name}-->/evenement/<!--{$evenement.id_evenement}-->-<!--{$evenement.nomSimplifie}-->.xml"/>
	<author>
		<name>Martouf</name>
	</author>
	<entry>
		<id>tag:<!--{$server_name}-->,<!--{$evenement.date_creation|date_format:'%Y-%m-%d'}-->:evenement-<!--{$evenement.id_evenement}--></id>
		<published><!--{$evenement.date_creation|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</published>
		<updated><!--{$evenement.date_modification|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
		<title type="text"><!--{$evenement.nom}--></title>
		<link rel="alternate" type="text/html" href="<!--{$alternateUrl}-->" title="<!--{$evenement.nom}-->"/>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/bigbang.css" media="all" />
				<div id="evenement" >
						
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

				</div>
				
			</div>
		</content>
		<author>
			<name>yopyop</name>
		</author>
	</entry>
</feed>