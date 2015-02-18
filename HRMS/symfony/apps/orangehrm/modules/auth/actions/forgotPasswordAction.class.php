<?php

class forgotPasswordAction extends sfAction {

	private $employeeService;
	private $userService;
	
	public function getEmployeeService() {
		if (is_null($this->employeeService)) {
			$this->employeeService = new EmployeeService();
		}
		return $this->employeeService;
	}
	
	public function getUserService() {
		if (is_null($this->userService)) {
			$this->userService = new UserService();
		}
		return $this->userService;
	}
	
	public function setForm(sfForm $form) {
		if (is_null($this->form)) {
			$this->form = $form;
		}
	}

    public function execute($request) {
       $this->setForm(new ForgetPasswordForm(array(), array(), true));
       if($request->isMethod('post'))
       {
       		$empid = $request->getParameter('employeeId');
       		$email = $request->getParameter('email');
       		$count = $this->getEmployeeService()->checksEmployeeExists($empid, $email);
       		if($count>0) {
       			$emp = $this->getEmployeeService()->getEmployeeByEmployeeId($empid);
       			$empNo = $emp->getEmpNumber();
       			$key = md5(uniqid(time(), true));
       			$this->getUserService()->saveResetPasswordDetails($empNo, $key);
       			$resetMailer = new ResetPasswordMailer();
       			$resetMailer->send($email, $key);
       			$this->getUser()->setFlash('forgotpassword.success', __(TopLevelMessages::RESET_EMAIL_SENT), false);
       		}
       		else {
       			$this->getUser()->setFlash('forgotpassword.warning', __(TopLevelMessages::EMPLOYEE_NOTEXISTS), false);
       		}
       }  
    }
}
?>