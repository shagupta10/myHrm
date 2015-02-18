<?php  
$haveCertifications = count($form->empCertificationList)>0;
?>
       
<a name="certification"></a>
<?php if ($certificationPermissions->canCreate() || ($haveCertifications && $certificationPermissions->canUpdate())) { ?>
<div id="changeCertification">
    <div class="head">
        <h1 id="headChangeCertification"><?php echo __('Add Certification'); ?></h1>
    </div>
                
    <div class="inner">
        <form id="frmCertification" name="frmCertification" enctype="multipart/form-data" action="<?php echo url_for('pim/saveDeleteCertification?empNumber=' . 
                $empNumber . "&option=save"); ?>" method="post">
            <?php echo $form['_csrf_token']; ?>
			<?php echo $form['approve'];?>
            <?php echo $form['emp_number']->render(); ?>
			           <?php echo $form['certification_id']->render(); ?>
            <fieldset>
                <ol>
                    <li>
                        <?php echo $form['name']->renderLabel(__('Certification Name') . ' <em>*</em>'); ?>
                        <?php echo $form['name']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
                    </li>
                    <li>
                        <?php echo $form['institute']->renderLabel(__('Institute') . ' <em>*</em>'); ?>
                        <?php echo $form['institute']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
                    </li>
					<li class = "fileSelector">
						<?php 
							echo $form['cattach']->renderLabel(__('Attachment'. ' <span class="required">*</span>'), array("class " => "resume"));
							echo $form['cattach']->render();
							echo '<div style ="float:left;padding-top:7px;margin-left:10px;">' . __(CommonMessages::FILE_LABEL_DOC) . '</div>';
						?>					
					</li>
					<li>
					<span  style="font-weight: bold;margin-left:250px;">OR</span>
					<span style="color:#aa4935;margin-left:127px" id="linkValidation" for="certification_link" generated="true"></span>
					</li>
					<li>
						 <?php echo $form['certification_link']->renderLabel(__('Certification Link') . ' <em>*</em>'); ?>
                        <?php echo $form['certification_link']->render(array("class" => "formInputText", "maxlength" => 100)); ?>
						
					</li>
                   <li>
                        <?php echo $form['date']->renderLabel(__('Certification Date') . ' <em>*</em>'); ?>
                        <?php echo $form['date']->render(); ?>
						<span style="color:#aa4935;" id="dateValidation" for="certification_institute" generated="true"></span>
                    </li>
					<li>
						<?php echo $form['grade']->renderLabel(__('Grade/Percentile')); ?>
                        <?php echo $form['grade']->render(); ?>
					</li>
                    <li class="required">
                        <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                    </li>
                </ol>
                <p>
                    <input type="button" class="" id="btnCertificationSave" value="<?php echo __("Save"); ?>" />
                    <?php if ((!$haveCertifications) || ($haveCertifications && $certificationPermissions->canCreate()) || 
                            ($certificationPermissions && $certificationPermissions->canUpdate())) { ?>
                    <input type="button" class="reset" id="btnCertificationCancel" value="<?php echo __("Cancel"); ?>" />
                    <?php } ?>
                </p>
            </fieldset>
        </form>
    </div>
</div> <!-- changeCertification -->
<?php } ?>
        
<div class="miniList" id="tblCertification">
    <div class="head">
        <h1><?php echo __("Certifications"); ?></h1>
    </div>
            
    <div class="inner">

        <?php if ($certificationPermissions->canRead()) : ?>
        <?php include_partial('global/flash_messages', array('prefix' => 'certification')); ?>

        <form id="frmDelCertification" name="frmDelCertification" action="<?php echo url_for('pim/saveDeleteCertification?empNumber=' . 
                $empNumber . "&option=delete"); ?>"  method="post">
            <p id="actionCertification">
                <?php if ($certificationPermissions->canCreate() ) { ?>
                <input type="button" value="<?php echo __("Add");?>" class="" id="addCertification" />
                <?php } ?>
                <?php if ($certificationPermissions->canDelete() ) { ?>
                <input type="button" value="<?php echo __("Delete");?>" class="delete" id="delCertification" />
                <?php } ?>
            </p>
            <table id="" cellpadding="0" cellspacing="0" width="100%" class="table tablesorter">
                <thead>
                    <tr>
                        <?php if ($certificationPermissions->canDelete()) { ?>
                        <th class="check" width="2%"><input type="checkbox" id="certificationCheckAll" /></th>
                        <?php } ?>
                        <th><?php echo __('Certification'); ?></th>
                        <th><?php echo __('Date'); ?></th>
						<th><?php echo __('Institute'); ?></th>						
						<th><?php echo __('Grade'); ?></th>
						<th><?php echo __('Attachment'); ?></th>
						<th><?php echo __('Certification Link'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (!$haveCertifications) { ?>
                    <tr>
                        <?php if ($certificationPermissions->canDelete()) { ?>
                        <td class="check"></td>
                        <?php } ?>
                        <td><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                        <td></td>
                    </tr>
                    <?php } else { ?>                        
                    <?php
					
                    $certifications = $form->empCertificationList;
                    $row = 0;
					 
                    foreach ($certifications as $certification) :
                        $cssClass = ($row % 2) ? 'even' : 'odd';                 
						$certificationName = $certification->name;						
                        ?>
                        <tr class="<?php echo $cssClass; ?>">
                            <td class="check">
								<input type="hidden" id="approve_<?php echo $certification->certificationId; ?>" 
                                       value="<?php echo htmlspecialchars($certification->approve); ?>" />
							    <input type="hidden" id="certificate_id_<?php echo $certification->certificationId; ?>" 
                                       value="<?php echo htmlspecialchars($certification->certificationId); ?>" />
                                <input type="hidden" id="name_<?php echo $certification->certificationId; ?>" 
                                       value="<?php echo htmlspecialchars($certificationName); ?>" />
                                <input type="hidden" id="institute_<?php echo $certification->certificationId; ?>" 
                                       value="<?php echo htmlspecialchars($certification->institute); ?>" />
                                <input type="hidden" id="date_<?php echo $certification->certificationId; ?>" 
                                       value="<?php echo htmlspecialchars($certification->date); ?>" />
							    <input type="hidden" id="grade_<?php echo $certification->certificationId; ?>" 
                                       value="<?php echo htmlspecialchars($certification->grade); ?>" />								
								<input type="hidden" id="cattach_<?php echo $certification->certificationId; ?>" 
                                       value="<?php echo htmlspecialchars($certification->cattach); ?>" />
								 <input type="hidden" id="certificate_link_<?php echo $certification->certificationId; ?>" 
                                       value="<?php echo htmlspecialchars($certification->certificationLink); ?>" />
                                <?php if ($certificationPermissions->canDelete()) { ?>
                                <input type="checkbox" class="chkbox" value="<?php echo $certification->certificationId; ?>" name="delCertification[]"/>
                                <?php } else { ?>
                                <input type="hidden" class="chkbox" value="<?php echo $certification->certificationId; ?>" 
                                       name="delCertification[]"/>
                                <?php } ?>
                            </td>
                            <td class="name">
                                <?php if($certification->approve == EmployeeCertification::CERTIFICATE_APPROVE) { ?>
									<?php if ($certificationPermissions->canUpdate()) { ?>
									<?php echo htmlspecialchars($certificationName); ?>
									<?php
									} else {
										echo htmlspecialchars($certificationName);
									}
									?>
								<?php } else { ?>
											<?php if ($certificationPermissions->canUpdate()) { ?>
											<span style="margin-right:20px" class="edit"><?php echo $certificationName; ?></a>
											<span style="color: #aa4935;font-weight: bold;"><?php echo "Pending Approval"; ?></span>
											<?php
											} else {
												echo htmlspecialchars($certificationName); ?>
											<span style="color: #aa4935;font-weight: bold;"><?php echo "Pending Approval"; ?></span>	
											<?php }
										}
									?>
									
                            </td>
                            <td><?php echo htmlspecialchars($certification->date); ?></td>
							<td><?php echo htmlspecialchars($certification->institute); ?></td>
							<td><?php echo htmlspecialchars($certification->grade); ?></td>
							<td><a href="<?php echo sfContext::getInstance()->getRequest()->getRelativeUrlRoot()."/index.php/pim/saveDeleteCertification?id={$certification->certificationId}"; ?>"><?php echo $certification->cattach_name; ?></a></td>
							<td><?php echo htmlspecialchars($certification->certificationLink); ?></td>
                        </tr>
                        <?php
                        $row++;
                    endforeach;
                    }
                    ?>
                </tbody>
            </table>
        </form>

        <?php else : ?>
            <div><?php echo __(CommonMessages::DONT_HAVE_ACCESS); ?></div>
        <?php endif; ?>

    </div>
</div> <!-- miniList-tblCertification -->

<script type="text/javascript">
    //<![CDATA[
    var fileModified = 0;
    var lang_addCertification = "<?php echo __('Add Certification'); ?>";
    var lang_editCertification = "<?php echo __('Edit Certification'); ?>";
    var lang_certificationRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_selectCertificationToDelete = "<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>";
    var lang_commentsMaxLength = "<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 100)); ?>";
    var lang_yearsOfExpShouldBeNumber = "<?php echo __('Should be a number'); ?>";
    var lang_yearsOfExpMax = "<?php echo __("Should be less than %amount%", array("%amount%" => '100')); ?>";
    var canUpdate = '<?php echo $certificationPermissions->canUpdate(); ?>';
    //]]>
</script>	

<script type="text/javascript">
    //<![CDATA[
    
    $(document).ready(function() {
        //To hide unchanged element into hide and show the value in span while editing
        $('#certification_id').after('<span id="static_certification_id" style="display:none;"></span>');

        function addEditLinks() {
            // called here to avoid double adding links - When in edit mode and cancel is pressed.
            removeEditLinks();
            $('form#frmDelCertification table tbody td.name').wrapInner('<a class="edit" href="#"/>');
        }
        
        function removeEditLinks() {
            $('form#frmDelCertification table tbody td.name a.edit').each(function(index) {
                $(this).parent().text($(this).text());
            });
        }
		
		
        //hide add section
        $("#changeCertification").hide();
        $("#certificationRequiredNote").hide();
        
        //hiding the data table if records are not available
        if($("div#tblCertification .chkbox").length == 0) {
            //$("#tblCertification").hide();
            $('div#tblCertification .check').hide();
            $("#editCertification").hide();
            $("#delCertification").hide();
        }
        
        //if check all button clicked
        $("#certificationCheckAll").click(function() {
            $("div#tblCertification .chkbox").removeAttr("checked");
            if($("#certificationCheckAll").attr("checked")) {
                $("div#tblCertification .chkbox").attr("checked", "checked");
            }
        });
        
        //remove tick from the all button if any checkbox unchecked
        $("div#tblCertification .chkbox").click(function() {
            $("#certificationCheckAll").removeAttr('checked');
            if($("div#tblCertification .chkbox").length == $("div#tblCertification .chkbox:checked").length) {
                $("#certificationCheckAll").attr('checked', 'checked');
            }
        });
        
        $("#addCertification").click(function() {
            
            //removeEditLinks();
            clearMessageBar();
            $('div#changeCertification label.error').hide();        
            
            //changing the headings
            $("#headChangeCertification").text(lang_addCertification);
            $("div#tblCertification .chkbox").hide();
            $("#certificationCheckAll").hide();
            
            //hiding action button section
            $("#actionCertification").hide();
            
            $('#static_certification_id').hide().val("");
            $("#certification_id").show().val("");
            $("#certification_id option[class='added']").remove();
            $("#certification_major").val("");
            $("#certification_year").val("");
            $("#certification_gpa").val("");
            
            //show add form
            $("#changeCertification").show();
            $("#certificationRequiredNote").show();
        });
        
        //clicking of delete button
        $("#delCertification").click(function(){
            
            clearMessageBar();
            
            if ($("div#tblCertification .chkbox:checked").length > 0) {
                $("#frmDelCertification").submit();
            } else {
                $("#certificationMessagebar").attr('class', 'messageBalloon_notice').text(lang_selectCertificationToDelete);
            }
            
        });
        
        $("#btnCertificationSave").click(function() {
            clearMessageBar();
			
            // Certification Date validation			
			certDate = new Date($('#certDate').val()).getTime();
			cDate = new Date().getTime();
			
			if(certDate > cDate){
				$("#dateValidation").text("Date should not be future date");
			} else {
				$("#frmCertification").submit();
			}    
			
			if($("#certification_cattach").val() == "" && $("#certification_certification_link").val() == "") {
				$("#linkValidation").text("Certification Proof is required");
			} else {
				$("#linkValidation").text("");
			}
        });
        
        //form validation
        var certificationValidator =
            $("#frmCertification").validate({
            rules: {
                'certification[name]': {required: true,  maxlength: 99},
                'certification[institute]': {required: true, maxlength:100},
				'certification[date]': {required: true},
				'certification[grade]': {required: false, maxlength:100},				
            }
        });
        
        $("#btnCertificationCancel").click(function() {
            clearMessageBar();
            /*if(canUpdate){
                addEditLinks();
            }*/
            
            certificationValidator.resetForm();
            
            $('div#changeCertification label.error').hide();
            
            $("div#tblCertification .chkbox").removeAttr("checked").show();
            
            //hiding action button section
            $("#actionCertification").show();
            $("#changeCertification").hide();
            $("#certificationRequiredNote").hide();        
            $("#certificationCheckAll").show();
            
            // remove any options already in use
            $("#certification_id option[class='added']").remove();
            $('#static_certification_code').hide().val("");
            
            //remove if disabled while edit
            $('#certification_id').removeAttr('disabled');
        });
        
        $('form#frmDelCertification a.edit').live('click', function(event) {
            
			event.preventDefault();
            clearMessageBar();
            
            //changing the headings
            $("#headChangeCertification").text(lang_editCertification);
            
            certificationValidator.resetForm();
            
            $('div#changeCertification label.error').hide();
            
            //hiding action button section
            $("#actionCertification").hide();
            
            //show add form
            $("#changeCertification").show();
            var code = $(this).closest("tr").find('input.chkbox:first').val();
            
            $('#static_certification_id').text($("#certification_name_" + code).val()).show();
            
            
            
            // remove any options already in use
            $("#certification_id option[class='added']").remove();
            
            $('#certification_id').
                append($("<option class='added'></option>").
                attr("value", code).
                text($("#certification_name_" + code).val())); 
            $('#certification_certification_id').val(code).hide();
            $("#certification_name").val($("#name_" + code).val());
            $("#certification_approve").val($("#approve_" + code).val());
            $("#certification_institute").val($("#institute_" + code).val());
			$("#certDate").val($("#date_" + code).val());
			$("#certification_grade").val($("#grade_" + code).val());
            
            $("#certificationRequiredNote").show();
            
            $("div#tblCertification .chkbox").hide();
            $("#certificationCheckAll").hide();        
        });
    });
    
    //]]>
</script>