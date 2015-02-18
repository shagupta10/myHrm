<?php

/**
 * PluginSystemUser
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    ##PACKAGE##
 * @subpackage ##SUBPACKAGE##
 * @author     ##NAME## <##EMAIL##>
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class PluginSystemUser extends BaseSystemUser
{
    
    const NO_OF_RECORDS_PER_PAGE    =   10 ;
    const ADMIN_USER_ROLE_ID        =   1;
    const CONSULTANT_USER_ROLE_ID   = 8;
    const RECRUITMENT_MANAGER_ROLE_ID = 9;
    const ENABLED = 1;
    const DISABLED = 0;
    const DELETED = 1;
    const UNDELETED = 0;
    const USER_TYPE_ADMIN = "Admin"; // TODO: Check the needness when new user roles are implemented
    const USER_TYPE_EMPLOYEE = "Employee"; // TODO: Check the needness when new user roles are implemented
    const USER_TYPE_SUPERVISOR = "Supervisor"; // TODO: Check the needness when new user roles are implemented   
    
    /**
     * Get text status according system user status
     * 
     * @return String
     */
    public function getTextStatus(){
        if( $this->getStatus() == '1'){
            return 'Enabled';
        }else{
            return 'Disabled';
        }
    }
    
    public function getIsAdmin(){
        if( $this->getUserRoleId() == SystemUser::ADMIN_USER_ROLE_ID){
            return 'Yes';
        }else{
            return 'No';
        }
    }
    
    public function getIsConsultant(){
    	 if( $this->getUserRoleId() == SystemUser::CONSULTANT_USER_ROLE_ID){
            return true;
        }else{
            return false;
        }
    }
    
    public function getIsRecruitmentManager(){
	    if( $this->getUserRoleId() == SystemUser::RECRUITMENT_MANAGER_ROLE_ID){
		    return true;
	    }else{
		    return false;
	    }
    }
    
    public function getUsergId(){
        if( $this->getUserRoleId() == SystemUser::ADMIN_USER_ROLE_ID){
             return 'USG001';
        }else{
            return null;
        }
      
    }
    
    public function getName(){
        if( $this->getEmployee()->getEmpFirstname() != ''){
            return $this->getEmployee()->getEmpFirstname();
        }else{
            return $this->getUserRole()->getName();
        }
            
           
    }

}