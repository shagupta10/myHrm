<?php

class getInterviewTimeAction extends sfAction {

	public $candidateService;
    public function getCandidateService() {
    	if (is_null($this->candidateService)) {
    		$this->candidateService = new CandidateService();
    		$this->candidateService->setCandidateDao(new CandidateDao());
    	}
    	return $this->candidateService;
    }

	public function execute($request) {

		$candidateId = $request->getParameter('candidateId');
		$interviewDetails = $this->getCandidateService()->getLatestScheduledInterview($candidateId);

		$ratingl = array();
		if(!is_null($interviewDetails))
		{
			$rating['name'] = $interviewDetails['name'];
			$rating['date'] = set_datepicker_date_format($interviewDetails['date']);
			$rating['time'] = $interviewDetails['time'];
			array_push($ratingl,$rating);
		}
		//array_push($rating,$list['description']);

		//$returnData[] = $rating;
		$response = $this->getResponse();
		$response->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
		$response->setHttpHeader('Expires', '0');
		$response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
		$response->setHttpHeader("Cache-Control", "private", false);
		return $this->renderText(json_encode($ratingl));
	}
}
