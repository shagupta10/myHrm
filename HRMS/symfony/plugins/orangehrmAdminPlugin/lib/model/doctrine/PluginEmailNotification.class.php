<?php

/**
 * PluginEmailNotification class file
 */

/**
 * PluginEmailNotification
 * 
 * This class has been auto-generated by the Doctrine ORM Framework
 * 
 * @package    orangehrm
 * @subpackage model\admin\plugin
 */
abstract class PluginEmailNotification extends BaseEmailNotification {
	
	const ENABLED = 1;
	const DISABLED = 0;

	const LEAVE_ASSIGNMENT = 2;
	const LEAVE_APPLICATION = 1;
	const LEAVE_APPROVAL = 3;
	const LEAVE_CANCELLATION = 4;
	const LEAVE_REJECTION = 5;
	const PERFORMANCE_SUBMISSION = 7;
	const Recruitment = 8;
	const LEAVE_CRON = 9;

	public function getSubscriberList() {
		$label = "";
		$subscribers = $this->getEmailSubscriber();
		$label = array();
		foreach ($subscribers as $subscriber) {
			$label[] = $subscriber->getName() . " <" . $subscriber->getEmail() . ">";
		}
		$label = implode(",", $label);
		return $label;
	}

}
