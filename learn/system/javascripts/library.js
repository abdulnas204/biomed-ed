/*
LICENSE: See "license.php" located at the root installation

This is a library of tools for use within the learning module
*/

$(document).ready(function() {
//Detect when a course or learning unit is hovered over, and display its tools
	$('div.showTools').live('mouseover', function() {
		$('a.draggable, a.visible, a.mediumEdit, a.smallDelete', this).show();
	});
	
	$('div.showTools').live('mouseout', function() {
		$('a.draggable, a.visible, a.mediumEdit, a.smallDelete', this).hide();
	});
	
//Detect when a request to reorder a course or learning unit is made
	$('a.draggable').parent().parent().parent().sortable({
		'placeholder' : 'ui-state-highlight',
		'axis' : 'y',
		'stop' : function(event, ui) {
			var object = ui.item;
			var position;
			
			$.each($('div.showTools'), function(index) {
				if($(this).is('#' + object.attr('id'))) {
					position = index + 1;
					var URL = document.location.href;
					
					if(URL.split('?').length >= 2) {
						URL = URL.split('?');
						
						if(URL[1].split('&').length >= 2) {
							URL = URL[1].split('&');
							
							$.each(URL, function(index) {
								if(URL[index].split('=')[0] == 'course') {
									URL = "index.php?course=" + URL[index].split("=")[1];
									return false;
								}
							});
						} else {
							var parameter = URL[1].split('=');
							
							if(parameter[0] == 'course') {
								URL = "index.php?course=" + parameter[1];
							}
						}
					} else {
						URL = "index.php";
					}
					
					$.ajax({
						'type' : 'POST',
						'url' : URL,
						'data' : {
							'id' : object.attr('id'),
							'currentPosition' : object.attr('name'),
							'newPosition' : position
						}
					});
					
					object.attr('name', position);
					
					return false;
				}
			});
		}
	}).disableSelection();
	
//Open the course creator dialog
	$('a.createCourse').click(function() {
	//Include the dialog contents in a variable for quick access
		var editor = $('div#manageDialog table tr');
		
	//Reset any previous values
		$('td :text#name', editor).val('');
		$('td :text#price', editor).val('0.00').attr('disabled', 'disabled');
		$('td :checkbox#priceEnabled', editor).removeAttr('checked');
		
	//Create the textarea
		$('td#placeHolder', editor).empty().html('<textarea name="creatorDescription" id="creatorDescription" style="width:475px;"></textarea>');
		
	//Listen for the price toggling checkbox
		$('td :checkbox#priceEnabled', editor).click(function() {
			if ($(this).attr('checked') == true) {
				$('td :input#price', editor).removeAttr('disabled');
			} else {
				$('td :input#price', editor).val('0.00').attr('disabled', 'disabled');
			}
		});
	
	//Open the dialog
		$('div#manageDialog').dialog({
			'title' : 'Create New Course',
			'width' : 650,
			'height' : 410,
			'resizable' : false,
			'close' : function() {
				tinyMCE.execCommand('mceRemoveControl', true, 'creatorDescription');
			},
			'buttons' : {
				'Submit' : function() {
				//Send the contents of the editor to the textarea
					tinyMCE.triggerSave();
					
				//Validate the form
					if ($('td :text#name', editor).val() != "" && $('td#placeHolder textarea#creatorDescription', editor).val() != "") {
						$.ajax({
						//Send the post data
							'type' : 'POST',
							'url' : 'index.php',
							'data' : {
								'name' : $('td :text#name', editor).val(),
								'description' : $('td#placeHolder textarea#creatorDescription', editor).val(),
								'price' : $('td :input#price', editor).val()
							},
							'success' : function(data) {
							//Close the dialog
								$('div#manageDialog').dialog('close');
								
							//Set the price of the course
								var value;
								
								if ($('td :input#price', editor).attr('disabled') == true) {
									var value = 'Free of Charge';
								} else {
									value = $('td :input#price', editor).val();
									value = value.replace(/[^0-9.]/g, '');
									
									if(value == "" || parseInt(value) == 0) {
										value = 'Free of Charge';
									} else {
										value = '$' + value;
										
										if (value.indexOf('.') == -1) {
											value += '.00';
										}
									}
								}
								
								data = $.parseJSON(data);
								
							//Append on the list of courses
								$('div.showTools:last').after('<div class="showTools" style="background-color:#FFF380" id="' + data.id + '" name="' + data.position + '"><p class="homeDivider" id="' + data.id + '"><span><a href="index.htm?course=' + data.id + '">' + $('td :text#name', editor).val() + '</a></span><a href="javascript:;" class="contentHide action draggable" id="' + data.id + '"></a><a href="javascript:;" class="contentHide visible" id="' + data.id + '"></a><a href="javascript:;" class="contentHide action mediumEdit editCourse" id="' + data.id + '"></a><a href="javascript:;" class="contentHide action smallDelete deleteCourse" id="' + data.id + '"></a></p><blockquote><div id="description">' + $('td#placeHolder textarea#creatorDescription', editor).val() + '</div><br /><p><em>No learning units currently avaliable</em></p><p id="price"><strong>Price:</strong> <span>' + value + '</span></p></blockquote></div>'
								);
								
							//Scroll to the new course and highlight its background
								$('html, body').animate({
									scrollTop: $('div.showTools:last').offset().top
								}, 1000, function() {
									$('div.showTools:last').animate({
										'background-color' : '#FFFFFF'
									}, 1000);
								});
								
							//Listen for new requests to reorder courses
								$('a.draggable').parent().parent().parent().sortable('refresh');
							}
						});
					} else {
					//Stretch the dialog to fit the new text
						$('div#manageDialog').dialog({
							'height' : 450
						});
						
					//Send the user a message
						$('div#manageDialog span#required').addClass('require').text('Both the name and description are required');
					}
				},
				'Cancel' : function() {
					$(this).dialog('close');
				}
			}
		});
		
	//Setup TinyMCE
		setupEditor();
	});
	
//Open the course editor dialog		
	$('div.showTools a.editCourse').live('click', function() {
	//Include the edited course and dialog contents in variables for quick access
		var region = $(this).parent();
		var editor = $('div#manageDialog table tr');
		
	//Set the value of the name textfield
		$('td :text#name', editor).val(region.children('span').text());
		
	//Set the value of the hidden id field
		$('td :hidden#id', editor).val(region.parent().children('p.homeDivider:eq(0)').attr('id'));
		
	//Create and set the value of the textarea
		$('td#placeHolder', editor).empty().html('<textarea name="editorDescription" id="editorDescription" style="width:475px;"></textarea>');
		$('td#placeHolder textarea#editorDescription', editor).val(region.next().children('div#description').html());
		
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
		
	//Open the dialog
		$('div#manageDialog').dialog({
			'title' : 'Edit ' + region.children('span').text(),
			'width' : 650,
			'height' : 410,
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
						$.ajax({
						//Send the post data
							'type' : 'POST',
							'url' : 'index.php',
							'data' : {
								'id' : $('td :hidden#id', editor).val(),
								'name' : $('td :text#name', editor).val(),
								'description' : $('td#placeHolder textarea#editorDescription', editor).val(),
								'price' : $('td :input#price', editor).val()
							},
							'success' : function() {
							//Close the dialog
								$('div#manageDialog').dialog('close');
								
							//Clear the form invalid message
								$('div#manageDialog span#required').removeClass().text('');
								
							//Set the entered values back into their respecitve places
								region.children('span').children('a').text($('td :text#name', editor).val());
								region.next().children('div#description').html($('td#placeHolder textarea#editorDescription', editor).val());
								
							//Set the price of the course
								if ($('td :input#price', editor).attr('disabled') == true) {
									region.next().children('p#price').children('span').text('Free of Charge');
								} else {
									var value = $('td :input#price', editor).val();
									value = value.replace(/[^0-9.]/g, '');
									
									if(value == "" || parseInt(value) == 0) {
										value = 'Free of Charge';
									} else {
										value = '$' + value;
										
										if (value.indexOf('.') == -1) {
											value += '.00';
										}
									}
									
									region.next().children('p#price').children('span').text(value);
								}
							}
						});
					} else {
					//Stretch the dialog to fit the new text
						$('div#manageDialog').dialog({
							'height' : 450
						});
						
					//Send the user a message
						$('div#manageDialog span#required').addClass('require').text('Both the name and description are required');
					}
				},
				
				'Cancel' : function() {
					$(this).dialog('close');
				}
			}
		});
		
	//Setup TinyMCE
		setupEditor();
	});
	
//Open the course deletion dialog
	$('a.deleteCourse').live('click', function() {
		var URL = $(this);
		
		$('<div id="deleteDialog" title="Confirm Delete"></div>').html('<p><strong>Warning:</strong> this action will remove this course, and all of its learning units.<br /><br />This action cannot be undone. Continue?</p>').dialog({
			'height' : 275,
			'width' : 500,
			'resizable' : false,
			'modal' : true,
			'buttons' : {
				'Yes' : function() {
					$.ajax({
						'type' : 'GET',
						'url' : 'index.php?action=delete&type=course&id=' + URL.attr('id'),
						'success' : function() {
							$('div#deleteDialog').dialog('close').remove();
							
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
	});
	
//Open the learning unit deletion dialog
	$('a.deleteUnit').live('click', function() {
		var URL = $(this);
		
		$('<div id="deleteDialog" title="Confirm Delete"></div>').html('<p><strong>Warning:</strong> this action will remove this learning unit and all related data.<br /><br />This action cannot be undone. Continue?</p>').dialog({
			'height' : 275,
			'width' : 500,
			'resizable' : false,
			'modal' : true,
			'buttons' : {
				'Yes' : function() {
					$.ajax({
						'type' : 'GET',
						'url' : 'index.php?action=delete&type=unit&id=' + URL.attr('id'),
						'success' : function() {
							$('div#deleteDialog').dialog('close').remove();
							
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
	});
});