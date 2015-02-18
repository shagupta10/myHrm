<?php
class ResetPasswordForm extends sfForm {
	public function configure() {

		$this->setWidgets(array(
				'password' => new sfWidgetFormInputPassword(array(),array('id' => 'pwd')),
				'repassword' => new sfWidgetFormInputPassword(array(),array('id' => 'repwd')),
				'empNumber' => new sfWidgetFormInputHidden()	
		));
		$this->widgetSchema->setNameFormat('reset[%s]');
	}
}
?>