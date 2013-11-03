/*
LICENSE: See "license.php" located at the root installation

//This script applies inviting visual effects and tools to the account activation form
*/

$(document).ready(function() {
//Convert the button into a jQuery styled button
	$(':button').button();
	
////Listen for requests to submit the activation form	
	$(':button').click(function() {
		var button = $(this);
		
		button.button('disable').button('option', 'label', 'Submitting...');
		
		if ($('#key').length) {
			var data = {
				'validate' : 'true',
				'key' : $('#key').val(),
				'passWord' : $('#passWord').val()
			}
		} else {
			var data = {
				'validate' : 'true',
				'passWord' : $('#passWord').val()
			}
		}
		
		$.ajax({
			'url' : document.location.href,
			'type' : 'POST',
			'data' : data,
			'success' : function(data) {
				if (data == 'success') {
					button.button('disable').button('option', 'label', 'Success');
					$('div.formContainer').fadeTo(2000, 0, function() {
						$(this).empty().addClass('spacer').html('<p>Your account has been activated! Logging you in now...').fadeTo(2000, 1, function() {
							setTimeout(document.location.href = '../portal/index.htm', 1000);
						});
					});
				} else {
					$('#key').val('');
					$('#passWord').val('');
					button.button('disabled').button('option', 'label', 'Invalid Key');
					
					setTimeout(function() {
						button.button('enable').button('option', 'label', 'Activate Account');
					}, 3000);
				}
			}
		});
	});
});