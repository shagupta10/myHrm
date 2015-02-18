<?php
class PerformanceReviewListConfigurationFactory extends ohrmListConfigurationFactory {
    protected static $loggedInEmpNumber;
    protected $currentFromDate;
    protected $currentReviewPeriod;
    
    public function init() {
        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');
        
        $header1 = new ListHeader();
        $header2 = new ListHeader();
        $header3 = new ListHeader();
        $header4 = new ListHeader();
        $header5 = new ListHeader();
        $header6 = new ListHeader();
        $header7 = new ListHeader();
        $header8 = new ListHeader();
        
        $header1->populateFromArray(array(
            'name' => __('Employee'),
            'width' => '10%',
            'isSortable' => true,
            'sortField' => 'emp_firstname',
            'elementType' => 'link',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
                'labelGetter' => array('getEmployee', 'getFirstAndLastNames'),
                'placeholderGetters' => array('id' => 'getId', 'pageName' => 'getReviewUrl'),
                'urlPattern' => public_path('index.php/performance/{pageName}/id/{id}'),
            ),
        ));

        $header2->populateFromArray(array(
            'name' => 'Job Title',
            'width' => '15%',
            'isSortable' => true,
            'sortField' => 'job_title',
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getEmpJobTitle')
        ));

        $header3->populateFromArray(array(
            'name' => 'Status',
            'width' => '7%',
            'isSortable' => true,
            'sortField' => 'state',
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getTextStatus',
            'placeholderGetters' => array('id' => 'status'))
        ));

        $header4->populateFromArray(array(
            'name' => 'Review Period',
            'width' => '20%',
            'isSortable' => true,
            'sortField' => 'period_from',
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getReviewPeriod')
        ));
        
        $header5->populateFromArray(array(
            'name' => 'Due Date',
            'width' => '13%',
            'isSortable' => true,
            'sortField' => 'due_date',
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getFormattedDueDate')
        ));
        $header6->populateFromArray(array(
            'name' => 'Final Grade',
            'width' => '7%',
            'isSortable' => true,
            'sortField' => 'final_rating',
            'elementType' => 'label',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('getter' => 'getEmpFinalRating')
        ));

        $header7->populateFromArray(array(
            'name' => 'Primary Reviewer',
            'width' => '10%',
            'isSortable' => false,
            'elementType' => 'primaryReviewer',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
			'labelGetter' => array('getPrimaryReviewer','getReviewer', 'getFirstAndLastNames'),
			'placeholderGetters' => array(
				'id' => 'getId',
				'empNumber' => 'getEmployeeId',
                'primaryReviewerList' => 'getPrimaryReviewerJSON'
                )
            )
        ));
        
        $header8->populateFromArray(array(
            'name' => 'Reviewer(s)',
            'width' => '25%',
            'isSortable' => false,
            'elementType' => 'reviewers',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array(
            'labelGetter' => array('getSecondaryReviewersList','getReviewer', 'getFirstAndLastNames'),
            'placeholderGetters' => array(
                'id' => 'getId',
                'periodFrom' => 'getPeriodFrom',
                'state' => 'getState',
                'reviewersList' => 'getSecondaryReviewersJSON')
            )
        ));
        
        $this->headers = array($header1, $header2, $header3, $header4, $header5, $header6, $header7, $header8);
    }
    
    public function getClassName() {
        return 'PerformanceReview';
    }
    public static function setLoggedInEmpNumber($empNumber) {
        self::$loggedInEmpNumber = $empNumber;
    }
    public function setCurrentFromDate($currentFromDate) {
        $this->currentFromDate = $currentFromDate;
    }
}
