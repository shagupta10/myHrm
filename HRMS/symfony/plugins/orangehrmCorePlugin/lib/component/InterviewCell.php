<?php

class InterviewCell extends Cell {

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
		$placeholderValue = array();
		
		if (!is_null($placeholderGetters)) {
			foreach ($placeholderGetters as $placeholder => $getter) {
				$placeholderValue[] = ($this->getDataSourceType() == self::DATASOURCE_TYPE_ARRAY) ? $this->dataObject[$getter] : $this->dataObject->$getter();
			}
		}
		$imagePath = theme_path("images/clock.ico");
		$value = '';
		if($placeholderValue[0]=='Interview Scheduled') {
			$value .='<div id="'.$placeholderValue[1].'" onmouseover="getinterviewTime(this)" onmouseout="hideinterviewTime(this)" >Interview Scheduled<img src = "'.$imagePath.'" height="17px" width="17px" style="padding-left:5px;"></div>';
			$value .='<div class = "messages" id="msg'.$placeholderValue[1].'">'.$placeholderValue[0].'</div>';
		} else {
			$value .= $placeholderValue[0];
		}
		
	 	return $value;
	}//<div id="msgbody'.$placeholderValue[3].'"></div>
}


