<?php

class viewDirectoryAction extends sfAction {
    private $employeeService;

    /**
     * Get CandidateService
     * @returns CandidateService
     */
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }
    
    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }
    

    /**
     *
     * @param <type> $request
     */
    public function execute($request) {
	    if ($this->getUser()->hasFlash('templateMessage')) {
		    list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
	    }
        /* DESC:- Cleared filter on page load at first time & back navigation */
        if($request->getMethod()!='POST')
        {  
            $this->setFilters(array());
        }
        $empNumber = $request->getParameter('empNumber');
        
        $RecordsLimit = $request->getPostParameter('empDir[recordsPer_Page_Limit]');
        $this->recordsLimits = $RecordsLimit;
        $isPaging = $request->getParameter('hdnAction') == 'search' ? 1 : $request->getParameter('pageNo', 1);

        $pageNumber = $isPaging;
        if (!empty($empNumber) && $this->getUser()->hasAttribute('pageNumber')) {
            $pageNumber = $this->getUser()->getAttribute('pageNumber');
        }
        if($RecordsLimit){
        	$noOfRecords = $RecordsLimit;
        }else{
        	$noOfRecords = 10;//sfConfig::get('app_items_per_page');
        	$this->recordsLimits = $noOfRecords;
        }       
        
        $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;

         // Reset filters if requested to
        if ($request->hasParameter('reset')) {
            $this->setFilters(array());
            $this->setSortParameter(array("field"=> NULL, "order"=> NULL));
            $this->setPage(1);
        }

        $this->form = new viewDirectoryForm($this->getFilters());
        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                
                if($this->form->getValue('isSubmitted')=='yes'){
                    $this->setSortParameter(array("field"=> NULL, "order"=> NULL));
                }         
                
                $this->setFilters($this->form->getValues());
                
            } else {
                $this->setFilters(array());
            }

            $this->setPage(1);
        }
        
        
        if ($request->isMethod('get')) {
            $sortParam = array("field"=>$request->getParameter('sortField'), 
                               "order"=>$request->getParameter('sortOrder'));
            $this->setSortParameter($sortParam);
            $this->setPage(1);
        }
        
        
        $sort = $this->getSortParameter();
        $sortField = $sort["field"];
        $sortOrder = $sort["order"];
        $filters = $this->getFilters();
        
        
        $this->filterApply = !empty($filters);
        $count = $this->getEmployeeService()->getSearchEmployeeCount( $filters, true );
        
        $parameterHolder = new EmployeeSearchParameterHolder();
        $parameterHolder->setOrderField($sortField);
        $parameterHolder->setOrderBy($sortOrder);
        $parameterHolder->setLimit($noOfRecords);
        $parameterHolder->setOffset($offset);
        $parameterHolder->setFilters($filters);
        $list = $this->getEmployeeService()->searchEmployeesForDirectory($parameterHolder);
        $this->setListComponent($list, $count, $noOfRecords, $pageNumber);

        // Show message if list is empty, and we don't already have a message.
        if (empty($this->message) && (count($list) == 0)) {

            // Check to see if we have any employees in system
            $employeeCount = $this->getEmployeeService()->getEmployeeCount(true,true);
            $this->messageType = "warning";

            if (empty($employeeCount)) {
                $this->message = __("No Employees Available");
            } else {
                $this->message = __(TopLevelMessages::NO_RECORDS_FOUND);
            }
        }
    }
    
    protected function setListComponent($employeeList, $count, $noOfRecords, $page) {
        
        $configurationFactory = $this->getListConfigurationFactory();
        $runtimeDefinitions = array();
        $buttons = array();
        $runtimeDefinitions['hasSelectableRows'] = false;
        $runtimeDefinitions['buttons'] = $buttons;
        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setActivePlugin('orangehrmPimPlugin');
        ohrmListComponent::setListData($employeeList);
        ohrmListComponent::setItemsPerPage($noOfRecords);
        ohrmListComponent::setNumberOfRecords($count);      
        ohrmListComponent::setPageNumber($page);
    }
    
    protected function getListConfigurationFactory() {
        $configurationFactory = new DirectoryHeaderFactory();        
        return $configurationFactory;
    }

    /**
     * Set's the current page number in the user session.
     * @param $page int Page Number
     * @return None
     */
    protected function setPage($page) {
        $this->getUser()->setAttribute('dirlist.page', $page, 'pim_module');
    }

    /**
     * Get the current page number from the user session.
     * @return int Page number
     */
    protected function getPage() {
        return $this->getUser()->getAttribute('dirlist.page', 1, 'pim_module');
    }
    
    /**
     * Sets the current sort field and order in the user session.
     * @param type Array $sort 
     */
    protected function setSortParameter($sort) {
        $this->getUser()->setAttribute('dirlist.sort', $sort, 'pim_module');
    }

    /**
     * Get the current sort feild&order from the user session.
     * @return array ('field' , 'order')
     */
    protected function getSortParameter() {
        return $this->getUser()->getAttribute('dirlist.sort', null, 'pim_module');
    }
    
    /**
     *
     * @param array $filters
     * @return unknown_type
     */
    protected function setFilters(array $filters) {
        return $this->getUser()->setAttribute('dirlist.filters', $filters, 'pim_module');
    }

    /**
     *
     * @return unknown_type
     */
    protected function getFilters() {
        return $this->getUser()->getAttribute('dirlist.filters', null, 'pim_module');
    }

    protected function _getFilterValue($filters, $parameter, $default = null) {
        $value = $default;
        if (isset($filters[$parameter])) {
            $value = $filters[$parameter];
        }
        return $value;
    }

}
