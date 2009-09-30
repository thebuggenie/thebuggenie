<div style="background-color: #F5F5F5; padding: 5px; width: auto; border-bottom: 1px solid #DDD;"><b><?php echo __('STEP 1'); ?></b> - <?php echo __('select project, edition and version'); ?></div>
<div style="width: auto; padding: 5px; <?php print ($step1_set == true) ? " background-color: #F9F9F9;" : ""; ?>" id="step1_main_content">
	<div style="width: 200px; float: left;" id="project_td"><?php require BUGScontext::getIncludePath() . 'include/report_issue/projectselection.inc.php'; ?></div>
	<div style="width: 200px; float: left;" id="edition_td"><?php require BUGScontext::getIncludePath() . 'include/report_issue/editionselection.inc.php'; ?></div>
	<div style="width: 200px; float: left;" id="build_td"><?php require BUGScontext::getIncludePath() . 'include/report_issue/buildselection.inc.php'; ?></div>
	<div style="width: auto; padding: 2px; text-align: right; clear: both;">
	<?php
	
		if (!$step1_set && $selectedProject instanceof BUGSproject && $selectedEdition instanceof BUGSedition && ($selectedBuild instanceof BUGSbuild || !$selectedProject->isBuildsEnabled()))
		{
			?>
			<button onclick="setStep(1);$('step1_button').hide();" id="step1_button"><?php echo __('Confirm'); ?></button>
			<?php
		}
		elseif (!$step1_set)
		{
			?><div style="padding: 3px; color: #AAA;"><?php echo __('You must select a project, edition and a build to continue'); ?></div><?php
		}
	
	?>
	</div>
</div>