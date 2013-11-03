<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
*/

/* 
Created by: Oliver Spryn
Created on: Novemeber 27th, 2010
Last updated: Novemeber 27th, 2010

This script contains functions used to create common HTML 
elements, and add a touch of dynamic content, while abiding
to system-wide HTML standards.
*/
	
/*
Form input elements
---------------------------------------------------------
*/

//Form initiator
	function form($name, $method = "post", $containsFile = false, $action = false, $additionalParameters = false) {
		$return = "\n<form name=\"" . $name . "\" method=\"" . $method . "\" id=\"validate\"";
		
		if ($containsFile == true) {
			$return .= " enctype=\"multipart/form-data\"";
		}
		
		$return .= " action=\"";
		
		if ($action == false) {
			$getParameters = $_GET;
			
			if (sizeof($getParameters) >= 1) {
				$parameters = "?";
				
				while(list($parameter, $value) = each($getParameters)) {
					$parameters .= $parameter . "=" . $value . "&";
				}
			}
			
			if (isset($parameters)) {
				$return .= $_SERVER['PHP_SELF'] . rtrim($parameters, "&");
			} else {
				$return .= $_SERVER['PHP_SELF'];
			}
		} else {
			$return .= $action;
		}
		
		$return .= "\"";
		
		if ($additionalParameters == true) {
			$return .= " onsubmit=\"" . $additionalParameters . "\"";
		}
		
		$return .= ">\n";
		
		return $return;
	}
	
//Close a form
	function closeForm($advancedClose = true) {
		if ($advancedClose == true) {
			return "</div>\n</form>\n";
		} else {
			return "\n</form>\n";
		}
	}
	
//Button
	function button($name, $id, $value, $type, $URL = false, $additionalParameters = false) {		
		switch ($type) {
			case "submit" : 
				return "\n<input type=\"submit\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"" . ltrim($additionalParameters) . "tinyMCE.triggerSave();\">\n";
				break;
				
			case "reset" : 
				return "\n<input type=\"reset\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"return confirm('Are you sure you wish to reset all of the content in this form? Click OK to continue');$.validationEngine.closePrompt('#validate');" . $additionalParameters . "\">\n";
				break;
				
			case "cancel" : 
				return "\n<input type=\"reset\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"window.location='" . $URL . "';" . $additionalParameters . "\">\n";
				break;
				
			case "history" : 
				return "\n<input type=\"button\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"history.go(-1);" . $additionalParameters . "\">\n";
				break;
				
			case "button" : 
				$return = "\n<input type=\"button\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\"" . $additionalParameters;
				
				if ($URL == true) {
					$return .= " onclick=\"window.location='" . $URL . "';";
				}
				
				$return .= "\">\n";
				
				return $return;
				
				break;
				
			case "image" :
				$return = "\n<input type=\"image\" name=\"" . $name . "\" id=\"" . $id . "\" src=\"" . $URL . "\"";
				
				if ($additionalParameters == true) {
					$return .= " onclick=\"" . $additionalParameters . "\"";
				}
				
				$return .= ">\n";
				
				return $return;
				
				break;
		}
	}

//Checkbox
	function checkbox($name, $id, $label = false, $checkboxValue = false, $validate = true, $minValues = false, $manualSelect = false, $editorTrigger = false, $arrayValue = false, $matchingValue = false, $additionalParameters = false) {
		global $$editorTrigger;
		
		$return = "\n<label><input type=\"checkbox\" name=\"" . $name . "\" id=\"" . $id . "\"";
		
		if ($validate == true && $minValues == true) {
			$return .= " class=\"validate[required,minCheckbox[" . $minValues . "]]\"";
		}
		
		if ($manualSelect == true && ($editorTrigger == false || !isset($$editorTrigger))) {
			$return .= " checked=\"checked\"";
		} else {
			if ($editorTrigger == true && isset($$editorTrigger)) {
				$value = $$editorTrigger;
				
				if (isset($$editorTrigger)) {
					if ($value[$arrayValue] == $matchingValue) {
						$return .= " checked=\"checked\"";
					}
				}
			}
		}
		
		if ($checkboxValue == true) {
			$return .= " value=\"" . $checkboxValue  . "\"";
		}
		
		$return .= $additionalParameters . "/>" . $label . "</label>\n";
		
		return $return;
	}
	
//Dropdown menu
	function dropDown($name, $id, $values, $valuesID, $multiple = false, $validate = true, $validateAddition = false, $manualSelect = false, $editorTrigger = false, $arrayValue = false, $additionalParameters = false) {
		global $$editorTrigger;
		
		$valuesArray = explode(",", $values);
		$valuesIDArray = explode(",", $valuesID);
		$valuesLimit = sizeof($valuesArray) - 1;
		
		if (sizeof($valuesArray) != sizeof($valuesIDArray)) {
			die(errorMessage("The values and IDs of the " . $name . " dropdown menu do not match"));
		} else {
			$return = "\n<select name=\"" . $name . "\" id=\"" . $id . "\"";
			
			if ($multiple == false) {
				if ($validate == true) {
					$return .= " class=\"validate[required" . $validateAddition . "]\"";
				}
			} else {
				if ($validate == true) {
					$return .= " multiple=\"multiple\" class=\"multiple validate[required" . $validateAddition . "]\"";
				} else {
					$return .= " multiple=\"multiple\" class=\"multiple\"";
				}
			}
			
			$return .= $additionalParameters . ">\n";
			
			if ($values == true && $valuesID == true) {
				for ($count = 0; $count <= $valuesLimit; $count ++) {					
					$return .= "<option value=\"" . $valuesIDArray[$count] . "\"";
					
					if (($manualSelect == true || $manualSelect == "0") && ($editorTrigger == false || !isset($$editorTrigger))) {
						if ($manualSelect == $valuesIDArray[$count]) {
							$return .= " selected=\"selected\"";
						}
					} else {
						if ($editorTrigger == true && isset($$editorTrigger)) {
							$value = $$editorTrigger;
							
							if (isset($$editorTrigger)) {
								if ($value[$arrayValue] == $valuesIDArray[$count]) {
									$return .= " selected=\"selected\"";
								}
							}
						}
					}
					
					$return .= ">" . $valuesArray[$count] . "</option>\n";
				}
			}
			
			$return .= "</select>\n";
			
			return $return;
		}			
	}
	
//File upload
	function fileUpload($name, $id, $size = false, $validate = true, $validateAddition = false, $manualValue = false, $editorTrigger = false, $arrayValue = false, $fileURL = false, $uploadNote = false, $hideUploadSize = false, $additionalParameters = false) {
		global $$editorTrigger;
		
		$return = "";
		
		if ($editorTrigger == true && isset($$editorTrigger)) {
			$value = $$editorTrigger;
			
			if (isset($$editorTrigger) && !empty($value[$arrayValue])) {
				$return .= "\nCurrent file: " . URL($value[$arrayValue], $fileURL . "/" . $value[$arrayValue], false, "_blank") . "<br />";
			}
		}
		
		if ($manualValue == true) {
			$return .= "\nCurrent file: " . URL($manualValue, $fileURL . "/" . urlencode($manualValue), false, "_blank") . "<br />";
		}
		
		$return .= "\n<input type=\"file\" name=\"" . $name . "\" id=\"" . $id . "\" size=\"";
		
		if ($size == false) {
			$return .= "50";
		} else {
			$return .= $size;
		}
		
		$return .= "\"";
		
		if ($validate == true) {
			$return .= " class=\"validate[required" . $validateAddition . "]\"";
		}
		
		$return .= ">\n";
		
		if ($hideUploadSize == false) {
			$return .= "<br />Max file size: " . ini_get('upload_max_filesize') . "\n";
		}
		
		if (($manualValue == true || (isset($$editorTrigger) && !empty($value[$arrayValue]))) && $uploadNote == true) {
			$return .= "<br /><strong>Note:</strong> Uploading a new file will replace the existing one.\n";
		}
		
		return $return;
	}
	
//Hidden
	function hidden($name, $id, $value) {
		return "\n<input type=\"hidden\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" />\n";
	}
	
//Radio button
	function radioButton($name, $id, $buttonLabels, $buttonValues, $inLine = true, $validate = true, $validateAddition = false, $manualSelect = false, $editorTrigger = false, $arrayValue = false, $additionalParameters = false) {
		$labelsArray = explode(",", $buttonLabels);
		$valuesArray = explode(",", $buttonValues);
		$valuesLimit = sizeof($labelsArray) - 1;
		$return = "";
		
		for ($count = 0; $count <= $valuesLimit; $count ++) {
			global $$editorTrigger;
			
			$return .= "\n<label><input type=\"radio\" name=\"" . $name . "\" id=\"" . $id . "_" . $count . "\" value=\"" . $valuesArray[$count] . "\"";
			
			if (($manualSelect == true || $manualSelect === "0") && ($editorTrigger == false || !isset($$editorTrigger))) {
				if ($valuesArray[$count] == $manualSelect) {
					$return .= " checked=\"checked\"";
				}
			} else {
				if ($editorTrigger == true && isset($$editorTrigger)) {
					$value = $$editorTrigger;
					
					if (isset($$editorTrigger)) {
						if ($valuesArray[$count] == $value [$arrayValue]) {
							$return .= " checked=\"checked\"";
						}
					}
				}
			}
			
			if ($validate == true) {
				$return .= " class=\"validate[required" . $validateAddition . "] radio\"";
			}
			
			$return .= $additionalParameters . ">" . $labelsArray[$count] . "</label>\n";
			
			if ($count != $valuesLimit) {
				if ($inLine != true) {
					$return .= "<br />\n";
				}
			}
		}
		
		return $return;
	}
	
//Textarea
	function textArea($name, $id, $size, $validate = true, $validateAddition = false, $manualValue = false, $editorTrigger = false, $arrayValue = false, $additionalParameters = false) {
		$return = "\n<textarea name=\"" . $name . "\" id=\"" . $id . "\" style=\"";
		
		switch ($size) {
			case "large" : 
				$return .= "width:640px; height:320px;";
				break;
				
			case "large" : 
				$return .= "width:475px;";
				break;
				
			case "large" : 
				$return .= "width:350px;";
				break;
				
			default :
				$error = debug_backtrace();
				die(errorMessage("Invalid textarea size type requested on line " .  $error['0']['line']));
				break;
		}
		
		$return .= "\"";
		
		if (strstr($additionalParameters, "class=\"") && $validate == true) {
			$searchParameters = array_filter(explode("\"", $additionalParameters));
			$returnClass = "";
			$returnParameters = "";
			
			for($count = 0; $count <= sizeof($searchParameters); $count ++) {
				if ($searchParameters[$count] == " class=") {
					$returnClass = $searchParameters[sprintf($count + 1)];
					unset($searchParameters[sprintf($count + 1)]);
					array_merge($searchParameters);
				} else {
					if (!empty($searchParameters[$count])) {
						$returnParameters .= $searchParameters[$count] . "\"";
					}
				}
			}
		}
		
		if ($validate == true) {
			if (isset($returnClass)) {
				$return .= " class=\"validate[required" . $validateAddition . "]" . " " . $returnClass . "\"";
			} else {
				$return .= " class=\"validate[required" . $validateAddition . "]\"";
			}
		}
		
		if (isset($returnParameters)) {
			$return .= $returnParameters . ">";
		} else {
			$return .= $additionalParameters . ">";
		}
		
		global $$editorTrigger;
		
		if ($manualValue == true && ($editorTrigger == false || !isset($$editorTrigger))) {
			$return .= $manualValue;
		} else {
			if ($editorTrigger == true && isset($$editorTrigger)) {
				$value = $$editorTrigger;
				
				if (isset($$editorTrigger)) {
					$return .= prepare($value[$arrayValue], false, true);
				}
			}
		}
		
		$return .= "</textarea>\n";
		
		return $return;
	}
	
//Text Fields
	function textField($name, $id, $size = false, $limit = false, $password = false, $validate = true, $validateAddition = false, $manualValue = false, $editorTrigger = false, $arrayValue = false, $additionalParameters = false) {
		$return .= "\n<input type=\"";
		
		if ($password == false) {
			$return .= "text";
		} else {
			$return .= "password";
		}
			
		$return .= "\"";
		
		if ($limit == true) {
			$return .= " maxlength=\"" . $limit . "\"";
		}
		
		$return .= " name=\"" . $name . "\" id=\"" . $id . "\" size=\"";
		
		if ($size == false) {
			$return .= "50";
		} else {
			$return .= $size;
		}
		
		$return .= "\" autocomplete=\"off\"";
		
		if ($validate == true) {
			$return .= " class=\"validate[required" . $validateAddition . "]\"";
		}
		
		if ($validate == false && $validateAddition == true) {
			$return .= " class=\"validate[optional" . $validateAddition . "]\"";
		}
		
		global $$editorTrigger;
		
		if ($manualValue == true && ($editorTrigger == false || !isset($$editorTrigger))) {
			$return .= "  value=\"" . prepare($manualValue, true, true) . "\"";
		} else {
			if ($editorTrigger == true && isset($$editorTrigger)) {
				$value = $$editorTrigger;
				
				if (isset($$editorTrigger)) {
					$return .= " value=\"" . prepare($value[$arrayValue], true, true) . "\"";
				}
			}
		}
		
		$return .= $additionalParameters . " />\n";
		
		return $return;
	}
	
/*
Elements used in form construction, but not actually form elements
---------------------------------------------------------
*/	
		
//Category divider for form layout and categorizing
	function catDivider($content, $class, $first = false, $last = false, $id = false) {
		if ($last == true) {
			echo "</div>";
		} else {
			if ($first == false) {
				echo "</div>";
			}
			
			echo "<div class=\"catDivider " . $class . "\"";
			
			if ($id == true) {
				echo " id=\"" . $id . "\"";
			}
			
			echo ">" . $content . "</div>";
			
			if ($last == false) {
				echo "<div class=\"stepContent\">";
			}
		}
	}
	
//Directions for a form element
	function directions($text, $required = false, $help = false) {
		global $root;
		
		echo "<p>" . $text;
		
		if ($required == true) {
			echo "<span class=\"require\">*</span>";
		}
		
		echo ": ";
		
		if ($help == true) {
			echo "\n<img src=\"" . $root . "system/images/admin_icons/help.png\" alt=\"Help\" width=\"17\" height=\"17\" onmouseover=\"Tip('" . $help . "')\" onmouseout=\"UnTip()\" />";
		}
		
		echo "</p>\n";
	}
	
//Help icon, with no additional text
	function help($help) {
		global $root;
		
		echo "<img src=\"" . $root . "system/images/admin_icons/help.png\" alt=\"Help\" width=\"15\" height=\"15\" onmouseover=\"Tip('" . $help . "')\" onmouseout=\"UnTip()\" />";
	}
	
//Indent a form element
	function indent($input) {
		echo "<blockquote><p>" . $input . "</p></blockquote>\n";
	}
	
/*
Non-form input elements
---------------------------------------------------------
*/

//Page title and introductory text
	function title($title, $text = false, $break = true, $class = false) {
		echo "\n<h2";
		
		if ($class == true) {
			echo " class=\"" . $class . "\"";
		}
		
		echo ">" . $title . "</h2>\n";
		
		if ($text == true) {
			echo $text . "\n";
		}
		
		if ($break == true) {
			echo "<p>&nbsp;</p>\n";
		}
	}
	
//Links
	function URL($text, $URL, $class = false, $target = false, $toolTip = false, $delete = false, $newWindow = false, $width = false, $height = false, $additionalParameters = false) {
		if ($newWindow == false || $width == false || $height == false) {
			$return = "<a href=\"" . $URL . "\"";
			
			if ($target == true) {
				$return .= " target=\"" . $target . "\"";
			}
			
			if ($class == true) {
				$return .= " class=\"" . $class . "\"";
			}
			
			if ($toolTip == true) {
				$return .= " onmouseover=\"Tip('" . prepare($toolTip, true, false) . "')\" onmouseout=\"UnTip()\"";
			}
			
			if ($delete == true) {
				$return .= " onclick=\" return confirm('This action cannot be undone. Continue?');\"" . $additionalParameters;
			} elseif ($additionalParameters == true) {
				$return .= $additionalParameters;
			}
			
			$return .= ">" . prepare($text) . "</a>";
		} else {
			$return = "<a href=\"javascript:void\" onclick=\"window.open('" . $URL . "','Window','status=yes,scrollbars=yes,resizable=yes,width=" . $width . ",height=" . $height . "')\"";
			
			if ($toolTip == true) {
				 $return .= " onmouseover=\"Tip('" . prepare($toolTip, true, false) . "')\" onmouseout=\"UnTip()\"";
			}
			
			if ($class == true) {
				$return .= " class=\"" . $class . "\"";
			}
			
			$return .= ">" . $text . "</a>";
		}
		
		return $return;
	}
	
//Create a tooltip
	function tip($text, $contents, $class = false) {
		$return = "\n<span onmouseover=\"Tip('" . $text . "')\" onmouseout=\"UnTip()\"";
		
		if ($class == true) {
			$return .= " class=\"" . $class . "\"";
		}
		
		$return .= ">" . $contents . "</span>\n";
		
		return $return;
	}
	
//Sideboxes
	function sideBox($title, $type, $text, $allowRoles = false, $editID = false) {
		//Display the title
		echo "\n<div class=\"block_course_list sideblock\">\n<div class=\"header\">\n<div class=\"title\">" . $title;
		
		//Detirimine whether or not the edit link should be displayed
		$premitted = false;
		
		if (loggedIn() && $allowRoles == true) {
			foreach (explode(",", $allowRoles) as $role) {
				if ($_SESSION['role'] == $role) {
					$premitted = true;
				}
			}
		}
		
		//Display the content
		switch ($type) {
			case "Custom Content" :				
				if (!loggedIn()) {
					echo "</div>\n</div>\n<div class=\"content\">\n" . $text . "\n</div>\n";
				} elseif (loggedIn() && $premitted == true) {
					echo "&nbsp;" . URL("", "cms/manage_sidebar.php?id=" . $editID, "smallEdit") . "</div>\n</div>\n<div class=\"content\">" . $text . "\n</div>\n";
				} else {
					echo "</div>\n</div>\n<div class=\"content\">\n" . $text . "\n</div>\n";
				}
				
				break;
				
			case "Login" :
				$roles = explode(",", $allowRoles);
			
				if (!loggedIn()) {
					echo "</div>\n</div>\n<div class=\"content\">";
					echo form("login");
					echo "<p>User name: <br />";
					echo textField("userName", "userName", "25");
					echo "<br />Password: <br />";
					echo textField("passWord", "passWord", "25", false, true);
					echo"</p>\n<p>";
					echo button("submit", "submit", "Login", "submit");
					echo "</p>";
					echo closeForm(false, false);
					echo "</div>";
				} elseif (loggedIn() && $premitted == true) {
					echo "&nbsp;" . URL("", "cms/manage_sidebar.php?id=" . $editID, "smallEdit") . "</div>\n</div>\n";
				} else {
					echo "</div>\n</div>\n";
				}
				
				break;
				
			case "Register" :
				$roles = explode(",", $allowRoles);
			
				if (!loggedIn()) {
					echo "</div>\n</div>\n<div class=\"content\">\n" . $text;
					echo button("register", "register", "Register", "cancel", "register.php");
					echo "</div>\n";
				} elseif (loggedIn() && $premitted == true) {
					echo "&nbsp;" . URL("", "cms/manage_sidebar.php?id=" . $editID, "smallEdit") . "</div>\n</div>\n";
				} else {
					echo "</div>\n</div>\n";
				}
				
				break;
		}
		
		//Close the HTML
		echo "</div>\n<br />\n";
	}
	
//Generate a charting component
	function chart($type, $source, $width = false, $height = false) {
		global $root;
		
		if ($width == false) {
			$width = "600";
		}
		
		if ($height == false) {
			$height = "350";
		}
		
		echo "<div align=\"center\">\n<embed type=\"application/x-shockwave-flash\" \nsrc=\"" . $root . "statistics/charts/" . $type . ".swf\" id=\"chart\" name=\"chart\" quality=\"high\" allowscriptaccess=\"always\" \nflashvars=\"chartWidth=" . $width . "&chartHeight=" . $height . "&debugMode=0&DOMId=overallstats&registerWithJS=0&scaleMode=noScale&lang=EN&dataURL=" . $root . "statistics/data/index.php?type=" . $source . "\" \nwmode=\"transparent\" width=\"" . $width . "\" height=\"" . $height . "\">\n</div>";

	}
	
/*
Elements used in data table loops
---------------------------------------------------------
*/

//Live action, for hiding/showing an element, or checking/unchecking a fake checkbox
	function option($id, $state, $checkboxTrigger, $type) {
		if ($type == "visible") {			
			if ($state == "") {
				$class = " hidden";
			} else {
				$class = "";
			}
		} else {
			$type = "checked";
			
			if ($state == "") {
				$class = " unchecked";
			} else {
				$class = "";
			}
		}
		
		echo "<div align=\"center\">";
		form("avaliability");
		hidden("action", "action", "setAvaliability");
		hidden("id", "id", $id);
		echo URL("", "#option" . $id, $type . $class);
		echo "<div class=\"contentHide\">";
		checkbox("option", "option" . $id, false, false, false, false, $checkboxTrigger, $checkboxTrigger, "visible", "on", " onclick=\"Spry.Utils.submitForm(this.form);\"");
		echo "</div>";
		closeForm(false, false);
		echo "</div>";
	}
	
//Reorder items
	function reorderMenu($id, $state, $menuTrigger, $table) {
		$itemCount = query("SELECT * FROM {$table}", "num");
		$values = "";
		
		for ($count = 1; $count <= $itemCount; $count++) {			
			if ($count < $itemCount) {
				$values .= $count . ",";
			} else {
				$values .= $count;
			}
		}
		
		form("reorder");
		hidden("id", "id", $id);
		hidden("currentPosition", "currentPosition", $state);
		hidden("action", "action", "modifyPosition");
		dropDown("position", "position", $values, $values, false, false, false, $state, false, false, " onchange=\"this.form.submit();\"");
		closeForm(false, false);
	}

/*
Module related constructor functions
---------------------------------------------------------
*/
	
//Lesson content
	function lesson($id, $table, $preview = false) {
		global $monitor, $root;
		
		if ($preview == false) {
			$URL = $_SERVER['PHP_SELF'] . "?id=" . $id . "&";
		} else {
			$URL = $_SERVER['PHP_SELF'] . "?";
		}
		
	//Grab all of the lesson and module data
		$moduleData = query("SELECT * FROM `moduledata` WHERE `id` = '{$id}'");
	
		if (isset($_GET['page'])) {
			if (exist($table) == true) {
				$page = $_GET['page'];
				$lesson = exist($table, "position", $page);
				
				if ($lesson = exist($table, "position", $page)) {
					//Do nothing
				} else {
					redirect($URL . "page=1");
				}
			} else {
				redirect($_SERVER['PHP_SHELF']);
			}
		} else {
			redirect($URL . "page=1");
		}
		
		echo "<div class=\"layoutControl\">";
		
	//Display the title and navigation
		if ($preview !== "miniPreview") {
			$previousPage = intval($_GET['page']) - 1;
			$nextPage = intval($_GET['page']) + 1;
			
			echo "<div class=\"toolBar noPadding\">";
			title($lesson['title'], false, false, "lessonTitle");
			
			$navigation = "<div align=\"center\">";
			
			if (exist($table, "position", $previousPage) == true) {
				$navigation .= URL("Previous Step", $URL . "page=" . $previousPage , "previousPage");
				
				if (exist($table, "position", $nextPage) || (!exist($table, "position", $nextPage) && !access("modifyModule"))) {
					$navigation .= " | ";
				}
			}
			
			if (exist($table, "position", $nextPage) == true) {
				$navigation .= URL("Next Step", $URL . "page=" . $nextPage , "nextPage");
			}
		}
		
		if ($preview == false && $_SESSION['MM_UserGroup'] != "Site Administrator") {
			$userData = userData();
			$accessGrabber = query("SELECT * FROM `users` WHERE `id` = '{$userData['id']}'");
			$accessArray = unserialize($accessGrabber['modules']);
			
			if ($accessArray[$id]['testStatus'] == "F") {
				$testURL = "review.php?id=" . $id;
				$text = "Review Test";
			} else {
				$testURL = $URL . "action=finish";
				$text = "Proceed to Test";
			}
			
			if (!exist($table, "position", $nextPage) && !access("modifyModule")) {
				if ($moduleData['reference'] == "0" && $accessArray[$id]['testStatus'] != "F") {
					$alert = " onclick=\"return confirm('This action will close and lock access to the lesson until you have completed the test. Continue?')\"";
				} else {
					$alert = false;
				}
				
				$navigation .= URL($text, $testURL, "nextPage", false, false, false, false, false, false, $alert);
			} elseif (!exist($table, "position", $nextPage) && $moduleData['test'] == "0") {				
				$navigation .= URL("Finish", $testURL, "nextPage");
			}
		}
		
		if ($preview !== "miniPreview") {
			$navigation .= "</div>";
			
			echo $navigation . "</div><p>&nbsp;</p>";
		}
		
		if ($preview == false) {
			echo "<div class=\"dataLeft\">";
			$pagesGrabber = query("SELECT * FROM `{$table}` ORDER BY `position` ASC", "raw");
			$text = "";
			
			while($pages = fetch($pagesGrabber)) {
				if ($_GET['page'] != $pages['position']) {
					$text .= "<p>" . URL(prepare($pages['title'], false, true), "lesson.php?id=" . $_GET['id'] . "&page=" . $pages['position']) . "</p>";
				} else {
					$text .= "<p><span class=\"currentPage\">" . prepare($pages['title'], false, true) . "</span></p>";
				}
			}
			
			sideBox("Lesson Navigation", "Custom Content", $text);
			echo "</div><div class=\"contentRight\">";
		} else {
			echo "<div>";
		}
		
	//Display the content		
		echo prepare($lesson['content'], false, true);
		
		if (!empty($lesson['attachment'])) {
			echo "<br />";
			$location = str_replace(" ", "", $id);
			$file = $root . "gateway.php/modules/" . $location . "/lesson/" . $lesson['attachment'];
			$fileType = extension($file);
			echo "<div align=\"center\">";
			
			switch ($fileType) {
			//If it is a PDF
				case "pdf" : echo "<iframe src=\"" . $file . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
			//If it is a Word Document
				case "doc" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/word2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				case "docx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/word2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is a PowerPoint Presentation
				case "ppt" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/powerPoint2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				case "pptx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/powerPoint2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is an Excel Spreadsheet
				case "xls" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/excel2003.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
				case "xlsx" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/excel2007.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is a Standard Text Document
				case "txt" : echo "<iframe src=\"" . $file . "\" width=\"100%\" height=\"700\" frameborder=\"0\"></iframe>"; break;
				case "rtf" : echo "<a href=\"" . $file . "\" target=\"_blank\"><img src=\"" . $root . "system/images/programIcons/text.png\" alt=\"icon\" width=\"52\" height=\"52\" border=\"0\" style=\"vertical-align:middle;\" /></a>&nbsp;&nbsp;&nbsp;<a href=\"" . $file . "\" target=\"_blank\">Click to download this file</a>"; break;
			//If it is a WAV audio file
				case "wav" : echo "<object width=\"640\" height=\"16\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\"><param name=\"src\" value=\"" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"controller\" value=\"true\"><embed src=\"" . $file . "\" width=\"640\" height=\"16\" autoplay=\"true\" controller=\"true\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed></object>"; break;
			//If it is an MP3 audio file
				case "mp3" : echo "<object id=\"player\" width=\"640\" height=\"30\" data=\"" . $root . "system/flash/player.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"" . $root . "system/flash/player.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":\"" . $file . "\", \"plugins\":{\"controls\":{\"autoHide\":false}}}' /></object>"; break;
			//If it is an AVI video file
				case "avi" : echo "<object id=\"MediaPlayer\" width=\"640\" height=\"480\" classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" standby=\"Loading Windows Media Player components...\" type=\"application/x-oleobject\"><param name=\"FileName\" value=\"" . $file . "\"><param name=\"autostart\" value=\"true\"><param name=\"ShowControls\" value=\"true\"><param name=\"ShowStatusBar\" value=\"true\"><param name=\"ShowDisplay\" value=\"false\"><embed type=\"application/x-mplayer2\" src=\"" . $file . "\" name=\"MediaPlayer\"width=\"640\" height=\"480\" showcontrols=\"1\" showstatusBar=\"1\" showdisplay=\"0\" autostart=\"1\"></embed></object>"; break;
			//If it is an WMV video file
				case "wmv" : echo "<object id=\"MediaPlayer\" width=\"640\" height=\"480\" classid=\"CLSID:22D6F312-B0F6-11D0-94AB-0080C74C7E95\" standby=\"Loading Windows Media Player components...\" type=\"application/x-oleobject\"><param name=\"FileName\" value=\"" . $file . "\"><param name=\"autostart\" value=\"true\"><param name=\"ShowControls\" value=\"true\"><param name=\"ShowStatusBar\" value=\"true\"><param name=\"ShowDisplay\" value=\"false\"><embed type=\"application/x-mplayer2\" src=\"" . $file . "\" name=\"MediaPlayer\"width=\"640\" height=\"480\" showcontrols=\"1\" showstatusBar=\"1\" showdisplay=\"0\" autostart=\"1\"></embed></object>"; break;
			//If it is an FLV file
				case "flv" : echo "<object id=\"player\" width=\"640\" height=\"480\" data=\"" . $root . "system/flash/player.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"" . $root . "system/flash/player.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":\"" . $file . "\"}' /></object>"; break;
			//If it is an MOV video file
				case "mov" : echo "<object width=\"640\" height=\"480\" classid=\"clsid:02BF25D5-8C17-4B23-BC80-D3488ABDDC6B\" codebase=\"http://www.apple.com/qtactivex/qtplugin.cab\"><param name=\"src\" value=\"" . $file . "\"><param name=\"autoplay\" value=\"true\"><param name=\"controller\" value=\"true\"><embed src=\"" . $file . "\" width=\"640\" height=\"480\" autoplay=\"true\" controller=\"true\" pluginspage=\"http://www.apple.com/quicktime/download/\"></embed></object>"; break;
			//If it is an MP4 video file			
				case "mp4" : echo "<object id=\"player\" width=\"640\" height=\"480\" data=\"" . $root . "system/flash/player.swf\" type=\"application/x-shockwave-flash\"><param name=\"movie\" value=\"" . $root . "system/flash/player.swf\" /><param name=\"allowfullscreen\" value=\"true\" /><param name=\"flashvars\" value='config={\"clip\":\"" . $file . "\"}' /></object>"; break;
			//If it is a SWF video file
				case "swf" : echo "<object width=\"640\" height=\"480\" data=\"" . $file . "\" type=\"application/x-shockwave-flash\">
<param name=\"src\" value=\"" . $file . "\" /></object>"; break;
			}
			
			echo "</div>";
		}
		
		echo "</div></div>";
		
		if ($preview !== "miniPreview") {
			echo "<p>&nbsp;</p>" . $navigation;
		}
	}
	
//Test content
	function test($table, $fileURL, $preview = false) {
		global $connDBA, $testValues, $monitor;
		$attempt = lastItem($testTable, "testID", $testID, "attempt");
		
		if ($attempt - 1 == 0) {
			$currentAttempt = 1;
		} else {
			$currentAttempt = $attempt - 1;
		}
		
		form("test", "post", true);
		echo "<table width=\"100%\" class=\"dataTable\">";
		
		if ($preview == true) {
			if (is_numeric($preview)) {
				$additionalSQL = " WHERE `id` = '{$preview}'";
				$limit = " LIMIT 1";
			} else {
				$additionalSQL = "";
				$limit = "";
			}
		} else {
			$userData = userData();
			$testID = str_replace("moduletest_", "", $table);
			$selectionGrabber = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$testID}'", "raw");
			$additionalSQLConstruct = " WHERE ";
			
			while ($selection = fetch($selectionGrabber)) {
				$additionalSQLConstruct .= "`id` = '{$selection['questionID']}' OR ";
			}
			
			$additionalSQL = rtrim($additionalSQLConstruct, " OR ") . " AND `testID` = '{$testID}' AND `attempt` = '{$currentAttempt}'";
			$limit = "";
		}
		
		if ($table != "questionbank_0" && $preview != false) {
			$order = " ORDER BY `position` ASC";
			$grab = "*";
			$join = "";
		} elseif (is_numeric($preview)) {
			$order = "";
			$grab = "*";
			$join = "";
		} else {
			$moduleInfo = query("SELECT * FROM `moduledata` WHERE `id` = '{$_GET['id']}'");
			
			if ($moduleInfo['randomizeAll'] == "Randomize") {
				$order = " ORDER BY `randomPosition` ASC";
			} else {
				$order = " ORDER BY `position` ASC";
			}
			
			$grab = $table . ".*, testdata_" . $userData['id'] . ".randomPosition, testdata_" . $userData['id'] . ".answerValueScrambled";
			$join = " LEFT JOIN testdata_" . $userData['id'] . " ON " . $table . ".id = testdata_" . $userData['id'] . ".questionID";
		}
		
		if (!is_numeric($preview)) {
			$moduleInfo = query("SELECT * FROM	`moduledata` WHERE `id` = '{$testID}'");
		}
		
		$testDataGrabber = query("SELECT {$grab} FROM `{$table}`{$join}{$additionalSQL}{$order}{$limit}", "raw");
		$count = 1;
		$restrictImport = array();
		
	  	while ($testDataLoop = fetch($testDataGrabber)) {
			if ($preview == false) {
				$testValues = query("SELECT * FROM `testdata_{$userData['id']}` WHERE `testID` = '{$testID}' AND `questionID` = '{$testDataLoop['id']}'");
			}
			
			if ($table != "questionbank_0" && $testDataLoop['questionBank'] == "1") {
				$importID = $testDataLoop['linkID'];
				$testData = query("SELECT * FROM `questionbank_0` WHERE `id` = '{$importID}'");
			} else {
				$testData = $testDataLoop;
			}
			
			if (!is_numeric($preview) && isset($testData['link']) && exist($table, "id", $testData['link']) && $moduleInfo['randomizeAll'] == "Randomize" && !empty($testData['link']) && $testDataLoop['link'] != "0" && !in_array($testDataLoop['link'], $restrictImport)) {
				$importDescription = query("SELECT * FROM `{$table}` WHERE `id` = '{$testDataLoop['link']}'");
				
				if ($importDescription['questionBank'] == "1") {
					$importDescription = query("SELECT * FROM `questionbank_0` WHERE `id` = '{$importDescription['linkID']}'");
				} else {
					$importDescription = $importDescription;
				}
				
				echo "<tr><td colspan=\"2\" valign=\"top\">" . prepare($importDescription['question'], false, true) . "</td></tr>";
				array_push($restrictImport, $testDataLoop['link']);
			}
			
			if ($testData['type'] != "Description") {
				echo "<tr><td width=\"100\" valign=\"top\"><p>";
				
				if (!is_numeric($preview)) {
					echo "<span class=\"questionNumber\">Question " . $count++ . "</span><br />";
				}
				
				echo "<span class=\"questionPoints\">" . $testData['points'] . " ";
				
				if ($testData['points'] == "1") {
					echo "Point";
				} else {
					echo "Points";
				}
				
				echo "</span>";
				
				if ($testData['extraCredit'] == "on") {
					echo "<br /><br /><span class=\"extraCredit\" onmouseover=\"Tip('Extra credit')\" onmouseout=\"UnTip()\"></span>";
				}
				
				echo "</p></td><td valign=\"top\">" . prepare($testData['question'], false, true);
				
				if ($testData['choiceType'] == "checkbox") {
					echo "(There may be more than one correct answer.)<br />";
				}
				
				echo "<br /><br />";
			}
			
			switch ($testData['type']) {
				case "Description" : 
					if (!in_array($testDataLoop['id'], $restrictImport)) {
						echo "<tr><td colspan=\"2\" valign=\"top\">" . prepare($testData['question'], false, true) . "</td></tr>";
						array_push($restrictImport, $testDataLoop['id']);
					}
					
					break;
				case "Essay" : 
					if (isset($testValues)) {
						textArea($testDataLoop['id'], $testDataLoop['id'], "small", true, false, unserialize($testValues['userAnswer']));
					} else {
						textArea($testDataLoop['id'], $testDataLoop['id'], "small", true);
					}
						
					break;
					
				case "File Response" : 
					if ($testData['totalFiles'] > 1 || sizeof(unserialize($testValues['userAnswer'])) > 1) {
						if (isset($monitor)) {
							$URL = $monitor['gatewayPath'] . "/test/responses";
						} else {
							$URL = "../gateway.php/modules/" . $_GET['id'] . "/test/responses";
							$fillValue = unserialize($testValues['userAnswer']);
						}
						
						echo "<table name=\"upload_" . $testDataLoop['id'] . "\" id=\"upload_" . $testDataLoop['id'] . "\">";
						
						if (isset($testValues) && !empty($fillValue)) {
							$fileID = 1;
							
							foreach ($fillValue as $key => $file) {
								echo "<tr id=\"" . $fileID . "\"><td>";
								
								fileUpload($testDataLoop['id'] . "_" . $fileID, $testDataLoop['id'] . "_" . $fileID, false, true, false, $fillValue[$key], false, false, $URL, false, true);
								echo "</td><td>" . URL("", $_SERVER['REQUEST_URI'] . "&delete=true&questionID=" . $testDataLoop['id'] . "&fileID=" . $fileID, "action smallDelete", false, false, false, false, false, false, " onclick=\"return confirm('This action will delete this file. Continue?')\"");
								echo "</td></tr>";
								
								$fileID++;
							}
							
							unset($fileID);
							
							echo "</table><p><span class=\"smallAdd\" id=\"add_" . $testDataLoop['id'] . "\" onclick=\"addFile('upload_" . $testDataLoop['id'] . "', '<input id=\'" . $testDataLoop['id'] . "_', '\' name=\'" . $testDataLoop['id'] . "_', '\' type=\'file\' size=\'50\' class=\'validate[required]\' />', '" . $testData['totalFiles'] . "');\">Add Another File</span></p><p><strong>Note:</strong> Uploading a new file will replace the existing one.</p>";
						} else {
							echo "<tr id=\"1\"><td>";
							fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true, false, false, false, false, false, false, true);
							echo "</td><td><span class=\"action smallDelete\" onclick=\"deleteObject('upload_" . $testDataLoop['id'] . "', '1', '1', true)\"></span>";
							echo "</td></tr></table><p><span class=\"smallAdd\" id=\"add_" . $testDataLoop['id'] . "\" onclick=\"addFile('upload_" . $testDataLoop['id'] . "', '<input id=\'" . $testDataLoop['id'] . "_', '\' name=\'" . $testDataLoop['id'] . "_', '\' type=\'file\' size=\'50\' class=\'validate[required]\' />', '" . $testData['totalFiles'] . "');\">Add Another File</span></p>";
						}
						
						echo "<p>Max file size (for single file): " . ini_get('upload_max_filesize') . "<br>Max file size (for all files): " . ini_get('post_max_size') . "</p>";
					} else {
						if (isset($testValues)) {
							$fillValue = unserialize($testValues['userAnswer']);
							
							if (!empty($fillValue)) {
								echo "<table name=\"upload_" . $testDataLoop['id'] . "\" id=\"upload_" . $testDataLoop['id'] . "\">";
								echo "<tr id=\"1\"><td>";
								fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true, false, $fillValue['0'], false, false, "../gateway.php/modules/" . $_GET['id'] . "/test/responses", false, false);
								echo "</td><td>" . URL("", $_SERVER['REQUEST_URI'] . "&delete=true&questionID=" . $testDataLoop['id'] . "&fileID=1", "action smallDelete", false, false, false, false, false, false, " return confirm('This action will delete this file. Continue?')");
								echo "</td></tr></table>";
								echo "<p><strong>Note:</strong> Uploading a new file will replace the existing one.</p>";
							} else {
								fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true);
							}
						} else {
							fileUpload($testDataLoop['id'] . "_1", $testDataLoop['id'] . "_1", false, true);
						}
					}
					
					break;
					
				case "Fill in the Blank" : 
					$blankQuestion = unserialize($testData['questionValue']);
					$blank = unserialize($testData['answerValue']);
					$answerCompare = unserialize($testData['answerValue']);
					$valueNumbers = sizeof($blankQuestion);
					$matchingCount = 1;
					echo "<p>";
					
					for ($list = 0; $list <= $valueNumbers - 1; $list++) {
					   echo prepare($blankQuestion[$list], false, true) . " ";
					   
					   if (!empty($blank[$list])) {
						   if (isset($testValues)) {
							   $value = unserialize($testValues['userAnswer']);
							   
							   if (is_array($value)) {
								   if (array_key_exists($list, $value)) {
									   echo textField($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount++, false, false, false, true, false, $value[$list])  . " ";
								   } elseif (!array_key_exists($list, $value) && isset($answerCompare[$list])) {
									   echo textField($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount++, false, false, false, true)  . " ";
								   }
							   } else {
								    echo textField($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount++, false, false, false, true)  . " ";
							   }
						   } else {
						   		echo textField($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount++, false, false, false, true)  . " ";
						   }
					   }
					}
					
					echo "</p>";
					break;
				
				case "Matching" : 
					$question = unserialize($testData['questionValue']);
					$answer = unserialize($testValues['answerValueScrambled']);
					$answerCompare = unserialize($testData['answerValue']);
					$valueNumbers = sizeof($question);
					$matchingCount = 1;
					$fillValue = unserialize($testValues['userAnswer']);
					
					echo "<table width=\"100%\">";
					
					for ($list = 0; $list <= $valueNumbers - 1; $list++) {
						echo "<tr><td width=\"20\">";
						$dropDownValue = "-,";
						$dropDownID = ",";
						
						for ($value = 1; $value <= $valueNumbers; $value++) {
							$dropDownValue .= $value . ",";
							$dropDownID .= $value . ",";
						}
						
						$values = rtrim($dropDownValue, ",");
						$IDs = rtrim($dropDownID, ",");
						
						if (isset($testValues)) {
							$value = unserialize($testValues['userAnswer']);
							 
							if (is_array($value)) {
								if (array_key_exists($list, $value)) {
									echo dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true, false, $fillValue[$list])  . " ";
								} elseif (!array_key_exists($list, $value) && isset($answerCompare[$list])) {
									dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true)  . " ";
								}
							} else {
								dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true)  . " ";
							}
						} else {
							dropDown($testDataLoop['id'] . "[]", $testDataLoop['id'] . "_" . $matchingCount, $values, $IDs, false, true);
						}
						
						echo"</td><td width=\"200\"><p>" . prepare($question[$list], false, true) . "</p></td><td width=\"200\"><p>" . $matchingCount++ . ". " . prepare($answer[$list], false, true) . "</p></td></tr>";
						
					}
					
					echo"</table>";				  
					break;
				
				case "Multiple Choice" :									
					if ($preview == true) {
						$questions = unserialize($testData['questionValue']);
						
						if ($testData['randomize'] == "1") {
							$questionsDisplay = $questions;
							shuffle($questionsDisplay);
						} else {
							$questionsDisplay = $questions;
						}
					} else {
						if ($testData['randomize'] == "1") {
							$questions = unserialize($testData['answerValueScrambled']);
						} else {
							$questions = unserialize($testData['questionValue']);
						}
					}
					
					if ($testData['choiceType'] == "radio") {
						$questionValue = "";
						$questionID = "";
					
						while (list($questionKey, $questionArray) = each($questions)) {
							$questionValue .= $questionArray . ",";
							$questionID .= $questionKey + 1 . ",";
						}
						
						$values = rtrim($questionValue, ",");
						$IDs = rtrim($questionID, ",");
						
						
						if (isset($testValues)) {
							radioButton($testDataLoop['id'], $testDataLoop['id'], $values, $IDs, false, true, false, unserialize($testValues['userAnswer']));
						} else {
							radioButton($testDataLoop['id'], $testDataLoop['id'], $values, $IDs, false, true);
						}
						
					} else {
						while (list($questionKey, $questionArray) = each($questions)) {
							$identifier = $questionKey + 1;
							if (isset($testValues)) {
								if (is_array(unserialize($testValues['userAnswer']))) {
									$fillValue = unserialize($testValues['userAnswer']);
								} else {
									$fillValue = array(unserialize($testValues['userAnswer']));
								}
								
								if (in_array($identifier, $fillValue)) {
									checkbox($testDataLoop['id'] . "[]", $testDataLoop['id'] . $identifier, $questionArray, $identifier, true, "1", true);
								} else {
									checkbox($testDataLoop['id'] . "[]", $testDataLoop['id'] . $identifier, $questionArray, $identifier, true, "1");
								}
							} else {
								checkbox($testDataLoop['id'] . "[]", $testDataLoop['id'] . $identifier, $questionArray, $identifier, true, "1");
							}
							
							echo "<br />";
						}
					}
					
					break;
					
				case "Short Answer" : 
					if (isset($testValues)) {
						textField($testDataLoop['id'], $testDataLoop['id'], false, false, false, true, false, unserialize($testValues['userAnswer']));
					} else {
						textField($testDataLoop['id'], $testDataLoop['id'], false, false, false, true);
					}
					
					break;
					
				case "True False" : 
					if ($preview == false) {
						if ($testData['randomize'] == "1") {						
							$label = unserialize($testValues['answerValueScrambled']);
							$id = implode(",", $label);
						} else {
							$label = unserialize($testValues['answerValue']);
							$id = implode(",", $label);
						}
					} else {
						$label = array("1", "0");
						
						if ($testData['randomize'] == "1") {
							shuffle($label);
						}
						
						if ($label['0'] == "1") {
							$id = "1,";
						} else {
							$id = "0,";
						}
						
						if ($label['1'] == "1") {
							$id .= "0";
						} else {
							$id .= "1";
						}
					}
					
					if ($label['0'] == "1") {
						$values = "True,";
					} else {
						$values = "False,";
					}
					
					if ($label['1'] == "1") {
						$values .= "True";
					} else {
						$values .= "False";
					}
					
					if (isset($testValues)) {
						radioButton($testDataLoop['id'], $testDataLoop['id'], $values, $id, true, true, false, unserialize($testValues['userAnswer']));
					} else {
						radioButton($testDataLoop['id'], $testDataLoop['id'], $values, $id, true, true);
					}
					
					break;
			}
			
			if ($testData['type'] != "Description") {
				echo "<br /><br /></td></tr>";
			}
		}
		
		echo "</table>";
		
		if ($preview == false) {
			echo "<blockquote><p>";
			button("save", "save", "Save", "submit", false);
			button("submit", "submit", "Submit", "submit", false, " return confirm('Once the test is submitted, it cannot be reopened. Continue?');");
			echo "</p></blockquote>";
		}
		
		closeForm(false, true);
	}
	
//Include the uploadify readying function
	function uploadifyTrigger($fileID, $formID) {
		global $root;
		
		$fileLimit = sprintf(ereg_replace("[^0-9]", "", ini_get('upload_max_filesize')) * 1024 * 1024);
		
		echo "<script type=\"text/javascript\">\$(function() {\$('#" . $fileID . "').uploadify({'uploader' : '" . $root . "system/flash/upload.swf', 'script' : '" . $_SERVER['REQUEST_URI'] . "', 'cancelImg' : '" . $root . "system/images/common/x.png', 'sizeLimit' : " . $fileLimit . "});});</script>";
	}
	
//Provide a letter grade for a test
	function grade($recieved, $total) {
		$score = round(sprintf($recieved / $total) * 100);
		
		switch ($score) {			
			case $score >= 90 :
				$letter = "A";
				$characterPrep = 100 - $score;
				break;
				
			case 80 <= $score && $score < 90 :
				$letter = "B";
				$characterPrep = 90 - $score;
				break;
				
			case 70 <= $score && $score < 80 :
				$letter = "C";
				$characterPrep = 80 - $score;
				break;
				
			case 60 <= $score && $score < 70 :
				$letter = "D";
				$characterPrep = 70 - $score;
				break;
				
			case $score < 60 :
				$letter = "F";
				$characterPrep = 60 - $score;
				break;
		}
		
		if ($score < 100) {
			switch (abs($characterPrep)) {
				case $characterPrep >= 7 :
					$character = "+";
					break;
					
				case $characterPrep <= 3 && $characterPrep < 7 :
					$character = "";
					break;
					
				case $characterPrep < 3 :
					$character = "-";
					break;
			}
		} else {
			$character = "+";
		}
		
		return $letter . $character;
	}
?>