<?php 

// Allow header partial to be overridden in individual actions
// Can be overridden by: slot('header', get_partial('module/partial'));
include_slot('header', get_partial('global/header'));
$currentYear = date('Y');
?>

</head>
<script>
    var link = "<?php echo url_for('recruitmentApply/jobs.html'); ?>";
	function changeWin(){
		window.open(link,"","");
	}
</script>
    <body>
      
        <div id="wrapper">
            
            <div id="branding">
                <img src="<?php echo theme_path('images/Syn-Big-Logo.gif')?>" width="283" height="56" alt="OrangeHRM"/>
                <a href="#" id="welcome" class="panelTrigger"><?php echo __("Welcome %username%", array("%username%" => $sf_user->getAttribute('auth.firstName'))); ?></a>
                <div id="welcome-menu" class="panelContainer">
                    <ul>
                        <li><a href="<?php echo url_for('admin/changeUserPassword'); ?>"><?php echo __('Change Password'); ?></a></li>
                        <li><a href="<?php echo url_for('auth/logout'); ?>"><?php echo __('Logout'); ?></a></li>
                    </ul>
                </div>
                <div style="float: right; padding-right: 10px; padding-top: 1px;">
               		<a href="#" onclick="changeWin()"><img  src="<?php echo theme_path('images/hiring_icon.png')?>"/></a>
               	</div>
            </div> <!-- branding -->      
            
            <?php include_component('core', 'mainMenu'); ?>

            <div id="content">

                  <?php echo $sf_content ?>

            </div> <!-- content -->
          
        </div> <!-- wrapper -->
        
        <div id="footer">
            SynerzipHRMS ver 2.6 &copy; <a href="http://www.orangehrm.com" target="_blank">OrangeHRM</a>. 2005 - <?php echo $currentYear?> All rights reserved.
        </div> <!-- footer -->        
        
        
<?php include_slot('footer', get_partial('global/footer'));?>
<script type="text/javascript">
$(document).ready(function(){
	$('#menu_core_showGoogleForms').click(function(e){
		url = 'https://drive.google.com/a/synerzip.com/folderview?id=0B_vE39dgPgKsVW9MYjhrSWdxdlU';
		window.open(url,"","");
		e.preventDefault();
		return false;
	});
});
</script>
