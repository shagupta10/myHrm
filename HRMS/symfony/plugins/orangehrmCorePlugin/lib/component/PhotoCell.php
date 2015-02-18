<?php 

class PhotoCell extends Cell {
	
	protected function getLabel() {
        if ($this->hasProperty('labelGetter')) {
            $label = $this->getValue('labelGetter');
        } else {
            $label = $this->getPropertyValue('label', 'Undefined');
        }

        return $label;
    }
    
	public function __toString() {
		$placeholderGetters = $this->getPropertyValue('placeholderGetters');
		$employeeId = $this->getLabel();
		$placeholderValue = array();
		
		if (!is_null($placeholderGetters)) {
			foreach ($placeholderGetters as $placeholder => $getter) {
				$placeholderValue[] = ($this->getDataSourceType() == self::DATASOURCE_TYPE_ARRAY) ? $this->dataObject[$getter] : $this->dataObject->$getter();
			}
		}
		
		$id= $placeholderValue[0];
		$employeeService = new EmployeeService();
		$empPicture = $employeeService->getEmployeePicture($id);
        $width = '200';
        $height = '200';
		
		if (!empty($empPicture)) {
            $width = $empPicture->width;
            $height = $empPicture->height;
        }
		$module='empDir';
	 	$imageHTML = tag('img', array(
				'src' => url_for("pim/viewPhoto?empNumber=". $id."&from=".$module),
	 			'height' => $height,
	 			'width' => $width,
		));
		
		//$value = '<div> <h1 class="directoryPhotoHeading">'.$employeeId.'</h1> <div class="directoryImageHolder">'.$imageHTML.'</div>';
	 	return $imageHTML;
	}
}


