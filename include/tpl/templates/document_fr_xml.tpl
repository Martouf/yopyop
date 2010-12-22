<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<id>tag:<!--{$server_name}-->,<!--{$smarty.now|date_format:'%Y-%m-%d'}-->:<!--{$document.nomSimplifie}--></id>
	<title><!--{$file_name}--></title>
	<updated><!--{$smarty.now|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
	<link rel="alternate" type="text/html" href="http://<!--{$server_name}-->/document/<!--{$document.id_document}-->-<!--{$document.nomSimplifie}-->.hml"/>
	<link rel="self" type="text/html" href="http://<!--{$server_name}-->/document/<!--{$document.id_document}-->-<!--{$document.nomSimplifie}-->.xml"/>
	<author>
		<name>Martouf</name>
	</author>
	<entry>
		<id>tag:<!--{$server_name}-->,<!--{$document.date_creation|date_format:'%Y-%m-%d'}-->:document-<!--{$document.id_document}--></id>
		<published><!--{$document.date_creation|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</published>
		<updated><!--{$document.date_modification|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
		<title type="text"><!--{$document.nom}--></title>
		<link rel="alternate" type="text/html" href="<!--{$alternateUrl}-->" title="<!--{$document.nom}-->"/>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/bigbang.css" media="all" />
				<div id="document" >

					<div id="blocContenu">
						<!--{$document.contenu}-->
					</div>

					<div id="blocMetaDonnees">
						<p>
							<!--{$document.pseudoAuteur}-->: <!--{$document.date_modification}-->
						</p>
					</div>
					<input type="hidden" name="idDocument" value="<!--{$document.id_document}-->" id="idDocument" />
				</div>
				
			</div>
		</content>
		<author>
			<name><!--{$document.pseudoAuteur}--></name>
		</author>
	</entry>
</feed>