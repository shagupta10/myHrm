<?php


class trainerInfoAction extends sfAction {
	private $trainerService;
	
	public function getTrainerService() {
		if(is_null($this->trainerService)) {
			$this->trainerService = new TrainerService();
			$this->trainerService->setTrainerDao(new TrainerDao());
		}
		return $this->trainerService;
	}
	
	public function execute($request) {
		$this->setLayout(false);
		sfConfig::set('sf_web_debug', false);
		sfConfig::set('sf_debug', false);
		$this->getResponse()->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
		$trainer = $this->getTrainerService()->getTrainerById($request->getParameter('id'));
		return $this->renderText(json_encode($trainer->toArray()));
	}
}