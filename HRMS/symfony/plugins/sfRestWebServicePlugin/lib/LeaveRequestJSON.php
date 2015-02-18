<?php




class LeaveRequestJSON {
	public $employeeName;
	public $dateRange;
	public $leaveType;
	public $noOfDays;
	public $leaveBalance;
	public $status;
	public $comment;
	public $action;
	    
	public function getEmployeeName() 
	{
	  return $this->employeeName;
	}
	
	public function setEmployeeName($value) 
	{
	  $this->employeeName = $value;
	}
	 
	public function getDateRange()
	{
		return $this->dateRange;
	}
	
	public function setDateRange($value)
	{
		$this->dateRange = $value;
	}
	 
	public function getNoOfDays()
	{
		return $this->noOfDays;
	}
	
	public function setNoOfDays($value)
	{
		$this->noOfDays = $value;
	}
	 
	public function getLeaveBalance()
	{
		return $this->leaveBalance;
	}
	
	public function setLeaveBalance($value)
	{
		$this->leaveBalance = $value;
	}
	 
	public function getStatus()
	{
		return $this->status;
	}
	
	public function setStatus($value)
	{
		$this->status = $value;
	}
	 
	public function getComment()
	{
		return $this->comment;
	}
	
	public function setComment($value)
	{
		$this->comment = $value;
	}
	 
	public function getAction()
	{
		return $this->action;
	}
	
	public function setAction($value)
	{
		$this->action = $value;
	}
	
	public function getLeaveType()
	{
		return $this->leaveType;
	}
	
	public function setLeaveType($value)
	{
		$this->leaveType = $value;
	}
}