<?php

/**
 * OrangeHRM is a comprehensive Human Resource Management (HRM) System that captures
 * all the essential functionalities required for any enterprise.
 * Copyright (C) 2006 OrangeHRM Inc., http://www.orangehrm.com
 *
 * OrangeHRM is free software; you can redistribute it and/or modify it under the terms of
 * the GNU General Public License as published by the Free Software Foundation; either
 * version 2 of the License, or (at your option) any later version.
 *
 * OrangeHRM is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY;
 * without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.
 * See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with this program;
 * if not, write to the Free Software Foundation, Inc., 51 Franklin Street, Fifth Floor,
 * Boston, MA  02110-1301, USA
 *
 */
class PerformanceAttachmentForm extends BaseForm {

	private $performanceReviewService;
	private $viewMode;	
	 /**
     * Get performance Service
     */
    public function getPerformanceReviewService() {
        $this->performanceReviewService = new PerformanceReviewService();
        $this->performanceReviewService->setPerformanceReviewDao(new PerformanceReviewDao());
        return $this->performanceReviewService;
    }

    /**
     * Set Performance Service
     * @param PerformanceReviewService $performanceReviewService
     * @return unknown_type
     */
    public function setPerformanceReviewService(PerformanceReviewService $performanceReviewService) {
        $this->performanceReviewService = $performanceReviewService;
    }
    
    public function getViewMode(){
    	return $this->viewMode;
    }
	/**
	 *
	 */
	public function configure() {

		$this->viewMode = $this->getOption('viewMode');

		$this->setWidgets(array(
		    'reviewId' => new sfWidgetFormInputHidden(),
		    'attachId' => new sfWidgetFormInputHidden(),
		    'ufile' => new sfWidgetFormInputFile(),
		    'comment' => new sfWidgetFormTextArea(),
		    'commentOnly' => new sfWidgetFormInputHidden(),
		));

		$this->setValidators(array(
		    'reviewId' => new sfValidatorNumber(array('required' => false, 'min' => 0)),
		    'attachId' => new sfValidatorNumber(array('required' => false, 'min' => 0)),
		    'ufile' => new sfValidatorFile(array('required' => false,
			'max_size' => 1024000), array('max_size' => __('Attachment Size Exceeded.'))),
		    'comment' => new sfValidatorString(array('required' => false, 'max_length' => 255)),
		    'commentOnly' => new sfValidatorString(array('required' => false)),
		));

		$this->widgetSchema->setNameFormat('performanceAttachment[%s]');
	}

	/**
	 *
	 * @return <type>
	 */
	public function save() {
		$reviewId = $this->getValue('reviewId');
		$recId = $this->getValue('attachId');
		$commentOnly = $this->getValue('commentOnly');
		$file = $this->getValue('ufile');
		$performanceService = $this->getPerformanceReviewService();
		if ($recId != "") {
			$existRec = $performanceService->getPerformanceAttachment($recId);
			if ($commentOnly == '1') {
				$existRec->comment = $this->getValue('comment');
				$existRec->save();
				return;
			} else {
				$existRec->delete();
			}
		}
		
		if (($file instanceof sfValidatedFile) && $file->getOriginalName() != "") {
			$tempName = $file->getTempName();
			$attachment = new PerformanceAttachment();
			$attachment->reviewId = $reviewId;
			$attachment->fileContent = file_get_contents($tempName);
			$attachment->fileName = $file->getOriginalName();
			$attachment->fileType = $file->getType();
			$attachment->fileSize = $file->getSize();
			$attachment->comment = $this->getValue('comment');
			$attachment->attachedBy = sfContext::getInstance()->getUser()->getEmployeeNumber();
			$attachment->save();
		}
	}

}