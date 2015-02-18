<?php


class getTrainingDetailsAction extends sfAction {
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
		
		$trainingId = $request->getParameter('tid');
		$training = $this->getTrainingService()->getTrainingById($trainingId);
		$dateList = $training->getTrainingSchedule();
		foreach ($dateList as $key => $val){
			$sessionDate[] = $val['sessionDate'];
		}
		$from_Date = min($sessionDate);
		$to_Date = max($sessionDate);
	
		$trainerNames = array();
		foreach ($training->getTrainingTrainer() as $trainer) {
			if($trainer->getTrainerFirstAndLastNames()) {
				array_push($trainerNames, $trainer->getTrainerFirstAndLastNames());
			}
		}
		$htmlString = '<table class = "descTable" width = "100%"><tbody><tr><td>Topic :</td><td><strong><u>'.$training->getTopic().'</u></strong></td></tr>
				<tr><td valign="top">Description :</td><td>'.nl2br($training->getDescription()).'</td></tr>
				<tr><td>Training Period :</td>'.$scheduleHTML.'<td>'.set_datepicker_date_format($from_Date).' - '.set_datepicker_date_format($to_Date).'</td></tr>
				<tr><td colspan = 3><table class="descInnerTable" border = "3"><caption>Session schedule</caption><tbody><tr><th>Date</th><th>From</th><th>To</th></tr>';
		if(count($training->getTrainingSchedule()) > 0) {
			foreach ($training->getTrainingSchedule() as $schedule) {
				$htmlString.='<tr><td>'.$schedule->getSessionDate().'</td><td>'.$schedule->getFromTime().'</td><td>'.$schedule->getToTime().'</td></tr>';
			}
		} else {
			$htmlString.='<tr><td colspan = "3"><strong>No records found.</strong></tr>';
		}			
		$htmlString.= '</tbody></table></td></tr>
				<tr><td>Total Hours :</td><td>'.$training->getTotalHours().'</td></tr>
				<tr><td>Trainers :</td><td>'.implode(', ', $trainerNames).'</td></tr>
				<tr><td>Location :</td><td>'.$training->getLocation().'</td></tr></tbody></table><hr>';
		echo $htmlString;
	}
}