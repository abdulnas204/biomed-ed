/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

function enroll(object) {
	$.ajax({
		type : 'POST',
		url : 'enroll/enroll.php',
		data : {'enroll' : object},
		success : function(data) {
			if (data == "success") {
				$("a#" + object).parent().empty().html('<span class="notAssigned">Enrolled</span>');
			}
		}
	});
}