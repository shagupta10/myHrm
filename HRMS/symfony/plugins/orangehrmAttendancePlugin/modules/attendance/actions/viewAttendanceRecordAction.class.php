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
class viewAttendanceRecordAction extends sfAction {

    private $employeeService;
    private $attendanceService;

    public function getEmployeeService() {

        if (is_null($this->employeeService)) {

            $this->employeeService = new EmployeeService();
        }

        return $this->employeeService;
    }

    public function setEmployeeService(EmployeeService $employeeService) {

        $this->employeeService = $employeeService;
    }
    
	public function getSystemUserService(){
    	if (is_null($this->systemUserService)) {
    		$this->systemUserService = new SystemUserService();
    		$this->systemUserService->setSystemUserDao(new SystemUserDao());
    	}
    	return $this->systemUserService;
    }
    
	public function getAttendanceService() {
		if (is_null($this->attendanceService)) {
			$this->attendanceService = new AttendanceService();
		}
		return $this->attendanceService;
	}

    public function execute($request) {

        $this->userObj = $this->getContext()->getUser()->getAttribute('user');
        $accessibleMenus = $this->userObj->getAccessibleAttendanceSubMenus();
        $hasRight = false;
        $this->parmetersForListCompoment = array();
        $this->showEdit = false;

        foreach ($accessibleMenus as $menu) {
            if ($menu->getDisplayName() === __("Employee Records")) {
                $hasRight = true;
                break;
            }
        }

        if (!$hasRight) {
            return $this->renderText(__("You are not allowed to view this page") . "!");
        }

        $this->trigger = $request->getParameter('trigger');

        if ($this->trigger) {
            $this->showEdit = true;
        }

        $this->date = $request->getParameter('date');
        $this->employeeId = $request->getParameter('employeeId');
        $this->employeeService = $this->getEmployeeService();
        $values = array('date' => $this->date, 'employeeId' => $this->employeeId, 'trigger' => $this->trigger);
        $this->form = new AttendanceRecordSearchForm(array(), $values);
        $this->actionRecorder = "viewEmployee";
        $isPaging = $request->getParameter('pageNo');
      	$records_limit = $request->getPostParameter('attendance[recordsPerPageLimit]');
		$this->RecordsLimit = $records_limit;
        $pageNumber = $isPaging;  
        if(empty($pageNumber)) { $pageNumber = 1; }
   		if($records_limit){
        	$noOfRecords = $records_limit;
        }else{
        	$noOfRecords = 10;
        	$this->RecordsLimit = $noOfRecords;
        }
        $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;
        if (!$this->trigger || !empty($this->trigger)) {
        	$this->allowedToDelete = array();
            $this->allowedActions = array();
            $this->allowedActions['Delete'] = false;
            $this->allowedActions['Edit'] = false;
            $this->allowedActions['PunchIn'] = false;
            $this->allowedActions['PunchOut'] = false;
            $this->userObj = $this->getContext()->getUser()->getAttribute('user');
            $userId = $this->userObj->getUserId();
            $userEmployeeNumber = $this->userObj->getEmployeeNumber();
            $userRoleFactory = new UserRoleFactory();
            $this->decoratedUser = $decoratedUser = $userRoleFactory->decorateUserRole($userId, $this->employeeId, $userEmployeeNumber);
            $accessibleEmployees = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityIds('Employee');
        	array_push($accessibleEmployees, $_SESSION['empNumber']);
            $usrObj = $this->getUser()->getAttribute('user');
            if ($request->isMethod('post')) {
                $this->form->bind($request->getParameter('attendance'));
                if ($this->form->isValid()) {
                    $post = $this->form->getValues();
                    if (!$this->employeeId) {
                        $empData = $post['employeeName'];
                        $this->employeeId = $empData['empId'];
                    }
                    if (!$this->date) {
                        $this->date = $post['date'];
                    }

                    if ($this->employeeId) {
                        $this->showEdit = true;
                    }
                    if (!$this->employeeId) {
                    	if($usrObj->isAdmin()) {
                    		$records = $this->getAttendanceService()->getAttendanceRecordsByEmployeeAndDate($this->date, NULL, NULL, $noOfRecords, $offset);
                    		$count = $this->getAttendanceService()->getAttendanceRecordsByEmployeeAndDate($this->date, NULL, NULL, $noOfRecords, $offset, true);
                    	} else {
                    		$records = $this->getAttendanceService()->getAttendanceRecordsByEmployeeAndDate($this->date, NULL, $accessibleEmployees, $noOfRecords, $offset);
                    		$count = $this->getAttendanceService()->getAttendanceRecordsByEmployeeAndDate($this->date, NULL, $accessibleEmployees, $noOfRecords, $offset, true);
                    	}
                    } else {
                    	$records = $this->getAttendanceService()->getAttendanceRecordsByEmployeeAndDate($this->date, $this->employeeId, $accessibleEmployees, $noOfRecords, $offset);
                    	$count = $this->getAttendanceService()->getAttendanceRecordsByEmployeeAndDate($this->date, $this->employeeId, $accessibleEmployees, $noOfRecords, $offset, true);
                    }
                }
            } else {
            	$pageNumber = 1; 
            	$offset = 0;
            	$empid = null;
            	$yesterdayDate = date('Y-m-d', strtotime(date( "Y-m-d"). "-1 days"));
            	if($request->getParameter('employeeId')!=null) {
            		$empid = $request->getParameter('employeeId');
            	}
            	if($request->getParameter('date') != null) {
            		$yesterdayDate = $request->getParameter('date');
            	}
                $this->form->setDefault('date', set_datepicker_date_format(date("Y-m-d",strtotime($yesterdayDate))));
                if($usrObj->isAdmin()) {
                	$records = $this->getAttendanceService()->getAttendanceRecordsByEmployeeAndDate($yesterdayDate, $empid, NULL, $noOfRecords, $offset);
                	$count = $this->getAttendanceService()->getAttendanceRecordsByEmployeeAndDate($yesterdayDate, $empid, NULL, $noOfRecords, $offset, true);
                } else {
                    $records = $this->getAttendanceService()->getAttendanceRecordsByEmployeeAndDate($yesterdayDate, $empid, $accessibleEmployees, $noOfRecords, $offset);
                    $count = $this->getAttendanceService()->getAttendanceRecordsByEmployeeAndDate($yesterdayDate, $empid, $accessibleEmployees, $noOfRecords, $offset, true);
                }
            }          
                    $params = array();
                    $this->parmetersForListCompoment = $params;

                    $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_OUT_TIME, PluginWorkflowStateMachine::ATTENDANCE_ACTION_EDIT_PUNCH_IN_TIME);
                    $actionableStates = $decoratedUser->getActionableAttendanceStates($actions);

                    if ($records != null) {
                        if ($actionableStates != null) {
                            foreach ($actionableStates as $state) {
                                foreach ($records as $record) {
                                    if ($state == $record->getState()) {
                                        $this->allowedActions['Edit'] = true;
                                        break;
                                    }
                                }
                            }
                        }

                        $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_DELETE);
                        $actionableStates = $decoratedUser->getActionableAttendanceStates($actions);

                        if ($actionableStates != null) {
                            foreach ($actionableStates as $state) {
                                foreach ($records as $record) {
                                    if ($state == $record->getState()) {
                                        $this->allowedActions['Delete'] = true;
                                        break;
                                    }
                                }
                            }
                        }

                        foreach ($records as $record) {
                            $this->allowedToDelete[] = $this->allowedToPerformAction(WorkflowStateMachine::FLOW_ATTENDANCE, PluginWorkflowStateMachine::ATTENDANCE_ACTION_DELETE, $record->getState(), $decoratedUser);
                            $recArray[] = $record;
                        }
                    } else {
                        $attendanceRecord = null;
                    }

                    $actions = array(PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN, PluginWorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT);
                    $allowedActionsList = array();

                    $actionableStates = $decoratedUser->getActionableAttendanceStates($actions);

                    if ($actionableStates != null) {
                        if (!empty($recArray)) {
                            $lastRecordPunchOutTime = $recArray[count($records) - 1]->getPunchOutUserTime();
                            if (empty($lastRecordPunchOutTime)) {
                                $attendanceRecord = "";
                            } else {
                                $attendanceRecord = null;
                            }
                        }

                        foreach ($actionableStates as $actionableState) {

                            $allowedActionsArray = $decoratedUser->getAllowedActions(PluginWorkflowStateMachine::FLOW_ATTENDANCE, $actionableState);
                            if (!is_null($allowedActionsArray)) {

                                $allowedActionsList = array_unique(array_merge($allowedActionsArray, $allowedActionsList));
                            }
                        }

                        if ((is_null($attendanceRecord)) && (in_array(WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_IN, $allowedActionsList))) {

                            $this->allowedActions['PunchIn'] = true;
                        }
                        if ((!is_null($attendanceRecord)) && (in_array(WorkflowStateMachine::ATTENDANCE_ACTION_PROXY_PUNCH_OUT, $allowedActionsList))) {

                            $this->allowedActions['PunchOut'] = true;
                        }
                    }
                    if ($this->employeeId == '') {
                        $this->showEdit = FALSE;
                    }
                    $this->_setListComponent($records, $noOfRecords, $pageNumber, $count, $this->showEdit, $this->allowedActions);
        }
    }

    private function _setListComponent($records, $noOfRecords, $pageNumber, $count=null, $showEdit=null, $allowedActions=null) {
        $configurationFactory = new AttendanceRecordHeaderFactory();
        $notSelectable = array();
        foreach ($records as $record) {
            if (!$this->allowedToPerformAction(WorkflowStateMachine::FLOW_ATTENDANCE, PluginWorkflowStateMachine::ATTENDANCE_ACTION_DELETE, $record->getState(), $this->decoratedUser)) {
                $notSelectable[] = $record->getId();
            }
        }
        $buttons = array();
        if (isset($allowedActions)) {
            if (isset($showEdit) && $showEdit) {
                if ($allowedActions['Edit']) :
                    $buttons['Edit'] = array('label' => __('Edit'), 'type' => 'button',);
                endif;
                if ($allowedActions['PunchIn']) :
                    $buttons['PunchIn'] = array('label' => __('Add Attendance Records'), 'type' => 'button', 'class' => 'punch');
                endif;
                if ($allowedActions['PunchOut']) :
                    $buttons['PunchOut'] = array('label' => __('Add Attendance Records'), 'type' => 'button', 'class' => 'punch');
                endif;
            }
            if ($allowedActions['Delete']) :
                $buttons['Delete'] = array('label' => __('Delete'),
                        'type' => 'submit',
                        'data-toggle' => 'modal',
                        'data-target' => '#dialogBox',
                        'class' => 'delete');
            endif;
        }
        $configurationFactory->setRuntimeDefinitions(array(
            'buttons' => $buttons,
            'unselectableRowIds' => $notSelectable,
        ));

        ohrmListComponent::setActivePlugin('orangehrmAttendancePlugin');
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($records);
        ohrmListComponent::setPageNumber($pageNumber);
        ohrmListComponent::setItemsPerPage($noOfRecords);
        ohrmListComponent::setNumberOfRecords($count);
    }

    public function allowedToPerformAction($flow, $action, $state, $userObject) {
        $actionsArray = $userObject->getAllowedActions($flow, $state);

        if (in_array($action, $actionsArray)) {
            return true;
        } else {
            return false;
        }
    }

}

