function appendRow(tableID, startHTML, endHTML) {
	var oRows = document.getElementById(tableID).getElementsByTagName('tr');
	var iRowCount = oRows.length+1;
	var tbl = document.getElementById(tableID);
	var newRow = tbl.insertRow(tbl.rows.length);
	var newCell = newRow.insertCell(0);
	var HTML = startHTML + iRowCount + endHTML;
	newCell.innerHTML = HTML;
}

function deleteLastRow(tableID) {
	var tbl = document.getElementById(tableID);
	if (tbl.rows.length > 1) tbl.deleteRow(tbl.rows.length - 1);
}