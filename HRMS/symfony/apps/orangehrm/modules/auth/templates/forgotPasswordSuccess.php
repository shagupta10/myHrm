
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
	
	table { border-spacing: 5px; }

	th, td { padding: 5px; }
</style>



   <center>
        <img src = "<?php echo "{$imagePath}/syn-logo-withTagLine.png"; ?>" width="339" height="66"/>
		<?php include_partial('global/flash_messages', array('prefix' => 'forgotpassword')); ?>
	</center>
		<div class="main">
		<div class=box>
		<div class="head"><h1>Identify your account</h1></div>
		<div class="inner">
		You need to enter the information below to help identify your account<br><br>
  		<form id="frmForgotPwd" action = "<?php echo url_for('auth/forgotPassword'); ?>" method="post">
  		<center>
			<table>
				<tr><td>Employee Id</td><td> <?php echo $form['employeeId']->render(); ?></td></tr>
				<tr><td>Email</td><td> <?php echo $form['email']->render(); ?></td></tr>
			</table>
			<br><br><input type="button" name="Submit" class = "button" id="btnReset" value="<?php echo __('Reset Password'); ?>" /> <input type="button" id="btnBack" value="<?php echo __("Back"); ?>" />
		</center>
		</form>
		</div>
		</div>
		</div>
	
<div class="footer">
	<?php include_partial('core/footer'); ?>
</div>

<script type="text/javascript">
    //<![CDATA[
    var linkForLogin = "<?php echo url_for('auth/login')?>";
    var linkForForgotPwd = "<?php echo url_for('auth/forgotPassword')?>";
    var lang_required = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	$(document).ready(function() {

			$('#btnReset').click(function() {
				if(isValidForm()){
	                $('form#frmForgotPwd').attr({
	                    action:linkForForgotPwd
	                });
	            	
	                $('#frmForgotPwd').submit();
	           }
			});

			$('#btnBack').click(function() {
				window.location.replace(linkForLogin);
			});

	});


	function isValidForm() {
	var validator = $("#frmForgotPwd").validate({
		rules: {
			'employeeId' : {
				required:true

			},
			'email' : {
				required:true
			}

		},
		messages: {
			'employeeId' : {
				required: lang_required
	
			},
			'email' : {
				required: lang_required
			}
		
		}
	});
	return true;
}
    //]]>
</script>