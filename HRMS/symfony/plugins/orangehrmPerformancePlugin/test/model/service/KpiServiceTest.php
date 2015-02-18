<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of PerformanceReviewTemplateServiceTest
 *
 * @author sujata
 */

/**
 * @group performance 
 */
class KpiServiceTest extends PHPUnit_Framework_TestCase {


    public function testSaveKpi() {

        $kpi360 = new DefineKpi();
        $daoMock = $this->getMock("KpiDao", array("saveKpi"));
        $daoMock->expects($this->any())
                ->method('saveKpi')
                ->will($this->returnValue($kpi360));

        $service = new KpiService();
        $service->setKpiDao($daoMock);

        $kpi = $service->saveKpi($kpi360);
        $this->assertTrue(is_object($kpi));
    }

    public function testGetKpiForJobTitle1() {

        $kpi360 = new DefineKpi();
        $daoMock = $this->getMock("KpiDao", array("getKpiForJobTitle"));
        $daoMock->expects($this->any())
                ->method('getKpiForJobTitle')
                ->will($this->returnValue(array($kpi360)));

        $service = new KpiService();
        $service->setKpiDao($daoMock);

        $kpis = $service->getKpiForJobTitle(array("jobTitle" => '1'));
        $this->assertEquals(1, sizeof($kpis));
    }


    public function testDeleteKpi() {

        $daoMock = $this->getMock("KpiDao", array("deleteKpi"));
        $daoMock->expects($this->any())
                ->method('deleteKpi')
                ->with($this->equalTo(array('1', '2')))
                ->will($this->returnValue(true));

        $service = new KpiService();
        $service->setKpiDao($daoMock);

        $this->assertTrue($service->deleteKpi(array('1', '2')));
    }

    public function testGetKpiList() {

        $daoMock = $this->getMock("KpiDao", array("getKpiList"));
        $daoMock->expects($this->any())
                ->method('getKpiList')
                ->will($this->returnValue(array(1)));

        $service = new KpiService();
        $service->setKpiDao($daoMock);

        $this->assertEquals(1, sizeof($service->getKpiList(array('1', '2'))));
    }
    
    public function testGetKpiForJobTitle(){
        $daoMock = $this->getMock("KpiDao", array("getKpiForJobTitle"));
        $daoMock->expects($this->any())
                ->method('getKpiForJobTitle')
                ->will($this->returnValue(array(6)));

        $service = new KpiService();
        $service->setKpiDao($daoMock);

        $this->assertEquals(1, sizeof($service->getKpiForJobTitle(array('jobCode'=>'6'))));
    }

}
