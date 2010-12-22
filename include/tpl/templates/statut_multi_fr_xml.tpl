<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<id>tag:<!--{$server_name}-->,<!--{$smarty.now|date_format:'%Y-%m-%d'}-->:<!--{$file_name}--></id>
	<title><!--{$file_name}--></title>
	<updated><!--{$smarty.now|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
	<link rel="alternate" type="text/html" href="http://<!--{$server_name}-->"/>
	<author>
		<name>Martouf</name>
	</author>
	<!--{foreach from=$statuts key=key item=aStatut}-->
	<entry>
		<id>tag:<!--{$server_name}-->,<!--{$aStatut.date_creation|date_format:'%Y-%m-%d'}-->:statut-<!--{$aStatut.id_statut}--></id>
		<published><!--{$aStatut.date_creation|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</published>
		<updated><!--{$aStatut.date_modification|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
		<title type="text"><!--{$aStatut.pseudo}-->: <!--{$aStatut.nomBrut}--></title>
		<link rel="alternate" type="text/html" href="<!--{$alternateUrl}-->" title="vers la version html"/>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/bigbang.css" media="all" />
					
					<div id="statut_<!--{$aStatut.id_statut}-->" >				

						<p class="corpsStatut"> <strong><!--{$aStatut.pseudo}--></strong> <!--{$aStatut.nom}--></p>
						<p class="dateStatut">
							<!--{$aStatut.dateModification}-->
						</p>

					</div>
			</div>
		</content>
		<author>
			<name><!--{$aStatut.pseudo}--></name>
		</author>
	</entry>
	<!--{/foreach}-->
</feed>