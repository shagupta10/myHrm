<?php

/**
 *
 *
 *
 */
class TrainingDao extends BaseDao {
	
	public function getTrainingById($id) {
		try {
			return Doctrine :: getTable('Training')->find($id);
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
	
	public function searchTrainings(TrainingSearchParameters $searchParameters, $isCount) {
		try {
			$query = $this->_buildSearchTrainingQuery($searchParameters, $isCount);
			$pdo = Doctrine_Manager::connection()->getDbh();
			$res = $pdo->query($query);
			$trainingList = $res->fetchAll();
			if($isCount) {
				return $trainingList[0][count];
			}
			$trainingArray = array();
		    foreach ($trainingList as $training) {
				$row = new TrainingSearchParameters();
				$row->setId($training["id"]);
				$row->setTrainingName($training["topic"]." ");
				$row->setFromDate(set_datepicker_date_format($training["from_Date"]));
				$row->setToDate(set_datepicker_date_format($training["to_Date"]));
				$row->setPublished($training["is_published"] == 1? "Published" : "Unpublished");
				$training = $this->getTrainingById($training["id"]);
				$trainingTrainers = $training->getTrainingTrainer();
				$trainerNamesArray = array();
				foreach($trainingTrainers as $trainingTrainer) {
					array_push($trainerNamesArray, $trainingTrainer->getTrainerFirstAndLastNames());
				}
				$row->setTrainers(implode(', ', $trainerNamesArray));
				$trainingArray[] = $row;
			}
			return $trainingArray; 		
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

	public function getUpcomingTrainings(){
		try{
			$q='SELECT topic,from_date FROM ohrm_training WHERE from_date BETWEEN CURDATE() AND DATE_ADD(CURDATE(), INTERVAL 7 DAY)';
			$pdo = Doctrine_Manager::connection()->getDbh();
			$res = $pdo->query($q);
			$upcomingTrainings = $res->fetchAll();
			return $upcomingTrainings;
		}catch(Exception $e){
			throw new DaoException($e->getMessage());
		}
	}
	
public function _buildSearchTrainingQuery(TrainingSearchParameters $searchParameters, $isCount) {
		
		$query = "SELECT count(*) as CNT, ot.id, ot.topic , MAX(ots.session_date) AS to_Date, MIN(ots.session_date) AS from_Date, GROUP_CONCAT(ott.id) AS trainerIds, ot.is_published ";
		$query .=  " FROM ohrm_training ot LEFT JOIN ohrm_training_trainer ott on ot.id = ott.training_id ";
        $query .=  "LEFT JOIN ohrm_training_attendees ota on ot.id = ota.training_id ";
        $query .= "LEFT JOIN ohrm_training_schedule ots ON ot.id = ots.training_id ";
        $query .=  "WHERE ot.is_deleted = 0  ";
        if(trim($searchParameters->getTrainingName()) != "") {
			$query .=  " AND ot.topic LIKE '%".$searchParameters->getTrainingName()."%' ";
        }
        if(count($searchParameters->getTrainers()) > 0) {
        	$query .=  " AND ott.trainer_id IN (".implode(',', $searchParameters->getTrainers()).") ";
        }
        if(count($searchParameters->getTrainerEmps()) > 0) {
        	$query .=  " AND ott.emp_number IN (".implode(',', $searchParameters->getTrainerEmps()).") ";
        }
        if(count($searchParameters->getAttendees()) > 0) {
        	$query .=  " AND ota.emp_number IN (".implode(',', $searchParameters->getAttendees()).") ";
        }
        if(!$isCount) {
			$query.= " GROUP BY ot.id ";
        }
        if(trim($searchParameters->getFromDate()) != "" && trim($searchParameters->getToDate()) != "" ) {
        	$query .=  " HAVING from_Date >= '".$searchParameters->getFromDate() ."' AND to_Date < '".$searchParameters->getToDate()."' ";
        }
        if(!$isCount) {
        	$query.= " ORDER BY ".$searchParameters->getSortField()." ". $searchParameters->getSortOrder(). " ";
        	$query.= " LIMIT " . $searchParameters->getOffset() . ", " .$searchParameters->getLimit();
        }
        
        
        if($isCount){
        		if (strpos($query, 'HAVING') !== false) { 
        			$str = explode('HAVING',$query);
        			$query = $str[0]." GROUP BY ot.id HAVING ".$str[1];
        		}else{ 
        			$query .= "GROUP BY ot.id";
        		}
        	$query = "SELECT COUNT(countOT.CNT) as count FROM(".$query.") as countOT ";
        } 

        return $query;
	}
	
	public function updateTraining(Training $training) {
		try {
			$q = Doctrine_Query::create()
				->update('Training')
				->set('topic','?', $training->getTopic())
				->set('description','?', $training->getDescription())
				->set('attendee_point','?', $training->getAttendeePoint())
				->set('trainer_point','?', $training->getTrainerPoint())
				->set('totalHours','?', $training->getTotalHours())
				->set('location','?', $training->getLocation())
				->set('is_published','?', $training->getIsPublished())
				->set('updated_by','?', $training->getUpdatedBy())
				->set('updated_date','?', $training->getUpdatedDate())
				->where('id = ?', $training->getId());
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
	
	public function getSubscribedTrainings($empNumber) {
		try {
			$q = Doctrine_Query::create()
				->from('TrainingAttendees')
				->addWhere('emp_number = ?',$empNumber);
			$trnAttendees = $q->execute();
			
			$trainings = array();
			foreach ($trnAttendees as $trnAttendee) {
				array_push($trainings, $trnAttendee->getTrainingId());
			}
			$trainingArray = array();
			foreach ($trainings as $tid) {
				$training = Doctrine :: getTable('Training')->find($tid);
				if($training->getIsPublished() == Training::IS_PUBLISHED && $training->getIsDeleted() == Training::IS_NOT_DELETED) {
					array_push($trainingArray, $training);
				}
			}
			return $trainingArray;
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
	
	public function unregisterTraining($trainingId, $empNumber) {
		try {
			$q = Doctrine_Query::create()
				->delete()
				->from('TrainingAttendees')
				->andWhere('training_id = ?', $trainingId)
				->andWhere('emp_number = ?', $empNumber);
				$q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}

    public function getTrainingListByProperties($properties,$offset,$limit, $count=false) {
		try {
            $q = Doctrine_Query::create()
            	->select('t.id, t.topic, t.location, t.created_date, t.created_by, t.updated_date,t.updated_by, t.is_published, t.is_deleted, t.description, t.total_hours, t.attendee_point, t.trainer_point, MIN(ts.session_date) as from_Date , Max(ts.session_date) as to_Date, ts.topic, ts.description, ts.from_time, ts.to_time')
            	->from('Training t')
            	->leftJoin('t.TrainingSchedule ts');
		    if(isset($properties['filter'])) {
				if($properties['filter'] == 'Upcoming') {
					$q->andWhere('ts.session_date >= ?', date('Y-m-d'));
				} else if($properties['filter'] == 'Registered') {
					$trainings = $this->getSubscribedTrainings($_SESSION['empNumber']);
					$trainingArray = array();
					foreach ($trainings as $training) {
						array_push($trainingArray, $training->getId());
					}
					$q->whereIn('id', $trainingArray);
				} else if($properties['filter'] == 'Completed') {
					$q->having('to_Date < ?', date('Y-m-d'));
				}
			}
			$q->andWhere('is_published = ?', Training::IS_PUBLISHED);
			$q->andWhere('is_deleted = ?', Training::IS_NOT_DELETED);
			$q->groupby('t.id');
			if(!$count){
				$q->orderBy('from_Date DESC');
	            $q->offset($offset);
				$q->limit($limit);
				return $q->execute();
			} else {
				return $q->count();
			}
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
	
	public function getTrainingScheduleById($id) {
		return Doctrine::getTable('TrainingSchedule')->find($id);
	}
	
	public function deleteTrainingSchedules($ids) {
		foreach ($ids as $id) {
			$schedule = $this->getTrainingScheduleById($id);
			if($schedule != null) {
				$schedule->delete();
			}
		}
	}
	
	public function saveAttendance($scheduleId, $empNumbers) {
		try {
			foreach ($empNumbers as $empNumber) {
				$trainingAttendance = new TrainingAttendance();
				$trainingAttendance->setScheduleId($scheduleId);
				$trainingAttendance->setEmpNumber($empNumber);
				$trainingAttendance->save();
			}
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
	
	public function deleteAttendance($scheduleId, $empNumbers) {
		
		try {
			if($empNumbers == 0) {
				var_dump('in delete >> schedule id >> '.$scheduleId.' empnumbers >> '.implode(", ", $empNumbers));
				$q = Doctrine_Query::create()
					->from('TrainingAttendance')
					->where('schedule_id = ?', $scheduleId);
				$results = $q->execute();
				foreach ($results as $result) {
					$result->delete();
				}
			} else {
				foreach ($empNumbers as $empNumber) {
					$q = Doctrine_Query::create()
						->from('TrainingAttendance')
						->where('emp_number = ?',$empNumber)
						->andWhere('schedule_id = ?',$scheduleId);
					$results = $q->execute();
					foreach ($results as $result) {
						$result->delete();
					}
				}
			}
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
	
	public function getExistingAttendance($trainingId) {
		try {
			$training = $this->getTrainingById($trainingId);
			$schedules = $training->getTrainingSchedule();
			$array = array();
			foreach ($schedules as $schedule) {
				$q = Doctrine_Query::create()
					->from('TrainingAttendance')
					->where('schedule_id = ?', $schedule->getId());
				$records = $q->execute();
				
				if(count($records) > 0) {
					$attendeesArray = array();
					$temp['schedule'] = intval($schedule->getId());
					foreach ($records as $record) {
						array_push($attendeesArray, intval($record->getEmpNumber()));
					}
					$temp['attendees'] = $attendeesArray ;
					array_push($array, $temp);
				}
			}
			return $array;
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
	
	public function getTrainingAttendanceByTrainingId($trainingId) {
		try {
			$training = $this->getTrainingById($trainingId);
			$schedules = $training->getTrainingSchedule();
			$scheduleArray = array();
			foreach ($schedules as $schedule) {
				array_push($scheduleArray, $schedule->getId());
			}
			
			$q = Doctrine_Query::create()
				->from('TrainingAttendance')
				->whereIn('schedule_id', $scheduleArray);
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage());
		}
	}
}