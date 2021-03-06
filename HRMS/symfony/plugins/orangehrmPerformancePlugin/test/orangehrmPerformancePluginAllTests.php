<?php
require_once 'config/ProjectConfiguration.class.php';
define( 'SF_APP_NAME', 'orangehrm' );
define( 'SF_ENV', 'test' );
define( 'SF_CONN', 'doctrine' );
AllTests::$configuration = ProjectConfiguration::getApplicationConfiguration( SF_APP_NAME , SF_ENV, true);
sfContext::createInstance(AllTests::$configuration);

class orangehrmPerformancePluginAllTests {

    protected function setUp() {

    }

    public static function suite() {
        
        $suite = new PHPUnit_Framework_TestSuite('orangehrmPerformancePluginTest');        
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/KpiDaoTest.php');       
        $suite->addTestFile(dirname(__FILE__) . '/model/dao/PerforamanceReviewDaoTest.php');
        //$suite->addTestFile(dirname(__FILE__) . '/model/reviewerGroup/ReviewerGroupFactoryTest.php');
        //$suite->addTestFile(dirname(__FILE__) . '/model/status/ReviewStatusFactoryTest.php');
        //$suite->addTestFile(dirname(__FILE__) . '/model/status/ReviewStatusTest.php');
        //$suite->addTestFile(dirname(__FILE__) . '/model/reviewerGroup/ReviewerGroupTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/KpiServiceTest.php');
        $suite->addTestFile(dirname(__FILE__) . '/model/service/PerforamanceReviewServiceTest.php');
        
        return $suite;
    }

    public static function main() {
        PHPUnit_TextUI_TestRunner::run(self::suite());
    }

}

if (PHPUnit_MAIN_METHOD == 'orangehrmPerformancePluginTest::main') {
    orangehrmPimPluginAllTests::main();
}

?>
