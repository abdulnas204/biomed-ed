<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: February 26th, 2011
Last updated: February 26th, 2011

This script will enroll users in their learning units that 
is free of charge.
*/

//Header functions
	require_once('../../system/core/index.php');
	require_once(relativeAddress("learn/system/php") . "index.php");
	require_once(relativeAddress("learn/system/php") . "functions.php");
	
//Enroll the user in the specified learning unit
	if (isset($_POST['enroll'])) {
		$enrollRequest = $_POST['enroll'];
		$currentUnits = arrayRevert($userData['learningunits']);
		
		if (!is_array($currentUnits)) {
			$currentUnits = array();
		}
		
		if (is_array($enrollRequest)) {
			foreach($enrollRequest as $item) {
				$unitData = query("SELECT * FROM `learningunits` WHERE `id` = '{$item}'");
				
				if ($unitData = exist("learningunits", "id", $unit) && empty($unitData['enablePrice'])) {
					$unit = array("item" => $item, "lessonStatus" => "C", "testStatus" => "C", "startDate" => strtotime("now"), "submitted" => "");
					$currentUnits[$item] = $unit;
				}
			}
		} else {
			$unitData = query("SELECT * FROM `learningunits` WHERE `id` = '{$enrollRequest}'");
			
			if ($unitData = exist("learningunits", "id", $enrollRequest) && empty($unitData['enablePrice'])) {
				$unit = array("item" => $enrollRequest, "lessonStatus" => "C", "testStatus" => "C", "startDate" => strtotime("now"), "submitted" => "");
				$currentUnits[$enrollRequest] = $unit;
			}
		}
		
		$units = arrayStore($currentUnits);
		
		query("UPDATE `users` SET `learningunits` = '{$units}' WHERE `id` = '{$userData['id']}'");
		
		if (isset($_POST['redirect'])) {
			redirect("../lesson.php?id=" . $_POST['enroll']);
		} else {
			echo "success";
		}
	} else {
		redirect("../index.php");
	}
?>