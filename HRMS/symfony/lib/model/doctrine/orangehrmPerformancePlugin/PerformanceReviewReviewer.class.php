<?php

/**
 * PerformanceReviewReviewer
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    orangehrm
 * @subpackage model
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
class PerformanceReviewReviewer extends BasePerformanceReviewReviewer
{
	private $performanceKpiService;
	const IS_DELETED = 1;
	const IS_NOT_DELETED = 0;
	const IS_PRIMARY_REVIEWER = 1;
	const IS_NOT_PRIMARY_REVIEWER = 0;
	
	/**
	 * Get Job Service
	 */
	public function getPerformanceKpiService() {
		$this->performanceKpiService = new PerformanceKpiService();
		return $this->performanceKpiService;
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
		
	public function isPrimaryReviewer(){
		$primaryReviewer = ($this->getIsPrimary() == 1) ? true : false;
		return  $primaryReviewer;
	}
	
	
}