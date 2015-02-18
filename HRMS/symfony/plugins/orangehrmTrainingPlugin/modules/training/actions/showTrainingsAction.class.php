<?php


class showTrainingsAction extends sfAction {
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
		$this->userObj = $this->getUser()->getAttribute('user');
		$this->setForm(new ShowTrainingsForm());
		$filter = trim($request->getParameter('filter'));
		$page = $request->getParameter('page');
		
        if($request->isMethod('post')) {
				$prop = array('filter' => $filter);
				$this->getUser()->setAttribute('showTrainingfilter', $filter);
		} else {
			$prop = array('filter' => 'Upcoming');
			if($page > 0) {
				$prop = array('filter' => $this->getUser()->getAttribute('showTrainingfilter'));
			}
		}
        $this->form->getSubscribedTrainings();
        if ($request->isMethod('post')) {
            $page = 1;
            $this->clues['pageNo'] = 1;
        } elseif ($request->getParameter('page')) {
            $this->clues['pageNo'] = $page;
        } elseif ($this->clues['pageNo']) {
            $page = $this->clues['pageNo'];
        }

        /* Pagination */
        if (!isset($page)) {
            $page = 1;
        }

        $this->pager = new SimplePager('ShowTrainingsAction', 20);
        $this->pager->setPage($page);
        $this->pager->setNumResults($this->getTrainingService()->getTrainingListByProperties($prop, null, null, true));
        $this->pager->init();

        /* Fetching trainings */
        $offset = $this->pager->getOffset();
        $offset = empty($offset) ? 0 : $offset;
        $limit = $this->pager->getMaxPerPage();
        $this->trainings = $this->getTrainingService()->getTrainingListByProperties($prop, $offset, $limit);
        $this->prop = $prop;
	}
}