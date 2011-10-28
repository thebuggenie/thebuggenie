<div class="rounded_box lightgrey shadowed story_estimation_div" id="scrum_story_<?php echo $issue->getID(); ?>_estimation" style="display: none; padding: 5px;">
	<form id="scrum_story_<?php echo $issue->getID(); ?>_estimation_form" action="<?php echo make_url('project_scrum_story_setestimates', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="setStoryEstimates('<?php echo make_url('project_scrum_story_setestimates', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>, 'scrum');return false;">
		<div class="header">
			<?php if (isset($show_hours) && $show_hours): ?>
				<?php echo __('New task estimate'); ?>
			<?php else: ?>
				<?php echo __('New estimate'); ?>
			<?php endif; ?>
		</div>
		<?php echo image_tag('spinning_20.gif', array('id' => 'point_selector_'.$issue->getID().'_indicator', 'style' => 'display: none;')); ?>
		<?php if (isset($show_hours) && $show_hours): ?>
			<input type="text" name="hours" value="<?php echo $issue->getEstimatedHours(); ?>" id="scrum_story_<?php echo $issue->getID(); ?>_hours_input"> hrs
		<?php else: ?>
			<input type="text" name="points" value="<?php echo $issue->getEstimatedPoints(); ?>" id="scrum_story_<?php echo $issue->getID(); ?>_points_input"> pts
		<?php endif; ?>
		<input type="submit" value="<?php echo __('Set'); ?>">
		<?php echo __('%set% or %cancel%', array('%set%' => '', '%cancel%' => '<a href="javascript:void(0);" onclick="$(\'scrum_story_' . $issue->getID() . '_estimation\').toggle();">' . __('cancel') . '</a>')); ?>
	</form>
</div>