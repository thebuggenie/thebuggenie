<?php
$planningcolumns = $project->getPlanningColumns();
$fields = $project->getIssueFields();
// Remove fields that don't make sense on the Project Planning table
unset($fields['description'], $fields['status'], $fields['milestone'], $fields['reproduction_steps'], $fields['resolution'], $fields['assignee'], $fields['user_pain']);
?>

<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
	<form action="<?php echo make_url('configure_project_updateplanning', array('project_id' => $project->getID())); ?>" method="post" id="project_planning">
<?php endif; ?>
	<h3><?php echo __('Editing Planning settings'); ?></h3>
	<table style="clear: both; width: 780px;" class="padded_table" cellpadding=0 cellspacing=0>
		<tr>
			<td style="width: 300px; vertical-align: top;"><label for="fields"><?php echo __('Show fields as columns'); ?></label></td>
			<td style="width: 580px;">
				<div id="checkboxes_milestones" style="margin-top: 5px;">
					<?php if (count($fields) == 0): ?>
						<div class="faded_out" style="padding: 5px; font-size: 1.1em;"><?php echo __('There are no fields'); ?></div>
					<?php else: ?>
						<?php foreach ($fields as $fieldname => $fieldopts):
							// Don't allow users to select textarea fields
							if (in_array($fieldopts['type'], array(TBGCustomDatatype::INPUT_TEXTAREA_MAIN, TBGCustomDatatype::INPUT_TEXTAREA_SMALL))) { continue; }
						?>
						<div style="clear: both; font-size: 12px;">
							<input type="checkbox" name="planning_column[<?php echo $fieldname; ?>]" value="<?php echo $fieldname; ?>"<?php if (array_key_exists($fieldname, $planningcolumns)): ?> checked<?php endif; ?> id="planning_column_<?php echo $fieldname; ?>" style="float: left;"<?php if ($access_level != TBGSettings::ACCESS_FULL): ?> disabled<?php endif; ?>>
							<label for="planning_column_<?php echo $fieldname; ?>"><?php echo __('Show %fieldlabel%', array('%fieldlabel%' => $fieldopts['label'])); ?></label>
						</div>
						<?php endforeach; ?>
					<?php endif; ?>
				</div>
			</td>
		</tr>
	</table>
<?php if ($access_level == TBGSettings::ACCESS_FULL): ?>
	<div style="margin: 15px 0px 5px 0px; width: 770px; vertical-align: middle; height: 23px; padding: 5px 10px 5px 10px;">
		<div style="float: left; font-size: 13px; padding-top: 2px; font-style: italic;" class="config_explanation"><?php echo __('When you are done, click "%save%" to save your changes', array('%save%' => __('Save'))); ?></div>
		<input class="button button-green" style="float: right;" type="submit" id="project_submit_settings_button" value="<?php echo __('Save'); ?>">
		<span id="settings_save_indicator" style="display: none; float: right;"><?php echo image_tag('spinning_20.gif'); ?></span>
	</div>
</form>
<?php endif; ?>