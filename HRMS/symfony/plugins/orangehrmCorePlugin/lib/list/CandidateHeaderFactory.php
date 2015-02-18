<?php

class CandidateHeaderFactory extends ohrmListConfigurationFactory {

	protected function init() {

		$headerList = array();

		for ($i = 1; $i < 8; $i++) {
			$headerList[$i] = new ListHeader();
		}

		/* $headerList[1]->populateFromArray(array(
		    'name' => 'Vacancy',
		    'isSortable' => true,
		    'sortField' => 'jv.name',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getVacancyName'),
		)); */
		
		$headerList[1]->populateFromArray(array(
			'name' => __('Vacancy'),
            'elementType' => 'vacancy',
            'isSortable' => false,
 			'sortField' => 'jv.name',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getVacancyName',
            						   'placeholderGetters' => array('id' => 'getVacancyId', 'vacancyName' => 'getVacancyNameWithNumbers'),
            ),
		));		
	
		
		$headerList[2]->populateFromArray(array(
		    'name' => 'Candidate',
		    'isSortable' => true,
		    'sortField' => 'jc.first_name',
		    'elementType' => 'candidate',
		    'elementProperty' => array(
			'labelGetter' => 'getCandidateName',
			'placeholderGetters' => array('id' => 'getCandidateId', 'candidateVacancyId' => 'getCandidateVacancyId', 'attachId' => 'getAttachmentId', 
									'contactNumber' => 'getCandidateContactNumber', 'email' =>'getCandidateEmail', 'microResume' => 'getMicroResume',
									'status' => 'getStatusName' )
			),
		));
		
		

		$headerList[3]->populateFromArray(array(
		    'name' => 'Hiring Manager',
		    'isSortable' => true,
		    'sortField' => 'e.emp_firstname',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getHiringManagerName'),
		));
		
		$headerList[4]->populateFromArray(array(
		    'name' => 'Referred By',
		    'isSortable' => true,
		    'sortField' => 'ref.emp_firstname',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getReferralName'),
		));
		
		$headerList[5]->populateFromArray(array(
		    'name' => 'Date of Application',
		    'isSortable' => true,
		    'sortField' => 'jc.date_of_application',
		    'elementType' => 'label',
		    'elementProperty' => array('getter' => 'getDisplayDateOfApplication'),
		));

		$headerList[6]->populateFromArray(array(
		    'name' => __('Status'),
                    'elementType' => 'interview',
                    'isSortable' => false,
 		    'sortField' => 'jcv.status',
                    'textAlignmentStyle' => 'left',
                    'elementProperty' => array('getter' => 'getStatusName',
            						   'placeholderGetters' => array(
										'status' => 'getStatusName',
            						   			'id' => 'getCandidateId')
            ),
		));
		
	/* 	$headerList[6]->populateFromArray(array(
				'name' => 'Status',
				'isSortable' => true,
				'filters' => array('I18nCellFilter' => array()
				),
				'sortField' => 'jcv.status',
				'elementType' => 'label',
				'elementProperty' => array('getter' => 'getStatusName'),
		)); */
		
		$headerList[1]->setWidth('15%');
		$headerList[2]->setWidth('40%');
		

		$headerList[7]->populateFromArray(array(
		    'name' => 'Resume',
		    'isSortable' => false,
		    'elementType' => 'link',
		    'elementProperty' => array(
			'labelGetter' => 'getLink',
			'placeholderGetters' => array('id' => 'getAttachmentId'),
			'urlPattern' => 'index.php/recruitment/viewCandidateAttachment?attachId={id}'),
		));

		$this->headers = $headerList;
	}

	public function getClassName() {
		return 'Candidate';
	}

}
