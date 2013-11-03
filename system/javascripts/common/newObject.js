/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

//Script to add or remove table rows

function addBlank(tableID, cellOneStart, cellOneEnd, cellTwoStart, cellTwoEnd) {
	var table = document.getElementById(tableID);
	var newRow = table.insertRow(table.rows.length);
	var previousID = document.getElementById(tableID).getElementsByTagName("tr")[table.rows.length - 2].id;
	var currentID = Number(previousID) + 1;
	newRow.id = currentID;
	newRow.align = "center";
	
	var newCell1 = newRow.insertCell(0);
	newCell1.innerHTML = cellOneStart + currentID + cellOneEnd;
	var newCell2 = newRow.insertCell(1);
	newCell2.innerHTML = cellTwoStart + currentID + cellTwoEnd;
	var newCell3 = newRow.insertCell(2);
	newCell3.innerHTML = "<span class=\"action smallDelete\" onclick=\"deleteObject('items', '" + currentID + "', '1')\">";
}

function addMatching(tableID, cellOneStart, cellOneEnd, cellTwoStart, cellTwoEnd) {
	var table = document.getElementById(tableID);
	var newRow = table.insertRow(table.rows.length);
	var previousID = document.getElementById(tableID).getElementsByTagName("tr")[table.rows.length - 2].id;
	var currentID = Number(previousID) + 1;
	newRow.id = currentID;
	newRow.align = "center";
	
	var newCell1 = newRow.insertCell(0);
	newCell1.innerHTML = cellOneStart + currentID + cellOneEnd;
	var newCell2 = newRow.insertCell(1);
	newCell2.innerHTML = cellTwoStart + currentID + cellTwoEnd;
	var newCell3 = newRow.insertCell(2);
	newCell3.innerHTML = "<span class=\"action smallDelete\" onclick=\"deleteObject('items', '" + currentID + "', '2')\">";
}

function addMultipleChoice(tableID, cellOneStart, cellOneMiddle, cellOneEnd, cellTwoStart, cellTwoEnd) {
	var table = document.getElementById(tableID);
	var newRow = table.insertRow(table.rows.length);
	var previousID = document.getElementById(tableID).getElementsByTagName("tr")[table.rows.length - 2].id;
	var currentID = Number(previousID) + 1;
	var currentValue = table.rows.length;
	newRow.id = currentID;
	newRow.align = "center";
	
	var newCell1 = newRow.insertCell(0);
	newCell1.innerHTML = cellOneStart + currentValue + cellOneMiddle + currentValue + cellOneEnd;
	var newCell2 = newRow.insertCell(1);
	newCell2.innerHTML = cellTwoStart + currentID + cellTwoEnd;
	var newCell3 = newRow.insertCell(2);
	newCell3.innerHTML = "<span class=\"action smallDelete\" onclick=\"deleteObject('items', this.parentNode.parentNode.id, '2', true, true)\">";
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