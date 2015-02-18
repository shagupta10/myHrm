<?php
class viewReviewAction extends sfAction{
    private $performanceReviewService;
	private $jobTitleService;
	private $employeeService;
	/**
     * This method is executed before each action
     */
    public function preExecute() {
        if (!empty($_SESSION['empNumber'])) {
            $this->loggedEmpId = $_SESSION['empNumber'];
        } else {
            $this->loggedEmpId = 0; // Means default admin
        }

        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
            $this->loggedAdmin = true;
        } else {
            $this->loggedAdmin = false;
        }

        if (isset($_SESSION['user'])) {
            $this->loggedUserId = $_SESSION['user'];
        }
    }
    
	public function getPerformanceReviewService() {
		if(is_null($this->performanceReviewService)) {
			$this->performanceReviewService = new PerformanceReviewService();
			$this->performanceReviewService->setPerformanceReviewDao(new PerformanceReviewDao());
		}
		return $this->performanceReviewService;
	}
	
    public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
            $this->jobTitleService->setJobTitleDao(new JobTitleDao());
        }
        return $this->jobTitleService;
    }
    public function getEmployeeService() {
    	if (is_null($this->employeeService)) {
    		$empService = new EmployeeService();
    		$empService->setEmployeeDao(new EmployeeDao());
    		$this->employeeService = $empService;
    	}
    	return $this->employeeService;
    }
    
    /**
     * Sets EmployeeService
     * @param EmployeeService $service
     */
    public function setEmployeeService(EmployeeService $service) {
    	$this->employeeService = $service;
    }
	/**
     * Checks whether the logged in employee is a reviewer
     */
    protected function isLoggedReviewer($empId, $clues) {
        $performanceReviewService = $this->getPerformanceReviewService();
        return $performanceReviewService->isReviewer($empId, $clues);
    }
    
    public function execute($request){
        $performanceReviewService = $this->getPerformanceReviewService();
        $this->currentPerformancePeriod = $this->getPerformanceReviewService()->getCurrentPerformancePeriod();
        $clues = $this->getReviewSearchClues($request);
		$this->loggedReviewer = $this->isLoggedReviewer($_SESSION['empNumber'], $clues);
        /* Job title list */
        $this->jobList = $this->getJobTitleService()->getJobTitleList("", "", false);

		/* Subdivision list */
        $compStructure = new CompanyStructureService();
        $treeObject = $compStructure->getSubunitTreeObject();
        $this->tree = $treeObject->fetchTree();

        /* Checking whether a newly invoked search form */
        $newSearch = false;
        if ($request->getParameter('mode') == 'new') {
            $newSearch = true;
        }
        
        /* Preserving search clues */
        $hdnEmpId = $request->getParameter("hdnEmpId");
        if (isset($hdnEmpId) && !$newSearch) { // If the user has performed a new search
            $this->clues = $clues;
        } else {
            if ($this->getUser()->hasAttribute('prSearchClues') && !$newSearch) {
                $this->clues = $this->getUser()->getAttribute('prSearchClues');
            }
            if ($this->getUser()->hasFlash('prClues') && !$newSearch) {
                $this->clues = $this->getUser()->getFlash('prClues');
            }
        }
        if ($request->getParameter('pageNo')) {
            $page = $request->getParameter('pageNo');
            $this->clues['pageNo'] = $page;
        } elseif ($this->clues['pageNo']) {
            $page = $this->clues['pageNo'];
        } else{
            $page = 1;
            $this->clues['pageNo'] = $page;
        }
        $sortOrder = $request->getParameter('sortOrder');
        $sortField = $request->getParameter('sortField');
        if(!empty($sortField) && !empty($sortOrder) ) { // final added for sorting
			$this->clues['Order'] = $sortOrder;
			$this->clues['sortBy'] = $sortField;
		}
        
        /* Preserving search clues */
        if (!isset($this->clues)) {
            $this->clues = $clues;
        }
        $params = array();
		$this->parmetersForListCompoment = $params;
        $this->getUser()->setAttribute('prSearchClues', $this->clues);

        /* Checking whether wrong seacrch criteria */
        if ((!$this->_isCorrectEmployee($this->clues['empId'], $this->clues['empName'])) ||
            (!$this->_isCorrectEmployee($this->clues['reviewerId'], $this->clues['reviewerName']))
        ) {
            $this->templateMessage = array('WARNING', __(TopLevelMessages::NO_RECORDS_FOUND));
        }

        /* Setting logged in user type */
        if (!$this->loggedAdmin && $this->loggedReviewer) {
            $this->clues['loggedReviewerId'] = $this->loggedEmpId;
        } elseif (!$this->loggedAdmin && !$this->loggedReviewer) {
            $this->clues['loggedEmpId'] = $this->loggedEmpId;
        }

        /* Pagination */
        $recordsLimit = $request->getParameter('recordsPerPage_Limit');
        $this->recordsPerPage = $recordsLimit;
        $recordCount = $performanceReviewService->countReviews($this->clues);
        if($recordsLimit){
        	$limit = $recordsLimit;
        }else{
        	$limit = 10;
        	$this->recordsPerPage = $limit;
        }
        $offset = ($page > 0) ? (($page - 1) * $limit) : 0; 
        
        /* Fetching reviews */
        $this->reviews = $performanceReviewService->searchPerformanceReview($this->clues, $offset, $limit);
        
        $this->disabledReviews = array();        
        $fromDate = $this->currentPerformancePeriod->getPeriodFrom();
        foreach ($this->reviews as $review){
            if(($fromDate == $review->getPeriodFrom() && $review->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED) 
            || ($fromDate != $review->getPeriodFrom())){
                $this->disabledReviews[] = $review->getId();
            }
        }
        $this->employeeService = $this->getEmployeeService();
        /* Employee list */
        if ($this->loggedAdmin) {
        	$this->empJson = $this->employeeService->getEmployeeListAsJson();
        	//Get customer list
        	$customerservice = new CustomerService();
        	$this->projectJson = $customerservice->getCustomerListAsJson();
        } elseif ($this->loggedReviewer) {
        	$this->empJson = $performanceReviewService->getRevieweeListAsJson($this->loggedEmpId, true);
        	$this->projectJson = $this->employeeService->getEmployeeCustomerJson($this->loggedEmpId);
        } else {
        	$this->empJson = json_encode(array());
        	$this->projectJson = json_encode(array());
        }
        /* Showing Performance Review Search form
         * ====================================== */
        $this->form = new ViewReviewForm(array(), array('approvedJson' => json_encode($this->disabledReviews), 'empJson' => $this->empJson,'projectJson' => $this->projectJson), true);
        
        $this->setListComponent($this->reviews, $recordCount, $page, $limit);
        /* Setting template message */
        if ($this->getUser()->hasFlash('templateMessage')) {
            $this->templateMessage = $this->getUser()->getFlash('templateMessage');
        } elseif ($recordCount == 0) {
            $this->templateMessage = array('WARNING', __(TopLevelMessages::NO_RECORDS_FOUND));
        }
    }
    protected function setListComponent($reviews, $count, $page, $limit) {
        $loggedInEmpNumber = $this->getUser()->getAttribute('auth.empNumber');
        $fromDate = $this->getPerformanceReviewService()->getCurrentPerformancePeriod()->getPeriodFrom();
        PerformanceReviewListConfigurationFactory::setLoggedInEmpNumber($loggedInEmpNumber);
        
        $configurationFactory = new PerformanceReviewListConfigurationFactory();
        $configurationFactory->setCurrentFromDate($fromDate);
        
        $runtimeDefinitions = array();
        $buttons = array();
        if($this->loggedAdmin ){
            $buttons['Add'] = array('label' => __('Add'), 'type' => 'button', 'id' => 'addReview', 'tabindex' => '9');
            $buttons['Edit'] = array('label' => __('Edit'), 'type' => 'button', 'id' => 'editReview', 'name' => 'editReview', 'tabindex' => '10');
            $buttons['Delete'] = array('label' => __('Delete'), 'type' => 'button', 'id' => 'deleteReview', 'data-toggle' => 'modal', 'data-target' => '#deleteConfModal', 'class' => 'delete', 'tabindex' => '11');
            $buttons['Export'] = array('label' => __('Export'), 'type' => 'button', 'id' => 'exportReviews', 'tabindex' => '12');
            $buttons['Approve'] = array('label' => __('Approve'), 'type' => 'button', 'id' => 'approveReviews', 'name' => 'approveReviews', 'tabindex' => '13');
            $runtimeDefinitions['hasSelectableRows'] = true;
        }else{
            $runtimeDefinitions['hasSelectableRows'] = false;
        } 
        $runtimeDefinitions['buttons'] = $buttons;
        
        $configurationFactory->setRuntimeDefinitions($runtimeDefinitions);
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setActivePlugin('orangehrmPerformancePlugin');
        ohrmListComponent::setListData($reviews);
        ohrmListComponent::setItemsPerPage($limit);
        ohrmListComponent::setNumberOfRecords($count);
        ohrmListComponent::setPageNumber($page);
    }
    
    protected function getReviewSearchClues($request, $suffix='') {

        $clues = array();
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        
        $dateValidator = new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern));
        
        if ($request instanceof sfWebRequest) {
           	$cycleDate = $request->getParameter('performanceCycle' . $suffix);
           	$fromDate = $dateValidator->clean($cycleDate['from']);
           	$toDate = $dateValidator->clean($cycleDate['to']);
           	$performancePeriod = $this->getPerformanceReviewService()->getCurrentPerformancePeriod();
           	if(empty($fromDate) && empty($toDate)){
           		$fromDate = $performancePeriod->getPeriodFrom();
           		$toDate = $performancePeriod->getPeriodTo();
           	}
           	
            $clues['from'] = $fromDate;
            $clues['to'] = $toDate;
            
            $clues['period'] = $request->getParameter('period' . $suffix);
            $clues['due'] = $dateValidator->clean($request->getParameter('txtDueDate' . $suffix));
            $clues['jobCode'] = $request->getParameter('txtJobTitleCode' . $suffix);
            $clues['state'] = $request->getParameter('txtState' . $suffix);
            $clues['divisionId'] = $request->getParameter('txtSubDivisionId' . $suffix);
            $clues['empName'] = $request->getParameter('txtEmpName' . $suffix);
            $clues['empId'] = empty($clues['empName']) ? 0 : $request->getParameter('hdnEmpId' . $suffix);
            $clues['reviewerName'] = $request->getParameter('txtReviewerName' . $suffix);
            $clues['reviewerId'] = empty($clues['reviewerName']) ? 0 : $request->getParameter('hdnReviewerId' . $suffix);
            $clues['pageNo'] = $request->getParameter('hdnPageNo' . $suffix);
            $clues['projectName'] = $request->getParameter('txtProjectName' . $suffix);
            $clues['customerId'] = empty($clues['projectName']) ? 0 : $request->getParameter('hdnCustomerId' . $suffix);
            $clues['directReview']   = $request->getParameter('directReview'. $suffix);
            $clues['Order'] = $request->getParameter('txtSortOrder'. $suffix);
            $clues['sortBy'] = $request->getParameter('txtSortField'. $suffix);
        } elseif ($request instanceof PerformanceReview) {
            $clues['from'] = $request->getPeriodFrom();
            $clues['to'] = $request->getPeriodTo();
            $clues['due'] = $request->getDueDate();
            $clues['jobCode'] = $request->getJobTitleCode();
            $clues['divisionId'] = $request->getSubDivisionId();
            $clues['empName'] = $request->getEmployee()->getFirstName() . " " . $request->getEmployee()->getLastName();
            $clues['empId'] = $request->getEmployeeId();
            $clues['reviewerName'] = $request->getReviewer()->getFirstName() . " " . $request->getReviewer()->getLastName();
            $clues['reviewerId'] = $request->getReviewerId();
            $clues['id'] = $request->getId();
            $clues['state'] = $request->getState();
        }

        return $clues;
    }

    protected function _isCorrectEmployee($id, $name) {
        $flag = true;
        if ((!empty($name) && $id == 0)) {
            $flag = false;
        }
        if (!empty($name) && !empty($id)) {
            $this->employeeService = $this->getEmployeeService();
            $employee = $this->employeeService->getEmployee($id);

            if (strcmp(strtolower(trim($employee->getFirstName()) . ' ' . trim($employee->getLastName())), strtolower($name))) {
                $flag = false;
            }
        }
        return $flag;
    }
}