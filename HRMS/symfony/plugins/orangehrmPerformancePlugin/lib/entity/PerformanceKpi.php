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
class PerformanceKpi{
	
		public $id ;
		public $kpi;
        public $minRate ;
        public $maxRate;
        public $rate ;
        public $selfRate;
		public $comment;
		public $selfComment;	
		public $ratingDescription;
		public $kpiTitle;
		public $finalRating;

        
        function __construct() {
        }

        public function getId()
        {
        	return $this->id;
        }
        
        public function setId( $id)
        {
        	$this->id	=	$id ;
        }
        
        public function getKpiTitle(){
        	return $this->kpiTitle;	
        }
        
        public function setKpiTitle($kpiTitle){
        	$this->kpiTitle = $kpiTitle;
        }
        
        public function getKpi() {
            return $this->kpi;
        }

        public function setKpi($kpi) {
            $this->kpi = $kpi;
        }

        public function getMinRate() {
            return $this->minRate;
        }

        public function setMinRate($minRate) {
            $this->minRate = $minRate;
        }

        public function getMaxRate() {
            return $this->maxRate;
        }

        public function setMaxRate($maxRate) {
            $this->maxRate = $maxRate;
        }

        public function getRate() {
            return $this->rate;
        }

        public function setRate($rate) {
            $this->rate = $rate;
        }

        public function getComment() {
            return $this->comment;
        }

        public function setComment($comment) {
            $this->comment = htmlspecialchars($comment);
        }
        
        public function getSelfRate() {
            return $this->selfRate;
        }

        public function setSelfRate($selfRate) {
            $this->selfRate = $selfRate;
        }

        public function getSelfComment() {
            return $this->selfComment;
        }

        public function setSelfComment($selfComment) {
            $this->selfComment = htmlspecialchars($selfComment);
        }
        
        public function getRatingDescription(){
        	return $this->ratingDescription;
        }

		public function setRatingDescription($ratingDescription){
			$this->ratingDescription = $ratingDescription;
		}
		
		public function getFinalRating() {
			return $this->finalRating;
		}
		
		public function setFinalRating($finalRating) {
			$this->finalRating = $finalRating;
		}
		
		
       

}