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
		<id>tag:<!--{$server_name}-->,<!--{$photo.date_creation|date_format:'%Y-%m-%d'}-->:photo-<!--{$photo.id_photo}--></id>
		<published><!--{$photo.date_creation|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</published>
		<updated><!--{$photo.date_modification|date_format:'%Y-%m-%dT%H:%M:%S'}-->.000+01:00</updated>
		<title type="text"><!--{$photo.listeTags}--></title>
		<link rel="alternate" type="text/html" href="<!--{$alternateUrl}-->" title="<!--{$photo.prenom}--> <!--{$photo.nom}-->"/>
		<content type="xhtml">
			<div xmlns="http://www.w3.org/1999/xhtml">
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/bigbang.css" media="all" />
				<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/regal.css" media="all" />
				
				
				<div id="bloc_moyenne">
					<div class="cadre_moyenne">
						<div class="<!--{if $photo.orientation == 'v' }-->moyenne_verticale<!--{else}-->moyenne_horizontale<!--{/if}-->">
							<div class="ombre">
								<a href="<!--{if $photo.externe != '1' }-->http://<!--{$server_name}-->/<!--{/if}--><!--{$photo.lien}-->" title="Cliquez pour agrandir">
									<img src="<!--{if $photo.externe != '1' }-->http://<!--{$server_name}-->/<!--{/if}--><!--{$photo.lienMoyenne}-->" alt="<!--{$photo.listeTags}-->" title="cliquez pour agrandir" />
								</a>
							</div>
						</div>
						<hr />
					</div>
				</div>
				
			</div>
		</content>
		<author>
			<name>Martouf</name>
		</author>
	</entry>
</feed>