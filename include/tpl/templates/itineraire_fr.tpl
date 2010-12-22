<div id="blocCentre">

<h1>Aide à la préparation de dispositifs de marche</h1>
<p>
	Cette application est destinée à aider à la réalisation de dispositifs de marche J+S.<br />
	Voici un exemple de <a href="">dispositif de marche en application excel</a>
</p>
<p>
	Il est toujours terriblement long d'aller chercher les coordonnées d'un point, puis son altitude et enfin de calculer la distance entre les deux.
	Ce petit programme fait tout tout seul! Il suffit de cliquer sur la photo aérienne pour connaitre la coordonnée du lieu, son altitude et la distance qui relie ce point du précédent cliqué.  
</p>
<p>Il y a moyen d'avoir plusieurs fond de carte. Les photos et le plan de google. Ce qui n'est pas très précis et vieux. Ou par défaut, les photos de l'Etat de Neuchâtel réalisées en juin 2006. L'altitude quand a elle provient de la mission SRTM de la NASA (qui a cartographié la planète sur une grille de 90mx90m) via un <a href="http://www.geonames.org/export/web-services.html">service de geonames.org</a>.</p>
<p>
	Il ne m'est pas possible d'intégrer la carte officielle de la suisse. Mais la carte swisstopo de la suisse entière est disponible sur le <a href="http://map.geo.admin.ch/?lang=fr">portail cartographique de la confédération</a>.
</p>

	<div id="map" style="width: 800px; height: 700px"></div>
   <label>Latitude</label> <input type="text" name="latitude" value="0" id="latitude" />
   <label>Longitude</label> <input type="text" name="longitude" value="0" id="longitude" />
   <label>Altitude</label> <input type="text" name="altitude" value="0" id="altitude" style="width: 30px;" />
	
	<label> adresse</label> <input type="text" name="adresse" value="" id="inputAdresse"></input>
	<a href="#" id="getAdresse"> montrer le lieu</a>
	
	<input type="hidden" name="latitude2" value="0" id="latitude2" />
	<input type="hidden" name="longitude2" value="0" id="longitude2" />
	
	<p>
		Coordonnées suisse CH1903: <input type="text" name="swissCoord" value="" id="swissCoord">
	</p>

	<p>
		<span class="coord">coordonnées</span> :: <span class="distance">distance</span>  ::  <span class="altitude">altitude</a>
	</p>
	<div id="parcours">
		
	</div>

</div>