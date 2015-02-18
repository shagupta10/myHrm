<?php

class viewCompOffListAction extends sfAction {

    protected $leavePeriodService;
    protected $employeeService;
    protected $leaveCompOffService;
     protected $leaveRequestService;
    protected $requestedMode;
      
    /**
     * Returns Leave Period
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {

        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }

        return $this->leavePeriodService;
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
     *
     * @return LeaveRequestService
     */
    public function getLeaveCompOffService() {
        if (is_null($this->leaveCompOffService)) {
            $leaveCompOffService = new LeaveCompOffService();
            $leaveCompOffService->setLeaveCompOffDao(new LeaveCompOffDao());
            $this->leaveCompOffService = $leaveCompOffService;
        }
        return $this->leaveCompOffService;
    }

    /**
     *
     * @param LeaveRequestService $leaveRequestService
     * @return void
     */
    public function setLeaveCompOffService(LeaveCompOffService $leaveCompOffService) {
        $this->leaveCompOffService = $leaveCompOffService;
    }

    protected function getMode() {
        $mode = CompOffLeaveListForm::MODE_COMPOFF_LIST;
        
        return $mode;
    }

    protected function isEssMode() {
         $userMode = 'ESS';
         
        if ($_SESSION['isSupervisor']) {
            $userMode = 'Supervisor';
        }

        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
            $userMode = 'Admin';
        }
        
        return ($userMode == 'ESS');
    }

    public function execute($request) {        
        
        $this->mode = $mode = $this->getMode();
        $this->essMode = $this->isEssMode();
        $this->form = $this->getComOffLeaveListForm($mode);
        
        $values = array();
        $page = $request->getParameter('hdnAction') == 'search' ? 1 : $request->getParameter('pageNo', 1);
        
        // Check for parametes sent from direct links
        // (PIM: 'txtEmpID' will be available as a get parameter)
        // (Leave Summary Links: leavePeriodId, leaveTypeId and status)
        $empNumber = $request->getParameter('empNumber');
        
        $fromDateParam = $request->getParameter('fromDate');
        $toDateParam = $request->getParameter('toDate');
        $leaveTypeId = $request->getParameter('leaveTypeId');
        $leaveStatusId = $request->getParameter('status');
        $stdDate = $request->getParameter('stddate');
        $now = time();
        // If leave period defined, use leave period start and end date
        $leavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d', $now));
        if (!empty($leavePeriod)) {
        	$values['date']['from'] = $leavePeriod[0];
        	$values['date']['to'] = $leavePeriod[1];
        } else {
        	// else use this year as the period
        	$year = date('Y', $now);
        	$values['date']['from'] = $year . '-1-1';
        	$values['date']['to'] = $year . '-12-31';
        }
        if ($request->isMethod('post')) {
        	
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $values = $this->form->getValues();
                $this->_setFilters($mode, $values);
            } else {
                if ($this->form->hasErrors()) {
                    echo $this->form->renderGlobalErrors();
                    foreach ($this->form->getWidgetSchema()->getPositions() as $widgetName) {
                        echo $widgetName . '--[' . $this->form[$widgetName]->renderError() . "]<br/>";
                    }
                }
            }
        } else if ($request->hasParameter('reset')) {
              $values = $this->form->getDefaults();
              $this->_setFilters($mode, $values);                            
        }
        
       // $subunitId = $this->_getFilterValue($values, 'cmbSubunit', null);
        $statuses = $this->_getFilterValue($values, 'chkSearchFilter', array());
        
        $terminatedEmp = $this->_getFilterValue($values, 'cmbWithTerminated', null);
        $fromDate = $this->_getFilterValue($values['date'], 'from', null);
        $toDate = $this->_getFilterValue($values['date'], 'to', null);
        $empData = $this->_getFilterValue($values, 'txtEmployee', null);
        $employeeName = $empData['empName'];
        $message = $this->getUser()->getFlash('message', '');
        $messageType = $this->getUser()->getFlash('messageType', '');

        $employeeFilter = $this->getEmployeeFilter($mode, $empNumber);

        $searchParams = new ParameterObject(array(
                    'dateRange' => new DateRange($fromDate, $toDate),
                    'statuses' => $statuses,
                    'employeeFilter' => $employeeFilter,
                    'noOfRecordsPerPage' => sfConfig::get('app_items_per_page'),
                    'cmbWithTerminated' => $terminatedEmp,
                    'employeeName' => $employeeName
                ));
        
        $result = $this->searchLeaveCompOffs($searchParams, $page);
        
        $list = $result['list'];
        $recordCount = $result['meta']['record_count'];

        if ($recordCount == 0 && $request->isMethod("post")) {
            $message = __('No Records Found');
            $messageType = 'notice';
        }

        $list = empty($list) ? null : $list;
        $this->form->setList($list);
        //$this->form->setEmployeeList($this->getEmployeeList());
        
        $this->message = $message;
        $this->messageType = $messageType;
        $this->baseUrl = $mode == CompOffLeaveListForm::MODE_MY_COMPOFF_LIST ? 'leave/viewMyCompOffList' : 'leave/viewCompOffList';

        $this->_setPage($mode, $page);
        
        $this->setListComponent($list, $recordCount, $page);
        
        $balanceRequest = array();
        
//        foreach ($list as $row) {
//            $dates = $row->getLeaveStartAndEndDate();
//            $balanceRequest[] = array($row->getEmpNumber(), $row->getLeaveTypeId(), $dates[0], $dates[1]);
//        }
//        
//        $this->balanceQueryData = json_encode($balanceRequest);

        $this->setTemplate('viewCompOffList');
    }
    
    
    /**
     *
     * @return LeaveRequestService
     */
    public function getLeaveRequestService() {
        if (is_null($this->leaveRequestService)) {
            $leaveRequestService = new LeaveRequestService();
            $leaveRequestService->setLeaveRequestDao(new LeaveRequestDao());
            $this->leaveRequestService = $leaveRequestService;
        }

        return $this->leaveRequestService;
    }

    /**
     *
     * @param LeaveRequestService $leaveRequestService
     * @return void
     */
    public function setLeaveRequestService(LeaveRequestService $leaveRequestService) {
        $this->leaveRequestService = $leaveRequestService;
    }
    protected function searchLeaveCompOffs($searchParams, $page) {
        $result = $this->getLeaveCompOffService()->searchCompOffLeaves($searchParams, $page);
        //$result = $this->getLeaveRequestService()->searchLeaveRequests($searchParams, $page, false, false, true, true);
        return $result;
    }
    
    protected function setListComponent($leaveList, $count, $page) {
        ohrmListComponent::setConfigurationFactory($this->getLeaveCompOffListConfigurationFactory());
        ohrmListComponent::setActivePlugin('orangehrmLeavePlugin');
        ohrmListComponent::setListData($leaveList);
        ohrmListComponent::setItemsPerPage(sfConfig::get('app_items_per_page'));
        ohrmListComponent::setNumberOfRecords($count);      
        ohrmListComponent::setPageNumber($page);
    }
    
    protected function getLeaveCompOffListConfigurationFactory() {
        $loggedInEmpNumber = $this->getUser()->getAttribute('auth.empNumber');
        LeaveCompOffListConfigurationFactory::setListMode($this->mode);
        LeaveCompOffListConfigurationFactory::setLoggedInEmpNumber($loggedInEmpNumber);
        $configurationFactory = new LeaveCompOffListConfigurationFactory();
        
        return $configurationFactory;
    }

    protected function getComOffLeaveListForm($mode) {
        $this->form = new CompOffLeaveListForm($mode);
        return $this->form;
    }
    
    /**
     * Get employee number search filter
     * 
     * @param string $mode Leave list mode.
     * @param int $empNumber employee number
     * @return mixed Array of employee numbers or an employee number.
     */
    protected function getEmployeeFilter($mode, $empNumber) {
        
        $loggedInEmpNumber = $this->getUser()->getAttribute('auth.empNumber');
        
        // default filter to null. Will fetch all employees
        $employeeFilter = null;
            
        if ($mode == CompOffLeaveListForm::MODE_MY_COMPOFF_LIST) {
            $employeeFilter = $loggedInEmpNumber;
        } else {
            $manager = $this->getContext()->getUserRoleManager();
            $requiredPermissions = array(
                BasicUserRoleManager::PERMISSION_TYPE_ACTION => array('view_leave_list'));
                
            $accessibleEmpIds = $manager->getAccessibleEntityIds('Employee', null, null, array(), array(), $requiredPermissions);

            if (empty($empNumber)) {
                $employeeFilter = $accessibleEmpIds;
            } else {
                if (in_array($empNumber, $accessibleEmpIds)) {
                    $employeeFilter = $empNumber;
                } else {
                    // Requested employee is not accessible. 
                    $employeeFilter = array();
                }           
            }                
        }
        
        return $employeeFilter;
    }

    protected function getEmployeeList() {

        $employeeService = new EmployeeService();
        $employeeList = array();

        if (Auth::instance()->hasRole(Auth::ADMIN_ROLE)) {
            $properties = array("empNumber","firstName", "middleName", "lastName", 'termination_id');
            $employeeList = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityProperties('Employee', $properties);
        }

        if ($_SESSION['isSupervisor'] && trim(Auth::instance()->getEmployeeNumber()) != "") {
            $employeeList = $employeeService->getSubordinateList(Auth::instance()->getEmployeeNumber());
        }

        return $employeeList;
        
    }
    
    /**
     * Set's the current page number in the user session.
     * @param $page int Page Number
     * @return None
     */
    protected function _setPage($mode, $page) {
        $this->getUser()->setAttribute($mode . '.page', $page, 'compOff_leave_list');
    }

    /**
     * Get the current page number from the user session.
     * @return int Page number
     */
    protected function _getPage($mode) {
        return $this->getUser()->getAttribute($mode . '.page', 1, 'compOff_leave_list');
    }

    /**
     * Remember filter values in session.
     * 
     * Dates are expected in standard date format (yy-dd-mm, 2012-21-02).
     * 
     * @param mode Leave list mode. One of (LeaveListForm::MODE_ADMIN_LIST,
     *                                      LeaveListForm::MODE_MY_LEAVE_LIST)                            
     * @param array $filters Filters
     * @return unknown_type
     */
    protected function _setFilters($mode, array $filters) {
        return $this->getUser()->setAttribute($mode . '.filters', $filters, 'compOff_leave_list');
    }

    /**
     *
     * @return unknown_type
     */
    protected function _getFilters($mode) {
        return $this->getUser()->getAttribute($mode . '.filters', null, 'compOff_leave_list');
    }

    protected function _getFilterValue($filters, $parameter, $default = null) {
        $value = $default;
        if (isset($filters[$parameter])) {
            $value = $filters[$parameter];
        }

        return $value;
    }

    protected function _isRequestFromLeaveSummary($request) {

        $txtEmpID = $request->getGetParameter('txtEmpID');

        if (!empty($txtEmpID)) {
            return true;
        }

        return false;
    }
    
    protected function _getStandardDate($localizedDate) {
        $localizationService = new LocalizationService();
        $format = sfContext::getInstance()->getUser()->getDateFormat();
        $trimmedValue = trim($localizedDate);
        $result = $localizationService->convertPHPFormatDateToISOFormatDate($format, $trimmedValue);   
        return $result;
    }

}