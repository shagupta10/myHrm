<?php


class exportAttendanceAction extends sfAction {
	public function getTrainingService() {
		if(is_null($this->trainingService)) {
			$this->trainingService = new TrainingService();
			$this->trainingService->setTrainingDao(new TrainingDao());
		}
		return $this->trainingService;
	}
	
	public function execute($request) {
		$trainingId = $request->getParameter('id');
		$training = $this->getTrainingService()->getTrainingById($trainingId);
		$attendees = $training->getTrainingAttendees();
		$schedules = $training->getTrainingSchedule();
		$noOfSessionPerSheet = 4;
		$ascii = ord('A');
		$objPHPExcel = new PHPExcel();
		$objPHPExcel->getProperties()->setCreator("Synerzip HRMS")
		->setLastModifiedBy("Synerzip HRMS")
		->setTitle("Training Attendance")
		->setSubject($training->getTopic())
		->setDescription($training->getTopic())
		->setKeywords("Training Attendance")
		->setCategory("Training Attendance");
		$objPHPExcel->setActiveSheetIndex(0);
		for($col = 'A'; $col !== 'G'; $col++) {
			$objPHPExcel->getActiveSheet()->getColumnDimension($col)->setAutoSize(true);
		}
		$objPHPExcel->getActiveSheet()->setPrintGridlines(TRUE);
		$BoldStyleArray = array(
				'font'  => array(
						'bold'  => true,
						'size'  => 10,
						'name'  => 'Verdana',
				));
		
		$objPHPExcel->getActiveSheet()->getStyle('A')->applyFromArray($BoldStyleArray);
		$objPHPExcel->getActiveSheet()->getStyle('1')->applyFromArray($BoldStyleArray);
		
		//Now start creating report
		$objPHPExcel->setActiveSheetIndex(0)->setCellValue(chr($ascii++).'1', 'Employee Name');
		$i= 2;
		foreach ( $attendees as $attendee ) {
			$objPHPExcel->setActiveSheetIndex(0)->setCellValue('A'.$i, $attendee->getEmployee()->getFirstAndLastNames());
			$i++;
		}
		for($i=0; $i < count($attendees)+2 ; $i++ ) {
			$objPHPExcel->getActiveSheet()->getRowDimension($i)->setRowHeight(30);
		}
		//Create sheet Headers
		if(count($schedule) == 1) {
			foreach ($schedules as $schedules) {
				$objPHPExcel->setActiveSheetIndex(0)
				->setCellValue('B1', date('d-M-Y', strtotime($schedules->getSessionDate())));
			}
		} else {
			$count = count($schedules);
			$numberOfsheets = ceil($count/$noOfSessionPerSheet);
			for($i = 1; $i < $numberOfsheets; $i++) {
				$objWorkSheetBase = $objPHPExcel->getSheet();
				$objWorkSheet1 = clone $objWorkSheetBase;
				$objWorkSheet1->setTitle('Training Attendance - '.$i);
				$objPHPExcel->addSheet($objWorkSheet1);
			}
			
			$inc = 0;
			$currentSheet = 0;
			foreach ($schedules as $schedule) {
				if($inc != 0) {
					if($inc%$noOfSessionPerSheet == 0) {
						$currentSheet++;
						$ascii = ord('B');
					}
				}
				$inc++;
				$objPHPExcel->setActiveSheetIndex($currentSheet)
				->setCellValue(chr($ascii++).'1', date('d-M-Y', strtotime($schedule->getSessionDate())));
			}
		}
		
		$objPHPExcel->getActiveSheet()->setTitle('Training Attendance');
		$objPHPExcel->setActiveSheetIndex(0);
		$objWriter = PHPExcel_IOFactory::createWriter($objPHPExcel, 'Excel2007');
		// $objWriter->save(str_replace('.php', '.xlsx', __FILE__));
		header("Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet");
		header("Content-Disposition: attachment; filename=\"Attendance_".$training->getId().".xlsx\"");
		header("Cache-Control: max-age=0");
		
		//$objWriter = PHPExcel_IOFactory::createWriter($phpExcel, "Excel5");
		$objWriter->save("php://output");
		exit;
	}
}