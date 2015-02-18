<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */

/**
 * LeaveCompOffService service
 */
class LeaveCompOffService extends BaseService {
	
	private $leaveConfigService;
	private $leaveEntitlementDao;
	private $leaveEntitlementStrategy;    
	private $leavePeriodService;
	private $leaveCompOffDao;
	private $accessFlowStateMachineService;    
	private $employeeService;
	
	
	public function getLeaveCompoffDao() {
		if (!($this->leaveCompOffDao instanceof LeaveCompOffDao)) {
			$this->leaveCompOffDao = new LeaveCompOffDao();
		}
		return $this->leaveCompOffDao;
	}
	
	public function setLeaveCompOffDao(LeaveCompOffDao $leaveCompOffDao) {
		$this->leaveCompOffDao = $leaveCompOffDao;
	}
	
	
	public function saveLeaveCompOff($empNumber, $noOfDays,$comment) {
		return $this->getLeaveCompoffDao()->saveLeaveCompOff($empNumber, $noOfDays,$comment);
	}
	
	/**
	 *
	 * @param ParameterObject $searchParameters
	 * @param array $statuses
	 * @return array
	 */
	public function searchCompOffLeaves($searchParameters, $page = 1) {
		$result = $this->getLeaveCompoffDao()->searchCompOffLeaves($searchParameters, $page);
		return $result;
	}
	
	public function getLeaveCompOffActions($request, $loggedInEmpNumber) {
		$actions = array();
		$includeRoles = array();
		$excludeRoles = array();
		
		$empNumber = $request->getEmpNumber();
		
		// If looking at own leave request, only consider ESS role
		if ($empNumber == $loggedInEmpNumber) {
			$includeRoles = array('ESS');
		}            
	
		$status = Leave::getTextForLeaveStatus($request->getStatus());
		
		if($status != "SCHEDULED")
		$workFlowItems = $this->getUserRoleManager()->getAllowedActions(WorkflowStateMachine::FLOW_LEAVE, 
			$status, $excludeRoles, $includeRoles, array('Employee' => $empNumber));
		
		foreach ($workFlowItems as $item) {
			$name = $item->getAction();
			if($empNumber == $loggedInEmpNumber){
				$actions[$item->getId()] = ucfirst(strtolower($name));
			}else{
				if($name != 'CANCEL'){
					$actions[$item->getId()] = ucfirst(strtolower($name));	
				}
			}
		}
		return $actions;
	}
	
	/**
	 * Get User role manager instance
	 * @return AbstractUserRoleManager
	 */
	public function getUserRoleManager() {
		if (!($this->userRoleManager instanceof AbstractUserRoleManager)) {
			$this->userRoleManager = UserRoleManagerFactory::getUserRoleManager();
		}
		return $this->userRoleManager;
	}
	
	 public function getAccessFlowStateMachineService() {
        if (is_null($this->accessFlowStateMachineService)) {
            $this->accessFlowStateMachineService = new AccessFlowStateMachineService();
        }
        return $this->accessFlowStateMachineService;
    }

    public function setAccessFlowStateMachineService($accessFlowStateMachineService) {
        $this->accessFlowStateMachineService = $accessFlowStateMachineService;
    }
	
	/**
	 * Change leave compOff status
	 * @param array $changes
	 * @param string $changeType
	 * @return boolean
	 */
	public function changeLeaveCompOffStatus($changedActions) {
		if (is_array($changedActions)) {
			$workflowService = $this->getAccessFlowStateMachineService();
			foreach ($changedActions as $changedCompOff => $workFlowId) {
				$workFlow = $workflowService->getWorkflowItem($workFlowId);
				$nextStateStr = $workFlow->getResultingState();                    
				$nextState = Leave::getLeaveStatusForText($nextStateStr);
				// update leave compOff
				$changedLeaveCompOff = $this->getLeaveCompOffDao()->updateLeaveCompOff($nextState, $changedCompOff);
				
				if(!is_null($changedLeaveCompOff)){
					$leaveCompOffMailer = new LeaveCompOffMailer();
					$leaveCompOffMailer->send($_SESSION['empNumber'],$nextState,$changedLeaveCompOff);
				}
			}
		}else {
			throw new LeaveServiceException('Empty changes list');
		}
	}
	
	protected function _changeLeaveCompOffStatus($changedLeaveCompOff, $newState) {
		$dao = $this->getLeaveCompOffDao();
		$changedLeaveCompOff->setStatus($newState);
		$dao->updateLeaveCompOff($changedLeaveCompOff);
	}
	
	/**
     * Returns the logged in employee
     * @return Employee
     * @todo Remove the use of session
     */
    public function getLoggedInEmployee() {
        $employee = $this->getEmployeeService()->getEmployee($_SESSION['empNumber']);
        return $employee;
    }
	
	  /**
     *
     * @return EmployeeService
     */
    public function getEmployeeService() {
        if (!($this->employeeService instanceof EmployeeService)) {
            $this->employeeService = new EmployeeService();
        }
        return $this->employeeService;
    }
}
