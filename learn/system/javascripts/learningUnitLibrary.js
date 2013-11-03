/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

//A javascript library used in the learning unit generator of this site

function toggleInfo() {
	if (document.getElementById("newCategory").className == "contentHide") {
		document.getElementById("newCategory").className = "contentShow";
	} else {
		document.getElementById("newCategory").className = "contentHide";
	}
}

function rollOverTools(div) {
	var togglePrep = document.getElementById(div).className;
	var toggle = togglePrep.split(" ");
	
	if (toggle['0'] == "contentHide") {
		var hide = toggle['1'] + " " + toggle['2'];
		document.getElementById(div).className = hide;
	} else {
		var show = "contentHide " + toggle['0'] + " " + toggle['1'];
		document.getElementById(div).className = show;
	}
}

function edit(elementIDs) {
	document.getElementById("editDisplay_" + elementIDs).className = "contentShow";
	document.getElementById("standardDisplay_" + elementIDs).className = "contentHide";
}

function clearEdit(elementIDs) {
	document.getElementById("editDisplay_" + elementIDs).className = "contentHide";
	document.getElementById("standardDisplay_" + elementIDs).className = "contentShow";
}