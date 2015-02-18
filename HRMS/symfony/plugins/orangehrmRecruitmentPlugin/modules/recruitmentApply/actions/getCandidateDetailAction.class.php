<?php

class getCandidateDetailAction extends sfAction {
	
	private $candidateService;
	
	/**
	 * Get CandidateService
	 * @returns CandidateService
	 */
	public function getCandidateService() {
		if (is_null($this->candidateService)) {
			$this->candidateService = new CandidateService();
			$this->candidateService->setCandidateDao(new CandidateDao());
		}
		return $this->candidateService;
	}
       /* Added By : Shagupta Faras
        * Added On : 22-07-2014
        * DESC: Agenet can apply for multiple vacancies for one candidate
        */
	public function execute($request) {	
		$email = $request->getParameter('email');
        $contactNumber = $request->getParameter('contactNo');
        if(isset($email))
        {    
            $candidate = $this->getCandidateService()->getCandidateByEmail($email);
        }
        else
        {
            if(isset($contactNumber))
            {    
            $candidate = $this->getCandidateService()->getCandidateByContactNumber($contactNumber);
            }
        }
		$date = date('Y-m-d', strtotime("-6 Months"));
                $duplicate=false;
		$candidateData= array();
		if($candidate)
		{
                    $candidate=$candidate[0];       
		    $candidateData=$candidate;
                    $candidateData['expectedDoj']=date("D, d M Y", strtotime($candidateData['expectedDoj']));
                    $candidateObj = $this->getCandidateService()->getCandidateById($candidate['id']);
                    $attachment = $candidateObj->getJobCandidateAttachment();                    
                    if($attachment!=''){                    
                    $linkHtml = "<li><label for=\"addCandidate_resume\">&nbsp;</label><a target=\"_blank\" class=\"fileLink\" href=\"";
                    $linkHtml .= url_for('recruitment/viewCandidateAttachment?attachId=' . $attachment->getId());
                    $linkHtml .= "\">{$attachment->getFileName()}</a></li>";
                    $candidateData['attachment']=$linkHtml;                    
                    } else {
                    $candidateData['attachment']='';
                    }                    
                    $loginUser = $_SESSION['empNumber'];
                    if(strtotime($date) < strtotime($candidateObj->getDateOfApplication()) && ($candidateObj->getAddedPerson()!=$loginUser)) {
                     $candidateData['duplicate']=true;                    
                    }
                    else
                    {
                        if(strtotime($date) < strtotime($candidateObj->getDateOfApplication()))
                        $candidateData['duplicate']=false; 
                    }
                }               
		$response = $this->getResponse();
		$response->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
		$response->setHttpHeader('Expires', '0');
		$response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
		$response->setHttpHeader("Cache-Control", "private", false);
		return $this->renderText(json_encode($candidateData)); 
	}
}
