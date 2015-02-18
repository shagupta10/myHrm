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
class CertificationService extends BaseService {
    
    private $certificationDao;
    
    /**
     * @ignore
     */
    public function getCertificationDao() {
        
        if (!($this->certificationDao instanceof CertificationDao)) {
            $this->certificationDao = new CertificationDao();
        }
        
        return $this->certificationDao;
    }

    /**
     * @ignore
     */
    public function setCertificationDao($certificationDao) {
        $this->certificationDao = $certificationDao;
    }
    
   
    
    /**
     * Retrieves a certification by ID
     * 
     * @version 2.6.12 
     * @param int $id 
     * @return Certification An instance of Certification or NULL
     */    
    public function getCertificationById($id) {
        return $this->getCertificationDao()->getCertificationById($id);
    }
    
    /**
     * Retrieves a certification by name
     * 
     * Case insensitive
     * 
     * @version 2.6.12 
     * @param string $name 
     * @return Certification An instance of Certification or false
     */    
    public function getCertificationByName($name) {
        return $this->getCertificationDao()->getCertificationByName($name);
    }    
  
    /**
     * Retrieves all certifications ordered by name
     * 
     * @version 2.6.12 
     * @return Doctrine_Collection A doctrine collection of Certification objects 
     */        
    public function getCertificationList() {
        return $this->getCertificationDao()->getCertificationList();
    }
    
	
	 public function getCertificationApproveList() {
        return $this->getCertificationDao()->getCertificationApproveList();
    }
    /**
     * Deletes certifications
     * 
     * @version 2.6.12 
     * @param array $toDeleteIds An array of IDs to be deleted
     * @return int Number of records deleted
     */    
    public function deleteCertifications($toDeleteIds) {
        return $this->getCertificationDao()->deleteCertifications($toDeleteIds);
    }
	
	public function disapproveCertifications($toDeleteIds) {
        return $this->getCertificationDao()->disapproveCertifications($toDeleteIds);
    }

}