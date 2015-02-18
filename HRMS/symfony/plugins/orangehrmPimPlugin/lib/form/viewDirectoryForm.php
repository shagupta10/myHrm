<?php


/**
 * Form class for employee directory
 */
class viewDirectoryForm extends BaseForm {

    private $companyStructureService;
    private $jobService;
    private $jobTitleService;
    private $empStatusService;
    private $projectService;
    private $customerService;
    

    public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
            $this->jobTitleService->setJobTitleDao(new JobTitleDao());
        }
        return $this->jobTitleService;
    }
    
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
    	$locationList = $this->_getLocationList();

        $this->setWidgets(array(
            'employee_name' => new sfWidgetFormInputText(),
        	'id' => new sfWidgetFormInputText(),
        	'skills'=>  new sfWidgetFormSelect(array('choices' => $this->_getSkillList())),
        	//'membership' => new sfWidgetFormSelect(array('choices' => $this->_getMembershipList()))
        ));

        $this->_setJobTitleWidget();
        
        $this->setWidget('project_name', new sfWidgetFormInputText());
        $this->setValidator('project_name', new sfValidatorString(array('required' => false)));
        
        $this->setWidget('recordsPer_Page_Limit', new sfWidgetFormInputHidden(array(), array()));
        $this->setValidator('recordsPer_Page_Limit', new sfValidatorString(array('required' => false)));
        
        $this->setWidget('isSubmitted', new sfWidgetFormInputHidden(array(), array()));
        $this->setValidator('isSubmitted', new sfValidatorString(array('required' => false)));
        $this->setValidator('employee_name', new sfValidatorString(array('required' => false)));
       	$this->setValidator('id', new sfValidatorString(array('required' => false)));
       	$this->setValidator('skills', new sfValidatorString(array('required' => false)));
       //	$this->setValidator('membership', new sfValidatorString(array('required' => false)));
        
        $this->widgetSchema->setNameFormat('empDir[%s]');
        $this->getWidgetSchema()->setLabels($this->getFormLabels());
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

    private function _setJobTitleWidget() {

        $jobTitleList = $this->getJobTitleService()->getJobTitleList();
        $choices = array('0' => __('All'));

        foreach ($jobTitleList as $job) {
            $choices[$job->getId()] = $job->getJobTitleName();
        }

        $this->setWidget('job_title', new sfWidgetFormChoice(array('choices' => $choices)));
        $this->setValidator('job_title', new sfValidatorChoice(array('choices' => array_keys($choices))));
    }


    /**
     *
     * @return array
     */
    protected function getFormLabels() {

        $labels = array(
            'employee_name' => __('Employee Name'),
            'job_title' => __('Job Title'),
        	'id' => __('Employee ID'),
        	'skills' => __('Skills'),
        	//'membership' => __('Certification'),
        	'project_name' => __('Project Name'),
        );
        return $labels;
    }
    
    private function _getLocationList() {
    	$locationService = new LocationService();
    	$locationList = array('-1' => __('All'));
    	$locations = $locationService->getLocationList();
 
    	foreach ($locations as $location) {
    		$locationList[$location->id] = $location->name;
    	}
    
    	return ($locationList);
    }
    
    /**
     * Get the customer list
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
    
    public function getEmployeeListAsJson() {
	    $jsonArray = array();
	    $properties = array("empNumber","firstName", "middleName", "lastName",);
	    $empService = new EmployeeService();
	    $employeeList = $empService->getEmployeePropertyList($properties, 'lastName', 'ASC', true, true);
	    
	    foreach ($employeeList as $employee) {
		    $empNumber = $employee['empNumber'];
		    $name = trim(trim($employee['firstName']) . ' ' . trim($employee['lastName']));
		    
		    $jsonArray[] = array('name' => $name, 'id' => $empNumber);
	    }
	    $jsonString = json_encode($jsonArray);
	    return $jsonString;
    }
    
    private function _getSkillList() {
        $skillService = new SkillService();
        $skillList = $skillService->getSkillList();
        $list = array("" => "-- " . __('Select') . " --");

        foreach($skillList as $skill) {
            $list[$skill->getId()] = $skill->getName();
        }
        
        // Clear already used skill items
        foreach ($this->empSkillList as $empSkill) {
            if (isset($list[$empSkill->skillId])) {
                unset($list[$empSkill->skillId]);
            }
        }
        return $list;
    }
    
      /**
     * Returns Membership Type List
     * @return array
     */
    public function _getMembershipList() {
        $list = array("" => "-- " . __('Select') . " --");
        $membershipService = new MembershipService();
        $membershipList = $membershipService->getMembershipList();
        foreach ($membershipList as $membership) {
            $list[$membership->getId()] = $membership->getName();
        }
        return $list;
    }
    
    //$properties, $orderField, $orderBy, $excludeTerminatedEmployees = false, $excludeConsultant = false

}

