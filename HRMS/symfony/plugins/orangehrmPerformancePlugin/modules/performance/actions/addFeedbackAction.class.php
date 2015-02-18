<?php
class addFeedbackAction extends sfAction {
	private $performanceReviewService;
	
	public function getPerformanceReviewService() {
		if(is_null($this->performanceReviewService)) {
			$this->performanceReviewService = new PerformanceReviewService();
			$this->performanceReviewService->setPerformanceReviewDao(new PerformanceReviewDao());
		}
		return $this->performanceReviewService;
	}
	
	public function setForm(sfForm $form) {
		if (is_null($this->form)) {
			$this->form = $form;
		}
	}
	
	public function getForm() {
		$this->form->request = $this->getRequest();
		return $this->form;
	}
	public function execute($request) {
		$usrObj = $this->getUser()->getAttribute('user');
		$usrNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
		$params = array('feedbackid' => $request->getParameter("id"));
		$this->feedback = $request->getParameter("id");
        $CurrentCycleObj=$this->getPerformanceReviewService()->getEmployeeCurrentCyclePerformance($usrNumber);
        if(empty($CurrentCycleObj)){
        $this->getUser()->setFlash('addFeedbackTop.warning.nofade', __('There is no performance appraisal cycle in progress.'));  
        }
		if($this->feedback !=null) {
			$feedbackDetails = $this->getPerformanceReviewService()->getFeedbackById($this->feedback);
			if(date('Y-m-d', strtotime($feedbackDetails->getToDate())) >= date('Y-m-d')) {
				$this->endOfCycle = true;
			} else {
				$this->endOfCycle = false;
			}
			$this->isSubmitted = $feedbackDetails->getIsSubmitted();
			$this->IsDeleted = $feedbackDetails->getIsDeleted();
			$this->isViewOnly = false;
			if($this->IsDeleted == EmployeeMultiSourceFeedback::IS_DELETED) {
				$this->getUser()->setFlash('addFeedback.error', __('Unable to view Feedback.'));
				$this->redirect('performance/addFeedback');
			} else {
				// check Credentials to access Feedback.
				$validCredentials = true;
				if ($feedbackDetails->getReviewersNumber() != $usrNumber) {
					$this->isViewOnly = true;
				}
				if(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
					$validCredentials = true;
				} else {
					$accessibleEmployees = UserRoleManagerFactory::getUserRoleManager()->getAccessibleEntityIds('Employee');
					array_push($accessibleEmployees, strval($usrNumber));
					$empIds = $this->getPerformanceReviewService()->getEmpNumbersForFeedback($this->getPerformanceReviewService()->getCurrentPerformancePeriod());
					foreach ($empIds as $empId) {
						array_push($accessibleEmployees, $empId[0]);
					}
					if(!(in_array($feedbackDetails->getEmpNumber(), $accessibleEmployees)) && $usrNumber != $feedbackDetails->getReviewersNumber()) {
						$validCredentials = false;
					}
				}
				if(!(isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes')) {
					if(/* $feedbackDetails->getEmpNumber() != $usrNumber && */ $feedbackDetails->getReviewersNumber() != $usrNumber && $feedbackDetails->getIsSubmitted() == 0) {
						$validCredentials = false;
					}
				}
				if(!$validCredentials) {
					$this->getUser()->setFlash('addFeedback.error', __('Unable to view Feedback.'));
					$this->redirect('performance/addFeedback');
				}
			}
			if($feedbackDetails->getReviewersNumber() == $usrNumber) {
				$this->getUser()->setFlash('addFeedbackTop.warning.nofade', __('Feedback will be editable till appraisal cycle ends.'));
			}
		}
		else {
			$this->feedback = -1; // new feedback
		    $this->isSubmitted = -1;
			$this->IsDeleted = -1;
			$this->isViewOnly = false;
		}
		$this->setForm(new addFeedbackForm(array(), $params ,true));
		
		if($request->isMethod('post')){
			$this->form->bind($request->getParameter($this->form->getName()));
			if ($this->form->isValid()) {
				$this->action = $request->getParameter('hdnAction');
				if($this->action == 'back') {
					$this->redirect('performance/viewMyFeedback');
				} else if($this->action == 'draft'){
					$id = $this->form->save(true, $request->getParameter("hdnId"));
					$this->getUser()->setFlash('addFeedback.success', __('Feedback saved as Draft. Submit feedback to publish it. '));
				} else if($this->action == 'save') {
					$id = $this->form->save();
					$this->getUser()->setFlash('addFeedback.success', __('Feedback has been submitted.'));
				} else if($this->action == 'saveDraft') {
					$id = $this->form->save(true, $request->getParameter("hdnId"), true);
					$this->getUser()->setFlash('addFeedback.success', __('Feedback has been submitted.'));
				} else if($this->action == 'discard') {
					$this->form->discard($request->getParameter("hdnId"));
					$this->getUser()->setFlash('addFeedback.success', __('Feedback draft has been Discarded.'));
					$this->redirect('performance/addFeedback');
				}
				$this->redirect('performance/addFeedback?id='.$id);
			}
		} // end of post
		
	}
}
