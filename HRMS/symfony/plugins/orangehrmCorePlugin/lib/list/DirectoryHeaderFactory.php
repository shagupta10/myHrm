<?php

class DirectoryHeaderFactory extends ohrmListConfigurationFactory {

    public function init() {
        sfContext::getInstance()->getConfiguration()->loadHelpers('OrangeDate');
        
        $header1 = new ListHeader();
        $header2 = new ListHeader();
        $header1->populateFromArray(array(
            'name' => 'Photo',
            'elementType' => 'photo',
            'isSortable' => true,
			'sortField' => 'employeeId',
        	'width' => '15%',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('labelGetter' => 'getEmployeeId',
            						   'placeholderGetters' => array('id'=> 'getEmpNumber'),
            ),
        ));

        $header2->populateFromArray(array(
            'name' => __('Details'),
            'elementType' => 'directory',
            'isSortable' => true,
 			'sortField' => 'firstMiddleName',
            'textAlignmentStyle' => 'left',
            'elementProperty' => array('labelGetter' => 'getEmpNumber',
            						   'placeholderGetters' => array(
												'id'=> 'getEmployeeId',
												'name'=> 'getFullName',
												'email'=> 'getEmpWorkEmail',
												'phone'=> 'getEmpMobile',
												'skype'=> 'getEmpSkypeId',
												'designation'=> 'getJobTitleName',
												'project'=> 'getProject',
												'domain/skills'=> 'getEmployeeSkills',
												'qualification'=> 'getEmployeeQualification',
												'bloodGrp' => 'getBloodGroup',
												'linkedInUrl'=> 'getLinkedInUrl',
												
												),
            ),
        	
        ));
        $this->headers = array($header1, $header2);
    }
    
    public function getClassName() {
        return 'Employee';
    }
}
