
<h1 class="rose barre">Moins de biens, plus de liens... &nbsp;&nbsp;&nbsp;Partageons nos objets...</h1>

<div id="blocPresentation">
	<!--{$contenuPresentation}-->
</div>
<h2 class="vert barre" title="Les objets les plus récemment ajoutés">Objets récemment ajoutés</h2>
<div id="blocObjetsRecents">
	<div id="listeObjet">
		
		<!--{foreach from=$objets key=key item=aObjet}-->	
			<div class="objetCarrousel">
				<h2>
					<a href="//<!--{$server_name}-->/objet/<!--{$aObjet.id_objet}-->-<!--{$aObjet.nomSimplifie}-->.html" title="Voir les détails..."><!--{$aObjet.nom}--></a>
				</h2>
				
				<a href="//<!--{$server_name}-->/objet/<!--{$aObjet.id_objet}-->-<!--{$aObjet.nomSimplifie}-->.html" title="Voir les détails...">
					<img class="ombre" src="http://<!--{$server_name}-->/<!--{$aObjet.image.lienVignette}-->" alt="<!--{$aObjet.image.nom}-->" title="Cliquez pour agrandir" />
				</a>
				<p>
					<span class="blocTags"><!--{$aObjet.listeTags}--></span><br />
					Partagé par <a href="//<!--{$server_name}-->/profile/<!--{$aObjet.proprietaire.id_personne}-->-<!--{$aObjet.proprietaire.surnom}-->.html" title="Voir son profil..."><!--{$aObjet.proprietaire.surnom}--></a>
				</p>
			</div>
		
		<!--{/foreach}-->
		<hr />
	</div>
</div>
<div id="blocNuage">
	<h2 class="violet barre" title="Le nuage de mot-clés ci-dessous, représente les objets à disposition sur ce site...">Pioche dans le nuage pour trouver l'objet de tes rêves...</h2>
	
	<!--{$contenuNuage}-->	
</div>