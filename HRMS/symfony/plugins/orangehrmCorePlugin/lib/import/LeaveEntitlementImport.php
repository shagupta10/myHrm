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
class LeaveEntitlementImport extends CsvDataImport {
	
	private $employeeService;
	private $leaveEntitlementService;
	
	//private $logger;
	
	public function import($data) {
		
		set_time_limit(90);
		if ($data[0] == "" || strlen($data[0]) > 30) {
			return false;
		}
		
		$empService = $this->getEmployeeService();
		$employee = $empService->getEmployeeByEmployeeId($data[0]);
		$logger = Logger::getLogger('import.LeaveEntitlementImport: No Leave balance for following employee');
		if(!empty($employee)){
			if ($data[2] != "") {
				$leaveType = $data[3];
				switch ($leaveType) {
					case 1://paid leave
						$this->createLeaveEntitlement($data, $employee);
						break;
					case 2: //wfh
						$this->createLeaveEntitlement($data, $employee);
						break;
					case 3://compoff
						$this->createLeaveEntitlement($data, $employee);
						break;
					case 4://marriage leave
						if(!$employee->isMarried()){
							$this->createLeaveEntitlement($data, $employee);	
						}
						break;
					case 5://paternity leave
						if($employee->isMarried() && ($employee->getEmpGender() == 1)){
							$this->createLeaveEntitlement($data, $employee);
						}
						break;
					case 6://maternity leave
						if($employee->isMarried()  && ($employee->getEmpGender() == 2)){
							$this->createLeaveEntitlement($data, $employee);
						}
						break;
					case 7://unpaid leave
						$this->createLeaveEntitlement($data, $employee);
						break;
				}
			}
		}else{
			$logger->error($data[1]);
		}
		return true;
	}
	
	private function createLeaveEntitlement($data, $employee){
		$leaveEntitlement = new LeaveEntitlement(); 
		$leaveEntitlement->setNoOfDays($data[2]);
		$leaveEntitlement->setEmpNumber($employee->getEmpNumber());
		$leaveEntitlement->setLeaveTypeId($data[3]);
		$user = sfContext::getInstance()->getUser();//->getEmployeeNumber();
		$userId = $user->getAttribute('auth.userId');
		$createdBy = $user->getAttribute('auth.firstName');
		$leaveEntitlement->setCreditedDate(date('Y-m-d'));
		$leaveEntitlement->setCreatedById($userId);
		$leaveEntitlement->setCreatedByName($createdBy);        
		$leaveEntitlement->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
		$leaveEntitlement->setDeleted(0);
		$leaveEntitlement->setNoOfDays(round($leaveEntitlement->getNoOfDays(), 2));
		
		$fromDateTime = new DateTime('2013-04-01');
		$from_date=date_format ( $fromDateTime, 'Y-m-d' );
		$leaveEntitlement->setFromDate($from_date);
		
		$toDateTime = new DateTime('2014-03-31');
		$to_date=date_format ( $toDateTime, 'Y-m-d' );
		$leaveEntitlement->setToDate($to_date);
		
		$this->getLeaveEntitlementService()->saveLeaveEntitlement($leaveEntitlement);
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
