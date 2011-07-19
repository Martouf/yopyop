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
			Cette demande de réservation est pour <!--{$futurLocataire.prenom}--> <!--{$futurLocataire.nom}-->
		</p>
		
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
