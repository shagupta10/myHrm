<?php


class AddTrainerForm extends sfForm {
	private $trainerService;
	public function getTrainerService() {
		if(is_null($this->trainerService)) {
			$this->trainerService = new TrainerService();
			$this->trainerService->setTrainerDao(new TrainerDao());
		}
		return $this->trainerService;
	}
	
	public function configure() {
		$this->setWidgets(array(
			'id' => new sfWidgetFormInputHidden(),
            'firstName' => new sfWidgetFormInputText(array()),
            'lastName' => new sfWidgetFormInputText(array()),
			'details' => new sfWidgetFormTextarea(array(), array(/*  "style"=>"height:150px;width:500px" */)),
		));
		
		$this->setValidators(array(
			'id' => new sfValidatorString(array('required' => false)),
			'firstName' => new sfValidatorString(array('required' => true, 'max_length' => 50)),
			'lastName' => new sfValidatorString(array('required' => false, 'max_length' => 50)),
            'details' => new sfValidatorString(array('required' => false))
		));
		$this->widgetSchema->setNameFormat('trainer[%s]');
		$this->getWidgetSchema()->setLabels($this->getFormLabels());
	}
	
	public function save() {
		$trainer = new Trainer();
		$trainer->setFirstName($this->getValue('firstName'));
		$trainer->setLastName($this->getValue('lastName'));
		$trainer->setDetails($this->getValue('details'));
		if($this->getValue('id') != "") {
			$trainer->setId($this->getValue('id'));
			return $this->getTrainerService()->updateTrainer($trainer);
		}
		return $trainer->save();
	}
	
	public function setDefaultValues() {
		
	}
	
	protected function getFormLabels() {
		$labels = array(
				'firstName' =>__('First name'.'<em>*</em>'),
				'lastName' =>__('Last name')
		);
		return $labels;
	}
}