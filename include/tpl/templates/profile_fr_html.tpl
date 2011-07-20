<div id="infosPersonne">
	<!--{if $droitModification}-->
	<div id="modificationProfile">
		<a href="//<!--{$server_name}-->/personne/<!--{$personne.id_personne}-->-<!--{$personne.nomSimplifie}-->.html?modify" title="modifier les données du profile"><img src="http://<!--{$server_name}-->/utile/img/vcard_edit.png" alt="editer"/>&nbsp;modifier</a><!--{/if}-->
	</div>
	<div id="blocAvatar">
		<!--{if !empty($personne.email)}-->
			<img alt="gravatar <!--{$personne.nomSimplifie}-->" class="avatarProfile ombre" src="http://www.gravatar.com/avatar/<!--{$personne.gravatar}-->.jpg?default=retro" />
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
	<hr />

<!--{if $droitModification}-->
<div id="fluxActu">
	<h2 class="barre jaune" title="Les 50 dernières actualités...">Actualité</h2>

	<div id="fenetreNotifications">
		<ul id="listeNotifications">
			<!--{foreach from=$notifications key=key item=aNotification}-->
			<li>
				<p>
				<!--{if $aNotification.type==3}-->
				<img src="//<!--{$server_name}-->/utile/img/flag_pink.png" alt="demande de réservation" title="Demande de réservation" />
				<!--{/if}-->
					<!--{$aNotification.description}--> <span class="dateNotification"><em><!--{$aNotification.dateCreation}--></em><span>
				</p>
			</li>
			<!--{/foreach}-->
		</ul>
		<p id="voirPlusNotification">
			<a href="//<!--{$server_name}-->/notification/?userid=<!--{$personne.id_personne}-->">Voir toutes les notifications...</a>
		</p>
	</div>
</div>
<!--{/if}-->

<div id="mesObjets">
	<!--{if $droitModification}-->
		<h2 class="rose barre" title="les 50 premiers objets dont je suis propriétaire">Mes objets</h2>
	<!--{else}-->
		<h2 class="rose barre" title="les 50 premiers objets appartenant à <!--{$personne.surnom}-->">Les objets de <!--{$personne.surnom}--></h2>
	<!--{/if}-->
	<div id="listeObjet">
		<!--{if $droitModification}-->
		<p>
			<a href="//<!--{$server_name}-->/objet/?new"><img src="//<!--{$server_name}-->/utile/img/camera_add.png" alt="ajouter un objet" title="ajouter un objet..." /> Ajouter un objet...</a>
		</p>
		<!--{/if}-->
		
		<!--{foreach from=$objets key=key item=aObjet}-->	
			<div class="objetCarrousel">
				<h2>
					<!--{if $aObjet.etat==0}-->
						<img src="//<!--{$server_name}-->/utile/img/bullet_blue.png" alt="en création" title="en cours de création" />
					<!--{elseif $aObjet.etat==1}-->
						<img src="//<!--{$server_name}-->/utile/img/bullet_green.png" alt="disponible" title="disponible" />
					<!--{else}-->
						<img src="//<!--{$server_name}-->/utile/img/bullet_red.png" alt="privé" title="privé" />
					<!--{/if}-->
					<a href="//<!--{$server_name}-->/objet/<!--{$aObjet.id_objet}-->-<!--{$aObjet.nomSimplifie}-->.html" title="Voir les détails..."><!--{$aObjet.nom}-->...</a>
				</h2>
				
				<a href="//<!--{$server_name}-->/objet/<!--{$aObjet.id_objet}-->-<!--{$aObjet.nomSimplifie}-->.html" title="Voir les détails...">
					<img class="ombre" src="http://<!--{$server_name}-->/<!--{$aObjet.image.lienVignette}-->" alt="<!--{$aObjet.image.nom}-->" title="Cliquez pour agrandir" />
				</a>
				<p>
					<!--{$aObjet.prix}--> Kong/jour <br />
					<span class="blocTags"><!--{$aObjet.listeTags}--></span>
				</p>
			</div>
		
		<!--{/foreach}-->
		<hr />
		<!--{if $droitModification}-->
		<p>
			<a href="//<!--{$server_name}-->/objet/?new"><img src="//<!--{$server_name}-->/utile/img/camera_add.png" alt="ajouter un objet" title="ajouter un objet..." /> Ajouter un objet...</a>
		</p>
		<!--{/if}-->
	</div>
</div>