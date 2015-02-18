<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 */
class AttendanceDao {

    /**
     * save punchRecord
     * @param AttendanceRecord $attendanceRecord
     * @return AttendanceRecord
     */
    public function savePunchRecord(AttendanceRecord $attendanceRecord) {

        try {
            $attendanceRecord->save();
            return $attendanceRecord;
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * getLastPunchRecord
     * @param $employeeId, $actionableStatesList
     * @return AttendanceRecord
     */
    public function getLastPunchRecord($employeeId, $actionableStatesList) {


        try {
            $query = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhereIn('state', $actionableStatesList);
            $lastReocord = $query->execute();
            if (is_null($lastReocord[0]->getId())) {
                return null;
            } else {
                return $lastReocord[0];
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * checkForPunchOutOverLappingRecords
     * @param $punchInTime,$punchOutTime,$employeeId
     * @return string 1,0
     */
    public function checkForPunchOutOverLappingRecords($punchInTime, $punchOutTime, $employeeId, $recordId) {

        $isValid = "1";

        try {

            $query1 = Doctrine_Query::create()
                    ->from("AttendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime > ?", $punchInTime)
                    ->andWhere("punchInUtcTime < ?", $punchOutTime);
            $records1 = $query1->execute();

         if ((count($records1) == 1) && ($records1[0]->getId() == $recordId)) {
                
            } elseif ((count($records1) > 0)) {

                $isValid = "0";
            }

            $query2 = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchOutUtcTime >= ?", $punchInTime)
                    ->andWhere("punchOutUtcTime < ?", $punchOutTime);
            $records2 = $query2->execute();
         


            $query3 = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime < ?", $punchInTime)
                    ->andWhere("punchOutUtcTime > ?", $punchOutTime);
            $records3 = $query3->execute();

            if ((count($records3) > 0)) {


                $isValid = "0";
            }

            $query4 = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere('punchInUtcTime > ?', $punchInTime)
                    ->andWhere('punchOutUtcTime < ?', $punchOutTime);
            $records4 = $query4->execute();


            if ((count($records4) > 0)) {


                $isValid = "0";
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
        return $isValid;
    }

    /**
     * check For Punch In OverLapping Records
     * @param $punchInTime, $employeeId
     * @return string 1,0
     */
    public function checkForPunchInOverLappingRecords($punchInTime, $employeeId) {
        $isValid = "1";

        try {

            $query1 = Doctrine_Query::create()
                    ->from("AttendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime <= ?", $punchInTime)
                    ->andWhere("punchOutUtcTime > ?", $punchInTime);
            $records1 = $query1->execute();

            if ((count($records1) > 0)) {

                $isValid = "0";
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
        return $isValid;
    }

    /**
     * get Saved Configuration
     * @param $workflow, $state, $role, $action, $resultingState
     * @return boolean
     */
    public function getSavedConfiguration($workflow, $state, $role, $action, $resultingState) {


        try {

            $query = Doctrine_Query::create()
                    ->from("WorkflowStateMachine")
                    ->where("workflow = ?", $workflow)
                    ->andWhere("state = ?", $state)
                    ->andWhere("role = ?", $role)
                    ->andWhere("action = ?", $action)
                    ->andWhere("resultingState = ?", $resultingState);
            $results = $query->execute();

            if ($results[0]->getId() == null) {

                return false;
            } else {
                return true;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * Get Attendance Record
     * @param $$employeeId,$date
     * @return attendance records
     */
    public function getAttendanceRecord($employeeId, $fromDate, $toDate) {

        $from = $fromDate . " " . "00:" . "00:" . "00";
        if(empty($toDate)) {
        	$end = $fromDate . " " . "23:" . "59:" . "59";
        } else {
        	$end = $toDate . " " . "23:" . "59:" . "59";
        }

        try {

            $query = Doctrine_Query::create()
                    ->from("attendanceRecord");
            if(!empty($employeeId)) {
            	if(is_array($employeeId)) {	
            		$query->whereIn("employeeId", $employeeId);
            	} else {
            		$query->where("employeeId = ?", $employeeId);
            	}
            }
            $query->andWhere("punchInUserTime >= ?", $from)
                    ->andWhere("punchInUserTime <= ?", $end);
            $records = $query->execute();
            if (is_null($records[0]->getId())) {

                return null;
            } else {

                return $records;
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
    }

    /**
     * get delete attendance records
     * @param $attendanceRecordId
     * @return boolean
     */
    public function deleteAttendanceRecords($attendanceRecordId) {

        try {
            $q = Doctrine_Query:: create()
                    ->delete('AttendanceRecord')
                    ->where("id = ?", $attendanceRecordId);


            $result = $q->execute();

            if (count($result) > 0) {
                return true;
            } else {
                return false;
            }
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * Get Attendance Record By Id
     * @param $attendanceRecordId
     * @return attendanceRecord
     */
    public function getAttendanceRecordById($attendanceRecordId) {

        try {
            $q = Doctrine_Query:: create()
                    ->from('AttendanceRecord')
                    ->where("id = ?", $attendanceRecordId);

            $result = $q->execute();

            return $result[0];
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }

    /**
     * checkForPunchOutOverLappingRecordsWhenEditing
     * @param $punchInTime,$punchOutTime,$employeeId
     * @return string 1,0
     */
    public function checkForPunchInOutOverLappingRecordsWhenEditing($punchInTime, $punchOutTime, $employeeId, $recordId) {

        $isValid = "1";

        try {

            $query1 = Doctrine_Query::create()
                    ->from("AttendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime <= ?", $punchInTime)
                    ->andWhere("punchOutUtcTime > ?", $punchInTime);
            $records1 = $query1->execute();

            if ((count($records1) == 1) && ($records1[0]->getId() == $recordId)) {
                
            } elseif ((count($records1) > 0)) {

                $isValid = "0";
            }


            $query2 = Doctrine_Query::create()
                    ->from("AttendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime >= ?", $punchInTime)
                    ->andWhere("punchOutUtcTime < ?", $punchOutTime);
            $records2 = $query2->execute();



            if ((count($records2) == 1) && ($records2[0]->getId() == $recordId)) {
                
            } elseif ((count($records2) > 0)) {

                $isValid = "0";
            }

            $query3 = Doctrine_Query::create()
                    ->from("AttendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime > ?", $punchInTime)
                    ->andWhere("punchInUtcTime < ?", $punchOutTime);
            $records3 = $query3->execute();

            if ((count($records3) == 1) && ($records3[0]->getId() == $recordId)) {
                
            } elseif ((count($records3) > 0)) {

                $isValid = "0";
            }

            $query4 = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime < ?", $punchInTime)
                    ->andWhere("punchOutUtcTime > ?", $punchOutTime);
            $records4 = $query4->execute();

            if ((count($records4) == 1) && ($records4[0]->getId() == $recordId)) {
                
            } elseif ((count($records4) > 0)) {

                $isValid = "0";
            }

            $query5 = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere('punchInUtcTime > ?', $punchInTime)
                    ->andWhere('punchOutUtcTime < ?', $punchOutTime);
            $records5 = $query5->execute();


            if ((count($records5) == 1) && ($records5[0]->getId() == $recordId)) {
                
            } elseif ((count($records5) > 0)) {

                $isValid = "0";
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
        return $isValid;
    }

    public function checkForPunchInOverLappingRecordsWhenEditing($punchInTime, $employeeId, $recordId, $punchOutTime) {


        $isValid = "1";


        try {

            $query1 = Doctrine_Query::create()
                    ->from("AttendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime < ?", $punchInTime)
                    ->andWhere("punchOutUtcTime > ?", $punchInTime);
            $records1 = $query1->execute();



            if ((count($records1) == 1) && ($records1[0]->getId() == $recordId)) {
                
            } elseif ((count($records1) > 0)) {

                $isValid = "0";
            }

            $query2 = Doctrine_Query::create()
                    ->from("AttendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime > ?", $punchInTime)
                    ->andWhere("punchOutUtcTime < ?", $punchOutTime);
            $records2 = $query2->execute();


            if ((count($records2) == 1) && ($records2[0]->getId() == $recordId)) {
                
            } elseif ((count($records2) > 0)) {

                $isValid = "0";
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
        return $isValid;
    }

    /**
     * checkForPunchOutOverLappingRecordsWhenEditing
     * @param $punchInTime,$punchOutTime,$employeeId
     * @return string 1,0
     */
    public function checkForPunchOutOverLappingRecordsWhenEditing($punchInTime, $punchOutTime, $employeeId, $recordId) {

        $isValid = "1";


        try {

            $query1 = Doctrine_Query::create()
                    ->from("AttendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime > ?", $punchInTime)
                    ->andWhere("punchInUtcTime < ?", $punchOutTime);
            $records1 = $query1->execute();

            if ((count($records1) == 1) && ($records1[0]->getId() == $recordId)) {
                
            } elseif ((count($records1) > 0)) {

                $isValid = "0";
            }



            $query3 = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere("punchInUtcTime < ?", $punchInTime)
                    ->andWhere("punchOutUtcTime > ?", $punchOutTime);
            $records3 = $query3->execute();

            if ((count($records3) == 1) && ($records3[0]->getId() == $recordId)) {
                
            } elseif ((count($records3) > 0)) {

                $isValid = "0";
            }

            $query4 = Doctrine_Query::create()
                    ->from("attendanceRecord")
                    ->where("employeeId = ?", $employeeId)
                    ->andWhere('punchInUtcTime > ?', $punchInTime)
                    ->andWhere('punchOutUtcTime < ?', $punchOutTime);
            $records4 = $query4->execute();


            if ((count($records4) == 1) && ($records4[0]->getId() == $recordId)) {
                
            } elseif ((count($records4) > 0)) {

                $isValid = "0";
            }
        } catch (Exception $ex) {
            throw new DaoException($ex->getMessage());
        }
        return $isValid;
    }
    
     /**
     *
     * @param int $employeeId
     * @param string $employeementStatus
     * @param int $subDivision    
     * @param date $dateFrom
     * @param date $dateTo
     * @return array 
     */
    
    public function searchAttendanceRecords($employeeIds = null, $employeementStatus = null, $subDivision = null, $dateFrom = null , $dateTo = null ){

         $q = Doctrine_Query::create()
                 ->select("e.emp_number, e.termination_id, e.emp_firstname, e.emp_middle_name, e.emp_lastname, a.punch_in_user_time as in_date_time, a.punch_out_user_time as out_date_time, punch_in_note, punch_out_note, TIMESTAMPDIFF(MINUTE, a.punch_in_user_time, a.punch_out_user_time) as duration")
                ->from("AttendanceRecord a")
                ->leftJoin("a.Employee e")
                ->orderBy('a.punch_in_user_time DESC');

        if( $employeeIds != null){
            
            if(is_array($employeeIds)){
                $q->andWhereIn("e.emp_number", $employeeIds);
            } else {
                $q->andWhere(" e.emp_number = ?", $employeeIds);
            }            
        }
        
        if( $employeementStatus != null){           
            $q->andWhere("e.emp_status = ?", $employeementStatus);
        } else {
            if($employeeIds <= 0){
                $q->andWhere("(e.termination_id IS NULL)");
            }            
        }
        
        if( $subDivision > 0){
            
            $companyService = new CompanyStructureService();
            $subDivisions = $companyService->getCompanyStructureDao()->getSubunitById($subDivision);
           
            $subUnitIds = array($subDivision);
             if (!empty($subDivisions)) {
                $descendents = $subDivisions->getNode()->getDescendants();
               
                foreach($descendents as $descendent) {                
                    $subUnitIds[] = $descendent->id;
                }
            }
            
            $q->andWhereIn("e.work_station", $subUnitIds);            
        }
        
        if( $dateFrom != null){            
            $q->andWhere("a.punch_in_user_time >=?", $dateFrom);
        }
        
        if( $dateTo != null){
            $q->andWhere("a.punch_out_user_time <=?", $dateTo);
        }
      
        $result = $q->execute(array(), Doctrine::HYDRATE_SCALAR);
        return $result;
      
    }

    public function getAllAttendanceRecordsByDate($date){
    	$date = date('Y-m-d', strtotime($date));
    	$from = $date . " " . "00:" . "00:" . "00";
    	$to = $date. " " . "23:" . "59:" . "59";
    	$q = Doctrine_Query::create()
    		->from("AttendanceRecord")
    		->where('punch_in_user_time >= ?', $from)
    		->andWhere('punch_out_user_time <= ?', $to);
    	return $q->execute();
    }
    
    public function getAttendanceRecordsBetweenDays($from, $to){
    	$q = Doctrine_Query::create()
	    	->from("AttendanceRecord")
	    	->where('punch_in_user_time >= ?', $from . " " . "00:" . "00:" . "00")
	    	->andWhere('punch_out_user_time <= ?', $to. " " . "23:" . "59:" . "59")
    		->orderBy('employeeId');
    	return $q->execute();
    }
    
 	/**
     * delete attendance records of particular date
     * @param DATE $date
     * @return boolean
     */
    public function deleteAttendanceRecordsByDate($date) {
		$date = date('Y-m-d', strtotime($date));
    	$from = $date . " " . "00:" . "00:" . "00";
    	$to = $date. " " . "23:" . "59:" . "59";
    	
        try {
            $q = Doctrine_Query:: create()
                    ->delete('AttendanceRecord')
                    ->where('punch_in_user_time >= ?', $from)
                    ->andWhere('punch_out_user_time <= ?', $to);
            $result = $q->execute();
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
    
	/**
     * get attendance records by particular date 
     * @param DATE $date
     * @param employee
     * @param ARRAY $accessibleEmployees
     * @return boolean
     */
    public function getAttendanceRecordsByEmployeeAndDate($date, $employee, $accessibleEmployees, $limit, $offset, $isCount) {
		$date = date('Y-m-d', strtotime($date));
    	$from = $date . " " . "00:" . "00:" . "00";
    	$to = $date. " " . "23:" . "59:" . "59";

        try {
        	$q = Doctrine_Query:: create()
                    ->from('Employee e')
                    ->leftJoin('e.AttendanceRecord atr')
                    ->leftJoin('e.locations loc')
                    ->andWhere('e.employee_id is not NULL')
                    ->andWhere('e.employee_id <> ?', '')
                    ->andWhere('((atr.punch_in_user_time >= ? AND atr.punch_out_user_time <= ?) OR atr.punch_out_user_time is NULL)', array($from, $to))
                    ->andWhere('loc.id = ?', Location::LOCATION_ACTIVE_FOR_ATTENDANCE_SYSTEM)
                    ->andWhere('e.termination_id is NULL');
             if($accessibleEmployees != NULL) {
             	$q->andWhereIn('e.emp_number', $accessibleEmployees);
             }
             if($employee != NULL) {
                $q->andWhere('e.emp_number = ?', $employee);
             }
             if(!$isCount) {
                $q->orderBy('e.emp_firstname','asc');
	            $q->limit($limit);
	            $q->offset($offset);
             }
            $results= $q->execute();
            $attendanceArray = array();
			foreach ($results as $res) {
				$attendanceList = $res->getAttendanceRecord();
				if(count($attendanceList) > 0) {
					foreach ($attendanceList as $att) {
						array_push($attendanceArray, $att);
					}
				} else {
					$attendanceRecord = new AttendanceRecord();
					$attendanceRecord->setEmployee($res);
					array_push($attendanceArray, $attendanceRecord);
				}
			}
			if($isCount) {
				return count($attendanceArray);
			} else {
				return $attendanceArray;
			}
        } catch (Exception $e) {
            throw new DaoException($e->getMessage());
        }
    }
}
