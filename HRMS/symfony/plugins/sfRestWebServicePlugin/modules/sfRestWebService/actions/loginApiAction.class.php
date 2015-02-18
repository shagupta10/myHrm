<?php
/**
 * returns response status and Api key on success
 * @return ApiAuthentication
 * @package    api
 * @subpackage action
 * @author     Mayur Kathale
 */
class loginApiAction extends BaseActionRest {
	private $authenticationService;
    private $user;
	
	/**
	 * Get Authentication service
	 * @return AuthenticationService
	 */
	public function getAuthenticationService() {
		if (!isset($this->authenticationService)) {
			$this->authenticationService = new AuthenticationService();
			$this->authenticationService->setAuthenticationDao(new AuthenticationDao());
		}
		return $this->authenticationService;
	}

	public function preExecute()
	{
		parent::preExecute();
		$headers = apache_request_headers();
		if(isset($headers['user-id']) && isset($headers['password'])) {
			$username = base64_decode($headers['user-id']);
			$password = base64_decode($headers['password']);
			$this->user = $this->getAuthenticationService()->checkCredentialsForApi($username, md5($password));
		} else {
			$this->user = false;
		}	
	}
	
    public function execute($request) {
    	if($this->user) {
    		$apiAuth = $this->getApiAuthenticationService()->checkApiKeyForUser($this->user->getEmpNumber());
    		if($apiAuth) {
    			$key =  $apiAuth->getApiKey();
    			$resp['api-key'] = $key;
    		} else {
    			$key = $this->getApiAuthenticationService()->createApiKeyForUser($this->user->getEmpNumber());
    			$resp['api-key'] = $key;
    		}
    		$resp['response'] = "success";
    		return $this->renderText(json_encode($resp, JSON_FORCE_OBJECT));
    	} else {
    		$resp['response'] = "failure";
    		return $this->renderText(json_encode($resp, JSON_FORCE_OBJECT));
    	}
    }
}