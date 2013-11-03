<?php

/* Begin form visual functions */		
	//Insert an error window, which will report errors live
	function errorWindow($type, $message, $phpGet = false, $phpError = false, $liveError = false) {
		global $connDBA;
		global $root;
		
		if ($type == "database") {
			if ($liveError == true) {
				if (isset($_GET[$phpGet]) && $_GET[$phpGet] == $phpError) {
						echo "<div align=\"center\" id=\"errorWindow\">" . errorMessage($message) . "</div>";
				} else {
					echo "<div align=\"center\" id=\"errorWindow\"><p>&nbsp;</p></div>";
				}
			} else {
				if ($_GET[$phpGet] == $phpError) {
						echo errorMessage($message);
				} else {
					echo "<p>&nbsp;</p>";
				}
			}
		}
		
		if ($type == "extension") {
			echo "<div align=\"center\"><div id=\"errorWindow\" class=\"error\" style=\"display:none;\">" .$message . "</div></div>";
		}
	}
/* End form visual functions */
	
/* Begin system functions */
	//Generate a random string
	function randomValue($length = 8, $seeds = 'alphanum') {
		global $connDBA;
		global $root;
		
		$seedings['alpha'] = 'abcdefghijklmnopqrstuvwqyz';
		$seedings['numeric'] = '0123456789';
		$seedings['alphanum'] = 'abcdefghijklmnopqrstuvwqyz0123456789';
		$seedings['hexidec'] = '0123456789abcdef';
		
		if (isset($seedings[$seeds])) {
			$seeds = $seedings[$seeds];
		}
		
		list($usec, $sec) = explode(' ', microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		mt_srand($seed);
		
		$string = '';
		$seeds_count = strlen($seeds);
		
		for ($i = 0; $length > $i; $i++) {
			$string .= $seeds{mt_rand(0, $seeds_count - 1)};
		}
		
		return $string;
	}
	
	//A function to limit the length of the directions
	function commentTrim ($length, $value) {
		global $connDBA;
		global $root;
		
	   $commentsStrip = preg_replace("/<img[^>]+\>/i", "(image)", $value);
	   $comments = strip_tags($commentsStrip);
	   $maxLength = $length;
	   $countValue = html_entity_decode($comments);
	   if (strlen($countValue) <= $maxLength) {
		  return stripslashes($comments);
	   }
	
	   $shortenedValue = substr($countValue, 0, $maxLength - 3) . "...";
	   return $shortenedValue;
	}
	
	//A function to prepare to display values from a database
	function prepare($item, $htmlEncode = false, $stripSlashes = true) {
		if ($stripSlashes == true) {
			if ($htmlEncode == true) {
				return htmlentities(stripslashes($item));
			} else {
				return stripslashes($item);
			}
		} else {
			if ($htmlEncode == true) {
				return htmlentities($item);
			} else {
				return $item;
			}
		}
	}
	
	//A function to check the extension of a file
	function extension ($targetFile) {
		$entension = explode(".", $targetFile);
		$value = count($entension)-1;
		$entension = $entension[$value];
		$output = strtolower($entension);
		
		if($output == "php" || $output == "php3" || $output == "php4" || $output == "php5" || $output == "tpl" || $output == "php-dist" || $output == "phtml" || $output == "htaccess" || $output == "htpassword") {
			die(errorMessage("Your file is a potential threat to this system, in which case, it was not uploaded"));
			return false;
			exit;
		} else {
			return $output;
		}
	}
	
	//A function to shuffle an array, while preserving the keys
	function shufflePreserve($array) {
		$preserveArray = array();
		
		while (list($arrayKey, $arrayValue) = each($array)) {
			$preserveArray[] = array("key" => $arrayKey, "value" => $arrayValue);
		}
		
		shuffle($preserveArray);
		$returnArray = array();
		
		while (list($returnArrayKey, $returnArrayValue) = each($returnArray)) {
			$returnArray[$returnArrayKey] = $returnArrayValue;
		}
		
		return $returnArray;
	}
	
	//A case-insensitive version of in_array()
	function inArray($needle, $haystack) {
		if (is_array($haystack)) {
			foreach($haystack as $value) {
				if (is_array($value)) {
					if (inArray($needle, $value)) {
						return true;
						exit;
					}
				} else {
					if (strtolower($value) == strtolower($needle)) {
						return true;
						exit;
					}
				}
			}
			
			return false;
		} else {
			return false;
		}
	}
	
	//A function to flatten a nested array
	function flatten($array) {
		if (!is_array($array)) {
			return array($array);
		}
	
		$result = array();
		
		foreach ($array as $value) {
			$result = array_merge($result, flatten($value));
		}
	
		return array_unique($result);
	}

	//A function to return of the size of an array, even if several values are left empty
	function size($array) {
		$return = end($array);
		return key($return);
	}
	
	//A function to remove an array value by the element
	function removeElement ($array, $element) {
		$return = array();
		
		for($count = 0; $count <= sizeof($array); $count ++) {
			if ($array[$count] === $element) {
				unset($array[$count]);
			} else {
				array_push($return, $array[$count]);
			}
		}
		
		return $return;
	}
	
	//A function to grab grab the previous item in the database
	function lastItem($table, $whereColumn = false, $whereValue = false, $column = false) {
		global $connDBA;
		
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
		
		$lastItemGrabber = query("SELECT * FROM {$table}{$where} ORDER BY {$column} DESC", "raw", false);
		
		if ($lastItemGrabber) {
			$lastItem = mysql_fetch_array($lastItemGrabber);
			return $lastItem[$column] + 1;
		} else {
			return "1";
		}
	}
	
	//A function to return the mime type of a file
	function getMimeType($filename, $debug = false) {
		if ( function_exists( 'finfo_open' ) && function_exists( 'finfo_file' ) && function_exists( 'finfo_close' ) ) {
			$fileinfo = finfo_open( FILEINFO_MIME );
			$mime_type = finfo_file( $fileinfo, $filename );
			finfo_close( $fileinfo );
			
			if ( ! empty( $mime_type ) ) {
				if ( true === $debug )
					return array( 'mime_type' => $mime_type, 'method' => 'fileinfo' );
				return $mime_type;
			}
		}
		if ( function_exists( 'mime_content_type' ) ) {
			$mime_type = mime_content_type( $filename );
			
			if ( ! empty( $mime_type ) ) {
				if ( true === $debug )
					return array( 'mime_type' => $mime_type, 'method' => 'mime_content_type' );
				return $mime_type;
			}
		}
		
		$mime_types = array(
			'ai'      => 'application/postscript',
			'aif'     => 'audio/x-aiff',
			'aifc'    => 'audio/x-aiff',
			'aiff'    => 'audio/x-aiff',
			'asc'     => 'text/plain',
			'asf'     => 'video/x-ms-asf',
			'asx'     => 'video/x-ms-asf',
			'au'      => 'audio/basic',
			'avi'     => 'video/x-msvideo',
			'bcpio'   => 'application/x-bcpio',
			'bin'     => 'application/octet-stream',
			'bmp'     => 'image/bmp',
			'bz2'     => 'application/x-bzip2',
			'cdf'     => 'application/x-netcdf',
			'chrt'    => 'application/x-kchart',
			'class'   => 'application/octet-stream',
			'cpio'    => 'application/x-cpio',
			'cpt'     => 'application/mac-compactpro',
			'csh'     => 'application/x-csh',
			'css'     => 'text/css',
			'dcr'     => 'application/x-director',
			'dir'     => 'application/x-director',
			'djv'     => 'image/vnd.djvu',
			'djvu'    => 'image/vnd.djvu',
			'dll'     => 'application/octet-stream',
			'dms'     => 'application/octet-stream',
			'dvi'     => 'application/x-dvi',
			'dxr'     => 'application/x-director',
			'eps'     => 'application/postscript',
			'etx'     => 'text/x-setext',
			'exe'     => 'application/octet-stream',
			'ez'      => 'application/andrew-inset',
			'flv'     => 'video/x-flv',
			'gif'     => 'image/gif',
			'gtar'    => 'application/x-gtar',
			'gz'      => 'application/x-gzip',
			'hdf'     => 'application/x-hdf',
			'hqx'     => 'application/mac-binhex40',
			'htm'     => 'text/html',
			'html'    => 'text/html',
			'ice'     => 'x-conference/x-cooltalk',
			'ief'     => 'image/ief',
			'iges'    => 'model/iges',
			'igs'     => 'model/iges',
			'img'     => 'application/octet-stream',
			'iso'     => 'application/octet-stream',
			'jad'     => 'text/vnd.sun.j2me.app-descriptor',
			'jar'     => 'application/x-java-archive',
			'jnlp'    => 'application/x-java-jnlp-file',
			'jpe'     => 'image/jpeg',
			'jpeg'    => 'image/jpeg',
			'jpg'     => 'image/jpeg',
			'js'      => 'application/x-javascript',
			'kar'     => 'audio/midi',
			'kil'     => 'application/x-killustrator',
			'kpr'     => 'application/x-kpresenter',
			'kpt'     => 'application/x-kpresenter',
			'ksp'     => 'application/x-kspread',
			'kwd'     => 'application/x-kword',
			'kwt'     => 'application/x-kword',
			'latex'   => 'application/x-latex',
			'lha'     => 'application/octet-stream',
			'lzh'     => 'application/octet-stream',
			'm3u'     => 'audio/x-mpegurl',
			'man'     => 'application/x-troff-man',
			'me'      => 'application/x-troff-me',
			'mesh'    => 'model/mesh',
			'mid'     => 'audio/midi',
			'midi'    => 'audio/midi',
			'mif'     => 'application/vnd.mif',
			'mov'     => 'video/quicktime',
			'movie'   => 'video/x-sgi-movie',
			'mp2'     => 'audio/mpeg',
			'mp3'     => 'audio/mpeg',
			'mp4'     => 'video/mp4',
			'mpe'     => 'video/mpeg',
			'mpeg'    => 'video/mpeg',
			'mpg'     => 'video/mpeg',
			'mpga'    => 'audio/mpeg',
			'ms'      => 'application/x-troff-ms',
			'msh'     => 'model/mesh',
			'mxu'     => 'video/vnd.mpegurl',
			'nc'      => 'application/x-netcdf',
			'odb'     => 'application/vnd.oasis.opendocument.database',
			'odc'     => 'application/vnd.oasis.opendocument.chart',
			'odf'     => 'application/vnd.oasis.opendocument.formula',
			'odg'     => 'application/vnd.oasis.opendocument.graphics',
			'odi'     => 'application/vnd.oasis.opendocument.image',
			'odm'     => 'application/vnd.oasis.opendocument.text-master',
			'odp'     => 'application/vnd.oasis.opendocument.presentation',
			'ods'     => 'application/vnd.oasis.opendocument.spreadsheet',
			'odt'     => 'application/vnd.oasis.opendocument.text',
			'ogg'     => 'application/ogg',
			'otg'     => 'application/vnd.oasis.opendocument.graphics-template',
			'oth'     => 'application/vnd.oasis.opendocument.text-web',
			'otp'     => 'application/vnd.oasis.opendocument.presentation-template',
			'ots'     => 'application/vnd.oasis.opendocument.spreadsheet-template',
			'ott'     => 'application/vnd.oasis.opendocument.text-template',
			'pbm'     => 'image/x-portable-bitmap',
			'pdb'     => 'chemical/x-pdb',
			'pdf'     => 'application/pdf',
			'pgm'     => 'image/x-portable-graymap',
			'pgn'     => 'application/x-chess-pgn',
			'png'     => 'image/png',
			'pnm'     => 'image/x-portable-anymap',
			'ppm'     => 'image/x-portable-pixmap',
			'ps'      => 'application/postscript',
			'qt'      => 'video/quicktime',
			'ra'      => 'audio/x-realaudio',
			'ram'     => 'audio/x-pn-realaudio',
			'ras'     => 'image/x-cmu-raster',
			'rgb'     => 'image/x-rgb',
			'rm'      => 'audio/x-pn-realaudio',
			'roff'    => 'application/x-troff',
			'rpm'     => 'application/x-rpm',
			'rtf'     => 'text/rtf',
			'rtx'     => 'text/richtext',
			'sgm'     => 'text/sgml',
			'sgml'    => 'text/sgml',
			'sh'      => 'application/x-sh',
			'shar'    => 'application/x-shar',
			'silo'    => 'model/mesh',
			'sis'     => 'application/vnd.symbian.install',
			'sit'     => 'application/x-stuffit',
			'skd'     => 'application/x-koan',
			'skm'     => 'application/x-koan',
			'skp'     => 'application/x-koan',
			'skt'     => 'application/x-koan',
			'smi'     => 'application/smil',
			'smil'    => 'application/smil',
			'snd'     => 'audio/basic',
			'so'      => 'application/octet-stream',
			'spl'     => 'application/x-futuresplash',
			'src'     => 'application/x-wais-source',
			'stc'     => 'application/vnd.sun.xml.calc.template',
			'std'     => 'application/vnd.sun.xml.draw.template',
			'sti'     => 'application/vnd.sun.xml.impress.template',
			'stw'     => 'application/vnd.sun.xml.writer.template',
			'sv4cpio' => 'application/x-sv4cpio',
			'sv4crc'  => 'application/x-sv4crc',
			'swf'     => 'application/x-shockwave-flash',
			'sxc'     => 'application/vnd.sun.xml.calc',
			'sxd'     => 'application/vnd.sun.xml.draw',
			'sxg'     => 'application/vnd.sun.xml.writer.global',
			'sxi'     => 'application/vnd.sun.xml.impress',
			'sxm'     => 'application/vnd.sun.xml.math',
			'sxw'     => 'application/vnd.sun.xml.writer',
			't'       => 'application/x-troff',
			'tar'     => 'application/x-tar',
			'tcl'     => 'application/x-tcl',
			'tex'     => 'application/x-tex',
			'texi'    => 'application/x-texinfo',
			'texinfo' => 'application/x-texinfo',
			'tgz'     => 'application/x-gzip',
			'tif'     => 'image/tiff',
			'tiff'    => 'image/tiff',
			'torrent' => 'application/x-bittorrent',
			'tr'      => 'application/x-troff',
			'tsv'     => 'text/tab-separated-values',
			'txt'     => 'text/plain',
			'ustar'   => 'application/x-ustar',
			'vcd'     => 'application/x-cdlink',
			'vrml'    => 'model/vrml',
			'wav'     => 'audio/x-wav',
			'wax'     => 'audio/x-ms-wax',
			'wbmp'    => 'image/vnd.wap.wbmp',
			'wbxml'   => 'application/vnd.wap.wbxml',
			'wm'      => 'video/x-ms-wm',
			'wma'     => 'audio/x-ms-wma',
			'wml'     => 'text/vnd.wap.wml',
			'wmlc'    => 'application/vnd.wap.wmlc',
			'wmls'    => 'text/vnd.wap.wmlscript',
			'wmlsc'   => 'application/vnd.wap.wmlscriptc',
			'wmv'     => 'video/x-ms-wmv',
			'wmx'     => 'video/x-ms-wmx',
			'wrl'     => 'model/vrml',
			'wvx'     => 'video/x-ms-wvx',
			'xbm'     => 'image/x-xbitmap',
			'xht'     => 'application/xhtml+xml',
			'xhtml'   => 'application/xhtml+xml',
			'xml'     => 'text/xml',
			'xpm'     => 'image/x-xpixmap',
			'xsl'     => 'text/xml',
			'xwd'     => 'image/x-xwindowdump',
			'xyz'     => 'chemical/x-xyz',
			'zip'     => 'application/zip',
			'doc'     => 'application/msword',
			'dot'     => 'application/msword',
			'docx'    => 'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
			'dotx'    => 'application/vnd.openxmlformats-officedocument.wordprocessingml.template',
			'docm'    => 'application/vnd.ms-word.document.macroEnabled.12',
			'dotm'    => 'application/vnd.ms-word.template.macroEnabled.12',
			'xls'     => 'application/vnd.ms-excel',
			'xlt'     => 'application/vnd.ms-excel',
			'xla'     => 'application/vnd.ms-excel',
			'xlsx'    => 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
			'xltx'    => 'application/vnd.openxmlformats-officedocument.spreadsheetml.template',
			'xlsm'    => 'application/vnd.ms-excel.sheet.macroEnabled.12',
			'xltm'    => 'application/vnd.ms-excel.template.macroEnabled.12',
			'xlam'    => 'application/vnd.ms-excel.addin.macroEnabled.12',
			'xlsb'    => 'application/vnd.ms-excel.sheet.binary.macroEnabled.12',
			'ppt'     => 'application/vnd.ms-powerpoint',
			'pot'     => 'application/vnd.ms-powerpoint',
			'pps'     => 'application/vnd.ms-powerpoint',
			'ppa'     => 'application/vnd.ms-powerpoint',
			'pptx'    => 'application/vnd.openxmlformats-officedocument.presentationml.presentation',
			'potx'    => 'application/vnd.openxmlformats-officedocument.presentationml.template',
			'ppsx'    => 'application/vnd.openxmlformats-officedocument.presentationml.slideshow',
			'ppam'    => 'application/vnd.ms-powerpoint.addin.macroEnabled.12',
			'pptm'    => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'potm'    => 'application/vnd.ms-powerpoint.presentation.macroEnabled.12',
			'ppsm'    => 'application/vnd.ms-powerpoint.slideshow.macroEnabled.12'
		);
		
		$ext = strtolower( array_pop( explode( '.', $filename ) ) );
		
		if ( ! empty( $mime_types[$ext] ) ) {
			if ( true === $debug )
				return array( 'mime_type' => $mime_types[$ext], 'method' => 'from_array' );
			return $mime_types[$ext];
		}
		
		if ( true === $debug )
			return array( 'mime_type' => 'application/octet-stream', 'method' => 'last_resort' );
		return 'application/octet-stream';
	}
	
	//A function prepare an uploaded file for storage
	function filePrepare($file) {
		$tempFile = $_FILES[$file] ['tmp_name'];
		$targetFile = basename($_FILES[$file] ['name']);
		$fileNameArray = explode(".", $targetFile);
		$targetFile = "";
		
		for ($count = 0; $count <= sizeof($fileNameArray) - 1; $count++) {
			if ($count == sizeof($fileNameArray) - 2) {
				$targetFile .= $fileNameArray[$count] . " " . randomValue(10, "alphanum") . ".";
			} elseif($count == sizeof($fileNameArray) - 1) {
				$targetFile .= $fileNameArray[$count];
			} else {
				$targetFile .= $fileNameArray[$count] . ".";
			}
		}
		
		$targetFile = mysql_real_escape_string($targetFile);
		
		return $targetFile;
	}
	
	//A function to process an uploaded file
	function fileProcess($fileField, $uploadDirectory, $insertRequired, $updateRequired, $tableCheck, $arrayValue, $emptyURL, $errorUploadURL, $errorMIMEURL = false, array $allowedFiles = NULL) {
		if (isset($_GET['id'])) {
			$id = $_GET['id'];
			$oldFile = query("SELECT * FROM `{$tableCheck}` WHERE `id` = '{$id}'");
			$targetFile = $oldFile[$arrayValue];
		} else {
			$targetFile = "";
		}
		
		if (is_uploaded_file($_FILES[$fileField] ['tmp_name'])) {
			$tempFile = $_FILES[$fileField] ['tmp_name'];
			$targetFile = filePrepare($fileField);	
			$filePath = rtrim($uploadDirectory, "/") . "/" . $targetFile;		
			
			if (!empty($allowedFiles) && $allowedFiles != NULL && !in_array(extension($targetFile), $allowedFiles)) {
				if (!isset($_GET['id'])) {
					redirect($_SERVER['REQUEST_URI'] . "?" . $errorMIMEURL);
				} else {
					redirect($_SERVER['REQUEST_URI'] . "&" . $errorMIMEURL);
				}
			}
			
			if (move_uploaded_file($tempFile, $filePath)) {
				unlink(rtrim($uploadDirectory, "/") . "/" . $oldFile[$arrayValue]);	
			} else {
				if (!isset($_GET['id'])) {
					redirect($_SERVER['REQUEST_URI'] . "?" . $errorUploadURL);
				} else {
					redirect($_SERVER['REQUEST_URI'] . "&" . $errorUploadURL);
				}
			}
		} else {
			if ($insertRequired == true) {
				if (!isset($_GET['id'])) {
					redirect($_SERVER['REQUEST_URI'] . "?" . $emptyURL);
				} else {
					redirect($_SERVER['REQUEST_URI'] . "&" . $emptyURL);
				}
			}
			
			if ($updateRequired == true) {
				if (!isset($_GET['id'])) {
					redirect($_SERVER['REQUEST_URI'] . "?" . $emptyURL);
				} else {
					redirect($_SERVER['REQUEST_URI'] . "&" . $emptyURL);
				}
			}
		}
		
		return $targetFile;
	}
	
	//A function to montior access to the module generator
	function monitor($title, $functions = false, $hideHTML = false) {
		global $connDBA, $strippedRoot;
		
		$titlePrefix = "Module Setup Wizard : ";
		
		if ($hideHTML == true) {
			$class = " class=\"overrideBackground\"";
		} else {
			$class = "";
		}
		
		if (!strstr($_SERVER['REQUEST_URI'], "/module_wizard/")) {
			if ($functions == false) {
				$functions = "showHide";
			} else {
				$functions .= ",showHide";
			}
		}
		
		if (strstr($_SERVER['REQUEST_URI'], "/module_wizard/lesson_settings.php") || strstr($_SERVER['REQUEST_URI'], "/questions/")) {
			$customScript = "<script type=\"text/javascript\">var data = new Spry.Data.XMLDataSet(\"" . $_SERVER['PHP_SELF'] . "?data=xml\", \"/root/group\");</script>";
		} else {
			$customScript = "";
		}
		
		headers($titlePrefix . $title, "Organization Administrator,Site Administrator", $functions, true, $class, false, false, false, false, $hideHTML, $customScript);
		$parentTable = "moduledata";
		$prefix = "";
		
		if (isset($_SESSION['currentModule'])) {
			$lessonTable = $prefix . "modulelesson" . "_" . $_SESSION['currentModule'];
			$testTable = $prefix . "moduletest" . "_" . $_SESSION['currentModule'];
			$directory = "../" . $_SESSION['currentModule'] . "/";
			$gatewayPath = "../../gateway.php/modules/" . $_SESSION['currentModule'] . "/";
			$redirect = "../module_wizard/test_content.php";
			$type = "Module";
			
			if (isset($_SESSION['currentModule'])) {
				$currentModule = $_SESSION['currentModule'];
				$currentTable = $_SESSION['currentModule'];
			} else {
				$currentModule = "";
				$currentTable = "";
			}
			
			$monitor = array("parentTable" => $parentTable, "lessonTable" => $lessonTable, "testTable" => $testTable, "prefix" => $prefix, "directory" => $directory, "gatewayPath" => $gatewayPath, "currentModule" => $currentModule, "currentTable" => $currentTable, "title" => $titlePrefix, "redirect" => $redirect, "type" => $type);
		} else {
			$id = lastItem($parentTable);
			$directory = "../" . $id . "/";
			
			$monitor = array("parentTable" => $parentTable, "title" => $titlePrefix, "prefix" => $prefix, "directory" => $directory);
		}
		
		$pageFile = end(explode("/", $_SERVER['SCRIPT_NAME']));
		
		if (isset($_SESSION['currentModule'])) {
			$id = $_SESSION['currentModule'];
			$moduleDataTestGrabber = mysql_query("SELECT * FROM `{$monitor['parentTable']}` WHERE id = '{$id}'", $connDBA);
			$moduleDataTest = mysql_fetch_array($moduleDataTestGrabber);
			
			if ($moduleDataTestGrabber && empty($moduleDataTest['name'])) {
				$allowedArray = array("index.php", "lesson_settings.php");
				
				if (!in_array($pageFile, $allowedArray)) {
					redirect("lesson_settings.php");
				}
			}
				
			if (!exist($monitor['lessonTable'], "position", "1")) {
				$allowedArray = array("lesson_settings.php", "lesson_content.php", "manage_content.php");
				
				if (!in_array($pageFile, $allowedArray)) {
					redirect("lesson_content.php");
				}
			}
			
			if ($moduleDataTestGrabber && exist($monitor['lessonTable'], "position", "1") && $moduleDataTest['test'] == "0") {
				$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "complete.php");
				
				if (!in_array($pageFile, $allowedArray)) {
					redirect("test_check.php");
				}
			} elseif ($moduleDataTestGrabber && exist($monitor['lessonTable'], "position", "1") && $moduleDataTest['test'] == "1") {
				if (empty($moduleDataTest['testName'])) {
					$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "test_settings.php");
					
					if (!in_array($pageFile, $allowedArray)) {
						redirect("test_settings.php");
					}
				}
				
				if (!empty($moduleDataTest['testName']) && ! exist($monitor['testTable'], "position", "1")) {
					$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "test_settings.php", "question_merge.php", "test_content.php", "preview_question.php", "description.php", "essay.php", "file_response.php", "blank.php", "matching.php", "multiple_choice.php", "short_answer.php", "true_false.php", "question_bank.php", "preview.php");
					
					if (!in_array($pageFile, $allowedArray)) {
						redirect("test_content.php");
					}
				}
				
				if (!empty($moduleDataTest['testName']) && exist($monitor['testTable'], "position", "1")) {
					$allowedArray = array("lesson_settings.php", "lesson_content.php", "preview_page.php", "manage_content.php", "lesson_verify.php", "test_check.php", "test_settings.php", "question_merge.php", "test_content.php", "preview_question.php", "description.php", "essay.php", "file_response.php", "blank.php", "matching.php", "multiple_choice.php", "short_answer.php", "true_false.php", "question_bank.php", "preview.php", "test_verify.php", "complete.php");
					
					if (!in_array($pageFile, $allowedArray)) {
						redirect("test_verify.php");
					}
				}
			}
		} elseif (!isset($_SESSION['currentModule']) && !strstr($_SERVER['REQUEST_URI'], "lesson_settings.php")) {
			if (!isset($_SESSION['questionBank'])) {
				$allowedArray = array("index.php", "lesson_settings.php");
				
				if (!in_array($pageFile, $allowedArray)) {
					redirect("lesson_settings.php");
				}
			} else {
				$allowedArray = array("index.php", "lesson_settings.php", "description.php", "essay.php", "file_response.php", "blank.php", "matching.php", "multiple_choice.php", "short_answer.php", "true_false.php");
				if (!in_array($pageFile, $allowedArray)) {
					redirect("../question_bank/index.php");
				}
			}
		}
		
		return $monitor;
	}
	
	//A function to keep track of steps in a module
	function navigation($title, $text, $break = true) {
		global $connDBA, $monitor;
		
		echo "<div class=\"layoutControl\"><div class=\"contentLeft\">";
		title($monitor['title'] . $title, $text, $break);
		echo "</div><div class=\"dataRight\" style=\"padding-top:15px;\">";
		
		if (isset($_SESSION['currentModule'])) {
			$id = $_SESSION['currentModule'];
			$moduleDataTestGrabber = mysql_query("SELECT * FROM `{$monitor['parentTable']}` WHERE id = '{$id}'", $connDBA);
			$moduleDataTest = mysql_fetch_array($moduleDataTestGrabber);
		}
		
		echo "<ul id=\"navigationmenu\"><li class=\"toplast\"><a name=\"navigation\"><span>Navigation</span></a><ul><li>";
		
		if ($moduleDataTestGrabber && !empty($moduleDataTest['name'])) {
			echo "<li>" . URL("Lesson Settings", "lesson_settings.php") . "</li>";
		} else {
			echo "<li>" . URL("Lesson Settings", "lesson_settings.php") . "</li>";
		}
		
		if ($moduleDataTestGrabber && !empty($moduleDataTest['name'])) {
			echo "<li>" . URL("Lesson Content", "lesson_content.php") . "</li>";
		}
				
		if (exist($monitor['lessonTable'], "position", "1")) {
			echo "<li>" . URL("Verify Lesson", "lesson_verify.php") . "</li>";
		}
		
		if ($moduleDataTestGrabber && exist($monitor['lessonTable'], "position", "1") && $moduleDataTest['test'] == "0") {
			echo "<li>" . URL("Add Test", "test_check.php", "incomplete") . "</li>";
			echo "<li>" . URL("Complete", "complete.php", "complete") . "</li>";
		} elseif ($moduleDataTestGrabber && exist($monitor['lessonTable']) == true && $moduleDataTest['test'] == "1") {
			if ($moduleDataTestGrabber && $moduleDataTest['test'] == "1") {
				echo "<li>" . URL("Test Settings", "test_settings.php") . "</li>";
			}
			
			if (!empty($moduleDataTest['testName'])) {
				echo "<li>" . URL("Test Content", "test_content.php") . "</li>";
			}
			
			if (exist($monitor['testTable'], "position", "1")) {
				echo "<li>" . URL("Verify Test", "test_verify.php") . "</li>";
			}
			
			if (!empty($moduleDataTest['testName']) && exist($monitor['testTable'], "position", "1")) {
				echo "<li>" . URL("Complete", "complete.php", "complete") . "</li>";
			}
		}
		
		echo "</ul></li></ul></div></div>";

	}
	
	//A function to regulate the how questions are inserted and updated
	function insertQuery($type, $moduleQuery, $bankQuery = false, $feedbackQuery = false) {
		global $connDBA, $monitor;
		
		switch ($type) {
			case "Module" :
				query("INSERT INTO `{$monitor['testTable']}` (
						  `id`, `questionBank`, `linkID`, `position`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `link`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
					  ) VALUES (
						  {$moduleQuery}
					  )");
					  
				redirect($monitor['redirect'] . "?inserted=question");
				break;
							
			case "Bank" :
				query("INSERT INTO questionbank_0 (
						  `id`, `type`, `points`, `extraCredit`, `partialCredit`, `difficulty`, `category`, `randomize`, `totalFiles`, `choiceType`, `case`, `tags`, `question`, `questionValue`, `answer`, `answerValue`, `fileURL`, `correctFeedback`, `incorrectFeedback`, `partialFeedback`
					  ) VALUES (							
						  {$bankQuery}
					  )");
				
				$category = prepare($_POST['category'], false, true);
				$categoryID = query("SELECT * FROM `modulecategories` WHERE `category` = '{$category}'");
				
				redirect("../question_bank/index.php?id=" . $categoryID['id'] . "&inserted=question");
				break;
		}
	}
	
	function updateQuery($type, $moduleQuery, $bankQuery = false, $feedbackQuery = false) {
		global $connDBA, $monitor;
		
		if (isset($_GET['id'])) {
			$update = $_GET['id'];
		} elseif (isset($_GET['bankID'])) {
			$update = $_GET['bankID'];
		} elseif (isset($_GET['feedbackID'])) {
			$update = $_GET['feedbackID'];
		}
		
		switch ($type) {
			case "Module" :
				query("UPDATE `{$monitor['testTable']}` SET {$moduleQuery} WHERE `id` = '{$update}'");
				
				redirect($monitor['redirect'] . "?updated=question");
				break;
				
			case "Bank" :
				query("UPDATE `questionbank_0` SET {$bankQuery} WHERE `id` = '{$update}'");
				
				$category = prepare($_POST['category'], false, true);
				$categoryID = query("SELECT * FROM `modulecategories` WHERE `category` = '{$category}'");
				
				redirect("../question_bank/index.php?id=" . $categoryID['id'] . "&updated=question");
				break;
		}
		
	}
	
	//A function to check if a name exists
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
					if ($table == "organizations" && !isset($_GET['id']) && $userInfo['organization'] != "0") {
						$data = query("SELECT * FROM `{$table}` WHERE `id` = '{$userInfo['organization']}'");
						
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
			}
			
			exit;
		}
	}
	
	//Check the user's access to a particular item
	function access($access) {		
		if (isset($_SESSION['MM_UserGroup'])) {
			switch ($_SESSION['MM_UserGroup']) {
				case "Site Administrator" :
					$allowedArray = array("assignOrganization", "modifyAllModules", "accessAllSuggestions", "modifyModule", "moduleStatistics", "manageAllUsers", "manageThisUser", "manageOrganizationUsers", "manageAllOrganizations", "manageAllCommunication");
					
					if (in_array($access, $allowedArray)) {
						return true;
					} else {
						return false;
					}
					
					break;
				
				case "Organization Administrator" :
					$allowedArray = array("modifyModule", "moduleStatistics", "moduleAvailability", "moduleDetails", "manageThisUser", "manageOrganizationUsers", "manageOrganizationGroups", "manageThisOrganization", "manageAllOrganizationCommunication");
					
					if (in_array($access, $allowedArray)) {
						if ($access == "manageThisUser") {
							$currentUser = userData();
							$userDataGrabber = query("SELECT * FROM `users` WHERE `id` = '{$_GET['id']}'", "raw");
							
							if ($userData = mysql_fetch_array($userDataGrabber) && $userData['organization'] == $currentUser['organization']) {
								return true;
							} else {
								return false;
							}
						} else {
							return true;
						}
						
						if ($access == "manageThisOrganization") {
							$userData = userData();
							$organizationDataGrabber = query("SELECT * FROM `organizations` WHERE `id` = '{$_GET['id']}'", "raw");
							
							if ($organizationData = mysql_fetch_array($organizationDataGrabber) && $userData['organization'] == $organizationData['id']) {
								return true;
							} else {
								return false;
							}
						} else {
							return true;
						}
					} else {
						return false;
					}
					
					return false;
					break;
					
				case "Instructor" : 
					$allowedArray = array("viewOrganizationGroups", "moduleDetails", "moduleStatistics", "assignUser");
					
					if (in_array($access, $allowedArray)) {
						return true;
					} else {
						return false;
					}
					
					break;
				
				case "Student" :
					$allowedArray = array("buyModule", "moduleDetails", "manageThisUser");
					
					if (in_array($access, $allowedArray)) {
						if ($access == "manageThisUser") {
							$currentUser = userData();
							
							if ($_GET['id'] == $currentUser['id']) {
								return true;
							} else {
								return false;
							}
						} else {
							return true;
						}
					} else {
						return false;
					}
					break;
			}
		} else {
			$allowedArray = array("buyModule", "moduleDetails");
			
			if (in_array($access, $allowedArray)) {
				return true;
			} else {
				return false;
			}
		}
	}
	
	
	
	
	
	//A function to write the ajax navigation for a page
	function navigate($dataSource, $type) {
		if ($type == "bottom") {
			echo "<br />";
		}
		
		echo "<div align=\"center\">";
		echo "<span spry:region=\"" . $dataSource . "PagedInfo\" align=\"center\">";
		echo "<span spry:if=\"{ds_UnfilteredRowCount} > 1\">";
		echo URL("Previous Page", "javascript:void", "spacerLeft previousPage", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} + 1 != '1'\" onclick=\"" . $dataSource . ".previousPage(); return false;\"");
		echo URL("1", "javascript:void", "searchNumber", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} >= 13 && {ds_PageCount} > 25\" onclick=\"" . $dataSource . ".goToPage('1'); return false;\"");
			
		echo "<span spry:if=\"{ds_CurrentRowNumber} >= 14 && {ds_PageCount} > 25 && {ds_PageCount} != 26\" class=\"currentSearchNumber\">...</span>";
		echo "</span>";
		echo "<span spry:if=\"{ds_UnfilteredRowCount} > 0\" spry:repeatchildren=\"" . $dataSource . "PagedInfo\" class=\"search\">";
		echo URL("{ds_PageNumber}", "javascript:void", "searchNumber", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} != {ds_RowNumber} && {ds_CurrentRowNumber} + 1 <= 13 && {ds_PageNumber} <= 25 && {ds_CurrentRowNumber} + 13 < {ds_PageCount}\" onclick=\"" . $dataSource . ".goToPage('{ds_PageNumber}'); return false;\"");
		echo URL("{ds_PageNumber}", "javascript:void", "searchNumber", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} != {ds_RowNumber} && {ds_CurrentRowNumber} + 1 > 13 && {ds_CurrentRowNumber} + 13 < {ds_PageCount} && ({ds_CurrentRowNumber} < {ds_PageNumber} + 12 && {ds_CurrentRowNumber} > {ds_PageNumber} - 14)\" onclick=\"" . $dataSource . ".goToPage('{ds_PageNumber}'); return false;\"");
		echo URL("{ds_PageNumber}", "javascript:void", "searchNumber", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} != {ds_RowNumber} && {ds_CurrentRowNumber} + 13 >= {ds_PageCount} && {ds_PageNumber} >= {ds_PageCount} - 24\" onclick=\"" . $dataSource . ".goToPage('{ds_PageNumber}'); return false;\"");
		echo "<span spry:if=\"{ds_CurrentRowNumber} == {ds_RowNumber} && {ds_PageCount} > 1\" class=\"currentSearchNumber\">{ds_PageNumber}</span>";
		echo "</span>";
		echo "<span spry:if=\"{ds_UnfilteredRowCount} > 0\">";
		echo "<span spry:if=\"{ds_CurrentRowNumber} + 14 < {ds_PageCount} && {ds_PageCount} > 25 && {ds_PageCount} != 26\" class=\"currentSearchNumber\">...</span>";
		echo URL("{ds_PageCount}", "javascript:void", "searchNumber", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} + 14 <= {ds_PageCount} && {ds_PageCount} > 25\" onclick=\"" . $dataSource . ".goToPage('{ds_PageCount}'); return false;\"");
		echo URL("Next Page", "javascript:void", "spacerRight nextPage", false, false, false, false, false, false, " spry:if=\"{ds_CurrentRowNumber} + 2 <= {ds_PageCount}\" onclick=\"" . $dataSource . ".nextPage(); return false;\"");
		echo "</span>";
		echo "</span>";
		echo "</div>";
		
		if ($type == "top") {
			echo "<br />";
		}
	}
	
	//Find the difference between two dates
	function dateDifference($firstDate, $secondDate, $precision = false) {
		$date = $firstDate - $secondDate;
		$return = "";
		
		if ($date >= 31556926) {
			$return .= floor($date/31556926);
			
			if (floor($date/31556926) == "1") {
				$return .= " Year ";
			} else {
				$return .= " Years ";
			}
			
			$date = ($date%31556926);
		}
		
		if (strtolower($precision) == "years") {
			return $return;
			exit;
		}
		
		if ($date >= 2629744) {
			$return .= floor($date/2629744);
			
			if (floor($date/2629744) == "1") {
				$return .= " Month ";
			} else {
				$return .= " Months ";
			}
			
			$date = ($date%2629744);
		}
		
		if (strtolower($precision) == "months") {
			return $return;
			exit;
		}
		
		if ($date >= 86400) {
			$return .= floor($date/86400);
			
			if (floor($date/86400) == "1") {
				$return .= " Day ";
			} else {
				$return .= " Days ";
			}
			
			$date = ($date%86400);
		}
		
		if (strtolower($precision) == "days") {
			return $return;
			exit;
		}
		
		if ($date >= 3600) {
			$return .= floor($date/3600);
			
			if (floor($date/3600) == "1") {
				$return .= " Hour ";
			} else {
				$return .= " Hours ";
			}
			
			$date = ($date%3600);
		}
		
		if (strtolower($precision) == "hours") {
			return $return;
			exit;
		}
		
		if ($date >= 60) {
			$return .= floor($date/60);
			
			if (floor($date/60) == "1") {
				$return .= " Min ";
			} else {
				$return .= " Mins ";
			}
			
			$date = ($date%60);
		}
		
		if (strtolower($precision) == "minutes") {
			return $return;
			exit;
		}
		
		$return .= $date;
		
		if ($date == "1") {
			$return .= " Sec ";
		} else {
			$return .= " Secs ";
		}
		
		return $return;
	}
	
	//A function to safely mail automated emails
	function autoEmail($to, $subject, $message) {
		$domain = explode(":", $_SERVER['HTTP_HOST']);
		
		if (mail($to, $subject, $message, "From: No Reply<no-reply@" . $domain['0'] . ">")) {
			//Do nothing, the email was sent
		} else {
			die("Error sending automated email.");
		}
	}
/* End system functions */
	
/* Begin statistics tracker */
	//Set the activity meter
	function activity($setActivity = "false") {
		global $root;
		global $connDBA;
		
		if ($setActivity == "true" && isset($_SESSION['MM_Username'])) {
			$userName = $_SESSION['MM_Username'];
			$activityTimestamp = time();
			mysql_query("UPDATE `users` SET `active` = '{$activityTimestamp}' WHERE `userName` = '{$userName}' LIMIT 1", $connDBA);
		}
	}
	
	//Overall statistics
	function stats($doAction = "false") {
		global $root;
		global $connDBA;
		
		if ($doAction == "true") {
			$date = date("M-d-Y");
			$statisticsCheck = mysql_query("SELECT * FROM `overallstatistics` WHERE `date` = '{$date}' LIMIT 1", $connDBA);
			
			if ($result = mysql_fetch_array($statisticsCheck)) {
				$newHit = $result['hits']+1;
				mysql_query("UPDATE `overallstatistics` SET `hits` = '{$newHit}' WHERE `date` = '{$date}' LIMIT 1", $connDBA);
			} else {
				mysql_query("INSERT INTO `overallstatistics` (
							`id`, `date`, `hits`
							) VALUES (
							NULL, '{$date}', '1'
							)");
			}
			
			if (loggedIn()) {
				$userData = userData();
					
				if ($userData['organization'] != "0") {
					$statisticsCheck = mysql_query("SELECT * FROM `organizationstatistics_{$userData['organization']}` WHERE `date` = '{$date}' LIMIT 1", $connDBA);
					
					if ($result = mysql_fetch_array($statisticsCheck)) {
						$newHit = $result['hits']+1;
						mysql_query("UPDATE `organizationstatistics_{$userData['organization']}` SET `hits` = '{$newHit}' WHERE `date` = '{$date}' LIMIT 1", $connDBA);
					} else {
						mysql_query("INSERT INTO `organizationstatistics_{$userData['organization']}` (
									`id`, `date`, `hits`
									) VALUES (
									NULL, '{$date}', '1'
									)");
					}
				}
			}
		}
	}
/* End statistics tracker */

//Force user to change password if required
	if (loggedIn()) {
		$userData = userData();
		$organizationStatus = query("SELECT * FROM `organizations` WHERE `id` = '{$userData['organization']}'");
		$URL = $_SERVER['REQUEST_URI'];
		
		if ($userData['changePassword'] == "on" && !strstr($URL, "logout.php")) {
		//Process the form
			if (isset ($_POST['submitPassword']) && !empty($_POST['oldPassword']) && !empty($_POST['newPassword']) && !empty($_POST['confirmPassword'])) {
				$userName = $_SESSION['MM_Username'];
				$oldPassword = encrypt($_POST['oldPassword']);
				$newPassword = encrypt($_POST['newPassword']);
				$confirmPassword = encrypt($_POST['confirmPassword']);
				$passwordGrabber = mysql_query("SELECT * FROM `users` WHERE `userName` = '{$userName}' AND `passWord` = '{$oldPassword}'", $connDBA);
				$password = mysql_fetch_array($passwordGrabber);
				
				if ($password && $newPassword === $confirmPassword) {
					if ($password['passWord'] != $newPassword) {
						mysql_query("UPDATE `users` SET `passWord` = '{$newPassword}', `changePassword` = '' WHERE `userName` = '{$userName}' AND `passWord` = '{$oldPassword}'", $connDBA);
						
						redirect($root . "portal/index.php");
						exit;
					} else {
						redirect($_SERVER['PHP_SELF'] . "?password=identical");
						exit;
					}
				} else {
					redirect($_SERVER['PHP_SELF'] . "?password=error");
					exit;
				}
			}
			
		//Display the content	
		//Top content
			headers("Change Password", false, "validate");
			
		//Title
			if (!isset($_GET['password'])) {
				title("Change Password", "You are required to change your password before using this site.");
			} else {
				title("Change Password", "You are required to change your password before using this site.", false);
			}
			
		//Display message updates
			message("password", "error", "error", "Either your old password is incorrect, or your new password does not match.");
			message("password", "identical", "error", "Your old password may not be the same as your new password.");
			
		//Password form
			form("updatePassword");			
			echo "<blockquote>";
			directions("Current password", true);
			echo "<blockquote><p>";
			textField("oldPassword", "oldPassword", false, false, true, true);
			echo "</p></blockquote>";
			directions("New password", true);
			echo "<blockquote><p>";
			textField("newPassword", "newPassword", false, false, true, true, ",length[6,30]");
			echo "</p></blockquote>";
			directions("Confirm new password", true);
			echo "<blockquote><p>";
			textField("confirmPassword", "confirmPassword", false, false, true, true, ",length[6,30],confirm[newPassword]");
			echo "</p></blockquote>";
			echo "<blockquote><p>";
			button("submitPassword", "submitPassword", "Submit", "submit");
			echo "</p></blockquote></blockquote>";
			closeForm(false, true);
			
		//Display the footer
			footer();
			
		//Exit so the rest of the page is not loaded
			exit;
		}
	}
	
//Force administrator to setup an organization if needed
	if (loggedIn() && $_SESSION['MM_UserGroup'] == "Organization Administrator" && !strstr($_SERVER['REQUEST_URI'], "manage_organization.php") && !strstr($_SERVER['REQUEST_URI'], "logout.php") && (empty($organizationStatus['specialty']) || empty($organizationStatus['webSite']) || empty($organizationStatus['phone']) || empty($organizationStatus['fax']) || empty($organizationStatus['mailingAddress1']) || empty($organizationStatus['mailingCity']) || empty($organizationStatus['mailingState']) || empty($organizationStatus['mailingZIP']) || empty($organizationStatus['billingAddress1']) || empty($organizationStatus['billingCity']) || empty($organizationStatus['billingState']) || empty($organizationStatus['billingZIP']) || empty($organizationStatus['billingPhone']) || empty($organizationStatus['billingFax']) || empty($organizationStatus['billingEmail']) || empty($organizationStatus['timeZone']))) {
		redirect($root . "organizations/manage_organization.php");
	}
?>