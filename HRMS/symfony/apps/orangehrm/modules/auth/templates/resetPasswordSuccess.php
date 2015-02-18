<?php
$imagePath = theme_path("images/login");
?>
<?php use_javascripts_for_form($form) ?>
<?php use_stylesheets_for_form($form) ?>
<style type="text/css">

	.main{
		margin-top: 75px;
		margin-right: auto;
		margin-left: auto;
		width: 35%;
	}
	.footer{
		margin-top: 20px;
	}


	
	#panelHeading{
	    padding-top:15px;
		text-align:center;
        font-family:sans-serif;
        font-size: 20px;
        color: #544B3C;
        font-weight: bold;
    }
    
table { border-spacing: 5px; }

th, td { padding: 5px; }
    
    #divmain {
    	cursor:pointer;
        width: 94px;
        height: 26px;
        border: none;
        color:#FFFFFF;
        font-weight: bold;
        font-size: 13px;
    }
    
    .message{
    	margin-top : 50px;
    	background-color: #EAC530;
    	height: 20px;
    	width: 60%;
    	margin-left: auto;
    	margin-right: auto;
    	font-size: 15px;
    	color : red;
    	padding-top: 15px;
    	border:2px solid;
    	border-color: red;
    	text-align: center;
    }
    
    .sentmessage{
    	margin-top : 50px;
    	background-color: #EAC530;
    	height: 30px;
    	width: 60%;
    	margin-left: auto;
    	margin-right: auto;
    	font-size: 15px;
    	color : green;
    	padding-top: 15px;
    	border:2px solid;
    	border-color: green;
    	text-align: center;
    }
    
    #footer { vertical-align:bottom; margin-left:auto; margin-right:auto; margin-top: 50px;}
</style>

 <center>
 <img src = "<?php echo "{$imagePath}/syn-logo-withTagLine.png"; ?>" width="339" height="66"/>
 </center>
<div class="main" id="divResetPwd">
<div class="box">
<div class="head"><h1>Reset Password</h1></div>
<div class="inner">
<div id="divResetPwd">
<form name = "frmResetPwd" id="frmResetPwd" method="post">
	<center>
 		<table>
 			<tr><td>Enter New Password</td><td> <?php echo $form["password"]->render(); ?></td></tr>
  			<tr><td>Confirm Password</td><td><?php echo $form["repassword"]->render(); ?></td></tr>
		</table>
		<br><br><input type="submit" name="Submit" class = "button" id="btnSave" value="<?php echo __('Reset Password'); ?>" />
	</center>
 </form>
 </div>
 </div>
 </div>
 </div>
 
 <div id="message" class = "message">
	This URL link might be expired or broken !  <a href = "<?php echo url_for('auth/forgotPassword'); ?>">Try again</a>
 </div>	
 
 <div id = "sentMessage" class = "sentmessage">
 	Your Password has been changed successfully! <a href="<?php echo url_for('auth/login'); ?>" >Login </a>
 </div>

 <div class="footer">
 	<?php include_partial('core/footer'); ?>
 </div>
 
 
 
 <script type="text/javascript">
    //<![CDATA[
    var lang_required = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var linkForPasswordReset = "<?php echo url_for('auth/resetPassword')?>";
    var lang_Password = "<?php echo __(ValidationMessages::PASSWORD_DONOT_MATCH); ?>";
    var lang_lengthExceeds = "Password should contain less than 20 Chars";
    var lang_lengthShort = "Password should contain more than 4 Chars";
   
	<?php if (isset($key)) { ?>
        	var key = '<?php echo $key; ?>';
	<?php } else { ?>
        	var key = "";
	<?php } ?>

	$(document).ready(function() {
			if(key != "")
			{
				$("#message").hide();
				$("#sentMessage").hide();
				if(key == "passwordreset")
				{
					$("#divResetPwd").hide();
					$("#sentMessage").show();
				}
			}
			else {
				$("#divResetPwd").hide();
				$("#sentMessage").hide();
			}

			$('#btnSave').click(function() {
				if(isValidForm()){
					
	                $('form#frmResetPwd').attr({
	                    action:linkForPasswordReset+"?rk="+key
	                });
	            	
	                $('#frmResetPwd').submit();
	           }
			
			});
	});

	
	
	function isValidForm() {
	
	$.validator.addMethod("confirmPassword", function(value, element, params) {
		var confirmPwd=true;
		
		if(($('#pwd').val()) != ($('#repwd').val()))
		{
			confirmPwd = false;
		}
		return confirmPwd;
	});

	var validator = $("#frmResetPwd").validate({
		
		rules: {
			'reset[password]' : {
				required:true,
				minlength: 4,
				maxlength:20
			},
			'reset[repassword]' : {
				confirmPassword: true,
				required:true
			}

		},
		messages: {
			'reset[password]' : {
				required: lang_required,
				minlength: lang_lengthShort,
				maxlength: lang_lengthExceeds
			},
			'reset[repassword]' : {
				confirmPassword: lang_Password,
				required: lang_required
			}
		}
	});
	return true;
}
    //]]>
</script>