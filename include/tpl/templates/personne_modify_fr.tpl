<h2 class="violet barre">Modifier les données personnelles de <!--{$personne.prenom}--> <!--{$personne.nom}--></h2>

<form action="<!--{$personne.id_personne}-->-personne.html?update" method="post" accept-charset="utf-8">
	
	<div id="blocEditionPersonne" >
		<p><label>Prenom</label> <input type="text" name="prenom" value="<!--{$personne.prenom}-->" id="inputPrenom"></input></p>
		<p><label>Nom</label> <input type="text" name="nom" value="<!--{$personne.nom}-->" id="inputNom"></input></p>
		<p><label>Pseudo</label> <input type="text" name="surnom" value="<!--{$personne.surnom}-->" id="inputSurnom"></input> <span class="info">Le pseudo sera utilisé pour vous identifier sur ce site.</span></p>
		<p><label>Naissance</label> <input type="text" name="date_naissance" value="<!--{$personne.date_naissance}-->" id="inputNaissance"></input> <span class="info">Selon le format: AAAA-MM-JJ... (ouais.. c'est un peu brut pour le moment :P)</span></p>
		<p><label>Rue</label> <input type="text" name="rue" value="<!--{$personne.rue}-->" id="inputRue"></input></p>
		<p><label>Npa</label> <input type="text" name="npa" value="<!--{$personne.npa}-->" id="inputNpa"></input></p>
		<p><label>Localité</label> <input type="text" name="lieu" value="<!--{$personne.lieu}-->" id="inputLieu"></input></p>
		<p><label>Tel</label> <input type="text" name="tel" value="<!--{$personne.tel}-->" id="inputTel"></input> <span class="info">Selon le format: +41 76 443 54 61</span></p>
		<p><label>E-mail</label> <input type="text" name="email" value="<!--{$personne.email}-->" id="inputEmail"></input> <span class="info">Cette adresse est utilisée pour afficher votre photo de profil via <a href="http://gravatar.com">gravatar</a>.</span></p>
		<p><label>site web perso</label> <input type="text" name="url" value="<!--{$personne.url}-->" id="inputUrl"></input></p>
		<p><label>Remarque</label> <input type="text" name="description" value="<!--{$personne.description}-->" id="inputDescription"></input></p>
	</div>
	
	<div id="blocAvatar">
		<!--{if !empty($personne.email)}-->
			<img alt="gravatar <!--{$personne.nomSimplifie}-->" class="avatarProfile ombre" src="http://www.gravatar.com/avatar/<!--{$personne.gravatar}-->.jpg?default=retro" />
		<!--{/if}-->
	</div>
	
	<p>
		La photo de profil qui est affichée est celle qui est associée à votre adresse e-mail sur votre compte <a href="http://gravatar.com">gravatar</a>, si vous en avez un.
	</p>
	<p>
		Gravatar est un service centralisé de photo de profil. Vous associez une adresse e-mail à une photo. <br />
		Une fois que vous avez un compte sur <a href="http://gravatar.com">gravatar</a>, il suffit d'indiquer votre adresse e-mail pour que la photo liée soit affichée par les sites web compatibles. Il y en a beaucoup, ça vaut la peine de créer un compte. :D
	</p><p>	
		On voit souvent les gravatar utilisés dans les commentaires de blog wordpress.
	</p>
	
	<p style="text-align: center;"><input type="submit" value="enregistrer"></p>
<a href="//<!--{$server_name}-->/profile/"><img src="//<!--{$server_name}-->/utile/img/action_back.gif" alt="retour" /> annuler</a>
	
</form>


