<?php

class VacancyCell extends Cell {

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
		
		$value = '<a href="#" onclick="getVacancyDescription(' .$placeholderValue[0]. ')" class ="links">';
		$value .= $placeholderValue[1].'</a>';
	 	return $value;
	}
}


