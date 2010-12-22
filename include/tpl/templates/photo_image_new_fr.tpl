<form name="form" action="photo.html?addimage" accept-charset="utf-8" method="post" enctype="multipart/form-data">
	
	<fieldset id="bloc_image">
		<legend>Image</legend>
		<p>
			<input type="file" accept="image/jpeg, image/gif, image/png" id="image" name="image" />
		</p>
	</fieldset>
	<fieldset id="bloc_taille_vignette">
		<legend>Taille de la vignette</legend>
		<p>
			<label for="largeur">Largeur</label>
			<input type="text" name="largeur" id="largeur" onkeyup="metAJourTaille()" />
		</p>
		<p>
			<label for="hauteur">hauteur</label>
			<input type="text" name="hauteur" id="hauteur" onkeyup="metAJourTaille()" />
		</p>
		<p>Si une seule valeur est fournie, l'autre est calculée.</p>
	</fieldset>
	<fieldset id="bloc_position_vignette">
		<legend>Position de la vignette</legend>
		<input type="radio" name="position" id="position" value="0" checked="checked" />&nbsp;Non spécifiée&nbsp;&nbsp;&nbsp;
		<input type="radio" name="position" id="position" value="g" />&nbsp;gauche&nbsp;&nbsp;&nbsp;
		<input type="radio" name="position" id="position" value="c" />&nbsp;centrée&nbsp;&nbsp;&nbsp;
		<input type="radio" name="position" id="position" value="d" />&nbsp;droite
	</fieldset>
	<div class="boutons">
		<input type="submit" name="submit" id="submit" value="Ajouter" />
	</div>
	<div id="taille" title="si une seule dimension est donnée, l'autre est calculée pour garder les proportions de l'image, elle ne peut donc pas être montrée ici.">
	Taille de la vignette
	</div>
</form>