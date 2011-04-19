	<h2>Nouvelle mesure</h2>
	
	<div id="mesure" >
		<p><label for="inputNom">Nom</label><input type="text" name="inputNom" value="Température eau °C" id="inputNom" /></p>
		<p>
			<label for="inputValeur">Valeur</label> <input type="text" name="inputValeur" value="" id="inputValeur" />
		</p>
		<p>
			<label for="inputLieu">Lieu</label> <input type="text" name="inputLieu" value="Plage de Serrières" id="inputLieu" />
		</p>
		<p>
			<label for="inputDate">Date</label> <input type="text" name="inputDate" value="<!--{$smarty.now|date_format:'%Y-%m-%d %H:%M:%S'}-->" id="inputDate" />
		</p>
		<p>
			<label for="inputType">Type</label>
			
			<select id="inputType" name="inputType">
					<option value="1">Température de l'air °C</option>
					<option selected="selected" value="2">Température de l'eau °C</option>
					<option value="3">Pression hPa</option>
					<option value="4">Tendance météo</option>
					<option value="5">Température air minima °C</option> 
					<option value="6">Température air maxima °C</option>
					<option value="7">Humidité relative %</option>
					<option value="8">Point de rosée °C</option>
					<option value="9">Direction vent °</option>
					<option value="10">Sens vent (N-E)</option>
					<option value="11">Vitesse vent noeud</option>
					<option value="12">Vitesse moyenne du vent sur une minute en noeud kts</option>
					<option value="13">Force du vent Bf</option>
					<option value="14">Description vent beaufort "petite brise"</option>
					<option value="15">Sensation thermique °C</option>
					<option value="16">Taux de précipitation en mm/h</option>
					<option value="17">Total précipitation mm</option>
			</select>
		</p>
	</div>
	
	<p>
		<a href="#" id="createMesure"><img src="http://<!--{$server_name}-->/utile/img/save.png" alt="save" /> enregistrer la mesure</a> 
	</p>


