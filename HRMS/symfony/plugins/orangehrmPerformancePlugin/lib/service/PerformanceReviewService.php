<?php
/**
 * Service Class for Performance Review
 *
 */
class PerformanceReviewService extends BaseService {

   private $performanceReviewDao = null;
   
   const EMAIL_TEMPLATE_REVIWER_SUBMIT     =   'performance_submit.txt';
   const EMAIL_TEMPLATE_HRADMIN_APPROVE    =   'performance_approve.txt';
   const EMAIL_TEMPLATE_HRADMIN_REJECT     =   'performance_reject.txt';
   const EMAIL_TEMPLATE_ADD_REVIEW         =   'add-review.txt';

  /**
   * Setting the PerformanceReviewDao
   * @param PerformanceReviewDao dao
   */
   public function setPerformanceReviewDao(PerformanceReviewDao $dao) {
      $this->performanceReviewDao = $dao;
   }

   /**
    * Return PerformanceReviewDao Instance
    * @returns PerformanceReviewDao
    */
   public function getPerformanceReviewDao() {
      return $this->performanceReviewDao;
   }

   /**
    * Save PerformanceReview
    * @param PerformanceReview $performanceReview
    * @returns PerformanceReview
    * @throws PerformanceServiceException
    */
   public function savePerformanceReview(PerformanceReview $performanceReview, $performanceReviewerList=array(), $removedReviewerList=array(), $primaryReviewerId=null) {
      try{
         return $this->performanceReviewDao->savePerformanceReview($performanceReview, $performanceReviewerList, $removedReviewerList, $primaryReviewerId);
      } catch(Exception $e) {
         throw new PerformanceServiceException($e->getMessage());
      }
   }

    /**
     * Read Performance Review
     * @param int $reviewId
     * @return PerformanceReview
     * @throws PerformanceServiceException
     */
    public function readPerformanceReview($reviewArr=array()) {
        try {
            return $this->performanceReviewDao->readPerformanceReview($reviewArr);
        } catch(Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
        }
    }
    
    /**
     * Return performace list by date
     */
    public function getPerformanceReviewsByDate($fromDate, $toDate, $employeeId = null){
    	try {
            return $this->performanceReviewDao->getPerformanceReviewsByDate($fromDate, $toDate, $employeeId);
        } catch(Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
        }
    }
    public function checkReviewExist($fromDate, $toDate, $employeeId = null){
    	try {
            return $this->performanceReviewDao->checkReviewExist($fromDate, $toDate, $employeeId);
        } catch(Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
        }
    }
    public function getPerformanceCycleDate(){
	    try{
		    return $this->performanceReviewDao->getPerformanceCycleDate();
	    } catch(Exception $e) {
		    throw new PerformanceServiceException($e->getMessage());
	    }
    }

    /**
     * Delete PerformanceReview
     * @param array $reviewList
     * @returns boolean
     * @throws PerformanceServiceException
     */
    public function deletePerformanceReview($reviewList) {
      try {
         return $this->performanceReviewDao->deletePerformanceReview($reviewList);
      } catch(Exception $e) {
         throw new PerformanceServiceException($e->getMessage());
      }
    }

    /**
     * Get All PerformanceReviews
     */
    public function getPerformanceReviewList() {
      try {
         return $this->performanceReviewDao->getPerformanceReviewList();
      } catch(Exception $e) {
         throw new PerformanceServiceException($e->getMessage());
      }
    }

    /**
     * Search for PerformanceReviews on multiple criteria
     * @param array $searchParam
     * @param $offset
     * @param $limit
     * @returns Collection
     * @throws PerformanceServiceException
     */
    public function searchPerformanceReview($clues, $offset=null, $limit=null) {

      try {
         return $this->performanceReviewDao->searchPerformanceReview($clues, $offset, $limit);
      } catch(Exception $e) {
         throw new PerformanceServiceException($e->getMessage());
      }

    }

    /**
     * Counting the reviews
     * @param array $searchParam
     * @returns int
     * @throws PerformanceServiceException
     */
    public function countReviews($clues) {
        
        try {
            return $this->performanceReviewDao->countReviews($clues);
        } catch(Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
        }

    }

    /**
     * Save Performance Review
     * @param PerformanceReview $performanceReview
     * @return PerformanceReview
     */
    public function changePerformanceStatus(PerformanceReview $performanceReview, $status){
	    try {
		    $this->performanceReviewDao->updatePerformanceReviewStatus($performanceReview, $status );
			    //send the notification mail.
			    $performanceMailer = new PerformanceMailer();
			    $performanceMailer->sendNotifications($performanceReview->getId());	
	    } catch (Exception $e) {
		    throw new PerformanceServiceException($e->getMessage());
	    }
    }

    /**
     * Add New comments to performance review
     * @return unknown_type
     */
    public function addComment( PerformanceReview $performanceReview ,$comment ,$user){
        
        try {
        
            $performanceReviewComment = new PerformanceReviewComment();

            $performanceReviewComment->setPrId($performanceReview->getId());
            $performanceReviewComment->setComment($comment);
            if(is_numeric($user)) {
            	$performanceReviewComment->setEmployeeId($user);
            }
            
            $performanceReviewComment->setCreateDate(date('Y-m-d'));
            $performanceReviewComment->save();

        } catch ( Exception $e) {
            throw new AdminServiceException($e->getMessage());
        }
    }

  

    /**
     * Checks whether the given employee is a reviewer
     */

    public function isReviewer($empId, $clues) {

        try {
        	$from = $clues['from'];
        	$to = $clues['to'];
            $reviews = $this->performanceReviewDao->getReviewIdsForReviewer($empId, false, $from , $to);
            if (count($reviews) > 0) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new PerformanceServiceException($e->getMessage());
        }

    }

    /**
     * Get reviewee list of given reviewer as json
     * If $addSelf is true then reviewer's details
     * will also be added.
     */
    public function getRevieweeListAsJson($reviewerId, $addSelf = false) {

        $jsonString = array();
        $resultList = $this->performanceReviewDao->searchPerformanceReview(array('reviewerId' => $reviewerId));
        $employeeUnique = array();
        foreach ($resultList as $reviewee) {
        	$empNumber = $reviewee->getEmployee()->getEmpNumber();
        	$name = $reviewee->getEmployee()->getFirstAndLastNames();
        	
        	if (!isset($employeeUnique[$empNumber])) {
        		$employeeUnique[$empNumber] = $name;
            	$jsonString[] = array('name' => $name, 'id' => $empNumber);
        	}
        }
        if ($addSelf) {
        	foreach (  $resultList[0]->getReviewers() as $reviewer ) {
        		if($reviewer->getReviewerId() == $reviewerId){
        			$jsonString[] = array('name' => $reviewer->getReviewer()->getFirstAndLastNames(), 'id' => $reviewer->getReviewer()->getEmpNumber());	
        		}
			}
        }
        return json_encode($jsonString);

    }

    public function saveFeedback(EmployeeMultiSourceFeedback $feedback) {
    	return  $this->getPerformanceReviewDao()->saveFeedback($feedback);
    }
    
    public function updateFeedback(EmployeeMultiSourceFeedback $feedback, $fid) {
    	return  $this->getPerformanceReviewDao()->updateFeedback($feedback, $fid);
    }
    /*Added by Sujata*/
    public function deleteFeedback($fidList) {
    	return  $this->getPerformanceReviewDao()->deleteFeedback($fidList);
    }
    public function discardFeedback($fid) {
    	return  $this->getPerformanceReviewDao()->discardFeedback($fid);
    }
    
    public function getFeedbackById($id) {
    	return $this->getPerformanceReviewDao()->getFeedbackById($id);
    }
    
    // for 360 feedback
    public function getReviewedEmployees($usrNumber) {
    	return $this->getPerformanceReviewDao()->getReviewedEmployees($usrNumber);
    }
    
    public function searchFeedback($searchParam) {
    	$searchFeedbackQuery = $this->getPerformanceReviewDao()->buildFeedbackSearchQuery($searchParam, $isCount = false, $this->getCurrentPerformancePeriod());
    	return $this->getPerformanceReviewDao()->searchFeedback($searchFeedbackQuery);
    }
    
    public function getCountSearchFeedback($searchParam) {
    	$searchFeedbackQuery = $this->getPerformanceReviewDao()->buildFeedbackSearchQuery($searchParam, true);
    	return $this->getPerformanceReviewDao()->searchFeedback($searchFeedbackQuery, true);
    }
    
    public function getAllFeedback($fromDate, $toDate, $employeeId) {
    	return $this->getPerformanceReviewDao()->getAllFeedback($fromDate, $toDate, $employeeId);
    }
    
    public function updateReviewer($reviewID, $reviewerID, $updatePrimary) {
    	return $this->getPerformanceReviewDao()->updateReviewer($reviewID, $reviewerID, $updatePrimary);
    }
    
     
    /**
     * Return performance attachments
     * @param <type> $id
     */
    public function getPerformanceAttachments($id){
	    return $this->getPerformanceReviewDao()->getPerformanceAttachments($id);
    }
    
     public function getPerformanceAttachment($attachId) {
     	 return $this->getPerformanceReviewDao()->getPerformanceAttachment($attachId);
     }
     
     public function getMultiSourceFeedback($fromDate, $toDate, $employeeId, $reviewerId = null) {
     	return $this->getPerformanceReviewDao()->getMultiSourceFeedback($fromDate, $toDate, $employeeId,$reviewerId);
     }
     
     public function getMultiSourceFeedbackByReviewer($fromDate, $toDate, $reviewerId) {
	     return $this->getPerformanceReviewDao()->getMultiSourceFeedbackByReviewer($fromDate, $toDate, $reviewerId);
     }
     
     public function approvePerformanceReviews($idsToApprove) {
     	return $this->getPerformanceReviewDao()->approvePerformanceReviews($idsToApprove);
     }
     
     /**
      * Get current performance cycle
      */
     public function getCurrentPerformancePeriod(){
	     return $this->getPerformanceReviewDao()->getCurrentPerformancePeriod();
     }
     
     /**
      * Get all performance periods
      */
     public function getAllPerformancePeriods(){
	     return $this->getPerformanceReviewDao()->getAllPerformancePeriods();
     }
     
     public function getPerformancePeriodByDate($fromDate,$toDate){
     	return $this->getPerformanceReviewDao()->getPerformancePeriodByDate($fromDate,$toDate);
     }
     
     /**
      * Get last performance cycle 
      */
     public function getLastPerformancePeriod($periodFrom){
     	$currentDate = new DateTime($periodFrom);
     	$currentDate->sub(new DateInterval('P1M'));
     	$currentDate = strtotime($currentDate->format('Y-m-d'));
     	$performancePeriods = $this->getAllPerformancePeriods();
     	$lastPerformancePeriod = null;
     	foreach ( $performancePeriods as $performancePeriod ) {
	     	$periodFrom = strtotime($performancePeriod->getPeriodFrom());
	     	$periodTo = strtotime($performancePeriod->getPeriodTo());
	     	if($periodFrom <= $currentDate && $currentDate <= $periodTo ){
		     	$lastPerformancePeriod = $performancePeriod;
		 		break;	    	
	     	}	
     	}
     	return $lastPerformancePeriod;
     }
     
     public function getLatestPerformancePeriodCycle() {
     	return $this->getPerformanceReviewDao()->getLatestPerformancePeriodCycle();
     }
     
     public function checkForUniquePerformanceCycle($performancePeriod) {
     	return $this->getPerformanceReviewDao()->checkForUniquePerformanceCycle($performancePeriod);
     }
     
     public function savePreviousObjective($reviewId, $goal) {
     	return $this->getPerformanceReviewDao()->savePreviousObjective($reviewId, $goal);
     }
     
     public function getEmpNumbersForFeedback($currCycle) {
     	return $this->getPerformanceReviewDao()->getEmpNumbersForFeedback($currCycle);
     }
     public function getEmployeeCurrentCyclePerformance($empNumber) {
     	return $this->getPerformanceReviewDao()->getEmployeeCurrentCyclePerformance($empNumber);
     }
     public function getEmployeeCurrentCyclePrimaryReviwer($empNumber) {
     	return $this->getPerformanceReviewDao()->getEmployeeCurrentCyclePrimaryReviwer($empNumber);
     }
     
     public function removePrimaryReviewerStatus($empNumber,$selectedPrimaryReviwer) {
     	return $this->getPerformanceReviewDao()->removePrimaryReviewerStatus($empNumber,$selectedPrimaryReviwer);
     }
     
     public function getPrimaryReviewerByReviewId($reviewId) {
     	return $this->getPerformanceReviewDao()->getPrimaryReviewerByReviewId($reviewId);
     }
     
     public function getSecodaryReviewerByReviewId($reviewId) {
     	return $this->getPerformanceReviewDao()->getSecodaryReviewerByReviewId($reviewId);
     }
     public function getEmployeePerformanceReview($empNumber,$fromDate,$toDate) {
     	return $this->getPerformanceReviewDao()->getEmployeePerformanceReview($empNumber,$fromDate,$toDate);
     }
}
