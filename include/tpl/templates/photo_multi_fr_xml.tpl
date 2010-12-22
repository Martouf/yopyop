<?xml version="1.0" encoding="utf-8"?>
<feed xmlns="http://www.w3.org/2005/Atom">
	<id>tag:<!--{$server_name}-->,<!--{$smarty.now|date_format:'%Y-%m-%d'}-->:<!--{$file_name}--></id>
	<title><!--{$file_name}--></title>
	<updated><!--{$smarty.now|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
	<link rel="alternate" type="text/html" href="http://<!--{$server_name}-->"/>
	<author>
		<name>Martouf</name>
	</author>
	<!--{foreach from=$photos key=key item=aPhoto}-->
	<entry>
		<id>tag:<!--{$server_name}-->,<!--{$aPhoto.date_creation|date_format:'%Y-%m-%d'}-->:photo-<!--{$aPhoto.id_photo}--></id>
		<published><!--{$aPhoto.date_creation|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</published>
		<updated><!--{$aPhoto.date_modification|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
		<title type="text"><!--{$aPhoto.listeTags}--></title>
		<link rel="alternate" type="text/html" href="<!--{$alternateUrl}-->" title="<!--{$aPhoto.listeTags}-->"/>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/bigbang.css" media="all" />
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/regal.css" media="all" />
					
					<div class="cadre_vignette">
						<div class="<!--{if $aPhoto.orientation == 'v' }-->vignette_verticale<!--{else}-->vignette_horizontale<!--{/if}-->">
							<div class="ombre">
								<a href="<!--{if $aPhoto.externe == '0' }-->http://<!--{$server_name}-->/<!--{/if}--><!--{$aPhoto.lienMoyenne}-->" title ="<!--{$aPhoto.listeTags}-->" rel="lightbox[album]">
									<img src="<!--{if $aPhoto.externe == '0' }-->http://<!--{$server_name}-->/<!--{/if}--><!--{$aPhoto.lienVignette}-->" alt="<!--{$aPhoto.listeTags}-->" title="Cliquez pour agrandir" />
								</a>
							</div>
						</div>
						<a href="http://<!--{$server_name}-->/photo/<!--{$aPhoto.id_photo}-->-<!--{$aPhoto.nom}-->.html" target="_blank" >
							<img src="http://<!--{$server_name}-->/utile/img/plus.png" alt="plus" title="ajouter un commentaire pour cette photo..." />
						</a>
					</div>
			</div>
		</content>
		<author>
			<name>Martouf</name>
		</author>
	</entry>
	<!--{/foreach}-->
</feed>