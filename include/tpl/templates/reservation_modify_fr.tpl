<a href="//<!--{$server_name}-->/reservation/<!--{$reservation.id_reservation}-->-<!--{$reservation.nomSimplifie}-->.html"><img src="//<!--{$server_name}-->/utile/img/action_back.gif" alt="retour" /> Retour à la fiche de la reservation</a>

<input type="hidden" name="idReservation" value="<!--{$reservation.id_reservation}-->" id="idReservation" />
<input type="hidden" name="objetIdCalendrier" value="<!--{$objetReserve.id_calendrier}-->" id="objetIdCalendrier" />
<input type="hidden" name="idEvenement" value="<!--{$evenementReserve.id_evenement}-->" id="idEvenement" />
<input type="hidden" name="objetNom" value="<!--{$objetReserve.nom}-->" id="objetNom" />
<input type="hidden" name="idObjet" value="<!--{$objetReserve.id_objet}-->" id="idObjet" />
<input type="hidden" name="tags" id="tags" value="" />	
	
	<div id="reservation" >
			
		<h1 class="barre vert">Réservation de l'objet <!--{$objetReserve.nom}--></h1>
		
		<div id="blocImagePresentation">
			<a href="http://<!--{$server_name}-->/<!--{$imagePresentation.lienMoyenne}-->" title ="<!--{$imagePresentation.nom}-->" rel="shadowbox[album];options={animate:false,continuous:true}">
				<img class="ombre" src="http://<!--{$server_name}-->/<!--{$imagePresentation.lienVignette}-->" alt="<!--{$imagePresentation.nom}-->" title="Cliquez pour agrandir" />
			</a>
		</div>
				
		<p>
			<label>Dates de début et de fin de réservation</label>
		</p>
		
		<div id="blocDateDebutReservation">
			<input type="text" name="jourDebutDetail" value="<!--{$evenementReserve.jourDebutEurope}-->" id="jourDebutDetail" class="date-pick" />

				<label class="interfaceHeure" for="heureDebutDetail">heure</label>

				<select class="interfaceHeure" id="heureDebutDetail" name="heureDebutDetail">
					<!--{foreach from=$heures key=id item=heure}-->
						<option <!--{if $heure==$evenementReserve.heureDebut}-->selected="selected"<!--{/if}-->  value="<!--{$heure}-->"><!--{$heure}--></option>
					<!--{/foreach}-->
				</select>
				<select class="interfaceHeure" id="minuteDebutDetail" name="minuteDebuDetail">
					<!--{foreach from=$minutes key=id item=minute}-->
						<option <!--{if $minute==$evenementReserve.minuteDebut}-->selected="selected"<!--{/if}-->  value="<!--{$minute}-->"><!--{$minute}--></option>
					<!--{/foreach}-->
				</select>
		</div>
		<div id="blocDateFinReservation">
			<input type="text" name="jourFinDetail" value="<!--{$evenementReserve.jourFinEurope}-->" id="jourFinDetail" class="date-pick" />


				<label class="interfaceHeure" for="heureFinDetail">heure</label>

				<select class="interfaceHeure" id="heureFinDetail" name="heureFinDetail">
					<!--{foreach from=$heures key=id item=heure}-->
						<option <!--{if $heure==$evenementReserve.heureFin}-->selected="selected"<!--{/if}-->  value="<!--{$heure}-->"><!--{$heure}--></option>
					<!--{/foreach}-->
				</select>
				<select class="interfaceHeure" id="minuteFinDetail" name="minuteFinDetail">
					<!--{foreach from=$minutes key=id item=minute}-->
						<option <!--{if $minute==$evenementReserve.minuteFin}-->selected="selected"<!--{/if}-->  value="<!--{$minute}-->"><!--{$minute}--></option>
					<!--{/foreach}-->
				</select>

		</div>
		<p>
			<label title="Réserver le jour entier sans définir d'heure" for="jourComplet">Jour entier</label>
			<input type="checkbox" name="jourComplet" id="jourComplet" <!--{if $evenementReserve.jour_entier=="true"}-->checked="checked"<!--{/if}-->  />
		</p>
		
		<p>
			<label for="caution">Disponibilités</label>
			<span class="valeur"><a title="Ouvrir le calendrier des réservations dans une nouvelle fenêtre..." target="blank" href="//<!--{$server_name}-->/calendrier/<!--{$objetReserve.id_calendrier}-->-reservations-<!--{$objetReserve.nom}-->.html">Vérifier les disponibilités sur le calendrier des réservations...</a>
			</span><br />
		<p>
		<p>
			<label>Remarque</label>
		</p>
		<textarea id="descriptionDetail" name="descriptionDetail" rows="8" cols="40"><!--{$reservation.description}--></textarea>
		
		<p>
			<label for="inputType">Type de réservation</label>
			<select id="inputType" name="inputType">
				<option value="1" <!--{if $reservation.type==1}-->selected="selected"<!--{/if}-->>définitive</option>
				<option value="2" <!--{if $reservation.type==2}-->selected="selected"<!--{/if}-->>préréservation</option>
			</select>
		</p>
		<!--{if $proprietaireObjet}-->
		<p>
			<label for="inputEtat">Etat de la réservation</label>
			<select id="inputEtat" name="inputEtat">
				<option value="0" <!--{if $reservation.etat==0}-->selected="selected"<!--{/if}-->>en attente de validation</option>
				<option value="1" <!--{if $reservation.etat==1}-->selected="selected"<!--{/if}-->>acceptée</option>
				<option value="2" <!--{if $reservation.etat==2}-->selected="selected"<!--{/if}-->>refusée</option>
			</select>
		</p>
		<!--{else}-->
		<p>
			<label>la réservation est </label>
			<!--{if $reservation.etat==0}-->
			en attente de validation <input type="hidden" name="inputEtat" value="0" id="inputEtat" />
			<!--{elseif $reservation.etat==1}-->
			acceptée <input type="hidden" name="inputEtat" value="1" id="inputEtat" />
			<!--{else}-->
			refusée <input type="hidden" name="inputEtat" value="2" id="inputEtat" />
			<!--{/if}-->
		</p>
		<!--{/if}-->
	</div>
	
	<p>
		<a href="#" onclick="app.saveReservation(); return false;"><img src="http://<!--{$server_name}-->/utile/img/save.png" alt="save" /> enregistrer les données de la réservation</a> &nbsp;|&nbsp; 
		<a href="//<!--{$server_name}-->/reservation/<!--{$reservation.id_reservation}-->-<!--{$reservation.nomSimplifie}-->.html" id="cancelReservation" >annuler</a>
	</p>
	
	
	<div id="loading">
		<img src="http://<!--{$server_name}-->/utile/img/loading.gif" alt="loading"/> loading
	</div>
	<div id="logAction">
	</div>