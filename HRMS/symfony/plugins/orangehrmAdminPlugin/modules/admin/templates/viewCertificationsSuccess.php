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
use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/viewCertificationsSuccess'));
?>



<!-- Listi view -->

<div id="recordsListDiv" class="box miniList">
    <div class="head">
            <h1><?php echo __('Certifications For Pending Approval'); ?></h1>
    </div>
    
    <div class="inner">
        
        <?php include_partial('global/flash_messages'); ?>
        
        <form name="frmList" id="frmList" method="post" action="<?php echo url_for('admin/deleteCertifications'); ?>">
            
            <p id="listActions">               
                <input type="submit" name="btnDel" class="" id="btnDel" value="<?php echo __('Approve'); ?>"/>
				<input type="submit" name="btnDisapprove" class="delete" id="btnDisapprove" value="<?php echo __('Reject'); ?>"/>
            </p>
            
            <table class="table hover" id="recordsListTable">
                <thead>
                    <tr>
                        <th class="check" style="width:2%"><input type="checkbox" id="checkAll" class="checkboxAtch" /></td>
                        <th><?php echo __('Employee Name'); ?></th>
						<th><?php echo __('Certificate Name'); ?></th>
                        <th><?php echo __('Certificate Date'); ?></th>
						<th><?php echo __('Institute'); ?></th>
						<th><?php echo __('Grade'); ?></th>
						<th><?php echo __('Attachment'); ?></th>
						<th><?php echo __('Link'); ?></th>
                    </tr>
                </thead>
                <tbody>
                    
                    <?php 
                    $row = 0;
					
                    foreach($records as $record) : 
                        $cssClass = ($row%2) ? 'even' : 'odd';
                   
					?>
                    
                    <tr class="<?php echo $cssClass;?>">
                        <td class="check">
                            <input type="checkbox" class="checkboxAtch" name="chkListRecord[]" value="<?php echo $record->getCertificationId(); ?>" />
                        </td>
						<td>
							<?php echo $record->getEmployee()->getFirstName()." ".$record->getEmployee()->getLastName(); ?>
						</td>						
                        <td>
                            <?php echo $record->getName(); ?>
                        </td>
                        <td>
                            <?php echo $record->getDate(); ?> 
                        </td>
						<td>
                            <?php echo $record->getInstitute(); ?> 
                        </td>
						<td>
                            <?php echo $record->getGrade(); ?> 
                        </td>
						<td>
                           <a href="<?php echo sfContext::getInstance()->getRequest()->getRelativeUrlRoot()."/index.php/pim/saveDeleteCertification?id={$record->getCertificationId()}"; ?>"><?php echo $record->getCattachName(); ?></a> 
                        </td>
						<td>
							<?php echo $record->getCertificationLink(); ?>
						</td>
                    </tr>
                    
                    <?php 
                    $row++;
                    endforeach; 
                    ?>
                    
                    <?php if (count($records) == 0) : ?>
                    <tr class="<?php echo 'even';?>">
                        <td>
                            <?php echo __(TopLevelMessages::NO_RECORDS_FOUND); ?>
                        </td>
                        <td>
                        </td>
                    </tr>
                    <?php endif; ?>
                    
                </tbody>
            </table>
        </form>
    </div>
</div> <!-- recordsListDiv -->    

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

<script type="text/javascript">
//<![CDATA[	    
 
    var recordsCount = <?php echo count($records);?>;
   
    var recordKeyId = "certification_id";   
    
    var urlForExistingNameCheck = '<?php echo url_for('admin/checkCertificationNameExistence'); ?>';
    
    var lang_addFormHeading = "<?php echo __('Add Certification'); ?>";
    var lang_editFormHeading = "<?php echo __('Edit Certification'); ?>";
    var lang_nameIsRequired = '<?php echo __(ValidationMessages::REQUIRED); ?>';
    var lang_descLengthExceeded = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 250)); ?>';
    var lang_nameExists = '<?php echo __(ValidationMessages::ALREADY_EXISTS); ?>';
   
    
//]]>	
</script> 
