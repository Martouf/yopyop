	<div id="document" >	
		<p>
			<label for="nom">Nom</label> <input type="text" name="nom" value="" id="nom" />
		</p>
	
		<div id="blocResume">
				<label for="description">Resumé</label>
			<textarea name="description" id="description" rows="2" cols="80"></textarea>
		</div>
				
		<div id="blocTags">
			<p>
				<label for="tags" title="séparés par des ,">tags</label><input type="text" name="tags" id="tags" value="<!--{$tags}-->" />
				&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
				<label for"access" title="choisir un type d'accès: par restriction ou par exclusivité (dans ce cas indiquer le groupe qui a accès au document)">Accès par</label>
				<select id="access" name="access">
					<option value="2">restriction</option>
					<option value="1">exclusivité</option>
				</select>
				
				<label for"groupe_autorise" title="Groupe ayant l'accès exclusif">Groupe</label>
				<select id="groupe_autorise" name="groupe_autorise">
					<option selected="selected" value=""></option>
					<!--{foreach from=$listeGroupeUtilisateur key=id_groupe item=groupe}-->	
						<option value="<!--{$id_groupe}-->"><!--{$groupe}--></option>
					<!--{/foreach}-->
				</select>
			</p>
		</div>
		
		
	
		<div id="blocContenu">
			<textarea name="contenu" id="contenu" rows="50" cols="130"></textarea>
		</div>
	
		<div id="blocMetaDonnees">
			<label for="infoModif">Commentaire sur la modification</label><input type="text" name="infoModif" value="" id="infoModif" />
			<label for="evaluation">Importance</label>
			<select id="evaluation" name="evaluation">
				<option value="0"></option>
				<option value="1">*</option>
				<option value="2">**</option>
				<option selected="selected" value="3">***</option>
				<option value="4">****</option>
				<option value="5">*****</option>
			</select>
		</div>
	</div>

	<p><a href="#" id="createDocument"><img src="http://<!--{$server_name}-->/utile/img/save.png" alt="save" /> enregistrer le document</a></p>