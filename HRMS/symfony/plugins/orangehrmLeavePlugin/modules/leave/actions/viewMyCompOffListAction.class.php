<?php

/**

 */
class viewMyCompOffListAction extends viewCompOffListAction {    
    
    protected function getMode() {
       
        $mode = CompOffLeaveListForm::MODE_MY_COMPOFF_LIST;
        return $mode;
    }
    
    protected function isEssMode() {       
        return true;
    }

}