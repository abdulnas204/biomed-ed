<?php
//Header functions
	require_once('system/connections/connDBA.php');
	headers("Test", false, "collapsiblePanel");
?>

<div id="CollapsiblePanel2" class="CollapsiblePanel">
  <div class="CollapsiblePanelTab">Collapsible Panel with options set </div>
  <div class="CollapsiblePanelContent">

    <p>Options have been set in this example.</p>
    <p>Default state has been set to closed. It will be closed on load.<br />
      Constructor Option: 
      'contentIsOpen:false'</p>
    <p>Animation has been turned off, so panel will snap open and shut. <br />
      Constructor Option: 
      'enableAnimation:false'</p>
  </div>

</div>
<script type="text/javascript">
<!--
var CollapsiblePanel2 = new Spry.Widget.CollapsiblePanel("CollapsiblePanel2", {contentIsOpen:false});
//-->
</script>

<?php
//Include the footer
	footer();
?>