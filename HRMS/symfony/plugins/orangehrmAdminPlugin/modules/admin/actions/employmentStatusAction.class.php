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
class employmentStatusAction extends sfAction {

	private $empStatusService;

	public function getEmploymentStatusService() {
		if (is_null($this->empStatusService)) {
			$this->empStatusService = new EmploymentStatusService();
			$this->empStatusService->setEmploymentStatusDao(new EmploymentStatusDao());
		}
		return $this->empStatusService;
	}

	/**
	 * @param sfForm $form
	 * @return
	 */
	public function setForm(sfForm $form) {
		if (is_null($this->form)) {
			$this->form = $form;
		}
	}

	public function execute($request) {

		$usrObj = $this->getUser()->getAttribute('user');
		$isPaging = $request->getParameter('pageNo');
		if (!$usrObj->isAdmin()) {
			$this->redirect('pim/viewPersonalDetails');
		}
		
		$this->setForm(new EmploymentStatusForm());
		$pageNumber = $isPaging;
		if ($this->getUser()->hasAttribute('pageNumber')) {
			$pageNumber = $this->getUser()->getAttribute('pageNumber');
		}
		$recordsLimits = $request->getParameter('recordsPerPage_Limit');
		$this->recordsLimit = $recordsLimits;
		if($recordsLimits){
			$noOfRecords = $recordsLimits;
		}else{
			$noOfRecords = 10;
			$this->recordsLimit = $noOfRecords;
		}
		 
		$offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;
		
		if ($this->getUser()->hasFlash('templateMessage')) {
			list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
		}

		$statusList = $this->getEmploymentStatusService()->getEmploymentStatusList();
		$this->_setListComponent($statusList, $noOfRecords, $pageNumber);
		$this->getUser()->setAttribute('pageNumber', $pageNumber);
		$params = array();
		$this->parmetersForListCompoment = $params;

		if ($request->isMethod('post')) {
			$this->form->bind($request->getParameter($this->form->getName()));
			if ($this->form->isValid()) {
				$this->form->save();
				$this->getUser()->setFlash('success', __(TopLevelMessages::SAVE_SUCCESS));
				$this->redirect('admin/employmentStatus');
			}
		}
	}
	
	private function _setListComponent($statusList, $noOfRecords, $pageNumber) {

		$configurationFactory = new EmploymentStatusHeaderFactory();
		ohrmListComponent::setConfigurationFactory($configurationFactory);
		ohrmListComponent::setListData($statusList);
		ohrmListComponent::setPageNumber($pageNumber);
		ohrmListComponent::setItemsPerPage($noOfRecords);
		ohrmListComponent::setNumberOfRecords(count($this->getEmploymentStatusService()->getEmploymentStatusList()));
	}
}
