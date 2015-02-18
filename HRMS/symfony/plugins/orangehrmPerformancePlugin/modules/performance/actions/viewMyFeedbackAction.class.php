<?php
 class viewMyFeedbackAction extends sfAction {
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
	
	public function execute($request) {
		$usrObj = $this->getUser()->getAttribute('user');
		$sortField = $request->getParameter('sortField');
        $sortOrder = $request->getParameter('sortOrder');
        $isPaging = $request->getParameter('pageNo');
        $id = $request->getParameter('id');
		$pageNumber = $isPaging;
		$flag = $request->getParameter('onChange') == '' ? 0 : 1;
		if(flag != 1) { $recordsLimit = $request->getParameter('recordsPerPage_Limit'); }
		$this->recordsPerPage = $recordsLimit;
		if (!is_null($this->getUser()->getAttribute('feedbackPageNumber')) && !($pageNumber >= 1)) {
			$pageNumber = $this->getUser()->getAttribute('feedbackPageNumber'); 
		}
		$this->getUser()->setAttribute('feedbackPageNumber', $pageNumber);
		$searchParam = new MyFeedbackSearchParameters();
		/*Set default current date for performance cycle*/
		$inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
		$dateValidator = new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
				array('invalid' => 'Date format should be ' . $inputDatePattern));
		$cycleDate = $request->getParameter('performanceCycle');
		$fromDate = $dateValidator->clean($cycleDate['from']);
		$toDate = $dateValidator->clean($cycleDate['to']);
		$performancePeriod = $this->getPerformanceReviewService()->getCurrentPerformancePeriod();
		if(empty($fromDate) && empty($toDate)){
			$fromDate = $performancePeriod->getPeriodFrom();
			$toDate = $performancePeriod->getPeriodTo();
		}
		$searchParam->setFromDate($fromDate);
		$searchParam->setToDate($toDate);
		
		if($recordsLimit){
			$searchParam->setLimit($recordsLimit);
			$noOfRecords = $searchParam->getLimit();
		}else{
			$noOfRecords = $searchParam->getLimit();
			$this->recordsPerPage = $noOfRecords;
		}
		$offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;
		$this->setForm(new viewMyFeedbackForm(array(), array(), true));
		
		if (!empty($sortField) && !empty($sortOrder) || $isPaging > 0 || $id > 0) {
			if ($this->getUser()->hasAttribute('searchFParameters')) {
				$searchParam = $this->getUser()->getAttribute('searchFParameters');
				$this->form->setDefaultDataToWidgets($searchParam);
			}
			if(!empty($sortField) && !empty($sortOrder) ) { // final added for sorting
				$searchParam->setSortField($sortField);
				$searchParam->setSortOrder($sortOrder);
			}
		} else {
			$this->getUser()->setAttribute('searchFParameters', $searchParam);
			$offset = 0;
			$pageNumber = 1;
		}
		
		$searchParam->setOffset($offset);
		$feedback = $this->getPerformanceReviewService()->searchFeedback($searchParam);
		$this->_setListComponent($usrObj, $feedback, $noOfRecords, $searchParam, $pageNumber);
		$params = array();
		$this->parmetersForListCompoment = $params;
		if (empty($isPaging)) {
			if ($request->isMethod('post') && $flag == 0) {
				$pageNumber = 1;
				$searchParam->setOffset(0);
				$this->getUser()->setAttribute('feedbackPageNumber', $pageNumber);
				$this->form->bind($request->getParameter($this->form->getName()));
				if ($this->form->isValid()) {
					$srchParams = $this->form->getSearchParamsBindwithFormData($searchParam);
					$this->getUser()->setAttribute('searchFParameters', $srchParams);
					$feedback = $this->getPerformanceReviewService()->searchFeedback($srchParams);
					$this->_setListComponent($usrObj, $feedback, $noOfRecords, $searchParam, $pageNumber);
				}
			}
		}
	}//end
	
	/**
	 *
	 * @param <type> $candidates
	 * @param <type> $noOfRecords
	 * @param CandidateSearchParameters $searchParam
	 */
	private function _setListComponent($usrObj, $feedback, $noOfRecords, MyFeedbackSearchParameters $searchParam, $pageNumber) {
		$configurationFactory = new MyFeedbackHeaderFactory();
		$buttons = array();
		$buttons['Delete'] = array(
                'label' => 'Delete',
                'type' => 'submit',
                'data-toggle' => 'modal',
                'data-target' => '#deleteConfModal',
                'class' => 'delete');
		$configurationFactory->setRuntimeDefinitions(array(
				'hasSelectableRows' => true,
				'buttons' => $buttons,
		));
		ohrmListComponent::setPageNumber($pageNumber);
		ohrmListComponent::setConfigurationFactory($configurationFactory);
		ohrmListComponent::setListData($feedback);
		ohrmListComponent::setItemsPerPage($noOfRecords);
		ohrmListComponent::setNumberOfRecords($this->getPerformanceReviewService()->getCountSearchFeedback($searchParam));
	}
} 