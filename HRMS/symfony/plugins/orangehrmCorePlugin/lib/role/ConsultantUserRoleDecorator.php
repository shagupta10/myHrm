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
 */
class ConsultantUserRoleDecorator extends UserRoleDecorator {
	
	private $user;
	const CONSULTANT_USER = "CONSULTANT";
	public function __construct(User $user) {
		$this->user = $user;
		parent::setEmployeeNumber($user->getEmployeeNumber());
		parent::setUserId($user->getUserId());
		parent::setUserTimeZoneOffset($user->getUserTimeZoneOffset());
	}


	public function isAdmin() {
		return false;
	}

	public function isHiringManager() {
		return false;
	}

	public function isProjectAdmin() {
		return false;
	}

	public function isInterviewer() {
		return false;
	}
	
	public function isConsultant() {
		return true;
	}
	
	public function isRecruitmentManager(){
		return false;
	}
    
	public function getAllowedCandidateListToDelete() {
		$accessFlowStateMachineService = new AccessFlowStateMachineService();
		$allowedCandidateIdListToDelete = $accessFlowStateMachineService->getAllowedCandidateList(ConsultantUserRoleDecorator::CONSULTANT_USER, $this->getEmployeeNumber());
		$existingIdList = $this->user->getAllowedCandidateListToDelete();
		if (is_null($allowedCandidateIdListToDelete)) {
			return $existingIdList;
		} else {
			$allowedCandidateIdListToDelete = array_unique(array_merge($allowedCandidateIdListToDelete, $existingIdList));
			return $allowedCandidateIdListToDelete;
		}
	
	}
	
	public function getAllowedCandidateList() {
	
		$accessFlowStateMachineService = new AccessFlowStateMachineService();
		$allowedCandidateIdList = $accessFlowStateMachineService->getAllowedCandidateList(ConsultantUserRoleDecorator::CONSULTANT_USER, $this->getEmployeeNumber());
		$existingIdList = $this->user->getAllowedCandidateList();
		if (is_null($allowedCandidateIdList)) {
			return $existingIdList;
		} else {
			$allowedCandidateIdList = array_unique(array_merge($allowedCandidateIdList, $existingIdList));
			return $allowedCandidateIdList;
		}
	}
	
	public function getAllowedCandidateHistoryList($candidateId) {
	
		$accessFlowStateMachineService = new AccessFlowStateMachineService();
		$allowedCandidateHistoryIdList = $accessFlowStateMachineService->getAllowedCandidateHistoryList(AdminUserRoleDecorator::ADMIN_USER, $this->getEmployeeNumber(), $candidateId);
		$existingIdList = $this->user->getAllowedCandidateHistoryList($candidateId);
		if (is_null($allowedCandidateHistoryIdList)) {
			return $existingIdList;
		} else {
			$allowedCandidateHistoryIdList = array_unique(array_merge($allowedCandidateHistoryIdList, $existingIdList));
			return $allowedCandidateHistoryIdList;
		}
	}
}
