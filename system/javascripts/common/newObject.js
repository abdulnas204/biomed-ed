/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

//Script to add or remove table rows

function addBlank(tableID) {
	var table = document.getElementById(tableID);
	var newRow = table.insertRow(table.rows.length);
	var previousID = document.getElementById(tableID).getElementsByTagName("tr")[table.rows.length - 2].id;
	var currentID = Number(previousID) + 1;
	newRow.id = currentID;
	newRow.align = "center";
	
	var newCell1 = newRow.insertCell(0);
	newCell1.innerHTML = "<input name='questionValue[]' type='text' id='questionValue" + currentID + "' autocomplete='off' size='50' class='validate[required]' />";
	var newCell2 = newRow.insertCell(1);
	newCell2.innerHTML = "<input name='answerValue[]' type='text' id='answerValue" + currentID + "' autocomplete='off' size='50' class='validate[required]' />";
	var newCell3 = newRow.insertCell(2);
	newCell3.innerHTML = "<span class=\"action smallDelete\" onclick=\"deleteObject('items', '" + currentID + "', '1')\">";
}

function addMatching(tableID) {
	var table = document.getElementById(tableID);
	var newRow = table.insertRow(table.rows.length);
	var previousID = document.getElementById('id').value;
	var currentID = Number(previousID) + 1;
	newRow.id = currentID;
	newRow.align = "center";
	
	var newCell1 = newRow.insertCell(0);
	newCell1.innerHTML = "<textarea name='questionValue[]' id='questionValue" + currentID + "' style='width:350px;' class='validate[required] noEditorMedia editorQuestion questionValue" + currentID + "' /></textarea>";
	var newCell2 = newRow.insertCell(1);
	newCell2.innerHTML = "<textarea name='answerValue[]' id='answerValue" + currentID + "' style='width:350px;' class='validate[required] noEditorMedia editorQuestion answerValue" + currentID + "' /></textarea>";
	var newCell3 = newRow.insertCell(2);
	newCell3.innerHTML = "<span class=\"action smallDelete\" onclick=\"deleteObject('items', '" + currentID + "', '2')\">";
	
	setup('questionValue' + currentID);
	setup('answerValue' + currentID);
	document.getElementById('id').value = currentID;
}

function addMultipleChoice(tableID, cellOneStart, cellOneMiddle, cellOneEnd, cellTwoStart, cellTwoEnd) {
	var table = document.getElementById(tableID);
	var newRow = table.insertRow(table.rows.length);
	var previousID = document.getElementById('id').value;
	var currentID = Number(previousID) + 1;
	var currentValue = table.rows.length;
	newRow.id = currentID;
	newRow.align = "center";
	
	var newCell1 = newRow.insertCell(0);
	newCell1.innerHTML = "<label><input name='choices[]' type='checkbox' id='choice" + currentID + "' value='" + currentID + "' class='validate[required,minCheckbox[1]]'></label>";
	var newCell2 = newRow.insertCell(1);
	newCell2.innerHTML = "<textarea name='values[]' id='value" + currentID + "' style='width:350px;' class='validate[required] noEditorMedia editorQuestion value" + currentID + "' /></textarea>";
	var newCell3 = newRow.insertCell(2);
	newCell3.innerHTML = "<span class=\"action smallDelete\" onclick=\"deleteObject('items', this.parentNode.parentNode.id, '2', true, true)\">";
	
	setup('value' + currentID);
	document.getElementById('id').value = currentID;
}

function addShortAnswer(tableID, cellOneStart, cellOneEnd) {
	var table = document.getElementById(tableID);
	var newRow = table.insertRow(table.rows.length);
	var previousID = document.getElementById(tableID).getElementsByTagName("tr")[table.rows.length - 2].id;
	var currentID = Number(previousID) + 1;
	newRow.id = currentID;
	newRow.align = "center";
	
	var newCell1 = newRow.insertCell(0);
	newCell1.innerHTML = cellOneStart + currentID + cellOneEnd;
	var newCell2 = newRow.insertCell(1);
	newCell2.innerHTML = "<span class=\"action smallDelete\" onclick=\"deleteObject('items', '" + currentID + "', '1', true)\">";
}

function addFile(tableID, cellOneStart, cellOneMiddle, cellOneEnd, totalFiles) {
	var table = document.getElementById(tableID);
	var newRow = table.insertRow(table.rows.length);
	var previousID = document.getElementById(tableID).getElementsByTagName("tr")[table.rows.length - 2].id;
	var currentID = Number(previousID) + 1;
	newRow.id = currentID;
	
	if (table.rows.length <= totalFiles) {
		var newCell1 = newRow.insertCell(0);
		newCell1.innerHTML = cellOneStart + currentID + cellOneMiddle + currentID + cellOneEnd;
		var newCell2 = newRow.insertCell(1);
		newCell2.innerHTML = "<span class=\"action smallDelete\" onclick=\"deleteObject('" + tableID + "', '" + currentID + "', '1', true)\">";
	} else {
		alert("You have reached the maximum number of allowed files for this question.")
	}
}

function deleteObject(tableID, rowID, values, noHeader, shiftValues) {
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
		
		if (shiftValues) {
			var starterRow = Number(rowID) + 1;
			var table = document.getElementById(tableID);
			var totalValues = table.rows.length + 1;
			
			for (var count = starterRow; count <= totalValues; count++) {
				var id = "choice" + count;
				var elementValuePrep = document.getElementById(id).value;
				var elementValue = Number(elementValuePrep) - 1;
				document.getElementById(count).id = elementValue;
				document.getElementById("choice" + count).value = elementValue;
				document.getElementById("choice" + count).id = "choice" + elementValue;
			}
		}
	} else {
		alert("You must have at least " + values + " item(s) in this list");
	}
}