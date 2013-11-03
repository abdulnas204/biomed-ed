/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

//A library which contains several useful functions to ease the construction of a lesson plan

//Check all options, set the table row as marked, and enable/disable fields
	function checkAll(trigger, form, fields) {
		var allElements = form.elements.length;
		var total = 0;
		//document.getElementById(displayID).value = "";
		
		for (var count = 0; count <= allElements - 1; count ++) {
			if (form.elements[count].type == "checkbox") {
				var id = form.elements[count].id;
				var rowClass = document.getElementById(id).parentNode.parentNode.parentNode.className;
				var zebraStrip = rowClass.split(" ");
				
				if (trigger.checked == true) {
					form.elements[count].checked = true;
					document.getElementById(id).parentNode.parentNode.parentNode.className = zebraStrip[0] + " marked";
				} else {
					form.elements[count].checked = false;
					document.getElementById(id).parentNode.parentNode.parentNode.className = zebraStrip[0];
				}
				
				var elements = fields.split(",");
				var idNumberPrep = id.split("_");
				var idNumber = idNumberPrep[1];
				
				for (var i = 0; i <= elements.length - 1; i ++) {					
					if (id != "masterAssign" && document.getElementById(elements[i] + "_" + idNumber) != "undefined") {
						if (trigger.checked == true) {
							document.getElementById(elements[i] + "_" + idNumber).disabled = false;
						} else {
							document.getElementById(elements[i] + "_" + idNumber).disabled = true;
						}
					}
				}
				
				/*for (var n = 0; n <= allElements; n ++) {
					if (form.elements[n].type == "checkbox") {				
						if (form.elements[n].checked == true) {
							total++;
							
							document.getElementById(displayID).value = total;
						}
					}
				}*/
			}
		}
	}
	
//Ensure all options are checked
	function allChecked(trigger, form) {
		var allElements = form.elements.length;
		
		for (var count = 0; count <= allElements - 1; count ++) {
			if (form.elements[count].type == "checkbox") {
				if (form.elements[count].checked == false && form.elements[count].id != "masterAssign") {
					document.getElementById("masterAssign").checked = false;
					break;
				} else {
					document.getElementById("masterAssign").checked = true;
				}
			}
		}
	}
	
//Highlight the table row when the checkbox is selected
	function setClass(trigger) {
		var id = trigger.id;
		
		if (document.getElementById(id).type == "checkbox") {
			var rowClass = document.getElementById(id).parentNode.parentNode.parentNode.className;
			var zebraStrip = rowClass.split(" ");
			
			if (trigger.checked == true) {
				document.getElementById(id).parentNode.parentNode.parentNode.className = zebraStrip[0] + " marked";
			} else {
				document.getElementById(id).parentNode.parentNode.parentNode.className = zebraStrip[0];
			}
		} else {
			var rowClass = document.getElementById(id).parentNode.className;
			var zebraStrip = rowClass.split(" ");
			var checkbox = document.getElementById("assign_" + id);
			
			if (checkbox.checked == true) {
				document.getElementById(id).parentNode.className = zebraStrip[0] + " marked";
			} else {
				document.getElementById(id).parentNode.className = zebraStrip[0];
			}
		}
	}
	
//Enable/disable fields as needed
	function toggleFields(trigger, fields) {
		var triggerID = trigger.id;
		var idPrep = triggerID.split("_");
		var id = idPrep[1];
		var elements = fields.split(",");
		
		for (var count = 0; count <= elements.length - 1; count ++) {
			if (document.getElementById(triggerID).checked == true) {
				document.getElementById(elements[count] + "_" + id).disabled = false;
			} else {
				document.getElementById(elements[count] + "_" + id).disabled = true;
			}
		}
	}
	
//Count the number of selected modules
	function totalModules(trigger, form, displayID) {
		var allElements = form.elements.length;
		var total = 0;
		document.getElementById(displayID).value = "";
		
		for (var count = 0; count <= allElements; count ++) {
			if (form.elements[count].type == "checkbox") {				
				if (form.elements[count].checked == true && form.elements[count].id != "masterAssign") {
					total++;
					
					document.getElementById(displayID).value = total;
				}
			}
		}
	}