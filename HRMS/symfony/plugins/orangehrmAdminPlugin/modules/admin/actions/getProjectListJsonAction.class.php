<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of getProjectListJsonAction
 *
 * @author orangehrm
 */
class getProjectListJsonAction extends sfAction {
	
	private $projectService;
    private $customerService;

	public function getProjectService() {
		if (is_null($this->projectService)) {
			$this->projectService = new ProjectService();
			$this->projectService->setProjectDao(new ProjectDao());
		}
		return $this->projectService;
	}
    public function getCustomerService() {
		if (is_null($this->customerService)) {
			$this->customerService = new CustomerService();
			$this->customerService->setCustomerDao(new CustomerDao());
		}
		return $this->customerService;
	}
	
	public function execute($request) {

		$this->setLayout(false);
		sfConfig::set('sf_web_debug', false);
		sfConfig::set('sf_debug', false);
        $jsonArray=array();
		if ($this->getRequest()->isXmlHttpRequest()) {
			$this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
		}

		$customerId = $request->getParameter('customerId');
        $searchText= $request->getParameter('q');
        if(!isset($searchText)) $searchText=null;
        if($customerId=='')
        $projectList = $this->getProjectService()->getAllProjects(true,$searchText);
        else  
		$projectList = $this->getProjectService()->getProjectsByCustomerId($customerId,$searchText);
        foreach ($projectList as $project) {
         $customer=$this->getCustomerService()->getCustomerById($project->getCustomerId());
         $customerName=  substr($customer->getName(),0,8);
         if(strlen($customer->getName())>8)  
          $customerName=  ''.$customerName.'...';
         $jsonArray[] = array('name' => $project->getName()." [".$customerName."]", 'id' => $project->getProjectId());
	    }
		return $this->renderText(json_encode($jsonArray));
	
	}
}

?>
