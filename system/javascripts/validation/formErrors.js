/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

//Script to validate all form field types, and tie serveral validation technologies together

function uploadCheck () {
	var formName = document.getElementById('validate');
	var extensionInput = "pdf,doc,docx,xls,xlsx,ppt,pptx,txt,rtf,wav,mp3,avi,wmv,flv,mov,mp4,swf";
	var extensionSplit = extensionInput.split(",");
	var possibleExtensions = extensionSplit.length - 1;
	var returnType = false;
	
	for (var count = 0; count <= formName.elements.length; count ++) {
		var type = formName.elements[count].type;
		
		if (type === "file") {				
			var fileCheck = formName.elements[count].value;
			
			if (fileCheck !== "") {
				var extensionPrep = fileCheck.split(".");
				var extension = extensionPrep[extensionPrep.length - 1].toLowerCase();
				
				for (var i in extensionSplit) {		
					if (extension === extensionSplit[i]) {		
						return true;
						break;
					} else {						
						if (i == possibleExtensions) {
							return false;
						}
					}
				}
			} else {
				return false;
			}
		}
	}
}