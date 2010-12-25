<div id="barreNavigation">
	
	<input type="hidden" name="baseUrl" value="<!--{$baseUrl}-->" id="baseUrl" />
	
	<input type="hidden" name="dateMoisPasse" value="<!--{$dateMoisPasse}-->" id="dateMoisPasse" />
	<input type="hidden" name="dateMoisProchain" value="<!--{$dateMoisProchain}-->" id="dateMoisProchain" />
	<input type="hidden" name="dateSemainePassee" value="<!--{$dateSemainePassee}-->" id="dateSemainePassee" />
	<input type="hidden" name="dateSemaineProchaine" value="<!--{$dateSemaineProchaine}-->" id="dateSemaineProchaine" />
		
	<a href="#" id="boutonPasse"><img src="http://<!--{$server_name}-->/utile/img/date_previous.png" alt="précédent" title="précédent" /></a>
	<span id="blocChoixVue">
		<select name="choixVue" id="choixVue">
			<option <!--{if $choixVue=='mois'}-->selected="selected"<!--{/if}--> value="mois">mois</option>
			<option <!--{if $choixVue=='semaine'}-->selected="selected"<!--{/if}--> value="semaine">semaine</option>
			<option <!--{if $choixVue=='liste'}-->selected="selected"<!--{/if}--> value="liste">liste</option>	
			<option <!--{if $choixVue=='annee'}-->selected="selected"<!--{/if}--> value="annee">annee</option>			
		</select>
	</span>
	<a href="<!--{$urlMoisProchain}-->"  id="boutonSuivant"><img src="http://<!--{$server_name}-->/utile/img/date_next.png" alt="suivant" title="suivant" /></a>
	
	
	<span id="navigationJour">
		<select class="choixJour" id="choixJour" name="choixJour">
			<option <!--{if $choixJour=='01'}-->selected="selected"<!--{/if}--> value="01">01</option>
			<option <!--{if $choixJour=='02'}-->selected="selected"<!--{/if}--> value="02">02</option>
			<option <!--{if $choixJour=='03'}-->selected="selected"<!--{/if}--> value="03">03</option>
			<option <!--{if $choixJour=='04'}-->selected="selected"<!--{/if}--> value="04">04</option>
			<option <!--{if $choixJour=='05'}-->selected="selected"<!--{/if}--> value="05">05</option>
			<option <!--{if $choixJour=='06'}-->selected="selected"<!--{/if}--> value="06">06</option>
			<option <!--{if $choixJour=='07'}-->selected="selected"<!--{/if}--> value="07">07</option>
			<option <!--{if $choixJour=='08'}-->selected="selected"<!--{/if}--> value="08">08</option>
			<option <!--{if $choixJour=='09'}-->selected="selected"<!--{/if}--> value="09">09</option>
			<option <!--{if $choixJour=='10'}-->selected="selected"<!--{/if}--> value="10">10</option>
			<option <!--{if $choixJour=='11'}-->selected="selected"<!--{/if}--> value="11">11</option>
			<option <!--{if $choixJour=='12'}-->selected="selected"<!--{/if}--> value="12">12</option>
			<option <!--{if $choixJour=='13'}-->selected="selected"<!--{/if}--> value="13">13</option>
			<option <!--{if $choixJour=='14'}-->selected="selected"<!--{/if}--> value="14">14</option>
			<option <!--{if $choixJour=='15'}-->selected="selected"<!--{/if}--> value="15">15</option>
			<option <!--{if $choixJour=='16'}-->selected="selected"<!--{/if}--> value="16">16</option>
			<option <!--{if $choixJour=='17'}-->selected="selected"<!--{/if}--> value="17">17</option>
			<option <!--{if $choixJour=='18'}-->selected="selected"<!--{/if}--> value="18">18</option>
			<option <!--{if $choixJour=='19'}-->selected="selected"<!--{/if}--> value="19">19</option>
			<option <!--{if $choixJour=='20'}-->selected="selected"<!--{/if}--> value="20">20</option>
			<option <!--{if $choixJour=='21'}-->selected="selected"<!--{/if}--> value="21">21</option>
			<option <!--{if $choixJour=='22'}-->selected="selected"<!--{/if}--> value="22">22</option>
			<option <!--{if $choixJour=='23'}-->selected="selected"<!--{/if}--> value="23">23</option>
			<option <!--{if $choixJour=='24'}-->selected="selected"<!--{/if}--> value="24">24</option>
			<option <!--{if $choixJour=='25'}-->selected="selected"<!--{/if}--> value="25">25</option>
			<option <!--{if $choixJour=='26'}-->selected="selected"<!--{/if}--> value="26">26</option>
			<option <!--{if $choixJour=='27'}-->selected="selected"<!--{/if}--> value="27">27</option>
			<option <!--{if $choixJour=='28'}-->selected="selected"<!--{/if}--> value="28">28</option>
			<option <!--{if $choixJour=='29'}-->selected="selected"<!--{/if}--> value="29">29</option>
			<option <!--{if $choixJour=='30'}-->selected="selected"<!--{/if}--> value="30">30</option>
			<option <!--{if $choixJour=='31'}-->selected="selected"<!--{/if}--> value="31">31</option>
		</select>
	</span>
	
	<span id="navigationMois">		
		<select class="choixMois" id="choixMois" name="choixMois">
				<option <!--{if $choixMois=='01'}-->selected="selected"<!--{/if}--> value="01">janvier</option>
				<option <!--{if $choixMois=='02'}-->selected="selected"<!--{/if}--> value="02">février</option>
				<option <!--{if $choixMois=='03'}-->selected="selected"<!--{/if}--> value="03">mars</option>
				<option <!--{if $choixMois=='04'}-->selected="selected"<!--{/if}--> value="04">avril</option>
				<option <!--{if $choixMois=='05'}-->selected="selected"<!--{/if}--> value="05">mai</option>
				<option <!--{if $choixMois=='06'}-->selected="selected"<!--{/if}--> value="06">juin</option>
				<option <!--{if $choixMois=='07'}-->selected="selected"<!--{/if}--> value="07">juillet</option>
				<option <!--{if $choixMois=='08'}-->selected="selected"<!--{/if}--> value="08">août</option>
				<option <!--{if $choixMois=='09'}-->selected="selected"<!--{/if}--> value="09">septembre</option>
				<option <!--{if $choixMois=='10'}-->selected="selected"<!--{/if}--> value="10">octobre</option>
				<option <!--{if $choixMois=='11'}-->selected="selected"<!--{/if}--> value="11">novembre</option>
				<option <!--{if $choixMois=='12'}-->selected="selected"<!--{/if}--> value="12">décembre</option>
		</select>
		
		<select class="choixAnnee" id="choixAnnee" name="choixAnnee">
			<!--{foreach from=$annees key=id item=annee}-->
				<option <!--{if $annee==$choixAnnee}-->selected="selected"<!--{/if}-->  value="<!--{$annee}-->" ><!--{$annee}--></option>
			<!--{/foreach}-->
		</select>
	</span>
	
	<span title="exporter la page au format pdf..." id="blocLienPdf">&nbsp;&nbsp;&nbsp;<img src="http://<!--{$server_name}-->/utile/img/page_white_acrobat.png" alt="export pdf" /> <a id="lienExportPdf" href="//<!--{$server_name}-->/agenda/calendrier.pdf">pdf</a></span>
	
</div>
<div id="barreFiltre">
	<fieldset>
		<legend>Créer une sélection d'événements</legend>

		<label title="séparés par des ,">Tags</label><input type="text" name="filtreTags" id="filtreTags" value="<!--{$choixTags}-->" /> <a id="sendTags" href="#"><img src="http://<!--{$server_name}-->/utile/img/action_forward.gif" alt="appliquer le filtre" /></a>
				
		<label>Lieu</label>
		<select name="filtreLieu" id="filtreLieu" >
			<option <!--{if $choixLieu==''}-->selected="selected"<!--{/if}--> value="">tous</option>
			<!--{foreach from=$lieux key=id item=lieu}-->
			<option <!--{if $lieu.id_lieu==$choixLieu}-->selected="selected"<!--{/if}--> value="<!--{$lieu.id_lieu}-->" ><!--{$lieu.commune}-->&nbsp;-&nbsp;<!--{$lieu.nom}--></option>
			<!--{/foreach}-->
		</select>
		
		<!--{if $multiCalendriers}-->
		<label>Calendrier</label>
		
		<select name="filtreCalendrier" id="filtreCalendrier" >
			<option <!--{if $choixCalendrier==''}-->selected="selected"<!--{/if}--> value="">tous</option>
			<!--{foreach from=$calendriers key=key item=aCalendrier}-->
				<option <!--{if $choixCalendrier==$aCalendrier.id_calendrier}-->selected="selected"<!--{/if}--> value="<!--{$aCalendrier.id_calendrier}-->" style="background-color:#<!--{$aCalendrier.couleur}-->" ><!--{$aCalendrier.nom}--></option>
			<!--{/foreach}-->
		</select>
		<!--{else}-->
			<input type="hidden" name="filtreCalendrier" value="<!--{$choixCalendrier}-->" id="filtreCalendrier" />
		<!--{/if}--> 
		
	</fieldset>
</div>
<br />