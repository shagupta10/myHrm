<?php

class PerformanceConfigForm extends sfForm {
	private $performanceReview;
	private $lastPeriod;
	public $dueDate;
	public $today;
	public $currentCycle;
	public $isSave;
	
	public function getPerformanceReviewService() {
		if(is_null($this->performanceReview)) {
			$this->performanceReview = new PerformanceReviewService();
			$this->performanceReview->setPerformanceReviewDao(new PerformanceReviewDao());
		}
		return $this->performanceReview;
	}
	
	public function configure() {
		$this->setWidgets(array(
			'from_date'=> new ohrmWidgetDatePicker(array(), array('id' => 'addCandidate_fromDate')),
			'to_date'=> new ohrmWidgetDatePicker(array(), array('id' => 'addCandidate_toDate')),
			'due_date' => new ohrmWidgetDatePicker(array(), array('id' => 'addCandidate_dueDate')),
			
			/*Added by Sujata to set date according to Configuration values */
			'self_review_date' => new ohrmWidgetDatePicker(array(), array('id' => 'addCandidate_selfReviewDate')),
			'one_on_one_review_date' => new ohrmWidgetDatePicker(array(), array('id' => 'addCandidate_oneOnOneReviewDate')),
			
		));
	
		$inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
	
		$this->setValidators(array(
			'from_date'=> new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                array('invalid' => 'Date format should be ' . $inputDatePattern)),
			'to_date'=> new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                array('invalid' => 'Date format should be ' . $inputDatePattern)),
			'due_date'=> new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                array('invalid' => 'Date format should be ' . $inputDatePattern)),
			
			/* Added by Sujata*/
			'one_on_one_review_date'=> new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                array('invalid' => 'Date format should be ' . $inputDatePattern)),
            'self_review_date'=> new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false),
                    array('invalid' => 'Date format should be ' . $inputDatePattern)),
		));
		
		$this->isSave = 0;
		$this->today = date('Y-m-d');
		$this->lastPeriod = $this->getPerformanceReviewService()->getLatestPerformancePeriodCycle();
		$this->currentCycle = set_datepicker_date_format($this->lastPeriod->getPeriodFrom())." - ".set_datepicker_date_format($this->lastPeriod->getPeriodTo());
		$this->dueDate = $this->lastPeriod->getDueDate();
		$this->widgetSchema->setNameFormat('performanceConfig[%s]');
	}
	
	public function setDataToWidgets($save = false) {
		/*Added by Sujata */
		$performanceCycleLength=OrangeConfig::getInstance()->getAppConfValue(ConfigService::KEY_PERFORMANCE_CYCLE_LENGTH);
		$oneOnOneReviewDays = OrangeConfig::getInstance()->getAppConfValue(ConfigService::KEY_ONE_ON_ONE_REVIEW_DAYS);
		$selfReviewDays = OrangeConfig::getInstance()->getAppConfValue(ConfigService::KEY_SELF_REVIEW_DAYS);
		$reviewDueDays = OrangeConfig::getInstance()->getAppConfValue(ConfigService::KEY_REVIEW_DUE_DAYS);
		$lastPeriodEnd = $this->lastPeriod->getPeriodTo();				
		 
		if($save) {
			$performancePeriod = new PerformancePeriod();
			$performancePeriod->setDueDate(date('Y-m-d', strtotime($this->getValue('due_date'))));
			$performancePeriod->setPeriodFrom(date('Y-m-d', strtotime($this->getValue('from_date'))));
			$performancePeriod->setPeriodTo(date('Y-m-d', strtotime($this->getValue('to_date'))));
			
			/* Added by Sujata */			
			$performancePeriod->setOneOnOneReviewDate(date('Y-m-d',strtotime($this->getValue('one_on_one_review_date'))));
			$performancePeriod->setSelfReviewDate(date('Y-m-d',strtotime($this->getValue('self_review_date')))); 
			if($this->getPerformanceReviewService()->checkForUniquePerformanceCycle($performancePeriod)) {	
				$performancePeriod->save();
			} else {
				return false;
			}
		} else {
			$newStart = date('Y-m-d', strtotime($lastPeriodEnd. '+1 days'));
			$newTemp = date('Y-m-d', strtotime($newStart. "+$performanceCycleLength months")); 
			$newEnd = date('Y-m-d', strtotime($newTemp. '-1 days'));
			/*Added by Sujata */
			$oneOnOneReviewDate = date('Y-m-d', strtotime($newEnd. "-$oneOnOneReviewDays days"));
			$selfReviewDate = date('Y-m-d', strtotime($newEnd. "-$selfReviewDays days"));
			 
			$this->setDefault('from_date', set_datepicker_date_format($newStart));
			$this->setDefault('to_date', set_datepicker_date_format($newEnd));
			$this->setDefault('due_date', set_datepicker_date_format(date('Y-m-d', strtotime($newEnd. "+$reviewDueDays days"))));
			$this->setDefault('one_on_one_review_date', set_datepicker_date_format($this->changeDay($oneOnOneReviewDate)));
			$this->setDefault('self_review_date', set_datepicker_date_format($this->changeDay($selfReviewDate)));
		}
		return true;
	}
	
    /* Added by sujata to check weekend*/
    public function isWeekend($day) {
        $workWeekService = new WorkWeekService();
        $workWeekService->setWorkWeekDao(new WorkWeekDao());
        return $workWeekService->isWeekend($day, true);
    }

    /**Added by sujata to check holiday*/
    public function isHoliday($day) {
        $holidayService = new HolidayService();
        return $holidayService->isHoliday($day);
    }
    /*Added by sujata to check day is holiday or weekend and set it for next working day */      
    public function changeDay($day) {
    	if ($this->isWeekend($day, true) && date("D", strtotime($day)) == "Sat"){
		    return date("Y-m-d", strtotime($day."+ 2 days"));
		}else if ($this->isHoliday($day) || ($this->isWeekend($day, true) && date("D", strtotime($day)) == "Sun")){
		    return date("Y-m-d", strtotime($day."+ 1 days"));
		}else{
			return $day;
		}
		
    }
   
}