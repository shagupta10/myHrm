<?php


class registerTrainingAction extends sfAction {
	private $trainingService;
	public function getTrainingService() {
		if(is_null($this->trainingService)) {
			$this->trainingService = new TrainingService();
			$this->trainingService->setTrainingDao(new TrainingDao());
		}
		return $this->trainingService;
	}
	
	public function execute($request) {
		sfConfig::set('sf_web_debug', false);
		sfConfig::set('sf_debug', false);
		$response = $this->getResponse();
		$response->setHttpHeader('Expires', '0');
		$response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
		$response->setHttpHeader("Cache-Control", "private", false);
		$responseMessage = "success";
		$empNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
		$trainingId = $request->getParameter('tid');
		if($request->getParameter('isUnreg') != 'yes') {
			$training = $this->getTrainingService()->getTrainingById($trainingId);
			$trainerArray = array();
			$trainers = $training->getTrainingTrainer();
			foreach($trainers as $trainer) {
				if($trainer->getTrainerType() == TrainingTrainer::INTERNAL_TRAINER && $empNumber == $trainer->getEmpNumber()) {
					$responseMessage = "failure";
					break;
				}
			}
			if($responseMessage == 'success') {
				$trainingAttendance = new TrainingAttendees();
				$trainingAttendance->setEmpNumber($empNumber);
				$trainingAttendance->setTrainingId($trainingId);
				$trainingAttendance->setRegDate(date('Y-m-d'));
				$trainingAttendance->save();
			}
		} else {
			$this->getTrainingService()->unregisterTraining($trainingId, $empNumber);
		}
		$regTrainings = $this->getTrainingService()->getSubscribedTrainings($empNumber);
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
		return $this->renderText(json_encode(array('status' => $responseMessage, 'data' => $trainingArray)));
	}
}