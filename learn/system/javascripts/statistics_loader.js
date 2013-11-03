/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

jQuery("div.loadStats").click(function() {
	var linkID = this.attr("id");
	var URL = document.location.href;
	
	if (URL.search("?") && URL.search("?") >= 0) {
		var requestURL = URL + "&action=statistics&id=" + linkID;
	} else {
		var requestURL = URL + "?action=statistics&id=" + linkID
	}
	
	$.getJSON(requestURL, function(json) {
		var htmlContent = "<span style=\"border:thin black solid; display:block; width:100%; text-decoration: none;\"><span style=\"background-color:#090; display:block; width:5%; text-decoration: none;\">&nbsp;</span></span>"
		
		$("div#" + linkID).html(htmlContent);
	})
});