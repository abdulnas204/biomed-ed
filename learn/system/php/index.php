<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: November 28th, 2010
Last updated: December 20th, 2010

This is the configuration script for the learning plugin.
*/

//Plugin information
	$name = "Learning Module";
	$menuName = "Learning Module";
	$author = "Oliver Spryn";
	$infoURL = "http://apexdevelopment.businesscatalyst.com";
	$version = "1.0";
	$pluginRoot = $root . "learn/";

/*
 * Set the parent on the navigation menu hierarchy
 * Use "top" for top-level, false for hidden
*/
	$menuParent = "top";
	
/*
 * Set the children on the navigation menu hierarchy, can only contain pages within this plugin
 * Use false for none, or create an array containing the list of sub-pages, with the URL as the key (relative to the plugin root) and the value as the title
*/
	$menuChildren = false;
	
/*
 * Define the privilege types required to access each page
 * The keys are the page URLs (relative to the plugin root), and the value is the privilege name
 * To account for URL parameters or active sessions, start with the page URL, then wrap each URL paramerter with a (), and a session with a [], and seperate each item with a comma
 * Here is an example of a page allowing access when an "id" parameter is defined and a "edit" session is active: "page.php,(id),[edit]"
 * To assign multiple pages to one permission, simply include all of the pages in one string, and seperate the URLs with a semicolon
 * To allow public access to a page, simply construct the array as described above, but, instead, assign the value as "Public"
*/
	$privileges = array(
				//View lessons and tests
				"index.php;
				lesson.php; 
				test.php;
				feedback.php;
				review.php" => "View Learning Units",
				
				/*
				Manage information
				---------------------------------------------------------
				*/
				
				//Creating lessons and tests
				"wizard/index.php,[currentUnit];
				wizard/lesson_settings.php,[currentUnit];
				wizard/lesson_content.php,[currentUnit];
				wizard/preview_page.php,[currentUnit];
				wizard/manage_content.php,[currentUnit];
				wizard/manage_content.php,(id),[currentUnit];
				wizard/lesson_verify.php,[currentUnit];
				wizard/test_check.php,[currentUnit];
				wizard/test_settings.php,[currentUnit];
				wizard/test_content.php,[currentUnit];
				wizard/preview_question.php,[currentUnit];
				wizard/question_merge.php,[currentUnit];
				wizard/complete.php,[currentUnit];
				questions/blank.php,[currentUnit];
				questions/description.php,[currentUnit];
				questions/essay.php,[currentUnit];
				questions/file_response.php,[currentUnit];
				questions/functions.php,[currentUnit];
				questions/matching.php,[currentUnit];
				questions/multiple_choice.php,[currentUnit];
				questions/preview.php,[currentUnit];
				questions/question_bank.php,[currentUnit];
				questions/short_answer.php,[currentUnit];
				questions/true_false.php,[currentUnit]" => "Create Learning Unit",
				
				//Editing lessons and tests
				"index.php,(id),(edit);
				wizard/lesson_settings.php,[review],[currentUnit];
				wizard/lesson_content.php,[review],[currentUnit];
				wizard/preview_page.php,[review],[currentUnit];
				wizard/manage_content.php,[review],[currentUnit];
				wizard/manage_content.php,(id),[review],[currentUnit];
				wizard/lesson_verify.php,[review],[currentUnit];
				wizard/test_check.php,[review],[currentUnit];
				wizard/test_settings.php,[review],[currentUnit];
				wizard/test_content.php,[review],[currentUnit];
				wizard/preview_question.php,[review],[currentUnit];
				wizard/question_merge.php,[review],[currentUnit];
				wizard/complete.php,[review],[currentUnit];
				questions/blank.php,[review],[currentUnit];
				questions/description.php,[review],[currentUnit];
				questions/essay.php,[review],[currentUnit];
				questions/file_response.php,[review],[currentUnit];
				questions/functions.php,[review],[currentUnit];
				questions/matching.php,[review],[currentUnit];
				questions/multiple_choice.php,[review],[currentUnit];
				questions/preview.php,[review],[currentUnit];
				questions/question_bank.php,[review],[currentUnit];
				questions/short_answer.php,[review],[currentUnit];
				questions/true_false.php,[review],[currentUnit]" => "Edit Learning Unit",
				
				//Editing unowned lessons and tests
				"index.php,(id),(edit);
				wizard/lesson_settings.php,[review],[currentUnit];
				wizard/lesson_content.php,[review],[currentUnit];
				wizard/preview_page.php,[review],[currentUnit];
				wizard/manage_content.php,[review],[currentUnit];
				wizard/manage_content.php,(id),[review],[currentUnit];
				wizard/lesson_verify.php,[review],[currentUnit];
				wizard/test_check.php,[review],[currentUnit];
				wizard/test_settings.php,[review],[currentUnit];
				wizard/test_content.php,[review],[currentUnit];
				wizard/preview_question.php,[review],[currentUnit];
				wizard/question_merge.php,[review],[currentUnit];
				wizard/complete.php,[review],[currentUnit];
				questions/blank.php,[review],[currentUnit];
				questions/description.php,[review],[currentUnit];
				questions/essay.php,[review],[currentUnit];
				questions/file_response.php,[review],[currentUnit];
				questions/functions.php,[review],[currentUnit];
				questions/matching.php,[review],[currentUnit];
				questions/multiple_choice.php,[review],[currentUnit];
				questions/preview.php,[review],[currentUnit];
				questions/question_bank.php,[review],[currentUnit];
				questions/short_answer.php,[review],[currentUnit];
				questions/true_false.php,[review],[currentUnit];
				overview.php,(id)" => "Edit Unowned Learning Units",
				
				//Delete lessons and tests
				"index.php,(id),(action)" => "Delete Learning Unit",
				
				//Add data into the question bank
				"question_bank/index.php;
				question_bank/discover.php;
				question_bank/preview.php;
				question_bank/search.php;
				questions/blank.php,[questionBank];
				questions/description.php,[questionBank];
				questions/essay.php,[questionBank];
				questions/file_response.php,[questionBank];
				questions/functions.php,[questionBank];
				questions/matching.php,[questionBank];
				questions/multiple_choice.php,[questionBank];
				questions/preview.php,[questionBank];
				questions/question_bank.php,[questionBank];
				questions/short_answer.php,[questionBank];
				questions/true_false.php,[questionBank]" => "Create Question Bank Questions",
				
				//Edit question bank entries
				"question_bank/index.php;
				question_bank/discover.php;
				question_bank/preview.php;
				question_bank/search.php;
				questions/blank.php,(bankID),[questionBank];
				questions/description.php,(bankID),[questionBank];
				questions/essay.php,(bankID),[questionBank];
				questions/file_response.php,(bankID),[questionBank];
				questions/functions.php,(bankID),[questionBank];
				questions/matching.php,(bankID),[questionBank];
				questions/multiple_choice.php,(bankID),[questionBank];
				questions/preview.php,(bankID),[questionBank];
				questions/question_bank.php,(bankID),[questionBank];
				questions/short_answer.php,(bankID),[questionBank];
				questions/true_false.php,(bankID),[questionBank]" => "Edit Question Bank Questions",
				
				//Delete question bank entries
				"question_bank/index.php,(id),(questionID),(action)" => "Delete Question Bank Questions",
				
				//Create feedback questions
				"feedback/index.php;
				feedback/preview.php;
				questions/blank.php;
				questions/description.php;
				questions/essay.php;
				questions/file_response.php;
				questions/functions.php;
				questions/matching.php;
				questions/multiple_choice.php;
				questions/preview.php;
				questions/question_bank.php;
				questions/short_answer.php;
				questions/true_false.php" => "Create Feedback Questions",
				
				//Edit feedback questions
				"feedback/index.php;
				feedback/preview.php;
				questions/blank.php,(feedbackID);
				questions/description.php,(feedbackID);
				questions/essay.php,(feedbackID);
				questions/file_response.php,(feedbackID);
				questions/functions.php,(feedbackID);
				questions/matching.php,(feedbackID);
				questions/multiple_choice.php,(feedbackID);
				questions/preview.php,(feedbackID);
				questions/question_bank.php,(feedbackID);
				questions/short_answer.php,(feedbackID);
				questions/true_false.php" => "Edit Feedback Questions",
				
				//Delete feedback questions
				"feedback/index.php,(id),(action)" => "Delete Feedback Questions",
				
				/*
				Manage user-specific information
				---------------------------------------------------------
				*/
				
				//Assign users to learning unit
				"assign/index.php;
				assign/assign.php;
				assign/details.php" => "Assign Users to Learning Unit",
				
				//Purchase learning unit
				"enroll/cart.php" => "Purchase Learning Unit",
				
				//Access learning unit statistics
				"statistics/index.php" => "Access Learning Unit Statistics"
				);

/*
 * Force a list of pages to use a secure (https) connection, if an SSL certificate is installed
 * The keys are the page URLs (relative to the plugin root), and the value is the level of importance
 * The values may either be "Optional" (use a secure connection if an SSL certificate is installed) or "Important" (deny access to the page if an SSL certificate is not installed)
 * To account for URL parameters or active sessions, start with the page URL, then wrap each URL paramerter with a (), and a session with a [], and seperate each item with a comma
 * Here is an example of a secured page when an "id" parameter is defined and a "edit" session is active: "page.php,(id),[edit]"
 * To assign multiple pages to a secure connection, simply include all of the pages in one string, and seperate the URLs with a semicolon
*/
	$SSL = array();
?>