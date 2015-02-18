<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of changeProjectAction
 *
 * @author shaguptaf
 */
class changeProjectAction extends basePimAction {
    /* Get EmployeeService
     * @returns EmployeeService
     */
    private $projectService;
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }
    public function getProjectService() {
        if (is_null($this->projectService)) {
            $this->projectService = new ProjectService();
            $this->projectService->setProjectDao(new ProjectDao());
        }
        return $this->projectService;
    }
    
    
    public function execute($request) {
        
        $loggedInEmpNum = $this->getUser()->getEmployeeNumber();
        $loggedInUserName = $_SESSION['fname'];        
        $job = $request->getParameter('job');
        $empNumber = (isset($job['emp_number'])) ? $job['emp_number'] : $request->getParameter('empNumber');
        
        /*
         * TODO: $empNumber gets empty when uploaded file size exceeds PHP max upload size.
         * Check for a better solution.
         */
        if (empty($empNumber)) {
            $this->getUser()->setFlash('jobdetails.warning', __(TopLevelMessages::FILE_SIZE_SAVE_FAILURE));
            $this->redirect($request->getReferer());
        }
        
        $this->empNumber = $empNumber;
        
        $this->jobInformationPermission = $this->getDataGroupPermissions('job_details', $empNumber);
        $this->ownRecords = ($loggedInEmpNum == $empNumber) ? true : false;


        if (!$this->IsActionAccessible($empNumber)) {
            $this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
        }

        if ($this->getUser()->hasFlash('templateMessage')) {
            list($this->messageType, $this->message) = $this->getUser()->getFlash('templateMessage');
        }

        $employee = $this->getEmployeeService()->getEmployee($empNumber);
        $param = array('empNumber' => $empNumber, 'ESS' => $this->essMode,
            'employee' => $employee,
            'loggedInUser' => $loggedInEmpNum,
            'loggedInUserName' => $loggedInUserName);
        
        $joinedDate = $employee->getJoinedDate();

        $this->form = new EmployeeChangeProjectForm(array(), $param, true);
        
        
        $this->employeeState = $employee->getState();
        
        if ($loggedInEmpNum == $empNumber) {
            $this->allowActivate = FALSE;
            $this->allowTerminate = FALSE;
        } else {
            $allowedActions = $this->getContext()->getUserRoleManager()->getAllowedActions(WorkflowStateMachine::FLOW_EMPLOYEE, $this->employeeState);
            $this->allowActivate = isset($allowedActions[WorkflowStateMachine::EMPLOYEE_ACTION_REACTIVE]);
            $this->allowTerminate = isset($allowedActions[WorkflowStateMachine::EMPLOYEE_ACTION_TERMINATE]);            
        }
        
        if ($this->getRequest()->isMethod('post')) {


            // Handle the form submission           
            $this->form->bind($request->getParameter($this->form->getName()));

            if ($this->form->isValid()) {

                // save data
                if ($this->jobInformationPermission->canUpdate()) {
                    $service = new EmployeeService();
                    //Save employee details
                    //$service->saveEmployee($this->form->getEmployee(), false);
                    
                    //---- Save employee project details ------
                    $projectList = explode(",",$this->form->getValue('project')) ;
                  
                    $idList = array();
                    
                    $existingProjects = $service->getEmployeeProject($this->empNumber);
                    $transactions=array();
                    $projectTransactionDetails='';
                    if(count($existingProjects) > 0){
	                 foreach ( $existingProjects as $existingEmpProject ) {                            
		                if(!in_array($existingEmpProject->getProjectId(),$projectList)){           
                          $existingProjectHistory=$this->getEmployeeService()->getEmployeeProjectHistory($empNumber,$existingEmpProject->getProjectId());
                          if(!empty($existingProjectHistory))
                          { 
                            $existingProjectHistory->setEndDate(date('Y-m-d'));                                
                            $existingProjectHistory->save();                                
                          }
                          $transactions[]['delete']=Doctrine::getTable('Customer')->findOneByCustomerId($existingEmpProject->getCustomerId())->getName();
                          $existingEmpProject->delete();
		                }else{
			              $idList[] = $existingEmpProject->getProjectId();
                             
		                }
	                 }
                    }
                    
                    $newList = array_diff($projectList, $idList);
                    if(!empty($newList)){
                     foreach ($newList as $newProjectid) {
                        if(!empty($newProjectid)){
                          $empProject = new EmployeeProject();
                          $empProject->setEmpNumber($empNumber);
                          $project=$this->getProjectService()->getProjectById($newProjectid);
                          $empProject->setCustomerId($project->getCustomerId());
                          $empProject->setProjectId($newProjectid);
                          $empProject->setCreatedBy($loggedInEmpNum);
                          $empProject->setCreatedDate(date('Y-m-d H:i:s'));
                          $empProject->save();
                          //maintained history
                          $projectHistory=new EmployeeProjectHistory();
                          $projectHistory->setEmpNumber($empProject->empNumber);
                          $projectHistory->setCustomerId($project->getCustomerId());
                          $projectHistory->setProjectId($empProject->getProjectId());
                          $projectHistory->setStartDate(date('Y-m-d'));
                          $empProject->setCreatedBy($loggedInEmpNum);
                          $projectHistory->save();
                          $transactions[]['add']=Doctrine::getTable('Project')->findOneByProjectId($empProject->getProjectId())->getName();
                        }
                     }	
                    }    
                }
               //send mail functionality on project change
                $deleteCnt=0;
                $addedCnt=0;
                $body='';
                $transactions1=array();
                foreach($transactions as $key=>$value)
                {
                   if(isset($value['delete'])) 
                   {
                     $deleteCnt++; 
                     $transactions1[$deleteCnt]['delete']=$value['delete'];                     
                   }
                   if(isset($value['add'])) 
                   {
                     $addedCnt++;
                     $transactions1[$addedCnt]['add']=$value['add'];
                   }
                }
                foreach($transactions1 as $key=>$value)
                {
                    $delStr=(isset($value['delete']))?$value['delete']:'';
                    $addStr=(isset($value['add']))?$value['add']:'';                    
                    $body.="<tr><td>Project</td><td>".$delStr."</td><td>".$addStr."</td></tr>";
                }   
                $employeeObj = $this->getEmployeeService()->getEmployee($empNumber);
                $receipients['To'][]=$employeeObj->getEmpWorkEmail();
                //getAll Repoty
                $details=$employeeObj->getSupervisors();
                foreach($details as $eachSup)
                 $receipients['Cc'][]=$eachSup->getEmpWorkEmail();                
                $receipientName=$employeeObj->getFirstName();              
                $mailer = new PimMailer('admin');
			    $mailer->send($employeeObj->getFirstAndLastNames(), $empNumber, $body);
                $this->getUser()->setFlash('jobdetails.success', __(TopLevelMessages::UPDATE_SUCCESS));
            } else {
                $validationMsg = '';
                foreach ($this->form->getWidgetSchema()->getPositions() as $widgetName) {
                  if ($this->form[$widgetName]->hasError()) {
                    $validationMsg .= $this->form[$widgetName]->getError()->getMessageFormat();
                  }
                }
                $this->getUser()->setFlash('jobdetails.warning', $validationMsg);
            }
            $this->redirect('pim/changeProject?empNumber=' . $empNumber);
        }
    }

}
