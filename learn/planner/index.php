<?php
/*
LICENSE: See "license.php" located at the root installation

This is the page where user's can manage their lesson plan with a visual drag-and-drop calendar.
*/

//Header functions
	require_once('../../system/server/index.php');
	require_once('../system/server/index.php');
	
//Generate JSON data for calendar
	if (isset($_GET['data']) && $_GET['data'] == "JSON") {
		$calendarReturn = array();
		
		foreach(arrayRevert($userData['learningunits']) as $event) {
			$detailsGrabber = query("SELECT * FROM `learningunits` WHERE `id` = '{$event['item']}'");
			$time = strip($detailsGrabber['timeFrame'], "numbersOnly");
			$timeLabel = strip($detailsGrabber['timeFrame'], "lettersOnly");	
			
			$eventReturn = array("id" => $detailsGrabber['id'], "title" => $detailsGrabber['name'], "start" => date("M d, Y", $event['startDate']), "end" => date("M d, Y", strtotime(date("Y-m-d", $event['startDate']) . " +" . $time . $timeLabel)), "allDay" => "true");
			
			array_push($calendarReturn, $eventReturn);
		}
		
		echo json_encode($calendarReturn);		
		exit;
	}
	
//Process the form
	if (isset($_POST['events'])) {
		$events = "";
		$oldEvents = arrayRevert($userData['learningunits']);
		
		foreach(json_decode($_POST['events'], true) as $event) {
			$eventTime = explode(" GMT", $event['start']);
			$oldEvents[$event['id']]['startDate'] = strtotime($eventTime['0']);
		}
		
		$eventsUpdate = escape(arrayStore($oldEvents));
		
		query("UPDATE `users` SET `learningunits` = '{$eventsUpdate}' WHERE `id` = '{$userData['id']}'");
		redirect("index.php");
	}
	
//Top content
	headers("Lesson Planner", "fullCalendar");
	
//Title
	title($name, "Below is a calendar which represents your lesson plan for this month. Each learning unit within your lesson plan is resembled by a horizontal blue bar, which spans several days or weeks. Drag these bars to different dates on the calendar to alter your lesson plan. For example, if you have too many learning units assigned at one time, you may wish to drag several of them to a future date.");
	
//Admin toolbar
	echo "<div class=\"toolBar\">\n";
	echo toolBarURL("Back to Learning Units", "../index.php", "toolBarItem back");
	echo "</div>\n<br />\n";
	
//Lesson plan calendar
	echo "<div class=\"important\" id=\"loading\" style=\"visible:none;\">loading...</div>\n";
	echo "<div align=\"center\">\n<div id=\"calendar\"></div>\n</div>\n";
	
//Include the footer
	footer();
?>