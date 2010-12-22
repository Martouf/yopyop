<div id="listePersonne">	
	<!--{foreach from=$personnes key=key item=aPersonne}-->	
	
			<div id="personne_<!--{$personnes[key].id_personne}-->" >
				<p><label>Prenom</label> <!--{$aPersonne.prenom}--></p>
				<p><label>Nom</label> <!--{$aPersonne.nom}--></p>
				<p><label>Surnom</label> <!--{$aPersonne.surnom}--></p>
				<p><label>Naissance</label> <!--{$aPersonne.date_naissance}--></p>
				<p><label>rue</label> <!--{$aPersonne.rue}--></p>
				<p><label>npa</label> <!--{$aPersonne.npa}--></p>
				<p><label>lieu</label> <!--{$aPersonne.lieu}--></p>
				<p><label>tel</label> <!--{$aPersonne.tel}--></p>
				<p><label>e-mail</label> <!--{$aPersonne.email}--></p>
				<p><label>Remarque</label> <!--{$aPersonne.description}--></p>
			</div>
	<!--{/foreach}-->
</div>

