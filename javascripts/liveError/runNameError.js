var validateName = function () {
	var response = document.getElementById('errorWindow').className;
	
	if(response != "contentHide") {
		document.getElementById('errorWindow').className = "contentShow";
	} else {
		document.getElementById('errorWindow').className = "contentHide";
	}
}

var checkName = function (form, field) {
	var enteredName = document.getElementById(form).name.value;
	window.Spry.Utils.updateContent('errorWindow', 'lesson_settings.php?checkName=' + enteredName);
}
