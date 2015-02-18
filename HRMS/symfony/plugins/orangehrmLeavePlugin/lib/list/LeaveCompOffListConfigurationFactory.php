<?php

class LeaveCompOffListConfigurationFactory extends ohrmListConfigurationFactory {
	
	protected static $listMode;
	protected static $loggedInEmpNumber;
	
	public function init() {
		sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');
		
		$header1 = new LeaveListHeader();
		$header2 = new LeaveListHeader();
		$header3 = new LeaveListHeader();
		$header4 = new LeaveListHeader();
		$header5 = new LeaveListHeader();
		$header6 = new LeaveListHeader();
		
		$header1->populateFromArray(array(
			'name' => 'Employee Name',
			'width' => '20%',
			'isSortable' => false,
			'elementType' => 'link',
			'textAlignmentStyle' => 'left',
			'elementProperty' => array(
				'labelGetter' => array('getEmployee', 'getFirstAndLastNames'),
				'placeholderGetters' => array('id' => 'getEmpNumber'),
				'urlPattern' => public_path('index.php/pim/viewPersonalDetails/empNumber/{id}'),
			),
		));
		
		$header2->populateFromArray(array(
			'name' => 'Number of Days',
			'width' => '12%',
			'isSortable' => false,
			'elementType' => 'label',
			'textAlignmentStyle' => 'right',
			'elementProperty' => array('getter' => 'getNumberOfDays'),
		));
		
		$header3->populateFromArray(array(
			'name' => 'CompOff Details',
			'width' => '20%',
			'isSortable' => false,
			'elementType' => 'label',
			'textAlignmentStyle' => 'right',
			'elementProperty' => array('getter' => 'getCompoffDetails'),
		));
		
		$header4->populateFromArray(array(
			'name' => 'Status',
			'width' => '20%',
			'isSortable' => false,
			'elementType' => 'label',
			'textAlignmentStyle' => 'right',
			'elementProperty' => array('getter' => 'getStatusText'),
		));
		
		$header5->populateFromArray(array(
			'name' => 'Created Date',
			'width' => '19%',
			'isSortable' => false,
			'elementType' => 'label',
			'textAlignmentStyle' => 'left',
			  'filters' => array('DateCellFilter' => array()),  
			'elementProperty' => array('getter' => 'getCreatedDate'),
		));
		
		$leaveCompOffService = new LeaveCompOffService();
        $header6->populateFromArray(array(
            'name' => 'Actions',
            'width' => '10%',
            'isSortable' => false,
            'isExportable' => false,
            'elementType' => 'selectSingle',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'classPattern' => 'select_action quotaSelect',
                'defaultOption' => array('label' => 'Select Action', 'value' => ''),
                'hideIfEmpty' => true,
                'options' => array($leaveCompOffService, 'getLeaveCompOffActions', array(self::RECORD, self::$loggedInEmpNumber)),
                'namePattern' => 'select_compoff_leave_action[{id}]',
                'idPattern' => 'select_compoff_leave_action_{id}',
                'hasHiddenField' => true,
                'hiddenFieldName' => 'leaveCompOff[{id}]',
                'hiddenFieldId' => '{id}-{eimId}',
                'hiddenFieldValueGetter' => 'getId',
                'hiddenFieldClass' => 'quotaHolder',
                'placeholderGetters' => array(
                    'id' => 'getId',
                    'eimId' => 'getEmpNumber',
                ),
            ),
        ));
		
	
		
		$this->headers = array($header1, $header2, $header3, $header4, $header5,$header6);
	}
	
	public function getClassName() {
		return 'LeaveCompOff';
	}
	
	public static function setListMode($listMode) {
		self::$listMode = $listMode;
	}
	
	public static function setLoggedInEmpNumber($empNumber) {
		self::$loggedInEmpNumber = $empNumber;
	}     
}
