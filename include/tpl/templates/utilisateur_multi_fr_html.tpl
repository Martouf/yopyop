<div id="utilisateur">
	<div id="barreOutil">
		<a href="/utilisateur/?new" title="ajouter une personne"><img src="/utile/img/vcard_add.png" alt="ajouter"/>&nbsp;ajouter</a>
	</div>
	<div id="colonneGroupes">
		<select name="groupeUtilisateur" id="groupeUtilisateur" onchange="" size="30">
			<option value=""<!--{if $groupeSelected==''}-->selected="selected"<!--{/if}--> >Tous</option>
			<!--{foreach from=$groupes key=key item=aGroupe}-->	
				<option value="<!--{$aGroupe}-->" <!--{if $groupeSelected==$aGroupe}-->selected="selected"<!--{/if}--> ><!--{$aGroupe}--></option>
			<!--{/foreach}-->
		</select>
	</div>
	<div id="colonneListeUtilisateurs">
		<select name="listeUtilisateurs" id="listeUtilisateurs" onchange="" size="30">
			<!--{foreach from=$personnes key=key item=aPersonne}-->	
				<option value="<!--{$aPersonne.id_personne}-->" <!--{if $personneSelected==$aPersonne.id_personne}-->selected="selected"<!--{/if}--> ><!--{$aPersonne.prenom}-->&nbsp;<!--{$aPersonne.nom}--></option>
			<!--{/foreach}-->
		</select>
	</div>
	<div id="colonneDetailUtilisateur">
		
	</div>
	<hr />
</div>
