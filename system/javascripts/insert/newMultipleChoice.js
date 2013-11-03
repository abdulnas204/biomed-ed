function appendRow(tableID, startHTML, middleHTML, endHTML) {
	var oRows = document.getElementById(tableID).getElementsByTagName('tr');
	var iRowCount = oRows.length+1;
	var tbl = document.getElementById(tableID);
	var newRow = tbl.insertRow(tbl.rows.length);
	var newCell = newRow.insertCell(0);
	var HTML = startHTML + iRowCount + middleHTML + iRowCount + endHTML;
	newCell.innerHTML = HTML;
}

function deleteLastRow(tableID) {
	var tbl = document.getElementById(tableID);
	if (tbl.rows.length > 2) tbl.deleteRow(tbl.rows.length - 1);
}