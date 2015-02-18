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
 */
class saveDeleteCertificationAction extends basePimAction {

    /**
     * @param sfForm $form
     * @return
     */
    public function setCertificationForm(sfForm $form) {
        if (is_null($this->certificationForm)) {
            $this->certificationForm = $form;
        }
    }
	
    public function execute($request) {
		if($attachId = $request->getParameter('id')) {
			$attachId = $request->getParameter('id');			
			$response = $this->getResponse();
			
			$certificationService = new CertificationService();
			$certRecord = $certificationService->getCertificationById($attachId);
			if($certRecord) {			
				$contents = $certRecord->getCattach();
				$contentType = $certRecord->getCattachType();
				$fileName = $certRecord->getCattachName();
				$fileLength = $certRecord->getCattachLength();

				$response->setHttpHeader('Pragma', 'public');

				$response->setHttpHeader('Expires', '0');
				$response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
				$response->setHttpHeader("Cache-Control", "private", false);
			    $response->setHttpHeader("Content-Type", $contentType);
				$response->setHttpHeader("Content-Disposition", 'attachment; filename="' . $fileName . '";');
				$response->setHttpHeader("Content-Transfer-Encoding", "binary");
				$response->setHttpHeader("Content-Length", $fileLength);

				$response->setContent($contents);
				$response->send();
			}
			return sfView::NONE;
			
		} else {			
			$certification = $request->getParameter('certification');
			$empNumber = (isset($certification['emp_number'])) ? $certification['emp_number'] : $request->getParameter('empNumber');
			
			if (!$this->IsActionAccessible($empNumber)) {
				$this->forward(sfConfig::get('sf_secure_module'), sfConfig::get('sf_secure_action'));
			}

			$this->certificationPermissions = $this->getDataGroupPermissions('qualification_certifications', $empNumber);

			$this->setCertificationForm(new EmployeeCertificationForm(array(), array('empNumber' => $empNumber, 'certificationPermissions' => $this->certificationPermissions), true));

			if ($request->isMethod('post')) {
				if ($request->getParameter('option') == "save") {
					if ($this->certificationPermissions->canCreate() || $this->certificationPermissions->canUpdate()) {					
						$this->certificationForm->bind($request->getParameter($this->certificationForm->getName()), $request->getFiles($this->certificationForm->getName()));					
						if ($this->certificationForm->isValid()) {						
							$certification = $this->getCertification($this->certificationForm);	
							$this->getEmployeeService()->saveEmployeeCertification($certification);						
							$this->getUser()->setFlash('certification.success', __(TopLevelMessages::SAVE_SUCCESS));
						} else {
							$this->getUser()->setFlash('certification.warning', __('Form Validation Failed'));
						}
					}
				}

				//this is to delete 
				if ($request->getParameter('option') == "delete") {
					if ($this->certificationPermissions->canDelete()) {
						$deleteIds = $request->getParameter('delCertification');

						if (count($deleteIds) > 0) {
							$this->getEmployeeService()->deleteEmployeeCertifications($empNumber, $request->getParameter('delCertification'));
							$this->getUser()->setFlash('certification.success', __(TopLevelMessages::DELETE_SUCCESS));
						}
					}
				}
			}
			$this->getUser()->setFlash('qualificationSection', 'certification');
			$this->redirect('pim/viewQualifications?empNumber=' . $empNumber . '#certification');
		}
    }

    private function getCertification(sfForm $form) {
        $post = $form->getValues();
		$certification = $this->getEmployeeService()->getEmployeeCertifications($post['emp_number'], $post['certification_id']);

        if (!$certification instanceof EmployeeCertification) {
            $certification = new EmployeeCertification();
        }
		$tempName = $_FILES['certification']['tmp_name']['cattach'];
		if($tempName != "") {		
			$content = file_get_contents($tempName);
			$certification->cattach_name = $_FILES['certification']['name']['cattach'];
			$certification->cattach_type = $_FILES['certification']['type']['cattach'];
			$certification->cattach_length = $_FILES['certification']['size']['cattach'];
			$certification->cattach = $content;
		}
        $certification->emp_number = $post['emp_number'];
        $certification->certificationId = $post['certification_id'];
        $certification->name = $post['name'];
        $certification->institute = $post['institute'];
		$certification->date = $post['date'];
		$certification->grade = $post['grade'];
		
		$certification->certificationLink = $post['certification_link'];
        return $certification;
    }

}