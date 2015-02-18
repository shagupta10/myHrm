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
class PimCsvDataImport extends CsvDataImport {

	private $employeeService;
	private $nationalityService;
	private $countryService;
	//private $logger;

	public function import($data) {
		
		set_time_limit(90);
		if ($data[0] == "" || strlen($data[0]) > 30) {
			return false;
		}
		
		$createUser = false;
		$empService = new EmployeeService();
		$employee = $empService->getEmployeeByEmployeeId($data[0]);
		
		if(empty($employee)){
			$employee = new Employee();
			$createUser = true;
		}
		
		if (strlen($data[0]) <= 50) {
			$employee->setEmployeeId($data[0]);
		}
		
		$employee->setFirstName($data[1]);
		if (strlen($data[2]) <= 30) {
			$employee->setMiddleName($data[2]);
		}
		$employee->setLastName($data[3]);

		
		if (strlen($data[4]) <= 30) {
			$employee->setOtherId($data[4]);
		}
		if (strlen($data[5]) <= 30) {
			$employee->setBloodGroup($data[5]);
		}
		
		if ($data[6] != ""){
			$dob = $this->formatDate($data[6]);
			$employee->setEmpBirthday($dob);
		}

		if (strtolower($data[7]) == 'male') {
			$employee->setEmpGender('1');
		} else if (strtolower($data[7]) == 'female') {
			$employee->setEmpGender('2');
		}

		if (strtolower($data[8]) == 'single') {
			$employee->setEmpMaritalStatus('Single');
		} else if (strtolower($data[8]) == 'married') {
			$employee->setEmpMaritalStatus('Married');
		} else if (strtolower($data[8]) == 'other') {
			$employee->setEmpMaritalStatus('Other');
		}

		$nationality = $this->isValidNationality($data[9]);
		if (!empty($nationality)) {
			$employee->setNationality($nationality);
		}
		
		if (strlen($data[10]) <= 70) {
			$employee->setFatherName($data[10]);
		}
		
		if (strlen($data[11]) <= 70) {
			$employee->setHusbandName($data[11]);
		}
		
		if (strlen($data[12]) <= 70) {
			$employee->setStreet1($data[12]);
		}
		if (strlen($data[13]) <= 70) {
			$employee->setStreet2($data[13]);
		}
		if (strlen($data[14]) <= 70) {
			$employee->setCity($data[14]);
		}
		
		if (strlen($data[16]) <= 10) {
			$employee->setEmpZipcode($data[16]);
		}

		$code = $this->isValidCountry($data[17]);
		if (!empty($code)) {
			$employee->setCountry($code);
			if (strtolower($data[17]) == 'united states') {				
				$code = $this->isValidProvince($data[15]);
				if(!empty($code)){
					$employee->setProvince($code);
				}
			} else if (strlen($data[15]) <= 70) {
				$employee->setProvince($data[15]);
			}
		}
		
		
		if (strlen($data[18]) <= 100) {
			$employee->setPermanentAddress($data[18]);
		}
		
		if ($this->isValidEmail($data[19]) && strlen($data[19]) <= 50 && $this->isUniqueEmail($data[19])) {
			$employee->setEmpWorkEmail($data[19]);
		}
		if ($this->isValidEmail($data[20]) && strlen($data[20]) <= 50) {
			$employee->setEmpOthEmail($data[20]);
		}
		if ($this->isValidEmail($data[21]) && strlen($data[21]) <= 50) {
			$employee->setEmpPersonalEmail($data[21]);
		}
		
		if (strlen($data[22]) <= 25 && $this->isValidPhoneNumber($data[22])) {
			$employee->setEmpMobile($data[22]);
		}
		if (strlen($data[23]) <= 25 && $this->isValidPhoneNumber($data[23])) {
			$employee->setEmpWorkTelephone($data[23]);
		}
		if (strlen($data[24]) <= 25 && $this->isValidPhoneNumber($data[24])) {
			$employee->setEmpHmTelephone($data[24]);
		}
		
		if (strlen($data[25]) <= 50) {
			$employee->setEmpPhoneAccesscode($data[25]);
		}
		
		if (strlen($data[26]) <= 50) {
			$employee->setEmpSkypeId($data[26]);
		}
		
		if ($data[27] != "" ) {
			$joinedDate = $this->formatDate($data[27]);
			$employee->setJoinedDate($joinedDate);
		}
		
		$jobTitle = $this->isValidJobTitle($data[28]);
		if (!empty($jobTitle)) {
			$employee->setJobTitleCode($jobTitle);
		}
		
		if ($data[29] != "" && is_numeric($data[29])) {
			$employee->setTotalExperience($data[29]);
		}
		
		if ($data[30] != "" && is_numeric($data[30])) {
			$employee->setCurrentExperience($data[30]);
		}
		
		if ($data[31] != "" && is_numeric($data[31])) {
			$employee->setNoticePeriod($data[31]);
		}
		
		if (strlen($data[32]) <= 50) {
			$employee->setProject($data[32]);
		}
		
		if (strlen($data[33]) <= 50) {
			$employee->setReferredBy($data[33]);
		}
		
		if (strlen($data[34]) <= 50) {
			$employee->setCustom4($data[34]);
		}
		
		if (strlen($data[38]) <= 50) {
			$employee->setCustom2($data[38]);
		}
		if (strlen($data[39]) <= 50) {
			$employee->setCustom3($data[39]);
		}
		if (strlen($data[40]) <= 50) {
			$employee->setCustom1($data[40]);
		}
		
		$employee = $empService->saveEmployee($employee);
		
		if(($data[35] != "" ) && ($data[36] != "" )){
			$employeeSalary = new EmployeeSalary();
			$employeeSalary->setSalaryName("CTC");
			$employeeSalary->setPayPeriodId("4");
			$employeeSalary->setCurrencyCode($data[36]);
			$employeeSalary->setAmount($data[35]);
			$employeeSalary->setEmpNumber($employee);
			
			$empDirectDebit = new EmpDirectdebit();
			$empDirectDebit->setAccount($data[37]);
			$empDirectDebit->setAccountType("SAVINGS");
			$employeeSalary->setDirectDebit($empDirectDebit);
			$empService->saveEmployeeSalary($employeeSalary);
		}
		
		if(($data[41] != "" ) && (strlen($data[41])<= 50)){
			$empPassport = new EmployeeImmigrationRecord();
			$empPassport->setEmployee($employee);
			$empPassport->setNumber($data[41]);
			$empPassport->setCountryCode($data[42]);
			if ($data[43] != "" ) {
				$expiryDate = $this->formatDate($data[43]);
				$empPassport->setExpiryDate($expiryDate);
			}
			$empPassport->setType(1);
			$empService->saveEmployeeImmigrationRecord($empPassport);
		}
        
        if(($data[44] != "" ) && (strlen($data[44])<= 50)){
	        $empVisaDetails = new EmployeeImmigrationRecord();
	        $empVisaDetails->setEmployee($employee);
	        $empVisaDetails->setNumber($data[44]);
	        if ($data[45]!="") {
	        	$visaExpiryDate = $this->formatDate($data[45]);
		        $empVisaDetails->setExpiryDate($visaExpiryDate);
	        }
	        $empVisaDetails->setType(2);
	        $empService->saveEmployeeImmigrationRecord($empVisaDetails);
        }
       
       	
        if(($data[46] != "" ) && ($data[46] != '0' ) && (strlen($data[46])<= 50)){
        	$sequence1 = 1;//$this->getDependentSeqNo($employee->getEmpNumber());
	        $dependent1 = $this->getEmployeeDependent($employee->getEmpNumber(),$sequence1);
	        $dependent1->setEmployee($employee);
	        $dependent1->setSeqno($sequence1);
	        $dependent1->setName($data[46]);
	        if($data[47]!=""){
		        $dependent1->setDateOfBirth($this->formatDate($data[47]));
	        }
	        $dependent1->setRelationshipType($data[48]);
	        $dependent1->setRelationship($data[49]);
	        $dependent1->save();
        }
        
       if(($data[50] != "" ) && ($data[50] != '0' )  && (strlen($data[50])<= 50)){
       		$sequence2 = 2;//$this->getDependentSeqNo($employee->getEmpNumber());
	        $dependent2 = $this->getEmployeeDependent($employee->getEmpNumber(),$sequence2);
	        $dependent2->setEmployee($employee);
	        $dependent2->setSeqno($sequence2);
	        $dependent2->setName($data[50]);
	        if($data[51] != ""){
		        $dependent2->setDateOfBirth($this->formatDate($data[51]));
	        }
	        $dependent2->setRelationshipType($data[52]);
	        $dependent2->setRelationship($data[53]);
	        $dependent2->save();
        }
        
        if(($data[54] != "" ) && ($data[54] != '0' ) && (strlen($data[54])<= 50)){
        	$sequence3 = 3;//$this->getDependentSeqNo($employee->getEmpNumber());
	        $dependent3 = $this->getEmployeeDependent($employee->getEmpNumber(),$sequence3);
	        $dependent3->setEmployee($employee);
	        $dependent3->setSeqno($sequence3);
	        $dependent3->setName($data[54]);
	        if($data[55] != ""){
		        $dependent3->setDateOfBirth($this->formatDate($data[55]));
	        }
	        $dependent3->setRelationshipType($data[56]);
	        $dependent3->setRelationship($data[57]);
	        $dependent3->save();
        }
        
        
         if($createUser && $data[58] != "" && $data[59]!= ""){
         	$user = new SystemUser();      
	        $user->setDateEntered(date('Y-m-d H:i:s'));
	        //$user->setCreatedBy($sfUser->getAttribute('user')->getUserId());
	        $user->setUserName($data[58]);
	        $user->setUserPassword(md5($data[59]));
	        $user->setEmployee($employee);
	        $user->setUserRoleId(2);
	        $this->getUserService()->saveSystemUser($user); 
        }
		return true;
	}

	private function isValidEmail($email) {
		return eregi("^[_a-z0-9-]+(\.[_a-z0-9-]+)*@[a-z0-9-]+(\.[a-z0-9-]+)*(\.[a-z]{2,3})$", $email);
	}
	
	/**
	 * Format date to YYYY-MM-dd
	 */
	private function formatDate($date){
		try{
			$dateTime = new DateTime($date);
			$formatted_date=date_format ( $dateTime, 'Y-m-d' );
			return $formatted_date;	
		} catch (Exception $e) {
			$logger = Logger::getLogger('import.PimCsvDataImport');
			$logger->error('PIM import Data issue: ' . $e);
		}
	}
	
	private function isUniqueEmail($email) {
		$emailList = $this->getEmployeeService()->getEmailList();
		$isUnique = true;
		foreach ($emailList as $empEmail) {
			if ($empEmail['emp_work_email'] == $email || $empEmail['emp_oth_email'] == $email) {
				$isUnique = false;
			}
		}
		return $isUnique;
	}

	private function isValidDate($date) {
		if (preg_match('/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/', $date)) {
			list($year, $month, $day) = explode('-', $date);
			return checkdate($month, $day, $year);
		} else {
			return false;
		}
	}

	private function isValidNationality($name) {

		$nationalities = $this->getNationalityService()->getNationalityList();

		foreach ($nationalities as $nationality) {
			if (strtolower($nationality->getName()) == strtolower($name)) {
				return $nationality;
			}
		}
	}
	
	private function isValidJobTitle($designation) {
		$jobTitleList = $this->getJobTitleService()->getJobTitleList();
		foreach ($jobTitleList as $jobTitle) {
			if (strtolower($jobTitle->getJobTitleName()) == strtolower($designation)) {
				return $jobTitle;
			}
		}
	}

	private function isValidCountry($name) {

		$countries = $this->getCountryService()->getCountryList();

		foreach ($countries as $country) {
			if (strtolower($country->cou_name) == strtolower($name)) {
				return $country->cou_code;
			}
		}
	}
	
	private function isValidProvince($name) {

		$provinces = $this->getCountryService()->getProvinceList();
		
		foreach ($provinces as $province) {
			if (strtolower($province->province_name) == strtolower($name)) {
				return $province->province_code;
			}
		}
	}

	public function isValidPhoneNumber($number) {
		if (preg_match('/^\+?[0-9 \-]+$/', $number)) {
			return true;
		}
	}

	public function getCountryService() {
		if (is_null($this->countryService)) {
			$this->countryService = new CountryService();
		}
		return $this->countryService;
	}

	public function getNationalityService() {
		if (is_null($this->nationalityService)) {
			$this->nationalityService = new NationalityService();
		}
		return $this->nationalityService;
	}

	public function getEmployeeService() {
		if (is_null($this->employeeService)) {
			$this->employeeService = new EmployeeService();
			$this->employeeService->setEmployeeDao(new EmployeeDao());
		}
		return $this->employeeService;
	}

	
	public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
            $this->jobTitleService->setJobTitleDao(new JobTitleDao());
        }
        return $this->jobTitleService;
    }
    
    private function getUserService() {
        if (is_null($this->userService)) {
            $this->userService = new SystemUserService();
        }
        return $this->userService;
    }
    
    private function getDependentSeqNo($empNumber){
	    $q = Doctrine_Query::create()
		    ->select('MAX(d.seqno)')
			->from('EmpDependent d')
			->where('d.emp_number = ?', $empNumber);
	    $result = $q->execute(array(), Doctrine::HYDRATE_ARRAY);           
	    $seqNo = is_null($result[0]['MAX']) ? 1 : $result[0]['MAX'] + 1;       
	    return $seqNo;
    }
    
    private function getEmployeeDependent($empNumber , $seqNo){
	    $dependent = Doctrine::getTable('EmpDependent')->find(array('emp_number' => $empNumber,
		    'seqno' => $seqNo));
	    if ($dependent === false) {
		    $dependent = new EmpDependent();
	    }
	    return $dependent;
    }
	
	
}

?>
