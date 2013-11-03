function addBlank(tableID, cellOneStart, cellOneEnd, cellTwoStart, cellTwoEnd) {
	var table = document.getElementById(tableID);
	var newRow = table.insertRow(table.rows.length);
	var previousID = document.getElementById(tableID).getElementsByTagName("tr")[tbl.rows.length - 2].id;
	var currentID = Number(previousID) + 1;
	newRow.id = currentID;
	newRow.align = "center";
	
	var newCell1 = newRow.insertCell(0);
	newCell1.innerHTML = cellOneStart + currentID + endHTML;
	var newCell2 = newRow.insertCell(1);
	newCell2.innerHTML = cellTwoStart + currentID + cellTwoEnd;
}

function deleteLastRow(tableID) {
	var tbl = document.getElementById(tableID);
	if (tbl.rows.length > 2) tbl.deleteRow(tbl.rows.length - 1);
}