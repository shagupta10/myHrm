<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */
class CandidateVacancyStatusForm extends BaseForm {

	private $candidateService;
	public $candidateVacancyId;
	public $selectedAction;
	public $actionName;
	public $candidateName;
	public $vacancyName;
	public $hiringManagerName;
	public $candidateId;
	public $id;
	public $performedActionName;
	public $currentStatus;
	public $performedDate;
	public $performedBy;
	public $vacancyId;
	private $selectedCandidateVacancy;
	private $interviewService;

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

	public function getInterviewService() {
		if (is_null($this->interviewService)) {
			$this->interviewService = new JobInterviewService();
			$this->interviewService->setJobInterviewDao(new JobInterviewDao());
		}
		return $this->interviewService;
	}

	/**
	 *
	 */
	public function configure() {

		$this->candidateVacancyId = $this->getOption('candidateVacancyId');
		$this->selectedAction = $this->getOption('selectedAction');
		$this->id = $this->getOption('id');
		if ($this->candidateVacancyId > 0 && $this->selectedAction != "") {
			$stateMachine = new WorkflowStateMachine();
			$this->actionName = $stateMachine->getRecruitmentActionName($this->selectedAction);
			$this->selectedCandidateVacancy = $this->getCandidateService()->getCandidateVacancyById($this->candidateVacancyId);
		}
		if ($this->id > 0) {
			$candidateHistory = $this->getCandidateService()->getCandidateHistoryById($this->id);
			$this->selectedCandidateVacancy = $this->getCandidateService()->getCandidateVacancyByCandidateIdAndVacancyId($candidateHistory->getCandidateId(), $candidateHistory->getVacancyId());
			$this->performedActionName = $candidateHistory->getActionName();
			$date = explode(" ", $candidateHistory->getPerformedDate());
			$this->performedDate = set_datepicker_date_format($date[0]);
			$this->performedBy = $candidateHistory->getPerformerName();
			$this->vacancyId = $candidateHistory->getVacancyId();
			$this->selectedAction = $candidateHistory->getAction();
		}
		$this->candidateId = $this->selectedCandidateVacancy->getCandidateId();
		$this->vacancyId = $this->selectedCandidateVacancy->getVacancyId();
		$this->candidateName = $this->selectedCandidateVacancy->getCandidateName();
		$this->vacancyName = $this->selectedCandidateVacancy->getVacancyName();
		$this->hiringManagerName = $this->selectedCandidateVacancy->getJobVacancy()->getJobHiringManagers();
		$this->currentStatus = ucwords(strtolower($this->selectedCandidateVacancy->getCandidateStatus()));

		$this->setWidget('optSoftSkill', new sfWidgetFormChoice(array('expanded' => true, 'choices' => array('Excellent' => __('Excellent'), 'Very Good' => __('Very Good'),'Good' => __('Good'), 'Below Average' => __('Below Avg.'),'Poor' => __('Poor')))));
		$this->setWidget('optAttitude', new sfWidgetFormChoice(array('expanded' => true, 'choices' => array('Excellent' => __('Excellent'), 'Very Good' => __('Very Good'),'Good' => __('Good'), 'Below Average' => __('Below Avg.'),'Poor' => __('Poor')))));
		$this->setWidget('optCommunication', new sfWidgetFormChoice(array('expanded' => true, 'choices' => array('Excellent' => __('Excellent'), 'Very Good' => __('Very Good'),'Good' => __('Good'), 'Below Average' => __('Below Avg.'),'Poor' => __('Poor')))));
		$this->setWidget('Comments', new sfWidgetFormTextArea());
		$this->setWidget('notes', new sfWidgetFormTextArea());
		$this->setWidget('interviewFeedbackId',new sfWidgetFormInputHidden());
		$this->setvalidator('interviewFeedbackId', new sfValidatorInteger(array('required' => false)));
		$this->setvalidator('optSoftSkill', new sfValidatorString(array('required' => false)));
		$this->setvalidator('optAttitude', new sfValidatorString(array('required' => false)));
		$this->setvalidator('optCommunication', new sfValidatorString(array('required' => false)));
		$this->setValidator('Comments', new sfValidatorString(array('required' => false, 'max_length' => 2147483647)));
		$this->setValidator('notes', new sfValidatorString(array('required' => true, 'max_length' => 2147483647)));
		$this->widgetSchema->setNameFormat('candidateVacancyStatus[%s]');

		if ($this->id > 0) {
			$this->setDefault('notes', $candidateHistory->getNote());
			$this->setDefault('interviewFeedbackId', $candidateHistory->getInterviewFeedback()->getId());
			$this->setDefault('optSoftSkill', $candidateHistory->getInterviewFeedback()->getSoftSkill());
			$this->setDefault('optAttitude', $candidateHistory->getInterviewFeedback()->getAttitude());
			$this->setDefault('optCommunication', $candidateHistory->getInterviewFeedback()->getCommunication());
			$this->setDefault('Comments', $candidateHistory->getInterviewFeedback()->getComments());
			$this->widgetSchema ['notes']->setAttribute('disabled', 'disable');
			$this->widgetSchema ['optSoftSkill']->setAttribute('disabled', 'disable');
			$this->widgetSchema ['optAttitude']->setAttribute('disabled', 'disable');
			$this->widgetSchema ['optCommunication']->setAttribute('disabled', 'disable');
			$this->widgetSchema ['Comments']->setAttribute('disabled', 'disable');
			$this->actionName = 'View Action History';
		}
	}

	/**
	 *
	 */
	public function performAction() {

		$note = $this->getValue('notes');
		$optSoftSkill = $this->getValue('optSoftSkill');
		$optAttitude = $this->getValue('optAttitude');
		$optCommunication = $this->getValue('optCommunication');
		$Comments = $this->getValue('Comments');
		
		if ($this->id > 0) {
			
			$history = $this->getCandidateService()->getCandidateHistoryById($this->id);
			$history->setNote($note);
			$this->getCandidateService()->saveCandidateHistory($history);
			$this->historyId = $history->getId();
			//interview feedback save in edit mode
			$InterviewFeedback = $history->getInterviewFeedback();
			$InterviewFeedback->setSoftSkill($optSoftSkill);
			$InterviewFeedback->setAttitude($optAttitude);
			$InterviewFeedback->setCommunication($optCommunication);
			$InterviewFeedback->setComments($Comments);	
			$InterviewFeedback->setHistoryId($history->getId());
			$InterviewFeedback->save();
			$resultArray['messageType'] = 'success';
			$resultArray['message'] = __(TopLevelMessages::SAVE_SUCCESS);
			return $resultArray;
		}
		$result = $this->getCandidateService()->updateCandidateVacancy($this->selectedCandidateVacancy, $this->selectedAction, sfContext::getInstance()->getUser()->getAttribute('user'));
		$interviews = $this->getInterviewService()->getInterviewsByCandidateVacancyId($this->candidateVacancyId);
		$interview = $interviews[count($interviews) - 1];
		$candidateHistory = new CandidateHistory();
		$candidateHistory->setCandidateId($this->candidateId);
		$candidateHistory->setVacancyId($this->vacancyId);
		$candidateHistory->setAction($this->selectedAction);
		$candidateHistory->setCandidateVacancyName($this->selectedCandidateVacancy->getVacancyName());
		if (!empty($interview)) {
			if ($this->selectedAction == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_INTERVIEW || $this->selectedAction == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SHEDULE_2ND_INTERVIEW || $this->selectedAction == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_PASSED || $this->selectedAction == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_FAILED || $this->selectedAction == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_INTERVIEW_MISSED) {
				$candidateHistory->setInterviewId($interview->getId());
			}
		}
		$empNumber = sfContext::getInstance()->getUser()->getEmployeeNumber();
		if ($empNumber == 0) {
			$empNumber = null;
		}
		$candidateHistory->setPerformedBy($empNumber);
		$date = date('Y-m-d');
		$candidateHistory->setPerformedDate($date . " " . date('H:i:s'));
		$candidateHistory->setNote($note);
		$result = $this->getCandidateService()->saveCandidateHistory($candidateHistory);
		$this->historyId = $candidateHistory->getId();
		if ($this->selectedAction == PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_PASSED || $this->selectedAction == PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_MARK_INTERVIEW_FAILED || $this->selectedAction == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_INTERVIEW_MISSED){
			$InterviewFeedback = new InterviewFeedback();
			$InterviewFeedback->setSoftSkill($optSoftSkill);
			$InterviewFeedback->setAttitude($optAttitude);
			$InterviewFeedback->setCommunication($optCommunication);
			$InterviewFeedback->setComments($Comments);
			$InterviewFeedback->setHistoryId($candidateHistory->getId());
			$InterviewFeedback->save();
		}
		if ($this->selectedAction == WorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_HIRE) {
			
			$employee = new Employee();
			$idGenService = new IDGeneratorService();
        	$idGenService->setEntity(new Employee());
        	$empNo1 = $idGenService->getNextID(false);
        	$employeeId = str_pad($empNo1, 4, '0');
        	$empNo = intval($empNo1);

			$employee->empNumber = intval($empNo);
			//$employee->employeeId = $employeeId;
			$employee->firstName = $this->selectedCandidateVacancy->getJobCandidate()->getFirstName();
			$employee->middleName = $this->selectedCandidateVacancy->getJobCandidate()->getMiddleName();
			$employee->lastName = $this->selectedCandidateVacancy->getJobCandidate()->getLastName();
			$employee->job_title_code = $this->selectedCandidateVacancy->getJobVacancy()->getJobTitleCode();
			$employee->jobTitle = $this->selectedCandidateVacancy->getJobVacancy()->getJobTitle();
			$employee->emp_personal_email = $this->selectedCandidateVacancy->getJobCandidate()->getEmail();
			$employee->emp_mobile = $this->selectedCandidateVacancy->getJobCandidate()->getContactNumber();
			
			$_SESSION['employeeToAdd'] = $employee;
			$_SESSION['passone'] = 1;
		}
        //
       if($this->selectedAction==PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_ATTACH_VACANCY)
       {   
           $otherVacancyAction=PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_HOLD_BY_SYS;
           $result = $this->getCandidateService()->updateCandidatesOtherVacancies($this->selectedCandidateVacancy, PluginJobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_HOLD_BY_SYS,$otherVacancyAction,true);
       }
       if($this->selectedAction==PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_REJECT)
       {  
           $otherVacancyAction=PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_SCREENING;  
           $result = $this->getCandidateService()->updateCandidatesOtherVacancies($this->selectedCandidateVacancy, PluginJobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_SCREENING,$otherVacancyAction,true);
       }
       if($this->selectedAction==PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_OFFER_JOB)
       {   
           $otherVacancyAction=PluginWorkflowStateMachine::RECRUITMENT_APPLICATION_ACTION_OFFER_JOB_BY_SYS;
           $result = $this->getCandidateService()->updateCandidatesOtherVacancies($this->selectedCandidateVacancy, PluginJobCandidateVacancy::RECRUITMENT_CANDIDATE_STATUS_JOB_OFFERED_BY_SYS,$otherVacancyAction,false);
       }
       
        
		//Now send mail to HR admin and Hiring manager
        $interviewMailer = new InterviewMailer($empNumber, $this->candidateId, $this->vacancyId, $this->selectedAction, $newJobInterview,$interviewersList);
	    $interviewMailer->send();
	}
     protected function getInterviewInterviewers($interviewId) {

        $interviewers = $this->getInterviewService()->getInterviewersByInterviewId($interviewId);
        $interviewersList = array();
        foreach ($interviewers as $interviewer) {
            $interviewersList[] = $interviewer->getInterviewerId();
        }
        return $interviewersList;
    }
 
}
