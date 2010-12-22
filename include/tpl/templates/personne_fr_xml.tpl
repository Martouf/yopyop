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
		<id>tag:<!--{$server_name}-->,<!--{$personne.date_creation|date_format:'%Y-%m-%d'}-->:personne-<!--{$personne.id_personne}--></id>
		<published><!--{$personne.date_creation|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</published>
		<updated><!--{$personne.date_modification|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
		<title type="text"><!--{$personne.prenom}--> <!--{$personne.nom}--></title>
		<link rel="alternate" type="text/html" href="<!--{$alternateUrl}-->" title="<!--{$personne.prenom}--> <!--{$personne.nom}-->"/>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/bigbang.css" media="all" />
				<div id="personne" >
					<input type="hidden" name="idPersonne" value="<!--{$personne.id_personne}-->" id="idPersonne" />
					<p><label>Prenom</label> <!--{$personne.prenom}--></p>
					<p><label>Nom</label> <!--{$personne.nom}--></p>
					<p><label>Surnom</label> <!--{$personne.surnom}--></p>
					<p><label>Naissance</label> <!--{$personne.date_naissance}--></p>
					<p><label>rue</label> <!--{$personne.rue}--></p>
					<p><label>npa</label> <!--{$personne.npa}--></p>
					<p><label>lieu</label> <!--{$personne.lieu}--></p>
					<p><label>tel</label> <!--{$personne.tel}--></p>
					<p><label>e-mail</label> <!--{$personne.email}--></p>
					<p><label>Remarque</label> <!--{$personne.description}--></p>
				</div>
			</div>
		</content>
		<author>
			<name>Martouf</name>
		</author>
	</entry>
</feed>