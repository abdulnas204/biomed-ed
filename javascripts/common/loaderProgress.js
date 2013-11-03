function validateFiles (formName, fieldName) {
	if (document.fileResponse.answer.value == "") {
		var errorBox = document.getElementById('errorBox');
		errorBox.style.display = 'block';
		errorBox.style.border = '1px solid #CC3333';
		errorBox.style.color = '#CC3333';
		errorBox.style.width = '410px';
		return false;
	} else {
		if (!Spry.Widget.Form.validate(fileResponse)) {
			var uploadBox = document.getElementById('progress');
			uploadBox.class = 'hiddenDiv';
			alert("1");
			return false;
		} else {
			var uploadBox = document.getElementById('progress');
			uploadBox.class = 'contentShow';
			alert("2");
			return true;
		}
	}
}