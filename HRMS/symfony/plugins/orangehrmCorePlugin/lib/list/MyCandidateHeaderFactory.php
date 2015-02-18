<?php

class MyCandidateHeaderFactory extends ohrmListConfigurationFactory {

	protected function init() {

		$headerList = array();

		for ($i = 1; $i < 6; $i++) {
			$headerList[$i] = new ListHeader();
		}

		$headerList[1]->populateFromArray(array(
		    'name' => 'Vacancy',
		    'isSortable' => true,
		    'sortField' => 'jv.name',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getVacancyName'),
		));

	if ((isset ( $_SESSION ['isConsultant'] ) && $_SESSION ['isConsultant'] == 'Yes')) {
			$headerList [2]->populateFromArray ( array (
					'name' => 'Candidate',
					'isSortable' => true,
					'sortField' => 'jc.first_name',
					'elementType' => 'link',
					'elementProperty' => array (
							'labelGetter' => 'getCandidateName',
							'placeholderGetters' => array (
									'id' => 'getCandidateId' 
							),
							'urlPattern' => 'addCandidate?id={id}' 
					) 
			) );
		} else {
			$headerList [2]->populateFromArray ( array (
					'name' => 'Candidate',
					'isSortable' => true,
					'sortField' => 'jc.first_name',
					'elementType' => 'label',
					'elementProperty' => array (
							'getter' => 'getCandidateName' 
					) 
			) );
		}

	
		/* $headerList[3]->populateFromArray(array(
		    'name' => 'Referred By',
		    'isSortable' => true,
		    'sortField' => 'ref.emp_firstname',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getReferralName'),
		)); */
		
		$headerList[3]->populateFromArray(array(
		    'name' => 'Date of Application',
		    'isSortable' => true,
		    'sortField' => 'jc.date_of_application',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getDisplayDateOfApplication'),
		));

		$headerList[4]->populateFromArray(array(
		    'name' => 'Status',
		    'isSortable' => true,
		     'filters' => array('I18nCellFilter' => array()
                              ),
		    'sortField' => 'jcv.status',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getStatusName'),
		));

		$headerList[5]->populateFromArray(array(
		    'name' => 'Resume',
		    'isSortable' => false,
		    'elementType' => 'link',
		    'elementProperty' => array(
			'labelGetter' => 'getLink',
			'placeholderGetters' => array('id' => 'getAttachmentId'),
			'urlPattern' => 'viewCandidateAttachment?attachId={id}'),
		));

		$this->headers = $headerList;
	}

	public function getClassName() {
		return 'MyCandidate';
	}

}
