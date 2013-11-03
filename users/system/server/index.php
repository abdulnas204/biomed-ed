<?php
/*
LICENSE: See "license.php" located at the root installation

This script contains additional functions relevent to this addon only.
*/

/*
Server-side functions
---------------------------------------------------------
*/

//Write the Spry ajax navigation for a page
	function navigate($dataSource, $type) {
		if ($type == "bottom") {
			echo "\n<br />";
		}
		
		echo "\n<div align=\"center\">\n";
		echo "<span spry:region=\"" . $dataSource . "PagedInfo\" align=\"center\">\n";
		echo "<span spry:if=\"{ds_UnfilteredRowCount} > 1\">\n";
		echo URL("Previous Page", "javascript:void", "spacerLeft previousPage", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} + 1 != '1'\" onclick=\"" . $dataSource . ".previousPage(); return false;\"") . "\n";
		echo URL("1", "javascript:void", "searchNumber", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} >= 13 && {ds_PageCount} > 25\" onclick=\"" . $dataSource . ".goToPage('1'); return false;\"") . "\n";
			
		echo "<span spry:if=\"{ds_CurrentRowNumber} >= 14 && {ds_PageCount} > 25 && {ds_PageCount} != 26\" class=\"currentSearchNumber\">...</span>\n";
		echo "</span>\n";
		echo "<span spry:if=\"{ds_UnfilteredRowCount} > 0\" spry:repeatchildren=\"" . $dataSource . "PagedInfo\" class=\"search\">\n";
		echo URL("{ds_PageNumber}", "javascript:void", "searchNumber", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} != {ds_RowNumber} && {ds_CurrentRowNumber} + 1 <= 13 && {ds_PageNumber} <= 25 && {ds_CurrentRowNumber} + 13 < {ds_PageCount}\" onclick=\"" . $dataSource . ".goToPage('{ds_PageNumber}'); return false;\"") . "\n";
		echo URL("{ds_PageNumber}", "javascript:void", "searchNumber", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} != {ds_RowNumber} && {ds_CurrentRowNumber} + 1 > 13 && {ds_CurrentRowNumber} + 13 < {ds_PageCount} && ({ds_CurrentRowNumber} < {ds_PageNumber} + 12 && {ds_CurrentRowNumber} > {ds_PageNumber} - 14)\" onclick=\"" . $dataSource . ".goToPage('{ds_PageNumber}'); return false;\"") . "\n";
		echo URL("{ds_PageNumber}", "javascript:void", "searchNumber", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} != {ds_RowNumber} && {ds_CurrentRowNumber} + 13 >= {ds_PageCount} && {ds_PageNumber} >= {ds_PageCount} - 24\" onclick=\"" . $dataSource . ".goToPage('{ds_PageNumber}'); return false;\"") . "\n";
		echo "<span spry:if=\"{ds_CurrentRowNumber} == {ds_RowNumber} && {ds_PageCount} > 1\" class=\"currentSearchNumber\">{ds_PageNumber}</span>\n";
		echo "</span>\n";
		echo "<span spry:if=\"{ds_UnfilteredRowCount} > 0\">\n";
		echo "<span spry:if=\"{ds_CurrentRowNumber} + 14 < {ds_PageCount} && {ds_PageCount} > 25 && {ds_PageCount} != 26\" class=\"currentSearchNumber\">...</span>\n";
		echo URL("{ds_PageCount}", "javascript:void", "searchNumber", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} + 14 <= {ds_PageCount} && {ds_PageCount} > 25\" onclick=\"" . $dataSource . ".goToPage('{ds_PageCount}'); return false;\"") . "\n";
		echo URL("Next Page", "javascript:void", "spacerRight nextPage", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} + 2 <= {ds_PageCount}\" onclick=\"" . $dataSource . ".nextPage(); return false;\"") . "\n";
		echo "</span>\n";
		echo "</span>\n";
		echo "</div>\n";
		
		if ($type == "top") {
			echo "\n<br />";
		}
	}

/*
Include JavaScripts and CSS for client-side modules
---------------------------------------------------------
*/
?>