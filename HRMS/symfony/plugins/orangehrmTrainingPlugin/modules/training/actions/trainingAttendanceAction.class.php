<?php

class trainingAttendanceAction extends sfAction {
	private $trainingService;
	public function setForm(sfForm $form) {
		if (is_null($this->form)) {
			$this->form = $form;
		}
	}

	public function getTrainingService() {
		if(is_null($this->trainingService)) {
			$this->trainingService = new TrainingService();
			$this->trainingService->setTrainingDao(new TrainingDao());
		}
		return $this->trainingService;
	}
	
	public function execute($request) {
		$this->setForm(new TrainingAttendanceForm(array(), array(), true));
		$trainingId = $request->getParameter('id');
		if($request->isMethod('post')) {
			$this->form->bind($request->getParameter($this->form->getName()));
			//Construct array structure to save attendance from checkbox on UI
			$array = array();
			if(count($_POST['group']) == 0) {
				$training = $this->getTrainingService()->getTrainingById($trainingId);
				foreach ($training->getTrainingSchedule() as $sch) {
					$this->getTrainingService()->deleteAttendance($sch->getId(), NULL);
				}
			} else {
				foreach ($_POST['group'] as $schedule => $attendees) {
					$scheduleId = explode("_", trim($schedule,'"'));
					$attendeesArray = array();
					foreach ($attendees as $attendee => $value) {
						$attendeeId = explode("_", $attendee);
						array_push($attendeesArray, intval($attendeeId[1]));
					}
					$temp['schedule'] = intval($scheduleId[1]);
					$temp['attendees'] = $attendeesArray;
					array_push($array, $temp);
				}
			}
			//END-Construct array structure to save attendance from checkbox on UI
			
			//Get Existing Attendance
			if(count($array) > 0) {
				$existingAttendance = $this->getTrainingService()->getExistingAttendance($trainingId);
				$this->getTrainingService()->saveTrainingAttendance($array, $existingAttendance);
			}
			$this->getUser()->setFlash('trainingAttendance.success', __('Training Attendance Updated.'));
			$this->redirect('training/trainingAttendance?id='.$trainingId);
		}
		
		if(!empty($trainingId)) {
			$this->form->getTrainingDetails($trainingId);
			$this->form->setDefault('trainingId', $trainingId);
			$training = $this->getTrainingService()->getTrainingById($trainingId);
			$this->form->getTrainingAttendance($trainingId);
			$schedule = $training->getTrainingSchedule();
			$attendees = $training->getTrainingAttendees();
		}
	}
}
