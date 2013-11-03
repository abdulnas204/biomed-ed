<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------

Created by: Oliver Spryn
Created on: November 28th, 2010
Last updated: Feburary 24th, 2010

This is the back-end function library, which contains 
useful function which are used through out this system.
*/

/*
String-related functions
---------------------------------------------------------
*/

//Generate a random string
	function randomValue($length = 8, $seeds = "alphanum") {
		$seedings["alpha"] = "abcdefghijklmnopqrstuvwqyzABCDEFGHIJKLMNOPQRSTUVWXYZ";
		$seedings["numeric"] = "0123456789";
		$seedings["alphanum"] = "abcdefghijklmnopqrstuvwqyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789";
		$seedings["hexidec"] = "0123456789abcdef";
		
		if (isset($seedings[$seeds])) {
			$seeds = $seedings[$seeds];
		}
		
		list($usec, $sec) = explode(" ", microtime());
		$seed = (float) $sec + ((float) $usec * 100000);
		mt_srand($seed);
		
		$string = "";
		$seeds_count = strlen($seeds);
		
		for ($i = 0; $length > $i; $i++) {
			$string .= $seeds{mt_rand(0, $seeds_count - 1)};
		}
		
		return $string;
	}

//Trim a string to the specified limit
	function commentTrim ($length, $value, $allowedTags = false) {
	   $commentsStrip = preg_replace("/<img[^>]+\>/i", "(image)", $value);
	   $comments = strip_tags($commentsStrip, $allowedTags);
	   $maxLength = $length;
	   $countValue = html_entity_decode($comments);
	   
	   if (strlen($countValue) <= $maxLength) {
		  return prepare($comments);
	   }
	
	   $shortenedValue = substr($countValue, 0, $maxLength - 3) . "...";
	   
	   return $shortenedValue;
	}
	
//Strip characters out of a string
	function strip($string, $type) {
		switch($type) {
			case "lettersOnly" : 
				return preg_replace("/[^a-zA-Z]/", "", $string);
				break;
				
			case "lettersSpace" : 
				return preg_replace("/[^a-zA-Z\s]/", "", $string);
				break;
				
			case "numbersOnly" : 
				return preg_replace("/[^0-9]/", "", $string);
				break;
				
			case "numbersSpace" : 
				return preg_replace("/[^0-9\s]/", "", $string);
				break;
				
			case "lettersNumbers" : 
				return preg_replace("/[^a-zA-Z0-9]/", "", $string);
				break;
				
			case "lettersNumbersSpace" : 
				return preg_replace("/[^a-zA-Z0-9\s]/", "", $string);
				break;
				
			default : 
				if (preg_replace($type, "", $string)) {
					return preg_replace($type, "", $string);
				} else {
					$error = debug_backtrace();
					die(errorMessage("The regular expression was invalid on line " . $error['0']['line']));
				}
		}
	}
	
/*
Array-related functions
---------------------------------------------------------
*/

//Shuffle an array, while preserving the keys
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
	
//Flatten a nested array
	function flatten($array) {
		if (!is_array($array)) {
			return array($array);
		}
	
		$result = array();
		
		foreach ($array as $value) {
			$result = array_merge($result, flatten($value));
		}
		
		return $result;
	}
	
//Detirmine the size of an array, even if several values are left empty
	function size($array) {
		$return = end($array);
		return key($return);
	}
	
//Remove an array value by the element
	function removeElement($array, $element) {
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
	
//Convert an array into a database storable format
	function arrayStore($array) {
		return base64_encode(serialize($array));
	}
	
//Revert an array from a database storable format
	function arrayRevert($array) {
		return unserialize(base64_decode($array));
	}
	
/*
File-related functions
---------------------------------------------------------
*/

//A function to check the extension of a file
	function extension($targetFile) {
		$entension = explode(".", $targetFile);
		$value = count($entension) - 1;
		$entension = $entension[$value];
		$output = strtolower($entension);
		$forbidden = array("php", "php3", "php4", "php5", "tpl", "php-dist", "phtml", "phtm", "htaccess", "htpassword", "asp", "asa", "ashx", "aspx", "ascx", "asmx", "cs", "vb", "config", "master", "shtm", "shtml", "stm", "ssi", "inc", "cfm", "cfml", "cfc", "jsp", "jst", "pl", "cgi");
		
		if(in_array($output, $forbidden)) {
			die(errorMessage("Your file is a potential threat to this system, in which case, it was not uploaded"));
			return false;
			exit;
		} else {
			return $output;
		}
	}
	
//Return the MIME type of a file
	function getMimeType($filename) {
		if (function_exists("mime_content_type")) {
			return mime_content_type($filename);
		} elseif (function_exists("finfo_open")) {
			$fileInfo = finfo_open(FILEINFO_MIME);
            $mimetype = finfo_file($fileInfo, $filename);
            finfo_close($fileInfo);
            return $mimetype;
		} else {
			$mimeTypes = array(
				"ai" => "application/postscript",
				"aif" => "audio/x-aiff",
				"aifc" => "audio/x-aiff",
				"aiff" => "audio/x-aiff",
				"asc" => "text/plain",
				"asf" => "video/x-ms-asf",
				"asx" => "video/x-ms-asf",
				"au" => "audio/basic",
				"avi" => "video/x-msvideo",
				"bcpio" => "application/x-bcpio",
				"bin" => "application/octet-stream",
				"bmp" => "image/bmp",
				"bz2" => "application/x-bzip2",
				"cdf" => "application/x-netcdf",
				"chrt" => "application/x-kchart",
				"class" => "application/octet-stream",
				"cpio" => "application/x-cpio",
				"cpt" => "application/mac-compactpro",
				"csh" => "application/x-csh",
				"css" => "text/css",
				"dcr" => "application/x-director",
				"dir" => "application/x-director",
				"djv" => "image/vnd.djvu",
				"djvu" => "image/vnd.djvu",
				"dll" => "application/octet-stream",
				"dms" => "application/octet-stream",
				"dvi" => "application/x-dvi",
				"dxr" => "application/x-director",
				"eps" => "application/postscript",
				"etx" => "text/x-setext",
				"exe" => "application/octet-stream",
				"ez" => "application/andrew-inset",
				"flv" => "video/x-flv",
				"gif" => "image/gif",
				"gtar" => "application/x-gtar",
				"gz" => "application/x-gzip",
				"hdf" => "application/x-hdf",
				"hqx" => "application/mac-binhex40",
				"htm" => "text/html",
				"html" => "text/html",
				"ice" => "x-conference/x-cooltalk",
				"ief" => "image/ief",
				"iges" => "model/iges",
				"igs" => "model/iges",
				"img" => "application/octet-stream",
				"iso" => "application/octet-stream",
				"jad" => "text/vnd.sun.j2me.app-descriptor",
				"jar" => "application/x-java-archive",
				"jnlp" => "application/x-java-jnlp-file",
				"jpe" => "image/jpeg",
				"jpeg" => "image/jpeg",
				"jpg" => "image/jpeg",
				"js" => "application/x-javascript",
				"kar" => "audio/midi",
				"kil" => "application/x-killustrator",
				"kpr" => "application/x-kpresenter",
				"kpt" => "application/x-kpresenter",
				"ksp" => "application/x-kspread",
				"kwd" => "application/x-kword",
				"kwt" => "application/x-kword",
				"latex" => "application/x-latex",
				"lha" => "application/octet-stream",
				"lzh" => "application/octet-stream",
				"m3u" => "audio/x-mpegurl",
				"man" => "application/x-troff-man",
				"me" => "application/x-troff-me",
				"mesh" => "model/mesh",
				"mid" => "audio/midi",
				"midi" => "audio/midi",
				"mif" => "application/vnd.mif",
				"mov" => "video/quicktime",
				"movie" => "video/x-sgi-movie",
				"mp2" => "audio/mpeg",
				"mp3" => "audio/mpeg",
				"mp4" => "video/mp4",
				"mpe" => "video/mpeg",
				"mpeg" => "video/mpeg",
				"mpg" => "video/mpeg",
				"mpga" => "audio/mpeg",
				"ms" => "application/x-troff-ms",
				"msh" => "model/mesh",
				"mxu" => "video/vnd.mpegurl",
				"nc" => "application/x-netcdf",
				"odb" => "application/vnd.oasis.opendocument.database",
				"odc" => "application/vnd.oasis.opendocument.chart",
				"odf" => "application/vnd.oasis.opendocument.formula",
				"odg" => "application/vnd.oasis.opendocument.graphics",
				"odi" => "application/vnd.oasis.opendocument.image",
				"odm" => "application/vnd.oasis.opendocument.text-master",
				"odp" => "application/vnd.oasis.opendocument.presentation",
				"ods" => "application/vnd.oasis.opendocument.spreadsheet",
				"odt" => "application/vnd.oasis.opendocument.text",
				"ogg" => "application/ogg",
				"otg" => "application/vnd.oasis.opendocument.graphics-template",
				"oth" => "application/vnd.oasis.opendocument.text-web",
				"otp" => "application/vnd.oasis.opendocument.presentation-template",
				"ots" => "application/vnd.oasis.opendocument.spreadsheet-template",
				"ott" => "application/vnd.oasis.opendocument.text-template",
				"pbm" => "image/x-portable-bitmap",
				"pdb" => "chemical/x-pdb",
				"pdf" => "application/pdf",
				"pgm" => "image/x-portable-graymap",
				"pgn" => "application/x-chess-pgn",
				"png" => "image/png",
				"pnm" => "image/x-portable-anymap",
				"ppm" => "image/x-portable-pixmap",
				"ps" => "application/postscript",
				"qt" => "video/quicktime",
				"ra" => "audio/x-realaudio",
				"ram" => "audio/x-pn-realaudio",
				"ras" => "image/x-cmu-raster",
				"rgb" => "image/x-rgb",
				"rm" => "audio/x-pn-realaudio",
				"roff" => "application/x-troff",
				"rpm" => "application/x-rpm",
				"rtf" => "text/rtf",
				"rtx" => "text/richtext",
				"sgm" => "text/sgml",
				"sgml" => "text/sgml",
				"sh" => "application/x-sh",
				"shar" => "application/x-shar",
				"silo" => "model/mesh",
				"sis" => "application/vnd.symbian.install",
				"sit" => "application/x-stuffit",
				"skd" => "application/x-koan",
				"skm" => "application/x-koan",
				"skp" => "application/x-koan",
				"skt" => "application/x-koan",
				"smi" => "application/smil",
				"smil" => "application/smil",
				"snd" => "audio/basic",
				"so" => "application/octet-stream",
				"spl" => "application/x-futuresplash",
				"src" => "application/x-wais-source",
				"stc" => "application/vnd.sun.xml.calc.template",
				"std" => "application/vnd.sun.xml.draw.template",
				"sti" => "application/vnd.sun.xml.impress.template",
				"stw" => "application/vnd.sun.xml.writer.template",
				"sv4cpio" => "application/x-sv4cpio",
				"sv4crc" => "application/x-sv4crc",
				"swf" => "application/x-shockwave-flash",
				"sxc" => "application/vnd.sun.xml.calc",
				"sxd" => "application/vnd.sun.xml.draw",
				"sxg" => "application/vnd.sun.xml.writer.global",
				"sxi" => "application/vnd.sun.xml.impress",
				"sxm" => "application/vnd.sun.xml.math",
				"sxw" => "application/vnd.sun.xml.writer",
				"t" => "application/x-troff",
				"tar" => "application/x-tar",
				"tcl" => "application/x-tcl",
				"tex" => "application/x-tex",
				"texi" => "application/x-texinfo",
				"texinfo" => "application/x-texinfo",
				"tgz" => "application/x-gzip",
				"tif" => "image/tiff",
				"tiff" => "image/tiff",
				"torrent" => "application/x-bittorrent",
				"tr" => "application/x-troff",
				"tsv" => "text/tab-separated-values",
				"txt" => "text/plain",
				"ustar" => "application/x-ustar",
				"vcd" => "application/x-cdlink",
				"vrml" => "model/vrml",
				"wav" => "audio/x-wav",
				"wax" => "audio/x-ms-wax",
				"wbmp" => "image/vnd.wap.wbmp",
				"wbxml" => "application/vnd.wap.wbxml",
				"wm" => "video/x-ms-wm",
				"wma" => "audio/x-ms-wma",
				"wml" => "text/vnd.wap.wml",
				"wmlc" => "application/vnd.wap.wmlc",
				"wmls" => "text/vnd.wap.wmlscript",
				"wmlsc" => "application/vnd.wap.wmlscriptc",
				"wmv" => "video/x-ms-wmv",
				"wmx" => "video/x-ms-wmx",
				"wrl" => "model/vrml",
				"wvx" => "video/x-ms-wvx",
				"xbm" => "image/x-xbitmap",
				"xht" => "application/xhtml+xml",
				"xhtml" => "application/xhtml+xml",
				"xml" => "text/xml",
				"xpm" => "image/x-xpixmap",
				"xsl" => "text/xml",
				"xwd" => "image/x-xwindowdump",
				"xyz" => "chemical/x-xyz",
				"zip" => "application/zip",
				"doc" => "application/msword",
				"dot" => "application/msword",
				"docx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
				"dotx" => "application/vnd.openxmlformats-officedocument.wordprocessingml.template",
				"docm" => "application/vnd.ms-word.document.macroEnabled.12",
				"dotm" => "application/vnd.ms-word.template.macroEnabled.12",
				"xls" => "application/vnd.ms-excel",
				"xlt" => "application/vnd.ms-excel",
				"xla" => "application/vnd.ms-excel",
				"xlsx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet",
				"xltx" => "application/vnd.openxmlformats-officedocument.spreadsheetml.template",
				"xlsm" => "application/vnd.ms-excel.sheet.macroEnabled.12",
				"xltm" => "application/vnd.ms-excel.template.macroEnabled.12",
				"xlam" => "application/vnd.ms-excel.addin.macroEnabled.12",
				"xlsb" => "application/vnd.ms-excel.sheet.binary.macroEnabled.12",
				"ppt" => "application/vnd.ms-powerpoint",
				"pot" => "application/vnd.ms-powerpoint",
				"pps" => "application/vnd.ms-powerpoint",
				"ppa" => "application/vnd.ms-powerpoint",
				"pptx" => "application/vnd.openxmlformats-officedocument.presentationml.presentation",
				"potx" => "application/vnd.openxmlformats-officedocument.presentationml.template",
				"ppsx" => "application/vnd.openxmlformats-officedocument.presentationml.slideshow",
				"ppam" => "application/vnd.ms-powerpoint.addin.macroEnabled.12",
				"pptm" => "application/vnd.ms-powerpoint.presentation.macroEnabled.12",
				"potm" => "application/vnd.ms-powerpoint.presentation.macroEnabled.12",
				"ppsm" => "application/vnd.ms-powerpoint.slideshow.macroEnabled.12"
			);
			
			$extension = strtolower(end(explode(".", $filename)));
			
			if (array_key_exists($extension, $mimeTypes)) {
				return $mimeTypes[$extension];
			} else {
				return "application/octet-stream";
			}
		}
	}
	
/*
Misc. functions
---------------------------------------------------------
*/
	
//Safely mail automated emails
	function autoEmail($to, $subject, $message) {
		$domain = explode(":", $_SERVER['HTTP_HOST']);
		
		if (mail($to, $subject, $message, "From: No Reply<no-reply@" . $domain['0'] . ">")) {
			//Do nothing, the email was sent
		} else {
			die(errorMessage("Error sending automated email."));
		}
	}
	
//Restrict access to super-administrator only parts of the site
	function lockAccess() {
		global $root;
		
		if (!strstr($_SERVER['PHP_SELF'], "login.php")) {
			if (!loggedIn() || !isset($_SESSION['administration'])) {
				redirect($root . "admin/login.php");
			}
		} else {
			if (loggedIn() && isset($_SESSION['administration'])) {
				redirect($root . "admin/index.php");
			}
		}
	}
?>