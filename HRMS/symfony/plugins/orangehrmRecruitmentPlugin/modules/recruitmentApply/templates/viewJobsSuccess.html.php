<?php use_javascript(plugin_web_path('orangehrmRecruitmentPlugin', 'js/viewJobsSuccess')); ?>

<style type="text/css">
    
    #content {
        padding-top: 0;
    }    
    
    pre {
        overflow: auto;
        white-space: pre-wrap;       /* css-3 */
        white-space: -moz-pre-wrap !important;  /* Mozilla, since 1999 */
        white-space: -pre-wrap;      /* Opera 4-6 */
        white-space: -o-pre-wrap;    /* Opera 7 */
        word-wrap: break-word;       /* Internet Explorer 5.5+ */
    }
    
    #toggleJobList {
        float: right;
        margin: -2px 10px 0px 0px;
        font-size: 12px;
    }

    #toggleJobList span {
        text-decoration: underline;
        cursor: pointer; 
    }

    .vacancyDescription, .vacancyShortDescription {
        display: none;
        line-height: 15px;
        margin-bottom: 10px;
    }

    .vacancyDescription {
        display: block;
    }

    .applyLink {
        display: none;
    }

    .vacancyTitle :hover {
        cursor: pointer;
    }

    .plusOrMinusmark {
        text-align: right;
        margin-top: 10px;
        padding-right: 3px;
        font-size: 12px;
        position: relative;
        top: 12px;
        right: 8px;
    }

    .plusMark, .minusMark {
        cursor: pointer;
    }

    .plusMark {
        display: none;
    }

    h3 {
        margin-bottom: 10px;
    }
    
</style>
<?php $loggedinUser = sfContext::getInstance()->getUser()->getEmployeeNumber();?>
<div id="jobPage">
    <div class="box">
	    <div align="right">
	    	Welcome  <?php echo $_SESSION['fname']?> &nbsp;&nbsp;&nbsp; <a href="<?php echo url_for('auth/logout'); ?>"><?php echo __('Logout'); ?></a>
	    </div>
	    <br>
        <div class="maincontent">
            <div class="head">
                <h1><?php echo __('Active Job Vacancies'); ?></h1>
            </div>

            <div class="inner">
                <?php if (count($publishedVacancies) != 0): ?>                    
                    <div id="toggleJobList">
                        <span id="expandJobList"><?php echo __('Expand all') ?></span> | <span id="collapsJobList"><?php echo __('Collapse all'); ?></span>
                    </div>

                    <?php foreach ($publishedVacancies as $vacancy): ?>
						<?php 
							$showVacancyFlag = true;
							$consultants = $vacancy->getConsultants();
							$consultantList = explode(',', $consultants);
							if(!empty($consultants) && $_SESSION['isConsultant']) {
								if(!in_array($loggedinUser, $consultantList)) {
									$showVacancyFlag = false;
								}
							}
								if($showVacancyFlag) {
						?>
                        <div class="plusOrMinusmark">
                            <span class="plusMark">[+]</span><span class="minusMark">[-]</span>
                        </div>

                        <div class="jobItem">
							
                            <div  class="vacancyTitle">
                               <h3><?php echo $vacancy->getName(); ?>  &nbsp; 
                               		<?php if($vacancy->isUrgent == JobVacancy::IS_URGENT_VACANCY) {?>
                               			 <img id="urgent_<?php echo $vacancy->getId(); ?>" src="<?php echo theme_path('images/urgent.jpg')?>" height= "14" width="64"/> &nbsp;
                               		<?php }?>
                                         <img id="new_<?php echo $vacancy->getId(); ?>" src="<?php echo theme_path('images/new.gif')?>"/></h3> 
                            </div>
							<script type="text/javascript">isNewlyAddedVacancy('<?php echo $vacancy->getId(); ?>','<?php echo $vacancy->getUpdatedTime(); ?>')</script>
                            <pre class="vacancyShortDescription"><?php echo getShortDescription($vacancy->getDescription(), 250, "..."); ?></pre>
                            <pre class="vacancyDescription"><?php echo $vacancy->getDescription(); ?></pre>
                            
                            <p class="borderBottom">
                                <input type="button" class="apply" name="applyButton" value="<?php echo __("Apply"); ?>" onmouseout="moutButton(this);" onmouseover="moverButton(this);" />
                                <a href="<?php echo public_path('index.php/recruitmentApply/applyVacancy/id/' . $vacancy->getId(), true); ?>" class="applyLink"></a>
                            </p>
                            
                        </div>
                        <?php }?>
                    <?php endforeach; ?>
                <?php else: ?>
                    <span class="noVacanciesMessage"><?php echo __('No active job vacancies to display'); ?></span>
                <?php endif; ?>

            </div>

        </div>

    </div>

</div>
<?php
/*
 * Get short description to show in default view in view job list
 * @param string $description full description
 * @param int $limit Number of characters show in short description
 * @param string $endString String added to end of the short description
 * @return string $description short description 
 */

function getShortDescription($description, $limit, $endString) {

    if (strlen($description) > $limit) {
        $subString = substr($description, 0, $limit);
        $wordArray = explode(" ", $subString);
        $description = substr($subString, 0, -(strlen($wordArray[count($wordArray) - 1]) + 1)) . $endString;
    }
    return $description;
}

