<?php

class validateCredentialsAction extends sfAction {

    protected $authenticationService;
    protected $homePageService;
    protected $logPath;

    public function execute($request) {
       
		$this->logPath = ROOT_PATH . '/lib/logs/logindetails.log';
        if ($request->isMethod(sfWebRequest::POST)) {
            
            $username = $request->getParameter('txtUsername');
            $password = $request->getParameter('txtPassword');
            $additionalData = array(
                'timeZoneOffset' => $request->getParameter('hdnUserTimeZoneOffset', 0),
            );
            
            // This is to view jobs and apply to it (online)           
            /*$url = $_SERVER['HTTP_REFERER'];
            $path = parse_url($url, PHP_URL_PATH);
			$pathFragments = explode('/', $path);
			$end = end($pathFragments);
			
			$logMessage = "The URL is : $url";
			$this->logResult('loginURL', $logMessage);*/

            try {

                $success = $this->getAuthenticationService()->setCredentials($username, md5($password), $additionalData);
                
                if ($success) {                    
                    $this->redirect($this->getHomePageService()->getPathAfterLoggingIn($this->getContext()));
                    
                } else {
                    $this->getUser()->setFlash('message', __('Invalid credentials'), true);
                    $this->forward('auth', 'retryLogin');
                }
                
            } catch (AuthenticationServiceException $e) {
                
                $this->getUser()->setFlash('message', $e->getMessage(), false);
                $this->forward('auth', 'login');
                
            }
            
        }

        return sfView::NONE;
    }

    /**
     *
     * @return AuthenticationService 
     */
    public function getAuthenticationService() {
        if (!isset($this->authenticationService)) {
            $this->authenticationService = new AuthenticationService();
            $this->authenticationService->setAuthenticationDao(new AuthenticationDao());
        }
        return $this->authenticationService;
    }
    
    public function getHomePageService() {
        
        if (!$this->homePageService instanceof HomePageService) {
            $this->homePageService = new HomePageService($this->getUser());
        }
        
        return $this->homePageService;
        
    }

    public function setHomePageService($homePageService) {
        $this->homePageService = $homePageService;
    }
    
      public function logResult($type = '', $logMessage = '') {

        if (file_exists($this->logPath) && !is_writable($this->logPath)) {
            throw new Exception("Email Notifications : Log file is not writable");
        }

        $message = '========== Message Begins ==========';
        $message .= "\r\n\n";
        $message .= 'Time : '.date("F j, Y, g:i a");
        $message .= "\r\n";
        $message .= 'Message Type : '.$type;
        $message .= "\r\n";
        $message .= 'Message : '.$logMessage;
        $message .= "\r\n\n";
        $message .= '========== Message Ends ==========';
        $message .= "\r\n\n";

        file_put_contents($this->logPath, $message, FILE_APPEND);

    }
    

}
