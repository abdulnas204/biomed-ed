/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

//Update the number of questions avaliable for a test during configuration

function updateDataSet(objectID) {
	var selection = document.getElementById('questions');
	selection.disabled = true;
	
	var object = document.getElementById(objectID);
	var parameters = "";
	
	if (document.getElementById('parameters').value !== "") {
		var valuePrep = document.getElementById('parameters').value.replace("&amp;", "&");
		var currentParameters = valuePrep.split("&");
		
		for (var count = 0; count <= currentParameters.length - 1; count ++) {
			var currentIdentifier = currentParameters[count].split("=");
			var identifier = currentIdentifier[0];
			
			if (object.type == "checkbox") {
				var compare = object.name;
			} else {
				var compare = object.id;
			}
			
			if (identifier.toLowerCase() == compare.toLowerCase()) {
				//Do nothing, as this parameter needs stripped out
			} else {
				parameters += currentIdentifier[0] + "=" + escape(unescape(currentIdentifier[1])) + "&amp;";
			}
		}
	}
	
	document.getElementById('parameters').value = parameters.substring(0, parameters.length-4);
	
	switch (object.type) {
		case "select-one" : 
		case "radio" : 
		case "text" : 
			if (object.value == "") {
				var returnValue = "";
			} else {
				var returnValue = object.id + "=" + escape(object.value);
			}
			
			break;
			
		case "checkbox" : 
			var returnValue = object.name + "=";
			var checks = document.getElementsByName(object.name);
			
			for (var count = 0; count <= checks.length - 1; count ++) {
				if (checks[count].checked) {
					returnValue += escape(checks[count].value) + ",";
				}
			}
			
			if (returnValue == object.name + "=") {
				returnValue = "";
			}
			
			returnValue = returnValue.substring(0, returnValue.length-1);
			
			break;
	}
	
	if (returnValue !== "") {
		document.getElementById('parameters').value = document.getElementById('parameters').value + returnValue;
	} else {
		document.getElementById('parameters').value = document.getElementById('parameters').value.substring(0, document.getElementById('parameters').value.length-1);
	}
	
	dsQuestions.url = window.location.href + "&data=xml&" + document.getElementById('parameters').value;
	dsQuestions.loadData();
}