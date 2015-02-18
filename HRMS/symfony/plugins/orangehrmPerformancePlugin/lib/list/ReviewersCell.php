<?php
class ReviewersCell extends Cell{
    private $performanceReviewService;
    private $jobTitleService;
    
    protected function getLabel(){
        if($this->hasProperty('labelGetter')){
            $label = $this->getValue('labelGetter');
        }else{
            $label = $this->getPropertyValue('label', 'Undefined');
        }
        return $label;
    }
	
	public function getPerformanceReviewService() {
		if(is_null($this->performanceReviewService)) {
			$this->performanceReviewService = new PerformanceReviewService();
			$this->performanceReviewService->setPerformanceReviewDao(new PerformanceReviewDao());
		}
		return $this->performanceReviewService;
	}
	
    public function __toString(){
        $placeholderGetters = $this->getPropertyValue('placeholderGetters');
        $reviewersName = $this->getLabel();
        $placeholderValue = array();
        if (!is_null($placeholderGetters)) {
            foreach ($placeholderGetters as $placeholder => $getter) {
                $placeholderValue[] = ($this->getDataSourceType() == self::DATASOURCE_TYPE_ARRAY) ? $this->dataObject[$getter] : $this->dataObject->$getter();
            }
        }
        $loggedInEmpNumber = $_SESSION['empNumber'];
        $currentFromDate = $this->getPerformanceReviewService()->getCurrentPerformancePeriod()->getPeriodFrom();
        if ((isset($_SESSION['isAdmin']) && $_SESSION['isAdmin'] == 'Yes') && ($loggedInEmpNumber&& $currentFromDate == $placeholderValue[1] && $placeholderValue[2] != PerformanceReview::PERFORMANCE_REVIEW_STATUS_APPROVED && $placeholderValue[2] != PerformanceReview::PERFORMANCE_REVIEW_STATUS_SUBMITTED)) { 
            $editLink = '<a href="javascript:void(0)" class="editLink" id="btnEdit-'.$placeholderValue[0].'" style="float:right;padding-right:10%" onclick="showEditablebox('.$placeholderValue[0].')">Edit</a>';
        }else{
            $editLink = '';
        }
        $spanContainerHTML = content_tag('span', $reviewersName);
        $reviewerContainerHTML = content_tag('div', $spanContainerHTML.$editLink, array(
            'id' => $this->generateAttributeValue($placeholderGetters, 'reviewersList-{id}'),
        ));
        
        $inputFieldHTML = tag('input', array(
            'type' => 'text',
            'id' => 'txtRevHdn-'.$placeholderValue[0]
        ));
        $txtContainerHTML = content_tag('div', $inputFieldHTML, array(
            'id' => $this->generateAttributeValue($placeholderGetters, 'txtContainer-{id}'),
        ));
        
        $saveLink = '<a href="javascript:void(0)" class="saveLink" id="btnSave-'.$placeholderValue[0].'" onclick="saveReviewers('.$placeholderValue[0].')">Save</a>';
        $cancelLink = '<a href="javascript:void(0)" class="cclLink" id="btnCcl-'.$placeholderValue[0].'" onclick="onClickCancel('.$placeholderValue[0].')">Cancel</a>';
	    $buttonContainerHTML = content_tag('div', $saveLink.'&nbsp;'.$cancelLink, array(
            'id' => $this->generateAttributeValue($placeholderGetters, 'txtContainer-{id}'), 'style' => 'float: right;'
        ));
        $jsContainerHTML = content_tag('script', 'populateTokenInput('.$placeholderValue[0].','.str_replace('&quot;', "'",$placeholderValue[3]).');', array(
            'type' => 'text/javascript'
        ));
        $reviewerHdnContainerHTML = content_tag('div', $txtContainerHTML.$buttonContainerHTML.$jsContainerHTML, array(
            'id' => $this->generateAttributeValue($placeholderGetters, 'reviewersListHdn-{id}'),'style' => 'display:none;'
        ));
        
	    return $reviewerContainerHTML.$reviewerHdnContainerHTML;
    }
}