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
 */

/**
 * Get leave balance for given employee for given leave type
 *
 */
class getLeaveBalanceAjaxAction extends sfAction {

    protected $leaveEntitlementService;
    protected $workScheduleService;
    protected $leaveApplicationService;        
    protected $leaveTypeService;
    
    protected function getLeaveTypeService() {
    	if (!($this->leaveTypeService instanceof LeaveTypeService)) {
    		$this->leaveTypeService = new LeaveTypeService();
    	}
    	return $this->leaveTypeService;
    }
    
    protected function setLeaveTypeService(LeaveTypeService $service) {
    	$this->leaveTypeService = $service;
    }
    
    /**
     * Get leave balance for given leave type
     * Request parameters:
     * *) leaveType: Leave Type ID
     * *) empNumber: (optional) employee number. If not present, currently
     *               logged in employee is used.
     * 
     * @param sfWebRequest $request
     */
    public function execute($request) {
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);

        $leaveTypeId = $request->getParameter('leaveType');
        /*modified by sujata to trim leading zeros*/
        $empNumber = ltrim($request->getParameter('empNumber'), '0');

        $user = $this->getUser();
        $loggedEmpNumber = $user->getAttribute('auth.empNumber');

        $allowed = false;

        if (empty($empNumber)) {
            $empNumber = $loggedEmpNumber;
            $allowed = true;
        } else {

            $manager = $this->getContext()->getUserRoleManager();
            if ($manager->isEntityAccessible('Employee', $empNumber)) {
                $allowed = true;
            } else {
                $allowed = ($loggedEmpNumber == $empNumber);
            }
        }

        $response = $this->getResponse();
        $response->setHttpHeader('Expires', '0');
        $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
        $response->setHttpHeader("Cache-Control", "private", false);
        
        $balance = '--';
        
        if ($allowed) {
            $localizationService = new LocalizationService();
            $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
            $startDate = $localizationService->convertPHPFormatDateToISOFormatDate($inputDatePattern, $request->getParameter("startDate"));

            $startDateTimeStamp = strtotime($startDate);
            
            // If not start date, show balance as of today
            if (!$startDateTimeStamp) {
                $startDate = date('Y-m-d');
            }
            
            $endDate = $localizationService->convertPHPFormatDateToISOFormatDate($inputDatePattern, $request->getParameter("endDate"));

            $endDateTimeStamp = strtotime($endDate);
            
            $leaveByPeriods = array();            
            
            if ($endDateTimeStamp && ($endDateTimeStamp > $startDateTimeStamp)) {
                
                $leaveParameterObject = $this->getLeaveParameterObject($empNumber, $leaveTypeId, $startDate, $endDate);
                $leaveDays =$this->getLeaveApplicationService()->createLeaveObjectListForAppliedRange($leaveParameterObject);
                
                $holidays = array(Leave::LEAVE_STATUS_LEAVE_WEEKEND, Leave::LEAVE_STATUS_LEAVE_HOLIDAY);

                $currentLeavePeriod = $this->getLeavePeriod($startDate, $empNumber, $leaveTypeId);
                $leavePeriodNdx = 0;
                $leaveByPeriods[$leavePeriodNdx] = array(
                    'period' => $currentLeavePeriod,
                    'balance' => false,
                    'days' => array()
                );
                
                foreach ($leaveDays as $k => $leave) {
                    
                    $leaveDate = $leave->getDate();
                    
                    // Get next leave period if request spans leave periods.
                    if (strtotime($leaveDate) > strtotime($leaveByPeriods[$leavePeriodNdx]['period'][1])) {
                        $currentLeavePeriod = $this->getLeavePeriod($leaveDate, $empNumber, $leaveTypeId);
                        $leavePeriodNdx++;
                        $leaveByPeriods[$leavePeriodNdx] = array(
                            'period' => $currentLeavePeriod,
                            'balance' => false,
                            'days' => array()
                        );                        
                    }
                    
                    $localizedDate = set_datepicker_date_format($leaveDate);
                    if (in_array($leave->getStatus(), $holidays)) {                        
                        $leaveByPeriods[$leavePeriodNdx]['days'][$localizedDate] = array('length' => 0, 'balance' => false, 
                            'desc' => ucfirst(strtolower($leave->getTextLeaveStatus())));
                    } else {
                        $leaveByPeriods[$leavePeriodNdx]['days'][$localizedDate] = array('length' => 1, 'balance' => false, 'desc' => '');
                    }
                }                 
            }            
            
            // If request spans leave periods
            $negativeBalance = false;
            
            if (count($leaveByPeriods) > 0) {
                foreach ($leaveByPeriods as $i => $leavePeriod) {
                    
                    $days = $leavePeriod['days'];
                    if (count($days) > 0) {
                        $firstDayInPeriod = $localizationService->convertPHPFormatDateToISOFormatDate(array_shift(array_keys($days)));
                        $lastDayInPeriod = $localizationService->convertPHPFormatDateToISOFormatDate(array_pop(array_keys($days)));
                    } else {
                        $firstDayInPeriod = $leavePeriod['period'][0];
                        $lastDayInPeriod = $leavePeriod['period'][1];                        
                    }
                    $leaveBalanceObj = $this->getLeaveEntitlementService()->getLeaveBalance($empNumber, $leaveTypeId, 
                            $firstDayInPeriod, $lastDayInPeriod);
                    
                    $leaveByPeriods[$i]['balance'] = $leaveBalanceObj;
                    
                    $leaveBalance = $leaveBalanceObj->getBalance();
                    
                    foreach ($days as $date => $leaveDateData) {
                        $leaveDateLength = $leaveDateData['length'];
                        if ($leaveDateLength > 0) {
                            $leaveBalance -= $leaveDateLength;
                            $leaveByPeriods[$i]['days'][$date]['balance'] = $leaveBalance;
                        }
                    }
                    
                    if ($leaveBalance < 0) {
                        $negativeBalance = true;
                    }  
                    
                    // localize data
                    $leaveByPeriods[$i]['period'][0] = set_datepicker_date_format($leaveByPeriods[$i]['period'][0]);
                    $leaveByPeriods[$i]['period'][1] = set_datepicker_date_format($leaveByPeriods[$i]['period'][1]);
                }

                if($leaveTypeId == 2){
                	$this->getWFHBalanceForMonth($leaveByPeriods[0]['balance'],null,$empNumber);
                }
                
                $result = array(
                    'multiperiod' => true,
                    'negative' => $negativeBalance,
                    'data' => $leaveByPeriods
                );
            }
             
            if (count($leaveByPeriods) == 0 || (count($leaveByPeriods) == 1 && !$negativeBalance)) {
                
                $endDateParam = !empty($endDateTimeStamp) ? $endDate : NULL;
                $balance = $this->getLeaveEntitlementService()->getLeaveBalance($empNumber, $leaveTypeId, $startDate, $endDateParam);
                if($leaveTypeId == LeaveType::LEAVE_TYPE_WFH_ID){
                   $this->getWFHBalanceForMonth($balance,null,$empNumber);
                }
                
                $asAtDate = set_datepicker_date_format($startDate);

                $result = array(
                    'multiperiod' => false,
                    'balance' => $balance,
                    'asAtDate' => $asAtDate
                );                
            }
            
            
            echo json_encode($result);
        }

        return sfView::NONE;
    }
    
    /**
     * @param array $formValues
     * @return LeaveParameterObject
     */
    protected function getLeaveParameterObject($empNumber, $leaveTypeId, $fromDate, $toDate) {
        
        $formValues = array();
        
        $formValues['txtEmpID'] = $empNumber;
        $formValues['txtFromDate'] = $fromDate;
        $formValues['txtToDate'] = $toDate;        
        $formValues['txtLeaveType'] = $leaveTypeId;
        
        $workSchedule = $this->getWorkScheduleService()->getWorkSchedule($empNumber);        
        $formValues['txtEmpWorkShift'] = $workSchedule->getWorkShiftLength();   
        
        return new LeaveParameterObject($formValues);
    }
    
    protected function getLeavePeriod($date, $empNumber, $leaveTypeId) {
        
        $strategy = $this->getLeaveEntitlementService()->getLeaveEntitlementStrategy();
        
        return $strategy->getLeavePeriod($date, $empNumber, $leaveTypeId);
    }

    /**
     * @return LeaveEntitlementService
     */
    public function getLeaveEntitlementService() {
        if (is_null($this->leaveEntitlementService)) {
            $this->leaveEntitlementService = new LeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }

    /**
     *
     * @param LeaveEntitlementService $leaveEntitlementService
     */
    public function setLeaveEntitlementService(LeaveEntitlementService $leaveEntitlementService) {
        $this->leaveEntitlementService = $leaveEntitlementService;
    }
    
    /**
     * Get work schedule service
     * @return WorkScheduleService
     */
    public function getWorkScheduleService() {
        if (!($this->workScheduleService instanceof WorkScheduleService)) {
            $this->workScheduleService = new WorkScheduleService();
        }
        return $this->workScheduleService;
    }

    /**
     *
     * @param WorkScheduleService $service 
     */
    public function setWorkScheduleService(WorkScheduleService $service) {
        $this->workScheduleService = $service;
    }      

    /**
     * Get leave application service instance
     * 
     * @return LeaveApplicationService
     */
    public function getLeaveApplicationService() {
        if (!($this->leaveApplicationService instanceof LeaveApplicationService)) {
            $this->leaveApplicationService = new LeaveApplicationService();
        }
        return $this->leaveApplicationService;
    }

    /**
     * Set leave application service instance
     * @param LeaveApplicationService $service 
     */
    public function setLeaveApplicationService(LeaveApplicationService $service) {
        $this->leaveApplicationService = $service;
    }        

    private function getWFHBalanceForMonth($leaveBalance,$month,$empNumber){
    	//$expected value for month -> [1 - 12 (*Jan - Dec)]
    	if($month == null){
    		$month = date("m");//get current month
    	}
    	$first_day_this_month = date('Y-'.$month.'-01'); // hard-coded '01' for first day of month Y-m-d
    	$last_day_this_month  = date('Y-'.$month.'-t');  // hard-coded 't' for last day of month Y-m-d
    	$wfhLeaveBalanceCalculator = new WfhLeaveBalanceCalculator();
    	$leaveBalance->WFHBalForMonth = $wfhLeaveBalanceCalculator->getWfhLeaveBalance($empNumber,array($first_day_this_month,$last_day_this_month));
    }
}

