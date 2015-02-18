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
class viewJobRequestsAction extends sfAction {

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

        $isAdmin = $usrObj->isAdmin();
        if (!($usrObj->isAdmin() || $usrObj->isHiringManager() || $usrObj->isInterviewer() || $usrObj->isRecruitmentManager())) {
            $this->redirect('pim/viewPersonalDetails');
        }
        
        //$param = array('allowedCandidateList' => $allowedCandidateList, 'allowedVacancyList' => $allowedVacancyList, 'allowedCandidateListToDelete' => $allowedCandidateListToDelete);
        //list($this->messageType, $this->message) = $this->getUser()->getFlash('candidateListMessageItems');
        $flag = $request->getParameter('onChange') == '' ? 0 : 1;
        if(flag != 1) { $records_Per_Page_limit = $request->getParameter('recordsPerPage_Limit'); }
        $this->getRecordsLimit = $records_Per_Page_limit;
        $candidateId = $request->getParameter('candidateId');
        $sortField = $request->getParameter('sortField');
        $sortOrder = $request->getParameter('sortOrder');
        $isPaging = $request->getParameter('pageNo');
        $this->pagenumber = $isPaging;
        $pageNumber = $isPaging;
        if (!is_null($this->getUser()->getAttribute('pageNumber')) && !($pageNumber >= 1)) {
            $pageNumber = $this->getUser()->getAttribute('pageNumber');
        }
        $this->getUser()->setAttribute('pageNumber', $pageNumber);

        $searchParam = new CandidateSearchParameters();
        if($records_Per_Page_limit)
        {
        	$searchParam->setLimit($records_Per_Page_limit);
        	$noOfRecords = $searchParam->getLimit();
        }else{
        	$noOfRecords = $searchParam->getLimit();
        	$this->getRecordsLimit = $noOfRecords;
        }
        
        $searchParam->setIsAdmin($isAdmin);
        $searchParam->setEmpNumber($usrObj->getEmployeeNumber());
        $searchParam->setReferralId($request->getParameter('referralId'));
        $searchParam->setReferralName($request->getParameter('referralName'));
        $noOfRecords = $searchParam->getLimit();
        $offset = ($pageNumber >= 1) ? (($pageNumber - 1) * $noOfRecords) : ($request->getParameter('pageNo', 1) - 1) * $noOfRecords;
        $searchParam->setAdditionalParams($request->getParameter('additionalParams', array()));
        
        $this->setForm(new viewJobRequestsForm(array(), null, true));
        
        //Not sure
      //  $this->vacancyDesc = $this->form->getVacancyDescription();
        
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
        //Search Requests
       // if ($request->isMethod('get')) { 
	        $candidateRequests = $this->getCandidateService()->searchJobCandidateRequests($searchParam);
	        $this->_setListComponent($usrObj, $candidateRequests, $noOfRecords, $searchParam, $pageNumber);
       // }

        $params = array();
        $this->parmetersForListCompoment = $params;
        if (empty($isPaging)) {
            if ($request->isMethod('post') && $flag==0) {
                $pageNumber = 1;
                $searchParam->setOffset(0);
                $this->getUser()->setAttribute('pageNumber', $pageNumber);

                $this->form->bind($request->getParameter($this->form->getName()));
                if ($this->form->isValid()) {
                	// TODO: chek this method
                    $srchParams = $this->form->getSearchParamsBindwithFormData($searchParam);
                    $this->getUser()->setAttribute('searchParameters', $srchParams);
                    
                    $candidateRequests = $this->getCandidateService()->searchJobCandidateRequests($srchParams);
                    $this->_setListComponent($usrObj, $candidateRequests, $noOfRecords, $searchParam, $pageNumber);
                }
            }
        }
    }

    /**
     *
     * @param <type> $candidates
     * @param <type> $noOfRecords
     * @param CandidateSearchParameters $searchParam
     */
    private function _setListComponent($usrObj, $candidateRequests, $noOfRecords, CandidateSearchParameters $searchParam, $pageNumber) {
        $configurationFactory = new JobCandidateRequestsHeaderFactory();
        ohrmListComponent::setPageNumber($pageNumber);
        ohrmListComponent::setConfigurationFactory($configurationFactory);
        ohrmListComponent::setListData($candidateRequests);
        ohrmListComponent::setItemsPerPage($noOfRecords);
        ohrmListComponent::setNumberOfRecords($this->getCandidateService()->getCandidateRequestRecordsCount($searchParam));
    }

}

