<div id="utilisateur">
	<div id="barreOutil">
		<a href="//<!--{$server_name}-->/utilisateur/?new" title="ajouter une personne"><img src="http://<!--{$server_name}-->/utile/img/vcard_add.png" alt="ajouter"/>&nbsp;ajouter</a>
	</div>
	<div id="colonneGroupes">
		<select name="groupeUtilisateur" id="groupeUtilisateur" onchange="" size="30">
			<option value="" <!--{if $groupeSelected==''}-->selected="selected"<!--{/if}-->>Tous</option>
			<!--{foreach from=$groupes key=key item=aGroupe}-->	
				<option value="<!--{$aGroupe}-->" <!--{if $groupeSelected==$aGroupe}-->selected="selected"<!--{/if}-->><!--{$aGroupe}--></option>
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
	<div id="colonneDetailUtilisateur">
		<input type="hidden" name="idPersonne" value="<!--{$personne.id_personne}-->" id="idPersonne"></input>
		
		<p><label>Prenom</label> <input type="text" name="prenom" value="<!--{$personne.prenom}-->" id="inputPrenom"></input></p>
		<p><label>Nom</label> <input type="text" name="nom" value="<!--{$personne.nom}-->" id="inputNom"></input></p>
		<p><label>Surnom</label> <input type="text" name="surnom" value="<!--{$personne.surnom}-->" id="inputSurnom"></input></p>
		<p><label>Naissance</label> <input type="text" name="date_naissance" value="<!--{$personne.date_naissance}-->" id="inputNaissance" class="date-pick"></input></p>
		<p><label>rue</label> <input type="text" name="rue" value="<!--{$personne.rue}-->" id="inputRue"></input></p>
		<p><label>npa</label> <input type="text" name="npa" value="<!--{$personne.npa}-->" id="inputNpa"></input></p>
		<p><label>localité</label> <input type="text" name="lieu" value="<!--{$personne.lieu}-->" id="inputLieu"></input></p>
		<p><label>tel</label> <input type="text" name="tel" value="<!--{$personne.tel}-->" id="inputTel"></input></p>
		<p><label>e-mail</label> <input type="text" name="email" value="<!--{$personne.email}-->" id="inputEmail"></input></p>
		<p><label>Remarque</label> <input type="text" name="description" value="<!--{$personne.description}-->" id="inputDescription"></input></p>
		
		<div id="blocTags">
			<p>
				<label for="tags" title="séparés par des ,">tags</label><input type="text" name="tags" id="tags" value="<!--{$tags}-->" /> <a id="enregistreTag" href="#">enregistrer</a>
			</p>
		</div>
		
		<p>
			<a href="#" id="savePersonne"><img src="http://<!--{$server_name}-->/utile/img/save.png" alt="save" /> enregistrer</a>  |  
			<a href="//<!--{$server_name}-->/utilisateur/<!--{$personne.id_personne}-->-<!--{$personne.nomSimplifie}-->.html" id="cancelPersonne" >annuler</a>
		</p>
	</div>
	<hr />
	
	<div id="logAction">
	</div>
	
</div>
