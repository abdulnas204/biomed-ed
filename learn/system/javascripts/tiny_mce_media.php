<?php
/*
LICENSE: See "license.php" located at the root installation

This is the setup script for the TinyMCE Media widget.
*/
	
//Header functions
	require_once("../../../system/server/index.php");

//Select the API key for the spell checker
	$api = query("SELECT * FROM `siteprofiles` WHERE `id` = '1'");

//Output this as a JavaScript file
	header("Content-type: text/javascript");
?>
tinyMCE.init({
    // General options
    mode : "textareas",
    theme : "advanced",
    skin : "o2k7",
    skin_variant : "silver",
    plugins : "inlinepopups,tabfocus,AtD,advimage,media",
    
    atd_button_url : "<?php echo $root; ?>system/tiny_mce/plugins/AtD/atdbuttontr.gif",
    atd_rpc_url : "<?php echo $root; ?>system/tiny_mce/plugins/AtD/server/proxy.php?url=",
    atd_rpc_id : "<?php echo $api['spellCheckerAPI']; ?>",
    atd_css_url : "<?php echo $root; ?>system/tiny_mce/plugins/AtD/css/content.css",
    atd_show_types : "Bias Language,Cliches,Complex Expression,Diacritical Marks,Double Negatives,Hidden Verbs,Jargon Language,Passive voice,Phrases to Avoid,Redundant Expression",
    atd_ignore_strings : "AtD,rsmudge",
    theme_advanced_buttons1_add : "AtD",
    atd_ignore_enable : "true",
    tab_focus : ':prev,:next',

    theme_advanced_buttons1 : "bold,italic,underline,strikethrough,forecolor,backcolor,|,image,media,|,justifyleft,justifycenter,justifyright, justifyfull,|,bullist,numlist,|,undo,redo,link,unlink",
    theme_advanced_buttons2 : "",
    theme_advanced_buttons3 : "",
    theme_advanced_toolbar_location : "top",
    theme_advanced_toolbar_align : "left",
    theme_advanced_statusbar_location : "bottom",
    theme_advanced_resizing : true,
    relative_urls : false,
    document_base_url : "<?php echo $root; ?>",
    remove_script_host : false,
    convert_urls : false,
    content_css : "<?php echo $root; ?>system/styles/common/universal.css",
    file_browser_callback: "filebrowser",

    external_link_list_url : "<?php echo $root; ?>system/tiny_mce/plugins/advlink/data_base_links.php",
    autosave_ask_before_unload : false,
    editor_deselector : "noEditorMedia",
    gecko_spellcheck : false
});

function filebrowser(field_name, url, type, win) {
    fileBrowserURL = "<?php echo $root; ?>system/tiny_mce/plugins/filebrowser/index.php?filter=" + type;
    
    tinyMCE.activeEditor.windowManager.open({
        file : fileBrowserURL,
        title : 'Server Files',
        width : 800, 
        height : 500,
        resizable : "no",
        scrollbars : "yes",
        inline : "yes",  // This parameter only has an effect if you use the inlinepopups plugin!
        close_previous : "no"
    }, {
        window : win,
        input : field_name
    });
 }