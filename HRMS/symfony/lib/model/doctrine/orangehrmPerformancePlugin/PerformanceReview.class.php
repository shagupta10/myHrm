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
class PerformanceReview extends BasePerformanceReview {

	const PERFORMANCE_REVIEW_STATUS_SCHDULED			=	1 ;
	const PERFORMANCE_REVIEW_STATUS_BEING_REVIWED		=	3 ;
	const PERFORMANCE_REVIEW_STATUS_SUBMITTED			=	5 ;
	const PERFORMANCE_REVIEW_STATUS_REJECTED			=	7 ;
	const PERFORMANCE_REVIEW_STATUS_APPROVED			=	9 ;
	
	const PERFORMANCE_REVIEW_STATUS_TEXT_SCHDULED		=	'Scheduled' ;
	const PERFORMANCE_REVIEW_STATUS_TEXT_BEING_REVIWED	=	'Being Reviewed' ;
	const PERFORMANCE_REVIEW_STATUS_TEXT_SUBMITTED		=	'Submitted' ;
	const PERFORMANCE_REVIEW_STATUS_TEXT_REJECTED		=	'Rejected' ;
	const PERFORMANCE_REVIEW_STATUS_TEXT_APPROVED		=	'Approved' ;
	
	const KPI_GENERAL_DATE = '2014-10-01';
	
	public static $performanceStatusList = array(
		self::PERFORMANCE_REVIEW_STATUS_SCHDULED => self::PERFORMANCE_REVIEW_STATUS_TEXT_SCHDULED,
		self::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED => self::PERFORMANCE_REVIEW_STATUS_TEXT_BEING_REVIWED,
		self::PERFORMANCE_REVIEW_STATUS_SUBMITTED => self::PERFORMANCE_REVIEW_STATUS_TEXT_SUBMITTED,
		self::PERFORMANCE_REVIEW_STATUS_REJECTED => self::PERFORMANCE_REVIEW_STATUS_TEXT_REJECTED,
		self::PERFORMANCE_REVIEW_STATUS_APPROVED => self::PERFORMANCE_REVIEW_STATUS_TEXT_APPROVED   
	);
	
	
	private $latestComment ;
	private $performanceReviewService;
	private $performanceKpiService;
	private $employeeService;
	/**
	 * Get Peformance Review Service
	 */
	public function getPerformanceReviewService() {
		if (is_null($this->performanceReviewService)) {
			$this->performanceReviewService = new PerformanceReviewService();
			$this->performanceReviewService->setPerformanceReviewDao(new PerformanceReviewDao());
		}
		return $this->performanceReviewService;
	}
	
	/**
	 * Get Peformance Kpi Service
	 */
	public function getPerformanceKpiService() {
		$this->performanceKpiService = new PerformanceKpiService();
		return $this->performanceKpiService;
	}
	public function getEmployeeService() {
		$this->employeeService = new EmployeeService();
		return $this->employeeService;
	}
	
	public function getKpiList(){
		$performanceKpiService = $this->getPerformanceKpiService();
		$performanceKpiList = $performanceKpiService->getPerformanceKpiList($this->getKpis());
		return $performanceKpiList;
	}
	
	public function getReviewRatings(){
		$performanceKpiService = $this->getPerformanceKpiService();
		$performanceRatings = $performanceKpiService->getPerformanceRatingList($this->getRatings());
		return $performanceRatings;
	}
		
	/**
	 * Returns the full name of employee, (first middle last)
	 * 
	 * @return String Full Name 
	 */
	public function getFullName() {
		
	    $fullName = trim($this->firstName) . " " . trim($this->middleName);
	    $fullName = trim( trim($fullName) . " " . trim($this->lastName) ); 
		
		return $fullName;
	}
	
	/**
	 *  Get Latest comment
	 */
	public function getLatestComment( )
	{
		return $this->latestComment ;
	}
	
	/**
	 * Set Latest Comment
	 * @return unknown_type
	 */
	public function setLatestComment( $latestComment)
	{
		$this->latestComment	=	$latestComment ;
	}
	
	
	/**
	 * Gets the names of all the supervisors of this employee as a comma separated string
	 * Only the first and last name are used.
	 * 
	 * @return String String containing comma separated list of supervisor names. 
	 *                Empty string if employee has no supervisors
	 */
	public function getSupervisorNames() {
	    $supervisorNames = array();
	    
	    foreach ($this->supervisors as $supervisor ){
	        $supervisorNames[] = trim($supervisor->firstName . ' ' . $supervisor->lastName); 
	    }
	    
	    return implode(',', $supervisorNames);
	}
	
	public function getReviewUrl() {
	
		$url = (strtotime($this->periodFrom) < strtotime(PerformanceReview::KPI_GENERAL_DATE))? 'performanceReview':'reviewPerformance';
	
		return $url;
	}
	
	/**
	 * Returns the review period of employee, (from to)
	 * 
	 * @return String $reviewPeriod
	 */
	public function getReviewPeriod() {
		
	    $reviewPeriod = set_datepicker_date_format(trim($this->periodFrom)) . " - " . set_datepicker_date_format(trim($this->periodTo));
		
		return $reviewPeriod;
	}
    /*returns datepicker formatted due_date*/
	public function getFormattedDueDate() { 
	    $dueDate = set_datepicker_date_format(trim($this->dueDate)); 
		
		return $dueDate;
	}
    /**
     * Get Text status
     */
    public function getTextStatus( )
    {
    	$textStatus	=	'';
    	switch( $this->getState() )
    	{
    		case self::PERFORMANCE_REVIEW_STATUS_SUBMITTED:
    			$textStatus	=	self::PERFORMANCE_REVIEW_STATUS_TEXT_SUBMITTED;
    		break;
    		
    		case self::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED:
    			$textStatus	=	self::PERFORMANCE_REVIEW_STATUS_TEXT_BEING_REVIWED;
    		break;
    		
    		case self::PERFORMANCE_REVIEW_STATUS_SCHDULED:
    			$textStatus	=	self::PERFORMANCE_REVIEW_STATUS_TEXT_SCHDULED;
    		break;
    		
    		case self::PERFORMANCE_REVIEW_STATUS_REJECTED:
    			$textStatus	=	self::PERFORMANCE_REVIEW_STATUS_TEXT_REJECTED;
    		break;
    		
    		case self::PERFORMANCE_REVIEW_STATUS_APPROVED:
    			$textStatus	=	self::PERFORMANCE_REVIEW_STATUS_TEXT_APPROVED;
    		break;
    		
    	}
    	return $textStatus;
    }
    
    /**
     * Get the secondary reviewer.
     */
    public function getSecondaryReviewers() {
    	$reviewers = $this->getReviewers();
    	$array = array();
    	foreach($reviewers as $reviewer) {
    		if($reviewer->isPrimary == PerformanceReviewReviewer::IS_NOT_PRIMARY_REVIEWER
    				&& $reviewer->isDeleted == PerformanceReviewReviewer::IS_NOT_DELETED ){
    			array_push($array, $reviewer);
    		}
    	}
    	return $array;
    }
	/**
     * Get the secondary reviewer.
     */
    public function getSecondaryReviewersList() {
    	$reviewers = $this->getReviewers();
    	$array = array();
    	foreach($reviewers as $reviewer) {
    		if($reviewer->isPrimary == PerformanceReviewReviewer::IS_NOT_PRIMARY_REVIEWER
    				&& $reviewer->isDeleted == PerformanceReviewReviewer::IS_NOT_DELETED ){
    			array_push($array, $reviewer->getReviewer()->getFirstAndLastNames());
    		}
    	}
    	if(!empty($array))
    		return implode(', ',$array);
    	else if(!empty($reviewers)){
    		$reviewId=$reviewers[0]->getReviewId();
    		return $this->getPerformanceReviewService()->getSecodaryReviewerByReviewId($reviewId);
    	}else{
    		return $array;
    	}
    		
    		
    }
    
    /**
     * Get the primary reviewer
     */
    public function getPrimaryReviewer() {
    	$reviewers = $this->getReviewers();
    	foreach($reviewers as $reviewer) {
    		if($reviewer->isPrimary == PerformanceReviewReviewer::IS_PRIMARY_REVIEWER 
    				&& $reviewer->isDeleted == PerformanceReviewReviewer::IS_NOT_DELETED ){
    			return $reviewer;
    		}
    	}
    	if(!empty($reviewers)){
    		$reviewId=$reviewers[0]->getReviewId();
    		return $this->getPerformanceReviewService()->getPrimaryReviewerByReviewId($reviewId);
    	}else{
    		return null;
    	}   	
    }
    /**
     * Get all non-deleted reviewers. USE this method to get reviewers
     */
    public function getReviewers(){
	    $reviewers = $this->getPerformanceReviewReviewer();
	    $array = array();
	    foreach($reviewers as $reviewer) {
	    	if($reviewer->isDeleted == PerformanceReviewReviewer::IS_NOT_DELETED){
	    		if($reviewer->isPrimary == PerformanceReviewReviewer::IS_PRIMARY_REVIEWER){
	    			array_unshift($array,$reviewer);
	    		}else{
	    			array_push($array, $reviewer);
	    		}
	    		
	    	}
	    }
	    return $array;
    }
    /* Added By: Shagupta Faras
       DESC: To display all reviewer's (including deleted reviewer) comment
    */
    public function getAllReviewers(){
        $reviewers = $this->getPerformanceReviewReviewer();
        $array = array();
        $isdeleted = array();
        foreach($reviewers as $reviewer) {
            if($reviewer->isDeleted == PerformanceReviewReviewer::IS_NOT_DELETED){            
                if($reviewer->isPrimary == PerformanceReviewReviewer::IS_PRIMARY_REVIEWER){
                    array_unshift($array,$reviewer);
                }else{
                    array_push($array, $reviewer);
                }
            }else{
                if($reviewer->isPrimary == PerformanceReviewReviewer::IS_PRIMARY_REVIEWER){
                    array_unshift($isdeleted,$reviewer);
                }else{
                    array_push($isdeleted, $reviewer);
                }
            }
        }
        $array = array_merge($array, $isdeleted);
        return $array;
    }
    
    public function getPrimaryReviewerJSON() {
    	$reviewers = $this->getReviewers();
    	$jsonString = array();
    	foreach($reviewers as $reviewer) {
    		if($reviewer->isPrimary == PerformanceReviewReviewer::IS_PRIMARY_REVIEWER 
    				&& $reviewer->isDeleted == PerformanceReviewReviewer::IS_NOT_DELETED ){
    		    $jsonString[] = array('name' => trim($reviewer->getReviewer()->getFirstAndLastNames()), 'id' => $reviewer->getReviewer()->getEmpNumber());
    		}
    	}
    	return json_encode($jsonString, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    
    public function getSecondaryReviewersJSON() {
        $reviewers = $this->getReviewers();
        $array = array();
    	foreach($reviewers as $reviewer) {
    		if($reviewer->isPrimary == PerformanceReviewReviewer::IS_NOT_PRIMARY_REVIEWER
    				&& $reviewer->isDeleted == PerformanceReviewReviewer::IS_NOT_DELETED ){
    		    $jsonString[] = array('name' => trim($reviewer->getReviewer()->getFirstAndLastNames()), 'id' => $reviewer->getReviewer()->getEmpNumber());
    		}
    	}
    	return json_encode($jsonString, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    }
    
    public function getEmpJobTitle(){
    	$empNumber = $this->getEmployeeId();
    	$employeeService = $this->getEmployeeService();
    	$employee = $employeeService->getEmployee($empNumber);
    	return $employee->getJobTitle()->getJobTitleName();
    }
    
    public function getEmpFinalRating(){
    	$finalRating = $this->getFinalRating();
    	$status = $this->getState();
    	if($status > PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED) {
    		return $finalRating;
    	}else{
    		return null;
    	}
    }
}