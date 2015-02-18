<?php
require_once 'config/ProjectConfiguration.class.php';
define( 'SF_APP_NAME', 'orangehrm' );
define( 'SF_ENV', 'test' );
define( 'SF_CONN', 'doctrine' );
AllTests::$configuration = ProjectConfiguration::getApplicationConfiguration( SF_APP_NAME , SF_ENV, true);
sfContext::createInstance(AllTests::$configuration);

class orangehrmRecruitmentPluginAllTests {

    protected function setUp() {
        
    }

    public static function suite() {

        $suite = new PHPUnit_Framework_TestSuite('orangehrmRecruitmentPluginAllTest');

        /* Dao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/CandidateDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/VacancyDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/RecruitmentAttachmentDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/JobInterviewDaoTest.php');

        /* Service Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/service/CandidateServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/VacancyServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/RecruitmentAttachmentServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/JobInterviewServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/CandidateHistoryServiceTest.php');



        return $suite;
    }

    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

}

if (PHPUnit_MAIN_METHOD == 'orangehrmRecruitmentPluginAllTests::main') {
    orangehrmRecruitmentPluginAllTests::main();
}
?>
