<?php

/**
* 
* @desc : Action class to Add/Edit training events
* @author: Mayur Kathale <mayur.kathale@gmail.com>
*/
class addTrainingAction extends sfAction {
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
	
	/**
	 *
	 * @param <type> $request
	 */
	public function execute($request) {
		$id = $request->getParameter('id');
		$this->trainingId = $id;
		$values = array('trainingId' => $this->trainingId);
		$this->setForm(new AddTrainingForm(array(), $values, true));
		if(empty($id)) {
			if ($request->isMethod('post')) {
				$this->form->bind($request->getParameter($this->form->getName()));
				if($this->form->isValid()) {
					$training = $this->form->save();
					if(!empty($training)) {
						$this->getUser()->setFlash('addTraining.success', __(TopLevelMessages::SAVE_SUCCESS));
						$this->redirect('training/addTraining?id='.$training);
					} else {
						$this->getUser()->setFlash('addTraining.error', __(TopLevelMessages::NO_RECORDS_FOUND));
						$this->redirect('training/addTraining');
					}
				}
			}
		} else {
			$training = $this->getTrainingService()->getTrainingById($id);
			$id = $this->form->setDefaultDataToWidgets($training);
		}
	}
}
