<?php
class reviewPerformanceAction extends sfAction{ 
	
	private $performanceKpiService;
	private $kpiService;
	private $performanceReviewService;
	private $employeeService;
	
	public function getEmployeeService() {
		if (is_null($this->employeeService)) {
			$empService = new EmployeeService();
			$empService->setEmployeeDao(new EmployeeDao());
			$this->employeeService = $empService;
		}
		return $this->employeeService;
	}
	
	/**
	 * Sets EmployeeService
	 * @param EmployeeService $service
	 */
	public function setEmployeeService(EmployeeService $service) {
		$this->employeeService = $service;
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
	 * Get performance Service
	 */
	public function getPerformanceReviewService() {
		$this->performanceReviewService = new PerformanceReviewService();
		$this->performanceReviewService->setPerformanceReviewDao(new PerformanceReviewDao());
		return $this->performanceReviewService;
	}
	
	/**
	 * Set Performance Service
	 * @param PerformanceReviewService $performanceReviewService
	 * @return unknown_type
	 */
	public function setPerformanceReviewService(PerformanceReviewService $performanceReviewService) {
		$this->performanceReviewService = $performanceReviewService;
	}
	
	/**
	 * This method is executed before each action
	 */
	public function preExecute() {
	
		if (!empty($_SESSION['empNumber'])) {
			$this->loggedEmpId = $_SESSION['empNumber'];
		} else {
			$this->loggedEmpId = 0; // Means default admin
		}
	
		if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
			$this->loggedAdmin = true;
		} else {
			$this->loggedAdmin = false;
		}
		
		if (isset($_SESSION['user'])) {
			$this->loggedUserId = $_SESSION['user'];
		}
	}
	
	
	public function execute($request){
		/* For highlighting corresponding menu item */
		$request->setParameter('initialActionName', 'viewReview');
	
		$this->form = new PerformanceReviewForm(array(), array(), true);
	
		$id = $request->getParameter('id');
        $reviewArr = array('id' => $id);
        $performanceReviewService = $this->getPerformanceReviewService();
        $performanceReview = $performanceReviewService->readPerformanceReview($reviewArr);
	
		$performanceKpiService = $this->getPerformanceKpiService();
		$this->_checkPerformanceReviewAuthentication($performanceReview);
	
		$this->performanceReview = $performanceReview;
	
		//Get last cycle details
		$performancePeriod = $performanceReviewService->getLastPerformancePeriod($performanceReview->getPeriodFrom());
		if(!is_null($performancePeriod)){
			$this->lastPerformanceList = $performanceReviewService->getPerformanceReviewsByDate($performancePeriod->getPeriodFrom(),$performancePeriod->getPeriodTo(),$performanceReview->getEmployeeId());
		}
		$this->selfMultiFeedbackList = $performanceReviewService->getMultiSourceFeedback($performanceReview->getPeriodFrom(),$performanceReview->getPeriodTo(), $performanceReview->getEmployeeId());
		$this->givenMultiFeedbackList = $performanceReviewService->getMultiSourceFeedbackByReviewer($performanceReview->getPeriodFrom(),$performanceReview->getPeriodTo(), $performanceReview->getEmployeeId());
		$this->feedbackCount = count($this->givenMultiFeedbackList);
		$performanceKpiList = $performanceKpiService->getPerformanceKpiList($performanceReview->getKpis());
		$performanceRatings = $performanceKpiService->getPerformanceRatingList($performanceReview->getRatings());
	
		/* Get All team members of projects has been worked in current performance cycle period. */
		$CurrentCycleObj=$performanceReviewService->getEmployeeCurrentCyclePerformance($performanceReview->getEmployeeId());
		$employeeService = new EmployeeService();
		$peerEmployees = $employeeService->getCurrentProjectTeamMember($performanceReview->getEmployeeId(),$CurrentCycleObj);
		$feedbackEmpIds = $performanceReviewService->getAllFeedback($performanceReview->getPeriodFrom(),$performanceReview->getPeriodTo(), $performanceReview->getEmployeeId());
	
		$flag = false;
		foreach ($feedbackEmpIds as $feedbackEmpId){
			if(array_key_exists($feedbackEmpId->getEmpNumber(), $peerEmployees)){
				$flag = true;
			}
		}
		$this->hasPeerEmployeeFeedback = $flag;
		$this->reviewerNames = $this->getReviewerNames($performanceReview);
		$this->isHrAdmin = $this->isHrAdmin();
		$this->isReviwer = $this->isReviwer($performanceReview);
		$this->isPrimary = $this->isPrimaryReviewer($performanceReview);
		$this->showReviewers = $this->showReviewers($performanceReview);
		$this->primaryReviewer = $performanceReview->getPrimaryReviewer();
		$this->secondaryReviewer = $performanceReview->getSecondaryReviewers();
		$this->reviewReviewerId = null;
		$this->isSelfReview = ($this->loggedEmpId == $performanceReview->getEmployeeId()) ? true : false ;
		foreach ($performanceReview->getReviewers() as $rev) {
			if($this->loggedEmpId == $rev->getReviewerId()) {
				$this->reviewReviewerId = $rev->getId();
			}
		}
		
		$this->currentPrimaryReviewer = $performanceReviewService->getPrimaryReviewerByReviewId($performanceReview->getId())->getReviewerId();
		$this->finalRating = ( $this->currentPrimaryReviewer == $this->loggedEmpId) ? trim($performanceReview->getFinalRating()) : trim($performanceReview->getEmpFinalRating());
		
		$this->isPrimaryReviwerCommented = $this->isPrimaryReviwerCommented($performanceReview->getPrimaryReviewer());
		 
		if ($request->isMethod('post')) {
	
			$this->form->bind($request->getParameter($this->form->getName()));
	
			if ($this->form->isValid()) {
	
				$saveMode = $request->getParameter('saveMode');
				if($saveMode == 'rejectComment' && trim($request->getParameter('reject_comments')) != $performanceReview->setRejectComments()) {
					$performanceReview->setRejectComments($request->getParameter('reject_comments'));
					$performanceReview->save();
					$mailer = new RejectCommentMailer();
					$mailer->send($performanceReview);
					$this->getUser()->setFlash('success', __('Updated Reject Appraisal reason'));
					$this->redirect('performance/reviewPerformance?id=' . $id);
				}
				if($saveMode == 'save' && !($this->isReviwer) && $this->isHrAdmin) {
					if (trim($request->getParameter('txtNotes')) != '') {
						$performanceReviewService->addComment($performanceReview, $request->getParameter('txtNotes'), $_SESSION['empNumber']);
					}
					$this->getUser()->setFlash('success', __(TopLevelMessages::UPDATE_SUCCESS));
					$this->redirect('performance/reviewPerformance?id=' . $id);
				}
				/*Add Self ratings and comments in xml string for both reviewer and employee review table.*/
				if (trim($request->getParameter('txtSelfKpiRating')) != '') {
					$performanceRatings->setSelfKpiRate($request->getParameter('txtSelfKpiRating'));
				}
				if (trim($request->getParameter('txtSelfGoalsRating')) != '') {
					$performanceRatings->setSelfGoalsRate($request->getParameter('txtSelfGoalsRating'));
				}
				if (trim($request->getParameter('txtSelfMajorAccomplishmentRating')) != '') {
					$performanceRatings->setSelfAccomplishmentRate($request->getParameter('txtSelfMajorAccomplishmentRating'));
				}
				if (trim($request->getParameter('txtSelf360FeedbackRating')) != '') {
					$performanceRatings->setSelf360FeedbackRate($request->getParameter('txtSelf360FeedbackRating'));
				}
	
				if (trim($request->getParameter('txtSelfMajorAccomplishmentComment')) != '') {
					$performanceRatings->setSelfAccomplishmentComment($request->getParameter('txtSelfMajorAccomplishmentComment'));
				}
				if (trim($request->getParameter('txtSelfGoalsComment')) != '') {
					$performanceRatings->setSelfGoalsComment($request->getParameter('txtSelfGoalsComment'));
				}
				if (trim($request->getParameter('txtSelf360FeedbackComment')) != '') {
					$performanceRatings->setSelf360FeedbackComment($request->getParameter('txtSelf360FeedbackComment'));
				}
							
				//set self review rating and comment
				if($this->isReviwer || $this->isHrAdmin){
					
					if (trim($request->getParameter('txtKpiRating')) != '') {
						$performanceRatings->setKpiRate($request->getParameter('txtKpiRating'));
					}
					
					if (trim($request->getParameter('finalRating')) != '') {
						$performanceReview->setFinalRating($request->getParameter('finalRating'));
					}else{
						$performanceReview->setFinalRating(null);
					}
	
					$performanceReview->setObjective($request->getParameter('objective'));
					$performanceReview->setLatestComment($request->getParameter('txtNotes'));
					$performanceReview->setStrongPoints($request->getParameter('strong_points'));
					
					$performanceReview->setStrongPoints($request->getParameter('strong_points'));
					$performanceReview->setNoticedImprovements($request->getParameter('noticed_improvement'));
					$performanceReview->setScopeForImprovement($request->getParameter('improvement_scope'));
				}else{
					$performanceReview->setEmployeeFeedback($request->getParameter('employee_feedback'));
				}
	
				// Update data for Self apprisal
				if($this->loggedEmpId == $performanceReview->getEmployeeId()){
					$selfRates = $request->getParameter('txtSelfRate');
					$selfComments = $request->getParameter('txtSelfComments');
					 
					if (count($selfRates)) {
						$modifyperformanceKpiList = array();
						foreach ($performanceKpiList as $performanceKpi) {
							$performanceKpi->setSelfRate($selfRates[$performanceKpi->getId()]);
							$performanceKpi->setSelfComment($selfComments[$performanceKpi->getId()]);
							array_push($modifyperformanceKpiList, $performanceKpi);
						}
						//Set self kpi
						$performanceReview->setKpis($performanceKpiService->getXml($modifyperformanceKpiList));
	
						// update self rating in reviewer
						foreach ($performanceReview->getReviewers() as $reviewer) {
							$modifyReviewerKpiList = array();
							$reviewerKpiList = $reviewer->getKpiList();
							 
							foreach ($reviewerKpiList as $performanceKpi) {
								//only update self review
								$performanceKpi->setSelfRate($selfRates[$performanceKpi->getId()]);
								$performanceKpi->setSelfComment($selfComments[$performanceKpi->getId()]);
								array_push($modifyReviewerKpiList, $performanceKpi);
							}
							$reviewer->setKpis($performanceKpiService->getXml($modifyReviewerKpiList));
						}
						/*Add ratings in review table*/
						$performanceReview->setRatings($performanceKpiService->getRatingXml($performanceRatings));
					}
					 
				}else if($this->isReviwer){ // update reviewr data
					$rates = $request->getParameter('txtReviewerRate');
					$comments = $request->getParameter('txtReviewerComments');
					foreach ($performanceReview->getReviewers() as $reviewer) {
						if($this->loggedEmpId == $reviewer->getReviewerId()){
							$modifyReviewerKpiList = array();
							$reviewerKpiList = $reviewer->getKpiList();
							foreach ($reviewerKpiList as $performanceKpi) {
								//only update reviewer rating
								$performanceKpi->setRate($rates[$performanceKpi->getId()."_".$reviewer->getId()]);
								$performanceKpi->setComment($comments[$performanceKpi->getId()."_".$reviewer->getId()]);
								array_push($modifyReviewerKpiList, $performanceKpi);
							}
							$reviewer->setKpis($performanceKpiService->getXml($modifyReviewerKpiList));
	
							$txtGoalsRate = $request->getParameter('txtGoalsRating_'.$reviewer->getReviewerId());
							$txtMajorAccomplishmentRate = $request->getParameter('txtMajorAccomplishmentRating_'.$reviewer->getReviewerId());
							$txt360FeedbackRate = $request->getParameter('txt360FeedbackRating_'.$reviewer->getReviewerId());
							
							if (trim($txtGoalsRate) != '') {
								$performanceRatings->setGoalsRate($txtGoalsRate);
							}else{
								$performanceRatings->setGoalsRate(null);
							}
							if (trim($txtMajorAccomplishmentRate) != '') {
								$performanceRatings->setAccomplishmentRate($txtMajorAccomplishmentRate);
							}else{
								$performanceRatings->setAccomplishmentRate(null);
							}
							
							if (trim($txt360FeedbackRate) != '') {
								$performanceRatings->setFeedbackRate($txt360FeedbackRate);
							}else{
								$performanceRatings->setFeedbackRate(null);
							}
							 
							$txtGoalsComment = $request->getParameter('txtGoalsComment_'.$reviewer->getReviewerId());
							$txtMajorAccomplishmentComment = $request->getParameter('txtMajorAccomplishmentComment_'.$reviewer->getReviewerId());
							$txt360FeedbackComment = $request->getParameter('txt360FeedbackComment_'.$reviewer->getReviewerId());
							 
							if (trim($txtMajorAccomplishmentComment) != '') {
								$performanceRatings->setAccomplishmentComment($txtMajorAccomplishmentComment);
							}else{
								$performanceRatings->setAccomplishmentComment(null);
							}
							if (trim($txtGoalsComment) != '') {
								$performanceRatings->setGoalsComment($txtGoalsComment);
							}else{
								$performanceRatings->setGoalsComment(null);
							}
							if (trim($txt360FeedbackComment) != '') {
								$performanceRatings->setFeedbackComment($txt360FeedbackComment);
							}else{
								$performanceRatings->setFeedbackComment(null);
							}
							
							//Add ratings and comments both employee and reviewer in xml string
							$reviewer->setRatings($performanceKpiService->getRatingXml($performanceRatings));
						}
					}
					$performanceReview->setRatings($performanceKpiService->getRatingXml($performanceRatings));
				}
				//save the review
				$performanceReview->save();
	
				switch ($saveMode) {
	
					/* case 'save':
					 if ($performanceReview->getState() != PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
					 $performanceReviewService->changePerformanceStatus($performanceReview, PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED);
					 }
					 break; */
	
					case 'submit':
						if ($performanceReview->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED) {
							$performanceReviewService->changePerformanceStatus($performanceReview, PerformanceReview::PERFORMANCE_REVIEW_STATUS_BEING_REVIWED);
						} else {
							$performanceReviewService->changePerformanceStatus($performanceReview, PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED);
						}
						break;
		
					case 'reject': $performanceReviewService->changePerformanceStatus($performanceReview, PerformanceReview::PERFORMANCE_REVIEW_STATUS_REJECTED);
						break;
		
					case 'approve': $performanceReviewService->changePerformanceStatus($performanceReview, PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED);
						break;
				}
	
				if (trim($request->getParameter('txtNotes')) != '') {
					$performanceReviewService->addComment($performanceReview, $request->getParameter('txtNotes'), $_SESSION['empNumber']);
				}
				if($saveMode == "reject"){
					$this->getUser()->setFlash('success.nofade','Successfully Rejected');
				} else {
					$this->getUser()->setFlash('success', __(TopLevelMessages::UPDATE_SUCCESS));
				}
				
				$this->redirect('performance/reviewPerformance?id=' . $id);
			}
		}
	}
	protected function _checkPerformanceReviewAuthentication($performanceReview) {
	
		if ($this->isHrAdmin()) {
			return;
		}
	
		if ($this->isReviwer($performanceReview)) {
			return;
		}
	
		$user = $this->getUser()->getAttribute('user');
	
		if ($performanceReview->getEmployeeId() == $user->getEmployeeNumber()) {
			return;
		}
	
		if (!$user->isAdmin()) {
			$this->redirect('auth/login');
		}
	}
	
	/* Added DESC: Function created to check, has Primary reviewer given rating for all KPIS? */
	public function isPrimaryReviwerCommented($primaryReviewer){
		$status=true;
		if($primaryReviewer != null) {
			foreach($primaryReviewer->getKpiList() as $kpi)
			{
				$val=$kpi->getRate();
				if(trim($val)=='') $status=false;
			}
		} else
			$status=false;
		return $status;
	}
	
	protected function isHrAdmin() {
		if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes')
			return true;
		else
			return false;
	}
	/**
	 *
	 */
	protected function showReviewers(PerformanceReview $performanceReview){
		$showReview = true;
		$user = $this->getUser()->getAttribute('user');
		if ($performanceReview->getEmployeeId() == $user->getEmployeeNumber() &&
				($performanceReview->getState() < PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED)) {
					$showReview = false;
				}
				return $showReview;
	}
	/**
	 * Is Reviewer
	 * @return unknown_type
	 */
	protected function isReviwer(PerformanceReview $performanceReview) {
		$performanceReviewerList = $performanceReview->getReviewers();
		$isReviewer = false;
		if(count($performanceReviewerList) > 0){
			foreach ($performanceReviewerList as $reviewer){
				if ($reviewer->getReviewerId() == $_SESSION['empNumber']){
					$isReviewer = true;
					break;
				}
			}
		}
		 
		return $isReviewer;
	}
	
	protected function isPrimaryReviewer (PerformanceReview $performanceReview){
		$performanceReviewerList = $performanceReview->getReviewers();
		$isPrimaryReviewer = false;
		foreach ($performanceReviewerList as $reviewer){
			if ($reviewer->getReviewerId() == $_SESSION['empNumber']){
				if($reviewer->isPrimaryReviewer()){
					$isPrimaryReviewer = true;
				}
				break;
			}
		}
		return $isPrimaryReviewer;
	}
	/**
	 *
	 * Return the reviewer names
	 * @param $performanceReview
	 */
	protected function getReviewerNames(PerformanceReview $performanceReview){
		$performanceReviewerList = $performanceReview->getReviewers();
		$reviewerName = "";
		foreach ($performanceReviewerList as $reviewer){
			$reviewerName.= ($reviewerName != "") ? ", ".$reviewer->getReviewer()->getFirstAndLastNames(): $reviewer->getReviewer()->getFirstAndLastNames();
		}
		return $reviewerName;
	}
	
	/**
	 * Checks whether the logged in employee is a reviewer
	 */
	protected function isLoggedReviewer($empId, $clues) {
		$performanceReviewService = $this->getPerformanceReviewService();
		return $performanceReviewService->isReviewer($empId, $clues);
	}
	
	/**
	 * Set message
	 */
	public function setMessage($messageType, $message = array()) {
		$this->getUser()->setFlash('messageType', $messageType);
		$this->getUser()->setFlash('message', $message);
	}
}