<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PerforamanceReviewServiceTest
 *
 * @author sujata
 */

/**
 * @group performance
 */
class PerforamanceReviewServiceTest extends PHPUnit_Framework_TestCase {

    public function testSavePerformanceReview() {

        $review = new PerformanceReview();
        $daoMock = $this->getMock("PerformanceReviewDao", array("savePerformanceReview"));
        $daoMock->expects($this->any())
                ->method('savePerformanceReview')
                ->will($this->returnValue($review));

        $service = new PerformanceReviewService();
        $service->setPerformanceReviewDao($daoMock);

        $review = $service->savePerformanceReview($review);
        $this->assertTrue(is_object($review));
    }

    public function testReadPerformanceReview() {

        $review = new PerformanceReview();
        $daoMock = $this->getMock("PerformanceReviewDao", array("readPerformanceReview"));
        $daoMock->expects($this->any())
                ->method('readPerformanceReview')
                ->will($this->returnValue(array($review)));

        $service = new PerformanceReviewService();
        $service->setPerformanceReviewDao($daoMock);

        $reviews = $service->readPerformanceReview(array('id' => 1));
        $this->assertEquals(1, sizeof($reviews));
    }

    public function testDeletePerformanceReview() {

        $daoMock = $this->getMock("PerformanceReviewDao", array("deletePerformanceReview"));
        $daoMock->expects($this->any())
                ->method('deletePerformanceReview')
                ->will($this->returnValue(true));

        $service = new PerformanceReviewService();
        $service->setPerformanceReviewDao($daoMock);

        $reviews = $service->deletePerformanceReview(array(4));
        $this->assertEquals(1, $service->deletePerformanceReview(4));
    }
}
