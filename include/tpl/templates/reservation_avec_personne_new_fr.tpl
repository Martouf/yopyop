	<div id="reservation" >
		
		<input type="hidden" name="objetIdCalendrier" value="<!--{$objetAReserver.id_calendrier}-->" id="objetIdCalendrier" />
		<input type="hidden" name="objetNom" value="<!--{$objetAReserver.nom}-->" id="objetNom" />
		<input type="hidden" name="idObjet" value="<!--{$objetAReserver.id_objet}-->" id="idObjet" />
		<input type="hidden" name="tags" id="tags" value="" />
			
		<h2 class="vert barre">Demande de réservation pour l'objet: <!--{$objetAReserver.nom}--></h2>
		
		<div id="blocImagePresentation">
			<a href="http://<!--{$server_name}-->/<!--{$imagePresentation.lienMoyenne}-->" title ="<!--{$imagePresentation.nom}-->" rel="shadowbox[album];options={animate:false,continuous:true}">
				<img class="ombre" src="http://<!--{$server_name}-->/<!--{$imagePresentation.lienVignette}-->" alt="<!--{$imagePresentation.nom}-->" title="Cliquez pour agrandir" />
			</a>
		</div>
		<p>
			Pour réserver un objet, <strong>vous devez être identifié(e)</strong>. Pour le moment vous ne l'êtes pas:
		</p>
		<ul>
			<li>Soit <strong>vous avez un compte</strong>, mais vous n'êtes pas connecté(e), c'est le moment de le faire (en haut à droite).</li>
			<li>Soit <strong>vous n'avez pas de compte</strong> sur ce site et nous allons le créer maintenant.</li>
		</ul>

		<h2>Création d'un nouveau compte</h2>
		<h3>Coordonnées du locataire:</h3>
		<div id="blocNouvellePersonne" >
			<p><label>Prenom</label> <input type="text" name="prenom" value="" id="inputPrenom"></input></p>
			<p><label>Nom</label> <input type="text" name="nom" value="" id="inputNom"></input></p>
			<p><label title="le pseudo est utilisé pour vous connecter" >Pseudo<sup>*</sup></label> <input type="text" name="surnom" value="Bob" id="inputSurnom"></input> <span class="info">Le pseudo sera utilisé pour vous identifier sur ce site.</span></p>
			<p><label>Mot de passe</label> <input type="password" name="mot_de_passe" value="" id="inputMotDePasse"></input></p>
			<p><label>Répéter le mot de passe</label> <input type="password" name="mot_de_passe2" value="" id="inputMotDePasseRepete"></input></p>
			<p><label>Date de naissance</label> <input type="text" name="date_naissance" value="1981-07-12 00:00:00" id="inputNaissance"></input> <span class="info">Selon le format: AAAA-MM-JJ 00:00:00 (ouais.. c'est un peu brut pour le moment :P)</span></p>
			<p><label>Rue</label> <input type="text" name="rue" value="" id="inputRue"></input></p>
			<p><label>NPA</label> <input type="text" name="npa" value="" id="inputNpa"></input></p>
			<p><label>Localité</label> <input type="text" name="lieu" value="" id="inputLieu"></input></p>
			<p><label>Tel</label> <input type="text" name="tel" value="" id="inputTel"></input> <span class="info">Selon le format: +41 76 443 54 61</span></p>
			<p><label>E-mail<sup>*</sup></label> <input type="text" name="email" value="" id="inputEmail"></input><span class="info">Cette adresse est utilisée pour afficher votre photo de profil via <a href="http://gravatar.com">gravatar</a>.</span></p> 
			<p><label>Remarque</label> <input type="text" name="description" value="" id="inputDescription"></input></p>
			<p><sup>*</sup>Champs requis</p>
		</div>
		
		<p>
			<label>Dates de début et de fin de réservation</label>
		</p>
		
		<div id="blocDateDebutReservation">
			<input type="text" name="jourDebutDetail" value="" id="jourDebutDetail" class="date-pick" />

				<label class="interfaceHeure" for="heureDebutDetail">heure</label>

				<select class="interfaceHeure" id="heureDebutDetail" name="heureDebutDetail">
					<!--{foreach from=$heures key=id item=heure}-->
						<option <!--{if $heure==$reservation.evenement.heureDebut}-->selected="selected"<!--{/if}-->  value="<!--{$heure}-->"><!--{$heure}--></option>
					<!--{/foreach}-->
				</select>
				<select class="interfaceHeure" id="minuteDebutDetail" name="minuteDebuDetail">
					<!--{foreach from=$minutes key=id item=minute}-->
						<option <!--{if $minute==$reservation.evenement.minuteDebut}-->selected="selected"<!--{/if}-->  value="<!--{$minute}-->"><!--{$minute}--></option>
					<!--{/foreach}-->
				</select>
		</div>
		<div id="blocDateFinReservation">
			<input type="text" name="jourFinDetail" value="" id="jourFinDetail" class="date-pick" />


				<label class="interfaceHeure" for="heureFinDetail">heure</label>

				<select class="interfaceHeure" id="heureFinDetail" name="heureFinDetail">
					<!--{foreach from=$heures key=id item=heure}-->
						<option <!--{if $heure==$reservation.evenement.heureFin}-->selected="selected"<!--{/if}-->  value="<!--{$heure}-->"><!--{$heure}--></option>
					<!--{/foreach}-->
				</select>
				<select class="interfaceHeure" id="minuteFinDetail" name="minuteFinDetail">
					<!--{foreach from=$minutes key=id item=minute}-->
						<option <!--{if $minute==$reservation.evenement.minuteFin}-->selected="selected"<!--{/if}-->  value="<!--{$minute}-->"><!--{$minute}--></option>
					<!--{/foreach}-->
				</select>
		</div>
		<p>
			<label for="jourComplet">Jour entier</label>
			<input type="checkbox" name="jourComplet" id="jourComplet" <!--{if $reservation.evenement.jourEntier=="true"}-->checked="checked"<!--{/if}-->  />
		</p>
		<p>
			<label>Remarque</label>
		</p>
		<textarea id="descriptionDetail" name="descriptionDetail" rows="8" cols="40"></textarea>
		
		<p>
			<label for="inputType">Type de réservation</label>
			<select id="inputType" name="inputType">
					<option value="1">définitive</option>
					<option value="2">préréservation</option>
			</select>
		</p>
		
	</div>
	<p>
		<a href="#" id="createReservation"><img src="http://<!--{$server_name}-->/utile/img/save.png" alt="save" /> Envoyer la demande de réservation</a> 
		<br /><span class="info">Le propriétaire de l'objet devra ensuite accepter la demande de réservation pour que vous puissiez avoir l'objet.</span>
	</p>
	
	<img src="http://<!--{$server_name}-->/utile/ajax/ticket.php" width="1" height="1" alt="transparent" />
