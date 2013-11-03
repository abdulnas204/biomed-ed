/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

//Script to warn a user prior to logout, and give the option to entend the session

/*function entendTime() {
	var overDue = setTimeout("timedOut()", 3000);
	var extendCheck = confirm("Your login session will expire in 10 minutes. Click \'OK\' to extend it.");

	if (extendCheck) {
		window.Spry.Utils.updateContent('null', location.href + '?extend=true');
		var newTimeout = setTimeout("entendTime()", 3000);
		clearTimeout(overDue)
		var overDue = setTimeout("timedOut()", 6000);
		setTimeout("entendTime()", 3000);
	}
}

function timedOut() {
	var extendOverdue = confirm("Your login session has expired. Click \'OK\' to log in again.");
	
	if (extendOverdue) {
		showPopWin('http://google.com', 600, 400, null);
	}
	
	return false;
}

setTimeout("entendTime()", 3000);*/