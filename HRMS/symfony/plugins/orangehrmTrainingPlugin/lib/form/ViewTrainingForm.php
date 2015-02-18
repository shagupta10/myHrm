<?php


class ViewTrainingForm extends BaseForm {
	private $trainingService;
	private $trainerService;
	private $employeeService;
	public $prepoluldatedTrainers = array();
	public $prepoluldatedAttendees = array();
	
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
		
		$this->setWidgets(array(
				'trainingName' => new sfWidgetFormInputText(),
				'trainer' => new sfWidgetFormInputText(),
				'attendees' => new sfWidgetFormInputText(),
				'trainingDate' => new ohrmWidgetFormDateRange(array(  
                    'from_date' => new ohrmWidgetDatePicker(array(), array('id' => 'viewTraining_fromDate')),  
                    'to_date' => new ohrmWidgetDatePicker(array(), array('id' => 'viewTraining_toDate')),
                    'from_label' => 'From',
                    'to_label' => 'To'
                	)), 
				));
		
		$inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
		
		$this->setValidators(array(
				'trainingName' => new sfValidatorString(array('required' => false, 'max_length' => 200)),
				'trainer' => new sfValidatorString(array('required' => false)),
				'attendees' => new sfValidatorString(array('required' => false)),
				'trainingDate' => new sfValidatorDateRange(array(
						'from_date' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false)),
						'to_date' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false)),
						'required' => false
				), array('invalid' => 'To date should be after from date')),
				));
		
		$this->widgetSchema->setNameFormat('viewTraining[%s]');
		$this->getWidgetSchema()->setLabels($this->getFormLabels());
	}
	
	protected function getFormLabels() {
		$labels = array(
				'trainingDate' =>__('Training Date'),
				'trainingName' =>__('Training Name'),
		);
		return $labels;
	}
	
	public function getBindedSearchParameters() {
		$searchParameters = new TrainingSearchParameters();
		$searchParameters->setTrainingName(trim($this->getValue('trainingName')));
		$trainingDate = $this->getValue('trainingDate');
		$searchParameters->setFromDate($trainingDate['from'] == null ? '1900-01-01': $trainingDate['from']);
		$searchParameters->setToDate($trainingDate['to'] == null ? '2200-01-01': $trainingDate['to']);
		if($this->getValue('attendees') != "") {
			$searchParameters->setAttendees(explode(',', $this->getValue('attendees')));
		}
		if($this->getValue('trainer') != "") {
			$allTrainersArray = explode(',', $this->getValue('trainer'));
			$trainerEmpArray = array();
			$trainerArray = array();
			foreach($allTrainersArray as $trainer) {
				$trainerId = explode('_', $trainer);
				if($trainerId[1] == TrainingTrainer::INTERNAL_TRAINER) {
					array_push($trainerEmpArray, $trainerId[0]);
				} else {
					array_push($trainerArray, $trainerId[0]);
				}
			}
			$searchParameters->setTrainers($trainerArray);
			$searchParameters->setTrainerEmps($trainerEmpArray);
		}
		return $searchParameters;
	}
	
	public function setDefaultDataToWidgets(TrainingSearchParameters $searchParam) {
		$newSearchParam = new CandidateSearchParameters();
		$this->setDefault('trainingName', $searchParam->getTrainingName());
		$displayFromDate = ($searchParam->getFromDate() == $newSearchParam->getFromDate()) ? "" : $searchParam->getFromDate();
		$displayToDate = ($searchParam->getToDate() == $newSearchParam->getToDate()) ? "" : $searchParam->getToDate();
		$this->setDefault('from_date', ($displayFromDate));
		$this->setDefault('to_date', ($displayToDate));
		$this->prepopulateTokenInput($searchParam);
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
	
	public function getEmployeeListAsJson() {
		return $this->getEmployeeService()->getEmployeeListAsJson();
	}
	
	 public function prepopulateTokenInput(TrainingSearchParameters $searchParam) {
		$trainerEmp = $searchParam->getTrainerEmps();
		$trainer = $searchParam->getTrainers();
		$attendees = $searchParam->getAttendees();
		$array = array();
		foreach ($trainerEmp as $empNumber) {
			$employee = $this->getEmployeeService()->getEmployee($empNumber);
			$array[] = array('id' => $employee->getEmpNumber()."_".TrainingTrainer::INTERNAL_TRAINER, 'name' => $employee->getFirstAndLastNames());
		}
		foreach ($trainer as $trainerId) {
			$trainerObj = $this->getTrainerService()->getTrainerById($trainerId);
			$array[] = array('id' => $trainerObj->getId()."_".TrainingTrainer::EXTERNAL_TRAINER, 'name' => $trainerObj->getTrainerFirstAndLastNames());
		}
		$this->prepoluldatedTrainers = $array;
		$array = array();
		foreach ($attendees as $attendee) {
			$employee = $this->getEmployeeService()->getEmployee($attendee);
			$array[] = array('id' => $employee->getEmpNumber(), 'name' => $employee->getFirstAndLastNames());
		}
		$this->prepoluldatedAttendees = $array;
	}
	
	public function getPrepopulatedTrainers() {
		return json_encode($this->prepoluldatedTrainers);
	}
	
	public function getPrepopulatedAttendees() {
		return json_encode($this->prepoluldatedAttendees);
	}
}