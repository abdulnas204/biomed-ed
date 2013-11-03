/*
This code was courtesy of jQueryUI. This code can be found from: http://jqueryui.com/demos/sortable/portlets.html.
  
A script to style portlets with a jQuery skin.
*/

$(document).ready(function() {
	$('.portlet')
	//To add a container around the portlet: .addClass('ui-widget ui-widget-content ui-helper-clearfix ui-corner-all')
	.find('.portlet-header')
	  .addClass('ui-widget-header ui-corner-all')
	  .end()
	.find('.portlet-content');
});