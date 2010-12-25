	<div id="utilisateur">
		<div id="barreOutil">
			<a href="//<!--{$server_name}-->/utilisateur/?new" title="ajouter une personne"><img src="http://<!--{$server_name}-->/utile/img/vcard_add.png" alt="ajouter"/>&nbsp;ajouter</a>
		</div>
		<div id="colonneGroupes">
			<select name="groupeUtilisateur" id="groupeUtilisateur" onchange="" size="30">
				<option value="" <!--{if $groupeSelected==''}-->selected="selected"<!--{/if}--> >Tous</option>
				<!--{foreach from=$groupes key=key item=aGroupe}-->	
					<option value="<!--{$aGroupe}-->" <!--{if $groupeSelected==$aGroupe}-->selected="selected"<!--{/if}--> ><!--{$aGroupe}--></option>
				<!--{/foreach}-->
			</select>
		</div>
		<div id="colonneListeUtilisateurs">
			<select name="listeUtilisateurs" id="listeUtilisateurs" onchange="" size="30">
				<!--{foreach from=$personnes key=key item=aPersonne}-->	
					<option value="<!--{$aPersonne.id_personne}-->" <!--{if $personneSelected==$aPersonne.id_personne}-->selected="selected"<!--{/if}-->><!--{$aPersonne.prenom}-->&nbsp;<!--{$aPersonne.nom}--></option>
				<!--{/foreach}-->
			</select>
		</div>
		<div id="colonneDetailUtilisateur" <!--{if $utilisateurConnu}--> ondblclick="document.location='<!--{$personne.id_personne}-->-<!--{$personne.nomSimplifie}-->.html?modify';"<!--{/if}--> >
			<input type="hidden" name="idPersonne" value="<!--{$personne.id_personne}-->" id="idPersonne" />
			<h2><!--{$personne.prenom}-->&nbsp;<!--{$personne.nom}--></h2>
			<p><label>Surnom</label> <!--{$personne.surnom}--></p>
			<p><label>Naissance</label> <!--{$personne.date_naissance}--></p>
			<p><label>rue</label> <!--{$personne.rue}--></p>
			<p><label>npa</label> <!--{$personne.npa}--></p>
			<p><label>lieu</label> <!--{$personne.lieu}--></p>
			<p><label>tel</label> <!--{$personne.tel}--></p>
			<p><label>e-mail</label> <!--{$personne.email}--></p>
			<p><label>Remarque</label> <!--{$personne.description}--></p>
			
			<div id="barreEtatPersonne">
				<!--{if $utilisateurConnu}--><a href="//<!--{$server_name}-->/utilisateur/<!--{$personne.id_personne}-->-<!--{$personne.nomSimplifie}-->.html?modify" title="modifier les données de la personne"><img src="http://<!--{$server_name}-->/utile/img/vcard_edit.png" alt="editer"/>&nbsp;modifier</a><!--{/if}-->
			</div>
		</div>
		<hr />
	</div>
