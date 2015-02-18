<?php
	
class JobCandidateRequestsHeaderFactory extends ohrmListConfigurationFactory {
	
	protected function init() {
		
		$headerList = array();
		
		for ($i = 1; $i <= 6; $i++) {
			$headerList[$i] = new ListHeader();
		}
		
		$headerList[1]->populateFromArray(array(
			'name' => 'Candidate',
			'isSortable' => false,
			'elementType' => 'link',
			'elementProperty' => array(
				'labelGetter' => 'getCandidateName',
				'placeholderGetters' => array('id' => 'getCandidateId'),
				'urlPattern' => 'index.php/recruitment/addCandidate?id={id}'),
		));
		
		$headerList[2]->populateFromArray(array(
			'name' => 'Current Vacancy',
			'isSortable' => false,
			'elementType' => 'label',
			'elementProperty' => array('getter' => 'getOldVacancyName'),
		));
		$headerList[3]->populateFromArray(array(
			'name' => 'New Vacancy',
			'elementType' => 'label',
			'elementProperty' => array('getter' => 'getNewVacancyName'),
		));
		
		$headerList[4]->populateFromArray(array(
			'name' => 'Requester Name',
			'elementType' => 'label',
			'elementProperty' => array('getter' => 'getRequesterName'),
		));
		
		$headerList[5]->populateFromArray(array(
			'name' => 'Created Date',
			'elementType' => 'label',
			'elementProperty' => array('getter' => 'getDisplayCreatedDate'),
		));
		
		$headerList[6]->populateFromArray(array(
			'name' => 'Status',
			'elementType' => 'label',
			'elementProperty' => array('getter' => 'getStatusName'),
		));
		
	
//		$headerList[7]->populateFromArray(array(
//			'name' => 'History',
//			'isSortable' => false,
//			'elementType' => 'link',
//			'elementProperty' => array(
//				'labelGetter' => 'getLink',
//				'placeholderGetters' => array('id' => 'getAttachmentId'),
//				'urlPattern' => 'viewCandidateAttachment?attachId={id}'),
//		));
		
		$this->headers = $headerList;
	}
	
	public function getClassName() {
		return 'JobCandidateRequests';
	}
	
}
