	<div id="outilsModif">
		<!--{if $utilisateurConnu}--> <a href="<!--{$reservation.id_reservation}-->-<!--{$reservation.nomSimplifie}-->.html?modify" title="modifier les données de la réservation"><img src="http://<!--{$server_name}-->/utile/img/edit.gif" /></a><!--{/if}-->
	</div>
	<div id="reservation" >
			
		<h2>Réservation de <!--{$objetReserve.nom}--></h2>
		
		<div id="blocImagePresentation">
			<a href="http://<!--{$server_name}-->/<!--{$imagePresentation.lienMoyenne}-->" title ="<!--{$imagePresentation.nom}-->" rel="shadowbox[album];options={animate:false,continuous:true}">
				<img src="http://<!--{$server_name}-->/<!--{$imagePresentation.lienVignette}-->" alt="<!--{$imagePresentation.nom}-->" title="Cliquez pour agrandir" />
			</a>
		</div>
				
		<p>
			<h3>Dates</h3>
		</p>
		
		<!--{if $evenementReserve.jour_entier=="true"}-->
		<p>
			<label>Date</label> <!--{$evenementReserve.jourDebutHumain}--> <!--{if $evenementReserve.jourDebut != $evenementReserve.jourFin}--> - <!--{$evenementReserve.jourFinHumain}--><!--{/if}-->
		</p>
		<!--{else}-->
		<p>
			<label>Début</label><span><!--{$evenementReserve.dateDebut}--></span><br />
			<label>Fin</label><span><!--{$evenementReserve.dateFin}--></span>
		</p>
		<!--{/if}-->
		<p>Voir le <a href="http://<!--{$server_name}-->/agenda/<!--{$objetReserve.id_calendrier}-->-reservation-pour-<!--{$objetReserve.nom}-->.html" title="voir le calendrier des réservations pour l'objet <!--{$objetReserve.nom}-->...">calendrier de réservation pour l'objet <!--{$objetReserve.nom}--></a></p>

		<p>
			<label>Remarque</label>
			<!--{$reservation.description}-->
		</p>
		<p>
			<label>la réservation est</label>
			<!--{if $reservation.type==1}--> définitive<!--{else}--> provisoire<!--{/if}-->
		</p>
		<p>
			<label>la réservation est </label>
			<!--{if $reservation.etat==0}--> en attente de validation<!--{elseif $reservation.etat==1}--> acceptée<!--{else}--> refusée<!--{/if}-->
		</p>
	</div>
