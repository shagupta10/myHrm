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
class JobTitleDataImport extends CsvDataImport {
	
	private $employeeService;
	private $nationalityService;
	private $countryService;
	//private $logger;
	
	public function import($data) {
		
		set_time_limit(90);
		if ($data[0] == "" || strlen($data[0]) > 30) {
			return false;
		}
		$empService = new EmployeeService();
		$employee = $empService->getEmployeeByEmployeeId($data[0]);
		$logger = Logger::getLogger('import.JobTitleImport');
		
		if(!empty($employee)){
			$jobTitle = $this->isValidJobTitle($data[3]);
			if (!empty($jobTitle)) {
				$employee->setJobTitleCode($jobTitle);
				$employee = $empService->saveEmployee($employee);
			}else{
				$logger->error('JobTitle:Employee: ' . $data[0] .' JobTitle: '.$data[3]);
			}
			
		}else{
			$logger->error('JobTitle:No Employee: ' . $data[0]);
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
	
	
	private function isValidJobTitle($designation) {
		$jobTitleList = $this->getJobTitleService()->getJobTitleList();
		foreach ($jobTitleList as $jobTitle) {
			if (strtolower($jobTitle->getJobTitleName()) == strtolower(trim($designation))) {
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
	
}

?>
