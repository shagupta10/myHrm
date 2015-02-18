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
class viewPayGradesAction extends sfAction {

	private $payGradeService;

	public function getPayGradeService() {
		if (is_null($this->payGradeService)) {
			$this->payGradeService = new PayGradeService();
			$this->payGradeService->setPayGradeDao(new PayGradeDao());
		}
		return $this->payGradeService;
	}

	public function execute($request) {

		$usrObj = $this->getUser()->getAttribute('user');
		if (!($usrObj->isAdmin())) {
			$this->redirect('pim/viewPersonalDetails');
		}

		$sortField = $request->getParameter('sortField');
		$sortOrder = $request->getParameter('sortOrder');
		$isPaging = $request->getParameter('pageNo');
		$recordsLimit =  $request->getParameter('recordsPerPage_Limit');
		$this->recordsLimits = $recordsLimit;
		$pageNumber = $isPaging;
		if ($this->getUser()->hasAttribute('pageNumber')) {
			$pageNumber = $this->getUser()->getAttribute('pageNumber');
		}
		if($recordsLimit){
			$noOfRecords = $recordsLimits;
		}else{
			$noOfRecords = 10;
			$this->recordsLimits = $noOfRecords;
			}
		
		$offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;
		
		$payGradeList = $this->getPayGradeService()->getPayGradeList($sortField, $sortOrder);
		$this->_setListComponent($payGradeList, $noOfRecords, $pageNumber);
		$this->getUser()->setAttribute('pageNumber', $pageNumber);
		$params = array();
		$this->parmetersForListCompoment = $params;
	}

	private function _setListComponent($payGradeList,$noOfRecords,$pageNumber) {

		$configurationFactory = new PayGradeHeaderFactory();
		ohrmListComponent::setConfigurationFactory($configurationFactory);
		ohrmListComponent::setListData($payGradeList);
		ohrmListComponent::setPageNumber($pageNumber);
		ohrmListComponent::setItemsPerPage($noOfRecords);
		ohrmListComponent::setNumberOfRecords(count($this->getPayGradeService()->getPayGradeList()));
	}

}

?>
