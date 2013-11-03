<?php
/*
LICENSE: See "license.php" located at the root installation

This is the configuration script for the session control extension.
*/

//Header functions
	require_once("../server/index.php");
	
//Output this as a JavaScript file
	header("Content-type: text/javascript");
?>
/*
LICENSE: See "license.php" located at the root installation
*/

<?php
	if (!loggedIn()) {
?>
$(document).ready(function() {
    $('a#login').click(function() {
        $('<div id="modal" title="Login Prompt"></div>')
        .html('<p>Please login to access your account.</p><span id="loginAttempt"></span><p>Username<span class="require">*</span>: </p><blockquote><p><input type="text" name="userNameModal" id="userNameModal" size="50" autocomplete="off" spellcheck="true" class="validate[required]" /></p></blockquote><p>Password<span class="require">*</span>: </p><blockquote><p><input type="password" name="passWordModal" id="passWordModal" size="50" autocomplete="off" spellcheck="true" class="validate[required]" /></p></blockquote>')
         .dialog({
            height : 400,
            width : 640,
            modal : true,
            resizable : false,
            draggable : false,
            closeOnEscape: false,
            open : function() {
                $(".ui-dialog-titlebar-close").hide();
            },
            buttons : {
            	'Login' : function() {
                	var userName = $('#userNameModal');
                    var passWord = $('#passWordModal');
                    var messageBox = $('#loginAttempt');
                    var modal = $('#modal');
                	
                	if (userName.val() == "" || passWord.val() == "") {
                    	 messageBox.addClass('require').text('A username and password are required.');
                         modal.dialog({height : 440});
                    } else {
                        $.ajax({
                            type : 'POST',
                            url : '<?php echo $root; ?>users/modal_login.php?type=modal',
                            data : {
                                'userName' : userName.val(),
                                'passWord' : passWord.val()
                            },
                            success : function(data) {
                                if (data == "success") {
                                    messageBox.empty();
                                    modal.dialog('close');
                                    window.location.href = '<?php echo $root; ?>portal/index.htm';
                                } else {
                                    messageBox.addClass('require').text('Your username and/or password was incorrect.');
                                    userName.val('');
                                    passWord.val('');
                                    modal.dialog({height : 440});
                                }
                            }
                        });
                    }
                 },
                 'Forgot Password' : function() {
                 	$('#modal').dialog('close');
                    window.location.href = '<?php echo $root; ?>users/forgot_password.htm';
                 },
                 'Register' : function() {
                 	$('#modal').dialog('close');
                 	window.location.href = '<?php echo $root; ?>users/register.htm';
                 },
                 'Cancel' : function() {
                    $('#modal').dialog('close');
                 }
            }
         });
    });
});
<?php
		exit;
	}
?>
// Begin configuration
var logoutCountdown = 1200000-300000; // Number of milliseconds before showing the logout countdown
var reloginMinutes = 5; // Number of minutes remaining before logged out (e.g.: 5 will give 5 minutes and 0 seconds)
var reloginSeconds = 0; // Number of seconds remaining before logged out (e.g.: 30 will give 0 minutes and 30 seconds)
// End configuration

var timer;

function relogin() {
    timer = setInterval("timeOut()", 1000);
    
    if (reloginMinutes > 1) {
        var minuteText = "minutes";
    } else if (reloginMinutes == 1) {
        var minuteText = "minute";
    } else {
        var minuteText = "";
    }
    
    if (reloginSeconds > 1) {
        var secondText = "seconds";
    } else if (reloginSeconds == 1) {
        var secondText = "second";
    } else {
        var secondText = "";
    }
    
    if (reloginMinutes > 1 && reloginSeconds > 1) {
        var andText = "and";
    } else {
        var andText = "";
    }
    
    if (reloginMinutes == 0) {
        reloginMinutes = '';
    }
    
    if (reloginSeconds == 0) {
        reloginSeconds = '';
    }
    
    $('<div id="relogin" title="Session Expiration Warning"></div>')
        .html('<p>You have been inactive for about ' + Math.ceil((logoutCountdown)/60000) + ' minutes, and will be automatically logged out in <strong><span id="minutes">' + reloginMinutes + '</span> <span id="minutesText">' + minuteText + '</span> <span id="and">' + andText + '</span> <span id="seconds">' + reloginSeconds + '</span> <span id="secondsText">' + secondText + '</span></strong> for security reasons.</p><p>Click &quot;Stay Logged In&quot; to keep your session active.</p>')
        .dialog({
            height : 300,
            width : 550,
            modal : true,
            resizable : false,
            draggable : false,
            closeOnEscape: false,
            open : function() {
                $(".ui-dialog-titlebar-close").hide();
            },
            buttons : {
                'Stay Logged In' : function() {
                    $.ajax({
                        type : 'GET',
                        url : '<?php echo $root; ?>users/modal_login.php?type=modal',
                        data : {'type' : 'modal'},
                        success : function() {
                            clearInterval(timer);
                            setTimeout("relogin()", logoutCountdown);
                            $('#relogin').dialog('close').remove();
                        }
                    });
                },
                'Logout' : function() {
                   $('#relogin').dialog('close');
                   window.location.href = '<?php echo $root; ?>users/logout.htm';
                }
            }
        });  
}

function timeOut() {
    var minutes = $('#minutes');
    var minutesValue = minutes.text();
    var minutesText = $('#minutesText');
    var and = $('#and');
    var seconds = $('#seconds');
    var secondsValue = seconds.text();
    var secondsText = $('#secondsText');
    
    if (secondsValue > 2) {
    	seconds.text(secondsValue-1);
    } else if (secondsValue == 2) {
    	seconds.text(secondsValue-1);
        secondsText.text('second');
    } else if (secondsValue == 1) {
        and.text('');
        seconds.text('');
        secondsText.text('');
    } else {    
        if (minutesValue-1 == 1) {
        	minutes.text('1');
            minutesText.text('minute');
        } else if (minutesValue-1 > 1) {
             minutesText.text('minutes');
        } else {
            minutes.text('');
            minutesText.text('');
            and.text('');
        }
        
        if ((secondsValue == 0 || secondsValue == "") && minutesValue !== "") {
        	if (minutesValue > 1) {
                minutes.text(minutesValue-1);
                and.text('and');
            }
            
            seconds.text('59');
            secondsText.text('seconds');
        } else if ((secondsValue == 0 || secondsValue == "") && minutesValue == "") {
            $('#relogin').dialog('close');
            clearInterval(timer);
            modal();
        } else {
            seconds.text(secondsValue-1);
        }
    }
}

function modal() {
    $('<div id="modal" title="Login Prompt"></div>')
        .html('<p>You have been logged out due to inactivity. All entered information is still in queue.</p><span id="loginAttempt"></span><p>Username<span class="require">*</span>: </p><blockquote><p><input type="text" name="userName" id="userName" size="50" autocomplete="off" spellcheck="true" class="validate[required]" /></p></blockquote><p>Password<span class="require">*</span>: </p><blockquote><p><input type="password" name="passWord" id="passWord" size="50" autocomplete="off" spellcheck="true" class="validate[required]" /></p></blockquote>')
        .dialog({
            height : 400,
            width : 640,
            modal : true,
            resizable : false,
            draggable : false,
            closeOnEscape: false,
            open : function() {
                $(".ui-dialog-titlebar-close").hide();
            },
            buttons : {
                'Login' : function() {
                	var userName = $('#userName');
                    var passWord = $('#passWord');
                    var messageBox = $('#loginAttempt');
                    var modal = $('#modal');
                	
                	if (userName.val() == "" || passWord.val() == "") {
                    	 messageBox.addClass('require').text('A username and password are required.');
                         modal.dialog({height : 440});
                    } else {
                        $.ajax({
                            type : 'POST',
                            url : '<?php echo $root; ?>users/modal_login.php?type=modal',
                            data : {
                                'userName' : userName.val(),
                                'passWord' : passWord.val()
                            },
                            success : function(data) {
                                if (data == "success") {
                                    messageBox.empty();
                                    modal.dialog('close');
                                    setTimeout("relogin()", logoutCountdown);
                                } else {
                                    messageBox.addClass('require').text('Your username and/or password was incorrect.');
                                    userName.val('');
                                    passWord.val('');
                                    modal.dialog({height : 440});
                                }
                            }
                        });
                    }
                 },
                 'Logout' : function() {
                    $('#modal').dialog('close');
                    window.location.href = '<?php echo $root; ?>users/logout.htm';
                 }
            }
        });
        
    $('.ui-widget-overlay').fadeTo(3000, 1);
}

setTimeout("relogin()", logoutCountdown);

//Confirm before logging out
$(document).ready(function() {
    $('a#logout').click(function() {
        $('<div id="logoutConfirm" title="Confirm Logout"></div>').html('Are you sure you wish to logout?').dialog({
            height : 175,
            width : 300,
            modal : true,
            resizable : false,
            draggable : false,
            closeOnEscape: false,
            open : function() {
                $(".ui-dialog-titlebar-close").hide();
            },
            buttons : {
            	'Yes' : function() {
                	window.location.href = '<?php echo $root; ?>users/logout.htm';
                },
                'No' : function() {
                	$(this).dialog('close');
                	return false;
                }
            }
        });
    });
});