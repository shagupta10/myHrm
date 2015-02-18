<?php
/*
 * Created on 27-Nov-2013
 *
 * To change the template for this generated file go to
 * Window - Preferences - PHPeclipse - PHP - Code Templates
 */

class RecruitmentUserRoleDecorator extends UserRoleDecorator {
	const RECRUITMENT_MANAGER = "RECRUITMENT MANAGER";
	const ADD_CANDIDATE = "./symfony/web/index.php/recruitment/addCandidate";
	const VIEW_CANDIDATES = "./symfony/web/index.php/recruitment/viewCandidates";
	private $user;

	public function __construct(User $user) {

		$this->user = $user;
		parent::setEmployeeNumber($user->getEmployeeNumber());
		parent::setUserId($user->getUserId());
		parent::setUserTimeZoneOffset($user->getUserTimeZoneOffset());
	}

	public function getAccessibleTimeMenus() {
		return $this->user->getAccessibleTimeMenus();
	}

	public function getAccessibleTimeSubMenus() {
		return $this->user->getAccessibleTimeSubMenus();
	}

	public function getAccessibleAttendanceSubMenus() {
		return $this->user->getAccessibleAttendanceSubMenus();
	}

	public function getAccessibleReportSubMenus() {
		return $this->user->getAccessibleReportSubMenus();
	}

	public function getEmployeeList() {
		return $this->user->getEmployeeList();
	}

	public function getEmployeeListForAttendanceTotalSummaryReport() {
		return $this->user->getEmployeeListForAttendanceTotalSummaryReport();
	}

	/**
	 * Get actions that this user can perform on a perticular workflow with the current state
	 * @param int $workFlow
	 * @param string $state
	 * @return string[]
	 */
	public function getAllowedActions($workFlow, $state) {

		$accessFlowStateMachineService = new AccessFlowStateMachineService();
		$allowedActionsForAdminUser = $accessFlowStateMachineService->getAllowedActions($workFlow, $state, AdminUserRoleDecorator::ADMIN_USER);
		$existingAllowedActions = $this->user->getAllowedActions($workFlow, $state);
		if (is_null($allowedActionsForAdminUser)) {
			return $existingAllowedActions;
		} else {
			$allowedActionsList = array_unique(array_merge($allowedActionsForAdminUser, $existingAllowedActions));
			return $allowedActionsList;
		}
	}
	

	/**
	 * Get next state given workflow, state and action for this user
	 * @param int $workFlow
	 * @param string $state
	 * @param int $action
	 * @return string
	 */
	public function getNextState($workFlow, $state, $action) {

		$accessFlowStateMachineService = new AccessFlowStateMachineService();
		$tempNextState = $accessFlowStateMachineService->getNextState($workFlow, $state, AdminUserRoleDecorator::ADMIN_USER, $action);

		$temp = $this->user->getNextState($workFlow, $state, $action);

		if (is_null($tempNextState)) {
		    return $temp;
		}

		return $tempNextState;
	}

	/**
	 * Get previous states given workflow, action for this user
	 * @param int $workFlow
	 * @param int $action
	 * @return string
	 */
	public function getAllAlowedRecruitmentApplicationStates($flow) {

	    $accessFlowStateMachineService = new AccessFlowStateMachineService();
	    $applicationStates = $accessFlowStateMachineService->getAllAlowedRecruitmentApplicationStates($flow, AdminUserRoleDecorator::ADMIN_USER);
	    $existingStates = $this->user->getAllAlowedRecruitmentApplicationStates($flow);
	    if (is_null($applicationStates)) {
	        return $existingStates;
	    } else {
	        $applicationStates = array_unique(array_merge($applicationStates, $existingStates));
	        return $applicationStates;
	    }
	}

	public function getActionableTimesheets() {
		return $this->user->getActionableTimesheets();
	}
	
    public function getEmployeeNameList() {
        return $this->user->getEmployeeNameList();
    }

	public function getActionableAttendanceStates($actions) {

		$accessFlowStateMachinService = new AccessFlowStateMachineService();
		$actionableAttendanceStatesForEssUser = $accessFlowStateMachinService->getActionableStates(PluginWorkflowStateMachine::FLOW_ATTENDANCE, EssUserRoleDecorator::ESS_USER, $actions);


		$actionableAttendanceStates = $this->user->getActionableAttendanceStates($actions);

		if (is_null($actionableAttendanceStatesForEssUser)) {
			return $actionableAttendanceStates;
		}

		$actionableAttendanceStatesList = array_unique(array_merge($actionableAttendanceStatesForEssUser, $actionableAttendanceStates));
		return $actionableAttendanceStatesList;
	}

	public function isAllowedToDefineTimeheetPeriod() {
		return $this->user->isAllowedToDefineTimeheetPeriod();
	}

	public function getActiveProjectList() {

		$activeProjectList = $this->user->getActiveProjectList();
		return $activeProjectList;
	}

	public function getActionableStates() {

		return $this->user->getActionableStates();
	}

	public function getAccessibleConfigurationSubMenus() {

		return $this->user->getAccessibleConfigurationSubMenus();
	}

	public function getAllowedCandidateList() {

		$accessFlowStateMachineService = new AccessFlowStateMachineService();
		$allowedCandidateIdList = $accessFlowStateMachineService->getAllowedCandidateList(AdminUserRoleDecorator::ADMIN_USER, null);
		$existingIdList = $this->user->getAllowedCandidateList();
		if (is_null($allowedCandidateIdList)) {
			return $existingIdList;
		} else {
			$allowedCandidateIdList = array_unique(array_merge($allowedCandidateIdList, $existingIdList));
			return $allowedCandidateIdList;
		}
	}
	
	public function getAllowedCandidateListToDelete() {

		$accessFlowStateMachineService = new AccessFlowStateMachineService();
		$allowedCandidateIdListToDelete = $accessFlowStateMachineService->getAllowedCandidateList(AdminUserRoleDecorator::ADMIN_USER, null);
		$existingIdList = $this->user->getAllowedCandidateListToDelete();
		if (is_null($allowedCandidateIdListToDelete)) {
			return $existingIdList;
		} else {
			$allowedCandidateIdListToDelete = array_unique(array_merge($allowedCandidateIdListToDelete, $existingIdList));
			return $allowedCandidateIdListToDelete;
		}
	}

	public function getAllowedVacancyList() {

		$accessFlowStateMachineService = new AccessFlowStateMachineService();
		$allowedVacancyIdList = $accessFlowStateMachineService->getAllowedVacancyList(AdminUserRoleDecorator::ADMIN_USER, null);
		$existingIdList = $this->user->getAllowedVacancyList();
		if (is_null($allowedVacancyIdList)) {
			return $existingIdList;
		} else {
			$allowedVacancyIdList = array_unique(array_merge($allowedVacancyIdList, $existingIdList));
			return $allowedVacancyIdList;
		}
	}

	public function getAllowedCandidateHistoryList($candidateId) {

		$accessFlowStateMachineService = new AccessFlowStateMachineService();
		$allowedCandidateHistoryIdList = $accessFlowStateMachineService->getAllowedCandidateHistoryList(AdminUserRoleDecorator::ADMIN_USER, null, $candidateId);
		$existingIdList = $this->user->getAllowedCandidateHistoryList($candidateId);
		if (is_null($allowedCandidateHistoryIdList)) {
			return $existingIdList;
		} else {
			$allowedCandidateHistoryIdList = array_unique(array_merge($allowedCandidateHistoryIdList, $existingIdList));
			return $allowedCandidateHistoryIdList;
		}
	}



	public function getAllowedProjectList() {
		return $this->user->getAllowedProjectList();
	}

	public function isAdmin() {
		return $this->user->isAdmin();
	}

	public function isHiringManager() {
		return $this->user->isHiringManager();
	}

	public function isProjectAdmin() {
		return $this->user->isProjectAdmin();
	}

	public function isInterviewer() {
		return $this->user->isInterviewer();
	}
	
	public function isConsultant() {
		return $this->user->isConsultant();
	}
	
	public function isRecruitmentManager(){
		return true;
	}

}
