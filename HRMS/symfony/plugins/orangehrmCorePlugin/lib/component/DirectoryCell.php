<?php

class DirectoryCell extends Cell {
	

	protected function getLabel() {
		if ($this->hasProperty('labelGetter')) {
			$label = $this->getValue('labelGetter');
		} else {
			$label = $this->getPropertyValue('label', 'Undefined');
		}
	
		return $label;
	}
	
	protected function getEditLink($actionName,$isAdmin){
		$employeeId = $this->getLabel();
		$link = '&nbsp;&nbsp;&nbsp;&nbsp;';
		if($_SESSION['isAdmin'] == 'Yes'){
			$link .= '[ <a href="'.$actionName.'/empNumber/'.$employeeId.'" style="color:#6600FF;">Edit</a> ]';
		}
		return $link;
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
		
		$value = '<span style="font-weight:bold;">';
		if(! empty($placeholderValue[0])){
			$value .= $placeholderValue[0].', ';
		}
		$value .= $placeholderValue[1].'</span>'.$this->getEditLink('viewPersonalDetails').'<br/>';
		
		if(! empty($placeholderValue[2])){
			$value .='<span style="font-weight:bold;">Email: </span>'. $placeholderValue[2].', ';
		}
		
		if(! empty($placeholderValue[3])){
			$value .= '<span style="font-weight:bold;">Mobile: </span>'.$placeholderValue[3].', ';
		}
		
		if(! empty($placeholderValue[4])){
			$value .= '<span style="font-weight:bold;">Skype: </span>'.$placeholderValue[4];
		}
		
		$value .= $this->getEditLink('contactDetails',true).'<br/>';
		
		if(! empty($placeholderValue[5])){
			$value .= '<span style="font-weight:bold;">Designation: </span> '.$placeholderValue[5].$this->getEditLink('viewJobDetails').' <br/> ';
		};
		
		if(! empty($placeholderValue[6])){
			$value .= '<span style="font-weight:bold;">Project: </span>'.$placeholderValue[6].$this->getEditLink('viewJobDetails').'<br/>';
		}
		
		if(! empty($placeholderValue[7])){
			$value .= '<span style="font-weight:bold;">Domain/Skills: </span>'.$placeholderValue[7].$this->getEditLink('viewQualifications').'<br/>';
		}
		
		if(! empty($placeholderValue[8])){
			$value .= '<span style="font-weight:bold;">Qualification: </span> '.$placeholderValue[8].$this->getEditLink('viewQualifications').'<br/>';
		}
		
		if(! empty($placeholderValue[9])){
			$value .= '<span style="font-weight:bold;">Blood Group: </span>: '.$placeholderValue[9].$this->getEditLink('viewPersonalDetails').'<br/>';
		}
		
		
		if(! empty($placeholderValue[10])){
			$value .= '<span style="font-weight:bold;">LinkedIn URL: </span>:  '.$placeholderValue[10].$this->getEditLink('viewPersonalDetails').'<br/>';
		}

	 	return $value;
	}
	
	
}


