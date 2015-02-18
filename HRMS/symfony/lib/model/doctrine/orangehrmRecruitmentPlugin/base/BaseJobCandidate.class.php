<?php

/**
 * BaseJobCandidate
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @property integer $id
 * @property string $firstName
 * @property string $middleName
 * @property string $lastName
 * @property string $email
 * @property string $contactNumber
 * @property integer $status
 * @property string $comment
 * @property integer $modeOfApplication
 * @property date $dateOfApplication
 * @property string $cvTextVersion
 * @property string $keywords
 * @property integer $addedPerson
 * @property Doctrine_Collection $JobCandidateVacancy
 * @property JobCandidateAttachment $JobCandidateAttachment
 * @property Doctrine_Collection $CandidateHistory
 * @property Employee $Employee
 * @property Doctrine_Collection $JobInterview
 * @property float $current_ctc
 * @property float $expected_ctc
 * @property integer $notice_period
 * @property Date $expected_doj
 * @property string $original_location
 * @property string $visa_status
 * 
 * @method integer                getId()                     Returns the current record's "id" value
 * @method string                 getFirstName()              Returns the current record's "firstName" value
 * @method string                 getMiddleName()             Returns the current record's "middleName" value
 * @method string                 getLastName()               Returns the current record's "lastName" value
 * @method string                 getEmail()                  Returns the current record's "email" value
 * @method string                 getContactNumber()          Returns the current record's "contactNumber" value
 * @method integer                getStatus()                 Returns the current record's "status" value
 * @method string                 getComment()                Returns the current record's "comment" value
 * @method integer                getModeOfApplication()      Returns the current record's "modeOfApplication" value
 * @method date                   getDateOfApplication()      Returns the current record's "dateOfApplication" value
 * @method string                 getCvTextVersion()          Returns the current record's "cvTextVersion" value
 * @method string                 getKeywords()               Returns the current record's "keywords" value
 * @method integer                getAddedPerson()            Returns the current record's "addedPerson" value
 * @method Doctrine_Collection    getJobCandidateVacancy()    Returns the current record's "JobCandidateVacancy" collection
 * @method JobCandidateAttachment getJobCandidateAttachment() Returns the current record's "JobCandidateAttachment" value
 * @method Doctrine_Collection    getCandidateHistory()       Returns the current record's "CandidateHistory" collection
 * @method Employee               getEmployee()               Returns the current record's "Employee" value
 * @method Doctrine_Collection    getJobInterview()           Returns the current record's "JobInterview" collection
 * @method double                 getCurrentCtc()             Returns the current record's "CurrentCtc" value
 * @method double                 getExpectedCtc()            Returns the current record's "ExpectedCtc" value
 * @method integer                getNoticePeriod()           Returns the current record's "NoticePeriod" value
 * @method date                   getExpectedDoj()            Returns the current record's "ExpectedDoj" value
 * @method string                 getOriginalLocation()       Returns the current record's "OriginalLocation" value
 * @method integer                getVisaStatus()             Returns the current record's "VisaStatus" value
 * @method JobCandidate           setId()                     Sets the current record's "id" value
 * @method JobCandidate           setFirstName()              Sets the current record's "firstName" value
 * @method JobCandidate           setMiddleName()             Sets the current record's "middleName" value
 * @method JobCandidate           setLastName()               Sets the current record's "lastName" value
 * @method JobCandidate           setEmail()                  Sets the current record's "email" value
 * @method JobCandidate           setContactNumber()          Sets the current record's "contactNumber" value
 * @method JobCandidate           setStatus()                 Sets the current record's "status" value
 * @method JobCandidate           setComment()                Sets the current record's "comment" value
 * @method JobCandidate           setModeOfApplication()      Sets the current record's "modeOfApplication" value
 * @method JobCandidate           setDateOfApplication()      Sets the current record's "dateOfApplication" value
 * @method JobCandidate           setCvTextVersion()          Sets the current record's "cvTextVersion" value
 * @method JobCandidate           setKeywords()               Sets the current record's "keywords" value
 * @method JobCandidate           setAddedPerson()            Sets the current record's "addedPerson" value
 * @method JobCandidate           setJobCandidateVacancy()    Sets the current record's "JobCandidateVacancy" collection
 * @method JobCandidate           setJobCandidateAttachment() Sets the current record's "JobCandidateAttachment" value
 * @method JobCandidate           setCandidateHistory()       Sets the current record's "CandidateHistory" collection
 * @method JobCandidate           setEmployee()               Sets the current record's "Employee" value
 * @method JobCandidate           setJobInterview()           Sets the current record's "JobInterview" collection
 * @method double                 setCurrentCtc()             Sets the current record's "CurrentCtc" value
 * @method double                 setExpectedCtc()            Sets the current record's "ExpectedCtc" value
 * @method integer                setNoticePeriod()           Sets the current record's "NoticePeriod" value
 * @method date                   setExpectedDoj()            Sets the current record's "ExpectedDoj" value
 * @method string                 setOriginalLocation()       Sets the current record's "OriginalLocation" value
 * @method string                setVisaStatus()             Sets the current record's "VisaStatus" value
 * 
 * @package    orangehrm
 * @subpackage model\recruitment\base
 * @author     Your name here
 * @version    SVN: $Id: Builder.php 7490 2010-03-29 19:53:27Z jwage $
 */
abstract class BaseJobCandidate extends sfDoctrineRecord
{
    public function setTableDefinition()
    {
        $this->setTableName('ohrm_job_candidate');
        $this->hasColumn('id', 'integer', 13, array(
             'type' => 'integer',
             'primary' => true,
             'length' => 13,
             ));
        $this->hasColumn('first_name as firstName', 'string', 30, array(
             'type' => 'string',
             'default' => '',
             'length' => 30,
             ));
        $this->hasColumn('middle_name as middleName', 'string', 30, array(
             'type' => 'string',
             'length' => 30,
             'default' => '',
             ));
        $this->hasColumn('last_name as lastName', 'string', 30, array(
             'type' => 'string',
             'length' => 30,
             'default' => '',
             ));
        $this->hasColumn('email', 'string', 100, array(
             'type' => 'string',
             'default' => '',
             'length' => 100,
             ));
        $this->hasColumn('contact_number as contactNumber', 'string', 30, array(
             'type' => 'string',
             'default' => '',
             'length' => 30,
             ));
        $this->hasColumn('status', 'integer', 4, array(
             'type' => 'integer',
             'default' => '0',
             'length' => 4,
             ));
        $this->hasColumn('comment', 'string', 2147483647, array(
             'type' => 'string',
             'default' => '',
             'length' => 2147483647,
             ));
        $this->hasColumn('mode_of_application as modeOfApplication', 'integer', 4, array(
             'type' => 'integer',
             'default' => '0',
             'length' => 4,
             ));
        $this->hasColumn('date_of_application as dateOfApplication', 'date', 25, array(
             'type' => 'date',
             'length' => 25,
             ));
        $this->hasColumn('cv_text_version as cvTextVersion', 'string', 2147483647, array(
             'type' => 'string',
             'length' => 2147483647,
             ));
        $this->hasColumn('keywords', 'string', 255, array(
             'type' => 'string',
             'default' => '',
             'length' => 255,
             ));
        $this->hasColumn('added_person as addedPerson', 'integer', null, array(
             'type' => 'integer',
             'default' => '0'
             ));           
       $this->hasColumn('current_ctc as currentCtc', 'double', null, array(
             'type' => 'double',
             'default' => '0',
             ));
        $this->hasColumn('expected_ctc as expectedCtc', 'double', null, array(
             'type' => 'double',
             'default' => '0',
             ));
        $this->hasColumn('notice_period as noticePeriod', 'integer', 4, array(
             'type' => 'integer',
             'default' => '0',
             'length' => 4,
             ));
        $this->hasColumn('expected_doj as expectedDoj', 'date', 25, array(
             'type' => 'date',
        	 'length' => 25,
             ));
        $this->hasColumn('original_location as originalLocation', 'string', 200, array(
             'type' => 'string',
             'default' => '',
        	 'length' => 200,
             ));
        $this->hasColumn('visa_status as visaStatus', 'string', 100, array(
             'type' => 'string',
             'default' => '',
             'length' => 100,
             ));
        $this->hasColumn('microresume as microResume', 'string', null, array(
	        'type' => 'string',
			'default' => '',
        ));
        $this->hasColumn('preferred_location as preferredLocation', 'string', 200, array(
             'type' => 'string',
        	 'length' => 200,
        	 'default' => '',
             ));
        $this->hasColumn('education_detail_degree as educationDetailDegree', 'string', 100, array(
             'type' => 'string',
        	 'length' => 100,
        	 'default' => '',
             ));    
        $this->hasColumn('education_detail_spec as educationDetailSpec', 'string', 100, array(
             'type' => 'string',
        	 'length' => 100,
        	 'default' => '',
             ));
        $this->hasColumn('education_detail_perc as educationDetailPerc', 'double', null, array(
             'type' => 'double',
             'default' => '0',
        	 ));
        $this->hasColumn('total_experience as totalExperience', 'double', null, array(
             'type' => 'double',
             'default' => '0',
             ));
        $this->hasColumn('relevant_experience as relevantExperience', 'double', null, array(
             'type' => 'double',
             'default' => '0',
             ));
        $this->hasColumn('current_company as currentCompany', 'string', 50, array(
             'type' => 'string',
             'default' => '',
        	 'length' => 50,
             ));
        $this->hasColumn('designation', 'string', 50, array(
             'type' => 'string',
             'default' => '',
        	 'length' => 50,
             ));
        $this->hasColumn('stability', 'string', 10, array(
             'type' => 'string',
        	 'length' => 10,
             ));
        $this->hasColumn('com_skills as communicationSkills', 'string', 200, array(
             'type' => 'string',
             'default' => '',
        	 'length' => 200,
             ));
        $this->hasColumn('key_skills as keySkills', 'string', 200, array(
             'type' => 'string',
             'default' => '',
        	 'length' => 200,
             ));
        $this->hasColumn('project_details as projectDetails', 'string', null, array(
             'type' => 'string',
             'default' => '',
             ));
        $this->hasColumn('employment_type as employmentType', 'string', 15, array(
             'type' => 'string',
        	 'length' => 15,
             ));
        $this->hasColumn('work_gap as workGap', 'double', null, array(
             'type' => 'double',
             'default' => '0',
             ));
        $this->hasColumn('edu_gap as educationGap', 'double', null, array(
             'type' => 'double',
             'default' => '0',
             ));
        $this->hasColumn('alternate_email as alternateEmail', 'string', 100, array(
             'type' => 'string',
             'length' => 100,
             ));
		$this->hasColumn('alternate_number as alternateNumber', 'string', 30, array(
             'type' => 'string',
             'length' => 30,
             ));
	    $this->hasColumn('is_deleted as isDeleted', 'integer', 1, array(
	    		'type' => 'integer',
	    		'length' => 1,
	    ));
    }

    public function setUp()
    {
        parent::setUp();
        $this->hasOne('JobCandidateVacancy', array(
             'local' => 'id',
             'foreign' => 'candidateId'));

        $this->hasOne('JobCandidateAttachment', array(
             'local' => 'id',
             'foreign' => 'candidateId'));

        $this->hasMany('CandidateHistory', array(
             'local' => 'id',
             'foreign' => 'candidateId'));

        $this->hasOne('Employee', array(
             'local' => 'addedPerson',
             'foreign' => 'empNumber'));

        $this->hasMany('JobInterview', array(
             'local' => 'id',
             'foreign' => 'candidateId'));
    }
}