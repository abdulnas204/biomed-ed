<?php 
//Header functions
	require_once('../../system/connections/connDBA.php');
	
//Export data to XML
	if (isset($_GET['data'])) {
		if ($_GET['data'] == "users") {
			headers("User Data Collection", "Instructor", false, false, false, false, false, false, false, "XML");
			header("Content-type: text/xml");
			$userData = userData();
			$userGrabber = query("SELECT * FROM `users` WHERE `role` = 'Student' AND `organization` = '{$userData['organization']}'", "raw");
			echo "<root>";
			
			while ($users = mysql_fetch_array($userGrabber)) {
				$module = arrayRevert($users['modules']);
				
				if ($module) {
					$modules = count($module);
					$startPrep = reset($module);
					$endPrep = end($module);
					$start = date("F j, Y", $startPrep['startDate']);
					$lastModule = end($module);
					$lastDueDate = query("SELECT * FROM `moduledata` WHERE `id` = '{$lastModule['item']}'");
					$letterArray = array("a","b","c","d","e","f","g","h","i","j","k","l","m","n","o","p","q","r","s","t","u","v","w","x","y","z","A","B","C","D","E","F","G","H","I","J","K","L","M","N","O","P","Q","R","S","T","U","V","W","X","Y","Z");
					$numberArray = array("0","1","2","3","4","5","6","7","8","9");
					$time = str_replace($letterArray, "", $lastDueDate['timeFrame']);
					$timeLabel = str_replace($numberArray, "", $lastDueDate['timeFrame']);					
					$end = date("F j, Y", strtotime(date("F j, Y", $endPrep['startDate']) . " +" . $time . $timeLabel));
					$finished = 0;
					$total = 0;
					
					foreach ($module as $moduleCalc) {
						if ($moduleCalc['moduleStatus'] == "F") {
							$finished++;
						}
						
						if ($moduleCalc['testStatus'] == "F") {
							$finished++;
						}
						
						$total = $total + 2;
					}
					
					$complete = sprintf(($finished / $total) * 100) . "%";
				} else {
					$modules = "0";
					$start = "-";
					$end = "-";
					$complete = "-";
				}
				
				echo "<user>";
				echo "<id>" . $users['id'] . "</id>";
				echo "<name>" . prepare($users['lastName'], false, true) . ", " . prepare($users['firstName'], false, true) . "</name>";
				echo "<firstName>" . prepare($users['firstName'], false, true) . "</firstName>";
				echo "<lastName>" . prepare($users['lastName'], false, true) . "</lastName>";
				echo "<modules>" . $modules . "</modules>";
				echo "<start>" . $start . "</start>";
				echo "<end>" . $end . "</end>";
				echo "<complete>" . $complete . "</complete>";
				echo "</user>";
			}
			
			echo "</root>";
			exit;
		}
	}
	
//Display overall details
	if (!isset($_GET['id'])) {
		headers("Assign Users", "Instructor", "liveData,tabbedPanels,showHide", true, false, false, false, false, false, false, "<script type=\"text/javascript\">var dsUsers = new Spry.Data.XMLDataSet(\"index.php?data=users\", \"root/user\"); var pvUsers = new Spry.Data.PagedView(dsUsers, {pageSize: 20}); var pvUsersPagedInfo = pvUsers.getPagingInfo();</script>");
		
	//Title
		title("Assign Users", "This is the user assignment manager. From here, users and groups may be assigned to a particluar module.");
		
	//Admin toolbar
		echo "<div class=\"toolBar\">";
		echo URL("Back to Modules", "../index.php", "toolBarItem back");
		echo "</div>";
		
	//Check to see if students and modules exist
		$userData = userData();
		
		if (query("SELECT * FROM `users` WHERE `role` = 'Student' AND `organization` = '{$userData['organization']}'") && query("SELECT * FROM `moduledata` WHERE `visible` = 'on' AND `organization` = '{$userData['organization']}' OR `organization` = '0'")) {
		//Grab the necessary data
			$usersGrabber = query("SELECT * FROM `users` WHERE `role` = 'Student' AND `organization` = '{$userData['organization']}'", "raw");
			$moduleGrabber = query("SELECT * FROM `moduledata` WHERE `visible` = 'on' AND `organization` = '{$userData['organization']}' OR `organization` = '0'", "raw");
			
		//Render percentage of assigned users
			echo "<p>" . URL("Toggle the Chart", "javascript:void", false, false, false, false, false, false, false, " onclick=\"toggleInfo('chart')\"") . "</p>";
			echo "<div class=\"contentShow\" id=\"chart\">";
			chart("stacked2D", "assignedUsers", false, "200");
			echo "</div>";
			
		//Assignment control panel
			//echo "<div class=\"layoutControl\"><div class=\"TabbedPanels\" id=\"assignmentPanel\"><ul class=\"TabbedPanelsTabGroup\"><li class=\"TabbedPanelsTab\" tabindex=\"0\">View By User</li><li class=\"TabbedPanelsTab\" tabindex=\"0\">View By Module</li></ul><div class=\"TabbedPanelsContentGroup\">";
			
		//View assignments by user
			//echo "<div class=\"TabbedPanelsContent\">";			
			//Search options
			echo "<div class=\"toolBar noPadding\">Users per Page: ";
			dropDown("resultsUsers", "resultsUsers", "10,20,30,40,50,100,200", "10,20,30,40,50,100,200", false, false, false, "20", false, false, " onchange=\"pvUsers.setPageSize(parseInt(document.getElementById('resultsUsers').value))\"");
			echo " Search By: ";
			dropDown("searchByUsers", "searchByUsers", "Name,Modules,Start Date,End Date,Complete", "name,modules,start,end,complete", false, false, false, "name", false, false, " onchange=\"filterData('searchByUsers', 'keywordsUsers', 'containsUsers', dsUsers)\"");
			echo " Keywords: ";
			textField("keywordsUsers", "keywordsUsers", false, false, false, false, false, false, false, false, " onkeyup=\"startFilterTimer('searchByUsers', 'keywordsUsers', 'containsUsers', dsUsers);\"");
			checkbox("containsUsers", "containsUsers", "Contains", false, false, false, true, false, false, false, " onchange=\"filterData('searchByUsers', 'keywordsUsers', 'containsUsers', dsUsers);\"");			
			echo "</div>";			
			
			//Top navigation toolbar
			navigate("pvUsers", "top");	
			
			//The loading state
			echo "<div spry:region=\"pvUsers dsUsers\" spry:loadingstate=\"loadingData\" spry:readystate=\"loaded\">";
			echo "<div spry:state=\"loadingData\" class=\"noResults\">Loading Users...</div>";
			
			//Assignments table
			echo "<table class=\"dataTable\" spry:if=\"{ds_UnfilteredRowCount} > 0\" spry:state=\"loaded\"><tr><th width=\"200\" spry:sort=\"name\" class=\"tableHeader\">Name</th><th width=\"75\" spry:sort=\"modules\" class=\"tableHeader\">Modules</th><th width=\"175\" spry:sort=\"start\" class=\"tableHeader\">Start Date</th><th width=\"175\" spry:sort=\"end\" class=\"tableHeader\">End Date</th><th width=\"50\" class=\"tableHeader\" spry:sort=\"complete\">Complete</th><th width=\"50\" class=\"tableHeader\">Statistics</th><th width=\"50\" class=\"tableHeader\">Edit</th></tr>";
			echo "<tr spry:repeat=\"pvUsers\" spry:odd=\"odd\" spry:even=\"even\">";
			echo "<td width=\"200\">" . URL("{pvUsers::name}", "details.php?type=user&id={pvUsers::id}") . "</td>";
			echo "<td width=\"75\">{pvUsers::modules}</td>";
			echo "<td width=\"175\">{pvUsers::start}</td>";
			echo "<td width=\"175\">{pvUsers::end}</td>";
			echo "<td width=\"50\">{pvUsers::complete}</td>";
			echo "<td width=\"50\">" . URL(false,"../../statistics/index.php?type=user&period=modules&id={pvUsers::id}", "action statistics", false, "View <strong>{pvUsers::firstName} {pvUsers::lastName}\'s</strong> module statistics") . "</td>";
			echo "<td width=\"50\">" . URL(false, "assign.php?type=user&id={pvUsers::id}", "action edit", false, "Edit <strong>{pvUsers::firstName} {pvUsers::lastName}\'s</strong> lesson plan") . "</td>";
			echo "</tr>";
			echo "</table>";
			echo "</div>";
			
			echo "<div spry:region=\"pvUsers\" spry:if=\"{ds_UnfilteredRowCount} == 0\" align=\"center\">No results found. Make sure your spelling is correct, and that you are searching under the correct category.</div>";
			
			//Bottom navigation toolbar
			navigate("pvUsers", "bottom");
			
			//echo "</div>";
		
		//View assignments by module
			/*echo "<div class=\"TabbedPanelsContent\">";
			//Search options
			echo "<div class=\"toolBar noPadding\">Users per Page: ";
			dropDown("resultsUsers", "resultsUsers", "10,20,30,40,50,100,200", "10,20,30,40,50,100,200", false, false, false, "20", false, false, " onchange=\"pvUsers.setPageSize(parseInt(document.getElementById('resultsUsers').value))\"");
			echo " Search By: ";
			dropDown("searchByUsers", "searchByUsers", "Name,Modules,Start Date,End Date,Complete", "name,modules,start,end,complete", false, false, false, "name", false, false, " onchange=\"filterData('searchByUsers', 'keywordsUsers', 'containsUsers', dsUsers)\"");
			echo " Keywords: ";
			textField("keywordsUsers", "keywordsUsers", false, false, false, false, false, false, false, false, " onkeyup=\"startFilterTimer('searchByUsers', 'keywordsUsers', 'containsUsers', dsUsers);\"");
			checkbox("containsUsers", "containsUsers", "Contains", false, false, false, true, false, false, false, " onchange=\"filterData('searchByUsers', 'keywordsUsers', 'containsUsers', dsUsers);\"");			
			echo "</div>";			
			
			//Top navigation toolbar
			navigate("pvUsers", "top");	
			
			//The loading state
			echo "<div spry:region=\"pvUsers dsUsers\" spry:loadingstate=\"loadingData\" spry:readystate=\"loaded\">";
			echo "<div spry:state=\"loadingData\" class=\"noResults\">Loading Modules...</div>";
			
			//Assignments table
			echo "<table class=\"dataTable\" spry:if=\"{ds_UnfilteredRowCount} > 0\" spry:state=\"loaded\"><tr><th width=\"200\" spry:sort=\"name\" class=\"tableHeader\">Name</th><th width=\"75\" spry:sort=\"modules\" class=\"tableHeader\">Modules</th><th width=\"175\" spry:sort=\"start\" class=\"tableHeader\">Start Date</th><th width=\"175\" spry:sort=\"end\" class=\"tableHeader\">End Date</th><th width=\"50\" class=\"tableHeader\" spry:sort=\"complete\">Complete</th><th width=\"50\" class=\"tableHeader\">Statistics</th><th width=\"50\" class=\"tableHeader\">Edit</th></tr>";
			echo "<tr spry:repeat=\"pvUsers\" spry:odd=\"odd\" spry:even=\"even\">";
			echo "<td width=\"200\">" . URL("{pvUsers::name}", "details.php?type=user&id={pvUsers::id}") . "</td>";
			echo "<td width=\"75\">{pvUsers::modules}</td>";
			echo "<td width=\"175\">{pvUsers::start}</td>";
			echo "<td width=\"175\">{pvUsers::end}</td>";
			echo "<td width=\"50\">{pvUsers::complete}</td>";
			echo "<td width=\"50\">" . URL(false,"../../statistics/index.php?type=user&period=modules&id={pvUsers::id}", "action statistics", false, "View <strong>{pvUsers::firstName} {pvUsers::lastName}\'s</strong> module statistics") . "</td>";
			echo "<td width=\"50\">" . URL(false, "assign.php?type=user&id={pvUsers::id}", "action edit", false, "Edit <strong>{pvUsers::firstName} {pvUsers::lastName}\'s</strong> lesson plan") . "</td>";
			echo "</tr>";
			echo "</table>";
			echo "</div>";
			
			echo "<div spry:region=\"pvUsers\" spry:if=\"{ds_UnfilteredRowCount} == 0\" align=\"center\">No results found. Make sure your spelling is correct, and that you are searching under the correct category.</div>";
			
			//Bottom navigation toolbar
			navigate("pvUsers", "bottom");
			
			echo "</div>";
			echo "</div></div></div></div>";*/
			
		//Configure the tabbed panels
			echo "<script type=\"text/javascript\">var tp1 = new Spry.Widget.TabbedPanels(\"assignmentPanel\");</script>";
	
		} else {
			echo "<div class=\"noResults\">Either no modules exist, or there aren't any students to assign a module to.</div>";
		}
	}
	
//Include the footer
	footer();
?>