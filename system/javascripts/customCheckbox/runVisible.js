//Create a fake checkbox and submit this data in realtime when clicked
	$(document).ready(function(){
		$("a.visible, a.hidden").live('click', function() {
			var object = $(this);
			
			if(object.hasClass('hidden')) {
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
					if(object.hasClass('hidden')) {
						object.removeClass('hidden');
					} else {
						object.addClass('hidden');
					}
				}
			});
		});
	});
