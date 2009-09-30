<div style="color: #555; background-color: #F1F1F1; padding: 5px; width: auto; border-bottom: 1px solid #DDD;"><b><?php echo __('STEP 2'); ?></b> - <?php echo __('select issue type, component, category and severity'); ?></div>
<?php

	if ($step1_set == false)
	{
		?>
		<div style="padding: 5px; background-color: #F9F9F9; color: #AAA;" id="step2_main_content"><?php echo __('Please complete all the above steps first'); ?></div>
		<?php
	}
	else
	{
		?>
		<div style="width: auto; padding: 5px;<?php print ($step2_set == true) ? " background-color: #F9F9F9;" : ""; ?>" id="step2_main_content">
			<div style="width: 170px; padding: 2px; float: left;" id="step2_issuetype"><?php require BUGScontext::getIncludePath() . 'include/report_issue/issuetypeselection.inc.php'; ?></div>
			<div style="width: 185px; padding: 2px; float: left;" id="step2_component"><?php require BUGScontext::getIncludePath() . 'include/report_issue/componentselection.inc.php'; ?></div>
			<div style="width: 175px; padding: 2px; float: left;" id="step2_category"><?php require BUGScontext::getIncludePath() . 'include/report_issue/categoryselection.inc.php'; ?></div>
			<div style="width: 125px; padding: 2px; float: left;" id="step2_severity"><?php require BUGScontext::getIncludePath() . 'include/report_issue/severityselection.inc.php'; ?></div>
			<div style="width: auto; padding: 2px; text-align: right; clear: both;" id="step2_button"><?php require BUGScontext::getIncludePath() . 'include/report_issue/step2_button.inc.php'; ?></div>
		</div>
		<?php
	}

?>