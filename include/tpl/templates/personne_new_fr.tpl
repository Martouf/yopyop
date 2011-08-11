<h2 class="violet barre">Créer un nouveau compte utilisateur</h2>

<form action="personne.html?add" method="post" accept-charset="utf-8">

			<div id="blocEditionPersonne" >
				<p><label>Prenom</label> <input type="text" name="prenom" value="" id="inputPrenom"></input></p>
				<p><label>Nom</label> <input type="text" name="nom" value="" id="inputNom"></input></p>
				<p><label>Pseudo</label> <input type="text" name="surnom" value="" id="inputSurnom"></input> <span class="info">Le pseudo sera utilisé pour vous identifier sur ce site.</span></p></p>
				<p><label>Mot de passe</label> <input type="password" name="mot_de_passe" value="" id="inputMotDePasse"></input></p>
				<p><label>Répéter le mot de passe</label> <input type="password" name="mot_de_passe2" value="" id="inputMotDePasseRepete"></input></p>
				<p><label>Date de naissance</label>
					<select class="interfaceJour" id="jourNaissanceDetail" name="jourNaissanceDetail">
						<!--{foreach from=$jours key=id item=jour}-->
							<option value="<!--{$jour}-->"><!--{$jour}--></option>
						<!--{/foreach}-->
					</select>
					<select class="interfaceMois" id="moisNaissanceDetail" name="moisNaissanceDetail">
						<!--{foreach from=$mois key=id item=moi}-->
							<option value="<!--{$moi}-->"><!--{$moi}--></option>
						<!--{/foreach}-->
					</select>
					<select class="interfaceAnnee" id="anneeNaissanceDetail" name="anneeNaissanceDetail">
						<!--{foreach from=$annees key=id item=annee}-->
							<option value="<!--{$annee}-->"><!--{$annee}--></option>
						<!--{/foreach}-->
					</select>
				     <span class="info">jour, mois, année</span>
				</p>
				<p><label>Rue</label> <input type="text" name="rue" value="" id="inputRue"></input></p>
				<p><label>Npa</label> <input type="text" name="npa" value="" id="inputNpa"></input></p>
				<p><label>Localité</label> <input type="text" name="lieu" value="" id="inputLieu"></input></p>
				<p><label>Tel</label> <input type="text" name="tel" value="" id="inputTel"></input> <span class="info">Selon le format: +41 76 443 54 61</span></p>
				<p><label>E-mail</label> <input type="text" name="email" value="" id="inputEmail"> <span class="info">Si vous avez un compte <a href="http://gravatar.com">gravatar</a>. Utilisez une adresse liée à une photo de profil.</span></input></p>
				<p><label>Site web perso</label> <input type="text" name="url" value="" id="inputUrl"></input></p>
				<p><label>Slogan / citation</label> <input type="text" name="description" value="" id="inputDescription"></input> <span class="info">Petite phrase pour personnaliser votre profil</span></p>
			</div>

				<p>
					La photo de profil qui sera affichée est celle qui est associée à votre adresse e-mail sur votre compte <a href="http://gravatar.com">gravatar</a>, si vous en avez un.
				</p>
				<p>
					Gravatar est un service centralisé de photo de profil. Vous associez une adresse e-mail à une photo. <br />
					Une fois que vous avez un compte sur <a href="http://gravatar.com">gravatar</a>, il suffit d'indiquer votre adresse e-mail pour que la photo liée soit affichée par les sites web compatibles. Il y en a beaucoup, ça vaut la peine de créer un compte. :D
				</p><p>	
					On voit souvent les gravatar utilisés dans les commentaires de blog wordpress.
				</p>

				<p style="text-align: center;"><input type="submit" value="Créer le compte"></p>
			<a href="//<!--{$server_name}-->/"><img src="//<!--{$server_name}-->/utile/img/action_back.gif" alt="retour" /> annuler</a>
			
			<img src="http://<!--{$server_name}-->/utile/ajax/ticket.php" width="1" height="1" alt="transparent" />
</form>