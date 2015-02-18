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
 * Actions class for performance module
 */
class performanceActions extends sfActions {

    private $kpiService;
    private $jobService;
    private $performanceKpiService;
    private $performanceReviewService;
    private $jobTitleService;
    protected $homePageService;

    public function getHomePageService() {

        if (!$this->homePageService instanceof HomePageService) {
            $this->homePageService = new HomePageService($this->getUser());
        }

        return $this->homePageService;
    }

    public function setHomePageService($homePageService) {
        $this->homePageService = $homePageService;
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

    /**
     * List Define Kpi
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeListDefineKpi(sfWebRequest $request) {

        $this->form = new ListKpiForm(array(), array(), true);

        $this->listJobTitle = $this->getJobTitleService()->getJobTitleList("", "", false);

        $kpiService = $this->getKpiService();
        $this->mode = $request->getParameter('mode');
        $recordsLimit = $request->getParameter('recordsPerPage_Limit');
        $this->recordsPerPageLimit = $recordsLimit;
        if($recordsLimit){
        	$noOfRecords = $recordsLimit;
        }else{
        	$noOfRecords = 10;
        	$this->recordsPerPageLimit = $noOfRecords;
        }
        if ($this->getUser()->hasFlash('templateMessage')) {
            $this->templateMessage = $this->getUser()->getFlash('templateMessage');
        }
        $this->pager = new SimplePager('KpiList', $noOfRecords);
        $this->pager->setPage(($request->getParameter('pageNo') != '') ? $request->getParameter('pageNo') : 0);

        if ($request->getParameter('mode') == 'search') {
            $jobTitleId = $request->getParameter('txtJobTitle');
            if ($jobTitleId != 'all') {
                $this->searchJobTitle = $this->getJobTitleService()->getJobTitleById($jobTitleId);
                $this->kpiList = $kpiService->getKpiForJobTitle($jobTitleId);
            } else {

                $this->pager->setNumResults($kpiService->getCountKpiList());
                $this->pager->init();
                $offset = $this->pager->getOffset();
                $offset = empty($offset) ? 0 : $offset;
                $limit = $this->pager->getMaxPerPage();

                $this->kpiList = $kpiService->getKpiList($offset, $limit);
              
            }
        } else {
            $this->pager->setNumResults($kpiService->getCountKpiList());
            $this->pager->init();

            $offset = $this->pager->getOffset();
            $offset = empty($offset) ? 0 : $offset;
            $limit = $this->pager->getMaxPerPage();

            $this->kpiList = $kpiService->getKpiList($offset, $limit);
        }

        $this->hasKpi = ( count($this->kpiList) > 0 ) ? true : false;
    }

    /**
     * Save Kpi
     * @param sfWebRequest $request
     * @return None
     */
    public function executeSaveKpi(sfWebRequest $request) {

        $this->form = new SaveKpiForm(array(), array(), true);

        $this->listJobTitle = $this->getJobTitleService()->getJobTitleList();

        $kpiService = $this->getKpiService();
        $this->defaultRate = $kpiService->getKpiDefaultRate();


        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {

                try {

                    $defineKpi = new DefineKpi();
                    
                    $defineKpi->setJobtitlecode($request->getParameter('txtJobTitle'));
                    $defineKpi->setKpiTitle($request->getParameter('txtPerformanceTitle'));
                    $defineKpi->setDesc(trim($request->getParameter('txtDescription')));
						
					$minRate = 0;
					$maxRate = 0;
					
                    if (trim($request->getParameter('txtMinRate')) != "") {
                    	$minRate = $request->getParameter('txtMinRate');
                        $defineKpi->setMin($minRate);
                    }

                    if (trim($request->getParameter('txtMaxRate')) != "") {
                    	$maxRate = $request->getParameter('txtMaxRate');
                        $defineKpi->setMax($maxRate);
                    }
                    
                    if($minRate != 0 && $maxRate != 0){
                    	$diff = $maxRate - $minRate;
                    	$ratingDesc = $request->getParameter("txtRatingDescription");
                    	$performanceRatingList = array();
                    	for ( $index = 1, $max_count = $diff+1; $index <= $max_count; $index++ ) {
							$performanceKpi = new PerformanceKpi();
							$performanceKpi->setRatingDescription($ratingDesc[$index]);
							$performanceKpi->setRate($minRate);
							array_push($performanceRatingList, $performanceKpi);
							$minRate++;
						}
						$defineKpi->setRatings($kpiService->getRatingXml($performanceRatingList));
                    }

                    if ($request->getParameter('chkDefaultScale') == 1) {
                        $defineKpi->setDefault(1);
                    } else {
                        $defineKpi->setDefault(0);
                    }

                    $defineKpi->setIsactive(1);

                    $kpiService->saveKpi($defineKpi);

                    $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
                    $this->redirect('performance/saveKpi');
                } catch (Doctrine_Validator_Exception $e) {
                    $this->setMessage('warning', array($e->getMessage()));
                    $this->errorMessage = $e->getMessage();
                }catch ( Exception $e ) {
                	$this->setMessage('warning', array($e->getMessage()));
                    $this->errorMessage = $e->getMessage();
					//throw new PerformanceServiceException ( $e->getMessage () );
				}
            }
        }
    }

    /**
     * Update Define Kpis
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateKpi(sfWebRequest $request) {

		$this->form = new SaveKpiForm(array(), array(), true);
        /* For highlighting corresponding menu item */
        $request->setParameter('initialActionName', 'listDefineKpi');

        $this->listJobTitle = $this->getJobTitleService()->getJobTitleList("", "", false);

        $kpiService = $this->getKpiService();
        $this->defaultRate = $kpiService->getKpiDefaultRate();

        $kpi = $kpiService->readKpi($request->getParameter('id'));
        $this->kpi = $kpi;
		$this->ratingList = $kpiService->getKpiRatingList($kpi->getRatings());

        if ($request->isMethod('post')) {

            $kpi->setJobtitlecode($request->getParameter('txtJobTitle'));
            $kpi->setDesc(trim($request->getParameter('txtDescription')));
            
            $minRate = 0;
            $maxRate = 0;
            
            if (trim($request->getParameter('txtMinRate')) != "") {
	            $minRate = $request->getParameter('txtMinRate');
	            $kpi->setMin($minRate);
            } else {
                $kpi->setMin(null);
            }
            
            if (trim($request->getParameter('txtMaxRate')) != "") {
	            $maxRate = $request->getParameter('txtMaxRate');
	            $kpi->setMax($maxRate);
            }else {
                $kpi->setMax(null);
            }
            
            if($minRate != 0 && $maxRate != 0){
	            $diff = $maxRate - $minRate;
	            $performanceRatingList = array();
	            $ratingDesc = $request->getParameter("txtRatingDescription");
	            for ( $index = 1, $max_count = $diff+1; $index <= $max_count; $index++ ) {
		            $performanceKpi = new PerformanceKpi();
		            $performanceKpi->setRatingDescription($ratingDesc[$index]);
		            $performanceKpi->setRate($minRate);
		            array_push($performanceRatingList, $performanceKpi);
		            $minRate++;
	            }
	            $kpi->setRatings($kpiService->getRatingXml($performanceRatingList));
            }

            if ($request->getParameter('chkDefaultScale') == 1) {
                $kpi->setDefault(1);
            } else {
                $kpi->setDefault(0);
            }

            $kpiService->saveKpi($kpi);
            $this->getUser()->setFlash('success', __(TopLevelMessages::UPDATE_SUCCESS));
            $this->redirect('performance/listDefineKpi');
        }
    }

    /**
     * get kpi ratings
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeGetKpiRatings(sfWebRequest $request) {
	    $kpiId = $request->getParameter('kpiId');
	    $kpiService = $this->getKpiService();
	    $kpi = $kpiService->readKpi($kpiId);
	   
	    $returnData = array();
	    if(!is_null($kpi)){
		    $kpiRatingList = $kpiService->getKpiRatingList($kpi->getRatings());
		    foreach( $kpiRatingList as $kpiRating){
			    $rating['rate'] = $kpiRating->getRate();
                $rating['description'] = nl2br($kpiRating->getRatingDescription());
                $returnData[] = $rating;
		    }
	    }
	    
	    $response = $this->getResponse();
	    $response->setHttpHeader('Expires', '0');
	    $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
	    $response->setHttpHeader("Cache-Control", "private", false);
	    return $this->renderText(json_encode($returnData));
    }
    
    /**
     * Copy define Kpi's into new Job Title
     *
     * @param sfWebRequest $request
     * $return none
     * */
    public function executeCopyKpi(sfWebRequest $request) {

        $this->form = new CopyKpiForm(array(), array(), true);

        $kpiService = $this->getKpiService();

        $this->listJobTitle = $this->getJobTitleService()->getJobTitleList();
        $this->listAllJobTitle = $this->getJobTitleService()->getJobTitleList("", "", false);

        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {

                $toJobTitle = $request->getParameter('txtCopyJobTitle');
                $fromJobTitle = $request->getParameter('txtJobTitle');
                $confirm = $request->getParameter('txtConfirm');

                $avaliableKpiList = $kpiService->getKpiForJobTitle($toJobTitle);
                $this->toJobTitle = $toJobTitle;
                $this->fromJobTitle = $fromJobTitle;

                if (count($avaliableKpiList) == 0 || $confirm == '1') {

                    $kpiService->copyKpi($toJobTitle, $fromJobTitle);

                    $this->getUser()->setFlash('success', __('Successfully Copied'));
                    $this->redirect('performance/listDefineKpi');
                } else {

                    $this->confirm = true;
                }
            }
        }
    }
	/*Added by Sujata*/
    /**
     * Delete Define DeleteMyFeedback
     * @param sfWebRequest $request
     * @return none
     */
    public function executeDeleteMyFeedback(sfWebRequest $request) {
        if ($request->isMethod('post')) {
            $performanceReviewService = $this->getPerformanceReviewService();
            $fIds = $request->getParameter('chkSelectRow');
            if(count($fIds)>0) {
                $performanceReviewService->deleteFeedback($fIds);
                $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
            }else {
    			$this->getUser()->setFlash('error', __('Bulk Delete failed : No Feedback Selected.'));
    		}
        }
        $this->redirect('performance/viewMyFeedback');
    }
    /**
     * Delete Define Kpi
     * @param sfWebRequest $request
     * @return none
     */
    public function executeDeleteDefineKpi(sfWebRequest $request) {

        $this->form = new ListKpiForm(array(), array(), true);

        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {

                $kpiService = $this->getKpiService();
                $kpiService->deleteKpi($request->getParameter('chkKpiID'));

                $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
            }
        }

        $this->redirect('performance/listDefineKpi');
    }
    
     /**
     * Action to import search review data
     */
    public function executeExportReviews(sfWebRequest $request) {
    	set_time_limit(0);
    	
        if ($this->getUser()->hasAttribute('prSearchClues')) {
            $this->clues = $this->getUser()->getAttribute('prSearchClues');
        }

        if ($this->getUser()->hasFlash('prClues')) {
            $this->clues = $this->getUser()->getFlash('prClues');
        }

	    if (!isset($this->clues)) {
            $this->clues = $this->getReviewSearchClues($request);
        }
        
        /* Setting logged in user type */
        if (!$this->loggedAdmin && $this->loggedReviewer) {
            $this->clues['loggedReviewerId'] = $this->loggedEmpId;
        } elseif (!$this->loggedAdmin && !$this->loggedReviewer) {
            $this->clues['loggedEmpId'] = $this->loggedEmpId;
        }
        
        
        $employeeService = new EmployeeService();
        $performanceReviewService = $this->getPerformanceReviewService();
        //Search reviews
        $performanceReviews = $performanceReviewService->searchPerformanceReview($this->clues);
        //Create excelsheet - PHPExcel
	    $objPHPExcel = new PHPExcel();
	    $objPHPExcel->getProperties()->setCreator("Synerzip HRMS")
		->setLastModifiedBy("Synerzip HRMS")
		->setTitle("Performance Report")
		->setSubject("Performance Report (April-Sep 2013)")
		->setDescription("Performance Report (April-Sep 2013).")
		->setKeywords("Performance")
		->setCategory("Performance");
	    //Create sheet Headers
	    $objPHPExcel->setActiveSheetIndex(0)
		->setCellValue('A1', 'Employee ID')
		->setCellValue('B1', 'Employee Name')
		->setCellValue('C1', 'Job Title')
		->setCellValue('D1', 'Primary Reviewer')
		->setCellValue('E1', 'Secondary Reviewer(s)')
		->setCellValue('F1', 'Final Rating');
		$i= 2;
		
		//Now start creating report 
		foreach ( $performanceReviews as $review ) {
			$employee = $employeeService->getEmployee($review->getEmployeeId());
			if($employee){
				$primaryReviewer = $review->getPrimaryReviewer();
					$pname = $primaryReviewer->getReviewer()->getFirstAndLastNames();
				$performanceReviewerList = $review->getSecondaryReviewers();
				$reviewerName = "";
				foreach ($performanceReviewerList as $reviewer){
					$reviewerName.= ($reviewerName != "") ? ", ".$reviewer->getReviewer()->getFirstAndLastNames():
						$reviewer->getReviewer()->getFirstAndLastNames();
				}
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('A'.$i, $employee->getEmployeeId())
				->setCellValue('B'.$i, $employee->getFirstAndLastNames())
				->setCellValue('C'.$i, $review->getJobTitle()->getJobTitleName())
				->setCellValue('D'.$i, $pname)
				->setCellValue('E'.$i, $reviewerName)
				->setCellValue('F'.$i, $review->getFinalRating());
				$i++;			
			}
			
		}
	    
	    $objPHPExcel->getActiveSheet()->setTitle('Performance Report');
	    $objPHPExcel->setActiveSheetIndex(0);
	    $objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
	   // $objWriter->save(str_replace('.php', '.xlsx', __FILE__));
	    
	    header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment; filename=\"Performance_report.xlsx\"");
		header("Cache-Control: max-age=0");
 
		//$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, "Excel5");
		$objWriter->save("php://output");
		exit;
    }
    
    public function executeViewPerformanceReview(sfWebRequest $request){
    	$id = $request->getParameter('id');
		$reviewArr = array('id' => $id);
        $performanceReviewService = $this->getPerformanceReviewService();
        $performanceReview = $performanceReviewService->readPerformanceReview($reviewArr);
        
 		$performanceKpiService = $this->getPerformanceKpiService();
       // $this->_checkPerformanceReviewAuthentication($performanceReview);
		
        $this->performanceReview = $performanceReview;

		//Get last cycle details
		$performancePeriod = $performanceReviewService->getLastPerformancePeriod($performanceReview->getPeriodFrom());
		if(!is_null($performancePeriod)){
			$this->lastPerformanceList = $performanceReviewService->getPerformanceReviewsByDate($performancePeriod->getPeriodFrom(),$performancePeriod->getPeriodTo(),$performanceReview->getEmployeeId());	
		}
		
		$this->selfMultiFeedbackList = $performanceReviewService->getMultiSourceFeedback($performanceReview->getPeriodFrom(),$performanceReview->getPeriodTo(), $performanceReview->getEmployeeId());
		$this->givenMultiFeedbackList = $performanceReviewService->getMultiSourceFeedbackByReviewer($performanceReview->getPeriodFrom(),$performanceReview->getPeriodTo(), $performanceReview->getEmployeeId());
		
        $performanceKpiList = $performanceKpiService->getPerformanceKpiList($performanceReview->getKpis());
		
        //$this->kpiList = $performanceKpiList;
		$this->reviewerNames = $this->getReviewerNames($performanceReview);
        $this->isHrAdmin = $this->isHrAdmin();
        $this->isReviwer = $this->isReviwer($performanceReview);
        $this->isPrimary = $this->isPrimaryReviewer($performanceReview);
        foreach ($performanceReview->getReviewers() as $rev) {
        	if($this->loggedEmpId == $rev->getReviewerId()) {
        		$this->reviewReviewerId = $rev->getId();
        	}
        }
    }

    /**
     * View Performance review
     * @param sfWebRequest $request
     * @return none
     */
    public function executePerformanceReview(sfWebRequest $request) {

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
        
        /* Modification DESC: To fix HRMS-267 [Admin & primary reviewer can only submit the apprisal form.]
         */ 
        $this->isPrimaryReviwerCommented=$this->isPrimaryReviwerCommented($performanceReview->getPrimaryReviewer());
        
       
        if ($request->isMethod('post')) {

            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {

                $saveMode = $request->getParameter('saveMode');
                if($saveMode == 'rejectComment' && trim($request->getParameter('reject_comments')) != $performanceReview->setRejectComments()) {
                	$performanceReview->setRejectComments($request->getParameter('reject_comments'));
                	$performanceReview->save();
                	$mailer = new PerformanceMailer();
                	$mailer->sendRejectCommentNotififcation($performanceReview);
                	$this->getUser()->setFlash('success', __('Updated Reject Appraisal reason'));
                	$this->redirect('performance/performanceReview?id=' . $id);
                }
                
                if($saveMode == 'save' && !($this->isReviwer) && $this->isHrAdmin) {
                	if (trim($request->getParameter('txtNotes')) != '') {
                		$performanceReviewService->addComment($performanceReview, $request->getParameter('txtNotes'), $_SESSION['empNumber']);
                	}
                	$this->getUser()->setFlash('success', __(TopLevelMessages::UPDATE_SUCCESS));
                	$this->redirect('performance/performanceReview?id=' . $id);
                }
                
                
             
				//set self review rating and comment
                if($this->isReviwer || $this->isHrAdmin){
	                if (trim($request->getParameter('finalRating')) != '') {
		                $performanceReview->setFinalRating($request->getParameter('finalRating'));
	                }else{
		                $performanceReview->setFinalRating(null);
	                }
	                
	                $performanceReview->setReviewSummary($request->getParameter('reviewSummary'));
	                $performanceReview->setObjective($request->getParameter('objective'));
	                $performanceReview->setLatestComment($request->getParameter('txtNotes'));
                    $performanceReview->setStrongPoints($request->getParameter('strong_points'));
                    /* Added By: Shagupta Faras
                     * Added On: 05 Aug 2014
                     * DESC : Added code to store 'Feedback_on_accomplishments' field to value
                     */
                    $performanceReview->setFeedbackOnAccomplishments($request->getParameter('feedback_on_accomplishments'));
	                
	                $performanceReview->setStrongPoints($request->getParameter('strong_points'));
	                $performanceReview->setNoticedImprovements($request->getParameter('noticed_improvement'));
	                $performanceReview->setScopeForImprovement($request->getParameter('improvement_scope'));
                }else{
	                $performanceReview->setMajorAccomplishments($request->getParameter('major_accomplishments'));
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
		                } 
		                
	                }
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
                if($saveMode == "reject")
                {
                	$this->getUser()->setFlash('success.nofade','Successfully Rejected');
                
                } else {
                	$this->getUser()->setFlash('success', __(TopLevelMessages::UPDATE_SUCCESS));
                }
                $this->redirect('performance/performanceReview?id=' . $id);
            }
        }
    }
    /* Added DESC: Function created to check, has Primary reviewer given rating for all KPIS?
    */ 
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

    /**
     * Get the current page number from the user session.
     * @return int Page number
     */
    protected function getPage() {
        return $this->getUser()->getAttribute('performancereviewlist.page', 1, 'performancereview_module');
    }

    /**
     * Set's the current page number in the user session.
     * @param $page int Page Number
     * @return None
     */
    protected function setPage($page) {
        $this->getUser()->setAttribute('performancereviewlist.page', $page, 'performancereview_module');
    }

    /**
     * Is HR admin
     * @return unknown_type
     */
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

    /**
     * Save performance Review
     * @param $request
     * @return unknown_type
     */
    public function executeSaveReview(sfWebRequest $request) {

        $reviewId = $request->getParameter('reviewId');
        
        (!empty($reviewId)) ? $this->reviewId = $reviewId : '';

        if ($request->getParameter('redirect')) {

            $empName = $request->getParameter('empName');
            $revId = $request->getParameter('reviewer');
            $revsId = $request->getParameter('reviewers');
            $empId = $request->getParameter('empId');
            //$revId = $request->getParameter('reviewerId');
//            $toDate = $request->getParameter('toDate');
//            $fromDate = $request->getParameter('fromDate');
//            $dueDate = $request->getParameter('dueDate');
            $this->form = new SaveReviewForm(array(), array('redirect' => $request->getParameter('redirect'), 'reviewId' => $reviewId,
             'empName' => $empName, 'empId' => $empId, 'reviewerName' => $revId, 'reviewersName' => $revsId), true);
        } else {
            $this->form = new SaveReviewForm(array(), array('reviewId' => $reviewId), true);
        }
        
        /* Saving Performance Reviews */

        if ($request->isMethod('post')) {
            /* Showing update form: Ends */
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {
                $reviewId = $this->form->getValue('reviewId');
                $empWidget = $this->form->getValue('employeeName');
                $primaryReviewerId = $this->form->getValue('reviewer');
                $employeeService = new EmployeeService();
                $employee = $employeeService->getEmployee($empWidget['empId']);
                $subDivisionId = $employee->getWorkStation();
                //$empJobCode = $employee->getJobTitleCode();
                
                $kpiService = $this->getKpiService();
                $performanceKpiService = $this->getPerformanceKpiService();
                $empJobCode = $kpiService->getJobTitleIdForKpi();
                
                $kpiList = $kpiService->getKpiForJobTitle($empJobCode);
                                
                if (count($kpiList) == 0) {
                	$this->noKpiDefined = true;
                	return;
                }
                $secondaryReviewersArray = explode(',', $this->form->getValue('reviewers'));
                
                $performanceReviewService = $this->getPerformanceReviewService();
                $localizationService = new LocalizationService();
                $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();

                if (!empty($reviewId)) { // Updating an existing one
                	$reviewArr = array('id' => $reviewId);
                    $review = $performanceReviewService->readPerformanceReview($reviewArr);
                } else { // Adding a new one
                    $review = new PerformanceReview();
                    $currentCycle =  $this->getPerformanceReviewService()->getCurrentPerformancePeriod();
                    $xmlStr = $performanceKpiService->getXmlFromKpi($kpiService->getKpiForJobTitle($empJobCode));
                    $review->setEmployeeId($empWidget['empId']);
                    $review->setSubDivisionId($subDivisionId);
                    $review->setState(PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED);
                    $review->setJobTitleCode($empJobCode);
                    $review->setCreatorId($this->loggedUserId);
                	$review->setCreationDate(date('Y-m-d'));
                	$review->setKpis($xmlStr);
                	$review->setPeriodFrom($currentCycle->getPeriodFrom());
                	$review->setPeriodTo($currentCycle->getPeriodTo());
                	$review->setDueDate($currentCycle->getDueDate());
                    //check record is already exist or not
                    $existReview = $performanceReviewService->checkReviewExist($currentCycle->getPeriodFrom(),$currentCycle->getPeriodTo(),$empWidget['empId']);
                }
               if(!empty($reviewId) || !$existReview) { 
                	$review = $this->compareReviewersAndSave($review, $secondaryReviewersArray, $primaryReviewerId);
					//Send the notification mail only for add review (not update);
    				if(empty($reviewId)){
    					$performanceMailer = new PerformanceMailer();
    					$performanceMailer->sendNotifications($review->getId());
    				}
    				    
                    $actionResult = (!empty($reviewId)) ? __('updated') : __('added');
                    $this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
                }else{
                	
                    $this->getUser()->setFlash('error', __(TopLevelMessages::SAVE_FAILURE));
                }
                $this->redirect('performance/viewReview/mode/new');
            }
        }
    }
    private function _copyReview($performanceReview,$performancePeriod,$copyDataFlag, $sendEmailFlag){
    	$copied = false; 
    	$logger = Logger::getLogger('action.CopyReview');
	    $employee = $performanceReview->getEmployee();
	    
	    $kpiService = $this->getKpiService();
	    $empJobCode = $kpiService->getJobTitleIdForKpi();
	    
	    // First check wheter employee is active or Terminated    	
	    if ($employee->getTerminationId() == null){
		    $performanceKpiService = $this->getPerformanceKpiService();
		    $performanceReviewService = $this->getPerformanceReviewService();
		    $reviewers = $performanceReview->getReviewers();
		    $setPrimaryReviwer = false;
			$performanceReviewerList = array();
		    $review = new PerformanceReview();
		    $kpiList = $kpiService->getKpiForJobTitle($empJobCode); 
		    // copy all data
		    if ($copyDataFlag == '2'){
			    $review->setEmployeeId($employee->getEmpNumber());
			    $review->setSubDivisionId($employee->getWorkStation());
			    $review->setState($performanceReview->getState());
			    $review->setJobTitleCode($empJobCode);
			    $review->setCreatorId($this->loggedUserId);
			    $review->setCreationDate(date('Y-m-d'));
			    $review->setKpis($kpiList);
			    $review->setPeriodFrom(date('Y-m-d',strtotime($performancePeriod->getPeriodFrom())));
			    $review->setPeriodTo(date('Y-m-d',strtotime($performancePeriod->getPeriodTo())));
			    $review->setDueDate(date('Y-m-d',strtotime($performancePeriod->getDueDate())));
			    $review->setFinalRating($performanceReview->getFinalRating());
			    $review->setObjective($performanceReview->getObjective());
			    $review->setPreviousObjective($performanceReview->getPreviousObjective());
			    $review->setMajorAccomplishments($performanceReview->getMajorAccomplishments());
			    $review->setStrongPoints($performanceReview->getStrongPoints());
			    $review->setNoticedImprovements($performanceReview->getNoticedImprovements());
			    $review->setFeedbackOnAccomplishments($performanceReview->getFeedbackOnAccomplishments());
			    $review->setScopeForImprovement($performanceReview->getScopeForImprovement());
			    $review->setEmployeeFeedback($performanceReview->getEmployeeFeedback());
			   
			    foreach($reviewers as $reviewer){
				    $performanceReviewer = new PerformanceReviewReviewer();
				    $isPrimaryReviewer = $reviewer->getIsPrimary();
				    $performanceReviewer->setReviewerId($reviewer->getReviewerId());
				    $performanceReviewer->setKpis($review->getKpis());
				    $performanceReviewer->setIsPrimary($isPrimaryReviewer);
				    if($isPrimaryReviewer == 1){
					    $setPrimaryReviwer = true;
				    }
				    array_push($performanceReviewerList,$performanceReviewer);
			    }
			 	$copied = true;   
		    }else{
                $performanceKpiService = $this->getPerformanceKpiService();               
                $kpiList = $kpiService->getKpiForJobTitle($empJobCode);
                if (count($kpiList) > 0) {
	                //copy only Goal Data and assign new KPI form
	                $xmlStr = $performanceKpiService->getXmlFromKpi($kpiList);
	                $review->setEmployeeId($employee->getEmpNumber());
	                $review->setSubDivisionId($employee->getWorkStation());
	                $review->setState(PerformanceReview::PERFORMANCE_REVIEW_STATUS_SCHDULED);
	                $review->setJobTitleCode($empJobCode);
	                $review->setCreatorId($this->loggedUserId);
	                $review->setCreationDate(date('Y-m-d'));
	                $review->setKpis($xmlStr);
	                $review->setPreviousObjective($performanceReview->getObjective());
	                
	                $review->setPeriodFrom($performancePeriod->getPeriodFrom());
	                $review->setPeriodTo($performancePeriod->getPeriodTo());
	                $review->setDueDate($performancePeriod->getDueDate());
	                
	                foreach($reviewers as $reviewer){
		                $performanceReviewer = new PerformanceReviewReviewer();
		                $isPrimaryReviewer = $reviewer->getIsPrimary();
		                $performanceReviewer->setReviewerId($reviewer->getReviewerId());
		                $performanceReviewer->setKpis($review->getKpis());
		                $performanceReviewer->setIsPrimary($isPrimaryReviewer);
		                if($isPrimaryReviewer == 1){
			                $setPrimaryReviwer = true;
		                }
		                array_push($performanceReviewerList,$performanceReviewer);
	                }
	             	$copied = true;       
                }else{
                	//Log Result
                	$logger->error('NO KIP FORM FOUND: ' . $employee->getFirstAndLastNames());
                }
			    
		    }
		    
		    if(!$setPrimaryReviwer && (count($performanceReviewerList) > 0)){
			    $performanceReviewerList[0]->setIsPrimary(1);
		    }
		    
		    //Save ther review
		    if($review = $performanceReviewService->savePerformanceReview($review, $performanceReviewerList)){
    		    if ($copyDataFlag != 2 && !is_null($review) && !empty($sendEmailFlag)) {
    		    	$performanceMailer = new PerformanceMailer();
    		    	$performanceMailer->sendNotifications($review->getId());
    		    }
    		    
    		    if ($copyDataFlag == 2){
    			    $performanceReviewComments = $performanceReview->getPerformanceReviewComment();
    			    if(count($performanceReviewComments) > 0){
    				    foreach ( $performanceReviewComments as $comment ) {
    					    $performanceReviewService->addComment($review, $comment->getComment(), $comment->getEmployeeId());    	
    				    }
    			    }
    		    }
		    }else{
               return false;
            }
	    }else{
			$logger->error('TERMINATED EMPLOYEE: ' . $employee->getFirstAndLastNames());
	    }
	    
	    return $copied;
    }

    /**
     * Copy performance review.
     *
     * @param sfWebRequest $request
     * $return none
     * */
    public function executeCopyReview(sfWebRequest $request) {
	    
	    $this->form = new CopyReviewForm(array(), array(), true);
	    
	    $message = null;
	    
	    $performanceReviewService = $this->getPerformanceReviewService();
	   
	    //get current cycle date
	    $performancePeriod = $performanceReviewService->getCurrentPerformancePeriod();
	    $this->performanceCycle = $performancePeriod;
	    $kpiService = $this->getKpiService();
	    
	    if ($request->isMethod('post')) {
		    $this->form->bind($request->getParameter($this->form->getName()));
		    if ($this->form->isValid()) {
			    
			    $allEmployeeChk = $request->getParameter('chkAllEmployees');
			    $employee= $request->getParameter('txtEmployee');
			    $cycleFrom = $request->getParameter('performanceCycleFrom');
			    $cycleTo = $request->getParameter('performanceCycleTo');
			    $copyDataFlag = $request->getParameter('copyData');
			    $sendEmailFlag = $request->getParameter('emailNotification');
			    $count = 0;
			    $total = 0;
			   
			    if(!empty($allEmployeeChk)){
				    $performanceList = $performanceReviewService->getPerformanceReviewsByDate($cycleFrom['from'],$cycleFrom['to']);
				    $total = count($performanceList);
			        if($total > 0){
					    foreach ( $performanceList as $performanceReview ) {
					        if(!$performanceReviewService->checkReviewExist(date('Y-m-d',strtotime($performancePeriod->getPeriodFrom())), date('Y-m-d',strtotime($performancePeriod->getPeriodTo())),$performanceReview->getEmployee()->getEmpNumber())){
					            if($performanceReview->getEmployee()->getTerminationId() == NULL) {
        						    $copied = $this->_copyReview($performanceReview, $performancePeriod,$copyDataFlag, $sendEmailFlag);
        						    if($copied){
        							    $count++;
        						    }
					    	    }
					    	}
					    }
				    }else{
					    $message = 'Performance data not found for given date';
				    }
			    }else{
				    $performanceList = $performanceReviewService->getPerformanceReviewsByDate($cycleFrom['from'],$cycleFrom['to'],$employee['empId']);
				    $total = count($performanceList);
			        if($total > 0){
					    foreach ( $performanceList as $performanceReview ) {
					        if(!$performanceReviewService->checkReviewExist(date('Y-m-d',strtotime($performancePeriod->getPeriodFrom())), date('Y-m-d',strtotime($performancePeriod->getPeriodTo())),$performanceReview->getEmployee()->getEmpNumber())){					        
    						    $copied = $this->_copyReview($performanceReview, $performancePeriod,$copyDataFlag, $sendEmailFlag);
    						    if($copied){
    							    $count++;
    						    }
					        }else{
					    	    $message = 'Performance Review data for current cycle already exists.';
					    	    break;
					    	}
					    }	
				    }else{
					    $message = 'Performance data not found for given date';
				    }
			    }
			    
		    }
		    
		    if(is_null($message)){
			    $message = 'Copied '.$count.' employee(s) performance data out of '.$total.' employee(s)'; 
		    }
		    
		    $this->getUser()->setFlash('success', __($message));
		    $this->redirect('performance/copyReview');
	    }
    }

//End of executeViewReview

    /**
     * Show not authorized message
     * 
     */
    public function executeUnauthorized(sfWebRequest $request) {
        sfConfig::set('sf_web_debug', false);
        sfConfig::set('sf_debug', false);

        $response = $this->getResponse();
        $response->setStatusCode(401, 'Not authorized');
        return $this->renderText("You do not have the proper credentials to access this page!");
    }

    public function executeDeleteReview(sfWebRequest $request) {

        $this->form = new ViewReviewForm(array(), array(), true);
        $delReviews = $request->getParameter('chkSelectRow');
        $this->getUser()->setFlash('prClues', $clues);

        if (empty($delReviews)) {
            $this->getUser()->setFlash('warning', __(TopLevelMessages::SELECT_RECORDS));
            $this->redirect('performance/viewReview');
        }else{
            $performanceReviewService = $this->getPerformanceReviewService();
            $performanceReviewService->deletePerformanceReview($delReviews);
            $this->getUser()->setFlash('success', __(TopLevelMessages::DELETE_SUCCESS));
        }

        $this->redirect('performance/viewReview');
    }

    protected function getReviewSearchClues($request, $suffix='') {

        $clues = array();
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        
        $dateValidator = new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern));
        
        if ($request instanceof sfWebRequest) {
            //$clues['from'] = $dateValidator->clean($request->getParameter('txtPeriodFromDate' . $suffix));
           // $clues['to'] = $dateValidator->clean($request->getParameter('txtPeriodToDate' . $suffix));
           	$cycleDate = $request->getParameter('performanceCycle' . $suffix);
           	
           	$fromDate = $dateValidator->clean($cycleDate['from']);
           	$toDate = $dateValidator->clean($cycleDate['to']);
           	$performancePeriod = $this->getPerformanceReviewService()->getCurrentPerformancePeriod();
           	if(empty($fromDate) && empty($toDate)){
           		$fromDate = $performancePeriod->getPeriodFrom();
           		$toDate = $performancePeriod->getPeriodTo();
           	}
            $clues['from'] = $fromDate;  
            $clues['to'] = $toDate;
            
            $clues['period'] = $request->getParameter('period' . $suffix);
            $clues['due'] = $dateValidator->clean($request->getParameter('txtDueDate' . $suffix));
            $clues['jobCode'] = $request->getParameter('txtJobTitleCode' . $suffix);
            $clues['state'] = $request->getParameter('txtState' . $suffix);
            $clues['divisionId'] = $request->getParameter('txtSubDivisionId' . $suffix);
            $clues['empName'] = $request->getParameter('txtEmpName' . $suffix);
            $clues['empId'] = empty($clues['empName']) ? 0 : $request->getParameter('hdnEmpId' . $suffix);
            $clues['reviewerName'] = $request->getParameter('txtReviewerName' . $suffix);
            $clues['reviewerId'] = empty($clues['reviewerName']) ? 0 : $request->getParameter('hdnReviewerId' . $suffix);
            $clues['pageNo'] = $request->getParameter('hdnPageNo' . $suffix);
            $clues['projectName'] = $request->getParameter('txtProjectName' . $suffix);
            $clues['customerId'] = empty($clues['projectName']) ? 0 : $request->getParameter('hdnCustomerId' . $suffix);
            $clues['directReview']   = $request->getParameter('directReview'. $suffix);
        } elseif ($request instanceof PerformanceReview) {
            $clues['from'] = $request->getPeriodFrom();
            $clues['to'] = $request->getPeriodTo();
            $clues['due'] = $request->getDueDate();
            $clues['jobCode'] = $request->getJobTitleCode();
            $clues['divisionId'] = $request->getSubDivisionId();
            $clues['empName'] = $request->getEmployee()->getFirstName() . " " . $request->getEmployee()->getLastName();
            $clues['empId'] = $request->getEmployeeId();
            $clues['reviewerName'] = $request->getReviewer()->getFirstName() . " " . $request->getReviewer()->getLastName();
            $clues['reviewerId'] = $request->getReviewerId();
            $clues['id'] = $request->getId();
            $clues['state'] = $request->getState();
            
            //$clues['customerId'] = $request->getParameter('hdnCustomerId' . $suffix);
            //$clues['projectName'] = $request->getParameter('txtProjectName' . $suffix);
        }
        return $clues;
    }

    public function executeViewPerformanceModule(sfWebRequest $request) {
        $this->redirect($this->getHomePageService()->getPerformanceModuleDefaultPath());
    }
    
    public function executeViewMultiSourceFeedback(sfWebRequest $request) {
    		$performanceReviewService = $this->getPerformanceReviewService();
    		$employeeService = new EmployeeService();	
    		$employeeId = $request->getParameter('eid');
    		$reviewId = $request->getParameter('reviewId');
    		
    		$performancePeriod = $performanceReviewService->getCurrentPerformancePeriod();
    		$this->feedbackEmp = $employeeService->getEmployee($employeeId);
    		if($this->canViewFeedback($this->feedbackEmp,$reviewId)){
    			$this->multisourceFeedbackList = $performanceReviewService->getMultiSourceFeedback($performancePeriod->getPeriodFrom(),$performancePeriod->getPeriodTo(), $employeeId);	
    		}else{
    			$this->multisourceFeedbackList = $performanceReviewService->getMultiSourceFeedback($performancePeriod->getPeriodFrom(),$performancePeriod->getPeriodTo(), $employeeId, $_SESSION['empNumber']);
    		}
    		
    }
    
    private function canViewFeedback($feedbackEmployee, $reviewId){
	    $canView = false;
	    if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
		    $canView = true;
	    }else if ($feedbackEmployee->getEmpNumber() == $_SESSION['empNumber']){
		    $canView = true;	
	    }else if (!empty($reviewId)){
	    	$reviewArr = array('id' => $reviewId);
		    $performanceReviewService = $this->getPerformanceReviewService();
		    $performanceReview = $performanceReviewService->readPerformanceReview($reviewArr);
		    if($this->isReviwer($performanceReview)){
			    $canView = true;		    		
		    }
	    }else if($_SESSION['isSupervisor']){
		    $supervisor = $feedbackEmployee->getSupervisorRepTo($_SESSION['empNumber']);
		    if(!is_null($supervisor)){
			    $canView = true;
		    }
	    } 
	    return $canView;
    }
    
  /**
     * AJAX method to update reviewers
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function executeUpdateReviewers(sfWebRequest $request) {
        $performanceReviewService = $this->getPerformanceReviewService();
        $reviewId = $request->getParameter('review');
        $primaryReviewerId = $request->getParameter('reviewer');  // primary Reviewer
        $reviewersIds = $request->getParameter('reviewers'); //secondary Reviewers
        $jsonString = array();
        if (!empty($reviewId)) { // Updating an existing one
            $reviewArr = array('id' => $reviewId);
            $performanceReview = $performanceReviewService->readPerformanceReview($reviewArr);
            /*Added by sujata*/
            $primaryReviewer = $performanceReview->getPrimaryReviewer();
            if (!is_null($primaryReviewer)) {
                $existingPrimaryReviewerId= $primaryReviewer->getReviewerId();
            }
            $existingSecondaryReviewers = $performanceReview->getSecondaryReviewers();
            foreach ( $existingSecondaryReviewers as $existingSecondaryReviewer ) {
                $existingSecondaryReviewerId[] = $existingSecondaryReviewer->getReviewerId();
            }
            /*Ended by sujata*/
            $review = $this->compareReviewersAndSave($performanceReview, explode(',', $reviewersIds), $primaryReviewerId);
            $secondaryReviewerList = $review->getSecondaryReviewers();
            $sendEmailReviewers = array();
            foreach ($secondaryReviewerList as $reviewer){
                $name = $reviewer->getReviewer()->getFirstAndLastNames();
                $empNumber = $reviewer->getReviewer()->getEmpNumber();
                $jsonString[] = array('name' => $name , 'id' => $empNumber);
                if( !in_array($empNumber, $existingSecondaryReviewerId) ) {
                    $sendEmailReviewers[] = $empNumber;
                }
            }
            
            $temp = array();
            $primary = $review->getPrimaryReviewer();
            if(!is_null($primary)){
                $name = $primary->getReviewer()->getFirstAndLastNames();
                $empNumber = $primary->getReviewer()->getEmpNumber();
                $temp[] = array('name' => $name , 'id' => $empNumber);
                array_unshift($jsonString, $temp);
                if((!is_null($existingPrimaryReviewerId) || !empty($existingPrimaryReviewerId)) && $existingPrimaryReviewerId != $primaryReviewerId ){
                    array_push($sendEmailReviewers, $empNumber);
                }
            }
            $performanceMailer = new PerformanceMailer();
            $performanceMailer->sendUpdatedNotifications($reviewId,$sendEmailReviewers);
        }
        
        $logText = $_SESSION['fname']." has updated review record " . $reviewId;
        $logger = Logger::getLogger('action.UpdateReviewers');
        $logger->info($logText);
    
        $response = $this->getResponse();
        $response->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
        $response->setHttpHeader('Expires', '0');
        $response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
        $response->setHttpHeader("Cache-Control", "private", false);
        return $this->renderText(json_encode($jsonString));
    }
    
    /**
     * Common method which compares previous and current reviewers and perform Addition and deletion of reviewers
     * @param sfWebRequest $request
     * @return unknown_type
     */
    public function compareReviewersAndSave(PerformanceReview $performanceReview , $secondaryReviewerIds, $primaryReviewerId) {
    	
    	$performanceReviewService = $this->getPerformanceReviewService();
    	
    	// For Secondary Reviewer
    	$existingSecondaryReviewerId = array();
    	$existingSecondaryReviewers = $performanceReview->getSecondaryReviewers();
    	$secondaryPerformanceReviewerList = array();
    	$reviewersListToDelete = array();
    	
    	foreach ( $existingSecondaryReviewers as $existingSecondaryReviewer ) {
	    	$existingSecondaryReviewerId[] = $existingSecondaryReviewer->getReviewerId();
    	}
    	
    	//check reviewer ids to add
    	foreach($secondaryReviewerIds as $newReviewerId){
    		if(!empty($newReviewerId)){
    			if( !in_array($newReviewerId, $existingSecondaryReviewerId) ) {
    				$performanceReviewer = new PerformanceReviewReviewer();
    				$performanceReviewer->setReviewerId($newReviewerId);
    				$performanceReviewer->setKpis($performanceReview->getKpis());
    				$performanceReviewer->setIsPrimary(PerformanceReviewReviewer::IS_NOT_PRIMARY_REVIEWER);
    				$performanceReviewer->setIsDeleted(PerformanceReviewReviewer::IS_NOT_DELETED);
    				array_push($secondaryPerformanceReviewerList, $performanceReviewer);
    			}
    		}
    	}
    	
    	//check reviewer ids to be deleted
    	foreach($existingSecondaryReviewerId as $id){
	    	if( !in_array($id , $secondaryReviewerIds) ) {
		    	array_push($reviewersListToDelete, $id);
	    	}
    	}
    	//return $primaryToUpdate;
    	return $performanceReviewService->savePerformanceReview($performanceReview, $secondaryPerformanceReviewerList, $reviewersListToDelete, $primaryReviewerId);
    }

public function executeApproveReviews(sfWebRequest $request) {
		$idArray = $request->getParameter('chkSelectRow');
		if(count($idArray)<1) {
			$this->getUser()->setFlash('warning', __('Select Reviews to Approve.'));
		} else {
			$performanceReviewService = $this->getPerformanceReviewService();
			$idsToApprove = array();
			foreach ($idArray as $id) {
				$reviewArr = array('id' => $id);
				$reviewObject = $performanceReviewService->readPerformanceReview($reviewArr);
				if($reviewObject->getState() == PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED 
						&& $reviewObject->getState() != PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED) {
					array_push($idsToApprove, $id);
				}
			}
			$count = $performanceReviewService->approvePerformanceReviews($idsToApprove);
			$this->getUser()->setFlash('success', __($count. ' Review(s) approved.'));
		}
		$this->redirect('performance/viewReview');
	}
	
	public function executeSaveGoalsAjax(sfWebRequest $request) {
		sfConfig::set('sf_web_debug', false);
		sfConfig::set('sf_debug', false);
		$response = $this->getResponse();
		$response->setHttpHeader('Expires', '0');
		$response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
		$response->setHttpHeader("Cache-Control", "private", false);
		$performanceService = $this->getPerformanceReviewService();
		$goals = $performanceService->savePreviousObjective($request->getParameter('reviewId'), $request->getParameter('goals'));
		return $this->renderText(json_encode($goals));
	}
		
	public function executeSendPerformanceEmails(sfWebRequest $request) {
		sfConfig::set('sf_web_debug', false);
		sfConfig::set('sf_debug', false);
		$response = $this->getResponse();
		$response->setHttpHeader('Expires', '0');
		$response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
		$response->setHttpHeader("Cache-Control", "private", false);
		set_time_limit(0);
		$performanceService = new PerformanceReviewService();
		$currentCycle = $performanceService->getCurrentPerformancePeriod();
		$reviewList = $performanceService->getPerformanceReviewsByDate($currentCycle->getPeriodFrom(), $currentCycle->getPeriodTo());
		foreach ($reviewList as $review) {
			$performanceMailer = new PerformanceMailer();
			$performanceMailer->sendNotifications($review->getId());
		}
		exit;
		return $this->renderText(json_encode($goals));
	}
}