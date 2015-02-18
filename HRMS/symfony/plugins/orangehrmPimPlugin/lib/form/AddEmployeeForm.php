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
class AddEmployeeForm extends sfForm {
	protected $leaveEntitlementService;
    private $employeeService;
    private $userService;
    private $widgets = array();
    public $createUserAccount = 0;
    private $jobTitleService;
    private $countryService;
	private $systemUserService;
	protected $leavePeriodService;
	protected $configService;
    
    public function getCountryService() {
    
    	if (is_null($this->countryService)) {
    		$countryService = new CountryService();
    		$this->countryService = $countryService;
    	}
    	return $this->countryService;
    }
    
    public function getConfigService() {
    
    	if (!$this->configService instanceof ConfigService) {
    		$this->configService = new ConfigService();
    	}
    
    	return $this->configService;
    }
    
    public function getSystemUserService() {
    
    	if (is_null($this->systemUserService)) {
    		$systemUserService = new SystemUserService();
    		$this->systemUserService = $systemUserService;
    	}
    	return $this->systemUserService;
    }
    
    public function getLeavePeriodService() {
    
    	if (is_null($this->leavePeriodService)) {
    		$leavePeriodService = new LeavePeriodService();
    		$leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
    		$this->leavePeriodService = $leavePeriodService;
    	}
    
    	return $this->leavePeriodService;
    }
   
    
    public function getJobTitleService() {
    	if (is_null($this->jobTitleService)) {
    		$this->jobTitleService = new JobTitleService();
    		$this->jobTitleService->setJobTitleDao(new JobTitleDao());
    	}
    	return $this->jobTitleService;
    }
    
    public function getLeaveEntitlementService() {
    	if (empty($this->leaveEntitlementService)) {
    		$this->leaveEntitlementService = new LeaveEntitlementService();
    	}
    	return $this->leaveEntitlementService;
    }
    
    public function setLeaveEntitlementService($leaveEntitlementService) {
    	$this->leaveEntitlementService = $leaveEntitlementService;
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

    private function getUserService() {

        if (is_null($this->userService)) {
            $this->userService = new SystemUserService();
        }

        return $this->userService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    public function configure() {
    	$this->currentUserId = sfContext::getInstance()->getUser()->getEmployeeNumber();
    	$jobtitleList = $this->getJobTitles();
    	$countryList = $this->getCountryList();
    	$employeeStatuses = $this->_getEmpStatuses();
        $status = array('Enabled' => __('Enabled'), 'Disabled' => __('Disabled'));
	/* 	if(! isset($_SESSION['employeeToAdd']))
		{ */
        	$idGenService = new IDGeneratorService();
        	$idGenService->setEntity(new Employee());
        	$empNumber = $idGenService->getNextID(false);
        	$employeeId = str_pad($empNumber, 4, '0');
        	$empNumber = intval($empNumber);
	/* 	}
		else
		{
			$emp = $_SESSION['employeeToAdd'];
			$empNumber = $emp->getEmpNumber();
		} */

        $this->widgets = array(
        	'employeeId' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 10)), 
            'firstName' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 100)),
            'middleName' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 100)),
            'lastName' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 100)),
            'photofile' => new sfWidgetFormInputFileEditable(array('edit_mode' => false, 'with_delete' => false, 
                'file_src' => ''), array("class" => "duplexBox")),
        	'otherEmail' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 100)), 
        	'contactNo' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 30)), 
            'optGender' => new sfWidgetFormChoice(array('expanded' => false, 'choices' => array('' => "-- " . __('Select') . " --", 1 => __('Male'), 2 => __('Female')))),
            'cmbMarital' => new sfWidgetFormChoice(array('expanded' => false,  'choices' => array('' => "-- " . __('Select') . " --", 'Single' => __('Single'), 'Married' => __('Married'), 'Other' => __('Other')))),
        	'chkLogin' => new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1), array()),
            'user_name' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 20)),
            'user_password' => new sfWidgetFormInputPassword(array(), array("class" => "formInputText passwordRequired", 
                "maxlength" => 20)),
            're_password' => new sfWidgetFormInputPassword(array(), array("class" => "formInputText passwordRequired", 
                "maxlength" => 20)),
            'status' => new sfWidgetFormSelect(array('choices' => $status), array("class" => "formInputText")),            
            'empNumber' => new sfWidgetFormInputHidden(),
        		
        	'addStreetOne' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 30)),
        	'addStreetTwo' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 30)),
        	'city' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 30)),
        	'state' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 30)),
        	'zipcode' => new sfWidgetFormInputText(array(), array("class" => "formInputText", "maxlength" => 30)),
        	'jobTitle' => new sfWidgetFormChoice(array('expanded' => false, 'choices' => $jobtitleList)),
        	'dateofjoining' => new ohrmWidgetDatePicker(array(), array('id' => 'expectedDoj')),
        	'country' => new sfWidgetFormChoice(array('expanded' => false, 'choices' => $countryList)),
        	'emp_status' => new sfWidgetFormSelect(array('choices' => $employeeStatuses)), 
        );
        
        	$inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        	$this->widgets['empNumber']->setDefault($empNumber);
        	$this->widgets['employeeId']->setDefault($employeeId);
        	if ($this->getOption(('employeeId')) != "") {
            	$this->widgets['employeeId']->setDefault($this->getOption(('employeeId')));
        	}
        
        	$this->widgets['firstName']->setDefault($this->getOption('firstName'));
        	$this->widgets['middleName']->setDefault($this->getOption('middleName'));
        	$this->widgets['lastName']->setDefault($this->getOption('lastName'));
        	$this->widgets['chkLogin']->setDefault($this->getOption('chkLogin'));
        	$this->widgets['user_name']->setDefault($this->getOption('user_name'));
        	$this->widgets['user_password']->setDefault($this->getOption('user_password'));
        	$this->widgets['re_password']->setDefault($this->getOption('re_password'));
        	$this->widgets['otherEmail']->setDefault($this->getOption('otherEmail'));
        	$this->widgets['contactNo']->setDefault($this->getOption('contactNo'));
        	$this->widgets['optGender']->setDefault($this->getOption('optGender'));
        	$this->widgets['cmbMarital']->setDefault($this->getOption('cmbMarital'));
        	
        	$this->widgets['addStreetOne']->setDefault($this->getOption('addStreetOne'));
        	$this->widgets['addStreetTwo']->setDefault($this->getOption('addStreetTwo'));
        	$this->widgets['city']->setDefault($this->getOption('city'));
        	$this->widgets['state']->setDefault($this->getOption('state'));
        	$this->widgets['zipcode']->setDefault($this->getOption('zipcode'));
        	$this->widgets['country']->setDefault($this->getOption('country'));
        	$this->widgets['jobTitle']->setDefault($this->getOption('jobTitle'));
        	$this->widgets['dateofjoining']->setDefault($this->getOption('dateofjoining'));
        	$this->widgets['emp_status']->setDefault($this->getOption('emp_status'));
        	$selectedStatus = $this->getOption('status');
       		if (empty($selectedStatus) || !isset($status[$selectedStatus])) {
            	$selectedStatus = 'Enabled';
        	}
        	$this->widgets['status']->setDefault($selectedStatus);
        	$this->setWidgets($this->widgets);
	

        $this->setValidators(array(
            'photofile' => new sfValidatorFile(array('max_size' => 1000000, 'required' => false)),
            'firstName' => new sfValidatorString(array('required' => true, 'max_length' => 100, 'trim' => true)),
            'empNumber' => new sfValidatorString(array('required' => false)),
            'lastName' => new sfValidatorString(array('required' => false, 'max_length' => 100, 'trim' => true)),
            'middleName' => new sfValidatorString(array('required' => false, 'max_length' => 100, 'trim' => true)),
            'employeeId' => new sfValidatorString(array('required' => true, 'max_length' => 10)),
            'chkLogin' => new sfValidatorString(array('required' => false)),
            'user_name' => new sfValidatorString(array('required' => false, 'max_length' => 20, 'trim' => true)),
            'user_password' => new sfValidatorString(array('required' => false, 'max_length' => 20, 'trim' => true)),
            're_password' => new sfValidatorString(array('required' => false, 'max_length' => 20, 'trim' => true)),
            'status' => new sfValidatorString(array('required' => false)),
        	'otherEmail' => new sfValidatorString(array('required' => false, 'max_length' => 100, 'trim' => true)),
        	'contactNo' => new sfValidatorString(array('required' => false, 'max_length' => 50, 'trim' => true)),
        	'optGender' => new sfValidatorString(array('required' => true)),
        	'cmbMarital' => new sfValidatorString(array('required' => true)),
 
        	'addStreetOne' => new sfValidatorString(array('required' => false, 'max_length' => 100, 'trim' => true)),
        	'addStreetTwo' => new sfValidatorString(array('required' => false, 'max_length' => 100, 'trim' => true)),
        	'city' => new sfValidatorString(array('required' => false, 'max_length' => 100, 'trim' => true)),
        	'state' => new sfValidatorString(array('required' => false, 'max_length' => 100, 'trim' => true)),
        	'zipcode' => new sfValidatorString(array('required' => false, 'max_length' => 20, 'trim' => true)),
        	'dateofjoining' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => true),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
        	'jobTitle' => new sfValidatorString(array('required' => true)),
        	'country' => new sfValidatorString(array('required' => false)),
        	'emp_status' => new sfValidatorString(array('required' => true)),
        ));

        //$this->getWidgetSchema()->setLabels($this->getFormLabels());

/*         $formExtension = PluginFormMergeManager::instance();
        $formExtension->mergeForms($this, 'addEmployee', 'AddEmployeeForm'); */
    
      /*   $customRowFormats[0] = "<li>%label% %field%<li>\n";
        $customRowFormats[1] = "<li class=\"line nameContainer\"><label class=\"hasTopFieldHelp\">". __('Full Name') . "</label><ol class=\"fieldsInLine\"><li><div class=\"fieldDescription\"><em>*</em> ". __('First Name') . "</div>\n %field%%help%\n%hidden_fields%%error%</li>\n";
        $customRowFormats[2] = "<li><div class=\"fieldDescription\">". __('Middle Name') . "</div>\n %field%%help%\n%hidden_fields%%error%</li>\n";
        $customRowFormats[3] = "<li><div class=\"fieldDescription\">". __('Last Name') . "</div>\n %label%%field%</li>\n</ol>\n</li>";
        $customRowFormats[5] = "<li class=\"line nameContainer\"><label class=\"hasTopFieldHelp\">". __('Contacts') . "</label><ol class=\"fieldsInLine\"><li><div class=\"fieldDescription\"> ". __('Email') . "</div>\n %field%</li>\n";
        $customRowFormats[6] = "<li><div class=\"fieldDescription\">". __('Phone Number') . "</div>\n %field%</li>\n</ol>\n</li>";
        $customRowFormats[7] = "<li>%label% %field%\n%hidden_fields%%error%</li>\n";
        $customRowFormats[8] = "<li>%label% %field%\n%hidden_fields%%error%</li>\n";
        $customRowFormats[9] = "<li>%label% %field%\n%hidden_fields%%error%</li>\n";
        $customRowFormats[11] = "<li class=\"loginSection\">%label%\n %field%%help%\n%hidden_fields%%error%</li>\n";
        $customRowFormats[12] = "<li class=\"loginSection\">%label%\n %field%%help%\n%hidden_fields%%error%</li>\n";
        $customRowFormats[13] = "<li class=\"loginSection\">%label%\n %field%%help%\n%hidden_fields%%error%</li>\n";
        $customRowFormats[14] = "<li class=\"loginSection\">%label%\n %field%%help%\n%hidden_fields%%error%</li>\n";
        
        sfWidgetFormSchemaFormatterCustomRowFormat::setCustomRowFormats($customRowFormats);
        $this->widgetSchema->setFormFormatterName('CustomRowFormat'); */
    }

    /**
     *
     * @return array
     */
/*     protected function getFormLabels() {
        $labels = array(
            'photofile' => __('Photograph'),
            'fullNameLabel' => __('Full Name'),
            'firstName' => false,
            'middleName' => false,
            'lastName' => false,
            'employeeId' => __('Employee Id'),
            'chkLogin' => __('Create Login Details'),
            'user_name' => __('User Name') . '<em> *</em>',
            'user_password' => __('Password') . '<em id="password_required"> *</em>',
            're_password' => __('Confirm Password') . '<em id="rePassword_required"> *</em>',
            'status' => __('Status') . '<em> *</em>',
        	'perAddress' => __('Address'),
        	'optGender' => __('Gender'),
        	'cmbMarital' => __('Marital Status')
        );

        return $labels;
    } */

    public function save() {
		
        $posts = $this->getValues();
        $file = $posts['photofile'];
        $employee = new Employee();
        $employee->firstName = $posts['firstName'];
        $employee->lastName = $posts['lastName'];
        $employee->middleName = $posts['middleName'];
        $employee->employeeId = $posts['employeeId'];
        $employee->emp_work_email = $posts['otherEmail'];
        $employee->emp_mobile = $posts['contactNo'];
        $employee->permanentAddress = $posts['perAddress'];
        $employee->emp_gender = $posts['optGender'];
        $employee->emp_marital_status = $posts['cmbMarital'];
         
        $employee->street1 = $posts['addStreetOne'];
        $employee->street2 = $posts['addStreetTwo'];
        $employee->city = $posts['city'];
        $employee->country = $posts['country'];
        $employee->province = $posts['state'];
        $employee->emp_zipcode = $posts['zipcode'];
        $employee->job_title_code = $posts['jobTitle'];
        $employee->joined_date = $posts['dateofjoining'];

        $employeeService = $this->getEmployeeService();
        $empStatus = $posts['emp_status'];
        if ($empStatus == '') {
        	$employee->emp_status = null;
        } else {
        	$employee->emp_status = $empStatus;
        }
        $employeeService->saveEmployee($employee);

        $empNumber = $employee->empNumber;

        //saving emp picture
        if (($file instanceof sfValidatedFile) && $file->getOriginalName() != "") {
            $empPicture = new EmpPicture();
          
            $empPicture->emp_number = $empNumber;
            $tempName = $file->getTempName();

            $empPicture->picture = file_get_contents($tempName);
           
            $empPicture->filename = $file->getOriginalName();
            $empPicture->file_type = $file->getType();
            $empPicture->size = $file->getSize();
            list($width, $height) = getimagesize($file->getTempName());
            $sizeArray = $this->pictureSizeAdjust($height, $width);
            $empPicture->width = $sizeArray['width'];
            $empPicture->height = $sizeArray['height'];
            $empPicture->save();
        }

        if ($this->createUserAccount) {
            $this->saveUser($empNumber);
        }
        
        $userId = sfContext::getInstance()->getUser()->getEmployeeNumber();
        if ($this->getConfigService()->isLeavePeriodDefined() && !empty($userId) && 
        		($posts['emp_status'] == EmploymentStatus::STATUS_FULLTIME_PERMENANT || $posts['emp_status'] == EmploymentStatus::STATUS_FULLTIME_PROBATION)) {
	        	$this->addEntitlements($employee, LeaveType::LEAVE_TYPE_PAID_LEAVE_ID, $posts);
        }

        //merge location dropdown
        $formExtension = PluginFormMergeManager::instance();
        $formExtension->saveMergeForms($this, 'addEmployee', 'AddEmployeeForm');
        return $empNumber;
    }

    private function saveUser($empNumber) {

        $posts = $this->getValues();

        if (trim($posts['user_name']) != "") {
            $userService = $this->getUserService();

            if (trim($posts['user_password']) != "" && $posts['user_password'] == $posts['re_password']) {
                $user = new SystemUser();
                $user->setDateEntered(date('Y-m-d H:i:s'));
                $user->setCreatedBy(sfContext::getInstance()->getUser()->getAttribute('user')->getUserId());
                $user->user_name = $posts['user_name'];
                $user->user_password = md5($posts['user_password']);
                $user->emp_number = $empNumber;
                $user->setStatus(($posts['status'] == 'Enabled') ? '1' : '0');
                $user->setUserRoleId(2);
                $userService->saveSystemUser($user);
            }
            
            $this->_handleLdapEnabledUser($posts, $empNumber);            
        }
    }
    
    
    private function pictureSizeAdjust($imgHeight, $imgWidth) {

        if ($imgHeight > 200 || $imgWidth > 200) {
            $newHeight = 0;
            $newWidth = 0;

            $propHeight = floor(($imgHeight / $imgWidth) * 200);
            $propWidth = floor(($imgWidth / $imgHeight) * 200);

            if ($propHeight <= 200) {
                $newHeight = $propHeight;
                $newWidth = 200;
            }

            if ($propWidth <= 200) {
                $newWidth = $propWidth;
                $newHeight = 200;
            }
        } else {
            if ($imgHeight <= 200)
                $newHeight = $imgHeight;

            if ($imgWidth <= 200)
                $newWidth = $imgWidth;
        }
        return array('width' => $newWidth, 'height' => $newHeight);
    }

    protected function _handleLdapEnabledUser($postedValues, $empNumber) {
        
        $sfUser = sfContext::getInstance()->getUser();
        
        $password           = $postedValues['user_password'];
        $confirmedPassword  = $postedValues['re_password'];
        $check1             = (empty($password) && empty($confirmedPassword))?true:false;
        $check2             = $sfUser->getAttribute('ldap.available');
        
        if ($check1 && $check2) {
            $user = new SystemUser();
            $user->setDateEntered(date('Y-m-d H:i:s'));
            $user->setCreatedBy($sfUser->getAttribute('user')->getUserId());
            $user->user_name = $postedValues['user_name'];
            $user->user_password = md5('');
            $user->emp_number = $empNumber;
            $user->setUserRoleId(2);
            $this->getUserService()->saveSystemUser($user);            
        }
        
    }  

    public function applyDefaultEmployeeData($emp)
    {
    	$this->setDefault('empNumber', $emp->getEmpNumber());
    	$this->setDefault('employeeId', str_pad($emp->getEmpNumber(), 4, "0", STR_PAD_LEFT));
    	$this->setDefault('firstName',$emp->getFirstName());
    	$this->setDefault('middleName',$emp->getMiddleName());
    	$this->setDefault('lastName',$emp->getLastName());
    	$this->setDefault('contactNo', $emp->getEmpMobile());
    	$this->setDefault('user_name', "");
    	$this->setDefault('user_password', "");
    	$this->setDefault('re_password', "");
    
    }
    /* 
    public function updateEmployee()
    {
    	$posts = $this->getValues();
    	$file = $posts['photofile'];
    	$employee = new Employee();
    	$employee->firstName = $posts['firstName'];
    	$employee->lastName = $posts['lastName'];
    	$employee->middleName = $posts['middleName'];
    	$employee->employeeId = $posts['employeeId'];
    	$employee->emp_work_email = $posts['otherEmail'];
    	$employee->emp_mobile = $posts['contactNo'];
    
    	$employee->empNumber = $posts['empNumber'];
    	$employee->emp_gender = $posts['optGender'];
    	$employee->emp_marital_status = $posts['cmbMarital'];
    	
    	$employee->street1 = $posts['addStreetOne'];
    	$employee->street2 = $posts['addStreetTwo'];
    	$employee->city = $posts['city'];
    	$employee->country = $posts['country'];
    	$employee->province = $posts['state'];
    	$employee->emp_zipcode = $posts['zipcode'];
    	$employee->job_title_code = $posts['jobTitle'];
    	$employee->joined_date = $posts['dateofjoining'];
    	
    	$employeeService = $this->getEmployeeService();
    	$employeeService->updateEmployee($employee); 
    	$tempEmp = $_SESSION['employeeToAdd'];
    	
    	//saving emp picture
    	if (($file instanceof sfValidatedFile) && $file->getOriginalName() != "") {
    		$empPicture = new EmpPicture();
    		$empPicture->emp_number = $tempEmp->getEmpNumber(); // work on emplyoyee number
    		$tempName = $file->getTempName();
    		$empPicture->picture = file_get_contents($tempName);	
    		$empPicture->filename = $file->getOriginalName();
    		$empPicture->file_type = $file->getType();
    		$empPicture->size = $file->getSize();
    		list($width, $height) = getimagesize($file->getTempName());
    		$sizeArray = $this->pictureSizeAdjust($height, $width);
    		$empPicture->width = $sizeArray['width'];
    		$empPicture->height = $sizeArray['height'];
    		$empPicture->save();
    	}
    	
    	//save user
    	if ($this->createUserAccount) {
    		$this->saveUser($tempEmp->getEmpNumber()); // modified
    	}
    	
    	$userId = sfContext::getInstance()->getUser()->getEmployeeNumber();
    	if(!empty($userId))
        {
        	$this->addEntitlements($employee, LeaveType::LEAVE_TYPE_PAID_LEAVE_ID);
        	$this->addEntitlements($employee, LeaveType::LEAVE_TYPE_WFH_ID);
        }
    	
    	//merge location dropdown
    	$formExtension = PluginFormMergeManager::instance();
    	$formExtension->saveMergeForms($this, 'addEmployee', 'AddEmployeeForm');
    	unset($_SESSION['joinMode']);
    	unset($_SESSION['employeeToAdd']);
    	return $posts['empNumber'];
    } */
    
    private function addEntitlements($employee, $leaveId, $posts)
    {
    	$doj = intval(date('d', strtotime($employee->getJoinedDate())));
    	$dom = intval(date('m', strtotime($employee->getJoinedDate())));
    	if($doj > 15 && $dom == 3) {
    		$plusMonth = date('Y-m-d', strtotime(date("Y-m-d", strtotime($employee->getJoinedDate())) . " +1 Months"));
    		$leavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate($plusMonth, true);
    		$from = $leavePeriod[0];
    		$to = $leavePeriod[1];
    	} else {
	    	$leavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate($employee->getJoinedDate());
	    	$from = $leavePeriod[0];
	    	$to = $leavePeriod[1];
    	}
    	$empNo = sfContext::getInstance()->getUser()->getEmployeeNumber();
    	$userId = $this->getSystemUserService()->getUserId($empNo);
    	$LoggedinUser = $this->getEmployeeService()->getEmployee(sfContext::getInstance()->getUser()->getEmployeeNumber());
    	$createdBy = trim(trim($LoggedinUser['firstName']) . ' ' . $LoggedinUser['lastName']);
    	if($posts['emp_status'] == EmploymentStatus::STATUS_FULLTIME_PROBATION) {
    		$count = 5;
    	} else if ($posts['emp_status'] == EmploymentStatus::STATUS_FULLTIME_PERMENANT)  {
    		if($doj > 15 && $dom == 3) {
    			$count = 21;
    		} else {
    			$count = $this->calculateNoOfLeaves($employee->getJoinedDate(), $leaveId, $from, $to);
    		}
    	}
    	$leaveEntitlement = new LeaveEntitlement();
    	$leaveEntitlement->setCreditedDate(date('Y-m-d'));
    	$leaveEntitlement->setCreatedById($userId);
    	$leaveEntitlement->setCreatedByName($createdBy);
    	$leaveEntitlement->setEntitlementType(LeaveEntitlement::ENTITLEMENT_TYPE_ADD);
    	$leaveEntitlement->setDeleted(0);
    	$leaveEntitlement->setDaysUsed(0);
    	$leaveEntitlement->setNoOfDays($count);
    	$leaveEntitlement->setEmpNumber($employee->empNumber);
    	$leaveEntitlement->setToDate($to);
    	$leaveEntitlement->setFromDate($from);
    	$leaveEntitlement->setLeaveTypeId($leaveId);
    	$this->getLeaveEntitlementService()->saveLeaveEntitlement($leaveEntitlement);
    }
    
    private function calculateNoOfLeaves($joinedDate, $leaveId, $from, $to)
    {
    	$noOfMonth = 3;
       	if(date('Y', strtotime($joinedDate))==date('Y', strtotime($from)))
    	{
    		$noOfMonth+= (12 - intval(date('m', strtotime($joinedDate))));
    	}
    	else {
    		$noOfMonth-= intval(date('m', strtotime($joinedDate)));
    	} 
    	$doj = intval(date('d', strtotime($joinedDate)));
    	if($leaveId==1) {
    		if($doj<=15) {
    			$days = $noOfMonth += 1;
    		}
    		$days = $noOfMonth * 1.75;
    	}
    	return $days;
    }
    
    private function getJobTitles() {
    	$jobTitleList = $this->getJobTitleService()->getJobTitleList();
    	$choices = array('' => __('--Select--'));
    	foreach ($jobTitleList as $job) {
    		$choices[$job->getId()] = $job->getJobTitleName();
    	}
    	return $choices;
    }
    
    private function getCountryList()
    {
    	$countryList = $this->getCountryService()->getCountryList();
    	$choices = array('' => __('--Select--'));
    	foreach($countryList as $country){
    		$choices[$country->getCouCode()] = $country->getCouName();
    	}
    	//var_dump($choices);
    	return $choices;
    }
    
    public function getEmployeeList() {
    	$properties = array("emp_work_email","empNumber");
    	$employeeList = $this->getEmployeeService()->getEmployeePropertyList($properties, 'lastName', 'ASC', true);
    	foreach ($employeeList as $employee) {
    		$empNumber = $employee['empNumber'];
    		$email = $employee['emp_work_email'];
    		$list[] = array('email' => $email, 'id' => $empNumber);
    	} 
    	return $list;
    }
    
    private function _getEmpStatuses() {
    	$empStatusService = new EmploymentStatusService();
    	$choices = array('' => '-- ' . __('Select') . ' --');
    	$statuses = $empStatusService->getEmploymentStatusList();
    	foreach ($statuses as $status) {
    		$choices[$status->getId()] = $status->getName();
    	}
    	return $choices;
    }
}