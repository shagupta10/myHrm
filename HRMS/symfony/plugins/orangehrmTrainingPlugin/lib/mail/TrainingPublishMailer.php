<?php


class TrainingPublishMailer extends orangehrmMailer {
	
	private $employeeService;
	//protected $logger;
 	public function getEmployeeService() {
        if(is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }
    
    public function getTrainingService() {
    	if(is_null($this->trainingService)) {
    		$this->trainingService = new TrainingService();
    		$this->trainingService->setTrainingDao(new TrainingDao());
    	}
    	return $this->trainingService;
    }
	
	public function __construct() {
		parent::__construct();
	}
	
	public function send($trainingId) {
		if (!empty($this->mailer)) {
			$properties = array('emp_work_email');
			$orderField = 'lastName';
			$orderBy = 'ASC';
			$array = array();
			$emails = $this->getEmployeeService()->getEmployeePropertyList($properties, $orderField, $orderBy, $excludeTerminatedEmployees = true, $excludeConsultant = true);
			foreach ($emails as $email) {
				array_push($array, $email['emp_work_email']);
			}
			$this->message->setBcc($array);
			$this->message->setBody($this->getBody($trainingId),'text/html');
			$this->message->setFrom($this->getSystemFrom());
			$this->message->setSubject($subject);
			$this->message->setContentType("text/html");
			$this->mailer->send($message);
		}
	}
	
	private function getBody($trainingId) {
		$training = $this->getTrainingService()->getTrainingById($trainingId);
		$stringHTML = "<html>";
	}
}