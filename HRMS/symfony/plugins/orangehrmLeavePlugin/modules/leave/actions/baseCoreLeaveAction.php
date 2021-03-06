<?php

abstract class baseCoreLeaveAction extends sfAction {

    public $form;
    protected $workWeekService;
    protected $leaveEntitlementService;
    protected $leaveTypeService;
    private $leaveRequestService;
    private $leavePeriodService;
    private $leaveCompOffService;

    /**
     *
     * @return WorkWeekService
     */
    protected function getWorkWeekService() {
        if (!($this->workWeekService instanceof WorkWeekService)) {
            $this->workWeekService = new WorkWeekService();
        }
        return $this->workWeekService;
    }

    /**
     *
     * @param WorkWeekService $service 
     */
    protected function setWorkWeekService(WorkWeekService $service) {
        $this->workWeekService = $service;
    }

    /**
     *
     * @return OldLeaveEntitlementService
     */
    protected function getLeaveEntitlementService() {
        if (!($this->leaveEntitlementService instanceof OldLeaveEntitlementService)) {
            $this->leaveEntitlementService = new OldLeaveEntitlementService();
        }
        return $this->leaveEntitlementService;
    }

    /**
     *
     * @param OldLeaveEntitlementService $service 
     */
    protected function setLeaveEntitlementService(OldLeaveEntitlementService $service) {
        $this->leaveEntitlementService = $service;
    }

    /**
     *
     * @return LeaveTypeService
     */
    protected function getLeaveTypeService() {
        if (!($this->leaveTypeService instanceof LeaveTypeService)) {
            $this->leaveTypeService = new LeaveTypeService();
        }
        return $this->leaveTypeService;
    }

    /**
     *
     * @param LeaveTypeService $service 
     */
    protected function setLeaveTypeService(LeaveTypeService $service) {
        $this->leaveTypeService = $service;
    }

    /**
     * Returns Leave Period
     * @return LeavePeriodService
     */
    public function getLeavePeriodService() {

        if (is_null($this->leavePeriodService)) {
            $leavePeriodService = new LeavePeriodService();
            $leavePeriodService->setLeavePeriodDao(new LeavePeriodDao());
            $this->leavePeriodService = $leavePeriodService;
        }

        return $this->leavePeriodService;
    }

    /**
     *
     * @return LeaveRequestService
     */
    public function getLeaveCompOffService() {
        if (is_null($this->leaveCompOffService)) {
            $leaveCompOffService = new LeaveCompOffService();
            $leaveCompOffService->setLeaveCompOffDao(new LeaveCompOffDao());
            $this->leaveCompOffService = $leaveCompOffService;
        }

        return $this->leaveCompOffService;
    }

    /**
     *
     * @param LeaveRequestService $leaveRequestService
     * @return void
     */
    public function setLeaveCompOffService(LeaveCompOffService $leaveCompOffService) {
        $this->leaveCompOffService = $leaveCompOffService;
    }
    
      /**
     *
     * @return LeaveRequestService
     */
    public function getLeaveRequestService() {
        if (is_null($this->leaveRequestService)) {
            $leaveRequestService = new LeaveRequestService();
            $leaveRequestService->setLeaveRequestDao(new LeaveRequestDao());
            $this->leaveRequestService = $leaveRequestService;
        }

        return $this->leaveRequestService;
    }

    /**
     *
     * @param LeaveRequestService $leaveRequestService
     * @return void
     */
    public function setLeaveRequestService(LeaveRequestService $leaveRequestService) {
        $this->leaveRequestService = $leaveRequestService;
    }

    /**
     *
     * @param sfForm $form
     * @return
     */
    protected function setForm(sfForm $form) {
        if (is_null($this->form)) {
            $this->form = $form;
        }
    }

    /**
     * Returns Logged in user details
     * @return array
     * @todo Refactor this method to use auth classes instead of directly accessing the session
     */
    protected function getLoggedInUserDetails() {
        $userDetails['userType'] = 'ESS';

        /* Value 0 is assigned for default admin */
        $userDetails['loggedUserId'] = (empty($_SESSION['empNumber'])) ? 0 : $_SESSION['empNumber'];
        $userDetails['empId'] = (empty($_SESSION['empID'])) ? 0 : $_SESSION['empID'];

        if ($_SESSION['isSupervisor']) {
            $userDetails['userType'] = 'Supervisor';
        }

        if (isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') {
            $userDetails['userType'] = 'Admin';
        }

        return $userDetails;
    }

}