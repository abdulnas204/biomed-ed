/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

//Script to display the total precentage a user recieved when entering the score of a test question

function calculate (textField, totalPoints, outputField) {
	var input = document.getElementById(textField).value;
	
	if (isNaN(input) || input == "") {
		document.getElementById(outputField).value = "";
	} else {
		var calculate = Math.round((Number(input)/Number(totalPoints)) * 100);
		document.getElementById(outputField).value = calculate + "%";
		
		if (calculate >= 80) {
			document.getElementById(outputField).className = "calculate good";
		}
		
		if (calculate >= 65 && calculate < 80) {
			document.getElementById(outputField).className = "calculate average";
		}
		
		if (calculate < 65) {
			document.getElementById(outputField).className = "calculate bad";
		}
	}
}