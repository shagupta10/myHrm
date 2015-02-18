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
 * Service Class for Performance Review
 *
 * @author orange
 */
class PerformanceKpiService extends BaseService {
	
	/**
	 * Get XML String from Kpi List
	 * @param $kpiList
	 * @return String
	 */
	public function getXmlFromKpi( $kpiList )
	{
		$xmlString	=	'';
		
		$performanceKpiList	=	$this->getKpiToPerformanceKpi( $kpiList );
		$xmlString			=	$this->getXml( $performanceKpiList );
		return $xmlString;
		
	}
	
	/**
	 * Get XML from Performance Kpi
	 * @param $performanceKpiList
	 * @return unknown_type
	 */
	public function getXml( $performanceKpiList)
	{
		try {
			$xmlStr = '
			<xml>
			</xml>';
	
	 
			$xml = simplexml_load_string($xmlStr);
			
			$kpis	=	$xml->addChild('kpis');
			$escapeHtml = array("&#039;" => "\'", "&" => "&amp;", "<" => "&lt;", ">" => "&gt;", "&#034;" => '\"');
			foreach( $performanceKpiList as $performanceKpi){
				$xmlKpi	=	$kpis->addChild('kpi');
				$xmlKpi->addChild('id',$performanceKpi->getId());
				
				$kpiTitle = $performanceKpi->getKpiTitle();
				foreach($escapeHtml as $char => $str) {
	               $kpiTitle = str_replace($char, $str, $kpiTitle);
	            }
				$xmlKpi->addChild('kpiTitle',$kpiTitle);
				
	            $desc = $performanceKpi->getKpi();
	            foreach($escapeHtml as $char => $str) {
	               $desc = str_replace($char, $str, $desc);
	            }
	            
				$xmlKpi->addChild('desc',$desc);
				$xmlKpi->addChild('min',$performanceKpi->getMinRate());
				$xmlKpi->addChild('max',$performanceKpi->getMaxRate());
				$xmlKpi->addChild('selfRate',($performanceKpi->getSelfRate()=='')?' ':$performanceKpi->getSelfRate());
				$xmlKpi->addChild('selfComment',($performanceKpi->getSelfComment()=='')?' ':$performanceKpi->getSelfComment());
				$xmlKpi->addChild('rate',($performanceKpi->getRate()=='')?' ':$performanceKpi->getRate());
				$xmlKpi->addChild('comment',($performanceKpi->getComment()=='')?' ':$performanceKpi->getComment());
			}
			return $xml->asXML();
		}catch (Exception $e) {
			    throw new PerformanceServiceException($e->getMessage());
		}	  
	}
	
	/**
	 * Get Performance List from XML
	 * @param $xmlString
	 * @return unknown_type
	 */
	public function getPerformanceKpiList( $xmlString )
	{
		try {
			$performanceKpiList	=	array();
			
			$xml = simplexml_load_string($xmlString,'SimpleXmlElement', LIBXML_NOERROR+LIBXML_ERR_FATAL+LIBXML_ERR_NONE);
			foreach( $xml->kpis->kpi	as $kpi){
				$performanceKpi	=	new PerformanceKpi();
				$performanceKpi->setId((int)$kpi->id);
				$performanceKpi->setKpiTitle((string)$kpi->kpiTitle);
				$performanceKpi->setKpi((string)$kpi->desc);
				$performanceKpi->setMinRate((string)$kpi->min);
				$performanceKpi->setMaxRate((string)$kpi->max);
				$performanceKpi->setRate((string)$kpi->rate);
				$desc = (string)$kpi->comment;
				$performanceKpi->setComment($desc);
				$performanceKpi->setSelfRate((string)$kpi->selfRate);
				$performanceKpi->setSelfComment((string)$kpi->selfComment);
				array_push($performanceKpiList,$performanceKpi);
			}
			
			return $performanceKpiList;
		}catch (Exception $e) {
			throw new PerformanceServiceException($e->getMessage());
		}	  
		
	}
	public function getRatingXml( $performanceRating) {
		try {
			$xmlStr = '<xml></xml>';
	
			$xml = simplexml_load_string($xmlStr);
	
			$ratings	=	$xml->addChild('ratings');
			$escapeHtml = array("&#039;" => "\'", "&" => "&amp;", "<" => "&lt;", ">" => "&gt;", "&#034;" => '\"');
				
			$ratings->addChild('kpiRate', $performanceRating->getKpiRate());
			$ratings->addChild('selfKpiRate', $performanceRating->getSelfKpiRate());
			$ratings->addChild('selfGoalsRate', $performanceRating->getSelfGoalsRate());
			$selfGoalsComment = $performanceRating->getSelfGoalsComment();
			foreach($escapeHtml as $char => $str) {
				$selfGoalsComment = str_replace($char, $str, $selfGoalsComment);
			}
			$ratings->addChild('selfGoalsComment', $selfGoalsComment);
			$ratings->addChild('selfAccomplishmentRate', $performanceRating->getSelfAccomplishmentRate());
			$selfAccomplishmentComment = $performanceRating->getSelfAccomplishmentComment();
			foreach($escapeHtml as $char => $str) {
				$selfAccomplishmentComment = str_replace($char, $str, $selfAccomplishmentComment);
			}
			$ratings->addChild('selfAccomplishmentComment', $selfAccomplishmentComment);
			$ratings->addChild('self360FeedbackRate', $performanceRating->getSelf360FeedbackRate());
			$self360FeedbackComment = $performanceRating->getSelf360FeedbackComment();
			foreach($escapeHtml as $char => $str) {
				$self360FeedbackComment = str_replace($char, $str, $self360FeedbackComment);
			}
			$ratings->addChild('self360FeedbackComment', $self360FeedbackComment);
				
			$ratings->addChild('goalsRate', $performanceRating->getGoalsRate());
			$goalsComment = $performanceRating->getGoalsComment();
			foreach($escapeHtml as $char => $str) {
				$goalsComment = str_replace($char, $str, $goalsComment);
			}
			$ratings->addChild('goalsComment', $goalsComment);
			$ratings->addChild('accomplishmentRate', $performanceRating->getAccomplishmentRate());
			$accomplishmentComment = $performanceRating->getAccomplishmentComment();
			foreach($escapeHtml as $char => $str) {
				$accomplishmentComment = str_replace($char, $str, $accomplishmentComment);
			}
			$ratings->addChild('accomplishmentComment', $accomplishmentComment);
			$ratings->addChild('feedbackRate', $performanceRating->getFeedbackRate());
			$feedbackComment = $performanceRating->getFeedbackComment();
			foreach($escapeHtml as $char => $str) {
				$feedbackComment = str_replace($char, $str, $feedbackComment);
			}
			$ratings->addChild('feedbackComment', $feedbackComment);
				
				
			return $xml->asXML();
		}catch (Exception $e) {
			throw new PerformanceServiceException($e->getMessage());
		}
	}
	
	public function getPerformanceRatingList( $xmlString ){
		try {
			$rating = simplexml_load_string($xmlString,'SimpleXmlElement', LIBXML_NOERROR+LIBXML_ERR_FATAL+LIBXML_ERR_NONE);
			$performanceRating	=	new PerformanceRating();
				
			$performanceRating->setKpiRate((float)$rating->ratings->kpiRate);
			$performanceRating->setSelfKpiRate((float)$rating->ratings->selfKpiRate);
				
			$performanceRating->setGoalsRate((string)$rating->ratings->goalsRate);
			$performanceRating->setGoalsComment((string)$rating->ratings->goalsComment);
			$performanceRating->setAccomplishmentRate((string)$rating->ratings->accomplishmentRate);
			$performanceRating->setAccomplishmentComment((string)$rating->ratings->accomplishmentComment);
			$performanceRating->setFeedbackRate((string)$rating->ratings->feedbackRate);
			$performanceRating->setFeedbackComment((string)$rating->ratings->feedbackComment);
				
			$performanceRating->setSelfGoalsRate((string)$rating->ratings->selfGoalsRate);
			$performanceRating->setSelfGoalsComment((string)$rating->ratings->selfGoalsComment);
			$performanceRating->setSelfAccomplishmentRate((string)$rating->ratings->selfAccomplishmentRate);
			$performanceRating->setSelfAccomplishmentComment((string)$rating->ratings->selfAccomplishmentComment);
			$performanceRating->setSelf360FeedbackRate((string)$rating->ratings->self360FeedbackRate);
			$performanceRating->setSelf360FeedbackComment((string)$rating->ratings->self360FeedbackComment);
			return $performanceRating;
		}catch (Exception $e) {
			throw new PerformanceServiceException($e->getMessage());
		}
	
	}
	
	/**
	 * Get Performance Kpi 
	 * @return unknown_type
	 */
	private function getKpiToPerformanceKpi( $kpiList)
	{
		try {
			$performanceKpiList	=	array();
			foreach ($kpiList as $kpi) {
				$performanceKpi	=	new PerformanceKpi();
				$performanceKpi->setId( $kpi->getId());
				$performanceKpi->setKpiTitle($kpi->getKpiTitle());
		    	$performanceKpi->setKpi( $kpi->getDesc());
		    	$performanceKpi->setMinRate( $kpi->getMin());
		    	$performanceKpi->setMaxRate( $kpi->getMax());
		    	$performanceKpi->setRatingDescription($kpi->getRatings());
		    	array_push($performanceKpiList,$performanceKpi);
			}
			return $performanceKpiList;
		} catch (Exception $e) {
		    throw new PerformanceServiceException($e->getMessage());
		}	    
	}


}