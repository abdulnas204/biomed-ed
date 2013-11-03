tinyMCEPopup.requireLangPack();

var DesignerDialog = {
	init : function(ed) {
		tinyMCEPopup.resizeToInnerSize();
	},

	insert : function(text) {
		tinyMCEPopup.execCommand('mceInsertContent', false, text);
		tinyMCEPopup.close();
	}
};

tinyMCEPopup.onInit.add(DesignerDialog.init, DesignerDialog);
