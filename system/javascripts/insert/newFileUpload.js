function appendRow(tableID, startHTML, middleHTML, endHTML, totalFiles, buttonID) {
	var oRows = document.getElementById(tableID).getElementsByTagName('tr');
	var iRowCount = oRows.length+1;
	var tbl = document.getElementById(tableID);
	
	if (tbl.rows.length != totalFiles) {
		var newRow = tbl.insertRow(tbl.rows.length);
		var newCell = newRow.insertCell(0);
		var HTML = startHTML + iRowCount + middleHTML + iRowCount + endHTML;
		newCell.innerHTML = HTML;
	}

//Repeat to test the new number of rows	
	var oRows = document.getElementById(tableID).getElementsByTagName('tr');
	var iRowCount = oRows.length+1;
	var tbl = document.getElementById(tableID);
	
	if (tbl.rows.length == totalFiles) {
		document.getElementById(buttonID).disabled = true;
	} else {
		document.getElementById(buttonID).disabled = false;
	}
}

function deleteLastRow(tableID, buttonID) {
	var tbl = document.getElementById(tableID);
	if (tbl.rows.length > 1) tbl.deleteRow(tbl.rows.length - 1);
	
	document.getElementById(buttonID).disabled = false;
}