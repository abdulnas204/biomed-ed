/*
LICENSE: See "license.php" located at the root installation

This is a library of tools to allow objects to be createable, editable, draggable, and deleteable on the fly. Also included are other minor real-time transactions.
*/

$(document).ready(function() {
/*
Real-time editor
---------------------------------------------------------
*/	

//Set globally used variables
	var container = $('div.showTools');
	
//Detect when a container is hovered over, and display its tools
	$(container).live('mouseover', function() {
		$('a.draggable, a.visible, a.mediumEdit, a.smallDelete', this).show();
	}).live('mouseout', function() {
		$('a.draggable, a.visible, a.mediumEdit, a.smallDelete', this).hide();
	});
	
//Detect when a request is made to reorder a list of containers
	$('a.draggable', container).parent().parent().parent().sortable({
		'placeholder' : 'ui-state-highlight',
		'axis' : 'y',
		'stop' : function(event, ui) {
			$.ajax({
				'type' : 'POST',
				'url' : document.location.href,
				'data' : {
					'id' : ui.item.attr('id'),
					'currentPosition' : ui.item.attr('name'),
					'newPosition' : ui.item.index() + 1
				}
			});
			
			ui.item.attr('name', ui.item.index() + 1);
		}
	}).disableSelection();
	
//Open the creator dialog
	function create(title, includePrice, width, height) {
	//Include the dialog contents in a variable for quick access
		var editor = $('div#manageDialog table tr');
		var dialog = $('div#manageDialog');
		
	//Empty the name text field
		$('td :text#name', editor).val('');
		
	//Create the description textarea
		$('td#placeHolder', editor).empty().html('<textarea name="editorDescription" id="editorDescription" style="width:475px;" class="required"></textarea>');
		
		if (includePrice == true) {
		//Disable the price field
			$('td :text#price', editor).val('0.00').attr('disabled', 'disabled');
			
		//Listen for the price toggling checkbox
			$('td :checkbox#priceEnabled', editor).click(function() {
				if ($(this).attr('checked') == true) {
					$('td :input#price', editor).removeAttr('disabled');
				} else {
					$('td :input#price', editor).val('0.00').attr('disabled', 'disabled');
				}
			});
		}
		
	//Set the width and height
		if (isNaN(width)) {
			width = 650;
		}
		
		if (isNaN(height)) {
			height = 410;
		}
	
	//Open the dialog
		$(dialog).dialog({
			'title' : title,
			'width' : width,
			'height' : height,
			'resizable' : false,
			'close' : function() {
			//Destroy the editor
				tinyMCE.execCommand('mceRemoveControl', true, 'editorDescription');
				
			//Clear the form invalid message
				$('span#message', dialog).removeClass().text('');
			},
			'buttons' : {
				'Submit' : function() {
				//Send the contents of the editor to the textarea
					tinyMCE.triggerSave();
					
				//Validate the form
					var valid = true;
					
					$.each($('.required:input'), function(index, value) {
						if ($(value, dialog).val() == "") {
							valid = false;
							return false;
						}
					});
					
					if (valid == true) {
					//Generate the post data
						if (includePrice == true) {
							var data = {
								'name' : $('td :text#name', editor).val(),
								'description' : $('td#placeHolder textarea#editorDescription', editor).val(),
								'price' : $('td :input#price', editor).val()
							}
						} else {
							var data = {
								'name' : $('td :text#name', editor).val(),
								'description' : $('td#placeHolder textarea#editorDescription', editor).val()
							}
						}
						
						$.ajax({
						//Send the post data
							'type' : 'POST',
							'url' : document.location.href,
							'data' : data,
							'success' : function(data) {
							//Close the dialog
								$(dialog).dialog('close');
								
							//Clear the form invalid message
								$('span#message', dialog).removeClass().text('');
								
							//Append on the list of curent items
								if ($('div.showTools', 'div#sortable').length == 0) {
									$('div#sortable').empty();
								}
								
								$('div#sortable').append(data);
								
							//Scroll to the new item and highlight its background
								$('html, body').animate({
									scrollTop : $('div.showTools:last').offset().top
								}, 1000, function() {
									$('div.showTools:last').animate({
										'background-color' : '#FFFFFF'
									}, 1000);
								});
								
							//Listen for new requests to reorder items
								$('div.showTools').sortable('refresh');
							}
						});
					} else {
					//Stretch the dialog to fit the new text
						$(dialog).dialog({
							'height' : height + 40
						});
						
					//Send the user a message
						$('#message', dialog).empty().addClass('require').text('The name and description are required.');
					}
				},
				'Cancel' : function() {
					$(this).dialog('close');
				}
			}
		});
		
	//Setup TinyMCE
		setupEditor();
	}
	
//Open the editor dialog		
	function editor(object, includePrice, width, height) {
	//Include the dialog contents in a variable for quick access
		var region = $(object).parent();
		var editor = $('div#manageDialog table tr');
		var dialog = $('div#manageDialog');
		
	//Set the value of the name textfield
		$('td :text#name', editor).val(region.children('span').text());
		
	//Set the value of the hidden id field
		$('td :hidden#id', editor).val(region.parent().children('p.homeDivider:eq(0)').attr('id'));
		
	//Create and set the value of the textarea
		$('td#placeHolder', editor).empty().html('<textarea name="editorDescription" id="editorDescription" style="width:475px;"></textarea>');
		$('td#placeHolder textarea#editorDescription', editor).val(region.next().children('div#description').html());
		
		if (includePrice == true) {
		//Set the value of the price, and its toggling checkbox
			if(region.next().children('p#price').children('span').text().replace(/[^0-9]/g, '') == "") {
				$('td :input#price', editor).val('0.00').attr('disabled', 'disabled');
				$('td :checkbox#priceEnabled', editor).removeAttr('checked');
			} else {
				$('td :input#price', editor).val(region.next().children('p#price').children('span').text().replace(/[^0-9.]/g, '')).removeAttr('disabled');
				$('td :checkbox#priceEnabled', editor).attr('checked', 'checked');
			}
			
		//Listen for the price toggling checkbox
			$('td :checkbox#priceEnabled', editor).click(function() {
				if ($(this).attr('checked') == true) {
					$('td :input#price', editor).removeAttr('disabled');
				} else {
					$('td :input#price', editor).val('0.00').attr('disabled', 'disabled');
				}
			});
		}
		
	//Set the width and height
		if (isNaN(width)) {
			width = 650;
		}
		
		if (isNaN(height)) {
			height = 410;
		}
		
	//Open the dialog
		$(dialog).dialog({
			'title' : 'Edit ' + region.children('span').text(),
			'width' : width,
			'height' : height,
			'resizable' : false,
			'close' : function() {
				tinyMCE.execCommand('mceRemoveControl', true, 'editorDescription');
			},
			'buttons' : {
				'Submit' : function() {
				//Send the contents of the editor to the textarea
					tinyMCE.triggerSave();
					
				//Validate the form
					if ($('td :text#name', editor).val() != "" && $('td#placeHolder textarea#editorDescription', editor).val() != "") {
						if (includePrice == true) {
							var data = {
								'id' : $('td :hidden#id', editor).val(),
								'name' : $('td :text#name', editor).val(),
								'description' : $('td#placeHolder textarea#editorDescription', editor).val(),
								'price' : $('td :input#price', editor).val()
							}
						} else {
							var data = {
								'id' : $('td :hidden#id', editor).val(),
								'name' : $('td :text#name', editor).val(),
								'description' : $('td#placeHolder textarea#editorDescription', editor).val()
							}
						}
						
						$.ajax({
						//Send the post data
							'type' : 'POST',
							'url' : document.location.href,
							'data' : data,
							'success' : function(data) {
							//Close the dialog
								$(dialog).dialog('close');
								
							//Clear the form invalid message
								$('span#required', dialog).removeClass().text('');
								
							//Set the entered values back into their respecitve places
								region.parent().empty().html(data);
							}
						});
					} else {
					//Stretch the dialog to fit the new text
						$(dialog).dialog({
							'height' : height + 40
						});
						
					//Send the user a message
						$('span#required', dialog).addClass('require').text('The name and description are required.');
					}
				},
				
				'Cancel' : function() {
					$(this).dialog('close');
				}
			}
		});
		
	//Setup TinyMCE
		setupEditor();
	}
	
//Open the deleter dialog
	function deleter(object, confirmMessage, width, height) {
		var URL = $(object);
		
	//Set the width and height
		if (isNaN(width)) {
			width = 500;
		}
		
		if (isNaN(height)) {
			height = 275;
		}
		
		$('<div id="deleteDialog" title="Confirm Delete"></div>').html(confirmMessage).dialog({
			'width' : width,
			'height' : height,
			'resizable' : false,
			'modal' : true,
			'buttons' : {
				'Yes' : function() {
					$.ajax({
						'type' : 'GET',
						'url' : document.location.href,
						'data' : {
							'action' : 'delete',
							'id' : URL.attr('id')
						},
						'success' : function() {
							$('#deleteDialog').dialog('close').remove();
							
							URL.parent().parent().fadeOut(1000, function() {
								$(this).hide('blind', 1000).remove();
							});
						}
					});
				},
				'No' : function() {
					$(this).dialog('close').remove();
				}
			}
		});
	}
	
//Listeners for course management
	$('a.createCourse').click(function() {
		create("Create a New Course", true);
	});
	
	$('a.editCourse').live('click', function() {
		editor(this, true);
	});
	
	$('a.deleteCourse').live('click', function() {
		deleter(this, '<p><strong>Warning:</strong> this action will delete this course and all of its learning units.<br /><br />This action cannot be undone. Continue?</p>');
	});
	
//Listeners for learning unit management
	$('a.deleteUnit').live('click', function() {
		deleter(this, '<p><strong>Warning:</strong> this action will delete this learning unit.<br /><br />This action cannot be undone. Continue?</p>');
	});
	
//Listeners for super category management
	$('a.newSuperCategory').click(function() {
		create("Create a Super Category");
	});
	
	$('a.editSuperCategory').live('click', function() {
		editor(this);
	});
	
	$('a.deleteSuperCategory').live('click', function() {
		deleter(this, '<p><strong>Warning:</strong> this action will delete this super category, <em>all</em> of it\'s sub-categories, and <em>all</em> of the questions in each sub-category.<br /><br />The deleted questions will also be removed from any of the tests they have been imported into.<br /><br />This action cannot be undone. Continue?</p>', 500, 375);
	});
	
//Listeners for sub-category management
	$('a.newSubCategory').click(function() {
		create("Create a Sub-category");
	});
	
	$('a.editSubCategory').live('click', function() {
		editor(this);
	});
	
	$('a.deleteSubCategory').live('click', function() {
		deleter(this, '<p><strong>Warning:</strong> this action will delete this sub-category and <em>all</em> of the questions inside this sub-category.<br /><br />The deleted questions will also be removed from any of the tests they have been imported into.<br /><br />This action cannot be undone. Continue?</p>', 500, 350);
	});
	
/*
Minor real-time transactions
---------------------------------------------------------
*/
	
//Add courses to cart, from the main course selection page
	$('span.cartOut:not(.cartUnit)').click(function() {
		var object = $(this);
		
		object.removeClass('cartOut').addClass('cartProcessing');
		
		$.ajax({
			'type' : 'POST',
			'url' : 'index.htm',
			'data' : {'addCourse' : object.attr('id')},
			'success' : function(data) {
				if (data == "success") {
					object.removeClass('cartProcessing').addClass('cartIn');
				} else {
					object.removeClass('cartProcessing').addClass('cartOut');
				}
			}
		});
	});

//Add courses to cart, from the lesson preview page
	$('span.cartUnit').live('click', function() {
		var object = $(this);
		
		$('<div title="Enrollment Confirmation"></div>')
		.html('<p>This learning unit is part of the &quot;' + object.attr('name') + '&quot; course.<br /><br />If you wish to purchase this entire course, which is <strong>$' + $('span#price').text() + '</strong>, then click &quot;Yes&quot; to add it to your cart.')
		.dialog({
			'width' : 450,
			'height' : 300,
			'modal' : true,
			'resizable' : false,
			'buttons' : {
				'Yes' : function() {
					$(this).dialog('close').remove();
					object.removeClass('cartOut').addClass('cartProcessing');
					
					$.ajax({
						'type' : 'POST',
						'url' : 'index.htm',
						'data' : {'addCourse' : object.attr('id')},
						'success' : function(data) {
							if (data == "success") {
								object.removeClass('cartProcessing').addClass('cartIn').removeClass('cartUnit');
							} else {
								object.removeClass('cartProcessing').addClass('cartOut');
							}
						}
					});
				},
				'No' : function() {
					$(this).dialog('close').remove();
				}
			}
		});
	});
	

//Enroll in courses that are free-of-charge
	$('span.cartFree').click(function() {
		var object = $(this);
		
		$('<div title="Enrollment Confirmation"></div>')
		.html('<p>This learning unit is part of the &quot;' + object.attr('name') + '&quot; course.<br /><br />If you wish to enroll yourself in this entire course, which is <strong>free of charge</strong>, then click &quot;Yes&quot;.')
		.dialog({
			'width' : 450,
			'height' : 300,
			'modal' : true,
			'resizable' : false,
			'buttons' : {
				'Yes' : function() {
					$(this).dialog('close').remove();		
					object.removeClass('cartFree').addClass('enrolling');
					
					$.ajax({
						'type' : 'POST',
						'url' : 'index.htm',
						'data' : {'enroll' : object.attr('id')},
						'success' : function(data) {
							if (data == "success") {
								object.fadeTo(1000, 0, function() {
									$(this).remove();
									
									$('<p>You have been enrolled!</p>')
									.appendTo('div.noResults')
									.delay(3000)
									.fadeTo(1000, 0, function() {
										$(this).remove();
										$('<input type="button">')
										.attr({
											'name' : 'begin',
											'id' : 'begin',
											'value' : 'Begin Lesson',
											'onclick' : 'window.location=\'' + document.location.href + '&page=1\''
										})
										.appendTo('div.noResults');
									});
								});
							} else {
								object.fadeTo(1000, 0, function() {
									$(this).remove();
									$('<p>Hmm... doesn\'t look like this was free of charge!</p>').appendTo('div.noResults');
								});
							}
						}
					});
				},
				'No' : function() {
					$(this).dialog('close').remove();
				}
			}
		});
	});
	
//Go to checkout
	$('span.cartIn').live('click', function() {
		document.location.href = 'enroll/cart.htm';
	});
});