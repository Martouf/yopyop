<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<id>tag:<!--{$server_name}-->,<!--{$smarty.now|date_format:'%Y-%m-%d'}-->:<!--{$file_name}--></id>
	<title><!--{$file_name}--></title>
	<updated><!--{$smarty.now|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
	<link rel="alternate" type="text/html" href="http://<!--{$server_name}-->"/>
	<author>
		<name>Martouf</name>
	</author>
	<!--{foreach from=$evenements key=key item=aEvenement}-->
	<entry>
		<id>tag:<!--{$server_name}-->,<!--{$aEvenement.date_creation|date_format:'%Y-%m-%d'}-->:evenement-<!--{$aEvenement.id_evenement}--></id>
		<published><!--{$aEvenement.date_creation|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</published>
		<updated><!--{$aEvenement.date_modification|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
		<title type="text"><!--{$aEvenement.nom}--></title>
		<link rel="alternate" type="text/html" href="<!--{$alternateUrl}-->" title="<!--{$aEvenement.nom}-->"/>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/bigbang.css" media="all" />
					
					<div id="evenement_<!--{$aEvenements[key].id_evenement}-->" >				

						<h3><!--{$aEvenement.nom}--></h3>

						<p>
							<label>Lieu</label> <span><!--{$aEvenement.lieu}--></span>
						</p>
						<!--{if $aEvenement.jour_entier=="true"}-->
						<p>
							<label>Date</label> <!--{$aEvenement.dateDebutComplete}--> <!--{if $aEvenement.dateDebut != $aEvenement.dateFin}--> - <!--{$aEvenement.dateFinComplete}--><!--{/if}-->
						</p>
						<!--{else}-->
						<p>
							<label>Début</label><span><!--{$aEvenement.dateDebutComplete}--></span>
						</p>
						<p>
							<label>Fin</label><span><!--{$aEvenement.dateFinComplete}--></span>
						</p>
						<!--{/if}-->
						<label>Description</label>
						<p><!--{$aEvenement.description}--></p>

					</div>
			</div>
		</content>
		<author>
			<name>yopyop</name>
		</author>
	</entry>
	<!--{/foreach}-->
</feed>