<?php
/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class PerformanceEvents {
    /** shagupta
     * Get EmployeeService
     * @returns EmployeeService
     */
    private $performanceReviewService, $employeeService;
    
    public function getEmployeeService() {
        if (is_null($this->employeeService)) {
            $this->employeeService = new EmployeeService();
            $this->employeeService->setEmployeeDao(new EmployeeDao());
        }
        return $this->employeeService;
    }
        
    public function getPerformanceReviewService() {
		if (is_null($this->performanceReviewService)) {
			$this->performanceReviewService = new PerformanceReviewService();
			$this->performanceReviewService->setPerformanceReviewDao(new PerformanceReviewDao());
		}
		return $this->performanceReviewService;
	}
 /* DESC:- Set Newly added reporty as a primary reviewer & old preimary reviewer as secondary reveriwer */
     function setPrimaryReviwerOnReportyChange($empNumber,$newPrimaryReviwer,$direcReporty=0){
        $pdo = Doctrine_Manager::connection()->getDbh();
        $performanceReviewService = $this->getPerformanceReviewService();
        $lastPerformancePeriod = $performanceReviewService->getLatestPerformancePeriodCycle();
        $fromDate = $lastPerformancePeriod->getPeriodFrom();
        $toDate = $lastPerformancePeriod->getPeriodTo();
        $resObj=$performanceReviewService->getEmployeePerformanceReview($empNumber,$fromDate,$toDate);
        $reviwerRecordId='';
        $primaryReviwer='';
        $id='';
        $primaryStatus=false;
        if(!empty($resObj)) {
            foreach($resObj as $res) 
            {    $id=$res['id'];
                 $reviwerRecordId=$res['reviwer_record'];
				                                  
                 if($res['is_primary']){
                 	$primaryReviwer = $res['reviewer_id'];        
                 	//set all other reviewers as secondary in order to set only one primary reviewer
                 	$performanceReviewService->updateReviewer($id,$primaryReviwer);
                 }
                 if($newPrimaryReviwer==$res['reviewer_id']) {
                 	//set existing reviewer as primary
                 	$performanceReviewService->updateReviewer($id,$newPrimaryReviwer,true);
                 	return true;
                 }else{ 
                 	// Add new Primary reviewer
                 	$primaryStatus = true;
                 }
            }
            if($primaryStatus){
            	$reviewArr = array('id' => $id);
            	$performanceReview= $this->getPerformanceReviewService()->readPerformanceReview($reviewArr);            	
            	$performanceReviewer = new PerformanceReviewReviewer();
            	$performanceReviewer->setReviewId($id);
            	$performanceReviewer->setReviewerId($newPrimaryReviwer);
            	$performanceReviewer->setKpis($performanceReview->getKpis());
            	$performanceReviewer->setIsPrimary($direcReporty);
            	$performanceReviewer->setIsDeleted(PerformanceReviewReviewer::IS_NOT_DELETED);
            	$performanceReviewer->save();
            	
            	$this->sendEmailOnPrimaryChange($empNumber,$primaryReviwer,$newPrimaryReviwer);
            	return true;
            }
        }else{
        	return false;
        }
    }
    
    function sendEmailOnPrimaryChange($empNumber,$primaryReviwer,$newPrimaryReviwer){
        $employeeObj = $this->getEmployeeService()->getEmployee($empNumber);
		$receipients['To'][]=$employeeObj->getEmpWorkEmail();
        //getAll Repoty
        $oldPrimaryName='';
        $newPrimaryName='';
        $body='';
        $details=$employeeObj->getSupervisors();
        foreach($details as $eachSup){  
	    	$receipients['Cc'][]=$eachSup->getEmpWorkEmail(); 
	        if($eachSup->getEmpNumber()==$primaryReviwer){
		    	$oldPrimaryName= $eachSup->getFirstAndLastNames();   
	        }
	        if($eachSup->getEmpNumber()==$newPrimaryReviwer){
	        	$newPrimaryName= $eachSup->getFirstAndLastNames();   
	        }
        }
        $subject='Project Changes';
        $receipientName=$employeeObj->getFirstName();  
        $body.="<tr><td>Primary Reviewer</td><td>".$oldPrimaryName."</td><td>".$newPrimaryName."</td></tr>";
                
        $mailer = new PimMailer('admin');
		$mailer->send($employeeObj->getFirstAndLastNames(), $empNumber, $body);
      
     }
}
