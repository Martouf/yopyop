<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<id>tag:<!--{$server_name}-->,<!--{$smarty.now|date_format:'%Y-%m-%d'}-->:<!--{$file_name}--></id>
	<title>Commentaires sur <!--{$server_name}--></title>
	<updated><!--{$smarty.now|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
	<link rel="alternate" type="text/html" href="<!--{$alternateUrl}-->"/>
	<author>
		<name>Martouf</name>
	</author>
	<!--{foreach from=$commentaires key=key item=aCommentaire}-->
	<entry>
		<id>tag:<!--{$server_name}-->,<!--{$aCommentaire.date_creation|date_format:'%Y-%m-%d'}-->:commentaire-<!--{$aCommentaire.id_commentaire}--></id>
		<published><!--{$aCommentaire.date_creation|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</published>
		<updated><!--{$aCommentaire.date_modification|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
		<title type="text"><!--{$aCommentaire.dateCreation}-->: <!--{$aCommentaire.nom}--></title>
		<link rel="alternate" type="text/html" href="http://<!--{$server_name}-->/<!--{$aCommentaire.type_element_commente}-->/<!--{$aCommentaire.id_element_commente}-->-.html" title="<!--{$aCommentaire.nom}-->"/>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/bigbang.css" media="all" />
					
					<div id="commentaire_<!--{$aCommentaire.id_commentaire}-->" class="commentaire">
						<div class="commentaire_texte">
							<p>
								<!--{$aCommentaire.description}-->
							</p>
						</div>
						<div class="commentaire_infos">
							<!--{$aCommentaire.nom}--> le <!--{$aCommentaire.dateCreation}--><img src="http://<!--{$server_name}-->/utile/img/bulle.gif" alt="bulle" />
						</div>
					</div>
			</div>
		</content>
		<author>
			<name><!--{$aCommentaire.nom}--></name>
		</author>
	</entry>
	<!--{/foreach}-->
</feed>