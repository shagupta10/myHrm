<?php


class ShowTrainingsForm extends sfForm {
	private $trainingService;
	public $trainingIds = array();
	public function getTrainingService() {
		if(is_null($this->trainingService)) {
			$this->trainingService = new TrainingService();
			$this->trainingService->setTrainingDao(new TrainingDao());
		}
		return $this->trainingService;
	}
	
	public function getSubscribedTrainings() {
		$regTrainings = $this->getTrainingService()->getSubscribedTrainings(sfContext::getInstance()->getUser()->getEmployeeNumber());
		$trainingArray = array();
		foreach ($regTrainings as $training) {
			array_push($this->trainingIds, $training->getId());
			$trainingObj['type'] = 'Training Session';
			$trainingObj['description'] = $training->getDescription();
			$trainingObj['title'] = $training->getTopic();
			$schedules = $training->getTrainingSchedule();
			foreach ($schedules as $schedule) {
				if($schedule->getFromTime() == "")
					$time = "00:00";
				else
					$time = date('H:i',strtotime($schedule->getFromTime()));
				$trainingObj['date'] = $schedule->getSessionDate(). " ".$time;
				array_push($trainingArray ,$trainingObj);
			}
		}
		$this->trainingIds;
		return json_encode($trainingArray);
	}
}