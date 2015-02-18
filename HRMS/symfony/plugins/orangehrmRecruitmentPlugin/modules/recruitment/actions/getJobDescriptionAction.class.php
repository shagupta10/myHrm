<?php

class getJobDescriptionAction extends sfAction {
	
	private $vacancyService;
	
	public function getVacancyService() {
		if(is_null($this->vacancyService)) {
			$this->vacancyService = new VacancyService();
		}
		return $this->vacancyService;
	}
	
	public function execute($request) {
	
		$vacId = $request->getParameter('vacId');
		$vacancy = $this->getVacancyService()->getVacancyById($vacId);
		
		$ratingl = array();
		if(!is_null($vacancy))
		{
			$rating['name'] = $vacancy->getName();
			$rating['description'] = nl2br($vacancy->getDescription());
			array_push($ratingl,$rating);
		}

		$response = $this->getResponse();
		$response->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
		$response->setHttpHeader('Expires', '0');
		$response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
		$response->setHttpHeader("Cache-Control", "private", false);
		return $this->renderText(json_encode($ratingl)); 
	}
}
