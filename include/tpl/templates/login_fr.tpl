<form action="//<!--{$server_name}-->/utile/ajax/login.php" method="post" accept-charset="utf-8" id="login_form">
	<div id="bloc_identification">
		<a class="toggle" id="toggleCoccinelle" href="#">
			se connecter
		</a>
		<fieldset id="form_identification" >
			<legend>Identification</legend>
			<p>
				<label for="pseudo">Pseudo</label>
				<input type="text" name="pseudo" id="pseudo" />
			</p>
			<p>
				<label for="password">Mot de passe</label>
				<input type="password" name="password" id="password" />
			</p>
				<div class="boutons">
					<input type="submit" name="submit" id="submit" value="Connect" />
				</div>
		</fieldset>
	</div>
</form>