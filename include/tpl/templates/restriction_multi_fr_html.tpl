<div id="restriction" >
	<label>Restrictions</label>
	<!--{foreach from=$restrictions key=key item=aRestriction}-->
		<p id="restriction_<!--{$aRestriction.id_restriction}-->">
			les gens du groupe <strong><!--{$aRestriction.nomGroupeUtilisateur.nom}--></strong> ne peuvent pas <strong><!--{$aRestriction.nomRestriction}--></strong> les éléments du groupe <strong><!--{$aRestriction.nomGroupeElement.nom}--></strong>
		</p>
	<!--{/foreach}-->
</div>
