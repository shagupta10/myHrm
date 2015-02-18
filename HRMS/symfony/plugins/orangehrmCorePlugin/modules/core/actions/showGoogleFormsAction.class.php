<?php

class showGoogleFormsAction extends sfActions {
	public function execute($request) {
		// forms vars
		$cvForm = "https://docs.google.com/a/synerzip.com/file/d/0B_vE39dgPgKsMTVLbXI4SF9hMzg";
		$cvTitle = "Curriculum Vitae";
		$ITdeclarationForm = "https://docs.google.com/a/synerzip.com/file/d/0B_vE39dgPgKsSE4yTndGM0l3Wjg";
		$ITdeclarationTitle = "IT Declaration Form";
		$expenseVoucherForm = "https://docs.google.com/a/synerzip.com/file/d/0ByLqPIGxd-rfWjJPZV9tWDVGZms";
		$expenseVoucherTitle = "Expense Voucher";
		$medicalLaundryForm = "https://docs.google.com/a/synerzip.com/file/d/0B_vE39dgPgKsR0dlb3hMWkh6ejg";
		$medicalLaundryTitle = "Medical And Laundry Reclaim Form";
		$lta = "https://docs.google.com/a/synerzip.com/file/d/0B_vE39dgPgKscXlSOG0tbmJHWUk";
		$ltaTitle = "Leave Travel Allowance";
		// add to formsArray
		$formsArray [$ITdeclarationForm] = $ITdeclarationTitle;
		$formsArray [$cvForm] = $cvTitle;
		$formsArray [$expenseVoucherForm] = $expenseVoucherTitle;
		$formsArray [$medicalLaundryForm] = $medicalLaundryTitle;
		$formsArray [$lta] = $ltaTitle;
		
		// policies vars
		$polAdvancePayment = "https://docs.google.com/a/synerzip.com/file/d/0B_vE39dgPgKsWXZuOTU5cW9Xb1U";
		$polAdvancePaymentTitle = "Advances to Employee";
		$polGrpMediclaim = "https://docs.google.com/a/synerzip.com/file/d/0B_vE39dgPgKsMmF4d2hXclV6U00";
		$polGrpMediclaimTitle = "Group Mediclaim";
		$polITsecurity = "https://docs.google.com/a/synerzip.com/file/d/0B_vE39dgPgKsRzVZME5DWXpEeGM";
		$polITtitle = "IT security";
		$polLapTop = "https://docs.google.com/a/synerzip.com/file/d/0B_vE39dgPgKsRlVIbkppQW8xSk0";
		$polLapTopTitle = "Laptop policy";
		$polLearnNDevlop = "https://docs.google.com/a/synerzip.com/file/d/0B_vE39dgPgKsUnRxUnlMYkw1aVE";
		$polLearnNDevlopTitle = "Learn and Develop";
		$polLeave = "https://docs.google.com/a/synerzip.com/file/d/0B_vE39dgPgKsNTZmeXo4ajVxRE0";
		$polLeaveTitle = "Leave";
		$polLTA = "https://docs.google.com/a/synerzip.com/file/d/0B_vE39dgPgKsUUpqaVdaV0UzcG8";
		$polLTATitle = "LTA and Mediclaim";
		$polLibrary = "https://docs.google.com/a/synerzip.com/file/d/0B_vE39dgPgKsMUNrZnowRTdnc2s";
		$polLibraryTitle = "Library";
		$polRefferal = "https://docs.google.com/a/synerzip.com/file/d/0B_vE39dgPgKsY1BXRFdmSVZaUTg";
		$polRefferalTitle = "Refferal";
		$polExit = "https://docs.google.com/a/synerzip.com/file/d/0B_vE39dgPgKsMEFmYVJWSHBGRzA";
		$polExitTitle = "Separation";
		$polWFH = "https://docs.google.com/a/synerzip.com/file/d/0B_vE39dgPgKsT3VicGdCeEZsSDA";
		$polWFHTitle = "Work from Home";
		// Add to policies array
		$policiesArray [$polAdvancePayment] = $polAdvancePaymentTitle;
		$policiesArray [$polGrpMediclaim] = $polGrpMediclaimTitle;
		$policiesArray [$polITsecurity] = $polITtitle;
		$policiesArray [$polLapTop] = $polLapTopTitle;
		$policiesArray [$polLearnNDevlop] = $polLearnNDevlopTitle;
		$policiesArray [$polLeave] = $polLeaveTitle;
		$policiesArray [$polLTA] = $polLTATitle;
		$policiesArray [$polLibrary] = $polLibraryTitle;
		$policiesArray [$polRefferal] = $polRefferalTitle;
		$policiesArray [$polExit] = $polExitTitle;
		$policiesArray [$polWFH] = $polWFHTitle;
		
		// Set formsArray and policies array to successpage.
		$this->formsArray = $formsArray;
		$this->policiesArray = $policiesArray;
	}
}
