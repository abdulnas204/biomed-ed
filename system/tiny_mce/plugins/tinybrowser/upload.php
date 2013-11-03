<?php
/**********************************************************************
Developer enhancements are denoted by a //Developer Enhancement comment
**********************************************************************/

require_once('config_tinybrowser.php');
// Set language
if(isset($tinybrowser['language']) && file_exists('langs/'.$tinybrowser['language'].'.php'))
	{
	require_once('langs/'.$tinybrowser['language'].'.php'); 
	}
else
	{
	require_once('langs/en.php'); // Falls back to English
	}
require_once('fns_tinybrowser.php');

if(!$tinybrowser['allowupload'])
	{
	echo TB_UPDENIED;
	exit;
	}

// Assign get variables
$validtypes = array('image','media','file');
$typenow = ((isset($_GET['type']) && in_array($_GET['type'],$validtypes)) ? $_GET['type'] : 'image');
$foldernow = str_replace(array('../','..\\','./','.\\'),'',($tinybrowser['allowfolders'] && isset($_REQUEST['folder']) ? urldecode($_REQUEST['folder']) : ''));
$passfolder = '&folder='.urlencode($foldernow);
$passfeid = (isset($_GET['feid']) && $_GET['feid']!='' ? '&feid='.$_GET['feid'] : '');
$passupfeid = (isset($_GET['feid']) && $_GET['feid']!='' ? $_GET['feid'] : '');

// Assign upload path
$uploadpath = urlencode($tinybrowser['path'][$typenow].$foldernow);

// Assign directory structure to array
$uploaddirs=array();
dirtree($uploaddirs,$tinybrowser['filetype'][$typenow],$tinybrowser['docroot'],$tinybrowser['path'][$typenow]);

// determine file dialog file types
switch ($_GET['type'])
	{
	case 'image':
		$filestr = TB_TYPEIMG;
		break;
	case 'media':
		$filestr = TB_TYPEMEDIA;
		break;
	case 'file':
		$filestr = TB_TYPEFILE;
		break;
	}
$fileexts = str_replace(",",";",$tinybrowser['filetype'][$_GET['type']]);
$filelist = $filestr.' ('.$tinybrowser['filetype'][$_GET['type']].')';

// Initalise alert array
$notify = array(
	'type' => array(),
	'message' => array()
);
$goodqty = (isset($_GET['goodfiles']) ? $_GET['goodfiles'] : 0);
$badqty = (isset($_GET['badfiles']) ? $_GET['badfiles'] : 0);
$dupqty = (isset($_GET['dupfiles']) ? $_GET['dupfiles'] : 0);

if($goodqty>0)
	{
	$notify['type'][]='success';
	$notify['message'][]=sprintf(TB_MSGUPGOOD, $goodqty);
	}
if($badqty>0)
	{
	$notify['type'][]='failure';
	$notify['message'][]=sprintf(TB_MSGUPBAD, $badqty);
	}
if($dupqty>0)
	{
	$notify['type'][]='failure';
	$notify['message'][]=sprintf(TB_MSGUPDUP, $dupqty);
	}
if(isset($_GET['permerror']))
	{
	$notify['type'][]='failure';
	$notify['message'][]=sprintf(TB_MSGUPFAIL, $tinybrowser['docroot'].$tinybrowser['path'][$typenow]);
	}
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
<title>Server Files :: Upload</title>
<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
<meta http-equiv="Pragma" content="no-cache" />
<?php
if($passfeid == '' && $tinybrowser['integration']=='tinymce')
	{
	?><link rel="stylesheet" type="text/css" media="all" href="<?php echo $tinybrowser['tinymcecss']; ?>" /><?php 
	}
else
	{
	?><link rel="stylesheet" type="text/css" media="all" href="css/stylefull_tinybrowser.css" /><?php 
	}
?>
<link rel="stylesheet" type="text/css" media="all" href="css/style_tinybrowser.css.php" />
<script type="text/javascript" src="js/swfobject.js"></script>
<script type="text/javascript">
function uploadComplete(url) {
document.location = url;
}
</script>
</head>
<?php
//Developer Enhancement, check if this is a secure file zone
//Developer Enhancement, modified from config below
//so.addVariable(\"sessid\", \"" . session_id() . "\");
	isSecure("onload='
      var so = new SWFObject(\"flexupload.swf\", \"mymovie\", \"100%\", \"340\", \"9\", \"#ffffff\");
      so.addVariable(\"folder\", \"" . $uploadpath . "\");
      so.addVariable(\"uptype\", \"" . $typenow . "\");
      so.addVariable(\"destid\", \"" . $passupfeid . "\");
      so.addVariable(\"maxsize\", \"" . $tinybrowser['maxsize'][$_GET['type']] . "\");
      so.addVariable(\"sessid\", \"\");
      so.addVariable(\"obfus\", \"" . md5($_SERVER['DOCUMENT_ROOT'].$tinybrowser['obfuscate']) . "\");
      so.addVariable(\"filenames\", \"" . $filelist . "\");
      so.addVariable(\"extensions\", \"" . $fileexts . "\");
      so.addVariable(\"filenamelbl\", \"" . TB_FILENAME . "\");
      so.addVariable(\"sizelbl\", \"" . TB_SIZE . "\");
      so.addVariable(\"typelbl\", \"" . TB_TYPE . "\");
      so.addVariable(\"progresslbl\", \"" . TB_PROGRESS . "\");
      so.addVariable(\"browselbl\", \"" . TB_BROWSE . "\");
      so.addVariable(\"removelbl\", \"" . TB_REMOVE . "\");
      so.addVariable(\"uploadlbl\", \"" . TB_UPLOAD . "\");
      so.addVariable(\"uplimitmsg\", \"" . TB_MSGMAXSIZE . "\");
      so.addVariable(\"uplimitlbl\", \"" . TB_TTLMAXSIZE . "\");
      so.addVariable(\"uplimitbyte\", \"" . TB_BYTES . "\");
      so.addParam(\"allowScriptAccess\", \"always\");
      so.addParam(\"type\", \"application/x-shockwave-flash\");
      so.write(\"flashcontent\");'");
?>
<?php
if(count($notify['type'])>0) alert($notify);
form_open('foldertab',false,'upload.php','?type='.$typenow.$passfeid);
?>
<div class="tabs">
<ul>
<li id="browse_tab"><span><a href="tinybrowser.php?type=<?php echo $typenow.$passfolder.$passfeid ; ?>"><?php echo TB_BROWSE; ?></a></span></li>
<li id="upload_tab" class="current"><span><a href="upload.php?type=<?php echo $typenow.$passfolder.$passfeid ; ?>"><?php echo TB_UPLOAD; ?></a></span></li>
<?php
if($tinybrowser['allowedit'] || $tinybrowser['allowdelete'])
	{
	?><li id="edit_tab"><span><a href="edit.php?type=<?php echo $typenow.$passfolder.$passfeid ; ?>"><?php echo TB_EDIT; ?></a></span></li>
	<?php 
	}
if($tinybrowser['allowfolders'])
	{
	?><li id="folders_tab"><span><a href="folders.php?type=<?php echo $typenow.$passfolder.$passfeid; ?>"><?php echo TB_FOLDERS; ?></a></span></li><?php
	}
// Display folder select, if multiple exist
if(count($uploaddirs)>1)
	{
	?><li id="folder_tab" class="right"><span><?php
	form_select($uploaddirs,'folder',TB_FOLDERCURR,urlencode($foldernow),true);
	?></span></li><?php
	}
?>
</ul>
</div>
</form>
<div class="panel_wrapper">
<div id="general_panel" class="panel currentmod">
<fieldset>
<legend><?php echo TB_UPLOADFILES; ?></legend>
<?php

?>
    <div id="flashcontent"></div>
</fieldset></div></div>
</body>
</html>
