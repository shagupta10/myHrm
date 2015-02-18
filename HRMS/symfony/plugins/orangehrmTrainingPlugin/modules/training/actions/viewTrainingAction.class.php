<?php

class viewTrainingAction extends sfAction {
	private $trainingService;
	
	public function setForm(sfForm $form) {
		if (is_null($this->form)) {
			$this->form = $form;
		}
	}
	
	public function getForm() {
		return $this->form;
	}
	
	public function getTrainingService() {
		if(is_null($this->trainingService)) {
			$this->trainingService = new TrainingService();
			$this->trainingService->setTrainingDao(new TrainingDao());
		}
		return $this->trainingService;
	}
	
	public function execute($request) {
		$sortField = $request->getParameter('sortField');
		$sortOrder = $request->getParameter('sortOrder');
		$isPaging = $request->getParameter('pageNo');
		$pageNumber = $isPaging;
		if (!is_null($this->getUser()->getAttribute('trainingPageNumber')) && !($pageNumber >= 1)) {
			$pageNumber = $this->getUser()->getAttribute('trainingPageNumber');
		}
		$this->getUser()->setAttribute('trainingPageNumber', $pageNumber);
		
		$this->setForm(new ViewTrainingForm(array(), null, true));
		$searchParam = new TrainingSearchParameters();
		$noOfRecords = $searchParam->getLimit();
		$offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;
		
		if (!empty($sortField) && !empty($sortOrder) || $isPaging > 0) {
			if ($this->getUser()->hasAttribute('srchTrainingParams')) {
				$searchParam = $this->getUser()->getAttribute('srchTrainingParams');
				$this->form->setDefaultDataToWidgets($searchParam);
			}
			if(!empty($sortField) && !empty($sortOrder) ) { 
				$searchParam->setSortField($sortField);
				$searchParam->setSortOrder($sortOrder);
			}
		} else {
			$this->getUser()->setAttribute('srchTrainingParams', $searchParam);
			$offset = 0;
			$pageNumber = 1;
		}
		$searchParam->setOffset($offset);
		$trainingList = $this->getTrainingService()->searchTrainings($searchParam);
		$this->_setListComponent($trainingList, $noOfRecords, $searchParam, $pageNumber);
		$params = array();
		$this->parmetersForListComponent = $params;
		if (empty($isPaging)) {
			if ($request->isMethod('post')) {
				$pageNumber = 1;
				$searchParam->setOffset(0);
				$this->getUser()->setAttribute('trainingPageNumber', $pageNumber);
				$this->form->bind($request->getParameter($this->form->getName()));
				if($this->form->isValid()) {
					$searchParam = $this->form->getBindedSearchParameters();
					$this->form->setDefaultDataToWidgets($searchParam);
					$this->getUser()->setAttribute('srchTrainingParams', $searchParam);
					$trainingList = $this->getTrainingService()->searchTrainings($searchParam);
					$this->_setListComponent($trainingList, $noOfRecords, $searchParam, $pageNumber);
				}
			}
		}
	}
	
	/**
	 *
	 * @param <type> $candidateHistory	
	 */
	private function _setListComponent($trainingList, $noOfRecords, $searchParam, $pageNumber) {
		$configurationFactory = new TrainingHeaderFactory();
		ohrmListComponent::setConfigurationFactory($configurationFactory);
		ohrmListComponent::setListData($trainingList);
 		ohrmListComponent::setPageNumber($pageNumber);
		ohrmListComponent::setItemsPerPage($noOfRecords);
		ohrmListComponent::setNumberOfRecords($this->getTrainingService()->searchTrainings($searchParam, true));
	}
}