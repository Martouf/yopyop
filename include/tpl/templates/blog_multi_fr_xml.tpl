<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<id>tag:<!--{$server_name}-->,<!--{$smarty.now|date_format:'%Y-%m-%d'}-->:<!--{$file_name}--></id>
	<title>Blog de Martouf</title>
	<updated><!--{$smarty.now|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
	<link rel="alternate" type="text/html" href="<!--{$alternateUrl}-->"/>
	<author>
		<name>Martouf</name>
	</author>
	<!--{foreach from=$documents key=key item=aDocument}-->
	<entry>
		<id>tag:<!--{$server_name}-->,<!--{$aDocument.date_creation|date_format:'%Y-%m-%d'}-->:document-<!--{$aDocument.id_document}--></id>
		<published><!--{$aDocument.date_publication|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</published>
		<updated><!--{$aDocument.date_modification|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
		<!--{foreach from=$aDocument.tags key=tag item=occurence}-->
		<category term="<!--{$tag}-->" scheme="http://<!--{$server_name}-->/document/<!--{$tag}-->/" />
		<!--{/foreach}-->
		<title type="text"><!--{$aDocument.nom}--></title>
		<summary type="html"><![CDATA[<!--{$aDocument.description}-->]]></summary>
		<link rel="alternate" type="text/html" href="http://<!--{$server_name}-->/blog/<!--{$aDocument.id_document}-->-<!--{$aDocument.nomSimplifie}-->.html" title="<!--{$aDocument.nom}-->"/>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/bigbang.css" media="all" />
					
					<div id="document_<!--{$documents[key].id_document}-->" >				

						<div id="blocContenu">
							<!--{$aDocument.contenu}-->
						</div>

						<div id="blocMetaDonnees">
							<p>
								<!--{$aDocument.pseudoAuteur}-->: <!--{$aDocument.date_modification}-->
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