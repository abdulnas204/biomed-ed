$(function(){
	var $eventCal = $('#eventCal'), $eventsInfo = $('#eventsInfo');
	
	$eventCal.EventCalendar({
        ajaxEntriesVariable: 'entries',
        ajaxEntriesUrl: '../learn/system/php/data.htm?type=calendar',
        ajaxPostData: {},
        ajaxCache: true,
       
        calendarClass: 'hasEventCalendar',
        dayEventClass: 'ui-state-active hasEvent',

        datepickerOptions: {
            firstDay: 0 /* Sunday */
        },
		
		disableClick: true,
		
		domEvents: {
			mouseenter: function(domEvent, details) {
				$eventsInfo.empty();
				
				var $day = $(this), dayEntries = details.dayEntries;
				
				$.each(dayEntries,function(i,entry){
					$eventsInfo.append(
						'<p>'+
							'<strong><a href="../learn/lesson.htm?id='+entry.id+'">'+entry.title+'</a></strong><br/>'+
							'Start: <em>'+entry.start.toLocaleDateString()+'</em><br />End: <em>'+entry.finish.toLocaleDateString()+'</em>'+
						'</p>'
					);
				});
			}
			
			/*mouseleave: function(domEvent, details) {
				$eventsInfo.empty();
			}*/
		}
	});
});