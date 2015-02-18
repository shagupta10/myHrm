<?php

class TrainingAttendanceForm extends sfForm {
	public $training;
	public $trainingSchedule;
	public $trainingAttendees;
	public $trainingAttendance = array();
	public $scheduleArray = array();
	private $trainingService;
	public $emails;
	
	public function getTrainingService() {
		if(is_null($this->trainingService)) {
			$this->trainingService = new TrainingService();
			$this->trainingService->setTrainingDao(new TrainingDao());
		}
		return $this->trainingService;
	}
	public function configure() {
		$this->setWidgets(array(
				'trainingId' => new sfWidgetFormInputHidden()
				));
		
		$this->setValidators(array(
				'trainingId' => new sfValidatorString(array('required' => false))
				));
		$this->widgetSchema->setNameFormat('trainingAttendance[%s]');
	}
	
	public function getTrainingDetails($trainingId) {
		$this->training =  $this->getTrainingService()->getTrainingById($trainingId);
		$this->trainingSchedule = $this->training->getTrainingSchedule();
		$this->trainingAttendees = $this->training->getTrainingAttendees();
		$emailArray = array();
		foreach ($this->trainingAttendees as $record) {
			array_push($emailArray, $record->getEmployee()->getEmpWorkEmail());
		}
		foreach ($this->trainingSchedule as $schedule) {
			array_push($this->scheduleArray, $schedule->getSessionDate());
		}
		$this->emails = implode(", ", $emailArray);
	}
	
	public function getTrainingAttendance($id) {
		$array = array();
		$attendance = $this->getTrainingService()->getTrainingAttendanceByTrainingId($id);
		foreach ($attendance as $record) {
			array_push($array, 'att_'.$record->getEmpNumber().'_sch_'.$record->getScheduleId());
		}
		$this->trainingAttendance = $array;
	}
	
	public function getTrainingAttendanceforWidget() {
		return json_encode($this->trainingAttendance);
	}
}