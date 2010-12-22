<div id="outilsModif">
	<!--{if $utilisateurConnu}--> <a href="<!--{$objet.id_objet}-->-<!--{$objet.nomSimplifie}-->.html?modify" title="modifier les données de l'objet..."><img src="http://<!--{$server_name}-->/utile/img/edit.gif" /></a><!--{/if}-->
</div>

<h1>
	<!--{$objet.nom}-->
</h1>

<div id="blocImagePresentation">
	<a href="http://<!--{$server_name}-->/<!--{$imagePresentation.lienMoyenne}-->" title ="<!--{$imagePresentation.nom}-->" rel="shadowbox[album];options={animate:false,continuous:true}">
		<img src="http://<!--{$server_name}-->/<!--{$imagePresentation.lienVignette}-->" alt="<!--{$imagePresentation.nom}-->" title="Cliquez pour agrandir" />
	</a>
</div>

<p id="descriptionObjet">
	<!--{$objet.description}-->
</p>

<p>
	<label for="prix">prix par jour</label>
	<!--{$objet.prix}-->
</p>
<p>
	<label for="caution">caution</label>
	<!--{$objet.caution}-->
</p>
<p>
	<label for="etat">Etat</label>
	<!--{if $objet.etat=='1'}-->
	sur le marché
	<!--{elseif $objet.etat=='2'}-->
	privé
	<!--{elseif $objet.etat=='0'}-->
	en cours de création
	<!--{/if}-->
</p>

<div id="blocTags">
	<p>
		<label for="tags" title="séparés par des ,">tags</label><!--{$tags}-->
	</p>
</div>

<div id="blocCalendrier">
	<a title="voir le calendrier des réservations dans une nouvelle fenêtre..." target="blank" href="/calendrier/<!--{$objet.id_calendrier}-->-reservations-<!--{$objet.nomSimplifie}-->.html">Voir le calendrier des réservations de l'objet...</a>
</div>
<p>
	<label for="lieu">Lieu où se trouve l'objet</label>
	<!--{$objet.lieu}-->
</p>
<div id="map" style="width: 500px; height: 400px"></div>
<input type="hidden" name="idObjet" value="<!--{$objet.id_objet}-->" id="idObjet" />
<!--{if $objet.latitude!=''}-->
<input type="hidden" name="latitude" value="<!--{$objet.latitude}-->" id="objetLatitude"></input>
<input type="hidden" name="longitude" value="<!--{$objet.longitude}-->" id="objetLongitude"></input>
<!--{/if}-->

<p>
	<a href="/reservation/?new&id_objet=<!--{$objet.id_objet}-->" title="réserver cet objet...">Réserver cet objet</a>
</p>