<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<id>tag:<!--{$server_name}-->,<!--{$smarty.now|date_format:'%Y-%m-%d'}-->:<!--{$file_name}--></id>
	<title><!--{$file_name}--></title>
	<updated><!--{$smarty.now|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
	<link rel="alternate" type="text/html" href="http://<!--{$server_name}-->"/>
	<author>
		<name>Martouf</name>
	</author>
	<entry>
		<id>tag:<!--{$server_name}-->,<!--{$statut.date_creation|date_format:'%Y-%m-%d'}-->:statut-<!--{$statut.id_statut}--></id>
		<published><!--{$statut.date_creation|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</published>
		<updated><!--{$statut.date_modification|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
		<title type="text"><!--{$statut.nom}--></title>
		<link rel="alternate" type="text/html" href="<!--{$alternateUrl}-->" title="<!--{$statut.nom}-->"/>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/bigbang.css" media="all" />
				<div id="statut" >
					<input type="hidden" name="idStatut" value="<!--{$statut.id_statut}-->" id="idStatut" />
					<p> <!--{$statut.pseudo}--> <!--{$statut.nom}--></p>
					<p class="dateStatut">
						<!--{$statut.dateModification}-->
					</p>
					
				</div>
			</div>
		</content>
		<author>
			<name>Martouf</name>
		</author>
	</entry>
</feed>