function errorsOnSubmit(form) {
	if (!Spry.Widget.Form.validate(form)) {
		var errorBox= document.getElementById('errorBox');
		errorBox.style.display = 'block';
		errorBox.style.border = '1px solid #CC3333';
		errorBox.style.color = '#CC3333';
		errorBox.style.width = '410px';
		return false;
	} else {
		var errorBox= document.getElementById('errorBox');
		errorBox.style.display = 'none';
		return true;
	}
}
