<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: Novemeber 27th, 2010
Last updated: February 14th, 2011

This script contains functions used to create common HTML 
elements, and add a touch of dynamic content, while abiding
to system-wide HTML standards.
*/
	
/*
Form input elements
---------------------------------------------------------
*/

//Form initiator
	function form($name, $method = false, $containsFile = false, $action = false, $id = false, $additionalParameters = false) {
		global $root, $protocol;
		
		$return = "\n<form name=\"" . $name . "\"";
		
		if ($method == true) {
			$return .=  " method=\"" . $method . "\"";
		} else {
			$return .=  " method=\"post\"";
		}
		
		if ($id == true) {
			$return .=  " id=\"" . $id . "\"";
		} else {
			$return .=  " id=\"validate\"";
		}
		
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
				$return .= str_replace(".php", ".htm", $_SERVER['PHP_SELF']) . rtrim($parameters, "&");
			} else {
				$return .= str_replace(".php", ".htm", $_SERVER['PHP_SELF']);
			}
		} else {
			if (strstr($action, $root) || !strstr($action, $protocol)) {
				$action = str_replace(".php", ".htm", $action);
			}
			
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
		global $root, $protocol;
		
		if (strstr($URL, $root) || !strstr($URL, $protocol)) {
			$URL = str_replace(".php", ".htm", $URL);
		}
			
		switch ($type) {
			case "submit" : 
				return "\n<input type=\"submit\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"" . ltrim($additionalParameters) . "tinyMCE.triggerSave();\">\n";
				break;
				
			case "reset" : 
				return "\n<input type=\"reset\" name=\"" . $name . "\" id=\"" . $id . "\" value=\"" . $value . "\" onclick=\"return confirm('Are you sure you wish to reset all of the content in this form? Click OK to continue');$.validationEngine.closePrompt('.formError', true);" . $additionalParameters . "\">\n";
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
		
		if ($validateAddition == true) {
			$validateAddition = "," . $validateAddition;
		}
		
		if ($validate == true) {
			$return .= " class=\"validate[required" . $validateAddition . "]\"";
		} elseif ($validate == false && $validateAddition == true) {
			$return .= " class=\"validate[optional" . $validateAddition . "]\"";
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
			
			if ($validateAddition == true) {
				$validateAddition = "," . $validateAddition;
			}
			
			if ($validate == true) {
				$return .= " class=\"validate[required" . $validateAddition . "] radio\"";
			}
			
			if ($additionalParameters == true) {
				$additionalParameters = " " . $additionalParameters;
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
			
			case false : 
			case "small" : 
				$return .= "width:475px;";
				break;
				
			case "extraSmall" : 
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
			
			for($count = 0; $count <= sizeof($searchParameters) - 1; $count ++) {
				if ($searchParameters[$count] == "class=") {
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
		
		if ($validateAddition == true) {
			$validateAddition = "," . $validateAddition;
		}
		
		if ($validate == true) {
			if (isset($returnClass)) {
				$return .= " class=\"validate[required" . $validateAddition . "]" . " " . $returnClass . "\"";
			} else {
				$return .= " class=\"validate[required" . $validateAddition . "]\"";
			}
		}
		
		if ($additionalParameters == true) {
			$additionalParameters = " " . $additionalParameters;
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
		$return = "\n<input type=\"";
		
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
		
		$return .= "\" autocomplete=\"off\" spellcheck=\"true\"";
		
		if ($validateAddition == true) {
			$validateAddition = "," . $validateAddition;
		}
		
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
		
		if ($additionalParameters == true) {
			$additionalParameters = " " . $additionalParameters;
		}
		
		$return .= $additionalParameters . " />\n";
		
		return $return;
	}
	
//Common form closure buttons
	function formButtons() {
		echo "<blockquote><p>";
		echo button("submit", "submit", "Submit", "submit");
		echo button("reset", "reset", "Reset", "reset");
		echo button("cancel", "cancel", "Cancel", "history");
		echo "</p></blockquote>\n";
	}
	
/*
Elements used in form construction, but not actually form elements
---------------------------------------------------------
*/	
		
//Category divider for form layout and categorizing
	function catDivider($content, $class, $first = false, $last = false, $id = false) {
		if ($last == true) {
			echo "\n</div>\n";
		} else {
			if ($first == false) {
				echo "\n</div>\n";
			}
			
			echo "<div class=\"catDivider " . $class . "\"";
			
			if ($id == true) {
				echo " id=\"" . $id . "\"";
			}
			
			echo ">" . $content . "</div>\n";
			
			if ($last == false) {
				echo "<div class=\"stepContent\">\n";
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
		
		if ((is_bool($break) === true && $break === true) || ($break == true && !isset($_GET[$break]))) {
			echo "<p>&nbsp;</p>\n";
		}
	}
	
//Links
	function URL($text, $URL, $class = false, $target = false, $toolTip = false, $delete = false, $newWindow = false, $width = false, $height = false, $additionalParameters = false) {
		global $root, $protocol;
		
		if (strstr($URL, $root) || !strstr($URL, $protocol)) {
			if (!strstr($URL, "gateway.php") && !strstr($URL, "preview.php")) {
				$URL = str_replace(".php", ".htm", $URL);
			}
		}
		
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
	
//Minifies the URL() function for specific use on admin toolbars, offering the $text, $URL, $class, and $delete options, but also adds a layer of security
	function toolBarURL() {
		$values = func_get_args();
		$error = debug_backtrace();
		
		if (isset($values['0'])) {
			$text = $values['0'];
		} else {
			die(errorMessage("Missing a URL value on line " . $error['0']['line']));
		}
		
		if (isset($values['1'])) {
			$URL = $values['1'];
		} else {
			die(errorMessage("Missing a URL value on line " . $error['0']['line']));
		}
		
		if (isset($values['2'])) {
			$class = $values['2'];
		} else {
			die(errorMessage("Missing a URL value on line " . $error['0']['line']));
		}
		
		if (isset($values['3'])) {
			$delete = $values['3'];
		} else {
			$delete = "";
		}
		
		if (sizeof($values) >= 5) {
			$doAction = 0;
			
			for($count = 4; $count <= sizeof($values) - 1; $count ++) {
				if (access($values[$count])) {
					$doAction++;
				}
			}
			
			if ($doAction + 4 === sizeof($values)) {
				return URL($text, $URL, $class, false, false, $delete) . "\n";
			}
		} else {
			return URL($text, $URL, $class, false, false, $delete) . "\n";
		}
	}
	
//Create a tooltip
	function tip($tip, $contents = false, $class = false) {
		$return = "\n<span onmouseover=\"Tip('" . str_replace("'", "\'", $tip) . "')\" onmouseout=\"UnTip()\"";
		
		if ($class == true) {
			$return .= " class=\"" . $class . "\"";
		}
		
		$return .= ">" . $contents . "</span>\n";
		
		return $return;
	}
	
//Sideboxes
	function sideBox($title, $type, $text, $editID = false) {
		//Display the title
		echo "\n<div class=\"block_course_list sideblock\">\n<div class=\"header\">\n<div class=\"title\">" . $title;
		
		//Detirimine whether or not the edit link should be displayed
		$premitted = false;
		
		if (access("Edit Sidebar Items")) {
			$premitted = true;
		} else {
			$premitted = false;
		}
		
		//Display the content
		switch ($type) {
			case "Custom Content" :				
				if (!loggedIn()) {
					echo "</div>\n</div>\n<div class=\"content\">\n" . $text . "\n</div>\n";
				} elseif ($premitted == true) {
					echo "&nbsp;" . URL("", "cms/manage_sidebar.php?id=" . $editID, "smallEdit") . "</div>\n</div>\n<div class=\"content\">" . $text . "\n</div>\n";
				} else {
					echo "</div>\n</div>\n<div class=\"content\">\n" . $text . "\n</div>\n";
				}
				
				break;
				
			case "Login" :
				if (!loggedIn()) {
					echo "</div>\n</div>\n<div class=\"content\">";
					echo $text . "\n";
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
				} elseif ($premitted == true) {
					echo "&nbsp;" . URL("", "cms/manage_sidebar.php?id=" . $editID, "smallEdit") . "</div>\n</div>\n";
				} else {
					echo "</div>\n</div>\n";
				}
				
				break;
				
			case "Register" :
				if (!loggedIn()) {
					echo "</div>\n</div>\n<div class=\"content\">\n" . $text;
					echo button("register", "register", "Register", "cancel", "users/register.php");
					echo "</div>\n";
				} elseif ($premitted == true) {
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
	function chart($chartURL, $source, $width = false, $height = false) {
		global $root;
		
		if ($width == false) {
			$width = "600";
		}
		
		if ($height == false) {
			$height = "350";
		}
		
		return "<div align=\"center\">\n<embed type=\"application/x-shockwave-flash\" align=\"center\" \nsrc=\"" . $chartURL . "\" id=\"chart\" name=\"chart\" quality=\"high\" allowscriptaccess=\"always\" \nflashvars=\"chartWidth=" . $width . "&chartHeight=" . $height . "&debugMode=0&DOMId=chart&registerWithJS=0&scaleMode=noScale&lang=EN&dataURL=" . $source . "\" \nwmode=\"transparent\" width=\"" . $width . "\" height=\"" . $height . "\">\n</div>\n";

	}
	
/*
Elements used in data table loops
---------------------------------------------------------
*/

//Generate a data table column header
	function column($content, $width = false, $requiredPrivilege = false) {
		if ($requiredPrivilege == true) {
			$doAction = access($requiredPrivilege);
		} else {
			$doAction = true;
		}
		
		if ($width == false) {
			$headerWidth = "";
		} else {
			$headerWidth = " width=\"" . $width . "\"";
		}
		
		if ($doAction == true) {
			return "<th class=\"tableHeader\"" . $headerWidth . ">" . $content . "</th>\n";
		}
	}

//Live action, for hiding/showing an element, or checking/unchecking a fake checkbox
	function option($table, $id, $type = "visible", $width = "25", $requiredPrivilege = false) {
		$state = query("SELECT * FROM `{$table}` WHERE `id` = '{$id}'");
		
		if ($requiredPrivilege == true) {
			$doAction = access($requiredPrivilege);
		} else {
			$doAction = true;
		}
		
		if ($type == "visible" || $type == false) {
			$type = "visible";
			
			if ($state['visible'] == "") {
				$class = " hidden";
			} else {
				$class = "";
			}
		} else {
			$type = "checked";
			
			if ($state['visible']== "") {
				$class = " unchecked";
			} else {
				$class = "";
			}
		}
		
		if ($width == false) {
			$cellWidth = "25";
		} else {
			$cellWidth = $width;
		}
		
		if ($doAction == true) {
			$return = "<td width=\"" . $cellWidth . "\">\n";
			$return .= "<div align=\"center\">\n";
			$return .= form("avaliability");
			$return .= hidden("action", "action", "setAvaliability");
			$return .= hidden("id", "id", $id);
			$return .= URL("", "#option" . $id, $type . $class);
			$return .= "\n<div class=\"contentHide\">\n";
			$return .= checkbox("option", "option" . $id, false, false, false, false, false, "state", "visible", "on");
			$return .= "</div>\n";
			$return .= closeForm(false);
			$return .= "</div>\n";
			$return .= "</td>\n";
			
			return $return;
		}
	}
	
//Reorder items
	function reorderMenu($table, $id, $width = "75", $requiredPrivilege = false, $additionalContent = false) {
		$itemCount = query("SELECT * FROM `{$table}`", "num");
		$state = query("SELECT * FROM `{$table}` WHERE `id` = '{$id}'");
		$values = "";
		
		if ($requiredPrivilege == true) {
			$doAction = access($requiredPrivilege);
		} else {
			$doAction = true;
		}
		
		for ($count = 1; $count <= $itemCount; $count++) {			
			if ($count < $itemCount) {
				$values .= $count . ",";
			} else {
				$values .= $count;
			}
		}
		
		if ($width == false) {
			$cellWidth = "75";
		} else {
			$cellWidth = $width;
		}
		
		if ($additionalContent == true) {
			$wrap = explode("{content}", $additionalContent);
			$begin = $wrap['0'];
			$end = $wrap['1'];
		} else {
			$begin = "";
			$end = "";
		}
		
		if ($doAction == true) {
			$return = "<td width=\"" . $cellWidth . "\">\n";
			$return .= $begin . "\n";
			$return .= form("reorder");
			$return .= hidden("id", "id", $id);
			$return .= hidden("currentPosition", "currentPosition", $state['position']);
			$return .= hidden("action", "action", "modifyPosition");
			$return .= dropDown("position", "position", $values, $values, false, false, false, $state['position'], false, false, " onchange=\"this.form.submit();\"");
			$return .= closeForm(false);
			$return .= $end . "\n";
			$return .= "</td>\n";
			
			return $return;
		}
	}
	
//Preview link
	function preview($name, $URL, $itemType = false, $width = false, $newWindow = false, $requiredPrivilege = false) {
		if ($requiredPrivilege == true) {
			$doAction = access($requiredPrivilege);
		} else {
			$doAction = true;
		}
		
		if ($width == false) {
			$cellWidth = "";
		} else {
			$cellWidth = " width =\"" . $width . "\"";
		}
		
		if ($itemType == false) {
			$type = "";
		} else {
			$type = " " . $itemType ;
		}
		
		if ($newWindow == false) {
			echo "<td" . $cellWidth . ">" . URL($name, $URL, false, false, "Launch the <strong>" . $name . "</strong>" . $type) . "</td>\n";
		} else {
			echo "<td" . $cellWidth . ">" . URL($name, $URL, false, false, "Launch the <strong>" . $name . "</strong>" . $type, false, true, "640", "480") . "</td>\n";
		}
	}
	
//Create a regular table cell
	function cell($content, $width = false, $requiredPrivilege = false) {
		if ($requiredPrivilege == true) {
			$doAction = access($requiredPrivilege);
		} else {
			$doAction = true;
		}
		
		if ($width == false) {
			$cellWidth = "";
		} else {
			$cellWidth = " width =\"" . $width . "\"";
		}
		
		if ($doAction == true) {
			return "<td" . $cellWidth . ">" . $content . "</td>\n";
		}
	}
	
//Statistics link
	function statsURL($URL, $name, $width = false, $requiredPrivilege = false) {
		if ($requiredPrivilege == true) {
			$doAction = access($requiredPrivilege);
		} else {
			$doAction = true;
		}
		
		if ($width == false) {
			$cellWidth = "50";
		} else {
			$cellWidth = $width;
		}
		
		if ($doAction == true) {
			return "<td width=\"" . $cellWidth . "\">" . URL(false, $URL, "action statistics", false, "View the <strong>" . $name . "</strong> statistics") . "</td>\n";
		}
	}
	
//Edit link
	function editURL($URL, $name, $itemType = false, $width = false, $requiredPrivilege = false) {
		if ($requiredPrivilege == true) {
			$doAction = access($requiredPrivilege);
		} else {
			$doAction = true;
		}
		
		if ($itemType !== false) {
			$tip = "Edit the <strong>" . $name . "</strong> " . $itemType;
		} else {
			$tip = "Edit <strong>" . $name . "</strong>";
		}
		
		if ($width == false) {
			$cellWidth = "50";
		} else {
			$cellWidth = $width;
		}
		
		if ($doAction == true) {
			return "<td width=\"" . $cellWidth . "\">" . URL(false, $URL, "action edit", false, $tip) . "</td>\n";
		}
	}
	
//Delete link
	function deleteURL($URL, $name, $itemType = false, $deleteMessage = false, $width = false, $requiredPrivilege = false) {
		if ($requiredPrivilege == true) {
			$doAction = access($requiredPrivilege);
		} else {
			$doAction = true;
		}
		
		if ($itemType !== false) {
			$tip = "Delete the <strong>" . $name . "</strong> " . $itemType;
		} else {
			$tip = "Delete <strong>" . $name . "</strong>";
		}
		
		if ($deleteMessage !== false) {
			$autoAlert = false;
			$customAlert = "onclick=\"return confirm('" . $deleteMessage . "')\"";
		} else {
			$autoAlert = true;
			$customAlert = false;
		}
		
		if ($width == false) {
			$cellWidth = "50";
		} else {
			$cellWidth = $width;
		}
		
		if ($doAction == true) {
			return "<td width=\"" . $cellWidth . "\">" . URL(false, $URL, "action delete", false, $tip, $autoAlert, false, false, false, $customAlert) . "</td>\n";
		}
	}
?>