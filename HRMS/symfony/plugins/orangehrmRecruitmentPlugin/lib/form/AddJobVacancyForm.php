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
class AddJobVacancyForm extends BaseForm {

    private $vacancyService;
    private $vacancyId;
    private $jobTitleService;
    private $employeeService;
    private $systemUserService;
    public  $numberOfHiringManagers = 10;
    public  $consultantsPrepopulated;
    public  $projectsPrepopulated;
    private $customerService;
    private $experience;
    private $goodToHaveSkills;
    private $keySkills;
        
    public function getCustomerService(){
    	if (is_null($this->customerService)) {
    		$this->customerService = new CustomerService();
    		$this->customerService->setCustomerDao(new CustomerDao());
    	}
    	return $this->customerService;
    }

    public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
            $this->jobTitleService->setJobTitleDao(new JobTitleDao());
        }
        return $this->jobTitleService;
    }

    /**
     * Get VacancyService
     * @returns VacncyService
     */
    public function getVacancyService() {
        if (is_null($this->vacancyService)) {
            $this->vacancyService = new VacancyService();
            $this->vacancyService->setVacancyDao(new VacancyDao());
        }
        return $this->vacancyService;
    }
    
    public function getEmployeeService(){
    	if (is_null($this->employeeService)) {
            $this->employeeService = new employeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }
    
    public function getSystemUserService(){
    	if (is_null($this->systemUserService)) {
    		$this->systemUserService = new SystemUserService();
    		$this->systemUserService->setSystemUserDao(new SystemUserDao());
    	}
    	return $this->systemUserService;
    }

    /**
     * Set VacancyService
     * @param VacancyService $vacancyService
     */
    public function setVacancyService(VacancyService $vacancyService) {
        $this->vacancyService = $vacancyService;
    }

    /**
     *
     */
    public function configure() {

        $jobTitleList = $this->getJobTitleList();

        $this->vacancyId = $this->getOption('vacancyId');
        if (isset($this->vacancyId)) {
            $vacancy = $this->getVacancyDetails($this->vacancyId);
        }

        //creating widgets
        $this->setWidgets(array(
            'jobTitle' => new sfWidgetFormSelect(array('choices' => $jobTitleList)),
            'name' => new sfWidgetFormInputText(),
        
            'noOfPositions' => new sfWidgetFormInputText(),
            'description' => new sfWidgetFormTextArea(),
        	'experience'=>new sfWidgetFormInputText(),
        	'keySkills'=>new sfWidgetFormTextArea(),
        	'goodToHaveSkills'=>new sfWidgetFormTextArea(),
            'status' => new sfWidgetFormInputCheckbox(array(), array('value' => 'on')),
            'flagResume' => new sfWidgetFormInputCheckbox(array(), array('value' => 'on')),
            'publishedInFeed' => new sfWidgetFormInputCheckbox(array(), array('value' => 'on')),
        	'selectedHiringManagerList' => new sfWidgetFormInputHidden(),
        	'consultants' => new sfWidgetFormInput(),
        	'urgent' => new sfWidgetFormInputCheckbox(array(), array()),
        	'projects' => new sfWidgetFormInput(),
        ));

            for ($i = 1; $i <= $this->numberOfHiringManagers; $i++) {
            $this->setWidget('hiringManager_' . $i, new sfWidgetFormInputText());
        	}
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

        //Setting validators
        $this->setValidators(array(
            'jobTitle' => new sfValidatorString(array('required' => true)),
            'name' => new sfValidatorString(array('required' => true)),
        
            'noOfPositions' => new sfValidatorInteger(array('required' => false, 'min' => 0)),
            'description' => new sfValidatorString(array('required' => false, 'max_length' => 41000)),
        	'experience'=>new sfValidatorString(array('required' => true, 'max_length' => 30)),
        	'keySkills'=>new sfValidatorString(array('required' => true)),
        	'goodToHaveSkills'=>new sfValidatorString(array('required' => true)),
            'status' => new sfValidatorString(array('required' => false)),
            'flagResume' => new sfValidatorString(array('required' => false)),
            'publishedInFeed' => new sfValidatorString(array('required' => false)),
         	'selectedHiringManagerList' => new sfValidatorString(array('required' => false)),
        	'consultants' => new sfValidatorString(array('required' => false)),
        	'urgent' => new sfValidatorString(array('required' => false)),
        	'projects' => new sfValidatorString(array('required' => false))
        ));
        
            for ($i = 1; $i <= $this->numberOfHiringManagers; $i++) {
            $this->setValidator('hiringManager_' . $i, new sfValidatorString(array('required' => false, 'max_length' => 100)));
       		 }
  
        $this->widgetSchema->setNameFormat('addJobVacancy[%s]');
        if (isset($vacancy) && $vacancy != null) {
            $this->setDefault('jobTitle', $vacancy->getJobTitleCode());
            $this->setDefault('name', $vacancy->getName());
          
            $this->setDefault('noOfPositions', $vacancy->getNoOfPositions());
            $this->setDefault('description', $vacancy->getDescription());
            $this->setDefault('experience', $vacancy->getExperience());
            $this->setDefault('keySkills', $vacancy->getKeySkills());
            $this->setDefault('goodToHaveSkills', $vacancy->getGoodToHaveSkills());
            if ($vacancy->getStatus() == JobVacancy::ACTIVE) {
                $this->setDefault('status', $vacancy->getStatus());
            }
            if ($vacancy->getPublishedInFeed() == JobVacancy::PUBLISHED) {
                $this->setDefault('publishedInFeed', $vacancy->getStatus());
            }
            if ($vacancy->flagForResume == JobVacancy::SHOW_MICRORESUME) {
            	$this->setDefault('flagResume', $vacancy->flagForResume);
            }
            if ($vacancy->isUrgent == JobVacancy::IS_URGENT_VACANCY) {
            	$this->setDefault('urgent', $vacancy->isUrgent);
            }
            
            $consultants = $vacancy->consultants;
            $consultantsList = explode(",", $consultants); // array of Consultant Ids
            $consultantArray = array();
            if($consultantsList[0]!="") {
	            foreach ($consultantsList as $consultant) {
	            	$emp = $this->getEmployeeService()->getEmployee($consultant);
	    			$consultantArray[] = array( 'name' => $emp->getFirstAndLastNames() , 'id' => $emp->getEmpNumber());
	    		}
            }
    		$this->consultantsPrepopulated = json_encode($consultantArray);
    		
    		$projects = $vacancy->projects;
    		$projectsList = explode(",", $projects);
    		$projectsArray = array();
    		if($projectsList[0]!="") {
    			foreach ($projectsList as $projectId) {
    				$project = $this->getCustomerService()->getCustomerById($projectId);
    				$projectsArray[] = array( 'name' => $project->getName() , 'id' => $project->getCustomerId());
    			}
    		}
    		$this->projectsPrepopulated = json_encode($projectsArray);
        } else {
            $this->setDefault('status', JobVacancy::ACTIVE);
            $this->setDefault('publishedInFeed', JobVacancy::PUBLISHED);
            $this->setDefault('flagResume', JobVacancy::SHOW_MICRORESUME);
            $this->consultantsPrepopulated = json_encode(array());
            $this->projectsPrepopulated = json_encode(array());
        }

        if (isset($this->vacancyId) && $this->vacancyId != '') {
         	$jobVacancy = $this->getVacancyService()->getVacancyById($this->vacancyId);
            $existingHiringManagers = $jobVacancy->getJobVacancyHiringManager();
            $i = 1;
     		foreach ($existingHiringManagers as $existingHiringManager) {
     			if($i <= count($existingHiringManagers))
     			{      				  
                   $this->setDefault('hiringManager_' . $i, $existingHiringManager->getEmployee()->getFirstAndLastNames());  
                   $i++; 
     			}              
              }
              $this->setDefault('selectedHiringManagerList', count($existingHiringManagers));
        }
    }
    /**
     *
     */
    public function save() {

        if (empty($this->vacancyId)) {
            $jobVacancy = new JobVacancy();
            $jobVacancy->definedTime = date('Y-m-d H:i:s');
            $jobVacancy->updatedTime = date('Y-m-d H:i:s');
        } else {
            $jobVacancy = $this->getVacancyService()->getVacancyById($this->vacancyId);
            $jobVacancy->updatedTime = date('Y-m-d H:i:s');    
            $existingHiringManagers = $jobVacancy->getJobVacancyHiringManager();        
        }
    	

        $HiringManagerArray = $this->getValue('selectedHiringManagerList');
        $selectedHiringManagerArrayList = explode(",", $HiringManagerArray);
        $jobVacancy->jobTitleCode = $this->getValue('jobTitle');
        $jobVacancy->name = $this->getValue('name');
        $jobVacancy->noOfPositions = $this->getValue('noOfPositions');
        $jobVacancy->description = $this->getValue('description');
        $jobVacancy->experience = $this->getValue('experience');
        $jobVacancy->keySkills = $this->getValue('keySkills');
        $jobVacancy->goodToHaveSkills = $this->getValue('goodToHaveSkills');
        $jobVacancy->status = JobVacancy::CLOSED;
        $jobVacancy->flagForResume = JobVacancy::DONOT_SHOW_MICRORESUME;
        $jobVacancy->isUrgent = JobVacancy::IS_NOT_URGENT_VACANCY;
        $jobVacancy->consultants = $this->getValue('consultants');
        $jobVacancy->projects = $this->getValue('projects');
        $status = $this->getValue('status');
        $flag = $this->getValue('flagResume');
        $isUrgent = $this->getValue('urgent');
        if (!empty($status)) {
            $jobVacancy->status = JobVacancy::ACTIVE;
        }
        if (!empty($flag)) {
        	$jobVacancy->flagForResume = JobVacancy::SHOW_MICRORESUME;
        }
        if (!empty($isUrgent)) {
        	$jobVacancy->isUrgent = JobVacancy::IS_URGENT_VACANCY;
        }

        $publishInFeed = $this->getValue('publishedInFeed');
        $jobVacancy->publishedInFeed = JobVacancy::NOT_PUBLISHED;
        if (!empty($publishInFeed)) {
            $jobVacancy->publishedInFeed = JobVacancy::PUBLISHED;
        }

   			 $idList = array();
         	// if ($existingHiringManager[0]->getEmployee()->empNumber != "") {
                foreach ($existingHiringManagers as $existingHiringManager) {
                        $existingHiringManager->delete();
                }
           // }
            
        $selectedHiringManagerArrayList = array_diff($selectedHiringManagerArrayList, $idList);
            $newList = array();
            foreach ($selectedHiringManagerArrayList as $elements) {
                $newList[] = $elements;
            }

             $selectedHiringManagerArrayList = $newList;
	         $this->getVacancyService()->saveJobVacancy($jobVacancy);

       
        if (!empty($selectedHiringManagerArrayList)) {
        	for ($i = 0; $i < count($selectedHiringManagerArrayList); $i++) {
        		$newHringManager = new JobVacancyHiringManager();
        		$newHringManager->setVacancyId($jobVacancy->getId());
        		$newHringManager->setHiringManagerId($selectedHiringManagerArrayList[$i]);
        		$newHringManager->save();
        	}
        }
        return $jobVacancy->getId();
    }
    public function getEmployeeListAsJson() {

        $jsonArray = array();
        $employeeService = new EmployeeService();
        $employeeService->setEmployeeDao(new EmployeeDao());

        $properties = array("empNumber","firstName", "middleName", "lastName", 'termination_id');
        $employeeList = $employeeService->getEmployeePropertyList($properties, 'lastName', 'ASC', true);
        
        foreach ($employeeList as $employee) {
            $empNumber = $employee['empNumber'];
            //$name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'],' ') . ' ' . $employee['lastName']);
             $name = trim(trim($employee['firstName']) . ' ' . trim($employee['lastName']));
            
            $jsonArray[] = array('name' => $name, 'id' => $empNumber);
        }

        $jsonString = json_encode($jsonArray);

        return $jsonString;
    }
	public function getSecondHiringManagerFullName($empNumber){
		$employee = $this->getEmployeeService()->getEmployee($empNumber);
		if (!empty($employee)) {
			return $employee->getFirstAndLastNames();
		}else{
			return;
		}
	}
	
    /**
     * Returns Vacancy List
     * @return array
     */
    public function getVacancyList() {
        $list = array();
        $vacancyList = $this->getVacancyService()->getVacancyList();
        foreach ($vacancyList as $vacancy) {
            $list[] = array('id' => $vacancy->getId(), 'name' => $vacancy->getName());
        }
        return json_encode($list);
    }

    /**
     * Returns job Title List
     * @return array
     */
    private function getJobTitleList() {
       $jobTitleList = $this->getJobTitleService()->getJobTitleList();
        $list = array("" => "-- " . __('Select') . " --");
        foreach ($jobTitleList as $jobTitle) {
            $list[$jobTitle->getId()] = $jobTitle->getJobTitleName();
        }
        return $list;
    }
    
    
    /**
     *
     * @return <type>
     */
    public function getHiringManagerListAsJson() {

        $jsonArray = array();
        $properties = array("empNumber","firstName", "middleName", "lastName", "termination_id");
        $employeeList = $this->getEmployeeService()->getEmployeePropertyList($properties, 'lastName', 'ASC', true);

        foreach ($employeeList as $employee) {
            $empNumber = $employee['empNumber'];
            //$name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'],' ') . ' ' . $employee['lastName']);
            $name = trim(trim($employee['firstName']) . ' ' . trim($employee['lastName']));
        
            $jsonArray[] = array('name' => $name, 'id' => $empNumber);
        }
        $jsonString = json_encode($jsonArray);
        return $jsonString;
    }

    /**
     *
     * @param <type> $vacancyId
     * @return <type>
     */
    private function getVacancyDetails($vacancyId) {

        return $this->getVacancyService()->getVacancyById($vacancyId);
    }
    
    public function getVacancyValues()
    {
    	$vacancy = new JobVacancy();
        $status = $this->getValue('status');
        if (!empty($status)) {
            $vacancy->status = JobVacancy::ACTIVE;
        }
    	return $vacancy;
    }
    
    
    public function getAllConsultantsAsJson() {
    	$consultantList = $this->getSystemUserService()->getUsersByRoles(array(SystemUser::CONSULTANT_USER_ROLE_ID));
    	$consultantArray = array();
    	foreach ($consultantList as $consultant) {
    		$emp = $consultant->getEmployee();
    		$consultantArray[] = array( 'name' => $emp->getFirstAndLastNames() , 'id' => $emp->getEmpNumber());
    	}
    	return json_encode($consultantArray);
    }
    
    /* This method is used to retrieve existing consultants to populate in JavaScript token-input.
     */
    public function getPrepopulatedConsultants() {
    	return $this->consultantsPrepopulated;
    }
    
    /* This method is used to retrieve Project List in JavaScript. 
     */
    public function getProjectsListAsJson() {
    	return $this->getCustomerService()->getCustomerListAsJson();
    }
    
    /* This method is used to retrieve existing project to populate in JavaScript token-input.
     */
    public function getPrepopulatedProjects() {
    	return $this->projectsPrepopulated;
    }
}

