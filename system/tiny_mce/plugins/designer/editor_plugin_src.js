/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

(function() {
	tinymce.PluginManager.requireLangPack('designer');
	
	tinymce.create('tinymce.plugins.DesignerPlugin', {
		init : function(ed, url) {
			// Register commands
			ed.addCommand('mceDesigner', function() {
				ed.windowManager.open({
					file : url + '/design.htm',
					width : 320 + parseInt(ed.getLang('designer.delta_width', 0)),
					height : 260 + parseInt(ed.getLang('designer.delta_height', 0)),
					inline : 1
				}, {
					plugin_url : url
				});
			});

			// Register buttons
			ed.addButton('designer', {title : 'designer.desc', cmd : 'mceDesigner', image : url + '/img/icon.png'});
		},

		getInfo : function() {
			return {
				longname : 'Page Designer Tools',
				author : 'Oliver Spryn',
				authorurl : 'http://apexdevelopment.businesscatalyst.com',
				infourl : 'http://apexdevelopment.businesscatalyst.com',
				version : "1.0"
			};
		}
	});

	// Register plugin
	tinymce.PluginManager.add('designer', tinymce.plugins.DesignerPlugin);
})();