/*
LICENSE: See "license.php" located at the root installation

This is a library of tools to allow objects to be createable, editable, draggable, and deleteable on the fly
*/

(function($) {
	$.fn.flyeditor = function(options) {
		var extend = $.extend($.fn.flyeditor.defaults, options);
		
		return this.each(function() {
		//Select necessary objects			
			if ($(this).attr('class') != '') {
				var container = $('.' + $(this).attr('class'));
			} else {
				var container = $('#' + $(this).attr('id'));
			}
			
		//Detect when a container is hovered over, and display its tools
			$(container).live('mouseover', function() {
				$(extend.draggerAttr + ', ' + extend.visibleAttr + ', ' + extend.editorAttr + ', ' + extend.deleterAttr, this).show();
			}).live('mouseout', function() {
				$(extend.draggerAttr + ', ' + extend.visibleAttr + ', ' + extend.editorAttr + ', ' + extend.deleterAttr, this).hide();
			});
			
		//Detect when a request is made to reorder a list of containers
			$(extend.draggerAttr, container).parent().parent().parent().sortable({
				'placeholder' : 'ui-state-highlight',
				'axis' : 'y',
				'stop' : function(event, ui) {
					$.ajax({
						'type' : 'POST',
						'url' : extend.draggerURL,
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
			$(extend.createAttr).click(function() {
			//Include the dialog contents in a variable for quick access
				var editor = $(extend.createDialog + ' table tr');
				
			//Run the createOpen() method
				extend.createOpen();
				
			//Empty the name text field
				$('td :text#name', editor).val('');
				
			//Create the description textarea
				$('td#placeHolder', editor).empty().html('<textarea name="editorDescription" id="editorDescription" style="width:475px;" class="required"></textarea>');
				
			
			//Open the dialog
				$(extend.createDialog).dialog({
					'title' : extend.createTitle,
					'width' : extend.createWidth,
					'height' : extend.createHeight,
					'resizable' : false,
					'close' : function() {
						tinyMCE.execCommand('mceRemoveControl', true, 'editorDescription');
					},
					'buttons' : {
						'Submit' : function() {
						//Send the contents of the editor to the textarea
							tinyMCE.triggerSave();
							
						//Validate the form
							var valid = true;
							
							$.each($('.required:input'), function(index, value) {
								if ($(value, extend.createDialog).val() == "") {
									valid = false;
									return false;
								}
							});
							
							if (valid == true) {
								$.ajax({
								//Send the post data
									'type' : 'POST',
									'url' : extend.creatorURL,
									'data' : $('form', extend.createDialog).serialize(),
									'success' : function(data) {
									//Close the dialog
										$(extend.createDialog).dialog('close');
										
									//Append on the list of courses
										$(container + ':last').after(data);
										
									//Scroll to the new course and highlight its background
										$(container + ':last').css('background-color', '#FFF380');
										
										$('html, body').animate({
											scrollTop : $(container + ':last').offset().top
										}, 1000, function() {
											$(container + ':last').animate({
												'background-color' : '#FFFFFF'
											}, 1000);
										});
										
									//Listen for new requests to reorder courses
										$(extend.draggerAttr).parent().parent().parent().sortable('refresh');
									}
								});
							} else {
							//Stretch the dialog to fit the new text
								$(extend.createDialog).dialog({
									'height' : extend.createHeight + 40
								});
								
							//Send the user a message
								$('#required', extend.createDialog).remove();
								$('table', extend.createDialog).before('<div align="center"><span id="required"></span></div>');
								$('#required', extend.createDialog).empty().addClass('require').text('Several required fields are empty.');
							}
						},
						'Cancel' : function() {
							$(this).dialog('close');
						}
					}
				});
				
			//Setup TinyMCE
				//setupEditor();
			});
			
		//Open the course editor dialog		
			$('nice').live('click', function() {
			//Include the edited course and dialog contents in variables for quick access
				var region = $(this).parent();
				var editor = $(extend.editorAttr + ' table tr');
				
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
				//setupEditor();
			});
			
		//Open the deleter dialog
			$(extend.deleterAttr).live('click', function() {
				var URL = $(this);
				
				$(extend.deleterDialog).dialog({
					'height' : extend.deleterHeight,
					'width' : extend.deleterWidth,
					'resizable' : false,
					'modal' : true,
					'buttons' : {
						'Yes' : function() {
							$.ajax({
								'type' : 'POST',
								'url' : extend.deleterURL,
								'data' : {
									'action' : 'delete',
									'id' : URL.attr('id')
								},
								'success' : function() {
									$(extend.deleterDialog).dialog('close').remove();
									
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
	}
	
//Plugin defaults
	$.fn.flyeditor.defaults = {
	//Avaliable tools
		create : true,
		dragger : true,
		visible : true,
		editor : true,
		deleter : true,
		
	//Tool identifier attributes
		createAttr : '.new',
		draggerAttr : '.draggable',
		visibleAttr : '.visible',
		editorAttr : '.mediumEdit',
		deleterAttr : '.smallDelete',
		
	//Tool processor URLs
		createURL : document.location.href,
		draggerURL : document.location.href,
		visibleURL : document.location.href,
		editorURL : document.location.href,
		deleterURL : document.location.href,
		
	//Creator dialog configuration
		createDialog : '.manager',
		createTitle : 'Create',
		createHeight : 410,
		createWidth : 650,
		createOpen : $.noop,
		createSubmit : $.noop,
		
	//Editor dialog configuration
		editorDialog : '.manager',
		editorHeight : 410,
		editorWidth : 650,
		editorOpen : $.noop,
		editorSubmit : $.noop,
		
	//Deleter dialog configuration
		deleterDialog : '.deleter',
		deleterHeight : 275,
		deleterWidth : 500
	};
})(jQuery);

$(document).ready(function() {
	
//Set the price of the course
									/*	var value;
										
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
										}*/
});