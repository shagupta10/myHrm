<?php use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/workShiftSuccess')); ?>
<script type="text/javascript"> var recordsPerpage = '<?php echo $recordsLimit; ?>'; </script>
<div id="workShift" class="box">
    
    <div class="head">
        <h1 id="workShiftHeading"><?php echo __("Work Shift"); ?></h1>
    </div>
    
    <div class="inner">

        <form name="frmWorkShift" id="frmWorkShift" method="post" action="<?php echo url_for('admin/workShift'); ?>" >

            <?php echo $form['_csrf_token']; ?>
            <?php echo $form->renderHiddenFields(); ?>
            
            <fieldset>
                
                <ol>                    
                    <li>
                        <?php echo $form['name']->renderLabel(__('Shift Name'). ' <em>*</em>'); ?>
                        <?php echo $form['name']->render(array("maxlength" => 52)); ?>
                    </li>
                    <li>
                        <?php echo $form['hours']->renderLabel(__('Hours Per Day'). ' <em>*</em>'); ?>
                        <?php echo $form['hours']->render(); ?>
                    </li>
                    <p id="selectManyTable">
                        <table border="0" width="45%" class="">
                            <tbody>
                                <tr>
                                    <td width="35%" style="font-weight:bold; height: 20px">
                                        <?php echo __("Available Employees"); ?>
                                    </td>
                                    <td width="30%"></td>
                                    <td width="35%" style="font-weight:bold;"><?php echo __("Assigned Employees"); ?></td>
                                </tr>
                                <tr>
                                    <td>
                                        <?php echo $form['availableEmp']->render(array("class" => "selectMany", "size" => 10, "style" => "width: 100%")); ?>	
                                    </td>
                                    <td align="center" style="vertical-align: middle">
                                        <!--
                                        <input type="button" style="width: 70%;" value="<?php echo __("Add"). " >"; ?>" class="" id="btnAssignEmployee" name="btnAssignEmployee">
                                        <input type="button" style="width: 70%;" value="<?php echo "< ".__("Remove"); ?>" class="delete" id="btnRemoveEmployee" name="btnRemoveEmployee">
                                        -->
                                        <a href="#" class="" id="btnAssignEmployee"><?php echo __("Add"). " >>"; ?></a><br /><br />
                                        <a href="#" class="delete" id="btnRemoveEmployee"><?php echo __("Remove") . " <<"; ?></a>
                                    </td>
                                    <td>
                                        <?php echo $form['assignedEmp']->render(array("class" => "selectMany", "size" => 10, "style" => "width: 100%")); ?>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </p>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>                    
                </ol>
                
                <p>
                    <input type="button" class="" name="btnSave" id="btnSave" value="<?php echo __("Save"); ?>"/>
                    <input type="button" class="reset" name="btnCancel" id="btnCancel" value="<?php echo __("Cancel"); ?>"/>
                </p>
                
            </fieldset>
            
        </form>
        
    </div>
    
</div>

<div id="customerList">
    <?php include_component('core', 'ohrmList', $parmetersForListCompoment); ?>
</div>

<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="deleteConfModal">
    <div class="modal-header">
        <a class="close" data-dismiss="modal">×</a>
        <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
    </div>
    <div class="modal-body">
        <p><?php echo __(CommonMessages::DELETE_CONFIRMATION); ?></p>
    </div>
    <div class="modal-footer">
        <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" />
        <input type="button" class="btn reset" data-dismiss="modal" value="<?php echo __('Cancel'); ?>" />
    </div>
</div>
<!-- Confirmation box HTML: Ends -->
<form name="frmHiddenParam" id="frmHiddenParam" method="post" action="<?php echo url_for('admin/workShift?recordsPerPage_Limit='.$recordsLimit); ?>">
    <input type="hidden" name="pageNo" id="pageNo" value="<?php //echo $form->pageNo;   ?>" />
    <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
</form>
<script type="text/javascript">
	var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson());?>;
    function submitPage(pageNo) {
		document.frmHiddenParam.pageNo.value = pageNo;
		document.frmHiddenParam.hdnAction.value = 'paging';
		document.getElementById('frmHiddenParam').submit();
	}
    if (employees == null) {
        var employeeList = new Array();
    } else {
        var employeeList = eval(employees);
    }
    
	var workShifts = <?php echo str_replace('&#039;', "'", $form->getWorkShiftListAsJson());?>;
	var workShiftList = eval(workShifts);
	var lang_NameRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	var lang_exceed50Charactors = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 50)); ?>';
	var lang_hoursRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
	var lang_notNumeric = '<?php echo __("Should be a positive number"); ?>';
	var lang_addWorkShift = "<?php echo __("Add Work Shift"); ?>";
	var lang_editWorkShift = "<?php echo __("Edit Work Shift"); ?>";
	var lang_possitiveNumber = "<?php echo __("Should be a positive number"); ?>";
	var lang_lessThan24 = '<?php echo __("Should be less than %amount%", array("%amount%" => '24')); ?>';
	var lang_nameAlreadyExist = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
	var workShiftInfoUrl = "<?php echo url_for("admin/getWorkShiftInfoJson?id="); ?>";
	var workShiftEmpInfoUrl = "<?php echo url_for("admin/getWorkShiftEmpInfoJson?id="); ?>";	
</script>