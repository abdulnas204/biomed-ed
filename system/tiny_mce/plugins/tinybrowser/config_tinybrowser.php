<?php
/**********************************************************************
Developer enhancements are denoted by a //Developer Enhancement comment
**********************************************************************/
die("depreciated");

/*
TinyBrowser 1.41 - A TinyMCE file browser (C) 2008  Bryn Jones
(author website - http://www.lunarvis.com)

    This program is free software: you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation, either version 3 of the License, or
    (at your option) any later version.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

// switch off error handling, to use custom handler
error_reporting(0); 

// set script time out higher, to help with thumbnail generation
set_time_limit(240);

$tinybrowser = array();

//Developer Enhancement, to disallow unwanted access
	session_start();
	
	if (isset($_SESSION['MM_Username']) && !empty($_SESSION['MM_Username']) && isset($_SESSION['MM_UserGroup']) && $_SESSION['MM_UserGroup'] === "Site Administrator") {
		//Do nothing, access is granted
	} else {
		die("You do not have access to this content");
	}
	
//Developer Enhancement, to detirmine the folder root
	$strippedRootExplode = explode("/", $_SERVER['REQUEST_URI']);
	$strippedRootCount = count($strippedRootExplode);
	$strippedRoot = "";
	
	foreach ($strippedRootExplode as $appendString) {
		if ($appendString != "tiny_mce") {
			$strippedRoot .= $appendString . "/";
		} else {
			break;
		}
	}
	
	if (isset($_SESSION['currentModule'])) {
		$secureRoot = str_replace("system/", "", $strippedRoot) . "modules/" . $_SESSION['currentModule'] . "/lesson/browser/";
		$imagePath = $secureRoot . "images/";
		$mediaPath = $secureRoot . "media/";
		$filePath = $secureRoot . "other/";
		$secure = true;
	} else {		
		$imagePath = $strippedRoot . 'files/images/';
		$mediaPath = $strippedRoot . 'files/media/';
		$filePath = $strippedRoot . 'files/other/';
		$secure = false;
	}
	
// Random string used to secure Flash upload if session control not enabled - be sure to change!
$tinybrowser['obfuscate'] = 'HJf8denj9dIUdhd';

// Set default language (ISO 639-1 code)
$tinybrowser['language'] = 'en';

// Set the integration type (TinyMCE is default)
$tinybrowser['integration'] = 'tinymce'; // Possible values: 'tinymce', 'fckeditor'

// Default is rtrim($_SERVER['DOCUMENT_ROOT'],'/') (suitable when using absolute paths, but can be set to '' if using relative paths)
$tinybrowser['docroot'] = rtrim($_SERVER['DOCUMENT_ROOT'],'/');

// Folder permissions for Unix servers only
$tinybrowser['unixpermissions'] = 0777;

// File upload paths (set to absolute by default)
$tinybrowser['path']['image'] = $imagePath; // Image files location - also creates a '_thumbs' subdirectory within this path to hold the image thumbnails
$tinybrowser['path']['media'] = $mediaPath; // Media files location
$tinybrowser['path']['file']  = $filePath; // Other files location

//Developer Enhancement, to pass all images through the gateway
	if ($secure == true) {
		$secureRoot = str_replace("system/", "", $strippedRoot) . "gateway.php/modules/" . $_SESSION['currentModule'] . "/lesson/browser/";
		$imageLink = $secureRoot . "images/";
		$mediaLink = $secureRoot . "media/";
		$fileLink = $secureRoot . "other/";
	} else {
		$imageLink = $tinybrowser['path']['image'];
		$mediaLink = $tinybrowser['path']['media'];
		$fileLink = $tinybrowser['path']['file'];
	}

// File link paths - these are the paths that get passed back to TinyMCE or your application (set to equal the upload path by default)
$tinybrowser['link']['image'] = $imageLink; // Image links
$tinybrowser['link']['media'] = $mediaLink; // Media links
$tinybrowser['link']['file']  = $fileLink; // Other file links

//Developer Enhancement, to detirmine the system upload_max_filesize
	$uploadLimit = sprintf(ereg_replace("[^0-9]", "", ini_get('upload_max_filesize')) * 1024 * 1024);

// File upload size limit (0 is unlimited)
$tinybrowser['maxsize']['image'] = $uploadLimit; // Image file maximum size
$tinybrowser['maxsize']['media'] = $uploadLimit; // Media file maximum size
$tinybrowser['maxsize']['file']  = $uploadLimit; // Other file maximum size

// Image automatic resize on upload (0 is no resize)
$tinybrowser['imageresize']['width']  = 0;
$tinybrowser['imageresize']['height'] = 0;

// Image thumbnail source (set to 'path' by default - shouldn't need changing)
$tinybrowser['thumbsrc'] = 'path'; // Possible values: path, link

// Image thumbnail size in pixels
$tinybrowser['thumbsize'] = 80;

// Image and thumbnail quality, higher is better (1 to 99)
$tinybrowser['imagequality'] = 80; // only used when resizing or rotating
$tinybrowser['thumbquality'] = 80;

// Date format, as per php date function
$tinybrowser['dateformat'] = 'd/m/Y H:i';

// Permitted file extensions
$tinybrowser['filetype']['image'] = '*.jpg, *.jpeg, *.gif, *.png, *.bmp'; // Image file types
$tinybrowser['filetype']['media'] = '*.swf, *.dcr, *.mov, *.qt, *.mpg, *.mp3, *.mp4, *.mpeg, *.avi, *.wmv, *.wm, *.asf, *.asx, *.wmx, *.wvx, *.rm, *.ra, *.ram'; // Media file types
$tinybrowser['filetype']['file']  = '*.*'; // Other file types

// Prohibited file extensions
$tinybrowser['prohibited'] = array('php','php3','php4','php5','phtml','asp','aspx','ascx','jsp','cfm','cfc','pl','bat','exe','dll','reg','cgi', 'sh', 'py','asa','asax','config','com','inc');

// Default file sort
$tinybrowser['order']['by']   = 'name'; // Possible values: name, size, type, modified
$tinybrowser['order']['type'] = 'asc'; // Possible values: asc, desc

// Default image view method
$tinybrowser['view']['image'] = 'thumb'; // Possible values: thumb, detail

// File Pagination - split results into pages (0 is none)
$tinybrowser['pagination'] = 0;

// TinyMCE dialog.css file location, relative to tinybrowser.php (can be set to absolute link)
$tinybrowser['tinymcecss'] = 'css/tinymce_style.css';

// TinyBrowser pop-up window size
$tinybrowser['window']['width']  = 770;
$tinybrowser['window']['height'] = 480;

// Assign Permissions for Upload, Edit, Delete & Folders
$tinybrowser['allowupload']  = true;
$tinybrowser['allowedit']    = true;
$tinybrowser['allowdelete']  = true;
$tinybrowser['allowfolders'] = true;

// Clean filenames on upload
$tinybrowser['cleanfilename'] = true;

// Set default action for edit page
$tinybrowser['defaultaction'] = 'delete'; // Possible values: delete, rename, move

// Set delay for file process script, only required if server response is slow
$tinybrowser['delayprocess'] = 0; // Value in seconds
?>
