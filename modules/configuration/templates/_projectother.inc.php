<div style="text-align: left;">
	<div class="config_header"><b><?php echo __('Project frontpage overview'); ?></b></div>
	<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
		<?php echo __('Select what to show on the frontpage project overview'); ?>. (<?php echo __('This will not affect the separate project overview page'); ?>)
		<form action="<?php echo make_url('configure_project_updateother', array('project_id' => $project->getID())); ?>" method="post" style="margin-top: 10px;" onsubmit="saveProjectOther('<?php echo make_url('configure_project_updateother', array('project_id' => $project->getID())); ?>');return false;" id="project_other">
	<?php else: ?>
		<div style="margin-bottom: 15px;"><?php echo __('This is what is shown on the frontpage project overview'); ?></div>
	<?php endif; ?>
		<label for="frontpage_summary"><?php echo __('On the frontpage summary, show'); ?></label>
		<select name="frontpage_summary" id="frontpage_summary" <?php if ($access_level == configurationActions::ACCESS_FULL): ?>onchange="$('checkboxes_issuetypes').hide();$('checkboxes_issuelist').hide();$('checkboxes_milestones').hide();if ($('checkboxes_'+this.getValue())) { $('checkboxes_'+this.getValue()).show(); }"<?php else: ?> disabled<?php endif; ?>>
			<option value=""<?php if (!$project->isAnythingVisibleInFrontpageSummary()): ?> selected<?php endif; ?>><?php echo __('only project information'); ?></option>
			<option value="milestones"<?php if ($project->isMilestonesVisibleInFrontpageSummary()): ?> selected<?php endif; ?>><?php echo __('status per milestone'); ?></option>
			<option value="issuetypes"<?php if ($project->isIssuetypesVisibleInFrontpageSummary()): ?> selected<?php endif; ?>><?php echo __('status per issue types'); ?></option>
			<option value="issuelist"<?php if ($project->isIssuelistVisibleInFrontpageSummary()): ?> selected<?php endif; ?>><?php echo __('list of open issues'); ?></option>
		</select>
		<div id="checkboxes_issuetypes" style="margin-top: 5px;<?php if (!$project->isIssuetypesVisibleInFrontpageSummary()): ?> display: none;<?php endif;?>">
			<?php foreach ($project->getIssueTypes() as $issuetype): ?>
				<div style="clear: both; font-size: 12px;">
					<input type="checkbox" name="showissuetype[<?php echo $issuetype->getID(); ?>]" onChange="$('checkboxes_issuelist').select('input#showissuetype_<?php echo $issuetype->getID(); ?>')[0].checked=this.checked;" value="<?php echo $issuetype->getID(); ?>"<?php if ($project->isIssuetypeVisible($issuetype->getID())): ?> checked<?php endif; ?> id="showissuetype_<?php echo $issuetype->getID(); ?>" style="float: left;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
					<label for="showissuetype_<?php echo $issuetype->getID(); ?>"><?php echo __('Show %issuetype%', array('%issuetype%' => $issuetype->getName())); ?></label>
				</div>
			<?php endforeach; ?>
		</div>
		<div id="checkboxes_issuelist" style="margin-top: 5px;<?php if (!$project->isIssuelistVisibleInFrontpageSummary()): ?> display: none;<?php endif;?>">
			<?php foreach ($project->getIssueTypes() as $issuetype): ?>
				<div style="clear: both; font-size: 12px;">
					<input type="checkbox" name="showissuetype[<?php echo $issuetype->getID(); ?>]" onChange="$('checkboxes_issuetypes').select('input#showissuetype_<?php echo $issuetype->getID(); ?>')[0].checked=this.checked;" value="<?php echo $issuetype->getID(); ?>"<?php if ($project->isIssuetypeVisible($issuetype->getID())): ?> checked<?php endif; ?> id="showissuetype_<?php echo $issuetype->getID(); ?>" style="float: left;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
					<label for="showissuetype_<?php echo $issuetype->getID(); ?>"><?php echo __('Show %issuetype%', array('%issuetype%' => $issuetype->getName())); ?></label>
				</div>
			<?php endforeach; ?>
		</div>
		<div id="checkboxes_milestones" style="margin-top: 5px;<?php if (!$project->isMilestonesVisibleInFrontpageSummary()): ?> display: none;<?php endif;?>">
			<?php if (count($project->getMilestones()) == 0): ?>
				<div class="faded_out" style="padding: 5px; font-size: 1.1em;"><?php echo __('There are no milestones'); ?></div>
			<?php else: ?>
				<?php foreach ($project->getMilestones() as $milestone): ?>
					<div style="clear: both; font-size: 12px;">
						<input type="checkbox" name="showmilestone[<?php echo $milestone->getID(); ?>]" value="<?php echo $milestone->getID(); ?>"<?php if ($project->isMilestoneVisible($milestone->getID())): ?> checked<?php endif; ?> id="showmilestone_<?php echo $milestone->getID(); ?>" style="float: left;"<?php if ($access_level != configurationActions::ACCESS_FULL): ?> disabled<?php endif; ?>>
						<label for="showmilestone_<?php echo $milestone->getID(); ?>"><?php echo __('Show %milestone%', array('%milestone%' => $milestone->getName())); ?></label>
					</div>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	<?php if ($access_level == configurationActions::ACCESS_FULL): ?>
		<div style="margin: 15px 0px 5px 0px; width: 770px; vertical-align: middle; height: 23px; padding: 5px 10px 5px 10px;">
			<div style="float: left; font-size: 13px; padding-top: 2px; font-style: italic;" class="config_explanation"><?php echo __('When you are done, click "Save" to save your changes'); ?></div>
			<input type="submit" id="project_submit_settings_button" style="float: right; padding: 0 10px 0 10px; font-size: 14px; font-weight: bold;" value="<?php echo __('Save'); ?>">
			<span id="settings_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
		</div>
	</form>
	<?php endif; ?>
</div>