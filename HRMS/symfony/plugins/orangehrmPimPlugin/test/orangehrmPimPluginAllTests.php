<?php
require_once 'config/ProjectConfiguration.class.php';
define( 'SF_APP_NAME', 'orangehrm' );
define( 'SF_ENV', 'test' );
define( 'SF_CONN', 'doctrine' );
AllTests::$configuration = ProjectConfiguration::getApplicationConfiguration( SF_APP_NAME , SF_ENV, true);
sfContext::createInstance(AllTests::$configuration);

class orangehrmPimPluginAllTests {

    protected function setUp() {

    }

    public static function suite() {

        $suite = new PHPUnit_Framework_TestSuite('orangehrmPimPluginAllTest');

        /* Dao Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/EmployeeDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/ReportingMethodConfigurationDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/TerminationReasonConfigurationDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/EmployeeListDaoTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/CustomFieldConfigurationDaoTest.php');
        

        /* Service Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/service/EmployeeServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/EmployeeYearsOfServiceTest.php');
        //$suite->addTestFile(dirname(__FILE__) . '/model/service/CustomFieldConfigurationServiceTest.php');
        
        /* ParameterHolder Test Cases */
        $suite->addTestFile(dirname(__FILE__) . '/model/parameterholder/EmployeeSearchParameterHolderTest.php');        

        return $suite;

    }

    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

}

if (PHPUnit_MAIN_METHOD == 'orangehrmPimPluginAllTests::main') {
    orangehrmPimPluginAllTests::main();
}

?>
