<table id="listeObjets" class="tablesorter" cellpadding="0" cellspacing="0" >
	<thead>
		<tr>
			<th>Etat</th>
			<th>Photo</th>
			<th>Nom</th>
			<th>prix</th>
			<th>tags</th>
			<th>Propriétaire</th>
			<th>calendrier</th>
		</tr>
	</thead>
	
	<!--{foreach from=$objets key=key item=aObjet}-->	
		<tr>
			<td>
				<p>
					<!--{if $aObjet.etat==0}-->
						<img src="http://<!--{$server_name}-->/utile/img/bullet_blue.png" alt="en création" title="en cours de création" />
					<!--{elseif $aObjet.etat==1}-->
						<img src="http://<!--{$server_name}-->/utile/img/bullet_green.png" alt="disponible" title="disponible" />
					<!--{else}-->
						<img src="http://<!--{$server_name}-->/utile/img/bullet_red.png" alt="privé" title="privé" />
					<!--{/if}-->
				</p>
			</td>
			<td>
				<a href="http://<!--{$server_name}-->/<!--{$aObjet.image.lienMoyenne}-->" title ="<!--{$aObjet.image.nom}-->" rel="shadowbox[album];options={animate:false,continuous:true}">
					<img src="http://<!--{$server_name}-->/<!--{$aObjet.image.lienVignette}-->" alt="<!--{$aObjet.image.nom}-->" title="Cliquez pour agrandir" />
				</a>
			</td>
			<td>
				<a href="//<!--{$server_name}-->/objet/<!--{$aObjet.id_objet}-->-<!--{$aObjet.nomSimplifie}-->.html" title="détails..."><!--{$aObjet.nom}--></a>
			</td>
			<td>
				<!--{$aObjet.prix}-->
			</td>
			<td>
				<!--{$aObjet.listeTags}-->
			</td>
			<td>
				<!--{$aObjet.proprietaire.surnom}-->
			</td>
			<td>
				<a title="voir le calendrier des réservations dans une nouvelle fenêtre..." target="blank" href="//<!--{$server_name}-->/calendrier/<!--{$aObjet.id_calendrier}-->-reservations-<!--{$aObjet.nomSimplifie}-->.html">voir le calendrier</a>
			</td>		

		</tr>
		<!--{/foreach}-->
</table>