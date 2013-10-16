<div class="rounded_box white <?php if (isset($mode) && $mode == 'inline'): ?>borderless<?php else: ?>shadowed dropdown_box<?php endif; ?>" id="<?php echo $field . '_' . $issue_id; ?>_change" style="<?php if (!isset($hidden) || $hidden == true): ?>display: none;<?php endif; ?> <?php if (!(isset($mode) && $mode == 'inline')): ?>width: 350px;<?php endif; ?> margin: 5px 0 5px 0; <?php if (isset($mode) && $mode == 'inline'): ?>position: relative;<?php else: ?>position: absolute; z-index: 10001;<?php endif; ?> <?php echo (isset($mode) && $mode == 'left') ? 'left' : 'right'; ?>: 0; padding: 5px; text-align: left;">
<?php if (!isset($save) || $save == true): ?>
	<form id="<?php echo $field . '_' . $issue_id; ?>_form" method="post" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="" onsubmit="TBG.Issues.Field.setTime('<?php echo make_url('issue_setfield', array('project_key' => $project_key, 'issue_id' => $issue_id, 'field' => $field)); ?>', '<?php echo $field; ?>', <?php echo $issue_id; ?>);return false;">
		<input type="hidden" name="do_save" value="<?php echo (integer) (isset($instant_save) && $instant_save); ?>">
<?php endif; ?>
	<?php if (!isset($headers) || $headers == true): ?>
		<div class="dropdown_header">
			<?php if ($field == 'estimated_time'): ?>
				<?php echo __('Estimate this issue'); ?>
			<?php else: ?>
				<?php echo __('Log time spent on this issue'); ?>
			<?php endif; ?>
		</div>
	<?php endif; ?>
		<?php if ($field == 'estimated_time' && (!isset($clear) || $clear == true)): ?>
			<div class="dropdown_content">
				<a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $project_key, 'issue_id' => $issue_id, 'field' => $field, 'value' => 0)); ?>', '<?php echo $field; ?>');"><?php echo ($field == 'estimated_time') ? __('Clear current estimate') : __('Clear time spent on this issue'); ?></a><br>
			</div>
		<?php endif; ?>
		<div class="dropdown_content">
			<label for="<?php echo $field . '_' . $issue_id; ?>_input">
				<?php if ($field == 'estimated_time'): ?>
					<?php echo trim(__('%clear_current_estimate type a new estimate %or_specify_below', array('%clear_current_estimate' => '', '%or_specify_below' => ''))); ?>:
				<?php else: ?>
					<?php echo trim(__('Type a value for the time spent %or_specify_below', array('%clear_current_time_spent' => '', '%or_specify_below' => ''))); ?>:
				<?php endif; ?>
			</label><br>
			<input type="text" name="<?php echo $field; ?>" id="<?php echo $field . '_' . $issue_id; ?>_input" placeholder="<?php echo ($field == 'estimated_time') ? __('Enter your estimate here') : __('Enter time spent here'); ?>" style="width: 240px; padding: 1px 1px 1px;">
		<?php if (!isset($save) || $save == true): ?>
			<input type="submit" style="width: 60px;" value="<?php echo ($field == 'estimated_time') ? __('Set') : __('Add'); ?>">
		<?php endif; ?>
		<?php if (!isset($headers) || $headers == true): ?>
			<div class="faded_out" style="padding: 5px 0 5px 0;">
				<?php echo __('Enter a value in plain text, like "1 week, 2 hours", "3 months and 1 day", or similar'); ?>.
			</div>
		<?php endif; ?>
		</div>
		<div class="dropdown_content">
		<label for="<?php echo $field . '_' . $issue_id; ?>_months"><?php echo __('%enter_a_value_in_plain_text or specify below', array('%enter_a_value_in_plain_text' => '')); ?>:</label><br>
		<input type="text" style="width: 20px;" value="<?php echo $hours; ?>" name="hours" id="<?php echo $field . '_' . $issue_id; ?>_hours_input"><b><?php echo __('%number_of hours', array('%number_of' => '')); ?></b><?php if (isset($mode) && $mode == 'inline'): ?>&nbsp;<?php else: ?><br><?php endif; ?>
		<input type="text" style="width: 20px;" value="<?php echo $days; ?>" name="days" id="<?php echo $field . '_' . $issue_id; ?>_days_input"><b><?php echo __('%number_of days', array('%number_of' => '')); ?></b><?php if (isset($mode) && $mode == 'inline'): ?>&nbsp;<?php else: ?><br><?php endif; ?>
		<input type="text" style="width: 20px;" value="<?php echo $weeks; ?>" name="weeks" id="<?php echo $field . '_' . $issue_id; ?>_weeks_input"><b><?php echo __('%number_of weeks', array('%number_of' => '')); ?></b><?php if (isset($mode) && $mode == 'inline'): ?>&nbsp;<?php else: ?><br><?php endif; ?>
		<input type="text" style="width: 20px;" value="<?php echo $months; ?>" name="months" id="<?php echo $field . '_' . $issue_id; ?>_months_input"><b><?php echo __('%number_of months', array('%number_of' => '')); ?></b><?php if (isset($mode) && $mode == 'inline'): ?>&nbsp;<?php else: ?><br><?php endif; ?>
		<input type="text" style="width: 20px;" value="<?php echo $points; ?>" name="points" id="<?php echo $field . '_' . $issue_id; ?>_points_input"><b><?php echo __('%number_of points', array('%number_of' => '')); ?></b><br>
		<?php if (!isset($save) || $save == true): ?>
			<a href="javascript:void(0);" onclick="$('<?php echo $field . '_' . $issue_id; ?>_change').hide();" style="float: right; margin-left: 5px;" class="button button-silver"><?php echo __('Cancel'); ?></a>
			<input type="submit" style="float: right;" value="<?php echo ($field == 'estimated_time') ? __('Save') : __('Add time spent'); ?>">
		<?php endif; ?>
		</div>
<?php if (!isset($save) || $save == true): ?>
	</form>
	<div id="<?php echo $field . '_' . $issue_id; ?>_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
	<div id="<?php echo $field . '_' . $issue_id; ?>_change_error" class="error_message" style="display: none;"></div>
<?php endif; ?>
</div>
