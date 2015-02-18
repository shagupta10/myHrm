<?php

class ApiAuthenticationDao extends BaseDao {
	public function saveEmployee(Employee $employee) {
		try {
			if ($employee->getEmpNumber() == '') {
				$idGenService = new IDGeneratorService();
				$idGenService->setEntity($employee);
				$employee->setEmpNumber($idGenService->getNextID());
			}
	
			$employee->save();
	
			return $employee;
	
			// @codeCoverageIgnoreStart
		} catch (Exception $e) {
			throw new DaoException($e->getMessage(), $e->getCode(), $e);
		}
		// @codeCoverageIgnoreEnd
	
	}
	public function checkApiKeyForUser($empNumber) {
		try {
			$q = Doctrine_Query::create()
				->from('ApiAuthentication')
				->where('emp_number = ?', $empNumber);
			return $q->fetchOne();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage(), $e->getCode(), $e);
		}
		
	}
	
	public function createApiKeyForUser($empNumber) {
		try {
			$apiAuthentication = new ApiAuthentication();
			$apikey = md5(microtime().rand());
			$apiAuthentication->setApiKey($apikey);
			$apiAuthentication->setEmpNumber($empNumber);
			$apiAuthentication->save();
			return $apikey;
		} catch (Exception $e) {
			throw new DaoException($e->getMessage(), $e->getCode(), $e);
		}
	
	}
	
	public function checkApiKeyForAuthentication($key) {
		$expiryDate = date("Y-m-d",strtotime(date("Y-m-d")." -3 Months"));
		try {
			$q = Doctrine_Query::create()
				->from('ApiAuthentication')
				->where('api_key = ?', $key)
				->andWhere('created_at > ?', $expiryDate);
			return $q->execute();
		} catch (Exception $e) {
			throw new DaoException($e->getMessage(), $e->getCode(), $e);
		}
	}
	
}