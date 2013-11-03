<?php require_once('../../../Connections/connDBA.php'); ?>
<?php loginCheck("Site Administrator"); ?>
<?php
//Search for questions
	if (isset($_GET['keywords']) && isset($_GET['searchMethod'])) {
		$keywords = $_GET['keywords'];
		$searchMethod = $_GET['searchMethod'];
		
		if (empty($keywords)) {
			header("Location: search.php");
			exit;
		}
		
		if (isset($_GET['limit'])) {
			$limit = $_GET['limit'];
			
			if ($limit == "all") {
				$showAll = "true";
			}
			
			if ($limit == "1") {
				header("Location: search.php");
				exit;
			}
		} else {
			$limit = "25";
		}
		
		if (isset($_GET['sort']) && isset($_GET['order'])) {
			$sortArray = explode(".", $_GET['sort']);
			$sortArrayValues = count($sortArray) - 1;
			
			$sort = " ORDER BY ";
			for ($count = 0; $count <= $sortArrayValues; $count++) {
				if ($_GET['order']) {
					$orderArray = explode(".", $_GET['order']);
					$orderArrayValues = count($orderArray) - 1;
					
					switch($orderArray[$count]) {
						case "ascending" : $order = " ASC"; break;
						case "descending" : $order = " DESC"; break;
					}
				} else {
					$order = " ASC";
				}
				
				if ($orderArrayValues != $sortArrayValues) {
					header("Location: search.php");
					exit;
				}
				
				$sort .= $sortArray[$count];
				
				if ($count != $sortArrayValues) {
					$sort .= $order . ", ";
				} else {
					$sort .= $order . " ";
				}
			}
		} else {
			$sort = " ORDER BY type ASC, tags ";
			$order = "ASC ";
		}
		
		if (!isset($showAll)) {
			$objectNumberGrabber = mysql_query("SELECT * FROM questionBank WHERE `{$searchMethod}` LIKE '%{$keywords}%'", $connDBA);
			$objectNumber = mysql_num_rows($objectNumberGrabber);
			if ($objectNumber == 1) {
				$searchPages = 1;
			} else {
				$searchPages = ceil($objectNumber/$limit);
			}
			
			if (!isset($_GET['page'])) {
				$questionGrabber = mysql_query("SELECT * FROM questionBank WHERE `{$searchMethod}` LIKE '%{$keywords}%'{$sort}LIMIT 0, {$limit}", $connDBA);
			} else {
				$searchPage = $_GET['page'];
				
				if ($searchPage > $searchPages) {
					header("Location: search.php");
					exit;
				}
				
				if ($searchPage == "1") {
					$lowerLimit = ($searchPage*$limit)-$limit;
				
					$questionGrabber = mysql_query("SELECT * FROM questionBank WHERE `{$searchMethod}` LIKE '%{$keywords}%'{$sort}LIMIT 0, {$limit}", $connDBA);
				} else {
					$lowerLimit = ($searchPage*$limit)-$limit;
					
					$questionGrabber = mysql_query("SELECT * FROM questionBank WHERE `{$searchMethod}` LIKE '%{$keywords}%'{$sort}LIMIT {$lowerLimit}, {$limit}", $connDBA);
				}
			}
			
			if (!isset($searchPages) || $searchPages != "1") {
				if (!isset($_GET['page'])) {
					$navigationPage = "1";
				} else {
					$navigationPage = $_GET['page'];
				}
				
				if (isset($_GET['sort']) && isset($_GET['order'])) {
					$additionalParameters = "keywords=" . $_GET['keywords'] . "&searchMethod=" . $_GET['searchMethod'] . "&sort=" . $_GET['sort'] . "&order=" . $_GET['order'] . "&";
				} else {
					$additionalParameters = "keywords=" . $_GET['keywords'] . "&searchMethod=" . $_GET['searchMethod'] . "&";
				}
				
				$navigation = "<div class=\"pagesBox\">";
				if (isset($_GET['page'])) {
					if ($_GET['page'] != "1") {
						$previousPage = $navigationPage - 1;
						
						$navigation .= "<a href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $previousPage . "\">(Previous)</a>";
					}
				}
				
				for ($count = 1; $count <= $searchPages; $count++) {
				//If there are less than or equal to 15 pages, then display them all
					if ($searchPages - 15 <= 1) {
						if ($navigationPage != $count) {
							$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $count . "\">" . $count . "</a>";
						} else {
							$navigation .= "<span class=\"searchNumber currentSearchNumber\">" . $count . "</span>";
						}
					}
					
				//If there are more than or equal to 15 pages
					if ($searchPages - 15 > 1) {
					//If the pages are in the lower set, then only break the upper set
						if ($navigationPage < 8) {
							$orginalUpper = $navigationPage - 7;
							switch ($searchPages - $navigationPage) {
								case "0" : $additionalLower = 6; break;
								case "1" : $additionalLower = 5; break;
								case "2" : $additionalLower = 4; break;
								case "3" : $additionalLower = 3; break;
								case "4" : $additionalLower = 2; break;
								case "5" : $additionalLower = 1; break;
								case "6" : $additionalLower = 0; break;
							}
							
							if ($count <= 14) {
								if ($navigationPage != $count) {
									$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $count . "\">" . $count . "</a>";
								} else {
									$navigation .= "<span class=\"searchNumber currentSearchNumber\">" . $count . "</span>";
								}
								
								if  ($count == 14) {
									$navigation .= "...<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $searchPages . "\">" . $searchPages . "</a>";
								}
							}
						}
						
					//If the pages are somewhere in the middle
						if ($navigationPage >= 8) {	
						//If this page is not one page after the first page, break the list (e.g.: NOT 1 3, BUT 1 ... 3)
							$additionalLower = 0;
							
							switch ($searchPages - $navigationPage) {
								case "0" : $additionalLower = 6; break;
								case "1" : $additionalLower = 5; break;
								case "2" : $additionalLower = 4; break;
								case "3" : $additionalLower = 3; break;
								case "4" : $additionalLower = 2; break;
								case "5" : $additionalLower = 1; break;
								case "6" : $additionalLower = 0; break;
							}
							
							if ($count == $navigationPage - 6 - $additionalLower && $count != 2) {
								$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=1\">1</a>...";
							} elseif ($count == $navigationPage - 6 - $additionalLower && $count == 2) {
								$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=1\">1</a>";
							}
						
						//Do not break the upper set of pages, if the user is approaching the end, and display a constant number of suggestions				
							if ($navigationPage + 7 > $searchPages) {
								$orginalLower = $navigationPage - 7;
								
								if ($orginalLower - $additionalLower < $count && $count < $navigationPage + 7) {
									if ($navigationPage != $count) {
										$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $count . "\">" . $count . "</a>";
									} else {
										$navigation .= "<span class=\"searchNumber currentSearchNumber\">" . $count . "</span>";
									}
								}
						//Display all pages in the center with a value of +- 6, with the upper and lower extremes
							} else {
							//For all pages in the center of the list
								if ($navigationPage - 7 < $count && $count < $navigationPage + 7) {
								//For the one page before the last page, do not break the list (e.g.: NOT 18 ... 19, BUT 18 19)
									if ($count + 1 == $searchPages) {									
										if ($navigationPage != $count) {
											$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $count . "\">" . $count . "</a>";
										} else {
											$navigation .= "<span class=\"searchNumber currentSearchNumber\">" . $count . "</span>";
										}
										
										$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $searchPages . "\">" . $searchPages . "</a>";
										break;
									}
									
								//If this page is not one page before the last page, break the list (e.g.: NOT 17 19, BUT 17 ... 19)	
									if ($count + 1 != $searchPages) {
										if ($navigationPage != $count) {
											$navigation .= "<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $count . "\">" . $count . "</a>";
										} else {
											$navigation .= "<span class=\"searchNumber currentSearchNumber\">" . $count . "</span>";
										}
									}
									
									if ($count == $navigationPage + 6) {
										$navigation .= "...<a class=\"searchNumber\" href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $searchPages . "\">" . $searchPages . "</a>";
									}
								}
							}
						}
					}
				}
				
				if (isset($_GET['page'])) {
					if ($_GET['page'] != $searchPages) {
						$nextPage = $navigationPage + 1;
						
						$navigation .= "<a href=\"?" . $additionalParameters . "limit=" . $limit . "&page=" . $nextPage . "\">(Next)</a>";
					}
				} else {
					$navigation .= "<a href=\"?" . $additionalParameters . "limit=" . $limit . "&page=2\">(Next)</a>";
				}
				$navigation .= "</div><br />";
			}
		} else {
			$questionGrabber = mysql_query("SELECT * FROM questionBank WHERE `{$searchMethod}` LIKE '%{$keywords}%'{$sort}", $connDBA);
		}
		
		$questionNumberGrabber = mysql_query("SELECT * FROM questionBank WHERE `{$searchMethod}` LIKE '%{$keywords}%' ORDER BY type ASC, tags ASC", $connDBA);
		$questionNumber = mysql_num_rows($questionNumberGrabber);
		
		if (!$questionNumber) {
			header("Location: search.php?suggestions=display");
			exit;
		}
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<?php title("Search for Questions"); ?>
<?php headers(); ?>
<?php validate(); ?>
<script src="../../../javascripts/common/openWindow.js" type="text/javascript"></script>
</head>
<body<?php bodyClass(); ?>>
<?php toolTip(); ?>
<?php topPage("site_administrator/includes/top_menu.php"); ?>
<h2>Search for Questions</h2>
<?php
	if (!isset($_GET['keywords']) && !isset($_GET['searchMethod']) && !isset($_GET['suggestions'])) {
		echo "<p>Search for questions in the question bank by either the keywords, or the question itself.</p><p>&nbsp;</p><form id=\"search\" name=\"search\" method=\"get\" action=\"search.php\">
			<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" class=\"\">
			  <tr>
				<td width=\"30%\"><div align=\"right\">Keywords:</div></td>
				<td width=\"70%\"><div align=\"left\">
				  <input name=\"keywords\" id=\"keywords\" size=\"50\" autocomplete=\"off\" type=\"text\" />
				</div></td>
			  </tr>
			  <tr>
				<td width=\"30%\"><div align=\"right\">Search by:</div></td>
				<td width=\"70%\"><div align=\"left\">
				  <select name=\"searchMethod\" id=\"searchMethod\">
					<option value=\"tags\" selected=\"selected\">Tags</option>
					<option value=\"question\">Question</option>
				  </select>
				</div></td>
			  </tr>
			  <tr>
				<td width=\"30%\"><div align=\"right\"></div></td>
				<td width=\"70%\"><div align=\"left\">
					<input name=\"submit\" id=\"submit\" value=\"Submit\" type=\"submit\" />
				</div></td>
			  </tr>
		  </table>
		</form>";
		formErrors();
	} else {
		if (isset($_GET['suggestions'])) {
			echo "<p>Search for questions in the question bank by either the keywords, or the question itself.</p><p>&nbsp;</p>";
			$failedMessage = "<p>Your search keywords did not return any results. Try checking your spelling, and ensuring that you are searching under the correct category.";
			errorMessage($failedMessage);
			
			echo "<form id=\"search\" name=\"search\" method=\"get\" action=\"search.php\">
			<table width=\"100%\" cellpadding=\"0\" cellspacing=\"0\" class=\"\">
			  <tr>
				<td width=\"30%\"><div align=\"right\">Keywords:</div></td>
				<td width=\"70%\"><div align=\"left\">
				  <input name=\"keywords\" id=\"keywords\" size=\"50\" autocomplete=\"off\" type=\"text\" />
				</div></td>
			  </tr>
			  <tr>
				<td width=\"30%\"><div align=\"right\">Search by:</div></td>
				<td width=\"70%\"><div align=\"left\">
				  <select name=\"searchMethod\" id=\"searchMethod\">
					<option value=\"tags\" selected=\"selected\">Tags</option>
					<option value=\"question\">Question</option>
				  </select>
				</div></td>
			  </tr>
			  <tr>
				<td width=\"30%\"><div align=\"right\"></div></td>
				<td width=\"70%\"><div align=\"left\">
					<input name=\"submit\" id=\"submit\" value=\"Submit\" type=\"submit\" />
				</div></td>
			  </tr>
		  </table>
		</form>";
		formErrors();
		} else {
			echo "<p>&nbsp;</p><div class=\"toolBar\"><a class=\"toolBarItem search\" href=\"search.php\">Perform another Search</a><a class=\"toolBarItem back\" href=\"index.php\">Back to Question Bank</a></div><br />";
			
			if (isset($navigation)) {
				echo $navigation;
			}
			
			echo "<table class=\"dataTable\">
			<tbody>
				<tr>";
				
					if (isset($_GET['limit'])) {
						$additionalParameters = "keywords=" . $_GET['keywords'] . "&searchMethod=" . $_GET['searchMethod'] . "&limit=" . $_GET['limit'] . "&page=1&";
					} else {
						$additionalParameters =  "keywords=" . $_GET['keywords'] . "&searchMethod=" . $_GET['searchMethod'] . "&page=1&";
					}
								
					echo "<th width=\"150\" class=\"tableHeader\"><a href=\"search.php";
					if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "type.tags" && $_GET['order'] == "descending.ascending") {
						echo "?" . $additionalParameters . "sort=type.tags&order=ascending.ascending\" class=\"ascending\">Type</a>";
					} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "type.tags" && $_GET['order'] == "ascending.ascending") {
						echo "?" . $additionalParameters . "sort=type.tags&order=descending.ascending\" class=\"descending\">Type</a>";
					} elseif (!isset ($_GET['sort'])) {
						echo "?" . $additionalParameters . "sort=type.tags&order=descending.ascending\" class=\"descending\">Type</a>";
					} else {
						echo "?" . $additionalParameters . "sort=type.tags&order=ascending.ascending\" class=\"sortHover\">Type</a>";
					}
					echo "</th>
					<th width=\"200\" class=\"tableHeader\"><a href=\"search.php";
					if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "tags.type" && $_GET['order'] == "ascending.ascending") {
						echo "?" . $additionalParameters . "sort=tags.type&order=descending.ascending\" class=\"descending\">Tags</a>";
					} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "tags.type" && $_GET['order'] == "descending.ascending") {
						echo "?" . $additionalParameters . "sort=tags.type&order=ascending.ascending\" class=\"ascending\">Tags</a>";
					} else {
						echo "?" . $additionalParameters . "sort=tags.type&order=ascending.ascending\" class=\"sortHover\">Tags</a>";
					}
					echo "</th>
					<th class=\"tableHeader\"><a href=\"search.php";
					if (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "question.type" && $_GET['order'] == "ascending.ascending") {
						echo "?" . $additionalParameters . "sort=question.type&order=descending.ascending\" class=\"descending\">Question</a>";
					} elseif (isset ($_GET['sort']) && isset ($_GET['order']) && $_GET['sort'] == "question.type" && $_GET['order'] == "descending.ascending") {
						echo "?" . $additionalParameters . "sort=question.type&order=ascending.ascending\" class=\"ascending\">Question</a>";
					} else {
						echo "?" . $additionalParameters . "sort=question.type&order=ascending.ascending\" class=\"sortHover\">Question</a>";
					}
					echo "</th>
					<th width=\"50\" class=\"tableHeader\">Discover</th>
					<th width=\"50\" class=\"tableHeader\">Edit</th>
					<th width=\"50\" class=\"tableHeader\">Delete</th>
					
				</tr>";
			$number = 1;
			while(($questionData = mysql_fetch_array($questionGrabber)) && ($number <= $questionNumber)) {
				echo "<tr";
				//Alternate the color of each row.
				if ($number++ & 1) {echo " class=\"odd\">";} else {echo " class=\"even\">";}
				echo "<td width=\"150\"><a href=\"javascript:void\" onclick=\"MM_openBrWindow('preview.php?id=" . $questionData['id'] . "','','status=yes,scrollbars=yes,resizable=yes,width=640,height=480')\">" . $questionData['type'] . "</a></td>" . 
				"<td width=\"200\">";
				
				if ($_GET['searchMethod'] == "tags") {
					if ($questionData['tags'] == "") {
						echo "<span class=\"notAssigned\">None</span>";
					} else {
						echo str_ireplace($_GET['keywords'], "<span class=\"searchKeywords\">" . strtolower($_GET['keywords']) . "</span>", commentTrim(25, $questionData['tags']));	
					}
				} else {
					if ($questionData['tags'] == "") {
						echo "<span class=\"notAssigned\">None</span>";
					} else {
						echo commentTrim(25, $questionData['tags']);
					}
				}
				
				echo "</a></td>" . 
				"<td>";
				
				if ($_GET['searchMethod'] == "question") {
					echo str_ireplace($_GET['keywords'], "<span class=\"searchKeywords\">" . strtolower($_GET['keywords']) . "</span>", commentTrim(65, $questionData['question']));	
				} else {
					echo commentTrim(65, $questionData['question']);
				}
				
				echo "</td>";
				
				echo "<td width=\"50\"><a class=\"action discover\" href=\"discover.php?linkID=" . $questionData['id'] . "\" onmouseover=\"Tip('Discover in which tests this <strong>" . $questionData['type'] . "</strong> question is used')\" onmouseout=\"UnTip()\"></a></td>" . 
				"<td width=\"50\"><a class=\"action edit\" href=\"";
				
				switch ($questionData['type']) {
					case "Description" : echo "questions/description.php"; break;
					case "Essay" : echo "questions/essay.php"; break;
					case "File Response" : echo "questions/file_response.php"; break;
					case "Fill in the Blank" : echo "questions/blank.php"; break;
					case "Matching" : echo "questions/matching.php"; break;
					case "Multiple Choice" : echo "questions/multiple_choice.php"; break;
					case "Short Answer" : echo "questions/short_answer.php"; break;
					case "True False" : echo "questions/true_false.php"; break;
				}
				
				echo "?id=" . $questionData['id'] . "\" onmouseover=\"Tip('Edit this <strong>" .  $questionData['type'] . "</strong> question')\" onmouseout=\"UnTip()\"></a>
				</td>" . "<td width=\"50\"><a class=\"action delete\" href=\"index.php?questionID=" .  $questionData['id'] . "&action=delete\" onclick=\"return confirm ('This action will delete this question from the question bank, and from any of the tests it is currently linked. Continue?');\" onmouseover=\"Tip('Delete this <strong>" . $questionData['type'] . "</strong> question')\" onmouseout=\"UnTip()\"></a></td></tr>";
			}
			echo "</tbody>
			</table>";
			
			if (isset($navigation)) {
				echo "<br />" . $navigation;
			}
		}
	}
?>
<?php footer("site_administrator/includes/bottom_menu.php"); ?>
</body>
</html>