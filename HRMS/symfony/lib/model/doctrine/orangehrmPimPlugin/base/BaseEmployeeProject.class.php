<?php

/**
 * BaseEmployeeProject
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property integer $empNumber
 * @property integer $projectId
 * @property integer $created_by
 * @property datetime $created_date
 * @property Doctrine_Collection $Employee
 * @property Doctrine_Collection $Project
 * 
 * @method integer             getId()           Returns the current record's "id" value
 * @method integer             getEmpNumber()    Returns the current record's "empNumber" value
 * @method integer             getProjectId()    Returns the current record's "projectId" value
 * @method integer             getCreatedBy()    Returns the current record's "created_by" value
 * @method datetime            getCreatedDate()  Returns the current record's "created_date" value
 * @method Employee			   getEmployee()     Returns the current record's "Employee" value
 * @method Project 			   getProject()      Returns the current record's "Project" value
 * @method EmployeeProject     setId()           Sets the current record's "id" value
 * @method EmployeeProject     setEmpNumber()    Sets the current record's "empNumber" value
 * @method EmployeeProject     setProjectId()    Sets the current record's "projectId" value
 * @method EmployeeProject     setCreatedBy()    Sets the current record's "created_by" value
 * @method EmployeeProject     setCreatedDate()  Sets the current record's "created_date" value
 * @method EmployeeProject     setEmployee()     Sets the current record's "Employee" collection
 * @method EmployeeProject     setProject()      Sets the current record's "Project" collection
 * 
 * @package    orangehrm
 * @subpackage model\pim\base
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseEmployeeProject extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('hs_hr_emp_project');
        $this->hasColumn('id', 'integer', null, array(
             'type' => 'integer',
             'primary' => true,
             'autoincrement' => true,
             ));
        $this->hasColumn('emp_number as empNumber', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
        $this->hasColumn('customer_id as customerId', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
        $this->hasColumn('project_id as projectId', 'integer', null, array(
             'type' => 'integer',
             'notnull' => false,
             ));
        $this->hasColumn('created_by', 'integer', null, array(
             'type' => 'integer',
             ));
        $this->hasColumn('created_date', 'datetime', null, array(
             'type' => 'datetime',
             ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('Employee', array(
             'local' => 'empNumber',
             'foreign' => 'empNumber'));

        $this->hasOne('Project', array(
             'local' => 'projectId',
             'foreign' => 'projectId'));
             
        $this->hasOne('Customer', array(
             'local' => 'customerId',
             'foreign' => 'customerId'));
    }
}