	<h2>Que fais-tu maintenant?</h2>
	
	<div id="statut" >
		<p><strong><!--{$pseudo}--></strong> <input type="text" name="nom" value="<!--{$statut.nom}-->" id="inputNom"></input></p>
	</div>
	
	<p>
		<a href="#" id="saveStatut"><img src="http://<!--{$server_name}-->/utile/img/save.png" alt="save" /> enregistrer le statut</a>
		<a href="//<!--{$server_name}-->/statut/<!--{$statut.id_statut}-->-<!--{$statut.nomSimplifie}-->.html" >retour au statut</a>
	</p>
	
	<input type="hidden" name="idStatut" value="<!--{$statut.id_statut}-->" id="idStatut" />