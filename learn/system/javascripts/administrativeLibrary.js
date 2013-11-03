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

function addValue(tableID) {
	var table = document.getElementById(tableID);
	var newRow = table.insertRow(table.rows.length);
	var previousID = document.getElementById(tableID).getElementsByTagName("tr")[table.rows.length - 2].id;
	var currentID = Number(previousID) + 1;
	newRow.id = currentID;
	newRow.align = "center";
	
	var newCell1 = newRow.insertCell(0);
	newCell1.innerHTML = "<input name='value[]' type='text' id='value" + currentID + "' autocomplete='off' size='50' class='validate[required]' />";
	var newCell2 = newRow.insertCell(1);
	newCell2.innerHTML = "<span class=\"action smallDelete\" onclick=\"deleteObject('items', '" + currentID + "', '2')\">";
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