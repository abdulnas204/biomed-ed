<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: Novemeber 28th, 2010
Last updated: Feburary 5th, 2011

This script contains user commonly used functions to process 
simple requests, such as reordering a list, deleting an item, 
or setting its availability.
*/

//Delete a file or directory, and its contents
	function deleteAll($path, $requiredPrivilege = false) {
		if ($requiredPrivilege == true) {
			$doAction = access($requiredPrivilege);
		} else {
			$doAction = true;
		}
		
		if ($doAction == true && file_exists($path)) {
			if (is_file($path)) {
				unlink($path);
			} else {
				$directory = opendir($path);
				
				while($contents = readdir($directory)) {
					if ($contents !== "." && $contents !== "..") {
						if (is_dir($path . "/" . $contents)) {
							deleteAll($path . "/" . $contents);
						} else {
							unlink($path . "/" . $contents);
						}
					}
				}
				
				closedir($directory);				
				rmdir($path);
			}
		} else {
			return false;
		}
	}

//Set an item's avaliability
	function avaliability($table, $redirect, $requiredPrivilege = false) {
		if ($requiredPrivilege == true) {
			$doAction = access($requiredPrivilege);
		} else {
			$doAction = true;
		}
		
		if ($doAction == true && isset($_POST['id']) && $_POST['action'] == "setAvaliability") {			
			$id = $_POST['id'];
			$toggleData = query("SELECT * FROM `{$table}` WHERE `id` = '{$id}'");
			
			if ($toggleData['visible'] == "on") {
				$option = "";
			} else {
				$option = "on";
			}
			
			query("UPDATE `{$table}` SET `visible` = '{$option}' WHERE `id` = '{$id}'");
			redirect($redirect);
		}
	}
	
//Reorder a list of items
	function reorder($table, $redirect, $requiredPrivilege = false) {
		if ($requiredPrivilege == true) {
			$doAction = access($requiredPrivilege);
		} else {
			$doAction = true;
		}
		
		if ($doAction == true && isset($_POST['action']) && $_POST['action'] == "modifyPosition" && isset($_POST['id']) && isset($_POST['position']) && isset($_POST['currentPosition'])) {
			$id = $_POST['id'];
			$newPosition = $_POST['position'];
			$currentPosition = $_POST['currentPosition'];
			
			if (!exist($table, "position", $currentPosition)) {
				redirect($redirect);
			}
		  
			if ($currentPosition > $newPosition) {
				query("UPDATE `{$table}` SET `position` = position + 1 WHERE `position` >= '{$newPosition}' AND `position` <= '{$currentPosition}'");
			} elseif ($currentPosition < $newPosition) {
				query("UPDATE `{$table}` SET `position` = position - 1 WHERE `position` <= '{$newPosition}' AND `position` >= '{$currentPosition}'");
			} else {
				redirect($redirect);
			}
			
			query("UPDATE `{$table}` SET `position` = '{$newPosition}' WHERE `id` = '{$id}'");
			redirect($redirect);
		}
	}
	
//Delete an item
	function delete($table, $redirect = false, $requiredPrivilege = false, $reorder = true, $file = false, $directory = false, $extraTables = false) {
		if ($requiredPrivilege == true) {
			$doAction = access($requiredPrivilege);
		} else {
			$doAction = true;
		}
		
		if ($doAction == true && isset ($_GET['action']) && $_GET['action'] == "delete" && isset($_GET['id'])) {
			if (isset ($_GET['questionID'])) {
				$deleteItem = $_GET['questionID'];
			} else {
				$deleteItem = $_GET['id'];
			}
			
			if (!exist($table, "id", $deleteItem)) {
				redirect($redirect);
			}
			
			if ($reorder == true) {
				$itemPosition = query("SELECT * FROM `{$table}` WHERE `id` = '{$deleteItem}'");
				
				query("UPDATE `{$table}` SET `position` = position - 1 WHERE `position` > '{$itemPosition['position']}'");
				query("DELETE FROM `{$table}` WHERE `id` = '{$deleteItem}'", false, false);
			} else {
				query("DELETE FROM `{$table}` WHERE `id` = '{$deleteItem}'", false, false);
			}
			
			if ($file == true) {
				unlink($file);
			}
			
			if ($directory == true) {
				deleteAll($directory, $requiredPrivilege);
			}
			
			if ($extraTables == true) {
				$tables = explode(",", $extraTables);
				
				foreach ($tables as $table) {
					query("DROP TABLE `{$table}`", false, false);
				}
			}
			
			if ($redirect == true) {
				redirect($redirect);
			}
		}
	}
?>