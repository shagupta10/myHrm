<?php



class performanceConfigAction extends sfAction {
	
	public function setForm(sfForm $form) {
		if (is_null($this->form)) {
			$this->form = $form;
		}
	}
	
	public function getForm() {
		$this->form->request = $this->getRequest();
		return $this->form;
	}
	
	public function execute($request) {
		$this->setForm(new PerformanceConfigForm(array(), array(),true));
		if($request->getParameter('isSave') == 'yes'){
		    $this->form->isSave = 1;
		}
		if($request->isMethod('post')) {
			$this->form->bind($request->getParameter($this->form->getName()));
			if($this->form->isValid()) {
				if($this->form->setDataToWidgets(true)) {
					$this->getUser()->setFlash('performanceConfig.success.nofade', __('Performance Cycle Saved. There are no reviews exist for this cycle.'));
					$this->redirect('performance/performanceConfig?isSave=yes');
				} else {
					$this->getUser()->setFlash('performanceConfig.failure', __('Performance Cycle is overlapping.'));
				}
			}
			$this->redirect('performance/performanceConfig');
		} else {
			$this->form->setDataToWidgets();	
		}
	}
}