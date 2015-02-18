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
class EmployeeCertificationForm extends sfForm {
    
    private $employeeService;
    public $fullName;
    private $widgets = array();
    public $empCertificationList;

    /**
     * Get EmployeeService
     * @returns EmployeeService
     */
    public function getEmployeeService() {
        if(is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }

    /**
     * Set EmployeeService
     * @param EmployeeService $employeeService
     */
    public function setEmployeeService(EmployeeService $employeeService) {
        $this->employeeService = $employeeService;
    }

    public function configure() {
        
		$this->certificationPermissions = $this->getOption('certificationPermissions');
        
        $empNumber = $this->getOption('empNumber');
        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $this->fullName = $employee->getFullName();

        $this->empCertificationList = $this->getEmployeeService()->getEmployeeCertifications($empNumber);
		
        $widgets = array('emp_number' => new sfWidgetFormInputHidden(array(), array('value' => $empNumber)));
        $validators = array('emp_number' => new sfValidatorString(array('required' => false)));
        
        if ($this->certificationPermissions->canRead()) {

            $certificationsWidgets = $this->getCertificationsWidgets();
            $certificationsValidators = $this->getCertificationsValidators();

            if (!($this->certificationPermissions->canUpdate() || $this->certificationPermissions->canCreate()) ) {
                foreach ($certificationsWidgets as $widgetName => $widget) {
                    $widget->setAttribute('disabled', 'disabled');
                }
            }
            $widgets = array_merge($widgets, $certificationsWidgets);
            $validators = array_merge($validators, $certificationsValidators);
        }

        $this->setWidgets($widgets);
        $this->setValidators($validators);


        $this->widgetSchema->setNameFormat('certification[%s]');
    }
    
    
    /*
     * Tis fuction will return the widgets of the form
     */
    public function getCertificationsWidgets() {
        $widgets = array();

        //creating widgets
		$widgets['certification_id'] = new sfWidgetFormInputHidden();
		$widgets['approve'] = new sfWidgetFormInputHidden();
        $widgets['name'] = new sfWidgetFormInputText();
        $widgets['institute'] = new sfWidgetFormInputText();
        $widgets['date'] = new ohrmWidgetDatePicker(array(), array('id' => 'certDate'));
		$widgets['grade'] = new sfWidgetFormInputText();
		$widgets['cattach'] = new sfWidgetFormInputFileEditable(
                    array('edit_mode' => false,
                        'with_delete' => false,
                        'file_src' => ''));
		$widgets['certification_link'] = new sfWidgetFormInputText();
        return $widgets;
    }

    /*
     * Tis fuction will return the form validators
     */
    public function getCertificationsValidators() {
        $inputDatePattern = sfContext::getInstance()->getUser()->getDateFormat();
        $validators = array(
			'certification_id' => new sfValidatorInteger(array('required' => false)),
			'approve' => new sfValidatorInteger(array('required' => false)),
            'name' => new sfValidatorString(array('required' => true, 'max_length' => 100)),
            'institute' => new sfValidatorString(array('required' => true)),
			'date' => new ohrmDateValidator(array('date_format' => $inputDatePattern, 'required' => false), array('invalid' => 'Date format should be ' . $inputDatePattern)),
			'grade' => new sfValidatorString(array('required' => false)),
			'cattach' => new sfValidatorFile(array('required' => false, 'max_size' => 1024000, 'validated_file_class' => 'orangehrmValidatedFile')),
			'certification_link' => new sfValidatorString(array('required' => false)),
        );

        return $validators;
    }
}
?>