<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<id>tag:<!--{$server_name}-->,<!--{$smarty.now|date_format:'%Y-%m-%d'}-->:<!--{$file_name}--></id>
	<title><!--{$file_name}--></title>
	<updated><!--{$smarty.now|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
	<link rel="alternate" type="text/html" href="http://<!--{$server_name}-->"/>
	<author>
		<name>Martouf</name>
	</author>
	<!--{foreach from=$personnes key=key item=aPersonne}-->
	<entry>
		<id>tag:<!--{$server_name}-->,<!--{$aPersonne.date_creation|date_format:'%Y-%m-%d'}-->:personne-<!--{$aPersonne.id_personne}--></id>
		<published><!--{$aPersonne.date_creation|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</published>
		<updated><!--{$aPersonne.date_modification|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
		<title type="text"><!--{$aPersonne.prenom}--> <!--{$aPersonne.nom}--></title>
		<link rel="alternate" type="text/html" href="<!--{$alternateUrl}-->" title="<!--{$aPersonne.prenom}--> <!--{$aPersonne.nom}-->"/>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/bigbang.css" media="all" />
					<input type="hidden" name="idPersonne" value="<!--{$aPersonne.id_personne}-->" id="idPersonne" />
					<p><label>Prenom</label> <!--{$aPersonne.prenom}--></p>
					<p><label>Nom</label> <!--{$aPersonne.nom}--></p>
					<p><label>Surnom</label> <!--{$aPersonne.surnom}--></p>
					<p><label>Naissance</label> <!--{$aPersonne.date_naissance}--></p>
					<p><label>rue</label> <!--{$aPersonne.rue}--></p>
					<p><label>npa</label> <!--{$aPersonne.npa}--></p>
					<p><label>lieu</label> <!--{$aPersonne.lieu}--></p>
					<p><label>tel</label> <!--{$aPersonne.tel}--></p>
					<p><label>e-mail</label> <!--{$aPersonne.email}--></p>
					<p><label>Remarque</label> <!--{$aPersonne.description}--></p>
			</div>
		</content>
		<author>
			<name>Martouf</name>
		</author>
	</entry>
	<!--{/foreach}-->
</feed>