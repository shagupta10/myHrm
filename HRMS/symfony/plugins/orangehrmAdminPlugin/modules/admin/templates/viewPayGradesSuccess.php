
<?php 
use_javascript(plugin_web_path('orangehrmAdminPlugin', 'js/viewPayGradesSuccess')); 
?>
<script type="text/javascript"> var recordsPerpage = '<?php echo $recordsLimits; ?>'; </script>
<div id="jobTitleList">
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
<form name="frmHiddenParam" id="frmHiddenParam" method="post" action="<?php echo url_for('admin/viewPayGrades?recordsPerPage_Limit='.$recordsLimits); ?>">
    <input type="hidden" name="pageNo" id="pageNo" value="<?php //echo $form->pageNo;   ?>" />
    <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
</form>
<script type="text/javascript">
    var addPayGradeUrl = '<?php echo url_for('admin/payGrade'); ?>';
    function submitPage(pageNo) {
        document.frmHiddenParam.pageNo.value = pageNo;
        document.frmHiddenParam.hdnAction.value = 'paging';
        document.getElementById('frmHiddenParam').submit();
    }
</script>