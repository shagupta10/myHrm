<?php

/**
 *
 *
 *
 */
class TrainerDao extends BaseDao {
	
	/**
	 *
	 *
	 */
	public function updateTrainer(Trainer $trainer) {
		try {
			$q = Doctrine_Query::create()
			->update('Trainer')
			->set('firstName','?', $trainer->getFirstName())
			->set('lastName','?', $trainer->getLastName())
			->set('details','?', $trainer->getDetails())
			->where('id = ?', $trainer->getId());
			return $q->execute();
		} catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
	}
	
	public function getTrainerList() {
		try {
				$q = Doctrine_Query::create()
					->from('Trainer')
					->where('is_deleted = ?', Trainer::IS_NOT_DELETED);
				return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
	
	public function getTrainerById($id) {
		try {
			return Doctrine::getTable('Trainer')->find($id);
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
	
	public function deleteTrainingTrainer($toDeleteArray, $trainingId) {
		try {
			foreach ($toDeleteArray as $trainer) {
				$trainerId = explode('_', $trainer);
				$q = Doctrine_Query::create()
					->delete('TrainingTrainer')
					->where('training_id = ?', $trainingId);
					if($trainerId[1] == TrainingTrainer::INTERNAL_TRAINER) {
						$q->andWhere('emp_number = ?', $trainerId[0]);
					} else {
						$q->andWhere('trainer_id = ?', $trainerId[0]);
					}
					$q->andWhere('trainerType = ?', $trainerId[1]);
				$q->execute();
			}
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
	
}