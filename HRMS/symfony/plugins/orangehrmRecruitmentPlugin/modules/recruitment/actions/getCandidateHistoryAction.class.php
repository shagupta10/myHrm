<?php
// Added by Upase G.
class getCandidateHistoryAction extends sfAction {
	public function getCandidateService() {
		if (is_null ( $this->candidateService )) {
			$this->candidateService = new CandidateService ();
			$this->candidateService->setCandidateDao ( new CandidateDao () );
		}
		return $this->candidateService;
	}
	public function execute($request) {
		error_reporting(E_ALL);
		$candidateId = $request->getParameter ( 'id' );
		$userObj = $this->getUser ()->getAttribute ( 'user' );
		$allowedHistoryList = $userObj->getAllowedCandidateHistoryList ( $candidateId );
		$candidateHistory = $this->getCandidateService ()->getCandidateHistoryForCandidateId ( $candidateId, $allowedHistoryList );
		$candidateHistoryService = new CandidateHistoryService ();
		$htmlDataArray = $candidateHistoryService->getCandidateHistoryList ( $candidateHistory );
		$htmlReturnData = "";
		if (empty ( $htmlDataArray )) {
			$htmlReturnData = "false";
		} else {
			$i=1;
			foreach ( $htmlDataArray as $htmlData ) {
				$urlstring = sfContext::getInstance ()->getRequest ()->getUriPrefix () . sfContext::getInstance ()->getRequest ()->getRelativeUrlRoot () . '/index.php/recruitment/changeCandidateVacancyStatus?id=' . $htmlData->getId ();
				if($i%2==0){
					$htmlReturnData .= "<tr class='even'>";
				}else{
					$htmlReturnData .= "<tr class='odd'>";
				}
				$htmlReturnData .= "<td>";
				$htmlReturnData .= $htmlData->getPerformedDate ();
				$htmlReturnData .= "</td>";
				$htmlReturnData .= "<td>";
				$htmlReturnData .= $htmlData->getDescription ();
				$htmlReturnData .= "</td>";
				$htmlReturnData .= "<td>";
				$htmlReturnData .= $htmlData->getNote ();
				$htmlReturnData .= "</td>";
				$htmlReturnData .= "<td>";
				$htmlData->getId ();
				$htmlReturnData .= "<a target='' href='" . $urlstring . "'>" . $htmlData->getDetails () . "</a>";
				$htmlReturnData .= "</td>";
				$htmlReturnData .= "</tr>";
				$i++;
			}
		}
		echo $htmlReturnData;
		return sfView::NONE;
	}
}
?>