<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PerforamanceReviewDaoTest
 *
 * @author sujata
 */

/**
 * @group performance
 */

require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

class PerforamanceReviewDaoTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        TestDataService::truncateTables(array('PerformanceReview', 'EmployeeMultiSourceFeedback', 'Employee'));
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmPerformancePlugin/test/fixtures/performance_reviews.yml');
    }

    public function testSavePerformanceReview() {

        $dao = new PerformanceReviewDao();

        $review = new PerformanceReview();
        $review->setId(1);
        $review->setEmployeeId(1);

        $review = $dao->savePerformanceReview($review);
        $this->assertEquals(1, $review->getId());
    }
    
    public function testIsReviewerExist() {
    	$dao = new PerformanceReviewDao();
    	$this->assertTrue($dao->isReviewerExist(5, 1));
    } 
    
    public function testUpdateReviewer() {
    	$dao = new PerformanceReviewDao();
    	$this->assertEquals(1, $dao->updateReviewer(7,3,true));
    }
    
    public function testDeleteReviewer() {
    	$dao = new PerformanceReviewDao();
    	$this->assertEquals(1, $dao->deleteReviewer(5,1));
    }
    
    public function testDeletePerformanceReview() {
    	$dao = new PerformanceReviewDao();
    	$this->assertFalse($dao->deletePerformanceReview(array(1)));
    }
    
    public function testDeletePerformanceReview1() {
    	$dao = new PerformanceReviewDao();
    	$this->assertTrue($dao->deletePerformanceReview(array(4,5)));
    }
    
    public function testReadPerformanceReview() {
    	$dao = new PerformanceReviewDao();
    	$orderby['orderBy'] ='employeeId';
    	$searchParams ['jobTitleCode'] = 1;
    	$this->assertEquals(1, sizeof($dao->readPerformanceReview($searchParams, $orderby)));
    }
    
    public function testReadPerformanceReview1() {
    	$dao = new PerformanceReviewDao();
    	$this->assertEquals(4, sizeof($dao->readPerformanceReview(array())));
    }
    
    public function testReadPerformanceReview2() {
        $dao = new PerformanceReviewDao();
        $searchParams ['jobTitleCode'] = 2;
        $this->assertEquals(3, sizeof($dao->readPerformanceReview($searchParams)));
    }
   
    public function testReadPerformanceReview3() {
        $dao = new PerformanceReviewDao();
        $searchParams ['jobTitleCode'] = 2;
        $this->assertEquals(3, sizeof($dao->readPerformanceReview($searchParams)));
    }
            
    public function testReadPerformanceReview4() {
        $dao = new PerformanceReviewDao();
        $searchParams ['id'] = 5;
        $result = $dao->readPerformanceReview($searchParams);
        $this->assertEquals(5, $result['id']);
    }
    
    public function testReadPerformanceReview5() {
        $dao = new PerformanceReviewDao();
        $searchParams ['employeeName'] = 'Kayla';
        $this->assertEquals(1, count($dao->readPerformanceReview($searchParams)));
    }
    
    public function testReadPerformanceReview6() {
        $dao = new PerformanceReviewDao();
        $searchParams ['from'] = '2011-01-02';
        $this->assertEquals(2, count($dao->readPerformanceReview($searchParams)));
    }
    
    public function testReadPerformanceReview7() {
        $dao = new PerformanceReviewDao();
        $searchParams ['to'] = '2011-01-01';
        $this->assertEquals(2, count($dao->readPerformanceReview($searchParams)));
    }
    
    public function testReadPerformanceReview8() {
        $dao = new PerformanceReviewDao();
        $searchParams ['employeeNumber'] = 1;
        $searchParams ['status'] = 1;
        $this->assertEquals(1, count($dao->readPerformanceReview($searchParams)));
    }
    
    public function testReadPerformanceReview9() {
        $dao = new PerformanceReviewDao();
        $searchParams ['employeeNotIn'] = 1;
        $this->assertEquals(3, count($dao->readPerformanceReview($searchParams)));
    }
    
    public function testReadPerformanceReview10() {
        $dao = new PerformanceReviewDao();
        $searchParams ['reviewerId'] = 2;
        $this->assertEquals(2, count($dao->readPerformanceReview($searchParams)));
    }
    
    public function testReadPerformanceReview11() {
        $dao = new PerformanceReviewDao();
        $searchParams ['limit'] = 1;
        $orderby['orderBy'] = 'dueDate';
        $this->assertEquals(1, count($dao->readPerformanceReview($searchParams, $orderby)));
    }
    public function testGetPerformanceReviewsByDate() {
    	$dao = new PerformanceReviewDao();
    	$fromDate = '2011-01-02';
    	$toDate = '2011-01-03';
    	$this->assertEquals(2, count($dao->getPerformanceReviewsByDate($fromDate,$toDate)));
    }
    
    public function testCheckReviewExist() {
    	$dao = new PerformanceReviewDao();
    	$fromDate = '2011-01-02';
    	$toDate = '2011-01-03';
    	$this->assertTrue($dao->checkReviewExist($fromDate, $toDate));
    }
    
    public function testGetPerformanceReviewList() {
    	$dao = new PerformanceReviewDao();
    	$this->assertEquals(4, sizeof($dao->getPerformanceReviewList()));
    }
    
    public function testGetPerformanceCycleDate() {
    	$dao = new PerformanceReviewDao();
    	$this->assertEquals(3, sizeof($dao->getPerformanceCycleDate()));
    }
    
    public function testSearchPerformanceReview() {
    	$dao = new PerformanceReviewDao();
    	$clues['jobCode'] = 2;
    	$clues['empId'] = 2;
    	$this->assertEquals(2, count($dao->searchPerformanceReview($clues)));
    }
    
    public function testCountReviews() {
    	$dao = new PerformanceReviewDao();
    	$clues['jobCode'] = 2;
    	$clues['empId'] = 2;
    	$this->assertEquals(2,$dao->countReviews($clues));
    }
    
    public function testSaveFeedback() {
    	$dao = new PerformanceReviewDao();
    	$feedback = new EmployeeMultiSourceFeedback(); 
    	$feedback->fromDate = '2014-01-01';
    	$feedback->toDate = '2014-01-06';
    	$feedback->empNumber = 4;
    	$feedback->reviewersNumber = 5;
    	$this->assertTrue(($dao->saveFeedback($feedback) > 0 )? true : false );
    }
	
    public function testDeleteFeedback() {
    	$dao = new PerformanceReviewDao();
    	$this->assertTrue($dao->deleteFeedback(array(1,2)));
    }
    
    public function testDiscardFeedback() {
    	$dao = new PerformanceReviewDao();
    	$this->assertTrue(($dao->discardFeedback(3) > 0)? true : false);
    }
    
    public function testGetAllFeedback() {
    	$dao = new PerformanceReviewDao();
    	$fromDate = '2011-01-01';
    	$toDate = '2011-01-03';
    	$this->assertEquals(3, count($dao->getAllFeedback($fromDate, $toDate)));
    }
    
    public function testGetMultiSourceFeedback() {
    	$dao = new PerformanceReviewDao();
    	$fromDate = '2011-01-01';
    	$toDate = '2011-01-03';
    	$empID = 1;
    	$this->assertEquals(1, count($dao->getMultiSourceFeedback($fromDate, $toDate, $empID)));
    }
    
    public function testGetMultiSourceFeedbackByReviewer() {
    	$dao = new PerformanceReviewDao();
    	$fromDate = '2011-01-01';
    	$toDate = '2011-01-03';
    	$reviewerID = 1;
    	$this->assertEquals(1, count($dao->getMultiSourceFeedbackByReviewer($fromDate, $toDate, $reviewerID)));
    }
    
    public function testApprovePerformanceReviews() {
    	$dao = new PerformanceReviewDao();
    	$this->assertEquals(3, $dao->approvePerformanceReviews(array(1,2,3)));
    }
    
    public function testGetLatestPerformancePeriodCycle() {
    	$dao = new PerformanceReviewDao();
    	$this->assertTrue(!empty($dao->getLatestPerformancePeriodCycle()->getPeriodFrom()));
    }
    
    public function testGetReviewIdsForReviewer() {
    	$dao = new PerformanceReviewDao();
    	$fromDate = '2011-01-01';
    	$toDate = '2011-01-01';
    	$reviewerID = 1;
    	$this->assertEquals(1, count($dao->getReviewIdsForReviewer($reviewerID, true, $fromDate, $toDate)));
    }
    public function testGetEmployeeCurrentCyclePerformance() {
    	$dao = new PerformanceReviewDao();
    	$this->assertEquals(1, count($dao->getEmployeeCurrentCyclePerformance(1)));
    }
    
    public function testGetEmployeeCurrentCyclePrimaryReviwer() {
    	$dao = new PerformanceReviewDao();
    	$this->assertTrue(empty($dao->getEmployeeCurrentCyclePrimaryReviwer(5)));
    }
    
}
