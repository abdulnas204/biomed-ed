/*
LICENSE: See "license.php" located at the root installation

//This script applies inviting visual effects and tools to the user registration form
*/

$(document).ready(function() {
/*
Global definitions
---------------------------------------------------------
*/
	
/*
 * Track the highest step that the user may view, as defined by the server.
 * Basically, the user should always be viewing the step that "step" defines, unless the backtrack to a previous step.
 * If they backtrack, then "step" monitors the highest step a user currently visit, if they decide to skip forward again.
*/
	var step;
	
//Listen to the server and see if there is a registration page
	var registration;
	
//Track the step the user is currently viewing. Note this is different than the "step" variable.
	var currentStep;
	
/*
 * When requsting a different page, should the script go to the step the server defines, or has the user requested another?
 * This would only be used if the user backtracks to a previous step.
*/
	var override;
	
//If "override" is true, then which step has the user requested?
	var overrideStep;
	
/*
 * Listen for a response from the server when a page was processed.
 * If the response was "success", then it is okay to fetch the next server-defined page.
 * This is "success" by default so the first page can load when the user enters registration.
*/
	var propel = 'success';
	
//Make an ajax call to reference the highest step a user may currently access, as defined by the server
	function getStep() {
		$.ajax({
			'url' : document.location.href,
			'type' : 'GET',
			'data' : { 'getStep' : 'true' },
			'success' : function(data) {
				data = $.parseJSON(data);
				
			//The server says this is the highest step the user may currently access
				step = data.step;
				
			//Is there a registration form?
				registration = data.registration;
				
			/*
			 * If this page is loading for the first time, then "currentStep" has not been defined.
			 * That being said, set the "currentStep" the step, since this is the step the user will see when the page first loads
			*/
				if(isNaN(currentStep)) {
					currentStep = step;
				}
			}
		});
	}
	
//Automatically call the above function, to define initial values for the "step" and "resistration" variables
	getStep();
	
/*
 * Define the specific class name for each page number button
 * buttonClass will equal "More" if there is a registration page (i.e.: "More" steps), or "Less" if there isn't
*/
	if (registration == 'true') {
		var buttonClass = 'More';
	} else {
		var buttonClass = 'Less';
	}

/*
First-time loading effects
---------------------------------------------------------
*/

//A special effects protocol to follow when transitioning from one page to another
	function loadPage() {
	/*
	 * Calculate which step to import
	 * If override if true, then the user is trying to backtrack
	*/
		if (override == true) {
		//Prepare the GET variables, and include the step we want
			var data = {
				'step' : 'true',
				'override' : overrideStep
			}
	//The user is not backtracking, just move to the next step
		} else {
			var data = {
				'step' : 'true'
			};
		}
		
	/*
	 * Was the last submission to the server valid, or did the processing terminate before the data could be entered?
	 * Processing would only terminate if the server was handed invalid data, such as an invalid email address.
	 * If is was successful, then it will return "success", and we can fetch the next page.
	*/
		if (propel == 'success') {
		//Make the HTTP request
			$.ajax({
				'type' : 'GET',
				'url' : document.location.href,
				'data' : data,
				'success' : function(data) {
				//Check to see if the fetched data was not empty
					if (data !== '') {
					/*
					 * Fill the "form" container <div> with the fetched data from the server
					 * Falling back to JavaScript's innerHTML attribute, because of Internet Explorer's inability to insert content with the .html() method
					*/
						$('div#form')[0].innerHTML = data;
						
					/*
					 * Reconstruct all of the ribbon bar buttons.
					 * This loop starts from the first button, and goes all of the way up to the highest step the user may access, as defines by the server.
					*/					
						for (var count = 1; count <= step; count++) {
						//Set all steps to the finished state if they are less than "step", and not currently being displayed
							if (count < step && count !== currentStep) {
								$('span.#' + count).removeAttr('class').addClass('stepBase step' + count + 'Finished' + buttonClass);
						//Set this step to the active state, if it is currently being displayed
							} else if (count == currentStep) {
								$('span.#' + count).removeAttr('class').addClass('stepBase step' + count + 'Active' + buttonClass + ' current');
						//All other buttons will be inactive
							} else {
								$('span.#' + count).removeAttr('class').addClass('stepBase step' + count + 'Inactive' + buttonClass + ' noAccess');
							}
						}
						
					/*
					 * If this is the first time loading, then there will be different objects to apply a transition effect.
					 * All of the effects below will all run at the same time.
					 * If the "formContainer" <div> holding the form is hidden, then the user has access this for this first time
					 * If the "formContainer" <div> holding the form is visible, then we are only transitioning from one step to another.
					*/
						if ($('div#formContainer').is(':hidden')) {
						//Display the "formContainer" <div>, which contains both the "ribbonContainer" and "form" <div>s
							$('div#formContainer').fadeTo(2000, 1, function() {
							//Automatically apply focus to the first text field
								$(':text:first').focus();
							});
							
						//Hide the "loader" <div> containing the welcome message
							$('div#loader').fadeTo(2000, 0, function() {
							//Remove the "loader" <div>, as it is not needed and in the way
								$(this).remove();
							});
						} else {
						//Display the "form" <div>, which contains the newly loaded content
							$('div#form').fadeTo(2000, 1, function() {
							//Automatically apply focus to the first text field
								$(':text:first').focus();
							});
						}
						
					//Stretch the "superContainer" <div> to fit the new content, with some extra room on the bottom
						$('div.superContainer').animate({ 'height' : $('div#formContainer').height() + 30 }, 2000);
					
					//Convert any of these elements into jQuery styled buttons
						$(':button, :submit').button();
				//If the fetched data was empty, then gracefully let the user know
					} else {
					//Create a dialog box from thin air
						$('<div id="loaderModal"></div>')
						.html('<p>Hmm... we\'re having trouble loading the registration form. We appologize for the trouble. Click the &quot;Reload&quot; button to reload this page.')
						.dialog({
							'modal' : true,
							'resizable' : false,
							'buttons' : {
								'Ok' : function() {
								//Destory this dialog box
									$(this).dialog('close').remove();
									
								//Reload the page
									window.location.reload();
								}
							}
						});
					}
				}
			});
	/*
	 * If server-side processing did not return "success", then there was a processing error.
	 * Do not load the new content, but simply transition back to same step.
	 * All of the effects below will all run at the same time.
	*/
		} else {
		//Display the "form" <div>, which contains the old content
			$('div#form').fadeTo(2000, 1, function() {
			//Automatically apply focus to the first text field
				$(':text:first').focus();
			});

		//Just for security, stretch the "superContainer" <div> to fit the content, with some extra room on the bottom
			$('div.superContainer').animate({ 'height' : $('div#formContainer').height() + 30 }, 2000);
			
		//Convert any of these elements into jQuery styled buttons
			$('.button, :button, :submit').button();
		}
	}

//Set the opacity of the registration form to zero
	$('div#formContainer').css('opacity', '0');
	
//Add a special effect to the welcome message, if the browser is not IE
	if (navigator.appName !== 'Microsoft Internet Explorer') {
		$('div#loader').fadeTo(2000, 1);
	}
	
//Wait until the page loads completely, then set a timer to transition page one of the form
	window.onload = function() {
		setTimeout(function() {
			loadPage();
		}, 4000);
	}
	
/*
Navigating back and forth between steps
---------------------------------------------------------
*/

//Throw an alert when a user tries to skip ahead
	$('span.noAccess').live('click', function() {
		$('<div title="Not ready yet!"></div>').html('<p>This step is not ready for you yet!<br /><br />There are a few things to finish up first.</p>').dialog({
			'width' : 400,
			'height' : 250,
			'modal' : true,
			'resizable' : false,
			'buttons' : {
				'Ok' : function() {
					$(this).dialog('close').remove();
				}
			}
		});
	});
	
//Set the active step of the registration form
	$('span.stepBase:not(.current, .noAccess)').live('click', function() {		
		var object = $(this);
		var ID = object.attr('id');
		
		$('<div title="Confirm Page Switch"></div>').html('<p>Changes made to this step will not be saved.<br /><br />To save your changes, click the button at the bottom of this page. Continue without saving?</p>').dialog({
			'width' : 500,
			'height' : 350,
			'modal' : true,
			'resizable' : false,
			'buttons' : {
				'Yes' : function() {
					$(this).dialog('close').remove();
					$('div.superContainer').animate({ 'height' : '400' }, 2000);
					$('div#form').fadeTo(2000, 0, function() {
						$('#' + step).removeClass('step' + step + 'Active' + buttonClass + ' current').addClass('step' + step + 'Finished' + buttonClass);
						object.removeClass('step' + ID + 'Finished' + buttonClass).addClass('step' + ID + 'Active' + buttonClass + ' current');
						step = ID;
						override = true;
						overrideStep = ID;
						loadPage();
						override = false;
					});
				},
				'No' : function() {
					$(this).dialog('close').remove();
				}
			}
		});
	});
	
/*
Form element styles
---------------------------------------------------------
*/

//Manipuate the text status beside each input field
	function alterStatus(object, color, display) {
		var message = $.parseJSON(object.parent().children('span.tip').attr('id'));
		
		if (display !== 'checking...') {
			display = message[display];
		}
		
		object
		.parent()
		.removeClass('glowBoxActive glowBoxHover')
		.css('background', color)
		.children('span.tip')
		.empty()
		.text(display);
	}
	
//Hide the message box beside the input field
	function hideBox(object) {		
	//Shorten the message container
		object.parent().removeClass('glowBoxActive glowBoxHover').animate({ 'width' : '200' }, 200) 
		
	//Add the rounded right corners on the text field, so the square corners to not cover the rounded border
		.find('input').css({
			'border-bottom-right-radius' : '5px',
			'border-top-right-radius' : '5px',
			'border-right' : '0px solid #FFFFFF'
		}).end()
		
	//Fade out the message, and remove the "style" attribute to keep the message from being pushed onto a new line, if it is displayed again
		.find('span').fadeOut(200).removeAttr('style');
		
	//Reset the message back to the default value
		alterStatus(object, '#FFFFFF', 'revert');
	}
	
//A simple client-side processor, which will manipulate the message beside the input based on validation status
	function validate(object, containsOptional) {
	//If this field requires custom processing, then process for required fields and optional fields with a value...
		if (object.parent().hasClass('custom') && (containsOptional == undefined || (containsOptional == true && object.val() !== ""))) {
		//A simple waiting message
			alterStatus(object, '#FFFFFF', 'checking...');
			
		//Send the POST request to the server for validation
			$.ajax({
				'url' : document.location.href,
				'type' : 'POST',
				'data' : {
					'name' : object.attr('name'),
					'value' : object.val()
				},
				'success' : function(data) {
				//If the server returns valid...
					if (data == 'valid') {
					//If the message box is told to stay open after validation...
						if (object.parent().hasClass('noHide')) {
							alterStatus(object, '#F0FEE9', 'valid');
					// ...otherwise hide the message box, after displaying a quick "valid" message
						} else {
							alterStatus(object, '#F0FEE9', 'valid');
							setTimeout(function() {
								hideBox(object);
							}, 1000);
						}
				//If the server returns invalid
					} else {
						alterStatus(object, '#FFCFCF','invalid');
					}
				}
			});
	//If no additional processing is needed...
		} else {
			if (containsOptional == false || (containsOptional == undefined && object.val() !== "")) {
			//If the message box is told to stay open after validation...
				if (object.parent().hasClass('noHide')) {
					alterStatus($object, '#F0FEE9', 'valid');
			// ...otherwise hide the message box
				} else {
					hideBox(object);
				}
			} else {
				hideBox(object);
			}
		}
	}

//Apply live rollover classes for each input
	$(':input', 'div.glowBox')
	.live('mouseover', function() {
		if ($(':focus', this) == false) {
			$(this).parent().addClass('glowBoxHover');
		}
	})
	.live('mouseout', function() {
		if ($(':focus', this) == false) {
			$(this).parent().removeClass('glowBoxHover');
		}
	})
	
//Apply focused classes for focused items, and remove the focus class for non-focused items
	.live('focus', function() {
	//Add the focus class and stretch the box
		$(this).parent().addClass('glowBoxActive glowBoxHover').animate({ 'width' : '400' }, 200)
		
	//Remove the rounded right corners on the text field, in case of an error, so background color does not show through
		.find('input').css({
			'border-bottom-right-radius' : '0px',
			'border-top-right-radius' : '0px',
			'border-right' : '2px solid #5F5F5F'
		}).end()
		
	//Fade in the message
		.find('span').fadeIn(200);
	})
	
//Validate the input field when the focus leaves the field, and style it accordingly
	.live('focusout', function() {
	//A variable for $(this) is not created, because of conflicts caused when a user moves though several fields too quickly
	//If the input is not empty, and is not optional...
		if ($(this).val() !== "" && !$(this).parent().hasClass('optional')) {
			validate($(this));
	//If the input is not empty, and is optional...
		} else if ($(this).val() !== "" && $(this).parent().hasClass('optional')) {
			validate($(this), true);
	//If the input is not optional, and is empty
		} else if ($(this).val() == "" && $(this).parent().hasClass('optional')) {
			hideBox($(this));
	//If the input is optional, and is empty
		} else {
			alterStatus($(this), '#FFCFCF', 'empty');
		}
	});
	
/*
Form processing
---------------------------------------------------------
*/

//Listen for submit requests that are NOT submitting to PayPal
	$('#submit').live('click', function() {
	//Check to make sure that no fields are currently validating
		if ($('span:contains(checking...)').length > 0) {
			$('<div title="Please Wait"></div>')
			.html('<p>Please wait until all of the fields have validated. You will know that it has finished when you don\'t see a &quot;checking...&quot; status beside any of the input fields.</p>')
			.dialog({
				'width' : 400,
				'modal' : true,
				'resizable' : false,
				'buttons' : {
					'Ok' : function() {
						$(this).dialog('close').remove();
					}
				}
			});
	//Check to make sure that no fields are invalid, or required field left empty
		} else {
			var valid = true;
			
		//Is this field invalid?
			$('span.tip').each(function() {
			//If the background color is a reddish color, then that indicates the field is invalid
				if ($(this).css('background-color') == '#FFCFCF' || $(this).css('background-color') == 'rgb(255, 207, 207)') {
					valid = false;
					return false;
				}
			});
			
		//Is this field empty, and *not* optional?
			$('input:text[value=""]', '.superContainer').each(function() {
				if ($(this).val() == '' && !$(this).parent().hasClass('optional')) {
					valid = false;
					return false;
				}
			});
			
		//If the form is valid, then submit away!
			if (valid == true) {
				$('div.superContainer').animate({ 'height' : '400' }, 2000);
				$('div#form').fadeTo(2000, 0, function() {
					$.ajax({
						'url' : document.location.href,
						'type' : 'POST',
						'data' : $('form').serialize(),
						'success' : function(data) {
							propel = data;
							step = getStep();
							currentStep++;
							loadPage();
							propel = "";
						}
					});
				});
		// ...otherwise give a notification
			} else {
				$('<div title="Form Invalid"></div>')
				.html('<p>There are errors in this form. Please correct them before proceeding.</p>')
				.dialog({
					'width' : 400,
					'modal' : true,
					'resizable' : false,
					'buttons' : {
						'Ok' : function() {
							$(this).dialog('close').remove();
						}
					}
				});
			}
		}
	});
	
//Listen for submit requests that ARE submitting to PayPal
	$('.preProcess').live('click', function() {
		
	});
	
/*
Finialzing
---------------------------------------------------------
*/

//Listen for requests to resend activation emails
	$('span#resend').live('click', function() {
		var object = $(this);
		
		object.button('disable').button('option', 'label', 'Sending...');
		
		$.ajax({
			'url' : document.location.href,
			'method' : 'GET',
			'data' : { 'sendEmail' : 'true' },
			'success' : function(data) {
				if (data == 'success') {
					object.button('disable').button('option', 'label', 'Email Sent!');
					
					function resetButton() {
						object.button('enable').button('option', 'label', 'Resend Activation Email');
					}
					
					setTimeout(resetButton, 2000);
				} else {
					object.button('disable').button('option', 'label', 'Email Could Not Be Sent');
					
					function resetButton() {
						object.button('enable').button('option', 'label', 'Resend Activation Email');
					}
					
					setTimeout(resetButton, 2000);
				}
			}
		});
	});
});