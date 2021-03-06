<?php
/**
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

abstract class orangehrmPerformanceMailer extends orangehrmMailer {

    protected $performer; // Type of Employee
    protected $performerType; // 'admin', 'supervisor' or 'ess'
    protected $recipient; // Type of Employee
    protected $employee; 
  
    protected $employeeService; // Type of EmployeeService
   
    private $systemUserService;

    public function getPerformer() {
        return $this->performer;
    }

    public function setPerformer($performer) {
        $this->performer = $performer;
    }

    public function getPerformerType() {
        return $this->performerType;
    }

    public function setPerformerType($performerType) {
        $this->performerType = $performerType;
    }

    public function getRecipient() {
        return $this->recipient;
    }

    public function setRecipient($recipient) {
        $this->recipient = $recipient;
    }

    public function getEmployeeService() {
    	if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }
    
    public function getSystemUserService() {
    	if (is_null($this->systemUserService)) {
             $this->systemUserService = new SystemUserService();
            $this->systemUserService->setSystemUserDao(new SystemUserDao());
        }
        return $this->systemUserService;
    }
    
    public function setEmployeeService($employeeService) {
        $this->employeeService = $employeeService;
    }

}
