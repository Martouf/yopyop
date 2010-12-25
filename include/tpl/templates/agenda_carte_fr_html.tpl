
<!--{include file="barre_navigation_public_fr.tpl"}-->	

	<hr />

<div id="blocCalendrier">
	<div id="map" style="width: 700px; height: 500px"></div>
	<a href="#" id="toto">charger les données</a>

	<hr />
	<ul id="listeCalendrier">
		<!--{foreach from=$calendriers key=key item=aCalendrier}-->	
		<li>
			<span style="background-color:#<!--{$aCalendrier.couleur}-->">
				&nbsp;&nbsp;
			</span>
			<a href="//<!--{$server_name}-->/agenda/<!--{$aCalendrier.id_calendrier}-->-<!--{$aCalendrier.nomSimplifie}-->.html"><!--{$aCalendrier.nom}--></a>
		</li>
		<!--{/foreach}-->
	</ul>
</div>

<div id="masque">
&nbsp;
</div>
<div id="boiteDialogue">
	<img src="http://<!--{$server_name}-->/utile/img/loading.gif" alt="loading"/> loading
</div>