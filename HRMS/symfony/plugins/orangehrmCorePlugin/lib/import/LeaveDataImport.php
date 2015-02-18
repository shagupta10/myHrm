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
class LeaveDataImport extends CsvDataImport {
	
	private $employeeService;
	private $leaveEntitlementService;
	private $leaveApplicationService;
	private $leaveTypeService;
	
	//private $logger;
	
	public function import($data) {
		$logger = Logger::getLogger('import.LeaveDataImport');
		$logger->error('Importing leave data of : ' . $data[0]);
		set_time_limit(180);
		if ($data[0] == "" || strlen($data[0]) > 30) {
			return false;
		}
		
		$empService = $this->getEmployeeService();
		$employee = $empService->getEmployeeByEmployeeId($data[0]);
		
		if(!empty($employee)){
			$leaveParameters = $this->getLeaveParameterObject($data,$employee);
			
			//$this->getLeaveApplicationService()->applyLeave($leaveParameters);
			$this->getLeaveAssignmentService()->assignLeave($leaveParameters);
		}else{
			$logger->error('No employee record found for : ' . $data[0]);
		}
		return true;
	}
	
	private function getLeaveParameterObject($data ,$employee){
		$dataArray = array();	
		$dataArray['txtEmpID'] =  $employee->getEmpNumber();
		$dataArray['txtFromDate'] = $this->formatDate($data[1]);
		$dataArray['txtToDate'] = $this->formatDate($data[2]);
		
		// $dataArray['txtFromTime'];
		// $dataArray['txtToTime'];
		$leaveType = $this->getLeaveTypeService()->readLeaveTypeByName($data[4]);
		$dataArray['txtLeaveType'] = $leaveType->getId();
		if($data[3] == 1){
			$dataArray['txtLeaveTotalTime'] = 8;
		}else{
			$dataArray['txtLeaveTotalTime'] = 4;
		}
		
		
		$dataArray['txtComment'] = $data[6];
		$dataArray['txtEmpWorkShift'] = 8;
		
		return new LeaveParameterObject($dataArray);
	}
	
	/**
	 *
	 * @return LeaveTypeService
	 */
	public function getLeaveTypeService() {
		if (!($this->leaveTypeService instanceof LeaveTypeService)) {
			$this->leaveTypeService = new LeaveTypeService();
		}
		return $this->leaveTypeService;
	}
	
	/**
	 * Get leave assignment service instance
	 * 
	 * @return LeaveAssignmentService
	 */
	public function getLeaveAssignmentService() {
		if (!($this->leaveAssignmentService instanceof LeaveAssignmentService)) {
			$this->leaveAssignmentService = new LeaveAssignmentService();
		}
		return $this->leaveAssignmentService;
	}
	
	/**
	 * Format date to YYYY-MM-dd
	 */
	private function formatDate($date){
		try{
			$dateTime = new DateTime($date);
			$formatted_date=date_format ( $dateTime, 'Y-m-d' );
			return $formatted_date;	
		} catch (Exception $e) {
			$logger = Logger::getLogger('import.LeaveDataImport');
			$logger->error('Leave import Data issue: ' . $e);
		}
	}
	
	/**
	 *
	 * @return LeaveApplicationService
	 */
	public function getLeaveApplicationService() {
		if (!($this->leaveApplicationService instanceof LeaveApplicationService)) {
			$this->leaveApplicationService = new LeaveApplicationService();
		}
		return $this->leaveApplicationService;
	}
	
	public function getEmployeeService() {
		if (is_null($this->employeeService)) {
			$this->employeeService = new EmployeeService();
			$this->employeeService->setEmployeeDao(new EmployeeDao());
		}
		return $this->employeeService;
	}
	
	public function getLeaveEntitlementService() {
		if (empty($this->leaveEntitlementService)) {
			$this->leaveEntitlementService = new LeaveEntitlementService();
		}
		return $this->leaveEntitlementService;
	}
	
}

?>
