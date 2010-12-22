<form action="restriction.html?add" method="post" accept-charset="utf-8">

			<div id="restriction">
					les gens du groupe
						
					<select id="selectGroupeUtilisateur" name="id_groupe_utilisateur">
						<option value="">-- groupe d'utilisateur --</option>
						<!--{foreach from=$nomsGroupes key=id_groupe item=groupe}-->	
							<option value="<!--{$groupe.id_groupe}-->"><!--{$groupe.nom}--></option>
						<!--{/foreach}-->
					</select>
					
						
					ne peuvent pas 
					
					<select id="selectRestriction" name="type">
						<option value="">-- restriction --</option>
						<!--{foreach from=$typesRestrictions key=id_restriction item=nom}-->	
							<option value="<!--{$id_restriction}-->"><!--{$nom}--></option>
						<!--{/foreach}-->
					</select>

					 les éléments du groupe
							
					<select id="selectGroupeElement" name="id_groupe_element">
						<option value="">-- groupe d'element --</option>
						<!--{foreach from=$nomsGroupes key=id_groupe item=groupe}-->	
							<option value="<!--{$groupe.id_groupe}-->"><!--{$groupe.nom}--></option>
						<!--{/foreach}-->
					</select>
			</div>

	<p><input type="submit" value="ajoute"></p>
</form>