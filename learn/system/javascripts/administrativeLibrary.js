/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

//A javascript library used in the administration section of this site

function triggerAddition(input) {
	var values = document.getElementsByName('values[]');
	
	if (input == "dropDown" || input == "radio" || input == "checkbox") {
		document.getElementById('addition').className = "contentShow";
		
		for (var count = 0; count <= values.length - 1; count++) {
			values[count].className = "validate[required]";
		}
	} else {
		document.getElementById('addition').className = "contentHide";
		
		for (var count = 0; count <= values.length - 1; count++) {
			values[count].className = "";
		}
	}
	
	if (input == "textField") {
		document.getElementById('suggest').className = "contentShow";
	} else {
		document.getElementById('suggest').className = "contentHide";
	}
}

function changeSelected(input) {
	var selectionInputs = document.getElementsByName("selected[]");
	
	switch(input) {
		case "" : 
		case "textField" : 
		case "textArea" : 
			//Do nothing
			break;
		
		case "dropDown" : 
		case "radio" : 
			for (var count = 0; count <= selectionInputs.length; count++) {
				selectionInputs[count].type = "radio"
			}
			
			break;
			
		case "checkbox" : 
			for (var count = 0; count <= selectionInputs.length; count++) {
				selectionInputs[count].type = "checkbox"
			}
			
			break;
		
		default : 
			alert("Invalid input type");
			break;
	}
}

function addValue(tableID) {
	var fieldType = document.getElementById("fieldType").value;
	var table = document.getElementById(tableID);
	var newRow = table.insertRow(table.rows.length);
	var previousID = document.getElementById(tableID).getElementsByTagName("tr")[table.rows.length - 2].id;
	var currentID = Number(previousID) + 1;
	newRow.id = currentID;
	newRow.align = "center";
	
	var newCell1 = newRow.insertCell(0);
	
	if (fieldType == "checkbox") {
		newCell1.innerHTML = "<label><input name='selected[]' type='checkbox' id='selected_" + currentID + "' value='" + currentID + "'></label>";
	} else {
		newCell1.innerHTML = "<label><input name='selected[]' type='radio' id='selected_" + currentID + "' value='" + currentID + "'></label>";
	}
	
	var newCell2 = newRow.insertCell(1);
	newCell2.innerHTML = "<input name='values[]' type='text' id='value" + currentID + "' autocomplete='off' size='50' class='validate[required]' />";
	var newCell3 = newRow.insertCell(2);
	newCell3.innerHTML = "<span class=\"action smallDelete\" onclick=\"deleteObject('items', '" + currentID + "', '2')\">";
}

function deleteObject(tableID, rowID, values, noHeader) {
	var table = document.getElementById(tableID);
	var row = document.getElementById(rowID);
	var allowedRows = Number(values);
	
	if (noHeader == true) {
		var addRow = 0;
	} else {
		var addRow = 1;
	}
	
	if (table.rows.length > allowedRows + addRow) {	
		row.parentNode.removeChild(row);
	} else {
		alert("You must have at least " + values + " item(s) in this list");
	}
}