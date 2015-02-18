<?php

/*
 * To change this template, choose Tools | Templates
 * and open the template in the editor.
 */

/**
 * Description of sampleCsvDownloadAction
 *
 * @author orangehrm
 */
class sampleCsvDownloadAction extends sfAction {

	public function execute($request) {

		$response = $this->getResponse();
		$response->setHttpHeader('Pragma', 'public');
		$response->setHttpHeader("Content-type", "application/csv");
		$response->setHttpHeader("Content-Disposition", "attachment; filename=Synerzip_PIM_Import_Data.csv");
		$response->setHttpHeader('Expires', '0');
		//$content = "first_name,middle_name,last_name,employee_id,other_id,driver's_license_no,license_expiry_date,gender,marital_status,nationality,date_of_birth,address_street_1,address_street_2,city,state/province,zip/postal_code,country,home_telephone,mobile,work_telephone,work_email,other_email";
		$content = "employee_id,first_name,middle_name,last_name,other_id,bld_grp,date_of_birth,gender,marital_status,nationality,father_name,husband_name,address_street_1,address_street_2,city,state/province,zip/postal_code,country,permanent_address,synerzip_email,project_email,personal_email,mobile,work_phone,emergency_no,access_code,skype_id,joining_date,designation,total_experience,current_experience,notice_period,project_name,referred_by,linkedIn_url,CTC,currency,icici_account_number,epf_number,hdfc_meal_card,pan_number,passport_number,place_of_issue,expiry_date,visa_type,visa_validate_date,Dependent1,DOB,Relation_type(child/other),Relation,Dependent2,DOB,Relation_type(child/other),Relation,Dependent3,DOB,Relation_type(child/other),Relation,UserName,Password";
		$response->setHttpHeader("Content-Length", strlen($content));
		$response->setContent($content);

		return sfView::NONE;
	}
}

?>
