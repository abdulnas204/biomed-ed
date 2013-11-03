tinyMCE.init({
		// General options
		mode : "textareas",
		theme : "advanced",
		skin : "o2k7",
		skin_variant : "silver",
		plugins : "inlinepopups,spellchecker,tabfocus",
		spellchecker_languages : "+English=en",
		tab_focus : ':prev,:next',

		theme_advanced_buttons1 : "bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright, justifyfull,|,bullist,numlist,|,undo,redo,link,unlink,spellchecker",
		theme_advanced_buttons2 : "",
		theme_advanced_buttons3 : "",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true
	});
$(function() {
	var validator = $("#validate").submit(function() {
		tinyMCE.triggerSave();
	}).validate({
		rules: {
			title: "required",
			content: "required"
		},
		errorPlacement: function(label, element) {
			if (element.is("textarea")) {
				label.insertAfter(element.next());
			} else {
				label.insertAfter(element)
			}
		}
	});
	validator.focusInvalid = function() {
		if( this.settings.focusInvalid ) {
			try {
				var toFocus = $(this.findLastActive() || this.errorList.length && this.errorList[0].element || []);
				if (toFocus.is("textarea")) {
					tinyMCE.get(toFocus.attr("id")).focus();
				} else {
					toFocus.filter(":visible").focus();
				}
			} catch(e) {
			}
		}
	}
})