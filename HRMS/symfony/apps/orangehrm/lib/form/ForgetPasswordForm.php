<?php
class ForgetPasswordForm extends sfForm {
	public function configure() {
	$this->setWidgets(array(
				'employeeId' => new sfWidgetFormInputText(array(), array('id' => 'employeeid')),
				'email' => new sfWidgetFormInputText(array(), array('id' => 'emailid'))
				)); 

	}
}