<?php

class coreActions extends sfActions {
	private $holidayService;
    private $candidateService;
    private $trainingService;
    
    public function getHolidayService() {
		if (is_null($this->holidayService)) {
			$this->holidayService = new HolidayService();
		}
		return $this->holidayService;
	}
	/**
	 * Set HolidayService
	 * @param HolidayService $holidayService
	 */
	public function setHolidayService(HolidayService $holidayService) {
		$this->holidayService = $holidayService;
	}
	
	public function getCandidateService() {
		if (is_null ( $this->candidateService )) {
			$this->candidateService = new CandidateService ();
			$this->candidateService->setCandidateDao ( new CandidateDao () );
		}
		return $this->candidateService;
	}
	
	public function getTrainingService() {
		if(is_null($this->trainingService)) {
			$this->trainingService = new TrainingService();
			$this->trainingService->setTrainingDao(new TrainingDao());
		}
		return $this->trainingService;
	}
	
	public function dateConvert(){
		date_default_timezone_set('Asia/Calcutta');
		$this->convertdate = date('Y-m-d H:i:s');
		return $this->convertdate;
	}
	
	public function executePublicHolidays(sfWebRequest $request) {
		$display = $this->getHolidayService()->getUpcomingPublicHolidayList();
		$counts=count($display);
		$Data="";
		if($counts==0){
			$Data ="false";
			echo $Data;
		}else{
			
			for($j=0;$j<$counts;$j++)
			{
				if($display[$j][recurring] == 1){
					$monDate = date("m-d", strtotime($display[$j][date]));
					$formattedDate = date('Y').'-'.$monDate;
					if(strtotime($formattedDate) < strtotime(date("Y-m-d")))
					{
						$formattedDate = date('Y',strtotime('+1 year')).'-'.$monDate;
					}
					$array[$j][date]= date("d-m-Y", strtotime($formattedDate));
				}else{
				 $array[$j][date] = date("d-m-Y", strtotime($display[$j][date]));
			    }
			    $array[$j][description] = $display[$j][description];
			}
			function sortFunction( $a, $b ) {
				return strtotime($a['date']) - strtotime($b['date']);
			}
			usort($array, "sortFunction");
			
			for($i=0;$i<$counts;$i++)
			{
			 if($i%2==0){
			   $Data .= "<tr class='odd'>";
         	 }else{
	       	   $Data .= "<tr class='even'>";
        	 }
	       			$Data .= "<td>";
	       			$Data .= $array[$i][date];
	       			$Data .= "</td>";
	       			$Data .= "<td>";
	       			$Data .= $array[$i][description];
	       			$Data .= "</td>";
	       			$Data .= "</tr>";
			}
			echo $Data;
		}
		return sfView::NONE;
		}//end of executePublicHolidays

	public function executePendingFeedbackHiringMgr(sfWebRequest $request){
		$convertDate = $this->dateConvert();
		$empNo = $_SESSION['empNumber'];
		$getPendingFeedbackforcandidates =array();
		$Urlfeedback = sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/recruitment/addCandidate';
		$getPendingFeedbackforcandidates = $this->getCandidateService()->getPendingFeedbackHiringMgr($empNo,$convertDate);
		$count = count($getPendingFeedbackforcandidates);
		$htmlData="";
		if ($count == 0)
		{
			$htmlData = "false";
			echo $htmlData;
		}
		else {
			for($i=0;$i<$count; $i++)
			{
			if($i%2==0){
			$htmlData .= "<tr class='odd'>";
			}else{
			$htmlData .= "<tr class='even'>";
			}
			$htmlData .= "<td style='	text-align: left;'>";
			$htmlData .= "<a href='".$Urlfeedback."?id=".$getPendingFeedbackforcandidates[$i][id]."' target='_blank'>".$getPendingFeedbackforcandidates[$i][first_name]." ".$getPendingFeedbackforcandidates[$i][last_name]."</a>";
			$htmlData .= "</td>";
			
			$htmlData .= "<td>";
			$htmlData .= $getPendingFeedbackforcandidates[$i][name];
			$htmlData .= "</td>";
			
			$htmlData .= "</tr>";
			}
			echo $htmlData;
		}
		return sfView::NONE;
	}
	
	public function executePendingFeedbackInterviewer(sfWebRequest $request){
		$convertDate = $this->dateConvert();
		$empNo = $_SESSION['empNumber'];
		$getPendingFeedbackforcandidates =array();
		$Urlfeedback = sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/recruitment/addCandidate';
		$getPendingFeedbackforcandidates = $this->getCandidateService()->getPendingFeedbackInterviewer($empNo,$convertDate);
		$count = count($getPendingFeedbackforcandidates);
		$htmlData="";
		if ($count == 0)
		{
			$htmlData = "false";
			echo $htmlData;
		}
		else {
			for($i=0;$i<$count; $i++)
			{
			if($i%2==0){
			$htmlData .= "<tr class='odd'>";
			}else{
			$htmlData .= "<tr class='even'>";
			}
			$htmlData .= "<td style='	text-align: left;'>";
			$htmlData .= "<a href='".$Urlfeedback."?id=".$getPendingFeedbackforcandidates[$i][id]."' target='_blank'>".$getPendingFeedbackforcandidates[$i][first_name]." ".$getPendingFeedbackforcandidates[$i][last_name]."</a>";
			$htmlData .= "</td>";
			$htmlData .= "<td>";
			$htmlData .= $getPendingFeedbackforcandidates[$i][name];
			$htmlData .= "</td>";
			$htmlData .= "</tr>";
			}
			echo $htmlData;
		}
		return sfView::NONE;
	}
	public function executeCurrentInterviewsHiringMgr(sfWebRequest $request){
		$empNumber = $_SESSION['empNumber'];
		$convertDate = $this->dateConvert();
		$getCurrentAndNextFewDaysInterviewsDetails = array();
		$getCurrentAndNextFewDaysInterviewsDetails = $this->getCandidateService()->getCurrentScheduledInterviewHiringMgr($empNumber,$convertDate);
		$counts=count($getCurrentAndNextFewDaysInterviewsDetails);
		$htmlReturnData="";
		if($counts == 0)
		{
			$htmlReturnData = "false";
			echo $htmlReturnData;
		}
		else
		{
			for($i=0;$i<$counts;$i++)
			{
				
			if($i%2==0){
			$htmlReturnData .= "<tr class='odd'>";
			}else{
			$htmlReturnData .= "<tr class='even'>";
			}
					$htmlReturnData .= "<td style='	text-align: left;'>";
					$htmlReturnData .= $getCurrentAndNextFewDaysInterviewsDetails[$i][first_name]." ".$getCurrentAndNextFewDaysInterviewsDetails[$i][last_name];
					$htmlReturnData .= "</td>";
						
					$htmlReturnData .= "<td>";
					$htmlReturnData .= $getCurrentAndNextFewDaysInterviewsDetails[$i][name];
					$htmlReturnData .= "</td>";
						
					$htmlReturnData .= "<td>";
					$htmlReturnData .= $getCurrentAndNextFewDaysInterviewsDetails[$i][interview_date];
					$htmlReturnData .= "</td>";
						
					$htmlReturnData .= "<td>";
					$htmlReturnData .= $getCurrentAndNextFewDaysInterviewsDetails[$i][interview_time];
					$htmlReturnData .= "</td>";
						
					$htmlReturnData .= "</tr>";
		}
		echo $htmlReturnData;
		}
		return sfView::NONE;
	} // end of executeCurrentInterviewsHiringMgr function
    
	 public function executeCurrentInterviewsInterviewer(sfWebRequest $request){
		$empNumber = $_SESSION['empNumber'];
		$convertDate = $this->dateConvert();
		$getCurrentAndNextFewDaysInterviewsDetails = array();
		$getCurrentAndNextFewDaysInterviewsDetails = $this->getCandidateService()->getCurrentScheduledInterviewInterviewer($empNumber,$convertDate);
		$counts=count($getCurrentAndNextFewDaysInterviewsDetails);
		$htmlReturnData="";
		if($counts == 0)
		{
			$htmlReturnData = "false";
			echo $htmlReturnData;
		}
		else
		{
			for($i=0;$i<$counts;$i++)
			{
	
			if($i%2==0){
			$htmlReturnData .= "<tr class='odd'>";
			}else{
			$htmlReturnData .= "<tr class='even'>";
			}
				$htmlReturnData .= "<td style='	text-align: left;'>";
					$htmlReturnData .= $getCurrentAndNextFewDaysInterviewsDetails[$i][first_name]." ".$getCurrentAndNextFewDaysInterviewsDetails[$i][last_name];
						$htmlReturnData .= "</td>";
	
								$htmlReturnData .= "<td>";
								$htmlReturnData .= $getCurrentAndNextFewDaysInterviewsDetails[$i][name];
								$htmlReturnData .= "</td>";
	
								$htmlReturnData .= "<td>";
								$htmlReturnData .= $getCurrentAndNextFewDaysInterviewsDetails[$i][interview_date];
								$htmlReturnData .= "</td>";
	
								$htmlReturnData .= "<td>";
								$htmlReturnData .= $getCurrentAndNextFewDaysInterviewsDetails[$i][interview_time];
								$htmlReturnData .= "</td>";
	
								$htmlReturnData .= "</tr>";
			}
								echo $htmlReturnData;
		}
		return sfView::NONE;
	}  // end of executeCurrentInterviews function
	
	public function executeUpcomingTraining(sfWebRequest $request)
	{
		$getTrainings = $this->getTrainingService()->getUpcomingTrainings();
		$counts = count($getTrainings);
		$htmlReturnData="";
		$url = sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/training/showTrainings';
		if($counts == 0)
		{
			$htmlReturnData = "false";
			echo $htmlReturnData;
		}
		else
		{
			for($i=0;$i<$counts;$i++)
			{
		
			if($i%2==0){
			$htmlReturnData .= "<tr class='odd'>";
			}else{
			$htmlReturnData .= "<tr class='even'>";
			}
					$htmlReturnData .= "<td style='	text-align: left;'>";
					$htmlReturnData .= $getTrainings[$i][topic];
					$htmlReturnData .= "</td>";
					
					$htmlReturnData .= "<td>";
					$htmlReturnData .= "<a href='".$url."'> Details </a>";
					$htmlReturnData .= "</td>";
					
					$htmlReturnData .= "</tr>";
			}
			echo $htmlReturnData;
		}
		return sfView::NONE;
	} // end of executeUpcomingTraining Function
	
	
	public function executeAgencyCandidatesReffered(sfWebRequest $request){
		
				
			/* Fetching Vacancies and candidate reffered details */
			$empVacancyStatusList = $this->getCandidateService ()->searchAgencyCandidatesReferred ();
			$count = count ( $empVacancyStatusList );
			
			$htmlReturnData="";
			if($count == 0)
			{
				$htmlReturnData = "false";
				echo $htmlReturnData;
			}
			else
			{
				for($i = 0; $i < $count; $i ++) {
					if ($i % 2 == 0) {
						$htmlReturnData .= "<tr class='even'>";
					} else {
						$htmlReturnData .= "<tr class='odd'>";
					}
					$htmlReturnData .= "<td style='text-align: left;'>";
					$htmlReturnData .= $empVacancyStatusList [$i] [name];
					$htmlReturnData .= "</td>";
				
					$htmlReturnData .= "<td >";
					$htmlReturnData .= $empVacancyStatusList [$i] [countScreening];
					$htmlReturnData .= "</td>";
				
					$htmlReturnData .= "<td>";
					$htmlReturnData .= $empVacancyStatusList [$i] [countAI];
					$htmlReturnData .= "</td>";
				
					$htmlReturnData .= "<td>";
					$htmlReturnData .= $empVacancyStatusList [$i] [countIS];
					$htmlReturnData .= "</td>";
				
					$htmlReturnData .= "<td>";
					$htmlReturnData .= $empVacancyStatusList [$i] [countShortlisted];
					$htmlReturnData .= "</td>";
				
					$htmlReturnData .= "<td>";
					$htmlReturnData .= $empVacancyStatusList [$i] [countHold];
					$htmlReturnData .= "</td>";
				
					$htmlReturnData .= "<td>";
					$htmlReturnData .= $empVacancyStatusList [$i] [countIP];
					$htmlReturnData .= "</td>";
				
					$htmlReturnData .= "<td>";
					$htmlReturnData .= $empVacancyStatusList [$i] [countIF];
					$htmlReturnData .= "</td>";
				
					$htmlReturnData .= "<td>";
					$htmlReturnData .= $empVacancyStatusList [$i] [countJobOffered];
					$htmlReturnData .= "</td>";
				
					$htmlReturnData .= "<td>";
					$htmlReturnData .= $empVacancyStatusList [$i] [countRejected];
					$htmlReturnData .= "</td>";
				
					$htmlReturnData .= "</tr>";
				}
				echo $htmlReturnData;
			}
			return sfView::NONE;
	} //End of the AgencyCandidatesPreffered function 
	
}
?>