<?php
/**
 *
 * @param  NULL
 * @return NULL
 * @author Sujata Halwai<sujata.halwai@synerzip.com>
 */
abstract class digestAction extends sfAction {
    
    public function preExecute(){
        $this->authentication = false;
        $headers = apache_request_headers();
        if(isset($headers['auth-key'])) {
                if(base64_decode($headers['auth-key']) == 'yourAuthKey') {
                        $this->authentication = true;
                } else {
                        $this->authentication = false;
                }
        } else {
                $this->authentication = false;
        }
    }
        
    public function execute($request) {
        $logger = Logger::getLogger('digest');
        if(!$this->authentication) {
            $logger->error('Authentication failed.');
            exit;
        }
        $logger->info('Authentication successful.');
        set_time_limit(0);
        $day = date('d.m.Y');
        if ( $this->isHoliday($day) || ($this->isWeekend($day, true) && (date("D", strtotime($day)) == "Sat" || date("D", strtotime($day)) == "Sun")) ){
            $logger->info('Today is holiday or not working day (weekends).');
            exit(0);
        }else{
            $this->performAction();
        }
    }
    
    abstract public function performAction();
    
    public function isWeekend($day) {
        $workWeekService = new WorkWeekService();
        $workWeekService->setWorkWeekDao(new WorkWeekDao());
        return $workWeekService->isWeekend($day, true);
    }
    
    public function isHoliday($day) {
        $holidayService = new HolidayService();
        return $holidayService->isHoliday($day);
    }
}
