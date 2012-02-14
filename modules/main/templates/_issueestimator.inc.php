<div class="rounded_box white shadowed dropdown_box" id="<?php echo $field . '_' . $issue->getID(); ?>_change" style="display: none; width: 280px; position: absolute; z-index: 10001; margin: 5px 0 5px 0; <?php echo (isset($mode) && $mode == 'left') ? 'left' : 'right'; ?>: 0; padding: 5px; text-align: left;">
	<form id="<?php echo $field . '_' . $issue->getID(); ?>_form" method="post" accept-charset="<?php echo TBGContext::getI18n()->getCharset(); ?>" action="" onsubmit="TBG.Issues.Field.setTime('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'estimated_time')); ?>', 'estimated_time', <?php echo $issue->getID(); ?>);return false;">
		<input type="hidden" name="do_save" value="<?php echo (integer) (isset($instant_save) && $instant_save); ?>">
		<div class="dropdown_header">
			<?php if ($field == 'estimated_time'): ?>
				<?php echo __('Estimate this issue'); ?>
			<?php else: ?>
				<?php echo __('Time spent on this issue'); ?>
			<?php endif; ?>
		</div>
		<div class="dropdown_content">
			<a href="javascript:void(0);" onclick="TBG.Issues.Field.set('<?php echo make_url('issue_setfield', array('project_key' => $issue->getProject()->getKey(), 'issue_id' => $issue->getID(), 'field' => 'estimated_time', 'value' => 0)); ?>', '<?php echo $field . '_' . $issue->getID(); ?>');"><?php echo ($field == 'estimated_time') ? __('Clear current estimate') : __('Clear time spent on this issue'); ?></a><br>
		</div>
		<div class="dropdown_content">
			<label for="<?php echo $field . '_' . $issue->getID(); ?>_input"><?php echo trim(__('%clear_current_estimate% type a new estimate %or_select_below%', array('%clear_current_estimate%' => '', '%or_select_below%' => ''))); ?>:</label><br>
			<input type="text" name="estimated_time" id="<?php echo $field . '_' . $issue->getID(); ?>_input" placeholder="<?php echo ($field == 'estimated_time') ? __('Enter your estimate here') : __('Enter time spent here'); ?>" style="width: 240px; padding: 1px 1px 1px;">
			<input type="submit" style="width: 60px;" value="<?php echo __('Set'); ?>">
			<div class="faded_out" style="padding: 5px 0 5px 0;">
				<?php echo __('Enter a value in plain text, like "1 week, 2 hours", "3 months and 1 day", or similar'); ?>.
			</div>
		</div>
		<div class="dropdown_content">
			<label for="<?php echo $field . '_' . $issue->getID(); ?>_months"><?php echo __('%enter_a_value_in_plain_text% or select from more options below', array('%enter_a_value_in_plain_text%' => '')); ?>:</label><br>
			<input type="text" style="width: 20px;" value="<?php echo $issue->getEstimatedMonths(); ?>" name="months" id="<?php echo $field . '_' . $issue->getID(); ?>_months_input"><b><?php echo __('%number_of% months', array('%number_of%' => '')); ?></b><br>
			<input type="text" style="width: 20px;" value="<?php echo $issue->getEstimatedWeeks(); ?>" name="weeks" id="<?php echo $field . '_' . $issue->getID(); ?>_weeks_input"><b><?php echo __('%number_of% weeks', array('%number_of%' => '')); ?></b><br>
			<input type="text" style="width: 20px;" value="<?php echo $issue->getEstimatedDays(); ?>" name="days" id="<?php echo $field . '_' . $issue->getID(); ?>_days_input"><b><?php echo __('%number_of% days', array('%number_of%' => '')); ?></b><br>
			<input type="text" style="width: 20px;" value="<?php echo $issue->getEstimatedHours(); ?>" name="hours" id="<?php echo $field . '_' . $issue->getID(); ?>_hours_input"><b><?php echo __('%number_of% hours', array('%number_of%' => '')); ?></b><br>
			<input type="submit" style="float: right;" value="<?php echo __('Save'); ?>">
			<input type="text" style="width: 20px;" value="<?php echo $issue->getEstimatedPoints(); ?>" name="points" id="<?php echo $field . '_' . $issue->getID(); ?>_points_input"><b><?php echo __('%number_of% points', array('%number_of%' => '')); ?></b><br>
		</div>
	</form>
	<div id="<?php echo $field . '_' . $issue->getID(); ?>_spinning" style="margin-top: 3px; display: none;"><?php echo image_tag('spinning_20.gif', array('style' => 'float: left; margin-right: 5px;')) . '&nbsp;' . __('Please wait'); ?>...</div>
	<div id="<?php echo $field . '_' . $issue->getID(); ?>_change_error" class="error_message" style="display: none;"></div>
</div>