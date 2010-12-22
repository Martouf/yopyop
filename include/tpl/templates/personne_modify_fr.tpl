<form action="<!--{$personne.id_personne}-->-personne.html?update" method="post" accept-charset="utf-8">
	
	<div id="personne" >
		<p><label>Prenom</label> <input type="text" name="prenom" value="<!--{$personne.prenom}-->" id="inputPrenom"></input></p>
		<p><label>Nom</label> <input type="text" name="nom" value="<!--{$personne.nom}-->" id="inputNom"></input></p>
		<p><label>Surnom</label> <input type="text" name="surnom" value="<!--{$personne.surnom}-->" id="inputSurnom"></input></p>
		<p><label>Naissance</label> <input type="text" name="date_naissance" value="<!--{$personne.date_naissance}-->" id="inputNaissance"></input></p>
		<p><label>rue</label> <input type="text" name="rue" value="<!--{$personne.rue}-->" id="inputRue"></input></p>
		<p><label>npa</label> <input type="text" name="npa" value="<!--{$personne.npa}-->" id="inputNpa"></input></p>
		<p><label>lieu</label> <input type="text" name="lieu" value="<!--{$personne.lieu}-->" id="inputLieu"></input></p>
		<p><label>tel</label> <input type="text" name="tel" value="<!--{$personne.tel}-->" id="inputTel"></input></p>
		<p><label>e-mail</label> <input type="text" name="email" value="<!--{$personne.email}-->" id="inputEmail"></input></p>
		<p><label>Remarque</label> <input type="text" name="description" value="<!--{$personne.description}-->" id="inputDescription"></input></p>
	</div>
	
	<p><input type="submit" value="sauve"></p>
</form>


