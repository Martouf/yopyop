	
	<div id="reservation" >
			
		<h1 class="barre vert">
			Réservation de <!--{$objetReserve.nom}-->
			<span id="outilsModif">
				<!--{if $utilisateurConnu}--> <a href="<!--{$reservation.id_reservation}-->-<!--{$reservation.nomSimplifie}-->.html?modify" title="modifier les données de la réservation"><img src="http://<!--{$server_name}-->/utile/img/edit.gif" /></a><!--{/if}-->
			</span>
		</h1>
		
		<div id="blocImagePresentation">
			<a href="http://<!--{$server_name}-->/<!--{$imagePresentation.lienMoyenne}-->" title ="<!--{$imagePresentation.nom}-->" rel="shadowbox[album];options={animate:false,continuous:true}">
				<img class="ombre" src="http://<!--{$server_name}-->/<!--{$imagePresentation.lienVignette}-->" alt="<!--{$imagePresentation.nom}-->" title="Cliquez pour agrandir" />
			</a>
		</div>
				
		<!--{if $evenementReserve.jour_entier=="true"}-->
		<p>
			<label>Dates</label> <!--{$evenementReserve.jourDebutHumain}--> <!--{if $evenementReserve.jourDebut != $evenementReserve.jourFin}--> - <!--{$evenementReserve.jourFinHumain}--><!--{/if}-->
		</p>
		<!--{else}-->
		<p>
			<label>Date de début</label><span><!--{$evenementReserve.dateDebut}--></span><br />
			<label>Date de fin  </label><span><!--{$evenementReserve.dateFin}--></span>
		</p>
		<!--{/if}-->
		<p>
			<label for="caution">Disponibilités</label>
			<span class="valeur"><a title="voir le calendrier des réservations dans une nouvelle fenêtre..." target="blank" href="//<!--{$server_name}-->/calendrier/<!--{$objetReserve.id_calendrier}-->-reservations-<!--{$objetReserve.nom}-->.html">Voir le calendrier des réservations...</a>
			</span><br />
		<p>
			<!--{$reservation.description}-->
		</p>
		<p>
			<label>La réservation est</label>
			<!--{if $reservation.type==1}--> définitive<!--{else}--> provisoire<!--{/if}--><br />
			
			<label>La réservation est </label>
			<!--{if $reservation.etat==0}--> en attente de validation<!--{elseif $reservation.etat==1}--> acceptée<!--{else}--> refusée<!--{/if}-->
		</p>
	</div>
