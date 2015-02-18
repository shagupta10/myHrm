<?php

/**
 *
 *
 *
 */
class TrainingService extends BaseService {
	
	private $trainingDao;
	private $trainingService;
	
	public function getTrainingDao() {
		return $this->trainingDao;
	}
	
	public function setTrainingDao(TrainingDao $trainingDao) {
		$this->trainingDao = $trainingDao;
	}
	
    /**
     * Construct
     */
    public function __construct() {
        $this->trainingDao = new TrainingDao();
    }
	
    public function addTraining(Training $training) {
    	return $this->getTrainingDao()->addTraining($training);
    }
    
    public function getTrainingById($id) {
    	return $this->getTrainingDao()->getTrainingById($id);
    }
    
    public function getUpcomingTrainings() {
    	return $this->getTrainingDao()->getUpcomingTrainings();
    }
    
    public function searchTrainings($searchParameters, $isCount = false) {
    	return $this->getTrainingDao()->searchTrainings($searchParameters, $isCount);
    }
    
    public function updateTraining($training) {
    	return $this->getTrainingDao()->updateTraining($training);
    }
    
    public function getTrainingListByProperties($properties,$offset,$limit,$count) {
    	return $this->getTrainingDao()->getTrainingListByProperties($properties,$offset,$limit,$count);
    }
    
    public function getSubscribedTrainings($empNumber) {
    	return $this->getTrainingDao()->getSubscribedTrainings($empNumber);
    }
    
	public function unregisterTraining($trainingId, $empNumber) {
    	return $this->getTrainingDao()->unregisterTraining($trainingId, $empNumber);
    }
    
    public function getTrainingScheduleById($id) {
    	return $this->getTrainingDao()->getTrainingScheduleById($id);
    }
    
    public function deleteTrainingSchedules($ids) {
    	return $this->getTrainingDao()->deleteTrainingSchedules($ids);
    }

    public function getExistingAttendance($trainingId) {
    	return $this->getTrainingDao()->getExistingAttendance($trainingId);
    }
    
    public function saveTrainingAttendance($newAttendance, $existingAttendance) {
    	$this->processTrainingAttendance($newAttendance, $existingAttendance);
    }
    
    private function processTrainingAttendance($newAttendance, $existingAttendance) {
    	$existingSchedules = array();
    	$newSchedules = array();
    	foreach ($newAttendance as $new) {
    		array_push($newSchedules, $new['schedule']);
    		$scheduleExist = false;
    		foreach ($existingAttendance as $existing) {
    			if(!in_array($existing['schedule'], $existingSchedules)) {
    				array_push($existingSchedules, $existing['schedule']);
    			}
    			if($new['schedule'] == $existing['schedule']) {
    				$save = array();
    				$delete = array();
    				$scheduleExist = true;
    				$delete = array_diff($existing['attendees'], $new['attendees']);
    				$save = array_diff($new['attendees'], $existing['attendees']);
    				//var_dump($new['schedule']. " save: ".implode(" ,", $save)." delete: ".implode(", ", $delete)."<br>");
    				$this->saveAttendance($new['schedule'], $save);
    				$this->deleteAttendance($new['schedule'], $delete);
    			}
    		}
    		if(!$scheduleExist) {
    			//var_dump($new['schedule']. " to save all: ".implode(", ", $new['attendees'])."<br>");
    			$this->saveAttendance($new['schedule'], $new['attendees']); // save attendance to db here
    		}
    	}
    	foreach (array_diff($existingSchedules, $newSchedules) as $schToDelete) {
    		var_dump('delete all records of : '.$schToDelete.'<br><br><br>');
    		$this->deleteAttendance($schToDelete, 0);
    	}
    }
    
    public function saveAttendance($scheduleId, $empNumbers) {
    	$this->getTrainingDao()->saveAttendance($scheduleId, $empNumbers);
    }
    
    public function deleteAttendance($scheduleId, $empNumbers) {
    	$this->getTrainingDao()->deleteAttendance($scheduleId, $empNumbers);
    }
    
    public function getTrainingAttendanceByTrainingId($id) {
    	return $this->getTrainingDao()->getTrainingAttendanceByTrainingId($id);
    }
}