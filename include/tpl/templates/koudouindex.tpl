<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="description" content="Koudou - Un aperçu des scouts neuchâtelois"/>
		<meta name="keywords" content="scout, koudou, neuchâtelois, neuchâtel"/>
		<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/koudou.css" media="screen" />
		<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/koudou_print.css" media="print" />
		<!--{$additionalHeader}-->
		<script type="text/javascript">
		//<![CDATA[
		var chemin = "//<!--{$server_name}-->/";
		//]]>
		</script>
	
		<title><!--{$file_name}--></title>
	</head>
	<body>
		<div id="feuille">
			<div id="top">
				<img src="http://<!--{$server_name}-->/utile/images/theme_koudou/texte-koudou.png" alt="koudou" id="texte-koudou" /> 
				<img src="http://<!--{$server_name}-->/utile/images/theme_koudou/scouts.png" alt="quelques scouts" id="scouts" /> 
				<div id="menu">
					<!--{$menu}-->
				</div>
				<!--{if $sessionIdPersonne!='1'}-->
				<div id="blocOutils">
					<!--{include file="outils_edition_fr.tpl"}-->
				</div>
				<!--{/if}-->
			</div> <!--entete-->
			<div id="entete">
				&nbsp;
			</div> <!--entete-->

			<div id="corps">
				<div id="page">
					<!--{include file="$contenu"}-->
				</div>
				<div id="bas" style="clear:both;">&nbsp;</div>
			</div><!--corps-->
		</div><!--feuille-->
		<div id="pied">
			<div id="liensPermanents">
				<a href="http://koudou.ch/blog/news/flux.xml">flux Atom du blog</a> | <a href="http://creativecommons.org/licenses/by-sa/3.0/">2009 Licence Creative Commons by-sa</a> | <a href="http://koudou.ch/document/document.html" title="résumé de tous les documents" >tous les documents</a> | <a href="http://koudou.ch" title="page d'accueil">rechercher</a> | <a href="http://scoutne.ch" title="le site officiel des scouts neuchâtelois">Scouts neuchâtelois</a>
			</div>
			<!--{if $sessionIdPersonne=='1'}-->
				<div id="blocLogin">
					<!--{include file="login_fr.tpl"}-->
				</div>
			<!--{else}-->
				<div id="blocPseudo">
					<a href="//<!--{$server_name}-->/profile/<!--{$sessionIdPersonne}-->-<!--{$sessionPseudo}-->.html" title="Aller à ma page de profile..."><!--{$sessionPseudo}--></a>
				</div>
				<div id="deconnexion">
					<a href="//<!--{$server_name}-->/utile/ajax/login.php?logout">déconnexion</a>
				</div>
			<!--{/if}-->
		</div><!--pied-->
		
		<div id="loading">
			<img src="http://<!--{$server_name}-->/utile/img/loading.gif" alt="loading"/> loading
		</div>
		<script type="text/javascript">
		var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
		document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
		</script>
		<script type="text/javascript">
		try {
		var pageTracker = _gat._getTracker("UA-2757333-10");
		pageTracker._trackPageview();
		} catch(err) {}</script>
	</body>
</html>