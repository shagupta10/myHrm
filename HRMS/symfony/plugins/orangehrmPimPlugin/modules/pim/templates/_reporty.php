<?php
/**
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
?>

<?php 
use_javascript(plugin_web_path('orangehrmPimPlugin', 'js/viewReportToDetailsSuccess'));

$numSupDetails = count($supDetails);
$hasSupDetails = $numSupDetails > 0;
$numSubDetails = count($subDetails);
$hasSubDetails = $numSubDetails > 0;
$allowDel = true;
$allowEdit = true;
?>


<!-- Confirmation box HTML: Begins -->
<div class="modal hide" id="deleteConfModal">
  <div class="modal-header">
    <a class="close" data-dismiss="modal">×</a>
    <h3><?php echo __('OrangeHRM - Confirmation Required'); ?></h3>
  </div>
  <div class="modal-body">
    <p><?php echo __('Change primary reviewer?'); ?></p>
  </div>
  <div class="modal-footer">
    <input type="button" class="btn" data-dismiss="modal" id="dialogDeleteBtn" value="<?php echo __('Ok'); ?>" />
    <input type="button" class="btn reset" data-dismiss="modal" id="dialogCancelBtn" value="<?php echo __('Cancel'); ?>" />
  </div>
</div>
<!-- Confirmation box HTML: Ends -->

   
    <?php // if ($Permissions->canCreate() || ($have && $Permissions->canUpdate())) { ?>
    <?php if ($reportToPermissions->canCreate()) { ?>
        <div id="addPaneReportTo">
            <div class="head">
                <h1 id="reportToHeading"><?php echo __('Add Supervisor/Subordinate'); ?></h1>
            </div>

            <div class="inner">
                <form name="frmAddReportTo" id="frmAddReportTo" method="post" 
                      action="<?php echo url_for('pim/updateReportToDetail?empNumber=' . $empNumber); ?>">
                    <?php echo $form['_csrf_token']; ?>
                    <?php echo $form["empNumber"]->render(); ?>
                    <input type="hidden" name="reportto[returnUrl]" id="reportto_returnUrl" value="changeProject">
                    <?php echo $form["previousRecord"]->render(); ?>
                    <fieldset>
                        <ol>
                            <li>
                                <?php echo $form['type_flag']->render(); ?>
                            </li>
                            <li>
                                <?php echo $form['supervisorName']->renderLabel(__('Name') . ' <em>*</em>'); ?>
                                <?php echo $form['supervisorName']->render(array("class" => "txtBoxR name", "maxlength" => 92)); ?>
                                <?php echo $form['subordinateName']->renderLabel(__('Name') . ' <em>*</em>'); ?>
                                <?php echo $form['subordinateName']->render(array("class" => "txtBoxR name", "maxlength" => 92)); ?>
                            </li>
                            <li>
                                <?php echo $form['reportingMethodType']->renderLabel(__('Reporting Method') . ' <em>*</em>'); ?>
                                <?php echo $form['reportingMethodType']->render(array("class" => "drpDownR", "maxlength" => 50)); ?>
                            </li>
                            <li id="pleaseSpecify">
                                <?php echo $form['reportingMethod']->renderLabel(__('Please Specify') . ' <em>*</em>'); ?>
                                <?php echo $form['reportingMethod']->render(array("class" => "txtBoxR", "maxlength" => 50)); ?>
                            </li>
                            <li class="required">
                               <em>*</em> <?php echo __(CommonMessages::REQUIRED_FIELD); ?>
                            </li>
                        </ol>
                        <p>    
                            <input type="button" class="" name="btnSaveReportTo" id="btnSaveReportTo" 
                                   value="<?php echo __("Save"); ?>"/>
                            <input type="button" id="btnCancel" class="reset" value="<?php echo __("Cancel"); ?>"/>
                        </p>
                    </fieldset>
                </form>
            </div>
        </div> <!-- addPaneReportTo-adding-sub/sup -->
    <?php } ?>

    <div id="listReportToDetails">
        
        <div class="miniList" id="listReportToSupDetails">
            <div class="head">
                <h1><?php echo __("Assigned Supervisors"); ?></h1>
            </div>

            <div class="inner">
            	<?php include_partial('global/flash_messages', array('prefix' => 'primary')); ?>
                <?php if ($reportToSupervisorPermission->canRead()) : ?>

                    <?php 
                    if ($section == 'supervisor') {
                        include_partial('global/flash_messages'); 
                    } 
                    ?>
                    <form name="frmEmpDelSupervisors" id="frmEmpDelSupervisors" method="post" 
                          action="<?php echo url_for('pim/deleteReportToSupervisor?empNumber=' . $empNumber); ?>">
                        <?php echo $deleteSupForm['_csrf_token']->render(); ?>
                        <?php echo $deleteSupForm['empNumber']->render(); ?>
                        <input type="hidden" name="supDelete[returnUrl]" id="supDelete_returnUrl" value="changeProject">
                        <p id="supListActions">
                            <?php if ($reportToSupervisorPermission->canCreate()) { ?>
                            <input type="button" class="" id="btnAddSupervisorDetail" value="<?php echo __("Add"); ?>"/>
                            <?php } ?>
                            <?php if ($reportToSupervisorPermission->canDelete()) { ?>
                            <input type="button" class="delete" id="delSupBtn" value="<?php echo __("Delete"); ?>" />
                            <?php } ?>
                        </p>
                        <table id="sup_list" class="table hover">
                            <thead>
                                <tr>
                                    <?php if ($reportToSupervisorPermission->canDelete()) { ?>
                                    <th class="check" style="width:2%"><input type='checkbox' id='checkAllSup' class="checkboxSup" /></th>
                                    <?php }?>
                                    <th class="supName"><?php echo __("Name"); ?></th>
                                    <th class="supReportMethod"><?php echo __("Reporting Method"); ?></th>
                                     <!-- <?php //if ($reportToSupervisorPermission->canDelete()) { ?> -->
                                    <th  style="width:2%"><?php echo __("Primary Reviwer"); ?></th>
                                   <!-- <?php //}                         
                                    ?>-->
                                    
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$hasSupDetails) { ?>
                                    <tr>
                                        <td class="check"></td>
                                        <td><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                                        <td></td>
                                         <td></td>
                                    </tr>
                                <?php } else { ?>                        
                                    <?php
                                    $supRow = 0;
                                    foreach ($supDetails as $sup) :
                                        $cssClass = ($supRow % 2) ? 'even' : 'odd';
                                        echo '<tr class="' . $cssClass . '">';
                                        $supChkBoxValue = $sup->getSupervisorId() . " " . $empNumber . " " . 
                                                $sup->getReportingMethodId();
                                        $supOptBoxValue =$sup->getSupervisorId();
                                        $selectOpt=($supOptBoxValue==$primaryReviewer)? 'checked' : '';
                                        if ($reportToSupervisorPermission->canDelete()) {
                                            echo "<td class='check'>
                                                <input type='checkbox' class='checkboxSup' name='chksupdel[]' 
                                                value='" . $supChkBoxValue . "'/></td>";                                          
                                        } else { ?>
                                            <input type='hidden' class='checkboxSup' name='chksupdel[]' 
                                                   value='<?php echo $supChkBoxValue; ?>'/>                                            
                                        <?php
                                        }                                        
                                        $supervisor = $sup->getSupervisor();
                                        $terminationId = $supervisor->getTerminationId();
                                        $suffix = (!empty($terminationId)) ? " (" . __('Past Employee') . ")" : "";
                                        $supName = $supervisor->getFirstName() . " " . $supervisor->getLastName() . $suffix;
                                        ?>
                                        <?php $supReportingMethodName = $sup->getReportingMethod()->getName(); ?>
                                        <?php if ($reportToSupervisorPermission->canUpdate()) { ?>
                                            <td class="supName" valign="top"><a href="#"><?php echo $supName; ?></a></td>
                                        <?php } else { ?>
                                            <td class="supName" valign="top"><?php echo $supName; ?></td>
                                            <?php
                                        }
                                        echo "<td  class='supReportMethod' valigh='top'>" . __($supReportingMethodName) . "</td>";
                                         if ($reportToSupervisorPermission->canDelete() && $sup->reportingMethodId==1) {                                          
                                            echo "<td><input  type='radio' name='optPrime' value='" . $supOptBoxValue . "'  ".$selectOpt."></td>";
                                        } else { ?>                                           
                                             <!-- <input type='hidden' class='checkboxSup' name='optPrime' 
                                                   value='<?php echo $supOptBoxValue; ?>' <?php echo $selectOpt; ?>/>-->
                                              <td> <?php echo ($selectOpt!='' || $sup->reportingMethodId==1)?"<input  type='radio' name='optPrime' value='" . $supOptBoxValue . "'  ".$selectOpt." disabled>" :"NA"; ?>
                                                  </td>
                                        <?php
                                        }
                                        echo '</tr>';
                                        $supRow++;
                                    endforeach;
                                } ?>
                            </tbody>
                        </table>
                    </form>

                <?php else : ?>
                    <div><?php echo __(CommonMessages::DONT_HAVE_ACCESS); ?></div>
                <?php endif; ?>
            </div>
        </div> 
        
        <div class="miniList" id="listReportToSubDetails">
            <div class="head">
                <h1><?php echo __("Assigned Subordinates"); ?></h1>
            </div>
                
            <div class="inner">
                <?php if ($reportToSubordinatePermission->canRead()) : ?>

                    <?php
                    if ($section == 'subordinates') {
                        include_partial('global/flash_messages');
                    }
                    ?>

                    <form name="frmEmpDelSubordinates" id="frmEmpDelSubordinates" method="post" 
                          action="<?php echo url_for('pim/deleteReportToSubordinate?empNumber=' . $empNumber); ?>">
                        <?php echo $deleteSubForm['_csrf_token']->render(); ?>
                        <?php echo $deleteSubForm['empNumber']->render(); ?>
                        <p id="subListActions">
                            <?php if ($reportToSubordinatePermission->canCreate()) { ?>
                            <input type="button" class="" id="btnAddSubordinateDetail" value="<?php echo __("Add"); ?>"/>
                            <?php } ?>
                            <?php if ($reportToSubordinatePermission->canDelete()) { ?>
                            <input type="button" class="delete" id="delSubBtn" value="<?php echo __("Delete"); ?>"/>
                            <?php } ?>
                        </p>
                        <table id="sub_list" class="table hover">
                            <thead>
                                <tr>
                                    <?php if ($reportToSubordinatePermission->canDelete()) { ?>
                                    <th class="check" style="width:2%"><input type='checkbox' id='checkAllSub' class="checkboxSub" /></th>
                                    <?php } ?>
                                    <th class="subName"><?php echo __("Name"); ?></th>
                                    <th class="subReportMethod"><?php echo __("Reporting Method"); ?></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if (!$hasSubDetails) { ?>
                                    <tr>
                                        <td class="check"></td>
                                        <td><?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?></td>
                                        <td></td>
                                    </tr>
                                <?php } else { ?>                        
                                    <?php
                                    $subRow = 0;
                                    foreach ($subDetails as $sub) :
                                        $cssClass = ($subRow % 2) ? 'even' : 'odd';
                                        echo '<tr class="' . $cssClass . '">';
                                        $subChkBoxValue = $empNumber . " " . $sub->getSubordinateId() . " " . 
                                                $sub->getReportingMethodId();
                                        if ($reportToSubordinatePermission->canDelete()) {
                                            echo "<td class='check'><input type='checkbox' class='checkboxSub' 
                                                name='chksubdel[]' value='" . $subChkBoxValue . "'/></td>";
                                        } else { 
                                        ?>
                                            <input type='hidden' class='checkboxSub' name='chksubdel[]' 
                                                   value='<?php echo $subChkBoxValue; ?>'/>
                                        <?php
                                        }
                                        $subordinate = $sub->getSubordinate();
                                        $terminationId = $subordinate->getTerminationId();
                                        $suffix = (!empty($terminationId)) ? " (" . __("Past Employee") . ")" : "";
                                        $subName = $subordinate->getFirstName() . " " . $subordinate->getLastName() . $suffix;
                                        ?>
                                        <?php $subReportingMethodName = $sub->getReportingMethod()->getName(); ?>
                                        <?php 
                                        if ($reportToSubordinatePermission->canUpdate()) { 
                                            ?>
                                            <td class="subName" valign="top"><a href="#"><?php echo $subName; ?></a></td>
                                        <?php
                                        } else { 
                                        ?>
                                            <td class="subName" valign="top"><?php echo $subName; ?></td>
                                        <?php
                                        }
                                        echo "<td  class='subReportMethod' valigh='top'>" . __($subReportingMethodName) . "</td>";
                                        echo '</tr>';
                                        $subRow++;
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
        </div> <!-- miniList-listReportToSubDetails -->

    </div> <!-- listReportToDetails -->


   
<script type="text/javascript">
    //<![CDATA[
    var fileModified = 0;
    var typeForHints = '<?php echo __("Type for hints") . "..."; ?>';
    var addSupervisor = '<?php echo __("Add Supervisor"); ?>';
    var addSubordinate = '<?php echo __("Add Subordinate"); ?>';
    var deleteWarning = '<?php echo __(TopLevelMessages::SELECT_RECORDS); ?>';
    var editSupervisor = '<?php echo __("Edit Supervisor"); ?>';
    var editSubordinate = '<?php echo __("Edit Subordinate"); ?>';
    var nameIsRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var reportingMethodIsRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var reportingMethodTypeIsRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var essMode = '<?php echo $essUserMode; ?>';
    
    var haveSupervisors = <?php echo $hasSupDetails ? 'true' : 'false'; ?>;
    var haveSubordinates = <?php echo $hasSubDetails ? 'true' : 'false'; ?>;
    var canUpdateSupervisors ='<?php echo $reportToSupervisorPermission->canUpdate(); ?>';
    var canUpdateSubordinates = '<?php echo $reportToSubordinatePermission->canUpdate(); ?>';
    var canCreateSupervisors = '<?php echo $reportToSupervisorPermission->canCreate(); ?>';
    var canCreateSubordinates = '<?php echo $reportToSubordinatePermission->canCreate(); ?>';
    var changePrimaryUrl ='<?php echo url_for('pim/changePrimary?empNumber=' . $empNumber); ?>';
  $(document).ready(function() {
    $('input[name=optPrime]:radio').click(function(){ 
    var prime = $(this).val();
   if(prime!=''){
    $("#deleteConfModal").modal('show');
   }
    // other stuff here for ajax call, etc
});
 $('#dialogDeleteBtn').click(function() {        
            var form=$("#frmEmpDelSupervisors");
   form.attr("action",changePrimaryUrl);
   form.submit();     
     });        

    $('#dialogCancelBtn').click(function() {
        $("#deleteConfModal").modal('hide');
    });
  });
    //]]>
</script>