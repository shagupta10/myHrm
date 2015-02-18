<?php

/**
 * Form class for leave list
 */
class CompOffLeaveListForm extends sfForm {

    const MODE_MY_COMPOFF_LIST = 'my_compoff_list';
    const MODE_COMPOFF_LIST = 'default_compoff_list';

    private $mode;
    private $list = null;

    private $employeeList;
    private $leavePeriodService;
    private $companyStructureService;

    public function getCompanyStructureService() {
        if (is_null($this->companyStructureService)) {
            $this->companyStructureService = new CompanyStructureService();
            $this->companyStructureService->setCompanyStructureDao(new CompanyStructureDao());
        }
        return $this->companyStructureService;
    }

    public function setCompanyStructureService(CompanyStructureService $companyStructureService) {
        $this->companyStructureService = $companyStructureService;
    }


    public function getLeavePeriodService() {
        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }
        return $this->leavePeriodService;
    }
    
    public function __construct($mode) {

        $this->mode = $mode;        
        parent::__construct(array(), array());
    }

    public function configure() {

        $widgets = array();
        $labels = array();
        $validators = array();
        $defaults = array();
        
        sfContext::getInstance()->getConfiguration()->loadHelpers(array('I18N', 'OrangeDate'));
        $widgets['date'] =  new ohrmWidgetFormLeavePeriod(array());
         // $this->setWidget('date', new ohrmWidgetFormLeavePeriod(array()));
        $labels['date'] = __('Leave Period');
        
        $validators['date'] = new sfValidatorDateRange(array(
            'from_date' => new ohrmDateValidator(array('required' => true)),
            'to_date' => new ohrmDateValidator(array('required' => true))
        ));
        
        // CompOff Leave Statuses
        $compOffLeaveStatusChoices = Leave::getCompOffStatusTextList();     
        $defaultStatuses = array_keys($compOffLeaveStatusChoices);
        
        $widgets['chkSearchFilter'] = new ohrmWidgetCheckboxGroup(
                array('choices' => $compOffLeaveStatusChoices,
                      'show_all_option' => true,
                      'default' => $defaultStatuses));
            
        $labels['chkSearchFilter'] = 'CompOff Leave Status';
        $defaults['chkSearchFilter'] = $defaultStatuses;

        $validators['chkSearchFilter'] = new sfValidatorChoice(
                array('choices' => array_keys($compOffLeaveStatusChoices), 
                      'required' => false, 'multiple' => true));


        if ($this->mode != self::MODE_MY_COMPOFF_LIST) {

            $requiredPermissions = array(
                BasicUserRoleManager::PERMISSION_TYPE_ACTION => array('view_leave_list'));
            
            $widgets['txtEmployee'] = new ohrmWidgetEmployeeNameAutoFill(
                    array('loadingMethod'=>'ajax',
                          'requiredPermissions' => $requiredPermissions));
            
            $labels['txtEmployee'] = __('Employee');
            $validators['txtEmployee'] = new ohrmValidatorEmployeeNameAutoFill();
            
            // TODO check cmbWithTerminated if searching for terminated employee
            $widgets['cmbWithTerminated'] = new sfWidgetFormInputCheckbox(array('value_attribute_value' => 'on'));
            $labels['cmbWithTerminated'] =  __('Include Past Employees');
            $validators['cmbWithTerminated'] =  new sfValidatorBoolean(array('true_values' => array('on'), 'required' => false));                        
        }       
        
        $this->setWidgets($widgets);
        $this->getWidgetSchema()->setLabels($labels);
        $this->setvalidators($validators);
        $this->setDefaults($defaults);
        $this->setDefaultDates();
        $this->getWidgetSchema()->setNameFormat('compOffLeaveList[%s]');
    }
    

    protected function setDefaultDates() {
    	$now = time();
    
    	// If leave period defined, use leave period start and end date
    	$leavePeriod = $this->getLeavePeriodService()->getCurrentLeavePeriodByDate(date('Y-m-d', $now));
    	if (!empty($leavePeriod)) {
    		$fromDate   = $leavePeriod[0];
    		$toDate     = $leavePeriod[1];
    	} else {
    		// else use this year as the period
    		$year = date('Y', $now);
    		$fromDate = $year . '-1-1';
    		$toDate = $year . '-12-31';
    	}
    
    	$this->setDefault('date', array(
    			'from' => set_datepicker_date_format($fromDate),
    			'to' => set_datepicker_date_format($toDate)));
    }
    
    /**
     * Formats the title of the leave list according to the mode
     *
     * @return string Title of the leave list
     */
    public function getTitle() {

        if ($this->mode === self::MODE_MY_COMPOFF_LIST) {
            $title = __('My CompOff List');
        } else {
            $title = __('CompOff List');
        }

        return $title;
    }

    /**
     * Returns the set of action buttons associated with each mode of the leave list
     *
     * @return array Array of action buttons as instances of ohrmWidegetButton class
     */
    public function getSearchActionButtons() {
        return array(
            'btnSearch' => new ohrmWidgetButton('btnSearch', 'Search', array()),
            'btnReset' => new ohrmWidgetButton('btnReset', 'Reset', array('class' => 'reset')),
        );
    }

    public function setList($list) {
        $this->list = $list;
    }

    public function getList() {
        return $this->list;
    }

    public function getEmployeeList() {
        return $this->employeeList;
    }

    public function setEmployeeList($employeeList) {
        $this->employeeList = $employeeList;
    }    

    public function getActionButtons() {
        $actionButtons = array();
        if (!empty($this->list)) {
            $actionButtons['btnSave'] = new ohrmWidgetButton('btnSave', "Save", array('class' => 'savebutton'));
        }
        return $actionButtons;
    }
    
    public function getJavaScripts() {
        $javaScripts = parent::getJavaScripts();
        $javaScripts[] = plugin_web_path('orangehrmLeavePlugin', 'js/viewCompOffListSuccess.js');
        return $javaScripts;
    }
    
   

}
