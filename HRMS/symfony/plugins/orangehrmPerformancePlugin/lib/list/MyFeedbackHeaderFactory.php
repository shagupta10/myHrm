<?php
class MyFeedbackHeaderFactory extends ohrmListConfigurationFactory {

	protected function init() {
		$headerList = array();

		for ($i = 1; $i < 5; $i++) {
			$headerList[$i] = new ListHeader();
		}

		$headerList[1]->populateFromArray(array(
				'name' => 'Employee Name',
				'isSortable' => true,
				'sortField' => 'empName',
				'elementType' => 'link',
				'textAlignmentStyle' => 'left',
				'elementProperty' => array(
						'labelGetter' => 'getEmpName',
						'placeholderGetters' => array('id' => 'getId'),
						'urlPattern' => 'index.php/performance/addFeedback/id/{id}'),
		));
		
	 	 $headerList[2]->populateFromArray(array(
				'name' => 'Review Period', 
				'isSortable' => false,
				'elementType' => 'label',
	 	 		'textAlignmentStyle' => 'left',
				'elementProperty' => array('getter' => 'getReviewPeriod'),
		)); 
	 	 
	 	 $headerList[3]->populateFromArray(array(
	 	 		'name' => 'Reviewer Name',
	 	 		'isSortable' => true,
	 	 		'sortField' => 'revName',
	 	 		'elementType' => 'label',
	 	 		'textAlignmentStyle' => 'left',
	 	 		'elementProperty' => array('getter' => 'getReviewerName'),
	 	 ));
	 	 
 	  	$headerList[4]->populateFromArray(array(
	 	 		'name' => '',
	 	 		'isSortable' => false,
	 	 		'elementType' => 'link',
	 	 		'textAlignmentStyle' => 'left',
	 	 		'elementProperty' => array(
					'label' => 'View',
					'placeholderGetters' => array('id' => 'getEmpNumber'),
					'urlPattern' => 'viewMultiSourceFeedback/eid/{id}',
					'target' => '_blank'),
	 	 ));
		$this->headers = $headerList;
	}

	public function getClassName() {
		return 'MyFeedback';
	}

}
