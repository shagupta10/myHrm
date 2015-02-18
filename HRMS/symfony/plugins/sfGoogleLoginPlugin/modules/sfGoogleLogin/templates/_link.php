<?php use_javascript('/sfGoogleLoginPlugin/js/sfGoogleLogin_jQuery'); ?>
<?php use_javascript('/sfGoogleLoginPlugin/js/sfGoogleLogin'); ?>
<?php if ( !sfConfig::get('sfGoogleLogin_jsLoaded') ) : ?>
<script type="text/javascript">
<!--
$('document').ready(function(e){
    $('a.googleLogin').click(function(e){
        e.preventDefault();
        var gLogin = new googleLogin();
        gLogin.googleLoginPopup();
    });
});
//-->
</script>
<style type="text/css">
	.googleLoginNew {
		float: right;
		margin: -3% 34%;
	}
	.idError {
		color:#dd7700;
		position:absolute;
		bottom:158px;
		right:530px;
		background: transparent url(<?php echo "{$imagePath}/mark.png"; ?>) no-repeat;
		font-weight: bold;
		padding-left: 18px;
	}
</style>
<?php endif; sfConfig::set('sfGoogleLogin_jsLoaded', true) ?>

<?php
    if ( $sf_user->isAuthenticated() ) {
		$homePage = sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/';
        header("Location: $homePage");exit;
	}
    else {
		if (isset($_GET['code'])) {		
			$client = new GoogleClient();
			$client->authenticate($_GET['code']);			
			$_SESSION['access_token'] = $client->getAccessToken();			
			$access_token_arr = json_decode($_SESSION['access_token']);			
			$result_encoded = $client->get_user_data($access_token_arr->access_token);
			$result = json_decode($result_encoded);					
			$emailId = $result->email;
			$emailArr = explode("@",$emailId);
			if($emailArr[1] == "synerzip.com") {
				$username = $emailArr[0];
				$userDetails=Doctrine :: getTable('SystemUser')->findOneByUserName($username);
				$password = $userDetails->getUserPassword();
				$additionalData = array(); 
				$authService = new AuthenticationService();
				$success = $authService->setCredentials($username, $password, $additionalData); ?>
				<script type='text/javascript'>window.location = '<?php echo $homePage; ?>'</script>
				<?php 
			} else { ?>
			<span class="idError">Enter Only Synerzip Id</span>	
		<?php	}		
		}		
		echo link_to ( image_tag('signin.png',array('width'=>'44px','style'=>'clear:both;display:both;margin:15%')), $loginUrl, array ('class' => 'googleLoginNew','target' => '_self' ) );
	}
?>
