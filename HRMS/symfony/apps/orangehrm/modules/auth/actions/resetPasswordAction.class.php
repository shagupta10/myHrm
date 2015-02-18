<?php
class resetPasswordAction extends sfAction {
	private $userService;
	public $empid;
	private $systemUserService;
	
	public function getUserService() {
		if (is_null($this->userService)) {
			$this->userService = new UserService();
		}
		return $this->userService;
	}
	
	public function getSystemUserService() {
		if (is_null($this->systemUserService)) {
			$this->systemUserService = new SystemUserService();
		}
		return $this->systemUserService;
	}
	
	public function setForm(sfForm $form) {
		if (is_null($this->form)) {
			$this->form = $form;
		}
	}
	
	public function execute($request) {
		$key = $request->getParameter('rk');
		$this->setForm(new ResetPasswordForm());
		$empResetId = $this->getUserService()->getEmpNumberIfTokenActive($key);
		if($request->isMethod('post')) {
			$pwd = $request->getPostParameter('reset[password]');
			$this->getSystemUserService()->saveResetPassword($empResetId, $key, $pwd);
			$empResetId = -1;
		}
		
		if($empResetId>0) {
			$this->key=$key;
		} else {
			$this->key="";
			if($empResetId == -1)
			{
				$this->key="passwordreset";
			}
		}
	}
}
?>