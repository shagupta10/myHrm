<?php

/*
 *
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
 * Displaying add compOff UI and saving data
 */
class applyCompOffAction extends sfAction {
	
	protected $employeeService;
    //protected $leaveApplicationService;
    protected $leaveCompOffService;
	
	/**
	 * @param sfForm $form
	 * @return
	 */
	public function setForm(sfForm $form) {
		if (is_null($this->form)) {
			$this->form = $form;
		}
	}
	
	public function getForm() {
		return $this->form;
	}
	
	 /**
     *
     * @return LeaveApplicationService
     */
    public function getLeaveCompoffService() {
        if (!($this->leaveCompOffService instanceof LeaveCompOffService)) {
            $this->leaveCompOffService = new LeaveCompOffService();
        }
        return $this->leaveCompOffService;
    }

    /**
     *
     * @param LeaveApplicationService $service 
     */
    public function setLeaveCompoffService(LeaveCompOffService $service) {
        $this->leaveCompOffService = $service;
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

    /**
     *
     * @param EmployeeService $service 
     */
    public function setEmployeeService(EmployeeService $service) {
        $this->employeeService = $service;
    }
	
	
	public function execute($request) {
		
		$userObj = $this->getUser()->getAttribute('user');
		$param = array('empNumber' => $userObj->getEmployeeNumber(), 'isAdmin' => $userObj->isAdmin());
		$this->setForm(new ApplyCompOffForm(array(), $param, true));
		
		//this section is to save leave compOff
		if ($request->isMethod('post')) {
			$this->form->bind($request->getParameter($this->form->getName()));
			if ($this->form->isValid()) {
				try {
				 	$posts = $this->form->getValues();
					$leaveCompOff = $this->getLeaveCompoffService()->saveLeaveCompOff($userObj->getEmployeeNumber(),$posts['numberOfDays'],$posts['txtComment'] );
					if(!is_null($leaveCompOff)){
						$leaveCompOffMailer = new LeaveCompOffMailer();
						$leaveCompOffMailer->send($userObj->getEmployeeNumber(),Leave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL,$leaveCompOff);
					}
					$this->getUser()->setFlash('success', __('Successfully Submitted'));
				} catch (Exception $e) {
					$this->getUser()->setFlash('warning', __($e->getMessage()));
				}
			}
		}
	}
	
	/**
	 * deprecated
	 */	
	protected function getLeaveCompoff() {
        $posts = $this->form->getValues();
        $userObj = $this->getUser()->getAttribute('user');
        $leaveCompOff = new LeaveCompOff();
        $leaveCompOff->setNumberOfDays($posts['numberOfDays']);
        $leaveCompOff->setEmpNumber($userObj->getEmployeeNumber());
        $leaveCompOff->setCompoffDetails($posts['txtComment']);
         $leaveCompoff->setStatus(1);
        $leaveCompOff->setCreatedDate(date('Y-m-d H:i:s'));
        $leaveCompOff->setUpdatedDate(date('Y-m-d H:i:s'));
        return $leaveCompOff;
    }
	
}
