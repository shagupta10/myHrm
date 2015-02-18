<?php

/**
 *
 *
 *
 */
class TrainerService extends BaseService {
	
	private $trainerDao;
	private $trainerService;
	
	public function getTrainerDao() {
		return $this->trainerDao;
	}
	
	public function setTrainerDao(TrainerDao $trainerDao) {
		$this->trainerDao = $trainerDao;
	}
	
    /**
     * Construct
     */
    public function __construct() {
        $this->trainerDao = new trainerDao();
    }
	
    public function updateTrainer($trainer) {
    	return $this->getTrainerDao()->updateTrainer($trainer);
    }
    
    public function getTrainerList() {
    	return $this->getTrainerDao()->getTrainerList();
    }
    
    public function getTrainerById($id) {
    	return $this->getTrainerDao()->getTrainerById($id);
    }
    
    public function deleteTrainingTrainer($toDeleteArray, $trainingId) {
    	return $this->getTrainerDao()->deleteTrainingTrainer($toDeleteArray, $trainingId);
    }

}