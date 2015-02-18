<?php
//getEmployeeJoiningDateAjaxAction.class.php
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
/* Added by sujata in oder to get Employee Joining date */
/**
 * Description of getEmployeeJoiningDateUrlAjaxAction
 */
class getEmployeeJoiningDateAjaxAction  extends sfAction {

    protected $entitlementService ;

    public function getEntitlementService() {
        if (empty($this->entitlementService)) {
            $this->entitlementService = new LeaveEntitlementService();
        }
        return $this->entitlementService;
    }

    public function setEntitlementService($entitlementService) {
        $this->entitlementService = $entitlementService;
    }

    protected function getEmployeeJoiningDate($empNumber) {

        $employeeJoiningDate = $this->getEntitlementService()->searchEmployeeJoiningDate( $empNumber );

        if(count($employeeJoiningDate) > 0){
            $joiningDate = $employeeJoiningDate->getFirst();
            // echo "<pre>"; print_r($joiningDate); echo "</pre>";
            $jd = $joiningDate->getJoinedDate();
        }
        return $jd;

    }

    public function execute($request) {
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);

        $employees = $this->getEmployeeJoiningDate($request->getGetParameters());

        $response = $this->getResponse();
        $response->setHttpHeader('Expires', '0');
        $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
        $response->setHttpHeader("Cache-Control", "private", false);

        //echo "<pre>"; print_r($employees); echo "</pre>";
        return $this->renderText(json_encode($employees))  ;
    }
}
