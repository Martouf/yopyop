<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<id>tag:<!--{$server_name}-->,<!--{$smarty.now|date_format:'%Y-%m-%d'}-->:<!--{$file_name}--></id>
	<title><!--{$file_name}--></title>
	<updated><!--{$smarty.now|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
	<link rel="alternate" type="text/html" href="http://<!--{$server_name}-->"/>
	<author>
		<name>Martouf</name>
	</author>
	<!--{foreach from=$documents key=key item=aDocument}-->
	<entry>
		<id>tag:<!--{$server_name}-->,<!--{$aDocument.date_creation|date_format:'%Y-%m-%d'}-->:document-<!--{$aDocument.id_document}--></id>
		<published><!--{$aDocument.date_creation|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</published>
		<updated><!--{$aDocument.date_modification|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
		<title type="text"><!--{$aDocument.nom}--></title>
		<link rel="alternate" type="text/html" href="<!--{$alternateUrl}-->" title="<!--{$aDocument.nom}-->"/>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/bigbang.css" media="all" />
					
					<div id="document_<!--{$documents[key].id_document}-->" >				

						<div id="blocContenu">
							<!--{$aDocument.contenu}-->
						</div>

						<div id="blocMetaDonnees">
							<p>
								<!--{$aDocument.pseudoAuteur}-->: <!--{$aDocument.dateModification}-->
							</p>
						</div>

					</div>
			</div>
		</content>
		<author>
			<name><!--{$aDocument.pseudoAuteur}--></name>
		</author>
	</entry>
	<!--{/foreach}-->
</feed>