<div style="background-color: #F5F5F5; padding: 5px; width: auto; border-bottom: 1px solid #DDD;"><b><?php echo __('STEP 3'); ?></b> - <?php echo __('describe the issue you are reporting'); ?></div>
<?php

	if ($step1_set == false || $step2_set == false)
	{
		?>
		<div style="padding: 5px; background-color: #F9F9F9; color: #AAA;" id="step3_main_content"><?php echo __('Please complete all the above steps first'); ?></div>
		<?php
	}
	else
	{
		?>
		<div style="width: auto; padding: 5px;<?php print ($step3_set == true) ? " background-color: #F9F9F9;" : ""; ?>" id="step3_main_content">
			<div id="step3_summary"><?php require BUGScontext::getIncludePath() . 'include/report_issue/inputsummary.inc.php'; ?></div>
			<div style="margin-top: 2px; margin-bottom: 4px; border-bottom: 1px solid #DDD; height: 2px;"></div>
			<div id="step3_description"><?php require BUGScontext::getIncludePath() . 'include/report_issue/inputdescription.inc.php'; ?></div>
			<div style="margin-top: 2px; margin-bottom: 2px; border-bottom: 1px solid #DDD; height: 2px;"></div>
			<div id="step3_repro"><?php require BUGScontext::getIncludePath() . 'include/report_issue/inputrepro.inc.php'; ?></div>
			<div style="width: auto; padding: 2px; text-align: right;" id="step3_button"><?php require BUGScontext::getIncludePath() . 'include/report_issue/step3_button.inc.php'; ?></div>
		</div>
		<?php
}
?>
