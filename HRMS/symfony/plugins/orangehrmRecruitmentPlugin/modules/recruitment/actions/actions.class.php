<?php

class recruitmentActions extends sfActions {
	
	
	public function getInterviewService() {
		if (is_null($this->interviewService)) {
			$this->interviewService = new JobInterviewService();
			$this->interviewService->setJobInterviewDao(new JobInterviewDao());
		}
		return $this->interviewService;
	}
	
	
	public function executeInterviewerAvailable(sfWebRequest $request) {
		 $selectedInterviewerList = $request->getParameter('interviewersArray');
		 $selectedInterviewerList  = implode(",", $selectedInterviewerList );
		 $selectedDate = $request->getParameter('date');
		 $selectedTime = $request->getParameter('time').":00";
    	 $selectedDate = date("Y-m-d", strtotime($selectedDate));
    	 $unAvailableInterviewers = $this->getInterviewService()->checkIfInterviewerAvailable($selectedInterviewerList,$selectedDate,$selectedTime);
    	 $returnData=array();
    	 if(sizeof($unAvailableInterviewers)>0){
    	 	$returnData['success'] = 0;
    	 	$returnData['unavailable_interviewers'] = $unAvailableInterviewers;
    	 }else{
    	 	$returnData['success'] = 1;
    	 }
    	 echo json_encode($returnData);
		 return sfView::NONE;
	}
}
?>