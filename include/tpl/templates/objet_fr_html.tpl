
<h1 class="barre vert" id="nomObjet">
	<!--{if $objet.etat=='1'}-->
	<img src="//<!--{$server_name}-->/utile/img/bullet_green.png" alt="disponible" title="disponible" />
	<!--{elseif $objet.etat=='2'}-->
	<img src="//<!--{$server_name}-->/utile/img/bullet_red.png" alt="privé" title="privé" />
	<!--{elseif $objet.etat=='0'}-->
	<img src="//<!--{$server_name}-->/utile/img/bullet_blue.png" alt="en création" title="en cours de création" />
	<!--{/if}-->
	<!--{$objet.nom}-->
	<span id="outilsModif">
		<!--{if $utilisateurConnu}--> <a href="<!--{$objet.id_objet}-->-<!--{$objet.nomSimplifie}-->.html?modify" title="modifier les données de l'objet..."><img src="http://<!--{$server_name}-->/utile/img/edit.gif" /></a><!--{/if}-->
	</span>
</h1>
<div id="blocImagePresentation">
	<a href="http://<!--{$server_name}-->/<!--{$imagePresentation.lienMoyenne}-->" title ="<!--{$imagePresentation.nom}-->" rel="shadowbox[album];options={animate:false,continuous:true}">
		<img class="ombre" src="http://<!--{$server_name}-->/<!--{$imagePresentation.lienVignette}-->" alt="<!--{$imagePresentation.nom}-->" title="Cliquez pour agrandir" />
	</a>
</div>
<div id="blocTags" class="blocTags">
	<p>
		<!--{$objet.listeTags}-->
	</p>
</div>

<p id="infosDetailObjet">
	<label for="prix">Prix</label> 
	<span class="valeur"><!--{$objet.prix}--> kong/jour </span><br />
	<label for="caution">Caution</label>
	<span class="valeur"><!--{$objet.caution}--></span><br />
	<label for="caution">Propriétaire</label>
	<span class="valeur"><a href="//<!--{$server_name}-->/profile/<!--{$objet.proprietaire.id_personne}-->-<!--{$objet.proprietaire.surnom}-->.html"><!--{$objet.proprietaire.surnom}--></a></span><br />
	<label for="caution">Disponibilités</label>
	<span class="valeur"><a title="voir le calendrier des réservations dans une nouvelle fenêtre..." target="blank" href="//<!--{$server_name}-->/calendrier/<!--{$objet.id_calendrier}-->-reservations-<!--{$objet.nomSimplifie}-->.html">Voir le calendrier des réservations...</a>
	</span><br />
</p>
<span class="jaune barre">
	<a href="//<!--{$server_name}-->/reservation/?new&id_objet=<!--{$objet.id_objet}-->" title="réserver cet objet...">Réserver cet objet...</a>
</span>
<p id="descriptionObjet">
	<!--{$objet.description}-->
</p>

<p>
	<label for="lieu">Lieu où se trouve l'objet</label>
	<!--{$objet.lieu}-->
</p>
<div id="map" style="width: 400px; height: 300px"></div>
<input type="hidden" name="idObjet" value="<!--{$objet.id_objet}-->" id="idObjet" />
<!--{if $objet.latitude!=''}-->
<input type="hidden" name="latitude" value="<!--{$objet.latitude}-->" id="objetLatitude"></input>
<input type="hidden" name="longitude" value="<!--{$objet.longitude}-->" id="objetLongitude"></input>
<!--{/if}-->