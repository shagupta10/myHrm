<?php

/*
  // OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
  // all the essential functionalities required for any enterprise.
  // Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com

  // OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
  // the GNU General Public License as published by the Free Software Foundation; either
  // version 2 of the License, or (at your option) any later version.

  // OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
  // without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
  // See the GNU General Public License for more details.

  // You should have received a copy of the GNU General Public License along with this program;
  // if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
  // Boston, MA  02110-1301, USA
 */

/**
 * Form class for employee contact detail
 */
class EmployeeChangeProjectForm extends BaseForm {

    public $fullName;   
    private $projectService;
    private $customerService;
    

   
    
    public function getProjectService() {
		if (is_null($this->projectService)) {
			$this->projectService = new ProjectService();
			$this->projectService->setProjectDao(new ProjectDao());
		}
		return $this->projectService;
	}
    
	public function getCustomerService(){
		if (is_null($this->customerService)) {
			$this->customerService = new CustomerService();
			$this->customerService->setCustomerDao(new CustomerDao());
		}
		return $this->customerService;
	}
    

    public function configure() {

        $empNumber = $this->getOption('empNumber');
        $employee = $this->getOption('employee');
        $this->fullName = $employee->getFullName();        

        $empService = new EmployeeService();

       
        // Note: Widget names were kept from old non-symfony version
        $this->setWidgets(array(
            'emp_number' => new sfWidgetFormInputHidden(),
            'customerName' => new sfWidgetFormInputText(),
            'project'=> new sfWidgetFormInput(),
            
        ));

        // Default values
        $this->setDefault('emp_number', $empNumber);    
        

        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');

    
        $this->setValidators(array(
            'emp_number' => new sfValidatorString(array('required' => true)),        
            'customerName' => new sfValidatorString(array('required' => false, 'max_length' => 52, 'trim'=>true)),
			'project'=> new sfValidatorString(array('required' => false)),
			
        ));


        $this->widgetSchema->setNameFormat('job[%s]');
    }

    public function postValidate($validator, $values) {       
            $error = new sfValidatorError($validator, $message);
            throw new sfValidatorErrorSchema($validator, array('' => $error));
       
    }

    /**
     * Get Employee object with values filled using form values
     */
    public function getEmployee() {

        $employeeService = new EmployeeService();
        $employee = $employeeService->getEmployee($this->getValue('emp_number'));     

        return $employee;
    }

   
   
    /**
     * Get the Project list
     * @return <type>
     */
    public function getProjectListAsJson() {
	    $jsonArray = array();
	 	$projects = $this->getProjectService()->getAllProjects(true);
	    foreach ($projects as $project) {
		    $jsonArray[] = array('name' => $project->getName(), 'id' => $project->getProjectId());
	    }
	    $jsonString = json_encode($jsonArray);
	    return $jsonString;
    }
    
     /**
     * Get the Project list
     * @return <type>
     */
    public function getCustomerListAsJson() {
	    $jsonArray = array();
	 	$customers = $this->getCustomerService()->getAllCustomers(true);
	    foreach ($customers as $customer) {
		    $jsonArray[] = array('name' => $customer->getName(), 'id' => $customer->getCustomerId());
	    }
	    $jsonString = json_encode($jsonArray);
	    return $jsonString;
    }
    
    /**
     * Get the existing employee customer list
     */
    public function getExistingEmpCustomerListAsJson(){
    	$jsonArray = array();
	    $empService = new EmployeeService();
	    $existingCustomer = $empService->getEmployeeCustomer($this->getOption('empNumber'));
	    foreach ($existingCustomer as $empCustomer) {
		    $jsonArray[] = array('name' => $empCustomer->getCustomer()->getName(), 'id' => $empCustomer->getCustomerId());
	    }
	    $jsonString = json_encode($jsonArray);
	    return $jsonString;
    }
    
    /**
     *
     * @return <type>
     */
    public function getExistingEmpProjectListAsJson() {
	    $jsonArray = array();
	    $empService = new EmployeeService();
	    $existingProjects = $empService->getEmployeeProjects($this->getOption('empNumber'));
	    foreach ($existingProjects as $empProject) {
         $customer=$this->getCustomerService()->getCustomerById($empProject->getCustomerId());
         $customerName=  substr($customer->getName(),0,9);
         if(strlen($customer->getName())>9)  
           $customerName=  ''.$customerName.'<a href="#" title="'.$customer->getName().'">...</a>';
         $jsonArray[] = array('name' => $empProject->getProject()->getName()." [".$customerName."]", 'id' => $empProject->getProjectId());
	    }
	    $jsonString = json_encode($jsonArray);
	    return $jsonString;
    }    
    
    

}

