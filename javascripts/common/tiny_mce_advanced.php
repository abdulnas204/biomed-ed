<?php require_once("../../Connections/connDBA.php"); ?>
<?php
	header("Content-type: text/javascript");
?>
tinyMCE.init({
		mode : "textareas",
		theme : "advanced",
		skin : "o2k7",
		skin_variant : "silver",
		plugins : "safari,pagebreak,style,layer,table,save,advhr,advimage,autosave,advlink,emotions,iespell,inlinepopups,insertdatetime,preview,media,searchreplace,print,contextmenu,paste,directionality,fullscreen,noneditable,visualchars,nonbreaking,xhtmlxtras,template,wordcount,spellchecker,tabfocus",
		spellchecker_languages : "+English=en",
		tab_focus : ':prev,:next',

		theme_advanced_buttons1 : "save,newdocument,|,bold,italic,underline,strikethrough,|,justifyleft,justifycenter,justifyright,justifyfull,styleselect,formatselect,fontselect,fontsizeselect",
		theme_advanced_buttons2 : "cut,copy,paste,pastetext,pasteword,|,search,replace,|,bullist,numlist,|,outdent,indent,blockquote,|,undo,redo,|,link,unlink,anchor,image,cleanup,help,code,|,insertdate,inserttime,preview,|,forecolor,backcolor",
		theme_advanced_buttons3 : "tablecontrols,|,hr,removeformat,visualaid,|,sub,sup,|,charmap,emotions,iespell,media,advhr,|,print,|,ltr,rtl,|,fullscreen,|,spellchecker",
		theme_advanced_buttons4 : "insertlayer,moveforward,movebackward,absolute,|,styleprops,|,cite,abbr,acronym,del,ins,attribs,|,visualchars,nonbreaking,template,pagebreak,restoredraft",
		theme_advanced_toolbar_location : "top",
		theme_advanced_toolbar_align : "left",
		theme_advanced_statusbar_location : "bottom",
		theme_advanced_resizing : true,
        relative_urls : false,
        document_base_url : "<?php echo $root; ?>",
        remove_script_host : false,
        convert_urls : false,
		content_css : "<?php echo $root; ?>styles/common/universal.css",
		file_browser_callback: "tinyBrowser",
		width : "640",
		height: "320",

		external_link_list_url : "<?php echo $root; ?>tiny_mce/plugins/advlink/data_base_links.php",
		external_image_list_url : "lists/image_list.js",
		media_external_list_url : "lists/media_list.js",
		autosave_ask_before_unload : false,
        editor_deselector : "noEditorAdvanced",
});
