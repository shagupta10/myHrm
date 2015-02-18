<?php
/**
 * returns directory
 * @return Directory
 * @package    api
 * @subpackage Action
 * @author     Abhishek Shringi
 */
class directoryAction extends BaseActionRest {
	public function preExecute() {
    	parent::preExecute();
    }
	public function execute($request) {
		set_time_limit(0);
		if($this->authentication == false) {
			return $this->renderText(json_encode(array('response' => 'failure', 'code' => '401')));
		} 
    	$request->setParameter('service', 'directory');
    	$this->service = 'directory';
		
		$connection = Doctrine_Manager::connection()->getDbh();
		if($request->getParameter('id') != "") {
			$query = "SELECT p.epic_filename,e.emp_number,e.employee_id,e.emp_firstname,e.emp_lastname FROM hs_hr_employee e LEFT JOIN hs_hr_emp_picture p ON e.emp_number = p.emp_number WHERE e.emp_number =  '".$request->getParameter('id')."' LIMIT 10";
		} else {
			$query = "SELECT p.epic_filename,e.emp_number,e.employee_id,e.emp_firstname,e.emp_lastname FROM hs_hr_employee e LEFT JOIN hs_hr_emp_picture p ON e.emp_number = p.emp_number LIMIT 10";
		}
		$statement = $connection->query($query);
		$listEmp = $statement->fetchAll(PDO::FETCH_ASSOC);
		return $this->renderText(json_encode($listEmp));
    }
}