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
class PerformanceRating{
			
		public $selfKpiRate;
		public $selfGoalsRate;
		public $selfAccomplishmentRate;
		public $self360FeedbackRate;
		
		public $kpiRate;
		public $goalsRate;
		public $accomplishmentRate;
		public $feedbackRate;
		
		public $selfGoalsComment;
		public $selfAccomplishmentComment;
		public $self360FeedbackComment;
		
		public $goalsComment;
		public $accomplishmentComment;
		public $feedbackComment;
				
        function __construct() {
        }
		 
        public function getKpiRate() {
        	return $this->kpiRate;
        }
        
        public function setKpiRate($kpiRate) {
        	$this->kpiRate = $kpiRate;
        }
         
        public function getGoalsRate() {
        	return $this->goalsRate;
        }
        
        public function setGoalsRate($goalsRate) {
        	$this->goalsRate = $goalsRate;
        }
        
        public function getAccomplishmentRate() {
        	return $this->accomplishmentRate;
        }
        
        public function setAccomplishmentRate($accomplishmentRate) {
        	$this->accomplishmentRate = $accomplishmentRate;
        }
        
        public function getFeedbackRate() {
        	return $this->feedbackRate;
        }
        
        public function setFeedbackRate($feedbackRate) {
        	$this->feedbackRate = $feedbackRate;
        }
        
        public function getSelfKpiRate() {
        	return $this->selfKpiRate;
        }
        
        public function setSelfKpiRate($selfKpiRate) {
        	$this->selfKpiRate = $selfKpiRate;
        }
         
        public function getSelfGoalsRate() {
        	return $this->selfGoalsRate;
        }
        
        public function setSelfGoalsRate($selfGoalsRate) {
        	$this->selfGoalsRate = $selfGoalsRate;
        }
        
        public function getSelfAccomplishmentRate() {
        	return $this->selfAccomplishmentRate;
        }
        
        public function setSelfAccomplishmentRate($selfAccomplishmentRate) {
        	$this->selfAccomplishmentRate = $selfAccomplishmentRate;
        }
        
        public function getSelf360FeedbackRate() {
        	return $this->self360FeedbackRate;
        }
        
        public function setSelf360FeedbackRate($self360FeedbackRate) {
        	$this->self360FeedbackRate = $self360FeedbackRate;
        }
        
        public function getGoalsComment() {
        	return $this->goalsComment;
        }	
        public function setGoalsComment($goalsComment) {
        	$this->goalsComment = $goalsComment;
        }
        public function getAccomplishmentComment() {
        	return $this->accomplishmentComment;
        }
        public function setAccomplishmentComment($accomplishmentComment) {
        	$this->accomplishmentComment = $accomplishmentComment;
        }
        public function getFeedbackComment() {
        	return $this->feedbackComment;
        }
        public function setFeedbackComment($feedbackComment) {
        	$this->feedbackComment = $feedbackComment;
        }
        		
		public function getSelfAccomplishmentComment() {
			return $this->selfAccomplishmentComment;
		}
		
		public function setSelfAccomplishmentComment($selfAccomplishmentRate) {
			$this->selfAccomplishmentComment = $selfAccomplishmentRate;
		}
		public function getSelfGoalsComment() {
			return $this->selfGoalsComment;
		}
		
		public function setSelfGoalsComment($selfGoalsComment) {
			$this->selfGoalsComment = $selfGoalsComment;
		}
		public function getSelf360FeedbackComment() {
			return $this->self360FeedbackComment;
		}
		public function setSelf360FeedbackComment($self360FeedbackComment) {
			$this->self360FeedbackComment = $self360FeedbackComment;
		}

}