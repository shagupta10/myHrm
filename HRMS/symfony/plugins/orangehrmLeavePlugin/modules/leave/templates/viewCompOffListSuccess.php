<?php
use_stylesheets_for_form($form);
use_javascripts_for_form($form);
?>

<?php if ($form->hasErrors()): ?>
    <div class="messagebar">
        <?php include_partial('global/form_errors', array('form' => $form)); ?>
    </div>
<?php endif; ?>

<div class="box toggableForm" id="leave-list-search">
    <div class="head">
        <h1><?php echo __($form->getTitle());?></h1>
    </div>
    <div class="inner">
        <form id="frmFilterLeave" name="frmFilterLeave" method="post" action="<?php echo url_for($baseUrl); ?>">

            <fieldset>                
                <ol>
                    <?php echo $form->render(); ?>
                </ol>            
                
                <p>
                    <?php
                    $searchActionButtons = $form->getSearchActionButtons();
                    foreach ($searchActionButtons as $id => $button) {
                        echo $button->render($id), "\n";
                    }
                    ?>                    
                    <?php include_component('core', 'ohrmPluginPannel', array('location' => 'listing_layout_navigation_bar_1')); ?>
                    <input type="hidden" name="pageNo" id="pageNo" value="" />
                    <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
                    
                </p>                
            </fieldset>
            
        </form>
        
    </div> <!-- inner -->
    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>
</div> <!-- leave-list-search -->

<?php include_component('core', 'ohrmList'); ?>

<script type="text/javascript">
    //<![CDATA[
    var lang_typeHint = "<?php echo __("Type for hints"); ?>" + "...";
    var resetUrl = '<?php echo url_for($baseUrl . '?reset=1'); ?>';
    var commentUpdateUrl = '<?php echo public_path('index.php/leave/updateComment'); ?>';
    var getCommentsUrl = '<?php echo url_for('leave/getLeaveCommentsAjax'); ?>';
    var datepickerDateFormat = '<?php echo get_datepicker_date_format($sf_user->getDateFormat()); ?>';
    var lang_dateError = '<?php echo __("To date should be after from date") ?>';
    var lang_invalidDate = '<?php echo __(ValidationMessages::DATE_FORMAT_INVALID, array('%format%' => str_replace('yy', 'yyyy', get_datepicker_date_format($sf_user->getDateFormat())))) ?>';
    var lang_comment_successfully_saved = '<?php echo __(TopLevelMessages::SAVE_SUCCESS); ?>';
    var lang_comment_save_failed = '<?php echo __(TopLevelMessages::SAVE_FAILURE); ?>';
    var lang_edit = '<?php echo __('Edit'); ?>';
    var lang_save = '<?php echo __('Save'); ?>';
    var lang_length_exceeded_error = '<?php echo __(ValidationMessages::TEXT_LENGTH_EXCEEDS, array('%amount%' => 255)); ?>';    
    var lang_selectAction = '<?php echo __("Select Action");?>';
    var lang_Close = '<?php echo __('Close');?>';
    var leave_status_pending = '<?php echo PluginLeave::LEAVE_STATUS_LEAVE_PENDING_APPROVAL;?>';
    var ess_mode = '<?php echo ($essMode) ? '1' : '0'; ?>';
    var lang_Required = '<?php echo __(ValidationMessages::REQUIRED);?>';
    var lang_Date = '<?php echo __('Date');?>';
    var lang_Time = '<?php echo __('Time');?>';
    var lang_Author = '<?php echo __('Author');?>';
    var lang_Comment = '<?php echo __('Comment');?>';
    var lang_Loading = '<?php echo __('Loading');?>...';
    var lang_View = '<?php echo __('View');?>';
    var balanceData = false;
    
    
    function submitPage(pageNo) {
        document.frmFilterLeave.pageNo.value = pageNo;
        document.frmFilterLeave.hdnAction.value = 'paging';
        var autoCompleteField = $('#compOffLeaveList_txtEmployee_empName');
        if ((autoCompleteField.val() === lang_typeHint) ||
                autoCompleteField.hasClass('ac_loading') || 
                autoCompleteField.hasClass('inputFormatHint')) {
            $('#compOffLeaveList_txtEmployee_empName').val('');
        }
        document.getElementById('frmFilterLeave').submit();        
    }

    function handleSaveButton() {
        $(this).attr('disabled', true);
        $('div.message').remove();
              
        var selectedActions = 0;
        
        $('select[name^="select_compoff_leave_action"]').each(function() {
            var id = $(this).attr('id').replace('select_compoff_leave_action', '');
           // alert($(this).val());
            if ($(this).val() != '') {
                selectedActions++;
            } 
        });  
    
        if (selectedActions > 0) {
            $('#frmList_ohrmListComponent').submit();
        } else {
            $('#helpText').before('<div class="message warning fadable">' + lang_selectAction + '<a href="#" class="messageCloseButton">' + lang_Close + '</a></div>');
            setTimeout(function(){
                $("div.fadable").fadeOut("slow", function () {
                    $("div.fadable").remove();
                });
            }, 2000);
            $(this).attr('disabled', false);      
            return false;
        }
    }

    function setPage() {}

    
    //]]>
 </script>
