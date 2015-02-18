<?php
/**
 * returns leaves of all employees
 * @return Leave
 * @package    api
 * @subpackage Action
 * @author     Mayur Kathale
 */
class leavesAction extends BaseActionRest {
	public function preExecute() {
    	parent::preExecute();
    }
    public function execute($request) {
		set_time_limit(0);
		if($this->authentication == false) {
			return $this->renderText(json_encode(array('response' => 'failure', 'code' => '401'), JSON_FORCE_OBJECT));
		} 
    	$request->setParameter('service', 'leaves');
    	$this->service = 'leaves';
    	$query = Doctrine::getTable('Leave')->createQuery('wsmodel');
    	if($request->getParameter('id')) {
    		$query->where('emp_number = ?', $request->getParameter('id'));
    	}
    	$this->executeRequest($query, $request);
    }
}