<?php use_javascripts_for_form($form); ?>
<?php use_stylesheets_for_form($form); ?>
<?php use_stylesheet(plugin_web_path('orangehrmTrainingPlugin', 'css/viewTrainingSuccess')); ?>
<?php use_javascript(plugin_web_path('orangehrmTrainingPlugin', 'js/viewTrainingSuccess')); ?>
<div class="box searchForm toggableForm" id="srchCandidates">
    <div class="head">
        <h1><?php echo __('Search Training'); ?></h1>
    </div>
    <div class="inner">
        <form name="frmViewTraining" id = "frmViewTraining" method = "post" action="<?php echo url_for('training/viewTraining'); ?>" >
        	<fieldset>  
                <ol>
                    <?php echo $form->render(); ?>
                </ol>     
                <p>
                    <input type="button" id="btnSrch" value="<?php echo __("Search") ?>" name="btnSrch" />
                    <input type="button" class="reset" id="btnRst" value="<?php echo __("Reset") ?>" name="btnSrch" />                    
                </p>
            </fieldset>            
        </form>
    </div>
    <a href="#" class="toggle tiptip" title="<?php echo __(CommonMessages::TOGGABLE_DEFAULT_MESSAGE); ?>">&gt;</a>
</div>

<?php  include_component('core', 'ohrmList', $parmetersForListComponent); ?>

<form name="frmHiddenParam" id="frmHiddenParam" method="post" action="<?php echo url_for('training/viewTraining'); ?>">
    <input type="hidden" name="pageNo" id="pageNo" value="<?php echo $form->pageNo;        ?>" />
    <input type="hidden" name="hdnAction" id="hdnAction" value="search" />
</form>

<script type = "text/javascript">

function submitPage(pageNo) {
document.frmHiddenParam.pageNo.value = pageNo;
document.frmHiddenParam.hdnAction.value = 'paging';
document.getElementById('frmHiddenParam').submit();
}
//<![CDATA[
	var addTrainingURL = '<?php echo url_for('training/addTraining') ?>';
	var trainerList = <?php echo str_replace('&#039;', "'", $form->getTrainers()) ?> ;
	var attendeesList = <?php echo str_replace('&#039;', "'", $form->getEmployeeListAsJson()) ?>;
	var prepopuldatedTrainerList = <?php echo str_replace('&#039;', "'", $form->getPrepopulatedTrainers()) ?>;
	var prepopuldatedAttendeesList = <?php echo str_replace('&#039;', "'", $form->getPrepopulatedAttendees()) ?>;
//]]>
</script>