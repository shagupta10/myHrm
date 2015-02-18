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
 * Leave CompOff Dao class
 */
class LeaveCompOffDao extends BaseDao {
	
	protected $leaveEntitlementStrategy;
	protected $leaveEntitlementService;
	protected $leavePeriodService;
	protected $leaveTypeService;
	protected $logger;
	
	/**
	 * Get Logger instance. Creates if not already created.
	 *
	 * @return Logger
	 */
	protected function getLogger() {
		if (is_null($this->logger)) {
			$this->logger = Logger::getLogger('dao.LeaveCompOffDao');
		}
		return($this->logger);
	}
	
	public function getLeaveEntitlementService() {
		if (empty($this->leaveEntitlementService)) {
			$this->leaveEntitlementService = new LeaveEntitlementService();
		}
		return $this->leaveEntitlementService;
	}
	
	public function setLeaveEntitlementService($leaveEntitlementService) {
		$this->leaveEntitlementService = $leaveEntitlementService;
	}    
	
	public function getLeavePeriodService() {
		if (is_null($this->leavePeriodService)) {
			$leavePeriodService = new LeavePeriodService();
			$leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
			$this->leavePeriodService = $leavePeriodService;
		}
		return $this->leavePeriodService;
	}
	
	/**
	 * @return LeaveTypeService
	 */
	public function getLeaveTypeService() {
		if(is_null($this->leaveTypeService)) {
			$this->leaveTypeService = new LeaveTypeService();
		}
		return $this->leaveTypeService;
	}
	
	public function saveLeaveCompOff($empNumber, $noOfDays,$comment) {
		$conn = Doctrine_Manager::connection();
		$conn->beginTransaction();
		try {
			$leaveCompOff = new LeaveCompOff();
			$leaveCompOff->setNumberOfDays($noOfDays);
			$leaveCompOff->setEmpNumber($empNumber);
			$leaveCompOff->setCompoffDetails($comment);
			$leaveCompOff->setStatus(Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL);
			$leaveCompOff->setCreatedDate(date('Y-m-d H:i:s'));
			$leaveCompOff->setUpdatedDate(date('Y-m-d H:i:s'));
			$leaveCompOff->save();
			$conn->commit();
			return $leaveCompOff;
		} catch (Exception $e) {
			$conn->rollback();
			throw new DaoException($e->getMessage(), 0, $e);
		}
	}
	
	public function updateLeaveCompOff($nextState, $id) {
		$conn = Doctrine_Manager::connection();
		$conn->beginTransaction();
		try {
			$q = Doctrine_Query:: create()->update('LeaveCompOff')
				->set('status', '?', $nextState)
				->set('updated_date', '?', date('Y-m-d H:i:s'))
				->where('id = ?', $id);
			$q->execute();
			
			$leaveCompOff = $this->fetchLeaveCompOff($id);
			
			if(Leave::LEAVE_STATUS_LEAVE_APPROVED == $nextState){
				// Add to Entitlement
				$calenderYear = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d', time()));
				$fromDate = $calenderYear[0];
				$toDate = $calenderYear[1];
				
				$empNumber =  $leaveCompOff->getEmpNumber();
				
				$leaveType = $this->getLeaveTypeService()->readLeaveTypeByName(LeaveType::LEAVE_TYPE_COFF);
				$leaveTypeId = $leaveType->getId();
				
				//$this->getLogger()->error("FromDate : ". set_datepicker_date_format($fromDate). " toDate ". set_datepicker_date_format($toDate). "Leave ". $leaveCompOff1->getEmpNumber() ." type ". $leaveType->getId());
				
				$entitlementList = 
					$this->getLeaveEntitlementService()
					->getMatchingEntitlements($leaveCompOff->getEmpNumber(), $leaveType->getId(), $fromDate, $toDate);    
				
				if(count($entitlementList) > 0){
					$leaveEntitlement = $entitlementList->getFirst();
					
					$newValue = $leaveEntitlement->getNoOfDays()+$leaveCompOff->getNumberOfDays();
					$leaveEntitlement->setNoOfDays($newValue);
				}else{
					$leaveEntitlement = new LeaveEntitlement(); 
					$leaveEntitlement->setNoOfDays($leaveCompOff->getNumberOfDays());
				}
				
				$leaveEntitlement->setEmpNumber($empNumber);
				$leaveEntitlement->setLeaveTypeId($leaveTypeId);
				$user = sfContext::getInstance()->getUser();//->getEmployeeNumber();
				$userId = $user->getAttribute('auth.userId');
				$createdBy = $user->getAttribute('auth.firstName');
				
				$leaveEntitlement->setCreditedDate(date('Y-m-d'));
				$leaveEntitlement->setCreatedById($userId);
				$leaveEntitlement->setCreatedByName($createdBy);        
				
				$leaveEntitlement->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
				$leaveEntitlement->setDeleted(0);            
				
				$leaveEntitlement->setNoOfDays(round($leaveEntitlement->getNoOfDays(), 2));
				
				$leaveEntitlement->setFromDate($fromDate);
				$leaveEntitlement->setToDate($toDate);
				$leaveEntitlement = $this->getLeaveEntitlementService()->saveLeaveEntitlement($leaveEntitlement);
			}
			
			$conn->commit();
			return $leaveCompOff;
		} catch (Exception $e) {
			$conn->rollback();
			$this->getLogger()->error($e->getMessage());
			throw new DaoException($e->getMessage(), 0, $e);
		}
	}
	
	/**
	 * Return leaveCompoff by ID
	 */
	public function fetchLeaveCompOff($leaveCompOffId){
		$q = Doctrine_Query::create()
			->from('LeaveCompOff lc')
			->where('lc.id = ?',$leaveCompOffId);
		return $q->fetchOne();	
	}
	
	/**
	 * Search CompOff Leaves.
	 * 
	 * Valid Search Parameter values
	 *    * 'noOfRecordsPerPage' (int) - Number of records per page. If not available, 
	 *                                   sfConfig::get('app_items_per_page') will be used.
	 *    * 'dateRange' (DateRange)    -
	 *    * 'statuses' (array)
	 *    * 'employeeFilter' (array)   - Filter by given employees. If an empty array(), does not match any employees.
	 *    * 'cmbWithTerminated'
	 *    * 'employeeName' (string)    - Match employee name (Wildcard match against full name).
	 * 
	 * @param ParameterObject $searchParameters Search Parameters
	 * @param int $page $status Page Number
	 * @param bool $isCSVPDFExport If true, returns all results (ignores paging) as an array
	 * @param bool $isMyLeaveList If true, ignores setting to skip terminated employees.
	 * @param bool $prefetchComments If true, will prefetch leave comments for faster access.
	 * 
	 * @return array Returns results and record count in the following format:
	 *               array('list' => results, 'meta' => array('record_count' => count)
	 * 
	 *               If $isCSVPDFExport is true, returns just an array of results.
	 */
	public function searchCompOffLeaves($searchParameters, $page = 1, $isMyLeaveList = false) {
		$limit = !is_null($searchParameters->getParameter('noOfRecordsPerPage')) ? $searchParameters->getParameter('noOfRecordsPerPage') : sfConfig::get('app_items_per_page');
		$offset = ($page > 0) ? (($page - 1) * $limit) : 0;
		
		$list = array();
		
		$q = Doctrine_Query::create()
			->select('lc.*, emp.firstName, emp.lastName, emp.middleName, emp.termination_id ')
			->from('LeaveCompOff lc')
			->leftJoin('lc.Employee emp');
		
		
		$dateRange = $searchParameters->getParameter('dateRange', new DateRange());
		$statuses = $searchParameters->getParameter('statuses');
		$employeeFilter = $searchParameters->getParameter('employeeFilter');
		$includeTerminatedEmployees = $searchParameters->getParameter('cmbWithTerminated');
		$employeeName = $searchParameters->getParameter('employeeName');
		
		$fromDate = $dateRange->getFromDate();
		$toDate = $dateRange->getToDate();
		
		if (!empty($fromDate)) {
			$q->andWhere("lc.created_date >= ?",$fromDate);
		}
		
		if (!empty($toDate)) {
			$q->andWhere("lc.created_date <= ?",$toDate);
		}
		
		if (!empty($statuses)) {
			$q->whereIn("lc.status", $statuses);
		}
		
		if (!empty($employeeFilter)) {
			if (is_numeric($employeeFilter) && $employeeFilter > 0) {
				$q->andWhere('lc.emp_number = ?', (int) $employeeFilter);
			} elseif ($employeeFilter instanceof Employee) {
				$q->andWhere('lc.emp_number = ?', $employeeFilter->getEmpNumber());
			} elseif (is_array($employeeFilter)) {
				$empNumbers = array();
				foreach ($employeeFilter as $employee) {
					$empNumbers[] = ($employee instanceof Employee) ? $employee->getEmpNumber() : $employee;
				}
				
				// Here, ->whereIn() is very slow when employee number count is very high (around 5000).
				// this seems to be due to the time taken by Doctrine to replace the 5000 question marks in the query.
				// Therefore, replaced with manually built IN clause.
				// Note: $empNumbers is not based on user input and therefore is safe to use in the query.
				$q->andWhere('lc.emp_number IN (' . implode(',', $empNumbers) . ')');
			}
		} else {
			// empty array does not match any results.
			if (is_array($employeeFilter)) {
				$q->andWhere('lc.emp_number = ?', -1);
			}
		}
		
		//        if ($isMyLeaveList) {
		//            $includeTerminatedEmployees = true;
		//        }
		
		// Search by employee name
		if (!empty($employeeName)) {
			$employeeName = str_replace(' (' . __('Past Employee') . ')', '', $employeeName);
			// Replace multiple spaces in string with wildcards
			$employeeName = preg_replace('!\s+!', '%', $employeeName);
			
			// Surround with wildcard character
			$employeeName = '%' . $employeeName . '%';
			
			$q->andWhere('CONCAT_WS(\' \', emp.emp_firstname, emp.emp_middle_name, emp.emp_lastname) LIKE ?', $employeeName);
		}
		
		
		if (empty($includeTerminatedEmployees)) {
			$q->andWhere("emp.termination_id IS NULL");
		}
		
		$count = $q->count();
		
		$q->orderBy('lc.created_date DESC, emp.emp_lastname ASC, emp.emp_firstname ASC');        
		
		$q->offset($offset);
		$q->limit($limit);
		
		$list = $q->execute();
		
		return array('list' => $list, 'meta' => array('record_count' => $count));
	}
}
