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

/**
 * Form class for Save Education
 */
class SaveReviewForm extends BaseForm {
	
	private $performanceReviewService = null;
	public $existingReviewerListAsJSON = array();
	public $existingPrimaryReviewerAsJSON = array();
	
	/**
	 * 
	 * @return type 
	 */
	public function getPerformanceReviewService() {
		if (is_null($this->performanceReviewService)) {
			$this->performanceReviewService = new PerformanceReviewService();
			$this->performanceReviewService->setPerformanceReviewDao(new PerformanceReviewDao());
		}
		return $this->performanceReviewService;
	}
	
	public function configure() {
		$this->setWidgets(array(
			'reviewId' => new sfWidgetFormInputHidden(),
			'employeeName' => new ohrmWidgetEmployeeNameAutoFill(),
			'reviewer' => new sfWidgetFormInput(),
			'reviewers' => new sfWidgetFormInput(),
		));
		
		
		$inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
		$this->setValidators(array(
			'reviewId' => new sfValidatorString(array('required' => false)),
			'employeeName' => new ohrmValidatorEmployeeNameAutoFill(array('required' => true)),
			'reviewer'=> new sfValidatorString(array('required' => false)),
			'reviewers'=> new sfValidatorString(array('required' => false))
		));
		
		
		$this->__setDefaultValues();
		
		$this->getWidgetSchema()->setLabels($this->getFormLabels());
		
		$this->widgetSchema->setNameFormat('saveReview[%s]');
	}
	
	private function __setDefaultValues() {
		$reviewId = $this->getOption('reviewId');
		if (!empty($reviewId)) {
			$reviewArr = array('id' => $reviewId);
			$review = $this->getPerformanceReviewService()->readPerformanceReview($reviewArr);
			$employee = array(
				'empName' => $review->getEmployee()->getFirstAndLastNames(),
				'empId' => $review->getEmployee()->getEmpNumber()
			);
			
			$this->setDefaults(array(
				'reviewId' => $review->getId(),
				'employeeName' => $employee
			));
			
			$reviewers = $review->getSecondaryReviewers();
			
			$existingReviewers = array();
			foreach ($reviewers as $reviewer) {
				$existingReviewers[] = array( 'name' => $reviewer->getReviewer()->getFirstAndLastNames() , 'id' => $reviewer->getReviewer()->getEmpNumber());
			}
			$this->existingReviewerListAsJSON = json_encode($existingReviewers);
			
			$primaryReviewer = $review->getPrimaryReviewer();
			$primaryReviewersArray = array();
			if(!is_null($primaryReviewer)){
				$primaryReviewersArray[] = array( 'name' => $primaryReviewer->getReviewer()->getFirstAndLastNames() , 'id' => $primaryReviewer->getReviewer()->getEmpNumber());
			}
			
			$this->existingPrimaryReviewerAsJSON = json_encode($primaryReviewersArray);
			
		}
		if ($this->getOption('redirect')) {
			$employee = array(
				'empName' => $this->getOption('empName'),
				'empId' => $this->getOption('empId')
			);
			$this->setDefaults(array(
				'employeeName' => $employee,
			));
		}
	}
	
	protected function getFormLabels() {
		$required = '<em> *</em>';
		$labels = array(
			'employeeName' => __('Employee Name') . $required,
			'reviewer' => __('Primary Reviewer') . $required,
			'reviewers' => __('Secondary Reviewer(s)'),
		);
		return $labels;
	}
	
	public function getEmployeeListAsJson() {
		$employeeService = new EmployeeService();
		return $employeeService->getEmployeeListAsJson();
	}
	
	public function getCurrentCycle(){
		$dateArray = array();
		$reviewId = $this->getOption('reviewId');
		if (!empty($reviewId)) {
			$reviewArr = array('id' => $reviewId);
			$review = $this->getPerformanceReviewService()->readPerformanceReview($reviewArr);
			$dateArray = array('from'=>$review->getPeriodFrom(), 'to'=>$review->getPeriodTo(), 'dueDate'=> $review->getDueDate());
		}else{
			$performancePeriod = $this->getPerformanceReviewService()->getCurrentPerformancePeriod();
			$dateArray = array('from'=>$performancePeriod->getPeriodFrom(), 'to'=>$performancePeriod->getPeriodTo(), 'dueDate'=> $performancePeriod->getDueDate());
		}
		return $dateArray;
	}	
		
}