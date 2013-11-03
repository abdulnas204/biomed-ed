//Create a fake checkbox and submit this data in realtime when clicked	
	$(document).ready(function(){
		$("a.checked, a.unchecked").live('click', function() {
			var object = $(this);
			
			if(object.hasClass('unchecked')) {
				var option = 'on';
			} else {
				var option = '';
			}
			
			$.ajax({
				'type' : 'POST',
				'url' : document.location.href,
				'data' : {
					'action' : 'setAvaliability',
					'id' : object.attr('id'),
					'option' : option
				},
				'success' : function() {
					if(object.hasClass('unchecked')) {
						object.removeClass('unchecked');
					} else {
						object.addClass('unchecked');
					}
				}
			});
		});
	});
