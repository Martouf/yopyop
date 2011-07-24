<h2 class="barre violet">Donner des kong à <!--{$destinataire.surnom}--></h2>

<form action="transaction.html?add" method="post" accept-charset="utf-8">

	<div id="transaction" style="width: 50%; margin: auto;">
		<p>
			Donner <input type="text" name="inputMontant" value="0" id="inputMontant" /> kong à <a href="//<!--{$server_name}-->/profile/<!--{$destinataire.id_personne}-->-<!--{$destinataire.surnom}-->.html"><!--{$destinataire.surnom}--></a>, (<!--{$destinataire.prenom}--> <!--{$destinataire.nom}-->)
		</p>
		<p>
			<label for="libelle">Libellé</label> <input type="text" name="inputLibelle" value="" id="inputLibelle" size="50" />
		</p>
		<p>
			<span class="info">Le montant sera transféré de votre compte (<a href="//<!--{$server_name}-->/profile/<!--{$personne.id_personne}-->-<!--{$personne.surnom}-->.html"><!--{$personne.surnom}--></a>) à celui de <a href="//<!--{$server_name}-->/profile/<!--{$destinataire.id_personne}-->-<!--{$destinataire.surnom}-->.html"><!--{$destinataire.surnom}--></a>.</span>
		</p>
		<p>
			Votre fortune actuelle est de <!--{$personne.fortune}--> kong.
		</p>
		<input type="hidden" name="inputFortune" value="<!--{$personne.fortune}-->" id="inputFortune" />
		<input type="hidden" name="inputIdSource" value="<!--{$personne.id_personne}-->" id="inputIdSource" />
		<input type="hidden" name="inputIdDestinataire" value="<!--{$destinataire.id_personne}-->" id="inputIdDestinataire" />
		
		<p style="text-align:center"><input type="submit" value="effectuer la transaction"></p>
		
	</div>

</form>
