<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of viewMyAttendanceRecordAction
 *
 * @author orangehrm
 */
class viewMyAttendanceRecordAction extends sfAction {

    public function execute($request) {

        $this->attendanceService = $this->getAttendanceService();
        $userObj = $this->getContext()->getUser()->getAttribute("user");
        $this->employeeId = $userObj->getEmployeeNumber();
        $this->date = $this->request->getParameter('date');
        $this->trigger = $request->getParameter('trigger');
        $this->actionRecorder="viewMy";
        $values = array('date' => $this->date, 'employeeId' => $this->employeeId, 'trigger' => $this->trigger);
        $this->form = new AttendanceRecordSearchForm(array(), $values);

        if (!($this->trigger)) {
            if ($request->isMethod('post')) {

                $this->form->bind($request->getParameter('attendance'));


                if ($this->form->isValid()) {
                    
                }
            } else  {
            	$this->updateOnLoad = true;
            	$yesterdayDate = date('Y-m-d', strtotime(date( "Y-m-d"). "-1 days"));
                $this->form->setDefault('dateRange', array('from' => set_datepicker_date_format($yesterdayDate), 'to' => set_datepicker_date_format($yesterdayDate)));
            }
        }
    }

    public function getAttendanceService() {

        if (is_null($this->attendanceService)) {

            $this->attendanceService = new AttendanceService();
        }

        return $this->attendanceService;
    }

    public function setAttendanceService(AttendanceService $attendanceService) {

        $this->attendanceService = $attendanceService;
    }

}

?>
