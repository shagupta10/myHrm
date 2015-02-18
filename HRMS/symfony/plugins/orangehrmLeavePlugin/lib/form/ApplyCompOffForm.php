<?php

/*
 *
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

/**
 * Form class for apply leave
 */
class ApplyCompOffForm extends sfForm {
	
	public function configure() {
		
		$this->setWidgets($this->getFormWidgets());
		$this->setValidators($this->getFormValidators());
		//$this->setDefault('txtEmpID', $this->getEmployeeNumber());
		//$this->getValidatorSchema()->setPostValidator(new sfValidatorCallback(array('callback' => array($this, 'postValidation'))));
		
		$this->getWidgetSchema()->setNameFormat('addCompOff[%s]');
		$this->getWidgetSchema()->setLabels($this->getFormLabels());
		
	}
	
	/**
	 * Get Employee number
	 * @return int
	 */
	private function getEmployeeNumber() {
		return $_SESSION['empID'];
	}
	
	/**
	 *
	 * @return array
	 */
	protected function getFormWidgets() {
		$widgets = array(
			'numberOfDays' => new sfWidgetFormInputText(),
			'txtComment' => new sfWidgetFormTextarea(array(), array('rows' => '3', 'cols' => '30'))
		);
		return $widgets;
	}
	
	/**
	 *
	 * @return array
	 */
	protected function getFormValidators() {
		$validators = array(
			'numberOfDays' => new sfValidatorString(array('required' => true), array('required' => __(ValidationMessages::REQUIRED))),
			'txtComment' => new sfValidatorString(array('required' => false, 'trim' => true, 'max_length' => 1000)),
		);
		return $validators;
	}
	
	/**
	 *
	 * @return array
	 */
	protected function getFormLabels() {
		$requiredMarker = ' <em>*</em>';
		$labels = array(
			'numberOfDays' => __('Number Of Days') . $requiredMarker,
			'txtComment' => __('Comment') . $requiredMarker,
		);
		return $labels;
	}
}
