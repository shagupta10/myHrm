<?php

/**
 * sfGoogleLogin actions.
 *
 * @package    sfGoogleLoginPlugin
 * @subpackage actions
 * @author     Sebastian Herbermann <sebastian.herbermann@googlemail.com>
 */
class sfGoogleLoginActions extends sfActions {
    public function executeLogout(sfWebRequest $request) {
        $this->getUser()->setAuthenticated( false );
        $this->getUser()->setAttribute( 'sfGoogleLogin_account', null );
        $this->getUser()->setAttribute( 'sfGoogleLogin_returnTo', null );
	    $authService = new AuthenticationService();
        $authService->clearCredentials();
        $this->redirect('auth/login');
    }
}
