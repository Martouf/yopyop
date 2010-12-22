<a href="/document/<!--{$document.id_document}-->-<!--{$document.nomSimplifie}-->.html">retour au document</a>
	
<input type="hidden" name="idDocument" value="<!--{$document.id_document}-->" id="idDocument" />

	<div id="document" >	
		<p>
			<label for="nom">nom</label>
			<input type="text" name="nom" value="<!--{$document.nom}-->" id="nom" />

			<label for="datePublication">Date de publication</label>
			<input type="text" name="datePublication" value="<!--{$document.date_publication}-->" id="datePublication" />
		</p>
	
		<div id="blocResume">
				<label for="description">Resumé</label>
			<textarea name="description" id="description" rows="2" cols="80"><!--{$document.description}--></textarea>
		</div>
		
		<div id="blocTags">
			<p>
				<label for="tags" title="séparés par des ,">tags</label><input type="text" name="tags" id="tags" value="<!--{$tags}-->" /> <a id="enregistreTag" href="#">enregistrer</a>
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<label for"access" title="choisir un type d'accès: par restriction ou par exclusivité (dans ce cas indiquer le groupe qui a accès au document)">Accès par</label>
				<select id="access" name="access">
					<option <!--{if $document.access=='2'}-->selected="selected"<!--{/if}--> value="2">restriction</option>
					<option <!--{if $document.access=='1'}-->selected="selected"<!--{/if}--> value="1">exclusivité</option>
				</select>
				
				<label for"groupe_autorise" title="Groupe ayant l'accès exclusif">Groupe</label>
				<select id="groupe_autorise" name="groupe_autorise">
					<option selected="selected" value=""></option>
					<!--{foreach from=$listeGroupeUtilisateur key=id_groupe item=groupe}-->	
						<option <!--{if $document.groupe_autorise==$id_groupe}-->selected="selected"<!--{/if}--> value="<!--{$id_groupe}-->"><!--{$groupe}--></option>
					<!--{/foreach}-->
				</select>
			</p>
		</div>
	
		<div id="blocContenu">
			<textarea name="contenu" id="contenu" rows="50" cols="130"><!--{$document.contenu}--></textarea>
		</div>
	
		<div id="blocMetaDonnees">
			<label for="infoModif">Commentaire sur la modification</label><input type="text" name="infoModif" value="" id="infoModif" />
			<label for="evaluation">Importance</label>
			<select id="evaluation" name="evaluation">
				<option selected="selected" value="0"></option>
				<option value="1">*</option>
				<option value="2">**</option>
				<option value="3">***</option>
				<option value="4">****</option>
				<option value="5">*****</option>
			</select>
		</div>
	</div>
	<div class="blocMetaDonneesBlog">
		<em title="Dernière modification"><!--{$document.dateModification}--></em>
	</div>
	<p><a href="#" onclick="app.saveDocument(); return false;"><img src="/utile/img/save.png" alt="save" /> enregistrer le document</a></p>

<div id="loading">
	<img src="/utile/img/loading.gif" alt="loading"/> loading
</div>
<div id="logAction">
</div>