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
class viewMyCandidatesAction extends sfAction {

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

    /**
     * Set CandidateService
     * @param CandidateService $candidateService
     */
    public function setCandidateService(CandidateService $candidateService) {
        $this->candidateService = $candidateService;
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

    /**
     *
     * @param <type> $request
     */
    public function execute($request) {
	
        $usrObj = $this->getUser()->getAttribute('user');
        
        list($this->messageType, $this->message) = $this->getUser()->getFlash('candidateListMessageItems');
       
        $candidateId = $request->getParameter('candidateId');
        $sortField = $request->getParameter('sortField');
        $sortOrder = $request->getParameter('sortOrder');
        $isPaging = $request->getParameter('pageNo');
        $this->pagenumber = $isPaging;
        $flag = $request->getParameter('onChange') == '' ? 0 : 1;
        $RecordsLimit = ($flag==1) ? $request->getParameter('recordsPerPage_Limit') : $request->getPostParameter('candidateSearch[recordsPer_Page_Limit]');
        $this->recordsLimits = $RecordsLimit;
        $pageNumber = $isPaging;
        if (!is_null($this->getUser()->getAttribute('pageNumber')) && !($pageNumber >= 1)) {
            $pageNumber = $this->getUser()->getAttribute('pageNumber');
        }
        $this->getUser()->setAttribute('pageNumber', $pageNumber);
        $searchParam = new CandidateSearchParameters();
        if($RecordsLimit){
        	$searchParam->setLimit($RecordsLimit);
        	$noOfRecords = $searchParam->getLimit();
        }else{
        	$noOfRecords = $searchParam->getLimit();
        	$this->recordslimits = $noOfRecords;
        }
	    $searchParam->setEmpNumber($usrObj->getEmployeeNumber());
        $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;
        
        $searchParam->setAdditionalParams($request->getParameter('additionalParams', array()));
        
        $this->setForm(new viewMyCandidatesForm(array(), array(), true));
        $this->vacancyListBox = $this->form->getVacancyListForDialogBox();
        $this->vacancyDesc = $this->form->getVacancyDescription();
        if (!empty($sortField) && !empty($sortOrder) || $isPaging > 0 || $candidateId > 0) {
            if ($this->getUser()->hasAttribute('searchParameters')) {
                $searchParam = $this->getUser()->getAttribute('searchParameters');
                $this->form->setDefaultDataToWidgets($searchParam);
            }
            $searchParam->setSortField($sortField);
            $searchParam->setSortOrder($sortOrder);
        } else {
            $this->getUser()->setAttribute('searchParameters', $searchParam);
            $offset = 0;
            $pageNumber = 1;
        }
        
        $searchParam->setOffset($offset);
        
        $candidates = $this->getCandidateService()->searchCandidatesReferred($searchParam);
        $this->_setListComponent($usrObj, $candidates, $noOfRecords, $searchParam, $pageNumber);
        
        $params = array();
        $this->parmetersForListCompoment = $params;
        if (empty($isPaging)) {
            if ($request->isMethod('post') && $flag==0) {
                $pageNumber = 1;
                $searchParam->setOffset(0);
                $this->getUser()->setAttribute('pageNumber', $pageNumber);
                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                    $srchParams = $this->form->getSearchParamsBindwithFormData($searchParam);
                    $this->getUser()->setAttribute('searchParameters', $srchParams);
                    $candidates = $this->getCandidateService()->searchCandidatesReferred($srchParams);
                    $this->_setListComponent($usrObj, $candidates, $noOfRecords, $searchParam, $pageNumber); 
                }
            }
        }

        $statusMapping = array();
        foreach ($candidates as $candidate) {
        	$mapping['id'] = $candidate->getCandidateId();
        	$mapping['vacancyId'] = $candidate->getVacancyId();
        	$mapping['status'] = $candidate->getStatusName();
        	array_push($statusMapping, $mapping);
        }
        //mapping array of candidate id, vacancyid, status generated and sent to  jsonEncodeStatusMapping method to encodeArray in JSON
        $this->form->jsonEncodeStatusMapping($statusMapping);
    }

    /**
     *
     * @param <type> $candidates
     * @param <type> $noOfRecords
     * @param CandidateSearchParameters $searchParam
     */
    private function _setListComponent($usrObj, $candidates, $noOfRecords, CandidateSearchParameters $searchParam, $pageNumber) {

        $configurationFactory = new MyCandidateHeaderFactory();
        ohrmListComponent::setPageNumber($pageNumber);
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($candidates);
        ohrmListComponent::setItemsPerPage($noOfRecords);
        ohrmListComponent::setNumberOfRecords($this->getCandidateService()->getReferralRecordCount($searchParam));
    }
}

