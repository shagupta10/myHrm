<?php
$imagePath = theme_path("images/login");
$version = '2.6';
$copyrightYear = date('Y');
?>

<style type="text/css">
    #divFooter {
        text-align: center;
    }
    
    #spanCopyright, #spanSocialMedia {
        padding: 20px 10px 10px 10px;
    }
    
    #spanSocialMedia a img {
		border: none;
    }

</style>
<div id="divFooter" >
    <span id="spanCopyright">
        <a href="http://www.orangehrm.com" target="_blank">SynerzipHRMS</a> 
        ver <?php echo $version; ?> &copy; OrangeHRM Inc. 2005 - <?php echo $copyrightYear; ?> All rights reserved.
    </span>
    <span id="spanSocialMedia">
        <a href="https://www.linkedin.com/company/synerzip" target="_blank">
            <img src="<?php echo "{$imagePath}/linkedin.png"; ?>" /></a>&nbsp;
        <a href="https://www.facebook.com/Synerzip" target="_blank">
            <img src="<?php echo "{$imagePath}/facebook.png"; ?>" /></a>&nbsp;
        <a href="https://twitter.com/Synerzip_Agile" target="_blank">
            <img src="<?php echo "{$imagePath}/twiter.png"; ?>" /></a>&nbsp;
        <a href="https://www.youtube.com/user/SynerzipWebiChannel" target="_blank">
            <img src="<?php echo "{$imagePath}/youtube.png"; ?>" /></a>&nbsp;
    </span>
    <br class="clear" />
</div>
