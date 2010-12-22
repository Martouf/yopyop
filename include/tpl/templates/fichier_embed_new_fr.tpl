<form name="form" action="fichier.html?addfichier" accept-charset="utf-8" method="post" enctype="multipart/form-data">
	
	<h2>Envoyer un fichier</h2>
	<p>
		Ce formulaire permet de choisir un fichier sur son propre disque dur, de l'envoyer sur le serveur et de créer un lien sur le fichier à l'endroit ou se trouve le curseur dans le document.
	</p>
	<fieldset id="bloc_fichier">
		<legend>Choisir le fichier</legend>
		<p>
			<input type="file" id="fichier" name="fichier" />
		</p>
	</fieldset>
	<div class="boutons">
		<input type="submit" name="submit" id="submit" value="Ajouter" />
	</div>
</form>