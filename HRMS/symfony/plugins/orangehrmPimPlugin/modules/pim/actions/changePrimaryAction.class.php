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
 * Actions class for PIM module updateMembership
 */
class changePrimaryAction extends basePimAction {

    private $reportingMethodConfigurationService;
    private $employeeService;
    const DIRECT_REPORTY=1;

    /** shagupta
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
     public function execute($request) {

        $supervisor = $request->getParameter('optPrime');      
        $empNumber = (isset($memberships['empNumber'])) ? $memberships['empNumber'] : $request->getParameter('empNumber');
        /* Shagupta */      
        $performanceObj=new PerformanceEvents();
        $returnFlag=$performanceObj->setPrimaryReviwerOnReportyChange($empNumber,$supervisor,self::DIRECT_REPORTY); 
        if($returnFlag){
        	$this->getUser()->setFlash('primary.success', __(TopLevelMessages::PRIMARY_REVIEWER_SUCCESS));
        }else{
        	$this->getUser()->setFlash('primary.warning', __(TopLevelMessages::PRIMARY_REVIEWER_FAILURE));
        }
        $this->redirect('pim/changeProject?empNumber=' . $empNumber);
    }
    
}