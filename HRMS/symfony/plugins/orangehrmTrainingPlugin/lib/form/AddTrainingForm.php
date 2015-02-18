<?php


class AddTrainingForm extends sfForm {
	private $employeeService;
	private $trainingService;
	private $trainerService;
	public $prePopulatedTrainers = array();
	public $prePopulatedScheduleDetails = array();
	
	public function getTrainingService() {
		if(is_null($this->trainingService)) {
			$this->trainingService = new TrainingService();
			$this->trainingService->setTrainingDao(new TrainingDao());
		}
		return $this->trainingService;
	}
	
	public function getTrainerService() {
		if(is_null($this->trainerService)) {
			$this->trainerService = new TrainerService();
			$this->trainerService->setTrainerDao(new TrainerDao());
		}
		return $this->trainerService;
	}
	
	/**
	 * Get VacancyService
	 * @returns VacncyService
	 */
	public function getEmployeeService() {
		if (is_null($this->employeeService)) {
			$this->employeeService = new EmployeeService();
			$this->employeeService->setEmployeeDao(new EmployeeDao());
		}
		return $this->employeeService;
	}
	
	public function configure() {
		$this->trainingId = $this->getOption('trainingId');
		$points = array();
		$points[''] = '--Select--';
		for($i=1; $i<=10; $i++) {
			$points[$i] = $i;
		}
		$this->setWidgets(array(
			'id' => new sfWidgetFormInputHidden(),
            'topic' => new sfWidgetFormInputText(array(), array( "style"=>"width:400px")),
            'trainingDesc' => new sfWidgetFormTextarea(array(), array( "style"=>"height:150px;width:500px")),
			'trainingDates' => new sfWidgetFormInputHidden(array()),
			'attendeePoint' => new sfWidgetFormSelect( array(
                        'choices' => $points)),
			'trainerPoint' => new sfWidgetFormSelect( array(
                        'choices' =>$points)),
			'totalHours' => new sfWidgetFormInputText(array(), array('id' => 'training_hours')),
			'trainer' => new sfWidgetFormInputText(array()),
			'location' => new sfWidgetFormInputText(),
			'isPublished' => new sfWidgetFormInputCheckbox(array('value_attribute_value' => 1), array()),
        ));
		
		$this->setValidators(array(
			'id' => new sfValidatorString(array('required' => false)),
			'topic' => new sfValidatorString(array('required' => true, 'max_length' => 200)),
			'trainingDesc' => new sfValidatorString(array('required' => false)),
			'trainingDates' => new sfValidatorString(array('required' => false)),
			'attendeePoint' => new sfValidatorNumber(array('required' => true)),
			'trainerPoint' => new sfValidatorNumber(array('required' => true)),
			'totalHours' => new sfValidatorNumber(array('required' => false)),
			'trainer' => new sfValidatorString(array('required' => true)),
			'location' => new sfValidatorString(array('required' => false)),
            'isPublished' => new sfValidatorString(array('required' => false)),
		));
		
		$this->widgetSchema->setNameFormat('training[%s]');
	}
	
	public function save() {
		$id = $this->getValue('id');
		if(empty($id)) {
			$training = new Training();
			$idGenService = new IDGeneratorService();
			$idGenService->setEntity($training);
			$training->setId($trainingId = $idGenService->getNextID());
			$training->setTopic($this->getValue('topic'));
			$training->setDescription($this->getValue('trainingDesc'));
			$training->setAttendeePoint($this->getValue('attendeePoint'));
			$training->setTrainerPoint($this->getValue('trainerPoint'));
			$training->setTotalHours($this->getValue('totalHours'));
			$training->setLocation($this->getValue('location'));
			$training->setIsPublished(Training::IS_NOT_PUBLISHED);
			$training->setCreatedBy(sfContext::getInstance()->getUser()->getEmployeeNumber());
			$training->setCreatedDate(date('Y-m-d'));
			$isPublished = $this->getValue('isPublished');
			if(!empty($isPublished)) {
				$training->setIsPublished(Training::IS_PUBLISHED);
			}
			$training->save();
			$trainers = explode(',', $this->getValue('trainer'));
			foreach ($trainers as $trainer) {
				$trainerSplit = explode('_', $trainer);
 				$trainerObj = new TrainingTrainer();
				$trainerObj->setTrainerType($trainerSplit[1]);
				$trainerObj->setTrainingId($trainingId);	
				if($trainerSplit[1] == TrainingTrainer::EXTERNAL_TRAINER) {
					$trainerObj->setTrainerId($trainerSplit[0]);
				} else {
					$trainerObj->setEmpNumber($trainerSplit[0]);
				}
				$trainerObj->save();
			}
			//Save Training Schedule
			$scheduleDetails = json_decode($this->getValue('trainingDates'), true);
			$this->saveArrayOfScheduleDetails($scheduleDetails, $trainingId);
			return $trainingId;
		} else {
			$training = $this->getTrainingService()->getTrainingById($id);
			$training->setTopic($this->getValue('topic'));
			$training->setDescription($this->getValue('trainingDesc'));
			$training->setAttendeePoint($this->getValue('attendeePoint'));
			$training->setTrainerPoint($this->getValue('trainerPoint'));
			$training->setTotalHours($this->getValue('totalHours'));
			$training->setLocation($this->getValue('location'));
			$training->setIsPublished(Training::IS_NOT_PUBLISHED);
			$training->setUpdatedBy(sfContext::getInstance()->getUser()->getEmployeeNumber());
			$training->setUpdatedDate(date('Y-m-d'));
			$isPublished = $this->getValue('isPublished');
			if (!empty($isPublished)) {
				$training->setIsPublished(Training::IS_PUBLISHED);
			}
			// update Trainer
			$trainingId = $training->getId();
			$newTrainers = explode(',', $this->getValue('trainer'));
			$existingTrainers = $training->getTrainingTrainer();
			$existingTrainersArray = array();
			$toAddArray = array();
			$toDeleteArray = array();
			foreach ($existingTrainers as $existingTrainer) {
				if($existingTrainer->getTrainerType() == TrainingTrainer::EXTERNAL_TRAINER) {
					array_push($existingTrainersArray, $existingTrainer->getTrainerId()."_".$existingTrainer->getTrainerType());
				} else {
					array_push($existingTrainersArray, $existingTrainer->getEmpNumber()."_".$existingTrainer->getTrainerType());
				}
			}
			foreach ($newTrainers as $newTrainer) {
				if(!in_array($newTrainer, $existingTrainersArray)) {
					array_push($toAddArray, $newTrainer);
				}
			}
			foreach ($existingTrainersArray as $existingTrainer) {
				if(!in_array($existingTrainer, $newTrainers)) {
					array_push($toDeleteArray, $existingTrainer);
				}
			}
			$this->updateTrainers($toAddArray, $toDeleteArray, $training);
			// End - update trainer
			//update Schedule Details
			$scheduleDetails = json_decode($this->getValue('trainingDates'), true);
			$existingSchedule = $training->getTrainingSchedule();
			$existingScheduleIds = array();
			foreach ($existingSchedule as $sch) {
				array_push($existingScheduleIds, $sch->getId());
			}
			$this->saveArrayOfScheduleDetails($scheduleDetails, $trainingId, $existingScheduleIds);
			//End schedule Details
			
		    $this->getTrainingService()->updateTraining($training);
		    return $id;
		}
	}
	
	public function updateTrainers($toAddArray, $toDeleteArray, $training) {
		foreach($toAddArray as $trainer) {
			$trainerId = explode('_', $trainer);
			$ttrainer = new TrainingTrainer();
			if($trainerId[1] == TrainingTrainer::EXTERNAL_TRAINER) {
				$ttrainer->setTrainerId($trainerId[0]);
			} else {
				$ttrainer->setEmpNumber($trainerId[0]);
			}
			$ttrainer->setTrainerType($trainerId[1]);
			$ttrainer->setTrainingId($training->getId());
			$ttrainer->save();
		}
		$this->getTrainerService()->deleteTrainingTrainer($toDeleteArray, $training->getId());
	}
	
	public function setDefaultDataToWidgets(Training $training) {
		$this->setDefault('id', $training->getId());
		$this->setDefault('topic', $training->getTopic());
		$this->setDefault('trainingDesc', $training->getDescription());
		$this->setDefault('attendeePoint', $training->getAttendeePoint());
		$this->setDefault('trainerPoint', $training->getTrainerPoint());
		$this->setDefault('totalHours', $training->getTotalHours());
		$this->setDefault('location', $training->getLocation());
		if ($training->getIsPublished() == Training::IS_PUBLISHED) {
			$this->setDefault('isPublished', $training->getIsPublished());
		}
		$trainers = $training->getTrainingTrainer();
		$jsonArray= array();
		foreach($trainers as $trainer) {
			if($trainer->getTrainerType() == TrainingTrainer::INTERNAL_TRAINER) {
				$jsonArray[] = array('name' => $trainer->getTrainerEmployee()->getFirstAndLastNames(), 'id' => $trainer->getTrainerEmployee()->getEmpNumber()."_".TrainingTrainer::INTERNAL_TRAINER);
			} else {
				$jsonArray[] = array('name' => $trainer->getTrainer()->getFirstAndLastNames(), 'id' => $trainer->getTrainer()->getId()."_".TrainingTrainer::EXTERNAL_TRAINER);
			}
		}
		$this->prePopulatedTrainers = json_encode($jsonArray);
		$scheduleDetails = $training->getTrainingSchedule();
		$jsonArray= array();
		foreach($scheduleDetails as $detail) {
			$jsonArray[] = array('id'=>$detail->getId() ,'date' => set_datepicker_date_format($detail->getSessionDate()), 'topic' => $detail->getTopic(), 'desc' => $detail->getDescription(), 'fromtime' => $detail->getFromTime(), 'totime' => $detail->getToTime());
		}
		$this->prePopulatedScheduleDetails = json_encode($jsonArray);
	}
	
	public function getTrainers() {
		$filters["termination"] = 1; // Current employee
		$searchParameters = new EmployeeSearchParameterHolder();
		$searchParameters->setFilters($filters);
		$searchParameters->setLimit(99999); // set max limit
		$employees = $this->getEmployeeService()->searchEmployees($searchParameters);
		$jsonArray = array();
		foreach ($employees as $employee) {
			$empNumber = $employee->getEmpNumber();
			$name = $employee->getFirstAndLastNames();
			$jsonArray[] = array('name' => $name, 'id' => $empNumber."_".TrainingTrainer::INTERNAL_TRAINER);
		}
		$trainers = $this->getTrainerService()->getTrainerList();
		foreach ($trainers as $trainer) {
			$id = $trainer->getId();
			$name = $trainer->getFirstAndLastNames(); 
			$jsonArray[] = array('name' => $name, 'id' => $id."_".TrainingTrainer::EXTERNAL_TRAINER);
		}
		$jsonString = json_encode($jsonArray);
		return $jsonString;
	}
	
	public function saveArrayOfScheduleDetails($scheduleDetails, $trainingId, $existingScheduleIds = null) {
		$count=0;
		$trainingScheduleArray = array();
		foreach ($scheduleDetails as $key => $value) {
			$key = split('_', $key);
			if($key[0] == 'ssn') { //check for session detail fields in array
				if($count == 0) { // if new session add as new record else update previously added record
					if(!empty($value)) {
						$trainingSchedule = $this->getTrainingService()->getTrainingScheduleById($value);
					} else {
						$trainingSchedule = new TrainingSchedule();
						$trainingSchedule->setTrainingId($trainingId);
					}
				}
				if($count == 1) {
					$trainingSchedule->setSessionDate(date('Y-m-d', strtotime($value)));
				}
				if($count == 2) { //set from time
					$a = explode(":",$value);
					$b = substr($a[1], 2);
					$c = substr($a[1], 0, 2);
					if($b == "PM" && $a[0] < 12) {
						$a[0] = $a[0] + 12;
					}
					if($b == "AM" && $a[0] == 12) {
						$a[0] = 00;
					}
					$finalFromTime = $a[0].":".$c.":00";
					$trainingSchedule->setFromTime($finalFromTime);
				}
				if($count == 3) { //set to time
					$a = explode(":",$value);
					$b = substr($a[1], 2);
					$c = substr($a[1], 0, 2);
					if($b == "PM" && $a[0] < 12) {
						$a[0] = $a[0] + 12;
					}
					if($b == "AM" && $a[0] == 12) {
						$a[0] = 00;
					}
					$finalToTime = $a[0].":".$c.":00";
					$trainingSchedule->setToTime($finalToTime);
				}
				if($count == 4) { // set topic
					$trainingSchedule->setTopic(trim($value));
				}
				if($count == 5) { // set Description
					$trainingSchedule->setDescription(trim($value));
					array_push($trainingScheduleArray, $trainingSchedule);
				}
				if($count != 5) {
					$count++;
				} else {
					$count = 0;
				}
			}
		}
		if($existingScheduleIds != null) {
			$newScheduleArray = array();
			foreach($trainingScheduleArray as $obj) {
				$obj->save();
				array_push($newScheduleArray, $obj->getId());
			}
			$toDelete = array_diff($existingScheduleIds, $newScheduleArray);
			$this->getTrainingService()->deleteTrainingSchedules($toDelete);
		} else {
			foreach($trainingScheduleArray as $obj) {
				$obj->save();
			}
		}
	}
}