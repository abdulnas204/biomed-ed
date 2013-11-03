<?php
/*
---------------------------------------------------------
(C) Copyright 2010 Apex Development - All Rights Reserved

This script may NOT be used, copied, modified, or
distributed in any way shape or form under any license:
open source, freeware, nor commercial/closed source.
---------------------------------------------------------
 
Created by: Oliver Spryn
Created on: December 23rd, 2010
Last updated: Janurary 3rd, 2011

This is the configuration script for the session control 
extension.
*/

//Header functions
	require_once("../core/index.php");
	
//Output this as a JavaScript file
	header("Content-type: text/javascript");
?>
//######
//## This work is licensed under the Creative Commons Attribution-Share Alike 3.0 
//## United States License. To view a copy of this license, 
//## visit http://creativecommons.org/licenses/by-sa/3.0/us/ or send a letter 
//## to Creative Commons, 171 Second Street, Suite 300, San Francisco, California, 94105, USA.
//######

var returnURL = window.location.href.replace("<?php echo rtrim($root, "/"); ?>", "");
var redirectTo = <?php echo "'" . $root . "users/login.htm?return='";?> + escape(returnURL);

function countDown() {
    var element = document.getElementById('countDown').innerHTML;
    var content = parseFloat(element) - 1;
    
    if (element > 0) {
    	document.getElementById('countDown').innerHTML = content;
    }
}

(function($){
    $.fn.idleTimeout = function(options) {
    	var defaults = {
            inactivity: 1200000, //20 Minutes
            noconfirm: 60000, //60 Seconds
            sessionAlive: 600000, //10 Minutes
            redirect_url: redirectTo,
            click_reset: true,
            alive_url: document.location.href,
            logout_url: redirectTo
    	}
    	
    	var opts = $.extend(defaults, options);
    	var liveTimeout, confTimeout, sessionTimeout;
    	var warning = "<div id='modal_pop'><p>You will be logged out in <span id='countDown'>10</span> seconds due to inactivity.<br /><br />Click &quot;Stay Logged In&quot; to continue your session.</p></div>";
        var login = "<div id='modal_pop'><p>You have been logged out due to inactivity. All entered information is still in queue.</p><p>User name<span class='require'>*</span>: </p><blockquote><p><input type='text' name='userName' id='userName' size='50' autocomplete='off' spellcheck='true' class='validate[required]' /></p></blockquote><p>Password<span class='require'>*</span>: </p><blockquote><p><input type='text' name='passWord' id='passWord' size='50' autocomplete='off' spellcheck='true' class='validate[required]' /></p></blockquote>";

    
    	var start_liveTimeout = function() {
			clearTimeout(liveTimeout);
			clearTimeout(confTimeout);
			liveTimeout = setTimeout(logout, opts.inactivity);
			
			if(opts.sessionAlive) {
				clearTimeout(sessionTimeout);
				sessionTimeout = setTimeout(keep_session, opts.sessionAlive);
			}
		}
    
    	var logout = function() {
			confTimeout = setTimeout(loginPrompt, opts.noconfirm);
            
			$(warning).dialog({
				buttons: {
                	"Stay Logged In":  function(){
                    	$(this).dialog('close');
                    	stay_logged_in();
					},
                    
                    "Logout":  function(){
                    	$(this).dialog('close');
                    	window.location.href = '<?php echo $root; ?>users/logout.htm';
					}
				},
                
                width: 480,
                height: 280,
                modal: true,
                resizable: false,
                draggable: false,
                title: 'Login Expiration'
            });
            
            var interval = self.setInterval("countDown()", 1000);
		}
    
        var loginPrompt = function() {
            $("#modal_pop").dialog('close');
            
            $(login).dialog({
				buttons: {
                	"Login":  function(){
                    	process();
					},
                    
                    "Logout":  function(){
                    	$(this).dialog('close');
                    	window.location.href = '<?php echo $root; ?>users/logout.htm';
					}
				},
                
                width: 640,
                height: 400,
                modal: true,
                resizable: false,
                draggable: false,
                title: 'Login'
            });
        }
        
        var process = function() {
            var userName = $("input#userName").val();
            var passWord = $("input#userPassword").val();
            var dataString = 'userName=' + userName + '&passWord=' + passWord;  
            
            $.ajax({
                type: "POST",
                url: "<?php echo $root . "users/login.htm";?>",
                data: dataString,
                success: function() {
                   alert("whoo!");
                }
            });
        }
        
        var stay_logged_in = function(el) {
            start_liveTimeout();
            
            if(opts.alive_url) {
                $.get(opts.alive_url);
            }
        }
        
        var keep_session = function() {
            $.get(opts.alive_url);
            clearTimeout(sessionTimeout);
            sessionTimeout = setTimeout(keep_session, opts.sessionAlive);
        } 
        
        return this.each(function() {
            obj = $(this);
            start_liveTimeout();
            
            if(opts.click_reset) {
                $(document).bind('click', start_liveTimeout);
            }
            
            if(opts.sessionAlive) {
                keep_session();
            }
		});
	};
})(jQuery);

$(document).ready(function(){
	$(document).idleTimeout({
		inactivity: 1200000, //20 Minutes
        noconfirm: 60000, //60 Seconds
        sessionAlive: 600000 //10 Minutes
	});
});
