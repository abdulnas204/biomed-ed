YAHOO.util.Event.on(window, 'load', function(){
	// Create the dialog
	var mySimpleDialog = new YAHOO.widget.SimpleDialog("dlg", { 
		  width: "50em", 
		  effect:{effect:YAHOO.widget.ContainerEffect.FADE,
			  duration:0.25
		}, 
		  fixedcenter:true,
		modal:true,
		visible:false,
		draggable:false 
	});
	//function openModal(header, message) {
		mySimpleDialog.setHeader("Warning!");
		mySimpleDialog.setBody("This question is currently located in the question bank. Once you edit this question, it will no long be linked to the question bank, but will be considered its own seperate question inside this test. To edit this question in the question bank, you must go to the question bank and edit it. Do you want to edit this inside of this test?<br /><br /><div class=\"underlay\"><div align=\"center\"><input name=\"editTest\" id=\"hide\" onclick=\"MM_goToURL('parent','index.php');return document.MM_returnValue\" value=\"Edit in this test\" type=\"button\">&nbsp;&nbsp;&nbsp;&nbsp;<input name=\"editBank\" id=\"hide\" onclick=\"MM_goToURL('parent','index.php');return document.MM_returnValue\" value=\"Edit in the bank\" type=\"button\">&nbsp;&nbsp;&nbsp;&nbsp;<input name=\"cancel\" id=\"hide\" onclick=\"MM_goToURL('parent','javascript:void;');return document.MM_returnValue\" value=\"Cancel\" type=\"button\" ></div></div>");
	//}
	mySimpleDialog.cfg.setProperty("icon",YAHOO.widget.SimpleDialog.ICON_WARN);
	mySimpleDialog.render(document.body);
	// Attach listener to text input
	YAHOO.util.Event.on('modal', 'click', function(e){
		mySimpleDialog.show();
	});
	
	YAHOO.namespace("example.container");

function init() {
	
	// Define various event handlers for Dialog
	var handleYes = function() {
		alert("You clicked yes!");
		this.hide();
	};
	var handleNo = function() {
		this.hide();
	};

	// Instantiate the Dialog
	YAHOO.example.container.simpledialog1 = new YAHOO.widget.SimpleDialog("simpledialog1", 
																			 { width: "300px",
																			   fixedcenter: true,
																			   visible: false,
																			   draggable: false,
																			   close: true,
																			   text: "Do you want to continue?",
																			   icon: YAHOO.widget.SimpleDialog.ICON_HELP,
																			   constraintoviewport: true,
																			   buttons: [ { text:"Yes", handler:handleYes, isDefault:true },
																						  { text:"No",  handler:handleNo } ]
																			 } );
	YAHOO.example.container.simpledialog1.setHeader("Are you sure?");
	
	// Render the Dialog
	YAHOO.example.container.simpledialog1.render("container");

	YAHOO.util.Event.addListener("show", "click", YAHOO.example.container.simpledialog1.show, YAHOO.example.container.simpledialog1, true);
	YAHOO.util.Event.addListener("hide", "click", YAHOO.example.container.simpledialog1.hide, YAHOO.example.container.simpledialog1, true);

}



});


/*var mySimpleDialog = new YAHOO.widget.SimpleDialog("dlg", {width: "20em", effect:{effect:YAHOO.widget.ContainerEffect.FADE, duration:0.25}});
function openModal (message) {
	YAHOO.util.Event.on(window, 'load', settings());
	mySimpleDialog.setHeader("Warning!");
	mySimpleDialog.setBody(message);
	mySimpleDialog.cfg.setProperty("icon",YAHOO.widget.SimpleDialog.ICON_WARN);
	mySimpleDialog.render(document.body);
	mySimpleDialog.show();
}


function settings() {
	var mySimpleDialog = new YAHOO.widget.SimpleDialog("dlg", {width: "20em", effect:{effect:YAHOO.widget.ContainerEffect.FADE, duration:0.25}}), 
	
	fixedcenter:true,
	modal:true,
	visible:false,
	draggable:true
}};*/