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
		<div id="blocGroupe">
			<!--{if !empty($tags)}-->
			<p>
				<label>Groupes</label> <!--{$tags}-->
			</p>
			<!--{/if}-->
		</div>
	<!--{/if}-->
</div>

<div id="fluxActu">
	<h2>Actualité</h2>
	<p>albert à réservé une table de jardin...</p>
</div>

<div id="mesObjets">
	<h2>Actualité</h2>
	<div id="listeObjet">
		<div id="objet1">
			vélo..
		</div>
		<div id="objet2">
			drapeau
		</div>
		<div id="objet3">
			four..
		</div>
		<div id="objet4">
			machine à pain
		</div>
	</div>
</div>