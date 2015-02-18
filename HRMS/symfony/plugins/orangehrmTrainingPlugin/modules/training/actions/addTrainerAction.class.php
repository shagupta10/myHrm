<?php


class addTrainerAction extends sfAction {
	private $trainerService;
	public function setForm(sfForm $form) {
		if (is_null($this->form)) {
			$this->form = $form;
		}
	}
	
	public function getForm() {
		return $this->form;
	}
	
	public function getTrainerService() {
		if(is_null($this->trainerService)) {
			$this->trainerService = new TrainerService();
			$this->trainerService->setTrainerDao(new TrainerDao());
		}
		return $this->trainerService;
	}
	
	public function execute($request) {
		$this->setForm(new AddTrainerForm(array(), array(), true));
	    if ($request->isMethod('post')) {
			$this->form->bind($request->getParameter($this->form->getName()));
			if($this->form->isValid()) {
				$trainer = $this->form->save();
				if(is_null($trainer)) {
					$this->getUser()->setFlash('addTrainer.success', __(TopLevelMessages::SAVE_SUCCESS));
				} else {
					$this->getUser()->setFlash('addTrainer.success', __(TopLevelMessages::UPDATE_SUCCESS));
				}
				$this->redirect('training/addTrainer');
			}
		} else {
			$trainerList = $this->getTrainerService()->getTrainerList();
			$this->_setListComponent($trainerList);  
		} 
	}
	
	/**
	 *
	 * @param <type> $candidateHistory
	 */
	private function _setListComponent($trainerList) {
		$configurationFactory = new TrainerHeaderFactory();
		ohrmListComponent::setConfigurationFactory($configurationFactory);
		ohrmListComponent::setListData($trainerList);
	}
}