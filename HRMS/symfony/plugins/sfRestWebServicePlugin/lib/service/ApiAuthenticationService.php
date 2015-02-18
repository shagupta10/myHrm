<?php

/**
 * ApiAuthentication service
 * @return ApiAuthenticationDao
 * @ignore
 */
class ApiAuthenticationService extends BaseService {
	private $apiAuthenticationDao;
	/**
	 * Get ApiAuthentication Dao
	 * @return ApiAuthenticationDao
	 * @ignore
	 */
	public function getApiAuthenticationDao() {
		return $this->apiAuthenticationDao;
	}

	/**
	 * Set ApiAuthentication Dao
	 * @param ApiAuthenticationDao $employeeDao
	 * @return void
	 * @ignore
	 */
	public function setApiAuthenticationDao(ApiAuthenticationDao $apiAuthenticationDao) {
		$this->apiAuthenticationDao = $apiAuthenticationDao;
	}
	
	/**
	 * Construct
	 * @ignore
	 */
	public function __construct() {
		$this->apiAuthenticationDao = new ApiAuthenticationDao();
	}
	
	/**
	 * @version method to get Api key for user
	 * @param int $empNumber
	 * @return ApiAuthentication instance if found or NULL
	 * @throws DaoException
	 */
	public function checkApiKeyForUser($empNumber) {
		return $this->getApiAuthenticationDao()->checkApiKeyForUser($empNumber);
	}
	
	public function checkApiKeyForAuthentication($key) {
		return $this->getApiAuthenticationDao()->checkApiKeyForAuthentication($key);
	}
	
	public function createApiKeyForUser($empNumber) {
		return $this->getApiAuthenticationDao()->createApiKeyForUser($empNumber);
	}
}