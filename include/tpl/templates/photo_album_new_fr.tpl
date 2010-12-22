<form action="photo.html?addalbum" method="post" accept-charset="utf-8">

	<div id="photo" >
		<p>
			<legend for="folderpath">Nom du dossier sur le serveur qui contient les photos</legend>
		</p>
		<p>
			<input type="text" name="folderpath" value="utile/images/photos/" id="folderpath" />
		</p>
		<p>
			<legend for="albumtag">Tag à attribuer à toutes les photos</legend>
		</p>
		<p>
			<input type="text" name="albumtag" value="" id="albumtag" />
		</p>
	</div>

	<p><input type="submit" value="sauve"></p>
</form>
