<?php
class MyFeedbackSearchParameters {
	private $id;
	private $empNumber;
	private $empName;
	private $fromDate = '1900-01-01';
	private $toDate = '2200-01-01';
	private $sortField = 'fb.created_date';
	private $sortOrder = 'DESC';
	private $offset = 0;
	private $limit = 10;
	private $reviewer;
	private $reviewerName;
	private $reviewPeriod;
	
	public function getId(){
		return $this->id;
	}
	
	public function setId($id){
		$this->id = $id;
	}
	
	public function getEmpNumber(){
		return $this->empNumber;
	}
	
	public function setEmpNumber($empNumber){
		$this->empNumber = $empNumber;
	}
	
	public function getEmpName(){
		return $this->empName;
	}
	
	public function setEmpName($empName){
		$this->empName = $empName;
	}
	
	public function getFromDate(){
		return $this->fromDate;
	}
	
	public function setFromDate($fromDate){
		$this->fromDate = $fromDate;
	}
	public function getToDate(){
		return $this->toDate;
	}
	public function setToDate($toDate){
		$this->toDate = $toDate;
	}
	public function getSortField(){
		return $this->sortField;
	}
	public function setSortField($sortField){
		$this->sortField = $sortField;
	}
	public function getSortOrder(){
		return $this->sortOrder;
	}
	public function setSortOrder($sortOrder){
		$this->sortOrder = $sortOrder;
	}
	public function getLimit(){
		return $this->limit;
	}
	public function setLimit($limit){
		$this->limit = $limit;
	}
	
	public function getOffset(){
		return $this->offset;
	}
	public function setOffset($offset){
		$this->offset = $offset;
	}
	
	public function getReviewer(){
		return $this->reviewer;
	}
	public function setReviewer($reviewer){
		$this->reviewer = $reviewer;
	}
	
	public function getReviewerName(){
		return $this->reviewerName;
	}
	
	public function setReviewerName($reviewerName){
		$this->reviewerName = $reviewerName;
	}
	
	public function getReviewPeriod(){
		return $this->reviewPeriod;
	}
	
	public function setReviewPeriod($reviewPeriod){
		$this->reviewPeriod = $reviewPeriod;
	}
}