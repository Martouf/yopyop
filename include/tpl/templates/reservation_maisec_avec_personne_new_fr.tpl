	<div id="reservation" >
		
		<input type="hidden" name="objetIdCalendrier" value="<!--{$objetAReserver.id_calendrier}-->" id="objetIdCalendrier" />
		<input type="hidden" name="objetNom" value="<!--{$objetAReserver.nom}-->" id="objetNom" />
		<input type="hidden" name="idObjet" value="<!--{$objetAReserver.id_objet}-->" id="idObjet" />
		<input type="hidden" name="tags" id="tags" value="" />
			
		<h2 class="orange barre">Demande de réservation pour <!--{$objetAReserver.nom}--></h2>
		
		<div id="blocImagePresentation">
			<a href="http://<!--{$server_name}-->/<!--{$imagePresentation.lienMoyenne}-->" title ="<!--{$imagePresentation.nom}-->" rel="shadowbox[album];options={animate:false,continuous:true}">
				<img src="http://<!--{$server_name}-->/<!--{$imagePresentation.lienVignette}-->" alt="<!--{$imagePresentation.nom}-->" title="Cliquez pour agrandir" />
			</a>
		</div>
		
		<p>
			Vous recevrez une réponse (mail ou téléphone)
			au plus tard le dimanche qui suit votre demande.
		</p>
		<p>
			Le contrat vous sera envoyé par poste 3 mois avant la location.
		</p>
		
		<h2>Coordonnées du locataire</h2>
		
		<div id="blocNouvellePersonne" >
			<p><label>prenom</label> <input type="text" name="prenom" value="" id="inputPrenom"></input> <label>nom</label> <input type="text" name="nom" value="" id="inputNom"></input></p>
			<p><label>société ou école (pour les camp)</label> <input type="text" name="description" value="" id="inputDescription"></input></p>
			<p><label>rue</label> <input type="text" name="rue" value="" id="inputRue"></input></p>
			<p><label>NPA</label> <input type="text" name="npa" value="" id="inputNpa"></input> <label>localité</label> <input type="text" name="lieu" value="" id="inputLieu"></input></p>
			<p><label>tel</label> <input type="text" name="tel" value="" id="inputTel"></input></p>
			<p><label>e-mail</label> <input type="text" name="email" value="" id="inputEmail"></input></p>
		</div>
		
		<p>
			<label>Dates de début et de fin de réservation</label>
		</p>
		
		<div>
			<input type="text" name="jourDebutDetail" value="<!--{$dateDuJour}-->" id="jourDebutDetail" class="date-pick" />

				<label class="interfaceHeure" for="heureMinuteDebutDetail">heure</label>				
				
				<select class="interfaceHeure" id="heureMinuteDebutDetail" name="heureMinuteDebutDetail">
					<option value="09:00">09:00</option>
					<option selected="selected" value="17:30">17:30</option>
				</select>
		</div>
		<div>
			<input type="text" name="jourFinDetail" value="<!--{$dateDuJour}-->" id="jourFinDetail" class="date-pick" />


				<label class="interfaceHeure" for="heureMinuteFinDetail">heure</label>

				<select class="interfaceHeure" id="heureMinuteFinDetail" name="heureMinuteFinDetail">
					<option selected="selected" value="09:00">09:00</option>
					<option value="17:30">17:30</option>
				</select>

		</div>
		<p>Pour les camps veuillez préciser dans le champ remarque l'<strong>effectif prévu</strong>.</p>
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
	<p><a href="#" id="createReservation"><img src="http://<!--{$server_name}-->/utile/img/save.png" alt="save" /> envoyer une demande de réservation</a></p>
	
	<img src="http://<!--{$server_name}-->/utile/ajax/ticket.php" width="1" height="1" alt="transparent" />
