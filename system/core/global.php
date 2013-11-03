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
Last updated: Novemeber 28th, 2010

This script contains user feedback, complete database 
management, and minor code simplification functions which 
will be used globally.
*/

/*
System alerts and messages
---------------------------------------------------------
*/
	
//Error message box
	function errorMessage($errorContent) {
		echo "<p><div align=\"center\"><div style=\"border:solid red; padding:5px; width:75%; text-align:center;\">" . $errorContent . "</div></div></p>";
	}

//Success message box
	function successMessage($successContent) {
		echo "<p><div align=\"center\"><div style=\"border:solid green; padding:5px; width:75%; text-align:center;\">" . $successContent . "</div></div></p>";
	}

//Alert box
	function alert($errorContent) {
		echo "<p><div align=\"center\"><div style=\"border:solid grey; padding:5px; width:75%; text-align:center;\">" . $errorContent . "</div></div></p>";
	}
	
//Generate a message based on URL parameters
	function message($trigger, $triggerValue, $type, $text) {
		global $messageBreakLimit;	
			
		if ((isset($_GET[$trigger]) && $_GET[$trigger] == $triggerValue) || (isset($type) && $trigger == false)) {
			switch($type) {
				case "success" :
					successMessage($text);
					break;
					
				case "error" : 
					errorMessage($text);
					break;
					
				case "alert" : 
					alert($text);
					break;
					
				default : 
					$error = debug_backtrace();
					die(errorMessage("Invalid message type requested on line " .  $error['0']['line']));
					break;
			}
			
			$messageBreakLimit = "true";
		} else {
			if (!isset($messageBreakLimit)) {
				echo "<br />";
			}
			
			$messageBreakLimit = "true";
		}
	}

/*
Database management functions
---------------------------------------------------------
*/

//Clean-up escaped values from a database prior to displaying
	function prepare($item, $htmlEncode = false, $stripSlashes = true) {
		if ($stripSlashes == true) {
		//Strip slashes and return the html entities of a string
			if ($htmlEncode == true) {
				return htmlentities(stripslashes($item));
		//Only strip the slashes of the string, DEFAULT BEHAVIOR
			} else {
				return stripslashes($item);
			}
		} else {
		//Only return the html entities of a string
			if ($htmlEncode == true) {
				return htmlentities($item);
			} else {
				return $item;
			}
		}
	}

//Run a mysql_query
	function query($query, $returnType = false, $showError = true) {
		global $connDBA;
		
		$action = mysql_query($query, $connDBA);
		
	//If no value was returned from the query
		if (!$action) {
		//If allowed to display an error
			if ($showError == true) {
				$error = debug_backtrace();
				die(errorMessage("There is an error with your query: " . $query . "<br /><br />" . mysql_error() . "<br /><br />Error on line: " . $error['0']['line'] . "<br />Error in file: " . $error['0']['file']));
			} else {
				return false;
			}
		} else {
		//If the following words are in a string, then a command was executed on the database, and no value needs to be returned
			if (!strstr($query, "INSERT INTO") && !strstr($query, "UPDATE") && !strstr($query, "SET") && !strstr($query, "CREATE TABLE") && !strstr($query, "ALTER TABLE") && !strstr($query, "DROP TABLE")) {
				switch($returnType) {
				//Fetch the array, and clean-up each value for display, DEFAULT BEHAVIOR
					case false : 
					case "array" : 
						$result = mysql_fetch_array($action);
						
						if (is_array($result) && !empty($result)) {
							array_merge_recursive($result);
							$return = array();
							
							foreach ($result as $key => $value) {
								$return[$key] = prepare($value, false, true);
							}
							
							return $result;
						} else {
							return false;
						}
						
						break;
						
				//Return the raw array
					case "raw" : 
						$actionTest = mysql_query($query, $connDBA);
						$result = mysql_fetch_array($actionTest);
						
						if ($result) {
							return $action;
						} else {
							return false;
						}
						
						break;
							
				//Return the number of rows
					case "num" : 
						$result = mysql_num_rows($action);
						return $result;
						break;
						
				//For a complex return type, where only certain rows are selected
					case "selected" : 
						$return = array();
					
						while ($result = mysql_fetch_array($action)) {
							array_push($return, $result);
						} 
					
						return flatten($return,array());
						break;
						
				//Return an error if an unsupported return-type is requested
					default : 
						$error = debug_backtrace();
						die(errorMessage("Invalid query return-type requested on line " .  $error['0']['line']));
						break;
				}
			}
		}
	}
	
//Check to see if a value exists in the databast
	function exist($table, $column = false, $value = false) {
		global $connDBA;
		
		if ($column == true) {
			$additionalCheck = " WHERE `{$column}` = '{$value}'";
		} else {
			$additionalCheck = "";
		}
		
		$itemCheckGrabber = query("SELECT * FROM `{$table}`{$additionalCheck}", "raw", false);
		
		if ($itemCheckGrabber) {
			$itemCheck = query("SELECT * FROM `{$table}`{$additionalCheck}", "num");
			
			if ($itemCheck >= 1) {
				$item = query("SELECT * FROM `{$table}`{$additionalCheck}");
				
				return $item;
			} else {
				return false;
			}
		} else {
			return false;
		}
	}
	
//Fetch an array for a loop
	function fetch($value) {
		return mysql_fetch_array($value);
	}
	
//Escape a string to store values into a database
	function escape($value) {
		return mysql_real_escape_string($value);
	}
	
//Grab the previous item's position in the database
	function lastItem($table, $whereColumn = false, $whereValue = false, $column = false) {
		if ($column == false) {
			$column = "position";
		} else {
			$column = $column;
		}
		
		if ($whereColumn == true && $whereValue == true) {
			$where = " WHERE `{$whereColumn}` = '{$whereValue}' ";
		} else {
			$where = "";
		}
		
		$lastItemGrabber = query("SELECT * FROM `{$table}`{$where} ORDER BY {$column} DESC", "raw", false);
		
		if ($lastItemGrabber) {
			$lastItem = fetch($lastItemGrabber);
			return $lastItem[$column] + 1;
		} else {
			return "1";
		}
	}
	
//Grab the next primary key ID
	function nextID($table) {
		$key = query("SHOW TABLE STATUS LIKE '{$table}'");
		return $key['Auto_increment'];
	}
	
//Live check if a name exists
	function validateName($table, $column) {
		if (isset($_POST['validateValue']) && isset($_POST['validateId']) && isset($_POST['validateError'])) {
			$value = $_POST['validateValue'];
			$id = $_POST['validateId'];
			$message = $_POST['validateError'];
			
			$return = array();
			$return[0] = $id;
			$return[1] = $message;
		
			if (!query("SELECT * FROM `{$table}` WHERE `{$column}` = '{$value}'", "raw")) {
				$return[2] = "true";
				echo "{\"jsonValidateReturn\":" . json_encode($return) . "}";
			} else {
				$userInfo = userData();
				
				if (isset($_GET['id'])) {
					$data = query("SELECT * FROM `{$table}` WHERE `id` = '{$_GET['id']}'");
					
					if ($data[$column] === $value) {
						$return[2] = "true";
						echo "{\"jsonValidateReturn\":" . json_encode($return) . "}";
					} else {
						$return[2] = "false";
						echo "{\"jsonValidateReturn\":" . json_encode($return) . "}";
					}
				} else {
					$return[2] = "false";
					echo "{\"jsonValidateReturn\":" . json_encode($return) . "}";
				}
			}
			
			exit;
		}
	}
	
/*
Code simplification
---------------------------------------------------------
*/

//Redirect to page
	function redirect($URL) {
		header("Location: " . $URL);
		exit;
	}
?>