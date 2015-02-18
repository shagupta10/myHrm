<?php
class saveTrainingPointsAction extends sfAction {
	
	public function execute($request) {
		$logger = Logger::getLogger('saving points');
		$logger->info('inside saving');
		
		sfConfig::set('sf_web_debug', false);
		sfConfig::set('sf_debug', false);
		$response = $this->getResponse();
		$response->setHttpHeader('Expires', '0');
		$response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
		$response->setHttpHeader("Cache-Control", "private", false);
		
		return $this->renderText(json_encode($trainingArray))  ;
		
	}
}