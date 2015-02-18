<?php
   // require_once 'PHPUnit/Framework.php';
    
    define( 'SF_APP_NAME', 'orangehrm' );
    define( 'SF_ENV', 'test' );
    define( 'SF_CONN', 'doctrine' );
    
    if ( SF_APP_NAME != '' )
    {
        require_once(dirname(__FILE__).'/../config/ProjectConfiguration.class.php');
        AllTests::$configuration = ProjectConfiguration::getApplicationConfiguration( SF_APP_NAME , SF_ENV, true);
        sfContext::createInstance(AllTests::$configuration);
    }
        
    class AllTests
    {
        public static $configuration = null;
        public static $databaseManager = null;
        public static $connection = null;
    
        protected function setUp()
        {
    
            if ( self::$configuration )
            {
                // initialize database manager
                self::$databaseManager = new sfDatabaseManager(self::$configuration);
                self::$databaseManager->loadConfiguration();
                
                if ( SF_CONN ) self::$connection = self::$databaseManager->getDatabase( SF_CONN );
            }
               
        }
    
        public static function suite()
        {
            $suite = new PHPUnit_Framework_TestSuite('PHPUnit');
    
            $dir = new DirectoryIterator( dirname(__FILE__). '\model' );
            
            while($dir->valid()) {
                if( strpos( $dir, 'Test.php' ) !== false ) {
                    $suite->addTestFile(  dirname(__FILE__). '\model\\'. $dir );
                    
                }
                $dir->next();
            }        
            
            return $suite;
        }
    }