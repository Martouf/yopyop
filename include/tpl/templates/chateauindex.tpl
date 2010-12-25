<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="description" content="Un aperçu du vaste monde par Mathieu Despont"/>
		<meta name="keywords" content="yopyop, martouf, sythéticien, hypyop"/>
		<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/chateau.css" media="screen" />
		<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/bigbang_print.css" media="print" />
		<link rel="icon" type="image/png" href="http://<!--{$server_name}-->/utile/img/oeil-favicon.png" />
		
		<link rel="schema.dc" href="http://purl.org/dc/elements/1.1/" />
		<meta name="dc.title" lang="fr" content="Martouf" />
		<meta name="dc.description" lang="fr" content="Un aperçu du vaste monde par Mathieu Despont" />
		<meta name="dc.language" content="fr" />
		<meta name="dc.publisher" content="Mathieu Despont" />
		<meta name="dc.rights" content="Cet article est sous licence Creative Commons Attribution-ShareAlike." />		
		<!--{$additionalHeader}-->
		<script type="text/javascript">
		//<![CDATA[
		var chemin = "//<!--{$server_name}-->/";
		//]]>
		</script>
	
		<title><!--{$file_name}--></title>
	</head>
	<body>
		<div id="dock">
			&nbsp;
		</div>
		<div id="feuille">
			<div id="entete">
				&nbsp;
			</div> <!--entete-->
			<div id="feuille_papier">
				<div id="corps">
					<div id="page">
						<!--{include file="$contenu"}-->
					</div>
					<div id="menu">
						<!--{$menu}-->
					</div>
					<div id="bas" style="clear:both;">&nbsp;</div>
					<div id="easterEggs">
						<img id="nain" src="http://<!--{$server_name}-->/utile/images/theme_chateau/nain-prisonnier.jpg" alt="nain de jardin prisonnier" />
						<img id="fourmi1" class="drag fourmi" src="http://<!--{$server_name}-->/utile/images/theme_chateau/fourmi.png" alt="fourmi" />
						<img id="fourmi2" class="drag fourmi" src="http://<!--{$server_name}-->/utile/images/theme_chateau/fourmi.png" alt="fourmi" />
						<img id="fourmi3" class="drag fourmi" src="http://<!--{$server_name}-->/utile/images/theme_chateau/fourmi.png" alt="fourmi" />
						<img id="fourmi4" class="drag fourmi" src="http://<!--{$server_name}-->/utile/images/theme_chateau/fourmi.png" alt="fourmi" />
						<img id="fourmi5" class="drag fourmi" src="http://<!--{$server_name}-->/utile/images/theme_chateau/fourmi.png" alt="fourmi" />
					</div>
				</div><!--corps-->
			</div><!--feuille_papier-->
		</div><!--feuille-->
		<div id="pied">
			<div id="liensPermanents">
				<a href="http://martouf.ch/blog/news/flux.xml">flux Atom du blog</a> | <a href="http://twitter.com/martouf_vert" title="Martouf sur twitter...">twitter</a> | <a href="http://www.wikio.fr/subscribe?url=http%3A%2F%2Fmartouf.ch%2Fblog%2Fnews%2Fflux.xml"><img src="http://www.wikio.fr/shared/images/add-rss.gif" style="border: none;" alt="http://www.wikio.fr"/></a> | <a rel="license" href="http://creativecommons.org/licenses/by-sa/2.5/ch/deed.fr"><img alt="Creative Commons License" style="border-width:0" src="http://i.creativecommons.org/l/by-sa/2.5/ch/80x15.png" /></a> | <a href="http://martouf.ch/document/document.html" title="résumé de tous les documents" >tous les documents</a> | <a href="http://ou-est-la-girafe.ch" title="où estla girafe?">Où est la girafe?</a> | <a href="http://koudou.ch" title="koudou, un aperçu des scouts neucâtelois">koudou.ch</a>
			</div>
			<img id="champignon1" src="http://<!--{$server_name}-->/utile/images/theme_chateau/champignon.png" alt="champignon" />
			<img id="champignon2" src="http://<!--{$server_name}-->/utile/images/theme_chateau/champignon.png" alt="champignon" />
		</div><!--pied-->
		
		<!--{if $sessionIdPersonne=='1'}-->
			<div id="blocLogin">
				<!--{include file="login_fr.tpl"}-->
			</div>
		<!--{else}-->
			<div id="blocPseudo">
				<!--{$sessionPseudo}-->
			</div>
			<div id="deconnexion">
				<a href="//<!--{$server_name}-->/utile/ajax/login.php?logout">deconnexion</a>
			</div>
		<!--{/if}-->
		
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