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
 */
?>
<?php use_javascript(plugin_web_path('orangehrmPimPlugin', 'js/viewDirectorySuccess')); ?>
<script type="text/javascript"> var recordsPerpage = '<?php echo $recordsLimits; ?>'; </script>
<div class="box searchForm toggableForm" id="employee-information">
    <div class="head">
        <h1><?php echo __("Directory") ?></h1>
    </div>
    <div class="inner">
        <form id="search_form" name="frmEmployeeSearch" method="post" action="<?php echo url_for('pim/viewDirectory'); ?>">

            <fieldset>
                
                <ol>
                    <?php echo $form->render(); ?>
                </ol>
                
                <input type="hidden" name="pageNo" id="pageNo" value="" />
                <input type="hidden" name="hdnAction" id="hdnAction" value="search" />                 
                
                <p>
                    <input type="button" id="searchBtn" value="<?php echo __("Search") ?>" name="_search" />
                    <input type="button" class="reset" id="resetBtn" value="<?php echo __("Reset") ?>" name="_reset" />                    
                </p>
                
            </fieldset>
            
        </form>
        
    </div> <!-- inner -->

    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>
    
</div> <!-- employee-information -->

<?php include_component('core', 'ohrmList'); ?>

<?php include_partial('global/delete_confirmation'); ?>

<script type="text/javascript">
	var employees = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?> ;
	var customerList = <?php echo str_replace('&#039;', "'", $form->getCustomerListAsJson()) ?>;
	
    function submitPage(pageNo) {
        document.frmEmployeeSearch.pageNo.value = pageNo;
        document.frmEmployeeSearch.hdnAction.value = 'paging';
        $('#search_form input.inputFormatHint').val('');
        $('#search_form input.ac_loading').val('');
        $("#empDir_isSubmitted").val('no');
        document.getElementById('search_form').submit();
    }   
</script>
