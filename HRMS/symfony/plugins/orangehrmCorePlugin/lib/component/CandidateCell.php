<?php

class CandidateCell extends Cell {
	
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
		
		$candidateName = $this->getLabel();
		
		$placeholderValue = array();
		
		if (!is_null($placeholderGetters)) {
			foreach ($placeholderGetters as $placeholder => $getter) {
				$placeholderValue[] = ($this->getDataSourceType() == self::DATASOURCE_TYPE_ARRAY) ? $this->dataObject[$getter] : $this->dataObject->$getter();
			}
		}
		$addCandidateURL = sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/recruitment/addCandidate?id=';
	    $value = "<a href ='".$addCandidateURL.$placeholderValue[0]."'>".$candidateName."</a>";
	    $value .= "<a id='chistory_$placeholderValue[0]' onclick='candidateHistory(this.id)' data-target='#candidatehistoryBox' data-toggle='modal' style='float:right;padding-right:10%' href =''>History</a>";
	    
	    if(! empty($placeholderValue[3])){
			$value .=" ,". $placeholderValue[3];
		}
		
		if(! empty($placeholderValue[4])){
			$value .=" ,". $placeholderValue[4];
		}
		
		$value .= "<br/>";
		
		if(! empty($placeholderValue[5])){
			$value .= html_entity_decode(nl2br($placeholderValue[5]))."  ";
			$shortlistrejectURL = sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/recruitment/changeCandidateVacancyStatus?candidateVacancyId=';
			$candidateStatusName = $placeholderValue[6];
			if($candidateStatusName == 'Application Initiated'){
				$value .= "<a href='".$shortlistrejectURL.$placeholderValue[1]."&selectedAction=2'> Shortlist </a> &nbsp; ";
				$value .= "<a href ='".$shortlistrejectURL.$placeholderValue[1]."&selectedAction=3'> Reject </a> &nbsp; ";
			}
		}
		
		return $value;
		
	}
}