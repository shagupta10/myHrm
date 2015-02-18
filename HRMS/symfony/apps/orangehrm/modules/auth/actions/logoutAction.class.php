<?php

class logoutAction extends sfAction {

    /**
     * Logout action
     * @param $request 
     */
    public function execute($request) {
    	$this->getUser()->setAuthenticated( false );
    	$this->getUser()->setAttribute( 'sfGoogleLogin_account', null );
    	$this->getUser()->setAttribute( 'sfGoogleLogin_returnTo', null );
        $authService = new AuthenticationService();
        $authService->clearCredentials();
        $this->redirect('auth/login');
    }

}

