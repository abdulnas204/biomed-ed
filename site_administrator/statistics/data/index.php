<?php require_once("../../../Connections/connDBA.php"); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Export as XML file
	header("Content-type: application/xml");
	
	if (isset($_GET['type'])) {
		switch ($_GET['type']) {
			case "overall" : 
				$statisticsCheck = mysql_query("SELECT * FROM `overallstatistics`");
				
				if (mysql_fetch_array($statisticsCheck)) {
					$firstItemGrabber = mysql_query("SELECT * FROM `overallstatistics` ORDER BY `id` ASC LIMIT 1");
					$firstItemArray = mysql_fetch_array($firstItemGrabber);
					$firstItem = $firstItemArray['date'];
					$lastItemGrabber = mysql_query("SELECT * FROM `overallstatistics` ORDER BY `id` DESC LIMIT 1");
					$lastItemArray = mysql_fetch_array($lastItemGrabber);
					$lastItem = $lastItemArray['date'];
					
					$statisticsGrabber = mysql_query("SELECT * FROM `overallstatistics` ORDER BY `id` ASC");
					
					echo "<graph caption=\"Overall Summary of Usage\" subcaption=\"From " . $firstItem . " to " . $lastItem ."\" xAxisName=\"\" yAxisMinValue=\"0\" yAxisName=\"Hits\" decimalPrecision=\"0\" formatNumberScale=\"0\" showNames=\"0\" showValues=\"0\" showAlternateHGridColor=\"1\" AlternateHGridColor=\"ff5904\" divLineColor=\"ff5904\" divLineAlpha=\"20\" alternateHGridAlpha=\"5\" bgAlpha=\"0\" rotateNames=\"1\">
			";
					while($statistics = mysql_fetch_array($statisticsGrabber)) {
						echo "<set name=\"" . $statistics['date'] . "\" value=\"" . $statistics['hits'] . "\" hoverText=\"" . $statistics['date'] . "\"/>";
					}
					echo "</graph>";
				} else {
					echo "<graph caption=\"Overall Summary of Usage\" subcaption=\"No Data\" xAxisName=\"\" yAxisMinValue=\"10\" yAxisName=\"hits\" decimalPrecision=\"0\" formatNumberScale=\"0\" numberPrefix=\"\" showNames=\"0\" showValues=\"0\"  showAlternateHGridColor=\"1\" AlternateHGridColor=\"ff5904\" divLineColor=\"ff5904\" divLineAlpha=\"20\" alternateHGridAlpha=\"5\">";
				}
				
				break;
		}
	}
?>