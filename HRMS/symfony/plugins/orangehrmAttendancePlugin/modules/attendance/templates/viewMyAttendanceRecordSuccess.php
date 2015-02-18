
<?php echo javascript_include_tag(plugin_web_path('orangehrmAttendancePlugin', 'js/viewMyAttendanceRecordSuccess')); ?>
<?php use_stylesheet(plugin_web_path('orangehrmAttendancePlugin', 'css/viewMyAttendanceRecordSuccess.css')); ?>

<div class="box">
    <div class="head">
        <h1><?php echo __('My Attendance Records'); ?></h1>
    </div>
    <div class="inner">
        <div id="validationMsg">
            <?php echo isset($messageData) ? templateMessage($messageData) : ''; ?>
        </div>
        <form action="<?php echo url_for("attendance/viewAttendanceRecord"); ?>" id="reportForm" method="post">
            <fieldset>
                <ol class="normal">
                    <li>
                        <?php echo $form['dateRange']->renderLabel(__('Date Range')); ?>
                        <?php echo $form['dateRange']->render(); ?>
                        <?php echo $form->renderHiddenFields(); ?>
                    </li>
                </ol>
                <input type="button" id="showButton" value="Show"/>
            </fieldset>
        </form>
    </div>
</div>

<div id="recordsTable1"><!-- To appear table when search success --></div>

<script type="text/javascript">
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var displayDateFormat = '<?php echo str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())); ?>';
    var errorForInvalidFormat='<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, 
            array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var errorMsge;
    var linkForGetRecords='<?php echo url_for('attendance/getRelatedAttendanceRecords'); ?>';
    var employeeId='<?php echo $employeeId; ?>';
    var actionRecorder='<?php echo $actionRecorder; ?>';
    var dateSelected='<?php echo $date; ?>';
    var trigger='<?php echo $trigger; ?>';
    var lang_NameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_dateValidation = '<?php echo __("Should be lesser than current date"); ?>';
    var currentDate = '<?php echo set_datepicker_date_format(date("Y-m-d")); ?>';
    var flag = '<?php echo $updateOnLoad; ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';
</script>