<?php


class TrainingSearchParameters {
	private $trainingName;
	private $attendees;
	private $trainers;
	private $trainerEmps;
	private $fromDate;
	private $toDate;
	private $offset = 0;
	private $limit = 10;
	private $sortField = 'ot.id';
	private $sortOrder = 'ASC';
	private $id;
	private $published;
	private $attendance = 'Attendance';
	
	public function getId() {
		return $this->id;
	}
	
	public function setId($id) {
		$this->id = $id;
	}
	
 	public function getTrainingName() {
 		return $this->trainingName;
 	}
 	
 	public function setTrainingName($trainingName) {
 		$this->trainingName = $trainingName;
 	}
 	
 	public function getAttendees() {
 		return $this->attendees;
 	}
 	
 	public function setAttendees($attendees) {
 		$this->attendees = $attendees;
 	}
 	
 	public function getTrainers() {
 		return $this->trainers;
 	}
 	
 	public function setTrainers($trainers) {
 		$this->trainers = $trainers;
 	}
 	
 	public function getTrainerEmps() {
 		return $this->trainerEmps;
 	}
 	
 	public function setTrainerEmps($trainerEmps) {
 		$this->trainerEmps = $trainerEmps;
 	}
 	
 	public function getToDate() {
 		return $this->toDate;
 	}
 	
 	public function setToDate($toDate) {
 		$this->toDate = $toDate;
 	}
 	
 	public function getFromDate() {
 		return $this->fromDate;
 	}
 	
 	public function setFromDate($fromDate) {
 		$this->fromDate = $fromDate;
 	}
 	
 	public function getLimit() {
 		return $this->limit;
 	}
 	
 	public function setLimit($limit) {
 		$this->limit = $limit;
 	}
 	
 	public function getOffset() {
 		return $this->offset;
 	}
 	
 	public function setOffset($offset) {
 		$this->offset = $offset;
 	}
 	
 	public function getSortField() {
 		return $this->sortField;
 	}
 	
 	public function setSortField($sortField) {
 		$this->sortField = $sortField;
 	}
 	
 	public function getSortOrder() {
 		return $this->sortOrder;
 	}
 	
 	public function setSortOrder($sortOrder) {
 		$this->sortOrder = $sortOrder;
 	}
 	
 	public function getPublished() {
 		return $this->published;
 	}
 	
 	public function setPublished($published) {
 		$this->published = $published;
 	}
 	
 	public function getAttendance() {
 		return $this->attendance;
 	}
}