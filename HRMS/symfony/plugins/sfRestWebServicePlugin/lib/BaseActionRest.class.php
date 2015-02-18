<?php

class BaseActionRest extends sfActions
{
	protected $leaveRestCodeMapping = array("paid","wfh","compoff","marriage","paternity","maternity","unpaid");
	const BIRTHDAY = "birthday";
	const HOLIDAY = "holiday";
	protected $authentication;
	/**
	 * Get ApiAuthentication service
	 * @return ApiAuthenticationService
	 */
	public function getApiAuthenticationService() {
		if (is_null($this->apiAuthenticationService)) {
			$this->apiAuthenticationService = new ApiAuthenticationService();
			$this->apiAuthenticationService->setApiAuthenticationDao(new ApiAuthenticationDao());
		}
		return $this->apiAuthenticationService;
	}
	
	public function preExecute()
	{
		parent::preExecute();
		$this->setJsonResponse();
		$this->config = new sfRestWebServiceConfiguration($this->getContext()->getConfiguration());
		$this->enableDoctrineValidation();
		if ($this->isProtected())
		{
			$this->authenticate();
		}
		$this->checkContentType();
		$headers = apache_request_headers();
		if(isset($headers['api-key'])) {
			$authentication =  $this->getApiAuthenticationService()->checkApiKeyForAuthentication($headers['api-key']);
			if(count($authentication) > 0) {
				foreach ($authentication as $obj) {
					$this->authentication = $obj;
				}
			} else {
				$this->authentication = false;
			}
		} else {
			$this->authentication = false;
		}
	}

	public function setJsonResponse() {
		$response = $this->getResponse();		
    	$response->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
    	$response->setHttpHeader('Expires', '0');
    	$response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
    	$response->setHttpHeader("Cache-Control", "private", false);
	}
	
	public function execute500(sfWebRequest $request)
	{
		$this->feedback = 'Internal server error: unsupported service';
		$this->setTemplate('error');
	}
	
	protected function appendMethodForQuery(Doctrine_Query $query)
	{
		$method_for_query = $this->config->get('services_'.$this->service.'_methodForQuery');
		if ($method_for_query)
		{
			return Doctrine::getTable($this->model)->$method_for_query($query);
		}
		return $query;
	}
	
	protected function authenticate()
	{
		$ip_addresses = $this->config->get('allowed');
	
		if (is_array($ip_addresses) && in_array($this->request->getRemoteAddress(), $ip_addresses))
		{
			return true;
		}
	
		$this->response->setStatusCode('403');
		$this->redirect($this->config->get('protectedRoute'), '403');
	}
	
	protected function checkContentType()
	{
		if ($this->request->getRequestFormat() == 'yaml')
		{
			$this->setLayout(false);
			$this->getResponse()->setContentType('text/yaml');
		}
	}
	
	protected function checkServiceAvailability(sfWebRequest $request)
	{
		$this->service = $request->getParameter('service');
		$services = $this->config->get('services');
		if (is_array($services) && !array_key_exists($this->service, $services))
		{
			$this->forward404();
		}
	
		$this->checkRequestState();
		$this->model = $this->config->get('services_'.$this->service.'_model');
	}
	
	protected function checkRequestState()
	{
		$service = $this->request->getParameter('service');
		$states = $this->config->get('services_'.$service.'_states');
	
		if (is_array($states) && !array_key_exists($this->request->getMethod(), $states))
		{
			$this->response->setStatusCode('405');
			$this->feedback = 'The request method isn\'t allowed';
			$this->setTemplate('error');
		}
	
		return true;
	}
	
	protected function enableDoctrinevalidation()
	{
		$manager = Doctrine_Manager::getInstance();
		$manager->setAttribute(Doctrine::ATTR_VALIDATE, Doctrine::VALIDATE_ALL);
	}
	
	protected function executeRequest(Doctrine_Query $query, sfWebRequest $request)
	{
		$method = ucfirst((strtolower($request->getMethod())));
		$request_type = 'execute'.$method.'Request';
		$this->$request_type($query, $request);
	}
	
	protected function getQuery(sfWebRequest $request)
	{
		$this->checkServiceAvailability($request);
	
		if (!class_exists($this->model))
		{
			$this->response->setStatusCode(500);
			$this->forward('sfRestWebService', '500');
		}
	
		return Doctrine::getTable($this->model)->createQuery('wsmodel');
	}
	
	protected function isProtected()
	{
		return $this->config->get('protected');
	}
	
	protected function executeDeleteRequest(Doctrine_Query $query, sfWebRequest $request)
	{
		$this->object->delete();
		$this->feedback = 'Object has been deleted';
		$this->setTemplate('delete');
	}
	
	protected function executeGetRequest(Doctrine_Query $query, sfWebRequest $request)
	{
		$query = $this->appendMethodForQuery($query);
		$this->objects = $query->execute();
	}
	
	
	protected function executePostRequest(Doctrine_Query $query, sfWebRequest $request)
	{
		$this->setTemplate('object');
		$this->object = new $this->model;
		$this->object->fromArray($request->getPostParameters());
		$this->updateObject($request);
	}
	
	protected function executePutRequest(Doctrine_Query $query, sfWebRequest $request)
	{
		$this->object->fromArray($request->getPostParameters());
		$this->updateObject($request);
	}
	
	protected function updateObject(sfWebRequest $request)
	{
		try
		{
			$this->object->save();
		}
		catch (Exception $e)
		{
			$this->feedback = $e->getMessage();
			$this->setTemplate('error');
		}
	}
}