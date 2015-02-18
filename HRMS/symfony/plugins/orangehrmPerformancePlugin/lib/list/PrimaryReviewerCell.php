<?php

class PrimaryReviewerCell extends Cell {
	
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
		$primaryReviewName = $this->getLabel();		
		$placeholderValue = array();		
		if (!is_null($placeholderGetters)) {
			foreach ($placeholderGetters as $placeholder => $getter) {
				$placeholderValue[] = ($this->getDataSourceType() == self::DATASOURCE_TYPE_ARRAY) ? $this->dataObject[$getter] : $this->dataObject->$getter();
			}
		}
		
		$spanPrimaryContainerHTML = content_tag('span', $primaryReviewName);
		$spanContainerHTML = content_tag('div', '', array(
            'id' => $this->generateAttributeValue($placeholderGetters, 'spanEmpNum-{id}'), 'style' => 'display: none;'
        ));
        $previewerContainerHTML = content_tag('div', $spanPrimaryContainerHTML.$spanContainerHTML, array(
            'id' => $this->generateAttributeValue($placeholderGetters, 'previewersList-{id}'),
        ));
        /****************************************************************************************/
        $inputFieldHTML = tag('input', array(
            'type' => 'text',
            'id' => 'ptxtRevHdn-'.$placeholderValue[0]
        ));
        $ptxtContainerHTML = content_tag('div', $inputFieldHTML, array(
            'id' => $this->generateAttributeValue($placeholderGetters, 'ptxtContainer-{id}'),
        ));
        $errContainerHTML = content_tag('span', 'Required', array(
            'class' => 'errorContainer', 'style' => 'display: none;'
        ));
        $jsContainerHTML = content_tag('script', 'populateEmployeeNumber('.$placeholderValue[0].','.$placeholderValue[1].');populateTokenInputPrimary('.$placeholderValue[0].','.str_replace('&quot;', "'",$placeholderValue[2]).');', array(
            'type' => 'text/javascript'
        ));
        
        $previewerHdnContainerHTML = content_tag('div', $ptxtContainerHTML.$errContainerHTML.$jsContainerHTML, array(
            'id' => $this->generateAttributeValue($placeholderGetters, 'previewersListHdn-{id}'),'style' => 'display:none;'
        ));
        
		return $previewerContainerHTML.$previewerHdnContainerHTML;
		
	}
}