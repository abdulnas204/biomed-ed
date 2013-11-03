(function($) {
	$(document).ready(function() {
		var eventCal = $('#eventCal');
		var eventsInfo = $('#eventsInfo');
		
		eventCal.EventCalendar({
			'ajaxEntriesVariable' : 'entries',
			'ajaxEntriesUrl' : 'addons/learn/data.htm?type=calendar',
			'ajaxCache' : true,
			'calendarClass' : 'hasEventCalendar',
			'dayEventClass' : 'ui-state-active hasEvent',
			'datepickerOptions' : { firstDay: 0 },
			'disableClick' : true,
			
			'domEvents' : {
				'mouseenter' : function(domEvent, details) {
					eventsInfo.empty();
					
					var day = $(this);
					var dayEntries = details.dayEntries;
					
					$.each(dayEntries, function(i,entry) {
						eventsInfo.append(
							'<p>'+
								'<strong><a href="../learn/lesson.htm?id=' + entry.id + '">' + entry.title + '</a></strong><br/>' + 
								'Start: <em>' + entry.start.toLocaleDateString() + '</em><br />End: <em>' + entry.finish.toLocaleDateString() + '</em>' + 
							'</p>'
						);
					});
				}
			}
		});
	});
})(jQuery);