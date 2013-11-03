function warningDelete(url, type) {	
	if (type == "organization") {
		var input = prompt("Warning: This action will delete this organization and all of its data. Exmaples include:" + '\n\n' + "   - All assigned users will be deleted" + '\n' + "   - Related files" + '\n' + "   - Statistics" + '\n\n' + "Billing information will be retained for a record." + '\n\n' + "This action cannot be undone. If you are sure, type \"Yes\" in the text field below. Otherwise, press cancel.", "");
	}
	
	if (type == "module") {
		var input = prompt("Warning: This action will delete all information associated with this module. Examples include:" + '\n\n' + "   - All users assigned will now become un-assigned" + '\n' + "   - All files related to this module" + '\n' + "   - Statistics" + '\n\n' + "Grades for students that have taken this module will not be deleted." + '\n\n' + "This action cannot be undone. If you are sure, type \"Yes\" in the text field below. Otherwise, press cancel.", "");
	}
	
	if (type == "user") {
		var input = prompt("Warning: This action will delete all information associated with this user. Examples include:" + '\n\n' + "   - Grades" + '\n' + "   - All realted files" + '\n' + "   - Statistics" + '\n\n' + "This action cannot be undone. If you are sure, type \"Yes\" in the text field below. Otherwise, press cancel.", "");
	}
	
	var lowerInput = input.toLowerCase();
	
	if (lowerInput == "yes") {
		window.location = url;
	}
}