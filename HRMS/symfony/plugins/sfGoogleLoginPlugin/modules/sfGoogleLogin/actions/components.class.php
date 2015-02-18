<?php

/**
 * sfGoogleLogin components.
 *
 * @package    sfGoogleLoginPlugin
 * @subpackage actions
 * @author     Sebastian Herbermann <sebastian.herbermann@googlemail.com>
 */
class sfGoogleLoginComponents extends sfComponents {
    /**
     * Executes index action
     *
     * @param sfRequest $request A request object
     */
    public function executeLink() {        
        $user = $this->getUser();
  
        if ( $user->isAuthenticated() ) {
        	$this->logoutUrl = $this->generateUrl('sfGoogleLogin_logout');
        } else {
			$client_id = '197255862356-4hucf7oc2885a53im2ou71m38oie7kp2.apps.googleusercontent.com';
			$client_secret = 'Ovo2vK6wg08FMW0jsa0IoP_P';
			$redirect_uri = 'http://' . $_SERVER['HTTP_HOST'] . $_SERVER['PHP_SELF'];
			$client = new GoogleClient();
			$client->setClientId($client_id);
			$client->setClientSecret($client_secret);
			$client->setRedirectUri($redirect_uri);
			$client->addScope("profile");
			$client->addScope("email");			
			$client->addScope("https://www.googleapis.com/auth/plus.me");			
			$client->setOpenidRealm("http://{$_SERVER['SERVER_NAME']}");
			$client->setLoginHint("");
			$authUrl = $client->createAuthUrl();			
	        $request = $this->getContext()->getRequest();	        
	        $user->setAttribute('sfGoogleLogin_returnTo', $request->getUri() );	    
	        $this->loginUrl = $authUrl;
        }        
    }
}
