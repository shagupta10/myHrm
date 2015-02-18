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
class PerformanceDataImport extends CsvDataImport {
	
	private $employeeService;
	private $kpiService;
    private $jobService;
    private $performanceKpiService;
    private $performanceReviewService;
    private $jobTitleService;
	
	//private $logger;
	
	public function import($data) {
		set_time_limit(180);
		$logger = Logger::getLogger('import.PerformanceDataImport');
		$empArray = $this->getEmployeeList();
		//$logger->error('Adding Reviewer : ' . $data[0]);
		
		if ($data[0] == "" || strlen($data[0]) > 30) {
			return false;
		}
		
		$empService = $this->getEmployeeService();
		$employee = $empService->getEmployeeByEmployeeId($data[0]);
		
		
		if(empty($employee)){
			$logger->error($data[0]. ' No employee found: ' . trim($data[1]));
			return true;
		}
		
		$empJobCode = $employee->getJobTitleCode();
		if (empty($empJobCode)) {
			$logger->error($data[0] .' No job title assigned : ' . trim($data[1]));
			return true;
		}
		
		$kpiService = $this->getKpiService();
		$performanceKpiService = $this->getPerformanceKpiService();
		$kpiList = $kpiService->getKpiForJobTitle($empJobCode);
		
		if (count($kpiList) == 0) {
			$this->noKpiDefined = true;
			$logger->error($data[0].' No kipList found: ' . trim($data[1]));
			return true;
		}
		
		$performanceReviewService = $this->getPerformanceReviewService();
		$review = new PerformanceReview();
		$xmlStr = $performanceKpiService->getXmlFromKpi($kpiService->getKpiForJobTitle($empJobCode));
		
		
		$review->setEmployeeId($employee->getEmpNumber());
		$review->setCreatorId(285);
		$review->setJobTitleCode($empJobCode);
		//$review->setSubDivisionId($subDivisionId);
		$review->setCreationDate(date('Y-m-d'));
		$localizationService = new LocalizationService();
		$inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
		$review->setPeriodFrom($this->formatDate('01-04-2013'));
		$review->setPeriodTo($this->formatDate('30-09-2013'));
		$review->setDueDate($this->formatDate('15-10-2013'));
		$review->setState(PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED);
		$review->setKpis($xmlStr);
		
		$performanceReviewerList = array();
		for($i=3; $i<=7;$i++){
			if($data[$i] != ""){
				if(array_key_exists(trim($data[$i]), $empArray)){
					$performanceReviewer = new PerformanceReviewReviewer();
					$performanceReviewer->setReviewerId( $empArray[$data[$i]]);
					$performanceReviewer->setKpis($xmlStr);
					array_push($performanceReviewerList,$performanceReviewer);	
				}else{
					$logger->error('Reviewer Not found for Employee: ' .$data[0]."-". trim($data[$i]));
				}
			}
		}
		
		$review = $performanceReviewService->savePerformanceReview($review, $performanceReviewerList);
		
		
		
		
//		if(trim($data[1]) != ""){
//				if(array_key_exists(trim($data[1]), $empArray)){
//				//$logger->error('Employee Found : ' .$data[1].'='. $empArray[$data[1]]);
//			}else{
//				$logger->error('Employee Not found: ' . trim($data[1]));
//			}
//		}
		return true;
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
			$logger = Logger::getLogger('import.LeaveDataImport');
			$logger->error('Leave import Data issue: ' . $e);
		}
	}
	
	/**
	 *
	 * @return LeaveApplicationService
	 */
	public function getLeaveApplicationService() {
		if (!($this->leaveApplicationService instanceof LeaveApplicationService)) {
			$this->leaveApplicationService = new LeaveApplicationService();
		}
		return $this->leaveApplicationService;
	}
	
	public function getEmployeeService() {
		if (is_null($this->employeeService)) {
			$this->employeeService = new EmployeeService();
			$this->employeeService->setEmployeeDao(new EmployeeDao());
		}
		return $this->employeeService;
	}
	
	
	public function getEmployeeList() {
        $jsonArray = array();
       
        $properties = array("empNumber","firstName", "middleName", "lastName", "job_title_code");
        $employeeList = $this->getEmployeeService()->getEmployeePropertyList($properties, 'lastName', 'ASC', true);

        foreach ($employeeList as $employee) {
            $empNumber = $employee['empNumber'];
            //$name = trim(trim($employee['firstName'] . ' ' . $employee['middleName'],' ') . ' ' . $employee['lastName']);
            $name = trim(trim($employee['firstName']) . ' ' . trim($employee['lastName']));
            $jsonArray[$name] =  $empNumber;
        }
        
        return $jsonArray;
    }
    
     public function getJobTitleService() {
        if (is_null($this->jobTitleService)) {
            $this->jobTitleService = new JobTitleService();
            $this->jobTitleService->setJobTitleDao(new JobTitleDao());
        }
        return $this->jobTitleService;
    }

    /**
     * Get Kpi Service
     * @return KpiService
     */
    public function getKpiService() {
        $this->kpiService = new KpiService();
        $this->kpiService->setKpiDao(new KpiDao());
        return $this->kpiService;
    }

    /**
     * Set Kpi Service
     *
     * @param KpiService $kpiService
     * @return void
     */
    public function setKpiService(KpiService $kpiService) {
        $this->kpiService = $kpiService;
    }

    /**
     * Get Job Service
     */
    public function getPerformanceKpiService() {
        $this->performanceKpiService = new PerformanceKpiService();
        return $this->performanceKpiService;
    }

    /**
     * Set Job Service
     * @param JobService $jobService
     * @return unknown_type
     */
    public function setPerformanceKpiService(PerformanceKpiService $performanceKpiService) {
        $this->performanceKpiService = $performanceKpiService;
    }

    /**
     * Get Job Service
     */
    public function getPerformanceReviewService() {
        $this->performanceReviewService = new PerformanceReviewService();
        $this->performanceReviewService->setPerformanceReviewDao(new PerformanceReviewDao());
        return $this->performanceReviewService;
    }

    /**
     * Set Job Service
     * @param JobService $jobService
     * @return unknown_type
     */
    public function setPerformanceReviewService(PerformanceReviewService $performanceReviewService) {
        $this->performanceReviewService = $performanceReviewService;
    }
}

?>
