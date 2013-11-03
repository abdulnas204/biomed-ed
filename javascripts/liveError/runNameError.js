var validateName = function () {
	var response = document.getElementById('errorWindow').className;
	
	if(response != "contentHide") {
		document.getElementById('errorWindow').className = "contentShow";
	} else {
		document.getElementById('errorWindow').className = "contentHide";
	}
}

var checkName = function (field, url) {
	var enteredName = document.getElementById(field).value;
	window.Spry.Utils.updateContent('errorWindow', url + '.php?checkName=' + enteredName);
}
