<div id="listeObjetGlobale">
	
	<!--{foreach from=$objets key=key item=aObjet}-->	
		<div class="objetCarrousel">
			<h2>
				<!--{if $aObjet.etat==0}-->
					<img src="http://<!--{$server_name}-->/utile/img/bullet_blue.png" alt="en création" title="en cours de création" />
				<!--{elseif $aObjet.etat==1}-->
					<img src="http://<!--{$server_name}-->/utile/img/bullet_green.png" alt="disponible" title="disponible" />
				<!--{else}-->
					<img src="http://<!--{$server_name}-->/utile/img/bullet_red.png" alt="privé" title="privé" />
				<!--{/if}-->
				<a href="//<!--{$server_name}-->/objet/<!--{$aObjet.id_objet}-->-<!--{$aObjet.nomSimplifie}-->.html" title="Voir les détails..."><!--{$aObjet.nom}--></a>
			</h2>
			
			<a href="//<!--{$server_name}-->/objet/<!--{$aObjet.id_objet}-->-<!--{$aObjet.nomSimplifie}-->.html" title="Voir les détails...">
				<img class="ombre" src="http://<!--{$server_name}-->/<!--{$aObjet.image.lienVignette}-->" alt="<!--{$aObjet.image.nom}-->" title="Cliquez pour agrandir" />
			</a>
			<p>
				<span class="blocTags"><!--{$aObjet.listeTags}--></span><br />
				<!--{$aObjet.prix}--> Kong/jour &nbsp;&nbsp;&nbsp;<a title="voir le calendrier des réservations dans une nouvelle fenêtre..." href="//<!--{$server_name}-->/agenda/<!--{$aObjet.id_calendrier}-->-reservations-<!--{$aObjet.nomSimplifie}-->.html">voir le calendrier</a><br />
				Partagé par <a href="//<!--{$server_name}-->/profile/<!--{$aObjet.proprietaire.id_personne}-->-<!--{$aObjet.proprietaire.surnom}-->.html" title="Voir son profil..."><!--{$aObjet.proprietaire.surnom}--></a><br />
			</p>
		</div>
	
	<!--{/foreach}-->
	<hr />
</div>