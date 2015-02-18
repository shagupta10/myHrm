<?php
/**
*  @param  NULL
* @return NULL
* @author Mayur V. Kathale<mayur.kathale@gmail.com>
*
*/

class EmailDigestContent {
	protected $bodyTemplateName = 'EmailDigestBody.txt';
	protected $interviewerName;
	private $directoryPathBase;
	private $bodyReplacements;
	private $vacancyName;
	private $managerEmp;
	private $detail;
	private $microResumes;

	public function  __construct($vacancyName, $managerEmp, $detail, $microResumes) {
		$this->vacancyName = $vacancyName;
		$this->managerEmp = $managerEmp;
		$this->detail = $detail;
		$this->microResumes = $microResumes;
		$this->directoryPathBase = sfConfig::get('sf_root_dir')."/plugins/orangehrmRecruitmentPlugin/modules/recruitment/templates/mail/en_US/";
		$this->bodyReplacements = $this->setBodyReplacements();
	}

	public function getBody() {
		return $this->replaceContent($this->readFile($this->directoryPathBase.$this->bodyTemplateName), $this->bodyReplacements);
	}
	
	public function setBodyReplacements() {
		$url = (empty($_SERVER['HTTPS']) OR $_SERVER['HTTPS'] === 'off') ? 'http://' : 'https://';
		$url .= $_SERVER['HTTP_HOST'];
		$this->bodyReplacements = array(
				'vacancyName' => $this->vacancyName,
				'total' => $this->detail['total'],
				'unprocessed' => $this->detail['appInitiated'],
				'shortlisted' => $this->detail['shortlisted'],
				'interview' => $this->detail['intScheduled'],
				'offered' => $this->detail['offered'],
				'microResumeList' => $this->microResumes,
				'synerzipHRMSite' => '<a href = "'.$url.'">http://hrms.synerzip.in</a>',
				'title' =>  "Daily Digest,  ".date("F j, Y"),
				'hmNames' => $this->detail['hmText']
		);
		return $this->bodyReplacements;
	}
	public function readFile($path) {
		if (!is_readable($path)) {
			throw new Exception("File is not readable.");
		}
		return file_get_contents($path);
	}
	
	public function replaceContent($template, $replacements, $wrapper = '`') {
	
		$keys = array_keys($replacements);
	
		foreach ($keys as $value) {
			$needls[] = $wrapper . $value . $wrapper;
		}
	
		return str_replace($needls, $replacements, $template);
	}
}
