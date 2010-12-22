<h1 id="nouvel_objet">Ajout d'un nouvel objet</h1>
<p>Veuillez lui donner un nom et fournir une photo de description.</p>

<form action="objet.html?add" method="post" enctype="multipart/form-data" accept-charset="utf-8">

	<div id="objet" >
		<p>
			<label for="nom">Nom</label><input type="text" name="nom" value="mon objet" id="nom" />
			
		</p>
		<p>
			<label for="image">Uploder une image (max 1024x768px)</label>
		</p>
		<p>
			<input type="file" accept="image/jpeg, image/gif, image/png" id="image" name="image" />
		</p>
	</div>

	<p><input type="submit" value="Ajouter"></p>
</form>
