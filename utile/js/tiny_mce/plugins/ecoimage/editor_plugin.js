/**
 * $Id: editor_plugin_src.js 677 2008-03-07 13:52:41Z spocke $
 *
 * @author Moxiecode
 * @copyright Copyright © 2004-2008, Moxiecode Systems AB, All rights reserved.
 */

(function() {
	tinymce.create('tinymce.plugins.EcoImagePlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceEcoImage', function() {
				// Internal image object like a flash placeholder
				if (ed.dom.getAttrib(ed.selection.getNode(), 'class').indexOf('mceItem') != -1)
					return;

				ed.windowManager.open({
				//	file : url + '/image.htm',
					file : '/photo/photo.html?newimage&theme=basic',
					width : 480 + parseInt(ed.getLang('advimage.delta_width', 0)),
					height : 385 + parseInt(ed.getLang('advimage.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('image', {
				title : 'uploader une image...',
				cmd : 'mceEcoImage'
			});
		},

		getInfo : function() {
			return {
				longname : 'Ecodev image',
				author : 'ecodev sàrl',
				authorurl : 'http://ecodev.ch',
				infourl : 'http://ecodev.ch',
				version : tinymce.majorVersion + "." + tinymce.minorVersion
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('ecoimage', tinymce.plugins.EcoImagePlugin);
})();