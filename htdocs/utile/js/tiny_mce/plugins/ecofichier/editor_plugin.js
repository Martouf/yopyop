/**
 * $Id: editor_plugin_src.js 677 2008-03-07 13:52:41Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright � 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.EcoFichierPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceEcoFichier', function() {
				// Internal image object like a flash placeholder
				if (ed.dom.getAttrib(ed.selection.getNode(), 'class').indexOf('mceItem') != -1)
					return;

				ed.windowManager.open({
				//	file : url + '/image.htm',
					file : '//' + chemin + '/fichier/fichier.html?newfichier&theme=basic',
					width : 480 + parseInt(ed.getLang('advimage.delta_width', 0)),
					height : 385 + parseInt(ed.getLang('advimage.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('file', {
				title : 'uploader un fichier...',
				image : '//' + chemin + '/utile/img/export.png',
				cmd : 'mceEcoFichier'
			});
		},

		getInfo : function() {
			return {
				longname : 'Ecodev fichier',
				author : 'ecodev s�rl',
				authorurl : 'http://ecodev.ch',
				infourl : 'http://ecodev.ch',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ecofichier', tinymce.plugins.EcoFichierPlugin);
})();