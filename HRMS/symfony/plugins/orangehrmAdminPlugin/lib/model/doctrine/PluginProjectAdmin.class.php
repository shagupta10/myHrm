<?php

/**
 * PluginProjectAdmin class file
 */

/**
 * PluginProjectAdmin
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    orangehrm
 * @subpackage model\admin\plugin
 */
abstract class PluginProjectAdmin extends BaseProjectAdmin
{
	
	const PROJECT_ADMIN = 1;
    const PROJECT_US_ACCOUNT_OWNER = 2;
    const PROJECT_INDIA_ACCOUNT_OWNER = 3;
    const PROJECT_POINT_OF_ESCALATION = 4;
    const PROJECT_TEAM_MEMBER = 5;
    
    
    public static $projectAdminTypes = array(
        self::PROJECT_ADMIN => 'Project Admin',
        self::PROJECT_US_ACCOUNT_OWNER => 'US Account Owner',
        self::PROJECT_INDIA_ACCOUNT_OWNER => 'India Account Owner',
        self::PROJECT_POINT_OF_ESCALATION => 'Point Of Escalation'
    );
	
}