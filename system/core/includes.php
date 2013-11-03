<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: Novemeber 27th, 2010
Last updated: Feburary 13th, 2011

This script contains functions which will construct the 
JavaScript and CSS callers for client-side modules.
*/

/*
Widgets
---------------------------------------------------------
*/

//TinyMCE advanced
	function tinyMCEAdvanced () {
		global $root;
		
		return "<script type=\"text/javascript\" src=\"" . $root . "system/tiny_mce/tiny_mce.js\"></script>
<script type=\"text/javascript\" src=\"" . $root . "system/tiny_mce/plugins/AtD/editor_plugin.js\"></script>
<script type=\"text/javascript\" src=\"" . $root . "system/javascripts/common/tiny_mce_advanced.htm\"></script>";
	}

//TinyMCE simple
	function tinyMCESimple () {
		global $root;
		
		return "<script type=\"text/javascript\" src=\"" . $root . "system/tiny_mce/tiny_mce.js\"></script>
<script type=\"text/javascript\" src=\"" . $root . "system/tiny_mce/plugins/AtD/editor_plugin.js\"></script>
<script type=\"text/javascript\" src=\"" . $root . "system/javascripts/common/tiny_mce_simple.htm\"></script>";
	}
	
//Auto-suggest
	function autoSuggest() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/autoSuggest/runAutoSuggest.js\" type=\"text/javascript\"></script>
<script src=\"" . $root . "system/javascripts/autoSuggest/autoSuggestOptions.js\" type=\"text/javascript\"></script>
<script src=\"" . $root . "system/javascripts/autoSuggest/autoSuggestCore.js\" type=\"text/javascript\"></script>
<link rel=\"stylesheet\" href=\"" . $root . "system/styles/common/autoSuggest.css\" type=\"text/css\">";
	}
	
//Include a calendar popup script
	function calendar() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/common/datePicker.js\" type=\"text/javascript\"></script>
<link rel=\"stylesheet\" href=\"" . $root . "system/styles/common/datePicker.css\" type=\"text/css\">";
	}
	
//Include a session refresher
	function sessionControl() {
		global $root;
		
		return "<script type=\"text/javascript\" src=\"" . $root . "system/javascripts/ajaxLibraries/jQuery_1.4.4.js\"></script>
<script type=\"text/javascript\" src=\"" . $root . "system/javascripts/ajaxLibraries/jQuery_UI_1.8.9.js\"></script>
<script type=\"text/javascript\" src=\"" . $root . "system/javascripts/session_control.htm\"></script>
<link rel=\"stylesheet\" href=\"" . $root . "system/styles/ajaxLibraries/jQuery_UI_1.8.9.css\" type=\"text/css\">";
	}
	
/*
Pre-processor scripts
---------------------------------------------------------
*/
	
//Form validator
	function validate () {
		global $root;
		
		return "<link rel=\"stylesheet\" href=\"" . $root . "system/styles/validation/validatorStyle.css\" type=\"text/css\">
<script src=\"" . $root . "system/javascripts/validation/validatorCore.js\" type=\"text/javascript\"></script>
<script src=\"" . $root . "system/javascripts/validation/validatorOptions.js\" type=\"text/javascript\"></script>
<script src=\"" . $root . "system/javascripts/validation/runValidator.js\" type=\"text/javascript\"></script>
<script src=\"" . $root . "system/javascripts/validation/formErrors.js\" type=\"text/javascript\"></script>";
	}
	
//Live submitter
	function liveSubmit() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/liveSubmit/submitterCore.js\" type=\"text/javascript\"></script>
<script src=\"" . $root . "system/javascripts/liveSubmit/runSubmitter.js\" type=\"text/javascript\"></script>";
	}
	
	//Include the custom checkbox script
	function customVisible() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/customCheckbox/runVisible.js\" type=\"text/javascript\"></script>";
	}
	
	function customCheckbox() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/customCheckbox/runCheckbox.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a show or hide script
	function showHide() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/common/showHide.js\" type=\"text/javascript\"></script>";
	}
	
	//Include an enable/disable script
	function enableDisable() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/common/enableDisable.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a new object script
	function newObject() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/common/newObject.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a script to calculate the score of a question
	function calculate() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/common/scoreCalculate.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a live data script
	function liveData() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/liveData/liveDataCore.js\" type=\"text/javascript\"></script><script src=\"" . $root . "system/javascripts/liveData/runLiveData.js\" type=\"text/javascript\"></script><script src=\"" . $root . "system/javascripts/liveData/pageData.htm\" type=\"text/javascript\"></script>";
	}
	
	//Include an option transfer script
	function optionTransfer() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/common/optionTransfer.js\" type=\"text/javascript\"></script>";
	}
	
	//Include a tabbed panel script
	function tabbedPanels() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/tabbedPanels/tabbedPanels.js\" type=\"text/javascript\"></script><link rel=\"stylesheet\" href=\"" . $root . "system/styles/common/tabbedPanels.css\" type=\"text/css\">";
	}
	
	//Include the assignment javascript library
	function assignmentLibrary() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/common/assignmentLibrary.js\" type=\"text/javascript\"></script>";
	}
	
	//Include the table reordering script
	function tableReorder() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/tableReorder/runReorder.js\" type=\"text/javascript\"></script>";
	}
	
	//Include uploadify javascripts
	function uploadify() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/uploader/uploaderCore.js\" type=\"text/javascript\"></script><script src=\"" . $root . "system/javascripts/uploader/swfobject.js\" type=\"text/javascript\"></script><script src=\"" . $root . "system/javascripts/uploader/runUploader.js\" type=\"text/javascript\"></script><link rel=\"stylesheet\" href=\"" . $root . "system/styles/common/uploader.css\" type=\"text/css\">";
	}
	
	//Include a javascript to print the contents of a container
	function printContents() {
		global $root;
		
		return "<script src=\"" . $root . "system/javascripts/common/print.htm\" type=\"text/javascript\"></script>";
	}
?>