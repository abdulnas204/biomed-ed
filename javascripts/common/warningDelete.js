function warningDelete(url, type) {	
	if (type == "organization") {
		var input = prompt("Warning: This action will delete all information associated with this organization. Examples include:" + '\n' + "   - All users assigned to this organization" + '\n' + "   - Scores and information on modules" + '\n' + "   - Statistics" + '\n' + "Billing information will be retained for a record." + '\n' + '\n' + "This action cannot be undone. If you are sure, type \"Yes\" in the text field below. (Non case-sensitive) Otherwise, press cancel.", "");
	}
	
	if (type == "module") {
		var input = prompt("Warning: This action will delete all information associated with this module. Examples include:" + '\n' + "   - All users assigned to this module will now become un-assigned" + '\n' + "   - All files related to this module" + '\n' + "Statistics and grades for students that have taken this module will not be deleted." + '\n' + '\n' + "This action cannot be undone. If you are sure, type \"Yes\" in the text field below. (Non case-sensitive) Otherwise, press cancel.", "");
	}
	
	if (type == "user") {
		var input = prompt("Warning: This action will delete all information associated with this user. Examples include:" + '\n' + "   - Scores and information on modules" + '\n' + "   - Statistics" + '\n' + '\n' + "This action cannot be undone. If you are sure, type \"Yes\" in the text field below. (Non case-sensitive) Otherwise, press cancel.", "");
	}
	
	if (input == "Yes" || input == "yes" || input == "YES") {
		window.location = url;
	}
}