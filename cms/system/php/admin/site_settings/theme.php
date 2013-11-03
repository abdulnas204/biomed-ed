<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Untitled Document</title>
<script type="text/javascript">
function MM_goToURL() { //v3.0
  var i, args=MM_goToURL.arguments; document.MM_returnValue = false;
  for (i=0; i<(args.length-1); i+=2) eval(args[i]+".location='"+args[i+1]+"'");
}
</script>
</head>

<body>
<p>This page will  modify the site colors, text styles, and splash image for this site.</p>
<p>&nbsp;</p>
<div class="layoutControl">
  <div class="dataLeft">
    <div class="block_course_list sideblock">
      <div class="header">
        <div class="title">
          <h2>Navigation</h2>
        </div>
      </div>
      <div class="content">
        <p>Modify other settings within this site:</p>
        <ul>
          <li class="homeBullet"><a href="index.php">Back to Home</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=logo">Site Logo</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=icon">Browser Icon</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=meta">Site Information</a></li>
          <li class="arrowBullet"><a href="site_settings.php?type=theme">Theme</a></li>
        </ul>
      </div>
    </div>
  </div>
  <div class="contentRight">
    <form action="site_settings.php?type=theme" method="post">
      <div class="catDivider alignLeft">Select a Theme</div>
      <div class="stepContent">
        <blockquote>
          <p><strong>American</strong></p>
          <?php if ($theme['style'] == "american.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
          <p><img src="../../images/themes/american/preview.jpg" alt="American Theme Preview" width="252" height="124" />
            <input type="button" name="chooseAmerican" id="chooseAmerican" value="Choose American Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&amp;action=modifyTheme&amp;theme=american.css');return document.MM_returnValue" />
          </p>
          <p><strong>Binary</strong></p>
          <?php if ($theme['style'] == "binary.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
          <p><img src="../../images/themes/binary/preivew.jpg" alt="Binary Theme Preview" width="251" height="119" />
            <input type="button" name="chooseBinary" id="chooseBinary" value="Choose Binary Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&amp;action=modifyTheme&amp;theme=binary.css');return document.MM_returnValue" />
          </p>
          <p><strong>Business</strong></p>
          <?php if ($theme['style'] == "business.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
          <p><img src="../../images/themes/business/preview.jpg" alt="Business Theme Preview" width="251" height="125" />
            <input type="button" name="chooseBusiness" id="chooseBusiness" value="Choose Business Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&amp;action=modifyTheme&amp;theme=business.css');return document.MM_returnValue" />
          </p>
          <p><strong>Digital University</strong></p>
          <?php if ($theme['style'] == "digitalUniversity.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
          <p><img src="../../images/themes/digital_university/preview.jpg" alt="Digitial University Theme Preview" width="252" height="111" />
            <input type="button" name="chooseDigital" id="chooseDigital" value="Choose Digital University Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&amp;action=modifyTheme&amp;theme=digitalUniversity.css');return document.MM_returnValue" />
          </p>
          <p><strong>e-Learning</strong></p>
          <?php if ($theme['style'] == "eLearning.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
          <p><img src="../../images/themes/e_learning/preview.jpg" alt="e-Learning Theme Preview" width="252" height="111" />
            <input type="button" name="chooseLearning" id="chooseLearning" value="Choose e-Learning Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&amp;action=modifyTheme&amp;theme=eLearning.css');return document.MM_returnValue" />
          </p>
          <p><strong>Knowledge Library</strong></p>
          <?php if ($theme['style'] == "knowledgeLibrary.css") { echo "<div class=\"selectedTheme\">This is the current theme</div>";} ?>
          <p><img src="../../images/themes/knowledge_library/preview.jpg" alt="Knowledge Library Theme Preview" width="252" height="111" />
            <input type="button" name="chooseLibrary" id="chooseLibrary" value="Choose Knowledge Library Theme" onclick="MM_goToURL('parent','site_settings.php?type=theme&amp;action=modifyTheme&amp;theme=knowledgeLibrary.css');return document.MM_returnValue" />
          </p>
          <p>&nbsp;</p>
        </blockquote>
      </div>
    </form>
  </div>
</div>
<?php 
//If the site meta page is requested
	} elseif (isset ($meta)) { 
?>
<?php 
	if (isset ($_POST['modifyMeta']) || isset ($_POST['modifyMeta2']) && !empty($_POST['name'])) {
		if (!empty($_POST['name'])) {
			$name = mysql_real_escape_string($_POST['name']);
			$footer = mysql_real_escape_string($_POST['footer']);
			$author = mysql_real_escape_string($_POST['author']);
			$language = mysql_real_escape_string($_POST['language']);
			$timeZone = mysql_real_escape_string($_POST['timeZone']);
			$copyright = mysql_real_escape_string($_POST['copyright']);
			$description = mysql_real_escape_string($_POST['description']);
			$meta = mysql_real_escape_string($_POST['meta']);
			
			$modifyMetaQuery = "UPDATE siteprofiles SET siteName = '{$name}', siteFooter = '{$footer}', author = '{$author}', language = '{$language}', copyright = '{$copyright}', description = '{$description}', meta = '{$meta}', timeZone = '{$timeZone}'";
			$modifyMetaQueryResult = mysql_query($modifyMetaQuery, $connDBA);
			header ("Location: index.php?updated=siteInfo");
			exit;
		}
	}
	
	$themeGrabber = mysql_query("SELECT * FROM siteprofiles", $connDBA);
	$theme = mysql_fetch_array($themeGrabber);
?>
</body>
</html>