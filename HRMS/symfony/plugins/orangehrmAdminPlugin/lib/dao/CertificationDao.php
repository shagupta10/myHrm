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
class CertificationDao extends BaseDao {

   
    public function getCertificationById($id) {
        
        try {
            return Doctrine::getTable('EmployeeCertification')->findOneByCertificationId($id);
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        
    }
    
    public function getCertificationByName($name) {
        
        try {
            
            $q = Doctrine_Query::create()
                                ->from('EmployeeCertification')
                                ->where('name = ?', trim($name));
            
            return $q->fetchOne();
            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }
        
    }    
    
    public function getCertificationList() {
        
        try {
            
            $q = Doctrine_Query::create()->from('EmployeeCertification c')
										 ->leftJoin('c.Employee e')
										 ->where('approve = ?', EmployeeCertification::CERTIFICATE_NOT_APPROVE)
                                         ->orderBy('name');
            
            return $q->execute();            
            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }        
        
    }
    public function getCertificationApproveList() {
        
        try {
            
            $q = Doctrine_Query::create()->from('EmployeeCertification c')
										 ->leftJoin('c.Employee e')
										 ->where('approve = ?', EmployeeCertification::CERTIFICATE_APPROVE)
                                         ->orderBy('name');
            
            return $q->execute();            
            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }        
        
    }
	
    public function deleteCertifications($toDeleteIds) {
        
        try {           
            $q = Doctrine_Query::create()->update('EmployeeCertification')->set('approve',EmployeeCertification::CERTIFICATE_APPROVE)
                            ->whereIn('certification_id', $toDeleteIds);			
            return $q->execute();            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }        
        
    }
	
	public function disapproveCertifications($toDeleteIds) {
        
        try {           
            $q = Doctrine_Query::create()->delete('EmployeeCertification')
                            ->whereIn('certification_id', $toDeleteIds);			
            return $q->execute();            
        } catch (Exception $e) {
            throw new DaoException($e->getMessage(), $e->getCode(), $e);
        }        
        
    }

}