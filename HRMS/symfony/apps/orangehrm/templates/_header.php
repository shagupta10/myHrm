<!DOCTYPE html>
<?php 
$cultureElements = explode('_', $sf_user->getCulture()); 
?>
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="<?php echo $cultureElements[0]; ?>" lang="<?php echo $cultureElements[0]; ?>">
    
    <head>

        <title>SynerzipHRM</title>
        
        <script>
			
/*------Added By Abhishek------ */var baseUrl = "<?php echo sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot(); ?>";
			  (function(i,s,o,g,r,a,m){i['GoogleAnalyticsObject']=r;i[r]=i[r]||function(){
			  (i[r].q=i[r].q||[]).push(arguments)},i[r].l=1*new Date();a=s.createElement(o),
			  m=s.getElementsByTagName(o)[0];a.async=1;a.src=g;m.parentNode.insertBefore(a,m)
			  })(window,document,'script','//www.google-analytics.com/analytics.js','ga');
			
			  ga('create', 'UA-46198119-1', 'synerzip.in');
			  ga('send', 'pageview');
		
		</script>
		
        <?php include_http_metas() ?>
        <?php include_metas() ?>
        
        <link rel="shortcut icon" href="<?php echo theme_path('images/syn_icon.png')?>" />
        
        <!-- Library CSS files -->
        <link href="<?php echo theme_path('css/reset.css')?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo theme_path('css/tipTip.css')?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo theme_path('css/jquery/jquery-ui-1.8.21.custom.css')?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo theme_path('css/jquery/jquery.autocomplete.css')?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo theme_path('css/jquery/token-input.css')?>" rel="stylesheet" type="text/css"/>
        <link href="<?php echo theme_path('css/jquery/token-input-facebook.css')?>" rel="stylesheet" type="text/css"/>
        
        <!-- Custom CSS files -->
        <link href="<?php echo theme_path('css/main.css')?>" rel="stylesheet" type="text/css"/>
        
        
        <?php       
        // Library JavaScript files

        echo javascript_include_tag('jquery/jquery-1.7.2.min.js');

        echo javascript_include_tag('jquery/validate/jquery.validate.js');
        
        echo javascript_include_tag('jquery/jquery.ui.core.js');
        echo javascript_include_tag('jquery/jquery.autocomplete.js');
        echo javascript_include_tag('orangehrm.autocomplete.js');
        echo javascript_include_tag('jquery/jquery.ui.datepicker.js');
        echo javascript_include_tag('jquery/jquery.form.js');
        echo javascript_include_tag('jquery/jquery.tipTip.minified.js');
        echo javascript_include_tag('jquery/bootstrap-modal.js');
        echo javascript_include_tag('jquery/jquery.clickoutside.js');
       	echo javascript_include_tag('jquery/jquery.easytabs.min.js');
       	echo javascript_include_tag('jquery/jquery.tablesorter.min.js');
       	echo javascript_include_tag('jquery/jquery.tokeninput.js');
       	
      
        // Custom JavaScript files
        echo javascript_include_tag('orangehrm.validate.js');
        echo javascript_include_tag('archive.js');
        

        /* Note: use_javascript() doesn't work well when we need to maintain the order of JS inclutions.
         * Ex: It may include a jQuery plugin before jQuery core file. There are two position options as
         * 'first' and 'last'. But they don't seem to resolve the issue.
         */
        ?>   
        
        <!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
        <!--[if lt IE 9]>
            <script src="http://html5shim.googlecode.com/svn/trunk/html5.js"></script>
        <![endif]-->        


        <!-- For Google Sign In -->
        <script type="text/javascript">
			// var helper = (function() {
			//   var authResult = undefined;

			//   return {
			// 	/**
			// 	 * Hides the sign-in button and connects the server-side app after
			// 	 * the user successfully signs in.
			// 	 *
			// 	 * @param {Object} authResult An Object which contains the access token and
			// 	 *   other authentication information.
			// 	 */
			// 	onSignInCallback: function(authResult) {
			// 	  if (authResult['access_token']) {
			// 		// The user is signed in
			// 		this.authResult = authResult;
			// 		helper.connectServer();
			// 		// After we load the Google+ API, render the profile data from Google+.
			// 		gapi.client.load('plus','v1',this.renderProfile);
			// 	  } else if (authResult['error']) {
			// 		// There was an error, which means the user is not signed in.
			// 		// As an example, you can troubleshoot by writing to the console:
			// 		console.log('There was an error: ' + authResult['error']);
			// 		$('#gConnect').show();
			// 	  }
			// 	  //console.log('authResult', authResult);
			// 	},
			// 	/**
			// 	 * Retrieves and renders the authenticated user's Google+ profile.
			// 	 */
			// 	renderProfile: function() {
			// 	  var request = gapi.client.plus.people.get( {'userId' : 'me'} );
			// 	  request.execute( function(profile) {
			// 	 	  $('#profile').empty();
			// 		  if (profile.error) {
			// 			$('#profile').append(profile.error);
			// 			return;
			// 		  }
			// 		  var ajaxUrl = '<?php echo sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/sfGoogleLogin/Userajax' ?>';
			// 		  var homeUrl = '<?php echo sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/pim/viewMyDetails' ?>';
			// 		  $.ajax({
			// 			  url:ajaxUrl,
			// 			  type:"POST",
			// 			  data: ({email: profile.emails[0]["value"]}),
			// 			  success: function(data){
			// 				if(data == "success") {
			// 					window.location.href = homeUrl;
			// 				}
			// 			  }
			// 		 });
				
			// 		});
				  
			// 	  //$('#gConnect').hide();
			// 	},
			// 	/**
			// 	 * Calls the server endpoint to disconnect the app for the user.
			// 	 */
			// 	disconnectServer: function() {
			// 	  // Revoke the server tokens
			// 	  $.ajax({
			// 		type: 'POST',
			// 		url: window.location.href + '/disconnect',
			// 		async: false,
			// 		success: function(result) {
			// 		  //console.log('revoke response: ' + result);
			// 		  $('#authOps').hide();
			// 		  $('#profile').empty();
			// 		  $('#visiblePeople').empty();
			// 		  $('#authResult').empty();
			// 		  $('#gConnect').show();
			// 		},
			// 		error: function(e) {
			// 		  console.log(e);
			// 		}
			// 	  });
			// 	},
			// 	/**
			// 	 * Calls the server endpoint to connect the app for the user. The client
			// 	 * sends the one-time authorization code to the server and the server
			// 	 * exchanges the code for its own tokens to use for offline API access.
			// 	 * For more information, see:
			// 	 *   https://developers.google.com/+/web/signin/server-side-flow
			// 	 */
			// 	connectServer: function() {
			// 	  //console.log(this.authResult.code);
			// 	  $.ajax({
			// 		type: 'POST',
			// 		url: window.location.href + '/connect?state={{ STATE }}',
			// 		contentType: 'application/octet-stream; charset=utf-8',
			// 		success: function(result) {
			// 		  //console.log(result);
			// 		  //helper.people();
			// 		},
			// 		processData: false,
			// 		data: this.authResult.code
			// 	  });
			// 	},
			//   };
			// })();

			// /**
			//  * Perform jQuery initialization and check to ensure that you updated your
			//  * client ID.
			//  */
			// $(document).ready(function() {
			//   $('#disconnect').click(helper.disconnectServer);
			// });

			// /**
			//  * Calls the helper method that handles the authentication flow.
			//  *
			//  * @param {Object} authResult An Object which contains the access token and
			//  *   other authentication information.
			//  */
			// function onSignInCallback(authResult) {
			//   helper.onSignInCallback(authResult);
			// }
		</script>
