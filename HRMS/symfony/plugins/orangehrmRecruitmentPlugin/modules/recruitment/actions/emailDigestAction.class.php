<?php
/**
 *
 * @param  NULL
 * @return NULL
 * @author Mayur V. Kathale<mayur.kathale@gmail.com>
 */
class emailDigestAction extends digestAction {
	
	private $systemUserService;
	private $candidateService;
	private $vacancyService;
	private $authentication;
	
	/**
	 * @param sfForm $form
	 * @return
	 */
	public function setForm(sfForm $form) {
		if (is_null($this->form)) {
			$this->form = $form;
		}
	}

	public function getForm() {
		return $this->form;
	}

	/**
	 *
	 * @return <type>
	 */
	public function getCandidateService() {
		if (is_null($this->candidateService)) {
			$this->candidateService = new CandidateService();
			$this->candidateService->setCandidateDao(new CandidateDao());
		}
		return $this->candidateService;
	}
	
	public function getSystemUserService() {
		if (empty($this->systemUserService)) {
			$this->systemUserService = new SystemUserService();
		}
		return $this->systemUserService;
	}

	/**
	 *
	 * @return <type>
	 */
	public function getVacancyService() {
		if (is_null($this->vacancyService)) {
			$this->vacancyService = new VacancyService();
			$this->vacancyService->setVacancyDao(new VacancyDao());
		}
		return $this->vacancyService;
	}
	
	/**
	 *
	 * @param <type> $request
	 */
	
	public function performAction (){
		$vacancies = $this->getVacancyService()->getAllVacancies(JobVacancy::ACTIVE);
		$detailsArray = array();
		foreach ($vacancies as $vacancy) {	
			$hiringManagers = $vacancy->getJobVacancyHiringManager();
			$hmArray = array();
			foreach ($hiringManagers as $hm) {
				array_push($hmArray, $hm->getEmployee()->getFirstAndLastNames());
			}
			$hmText = 'Hiring Managers :<strong>&nbsp;'.implode(' ,', $hmArray)."</strong>";
			$candidateVacancies = $vacancy->getJobCandidateVacancy();
			$appInitiatedArray = array();
			$shortlistedArray = array();
			$totalArray = array();
			$offeredArray = array();
			$intScheduledArray = array();
			foreach ($candidateVacancies as $candidateVacancy) {
				if($candidateVacancy->getStatus() == JobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_APPLICATION_INITIATED) {
					array_push($appInitiatedArray, $candidateVacancy);
				}
				if($candidateVacancy->getStatus() == JobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_SHORTLISTED) {
					array_push($shortlistedArray, $candidateVacancy);
				}
				if($candidateVacancy->getStatus() == JobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_JOB_OFFERED) {
					array_push($offeredArray, $candidateVacancy);
				}
				if($candidateVacancy->getStatus() == JobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_INTERVIEW_SCHEDULED) {
					array_push($intScheduledArray, $candidateVacancy);
				}
				array_push($totalArray, $candidateVacancy);
			}
			$detail['shortlisted'] = count($shortlistedArray);
			$detail['appInitiated'] = count($appInitiatedArray);
			$detail['total'] = count($totalArray);
			$detail['offered'] = count($offeredArray);
			$detail['intScheduled'] = count($intScheduledArray);
			$detail['vacancy'] = $vacancy;
			$detail['appInitiatedRecord'] = $appInitiatedArray;
			$detail['hiringManagers'] = $hiringManagers;
			$detail['hmText'] = $hmText;
			if(count($appInitiatedArray) > 0) {
				array_push($detailsArray, $detail);
			}
		}
		$this->sendDigest($detailsArray);
		exit;
	}
	
	public function sendDigest($detailsArray) {
		$adminUsers = $this->getSystemUserService()->getAdminSystemUsers();
		foreach ($detailsArray as $detail) {
			$hmArray = array();
			$hmEmailArray = array();
			$adminEmailArray = array();
			$vacancyName = $detail['vacancy']->getName();
			$microResumes = $this->getMicroResumes($detail);
			
			foreach ($detail['hiringManagers'] as $hiringManager) {
				$emp = $hiringManager->getEmployee();
				array_push($hmArray, $emp->getEmpNumber());
				if(trim($emp->getEmpWorkEmail()) != "")
					array_push($hmEmailArray, $emp->getEmpWorkEmail());
			}
			
		    foreach($adminUsers as $admin) {
				$emp = $admin->getEmployee();
				if(!in_array($emp->getEmpNumber(), $hmArray)) { 
					if(trim($emp->getEmpWorkEmail()) != "")
						array_push($adminEmailArray, $emp->getEmpWorkEmail());
				} 
			}
			$mailer = new EmailDigestMailer($vacancyName, $detail, $microResumes, $hmEmailArray, $adminEmailArray);
			$this->getMailer()->send($mailer->getMessage());
		}
	}
	
	public function getMicroResumes($detail) {
		$bodyString = "";
		if($detail['appInitiated'] > 0) {
			$url = (empty($_SERVER['HTTPS']) OR $_SERVER['HTTPS'] === 'off') ? 'http://' : 'https://';
			$url .= $_SERVER['HTTP_HOST'];
		    foreach ($detail['appInitiatedRecord'] as $candidateVacancy) {
				$candidate = $this->getCandidateService()->getCandidateById($candidateVacancy->getCandidateId());
				if($candidate->isDeleted == JobCandidate::IS_NOT_DELETED) {
					$candidateVacanyId = $candidateVacancy->getId();
					$bodyString .= '<tr bgcolor="white"><td style="padding:4px;"><strong>'.$candidate->getFirstName().' '.$candidate->getLastName().'</strong>, '.$candidate->getEmail().', '.$candidate->getContactNumber().'<br>';
					$bodyString .= nl2br($candidate->getMicroResume())."<br>";
					$viewLink = $url."/symfony/web/index.php/recruitment/addCandidate?id=".$candidate->getId();
					$shortlistLink = $url."/symfony/web/index.php/recruitment/changeCandidateVacancyStatus?candidateVacancyId=".$candidateVacanyId."&selectedAction=2";
					$rejectLink =  $url."/symfony/web/index.php/recruitment/changeCandidateVacancyStatus?candidateVacancyId=".$candidateVacanyId."&selectedAction=3";
					$downloadResumeLink = $url."/symfony/web/index.php/recruitment/viewCandidateAttachment?attachId=".$candidate->getJobCandidateAttachment()->getId();
					$bodyString .= '</br><a href="'.$viewLink.'">View </a> | <a href="'.$shortlistLink.'">Shortlist</a> | <a href="'.$rejectLink.'">Reject</a> |<a href="'.$downloadResumeLink.'"> Download </a></td></tr>';
				}
			}
		} else {
			$bodyString = '<tr bgcolor="white"><td style="padding-top:4px;padding-bottom:4px;"><center>no results.</center></td></tr>';
		}
		return $bodyString;
	}
}
