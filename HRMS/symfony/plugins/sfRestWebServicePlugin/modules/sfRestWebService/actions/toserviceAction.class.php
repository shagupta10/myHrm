<?php

class toserviceAction extends sfAction {
	public function preExecute()
	{
		parent::preExecute();
	
		$this->config = new sfRestWebServiceConfiguration($this->getContext()->getConfiguration());
		$this->enableDoctrineValidation();
	
		if ($this->isProtected())
		{
			$this->authenticate();
		}
	
		$this->checkContentType();
	}
	
    public function execute($request) {
    	$request->setParameter('service', 'leaves');
    	$this->service = 'leaves';
    	$query = Doctrine::getTable('Leave')->createQuery('wsmodel');
    	if($request->getParameter('id')) {
    		$query->where('emp_number = ?', $request->getParameter('id'));
    	}
    	$this->executeRequest($query, $request);
    }
}