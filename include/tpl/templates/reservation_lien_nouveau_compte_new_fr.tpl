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
		<br />
		<p style="text-align: center;">
			<span class="barre jaune"><a href="//<!--{$server_name}-->/personne/?new">Créer un nouveau compte utilisateur</a></span>
		</p>
	</div>