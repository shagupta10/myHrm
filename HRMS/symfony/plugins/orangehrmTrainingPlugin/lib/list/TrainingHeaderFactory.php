<?php


class TrainingHeaderFactory extends ohrmListConfigurationFactory {
	protected function init() {

		$header1 = new ListHeader();
		$header2 = new ListHeader();
		$header3 = new ListHeader();
		$header4 = new ListHeader();
		$header5 = new ListHeader();
		$header6 = new ListHeader();
		
		$header1->populateFromArray(array(
				'name' => 'Training Name',
				'width' => '45%',
				'isSortable' => true,
		    	'sortField' => 'ot.topic',
				'elementType' => 'link',
				'elementProperty' => array(
						'labelGetter' => 'getTrainingName',
						'placeholderGetters' => array('id' => 'getId'),
						'urlPattern' => 'index.php/training/addTraining/id/{id}')
		));

		 $header2->populateFromArray(array(
				'name' => 'From Date',
				'elementType' => 'label',
				'width' => '20%',
				'isSortable' => true,
				'sortField' => 'from_Date',
				'elementProperty' => array(
						'getter' => 'getFromDate')
		));
		
		$header3->populateFromArray(array(
				'name' => 'To Date',
				'elementType' => 'label',
				'width' => '20%',
				'isSortable' => true,
				'sortField' => 'to_Date',
				'elementProperty' => array(
						'getter' => 'getToDate')
		));
		
		$header4->populateFromArray(array(
				'name' => 'Trainers',
				'width' => '25%',
				'elementType' => 'label',
				'elementProperty' => array(
						'getter' => 'getTrainers')
		));
		
		$header5->populateFromArray(array(
				'name' => 'Status',
				'width' => '10%',
				'isSortable' => true,
				'sortField' => 'ot.is_published',
				'elementType' => 'label',
				'elementProperty' => array(
						'getter' => 'getPublished')
		));
		
		$header6->populateFromArray(array(
				'name' => 'Attendance',
				'width' => '15%',
				'elementType' => 'link',
				'elementProperty' => array(
						'labelGetter' => 'getAttendance',
						'placeholderGetters' => array('id' => 'getId'),
						'urlPattern' => 'index.php/training/trainingAttendance/id/{id}')
		));

		$this->headers = array($header1, $header2, $header3, $header4, $header5, $header6);
	}

	public function getClassName() {
		return 'Training';
	}
}