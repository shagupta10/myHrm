<?php


class TrainerHeaderFactory extends ohrmListConfigurationFactory {
	protected function init() {

		$header1 = new ListHeader();
		$header2 = new ListHeader();


		$header1->populateFromArray(array(
				'name' => 'Trainer Name',
				'elementType' => 'link',
				'width' => '40%',
				'elementProperty' => array(
						'labelGetter' => 'getFirstAndLastNames',
						'urlPattern' => 'javascript:')
		));

		$header2->populateFromArray(array(
				'name' => 'Details',
				'elementType' => 'label',
				'width' => '60%',
				'elementProperty' => array(
						'getter' => 'getDetails')
		));

		$this->headers = array($header1, $header2);
	}

	public function getClassName() {
		return 'Trainer';
	}
}