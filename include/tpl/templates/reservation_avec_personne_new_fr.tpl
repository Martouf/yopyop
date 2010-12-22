	<div id="reservation" >
		
		<input type="hidden" name="objetIdCalendrier" value="<!--{$objetAReserver.id_calendrier}-->" id="objetIdCalendrier" />
		<input type="hidden" name="objetNom" value="<!--{$objetAReserver.nom}-->" id="objetNom" />
		<input type="hidden" name="idObjet" value="<!--{$objetAReserver.id_objet}-->" id="idObjet" />
		<input type="hidden" name="tags" id="tags" value="" />
			
		<h2>Formulaire de demande de réservation</h2>
		
		<div id="blocImagePresentation">
			<a href="http://<!--{$server_name}-->/<!--{$imagePresentation.lienMoyenne}-->" title ="<!--{$imagePresentation.nom}-->" rel="shadowbox[album];options={animate:false,continuous:true}">
				<img src="http://<!--{$server_name}-->/<!--{$imagePresentation.lienVignette}-->" alt="<!--{$imagePresentation.nom}-->" title="Cliquez pour agrandir" />
			</a>
		</div>
		<p>
			Demande de réservation pour <!--{$futurLocataire.prenom}--> <!--{$futurLocataire.nom}--> pour l'objet: 
			<!--{$objetAReserver.nom}-->
		</p>
		
		<h2>Coordonnées du locataire</h2>
		<p>
			Pour réserver un objet, il faut que vous soyez identifié(e). Pour le moment vous ne l'êtes pas.<br />
			Soit vous n'est pas identifié(e) sur le site, et c'est le moment de le faire. (en bas à gauche de la page)<br />
			Soit vous n'avez pas de compte sur ce site et nous allons le créer maintenant.
		</p>
		
		<div id="blocNouvellePersonne" >
			<p><label>prenom</label> <input type="text" name="prenom" value="" id="inputPrenom"></input> <label>nom</label> <input type="text" name="nom" value="" id="inputNom"></input></p>
			<p><label title="le pseudo est utilisé pour vous connecter" >Pseudo*</label> <input type="text" name="surnom" value="" id="inputSurnom"></input></p>
			<p><label>mot de passe</label> <input type="password" name="mot_de_passe" value="" id="inputMotDePasse"></input></p>
			<p><label>répéter le mot de passe</label> <input type="password" name="mot_de_passe2" value="" id="inputMotDePasseRepete"></input></p>
			<p><label>date de naissance</label> <input type="text" name="date_naissance" value="12-07-1981" id="inputNaissance"></input></p>
			<p><label>rue</label> <input type="text" name="rue" value="" id="inputRue"></input></p>
			<p><label>NPA</label> <input type="text" name="npa" value="" id="inputNpa"></input> <label>localité</label> <input type="text" name="lieu" value="" id="inputLieu"></input></p>
			<p><label>tel</label> <input type="text" name="tel" value="" id="inputTel"></input></p>
			<p><label>e-mail*</label> <input type="text" name="email" value="" id="inputEmail"></input></p>
			<p><label>remarque</label> <input type="text" name="description" value="" id="inputDescription"></input></p>
			<p>*champs requis</p>
		</div>
		
		<p>
			<label>Dates de début et de fin de réservation</label>
		</p>
		
		<div>
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
		<div>
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
	<p><a href="#" id="createReservation"><img src="/utile/img/save.png" alt="save" /> enregistrer l'évenement</a></p>
	
	<img src="/utile/ajax/ticket.php" width="1" height="1" alt="transparent" />
