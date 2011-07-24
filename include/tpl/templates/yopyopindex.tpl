<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN"
	"http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">

<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
		<meta name="description" content="YopYop - partageons nos objets..."/>
		<meta name="keywords" content="échange, partage, consommation, collaborative, location, kong"/>
		<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/yopyop.css" media="screen" />
		<link type="text/css" rel="stylesheet" href="http://<!--{$server_name}-->/utile/css/yopyop_print.css" media="print" />
		<link rel="icon" type="image/png" href="http://<!--{$server_name}-->/utile/images/theme_yopyop/singe.png" />
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
				<img src="http://<!--{$server_name}-->/utile/images/theme_yopyop/texte-yopyop-jaune.png" alt="yopyop" id="texte-yopyop" /> 
				<div id="nuage">
					&nbsp;
				</div>
				<div id="menu">
					<!--{$menu}-->
				</div>
				<div id="singe">
					&nbsp;
				</div>
				<!--{if $sessionIdPersonne!='1'}-->
				<div id="blocOutils">
					<!--{include file="outils_edition_fr.tpl"}-->
				</div>
				<!--{/if}-->
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
			</div>
			<div id="corps">
				<div id="page">
					<!--{include file="$contenu"}-->
				</div>
				<div id="bas" style="clear:both;">&nbsp;</div>
			</div><!--corps-->

		<div id="pied">
			<div id="blocMontagnes">
				&nbsp;
			</div>
			<div id="bateauPirate" title="incline ton ordinateur pour faire voguer le bateau...">
				&nbsp;
			</div>
			<div id="poulpe" title="bouh...">
				&nbsp;
			</div>
			<div id="tresor" title="le trésor des pirates...">
				&nbsp;
			</div>
			<div id="liensPermanents">
				<a href="//<!--{$server_name}-->/blog/news/">blog</a> | <a href="//<!--{$server_name}-->/blog/news/flux.xml">flux Atom du blog</a> | <a href="http://creativecommons.org/licenses/by-sa/3.0/deed.fr">2011 Licence Creative Commons by-sa</a>
			</div>
		
		</div><!--pied-->
	</div><!--feuille-->
		<div id="loading">
			<img src="http://<!--{$server_name}-->/utile/img/loading.gif" alt="loading"/> loading
		</div>
		<script type="text/javascript">

		  var _gaq = _gaq || [];
		  _gaq.push(['_setAccount', 'UA-2757333-13']);
		  _gaq.push(['_trackPageview']);

		  (function() {
		    var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
		    ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
		    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
		  })();

		</script>
		<script type="text/javascript" src="http://<!--{$server_name}-->/utile/js/pirate.js"></script>
	</body>
</html>