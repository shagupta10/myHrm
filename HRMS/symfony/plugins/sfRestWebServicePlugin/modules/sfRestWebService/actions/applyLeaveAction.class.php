<?php

/**
 * Accepts Json Object as post parameter
 *
 * @package    upgrader
 * @subpackage Action
 * @author     Mayur Kathale
 */
class applyLeaveAction extends BaseActionRest {
    public function execute($request) {
    	$logger = Logger::getLogger('topost');
	   	$json_data = file_get_contents('php://input');
	   	$headers = apache_request_headers();
    	$logger->error(/* 'searchLeaves: ' . $json_data .  */' userid : '.base64_decode($headers["user-id"]). 'token : '.base64_decode($headers["token"]).' ***data :'.$json_data);
	 	$request->setParameter('service', 'topost');
	 	$this->service = 'topost';
	 	$response = $this->getResponse();
	 	$response->setHttpHeader('Content-Type', 'application/json; charset=utf-8');
	 	$response->setHttpHeader('Expires', '0');
	 	$response->setHttpHeader("Cache-Control", "must-revalidate, post-check=0, pre-check=0");
	 	$response->setHttpHeader("Cache-Control", "private", false);
	 	$resp['response'] = "success";
	 	return $this->renderText(json_encode($resp, JSON_FORCE_OBJECT));
    }
}