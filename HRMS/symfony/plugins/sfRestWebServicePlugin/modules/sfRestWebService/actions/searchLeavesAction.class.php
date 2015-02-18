<?php

/**
 * Accepts Json Object as post parameter
 *
 * @package    api
 * @subpackage Action
 * @author     Mayur Kathale
 */
class searchLeavesAction extends BaseActionRest {
	private $leaveRequestService;
	
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
	
	public function execute($request) {
		set_time_limit(0);
    	$logger = Logger::getLogger('searchLeaves');
    	$json_data = file_get_contents('php://input'); //Retrieve JSON document containing parameters.
    	$headers = apache_request_headers();
    	$logger->error(' Authorization : '.md5(utf8_encode($headers["Authorization"])));
    	        $searchParams = new ParameterObject(array(
    			'dateRange' => new DateRange('2014-04-01', '2015-03-31'),
    			'statuses' => array(1,2,3,0),
    			'leaveTypeId' => 1,
    			'employeeFilter' => $employeeFilter,
    			'noOfRecordsPerPage' => sfConfig::get('app_items_per_page'),
    			'cmbWithTerminated' => null,
    			'subUnit' => null,
    			'employeeName' => null
    	));
    	$leaveList = $this->getLeaveRequestService()->searchLeaveRequests($searchParams,1,true);
    	$jsonResponseArray = array();
    	foreach($leaveList as $leave) {
    		$leaveObject = new LeaveRequestJSON();
    		$leaveObject->setDateRange($leave->getLeaveDateRange());
    		$leaveObject->setAction($this->getLeaveRequestService()->getLeaveRequestActions($leave, 1));
    		$leaveObject->setComment($leave->getComments());
    		$leaveObject->setEmployeeName($leave->getEmployee()->getFirstAndLastNames());
    		$leaveObject->setLeaveBalance($leave->getLeaveBalance());
    		$leaveObject->setNoOfDays($leave->getNumberOfDays());
    		$leaveObject->setStatus($leave->getLeaveBreakdown());
    		$leaveObject->setLeaveType($leave->getLeaveType()->getDescriptiveLeaveTypeName());
    		array_push($jsonResponseArray, $leaveObject);
    	}
    	$response = $this->getResponse();
    	$response->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
    	$response->setHttpHeader('Expires', '0');
    	$response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
    	$response->setHttpHeader("Cache-Control", "private", false);
     	return $this->renderText(json_encode($jsonResponseArray,JSON_FORCE_OBJECT)); // convert PHP array to JSON object and return it to requested client.
	}
}