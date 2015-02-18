<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class reportyComponent extends sfComponent {
    
    private $employeeService, $performanceReviewService;

    public function getPerformanceReviewService() {
		if (is_null($this->performanceReviewService)) {
			$this->performanceReviewService = new PerformanceReviewService();
			$this->performanceReviewService->setPerformanceReviewDao(new PerformanceReviewDao());
		}
		return $this->performanceReviewService;
	}
    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    /**
     * @param sfForm $form
     * @return
     */
    public function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    public function execute($request) {

        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();

        $reportTo = $request->getParameter('reportto');
        $empNumber = (isset($membership['empNumber'])) ? $membership['empNumber'] : $request->getParameter('empNumber');
        $this->empNumber = $empNumber;

        $this->essUserMode = $this->isAllowedAdminOnlyActions($loggedInEmpNum, $empNumber);
        $this->essUserMode=true;
        $this->reportToPermissions = basePimAction::getDataGroupPermissions(array('supervisor','subordinates'), $empNumber);
        
        $this->reportToSupervisorPermission = basePimAction::getDataGroupPermissions('supervisor', $empNumber);

        $this->reportToSubordinatePermission = basePimAction::getDataGroupPermissions('subordinates', $empNumber);
       
        $adminMode = $this->getUser()->hasCredential(Auth::ADMIN_ROLE);
/*
        if (!$this->IsActionAccessible($empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }
*/
        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }

        $essMode = !$adminMode && !empty($loggedInEmpNum) && ($empNumber == $loggedInEmpNum);
        $param = array('empNumber' => $empNumber, 'ESS' => $essMode, 'reportToPermissions'=>$this->reportToPermissions);

        $this->setForm(new EmployeeReportToForm(array(), $param, true));

        $this->deleteSupForm = new EmployeeReportToSupervisorDeleteForm(array(), $param, true);
        $this->deleteSubForm = new EmployeeReportToSubordinateDeleteForm(array(), $param, true);
        $this->supDetails = $this->getEmployeeService()->getImmediateSupervisors($this->empNumber);
        $this->subDetails = $this->getEmployeeService()->getSubordinateListForEmployee($this->empNumber);
        $this->primaryReviewer=$this->getPerformanceReviewService()->getEmployeeCurrentCyclePrimaryReviwer($this->empNumber);
        //$this->primaryReviewer='34';
        $this->_setMessage();
    }
    
    protected function _setMessage() {
        $this->section = '';
        if ($this->getUser()->hasFlash('reportTo')) {
            $this->section = $this->getUser()->getFlash('reportTo');
        } 
    }
    function isAllowedAdminOnlyActions($loggedInEmpNumber, $empNumber) {

        if ($loggedInEmpNumber == $empNumber) {
            return false;
        }

        $userRoleManager = $this->getContext()->getUserRoleManager();   
        $excludeRoles = array('Supervisor');
        
        $accessible = $userRoleManager->isEntityAccessible('Employee', $empNumber, null, $excludeRoles);
        
        if ($accessible) {
            return true;
        }

        return false;

    }
}