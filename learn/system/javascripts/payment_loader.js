/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

var interval = self.setInterval("paymentStatus()", 1000);

function paymentStatus() {
	if (document.location.href.search("=") >= 0) {
		var URLaddtion = "&action=checkStatus";
	} else {
		var URLaddtion = "?action=checkStatus";
	}
	
	$.get(document.location.href + URLaddtion, function(data) {
		if (data != "not processed") {
			$('#status').empty();
			$('#status').html(data);
			clearInterval(interval);
		}
	})	
}
