<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of KpiDaoTest
 *
 * @author sujata
 */

/**
 * @group performance
 */

require_once sfConfig::get('sf_test_dir') . '/util/TestDataService.php';

class KpiDaoTest extends PHPUnit_Framework_TestCase {

    protected function setUp() {
        TestDataService::truncateTables(array('DefineKpi'));
        TestDataService::populate(sfConfig::get('sf_plugins_dir') . '/orangehrmPerformancePlugin/test/fixtures/kpi.yml');
    }
    
    public function testSaveKpi() {

        $dao = new KpiDao();

        $kpi = new DefineKpi();
        $kpi->setJobtitlecode(1);
        $kpi->setId(1);
        $kpi->setKpiTitle('new kpi');
        $kpi->setMax(1);
        $kpi->setMin(2);
        $kpi->setDefault(1);

        $kpi = $dao->saveKpi($kpi);
        $this->assertEquals(1, $kpi->getId());
    }
    
    public function testReadKpi1() {
    
    	$dao = new KpiDao();
    	$kpis = $dao->readKpi(2);
    	$this->assertEquals(1, sizeof($kpis));
    }
    public function testReadKpi2() {
    
    	$dao = new KpiDao();
    	$kpis = $dao->readKpi(7);
    	$this->assertEquals(1, sizeof($kpis));
    }
    
    public function testGetKpiList1() {

        $dao = new KpiDao();
        $kpis = $dao->getKpiList();
        $this->assertEquals(3, sizeof($kpis));
    }
    
    public function testGetKpiList2() {

        $dao = new KpiDao();
        $kpis = $dao->getKpiList('','',array('jobCode' => 1));
        $this->assertEquals(count($kpis), sizeof(array('jobCode' => 1)));
    }
    
    public function testDeleteKpi() {

        $dao = new KpiDao();
        $this->assertTrue($dao->deleteKpi(array("1")));
    }
    
    public function testDeleteKpiForJobTitle() {
    
    	$dao = new KpiDao();
    	$this->assertTrue($dao->deleteKpiForJobTitle(3));
    }
    
    public function testGetKpiList3() {

        $dao = new KpiDao();
        $kpis = $dao->getKpiList('','',array('id' => 2));
        $this->assertEquals($kpis['id'], 2);
    }
    
    public function testGetKpiList4() {

        $dao = new KpiDao();
        $kpis = $dao->getKpiList('','',array('isDefault' => 1));
        $this->assertEquals(count($kpis), 1);
    }
    
    public function testGetKpiForJobTitle() {

        $dao = new KpiDao();
        $kpis = $dao->getKpiForJobTitle(2);
        $this->assertEquals(count($kpis), 1);
    }
	
    public function testGetCountKpiList() {
    
    	$dao = new KpiDao();
    	$kpis = $dao->getCountKpiList();
    	$this->assertEquals(3, $kpis);
    }
    public function testGetKpiDefaultRate() {
    
    	$dao = new KpiDao();
    	$kpis = $dao->getKpiDefaultRate();
    	$this->assertEquals(1, count($kpis));
    }
    public function testOverRideKpiDefaultRate() {
    
    	$dao = new KpiDao();
    	$kpis = $dao->overRideKpiDefaultRate(new DefineKpi());
    	$this->assertEquals(5, count($kpis));
    }
    
}
