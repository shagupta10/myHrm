<?php
/**
 * returns events
 * @return Events
 * @package    api
 * @subpackage Action
 * @author     Abhishek Shringi
 */
class eventsAction extends BaseActionRest {
   
    private $employeeService;
    private $holidayService;
   
    public function getEmployeeService(){
		if(is_null($this->employeeService)){
			$this->employeeService =new EmployeeService();
			$this->employeeService->getEmployeeDao(new EmployeeDao());
		}
		return $this->employeeService;
	}
	
	public function getHolidayService() {
		if (is_null($this->holidayService)) {
			$this->holidayService = new HolidayService();
		}
		return $this->holidayService;
	}
	
	public function preExecute() {
    	parent::preExecute();
    }
    
	public function execute($request) {
		set_time_limit(0);
		if($this->authentication == false) {
			return $this->renderText(json_encode(array('response' => 'failure', 'code' => '401'), JSON_FORCE_OBJECT));
		} 
    	$request->setParameter('service', 'events');
    	$this->service = 'events';    	
		$birthday = $this->getEmployeeService()->getTodaysBirthday(true);		
		$holiday = $this->getHolidayService()->getUpcomingPublicHolidayList();
		
		if($request->getParameter('id') == BaseActionRest::BIRTHDAY) {
			return $this->renderText(json_encode($birthday));
		} else if($request->getParameter('id') == BaseActionRest::HOLIDAY) {
			return $this->renderText(json_encode($holiday));
		} else {
			return $this->renderText(json_encode(array_merge($birthday, $holiday)));
		}
    }
}