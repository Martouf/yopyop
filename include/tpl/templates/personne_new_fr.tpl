<form action="personne.html?add" method="post" accept-charset="utf-8">

			<div id="personne_<!--{$personne[id_personne]}-->" >
				<p><label>Prenom</label> <input type="text" name="prenom" value="" id="inputPrenom"></input></p>
				<p><label>Nom</label> <input type="text" name="nom" value="" id="inputNom"></input></p>
				<p><label>Surnom</label> <input type="text" name="surnom" value="" id="inputSurnom"></input></p>
				<p><label>Naissance</label> <input type="text" name="date_naissance" value="" id="inputNaissance"></input></p>
				<p><label>rue</label> <input type="text" name="rue" value="" id="inputRue"></input></p>
				<p><label>npa</label> <input type="text" name="npa" value="" id="inputNpa"></input></p>
				<p><label>lieu</label> <input type="text" name="lieu" value="" id="inputLieu"></input></p>
				<p><label>tel</label> <input type="text" name="tel" value="" id="inputTel"></input></p>
				<p><label>e-mail</label> <input type="text" name="email" value="" id="inputEmail"></input></p>
				<p><label>Remarque</label> <input type="text" name="description" value="" id="inputDescription"></input></p>
			</div>

	<p><input type="submit" value="ajoute"></p>
</form>