<?php use_stylesheet(plugin_web_path('orangehrmLeavePlugin', 'css/viewLeaveBalanceReport')); ?>
<?php use_stylesheet(plugin_web_path('orangehrmLeavePlugin', 'css/calender/fullcalendar.css')); 
    use_stylesheet(plugin_web_path('orangehrmLeavePlugin', 'css/calender/cupertino/jquery-ui.min.css'));
    use_javascript(plugin_web_path('orangehrmLeavePlugin','js/calender/moment.min.js'));
    use_javascript(plugin_web_path('orangehrmLeavePlugin','js/calender/fullcalendar.js'));
    use_stylesheet(plugin_web_path('orangehrmCorePlugin','css/showDashboardSuccess.css'));
    use_javascript(plugin_web_path('orangehrmCorePlugin','js/showDashboardSuccess.js'));
?>

<input name="hdnPageNo" type="hidden"
	value="<?php echo isset($clues['pageNo']) ? $clues['pageNo'] : '' ?>">
<div id="content">
<div class="box pimPane">
<?php
if (! $_SESSION ['isConsultant']) {
	?>
<div style="width:100%; height:100%;">
<table width=100%>
<tr><td width=78% style="max-width: 78%;  vertical-align: top;">
<div style="width:100%; height:100%; float: left;">
<!-- Calender -->
<div style="float:left; width:34%">
<fieldset id="panel-calender">
			<legend>
				<h2>My Calender</h2>
			</legend>
			<div style='float:right; margin-bottom: 2%; margin-right: 2%;'>
<?php 
foreach (LeaveType::$leaveTypeShort as $key=>$value): 
        preg_match_all('/\b\w/', $key, $match);
        echo "<label class='".strtolower(preg_replace('/\s/', '_', $key))."'><a href='javascript:void();' title='".$key."' >".$value."</a></label>\t";
    endforeach;	?>
    </div>
    
	<div id='calendar'></div>
	</fieldset>
</div>
<!-- end calender -->

<!-- Start of Panel for My Leave Balance -->
<div id="myleave" > 
        <fieldset id="panel-2">
			<legend>
				<h2 class='myleaveB'>My Leave Balance</h2>
			</legend>
			<table class="table" style="width:100%">
			 <thead>
			 <tr>
			 <th>Leave Type</th><th>Leave Balance (Days)</th>
			 </thead>
			 </tr>
			 <tbody>
			 <tr class="odd"><td align="left">Paid leave</td><td align="center"><?php echo (float)$paidLeaveBal;?></td></tr>
			 <tr><td align="left">Work from Home (Monthly)</td><td align="center"><?php echo (float)$WFHBalForMonth;?></td></tr>
			 </tbody>
			</table>
        </fieldset>
</div>
    	<!-- End of Panel for My Leave Balance -->
    	
<!--  Start: List of Current Interviews (Today's and next 3 days) -->
<div id="divpanel-3">
<fieldset id="panel-3">
			<legend>
				<h2>Upcoming Interview List</h2>
			</legend>
			
			<table class="table">
					<thead>
					<tr>
						<th class="header">Candidate Name</th>
						<th class="header">Vacancy</th>
						<th class="header">Interview Date</th>
						<th class="header">Interview Time</th>
					</tr>
					</thead>
					<tbody id="interviewList">
					
					</tbody>
			</table>
			
</fieldset>
</div>

<!--  Start: List of pending feedback (past 3 months) -->
<div id="pendingfeedback">
<fieldset id="panel-3-1">
			<legend>
				<h2>Fill Pending Feedback 
				<blink> (Pending) </blink>   
				</h2> 
			</legend>
			<table class="table">
					<thead>
					<tr>
						<th class="header">Candidate Name</th>
						<th class="header">Vacancy</th>
					</tr>
					</thead>
					<tbody id="pendingFeedbackList">
					</tbody>
			</table>
			
</fieldset>
</div>

</div>
</td>
<td width=21% style="max-width: 21%; vertical-align: top;">
<div style="width:100%; height:100%; float: right;" >

<fieldset id="eventpanel">
<legend ><h2>Upcoming Events</h2> </legend>

<!-- Start of Panel for Birthday -->
<div id="Birthday">
<fieldset id="panel-birthday">
			<legend>
				<h2>Today's Birthday</h2>
			</legend>
			<div >
			<?php  
			for($i=0;$i<$empBirthCount;$i++)
			{  
			$path = sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/pim/viewPhoto/empNumber/'.$empBirthList[$i]['emp_number'].'?from=empDir';
			?>
			<div style="float:left; margin-bottom:3%; margin-right:1%; max-width:48%; margin-left:1%;">
			<img src="<?php echo $path; ?>" width="100" height="100" >
			<?php 
			echo '<br><strong> &nbsp;'.$empBirthList[$i]['emp_firstname'].' '.$empBirthList[$i]['emp_lastname'].'</strong></div>';
			}?>
			</div>
</fieldset>
</div>
<!-- Start of Panel for Upcoming Public Holidays -->
<div id="Pholiday">
<fieldset id="panel-rightpholiday">
			<legend>
				<h2>Upcoming Public Holiday's</h2>
			</legend>
			<table class="table">
			<tbody id="publicholidays">
			</tbody>
			</table>			
</fieldset>
</div>
<!-- End of Panel for Upcoming Public Holidays -->
<!-- Start of Panel for Upcoming Trainings -->
<div id="Training">
<fieldset id="panel-training">
			<legend>
				<h2>Upcoming Training</h2>
			</legend>
			<table class="table">
			<thead>
					<tr>
						<th class="header">Topic</th>
						<th class="header">Details</th>
					</tr>
					</thead>
			<tbody id="publictraining">
			</tbody>
			</table>			
</fieldset>
</div>
<!-- End of Panel for Upcoming Public Holidays -->

</fieldset>
</div>
</div>
</div>
</td>
</table>
</div>	
<?php
}else { $con = $_SESSION['isConsultant'];?>
<!-- Start of consultant Dashboard  -->
<fieldset id="panel_resizable_2_2"
			class="panel_resizable panel-preview"
			style="border: 2px solid #fc9a24; margin-top: 0px;">
			<legend style="margin-left: 30px;">
				<h2>Recruitment Summary</h2>
			</legend>

			<div id="divagencySpan" style=" margin-top: 1%; margin-bottom: 1%; text-align: center;"><span align="center" id="agencySpan" > </span></div>

			<div id="task-list-group-panel-container-time1"
				class="task-list-group-panel-container" style="height: 100%;">
				<div id="task-list-group-panel-menu_holder-time"
					class="task-list-group-panel-menu_holder"
					style="height: 100%; overflow-x: hidden; overflow-y: auto;">
					<div class="top">
                   
	<table class="table hover">
						<thead><tr>
								<th class="header">Vacancy</th>
								<th class="header">Screening</th>
								<th class="header">Application Initiated</th>
								<th class="header">Interview Scheduled</th>
								<th class="header">Shortlisted</th>
								<th class="header">Hold</th>
								<th class="header">Interview Passed</th>
								<th class="header">Interview Failed</th>
								<th class="header">Job Offered</th>
								<th class="header">Rejected</th>
							</tr>
						</thead>
   						<tbody id="agencydata">
   						</tbody>
 	</table>
	</div>
		</div>
			</div>
	</fieldset> 

<?php } ?>
</div>
</div>

<?php $calData = array();
foreach($leaveEventList as $intra_cal): 
    $calData[] = array(
         'id'     => $intra_cal['id'],
         'type'   => $intra_cal['type'],
         'title'  => htmlentities($intra_cal['title']),
         'start'  => $intra_cal['start'],
         'end'    => $intra_cal['end'],
         'status' => $intra_cal['status'],
         'url'    => url_for('leave/viewLeaveRequest?id=').$intra_cal['id'],
         );
        endforeach; 
$event = preg_replace('/"([a-zA-Z_]+[a-zA-Z0-9_]*)":/','$1:',json_encode($calData, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
?>
<script type="text/javascript">
var consultant = '<?php echo $_SESSION['isConsultant']; ?>';
var ajaxUrlagency = '<?php echo sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/core/agencyCandidatesReffered' ?>';
var hiringmanager = '<?php echo $_SESSION['isHiringManager'];?>';
var interviewer = '<?php echo $_SESSION['isInterviewer'];?>';
var ajaxUrlinterviewsHiringMgr = '<?php echo sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/core/currentInterviewsHiringMgr' ?>';
var ajaxUrlinterviewsInterviewer = '<?php echo sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/core/currentInterviewsInterviewer' ?>';
var empCount = "<?php echo $empBirthCount; ?>";
var Urlpholiday = '<?php echo sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/core/publicHolidays' ?>';
var Urltraining = '<?php echo sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/core/upcomingTraining' ?>';
var ajaxUrlfeedbackHiringMgr = '<?php echo sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/core/pendingFeedbackHiringMgr' ?>';
var ajaxUrlfeedbackInterviewer = '<?php echo sfContext::getInstance()->getRequest()->getUriPrefix().sfContext::getInstance()->getRequest()->getRelativeUrlRoot().'/index.php/core/pendingFeedbackInterviewer' ?>';
var eventObj = <?php echo $event; ?>

</script>