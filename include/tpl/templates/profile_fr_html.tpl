<div id="infosPersonne">
	<!--{if $droitModification}-->
	<div id="modificationProfile">
		<a href="//<!--{$server_name}-->/personne/<!--{$personne.id_personne}-->-<!--{$personne.nomSimplifie}-->.html?modify" title="modifier les données du profile"><img src="http://<!--{$server_name}-->/utile/img/vcard_edit.png" alt="editer"/>&nbsp;modifier</a><!--{/if}-->
	</div>
	<div id="blocAvatar">
		<!--{if !empty($personne.email)}-->
			<img alt="gravatar <!--{$personne.nomSimplifie}-->" class="avatarProfile" src="http://www.gravatar.com/avatar/<!--{$personne.gravatar}-->.jpg?default=identicon" />
		<!--{/if}-->
	</div>
	<h1 id="blocpseudoPersonne"><!--{$personne.surnom}--></h1>
	<div id="blocFinance">
		<!--{if $droitModification}-->
			<p><label>Fortune</label> <strong><!--{$personne.fortune}--></strong> kong</p>
		<!--{else}-->	
			<p>
				<a href="//<!--{$server_name}-->/transaction/?new&amp;for=<!--{$personne.id_personne}-->">lui donner des kong...</a>
			</p>
		<!--{/if}-->
	</div>
	<!--{if $utilisateurConnu}-->
		<div id="blocPedigre">
			<p>
				<!--{$personne.dateNaissance}-->, <!--{$personne.lieu}-->
				<!--{if !empty($personne.url)}--><p><a href="<!--{$personne.url}-->"><!--{$personne.url}--></a></p><!--{/if}-->
			</p>
		</div>
		<div id="blocTags">
			<!--{if !empty($tags)}-->
			<p>
				<label>Groupes</label> <!--{$tags}-->
			</p>
			<!--{/if}-->
		</div>
	<!--{/if}-->
</div>

<div id="fluxActu">
	<h2 class="barre jaune">Actualité</h2>
	<p>albert à réservé une table de jardin...</p>
	
	<table id="listeNotifications" class="tablesorter" cellpadding="0" cellspacing="0" >
		<thead>
			<tr>
				<th>Nom</th>
				<th>description</th>
				<th>type</th>
				<th>etat</th>
				<th>date</th>
			</tr>
		</thead>
		<tbody>
			<!--{foreach from=$notifications key=key item=aNotification}-->	
			<tr>
				<td><!--{$aNotification.nom}--></td>
				<td><!--{$aNotification.description}--></td>
				<td><!--{$aNotification.type}--></td>
				<td><!--{$aNotification.etat}--></td>
				<td><!--{$aNotification.dateCreation}--></td>					
			</tr>
			<!--{/foreach}-->
		</tbody>
	</table>
	
</div>

<div id="mesObjets">
	<!--{if $droitModification}-->
		<h2 class="rose barre">Mes objets</h2>
	<!--{else}-->
		<h2 class="rose barre">Les objets de <!--{$personne.surnom}--></h2>
	<!--{/if}-->
	<div id="listeObjet">
		
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
					<a href="//<!--{$server_name}-->/objet/<!--{$aObjet.id_objet}-->-<!--{$aObjet.nomSimplifie}-->.html" title="Voir les détails..."><!--{$aObjet.nom}-->...</a>
				</h2>
				
				<a href="http://<!--{$server_name}-->/<!--{$aObjet.image.lienMoyenne}-->" title ="<!--{$aObjet.image.nom}-->" rel="shadowbox[album];options={animate:false,continuous:true}">
					<img class="ombre" src="http://<!--{$server_name}-->/<!--{$aObjet.image.lienVignette}-->" alt="<!--{$aObjet.image.nom}-->" title="Cliquez pour agrandir" />
				</a>
				<p>
					<!--{$aObjet.prix}--> Kong/jour <br />
					<span class="blocTags"><!--{$aObjet.listeTags}--></span>
				</p>
			</div>
		
		<!--{/foreach}-->
		
	</div>
</div>