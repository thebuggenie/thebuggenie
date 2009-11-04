<li class="story_card moveable" id="scrum_story_<?php echo $issue->getID(); ?>">
	<div style="display: none;" class="story_color_selector" id="color_selector_<?php echo $issue->getID(); ?>">
		<div style="float: left;">
			<?php foreach ($colors as $color): ?>
				<div onclick="setStoryColor('<?php echo make_url('project_scrum_story_setcolor', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>, '<?php echo $color; ?>');" class="story_color_selector_item c_sel_red_1" style="background-color: <?php echo $color; ?>;">&nbsp;</div>
			<?php endforeach; ?>
		</div>
		<div style="float: left; position: relative;">
			<div class="header" style="margin-left: 5px;"><?php echo __('Pick a color for this user story'); ?></div>
			<div style="margin-left: 5px; width: 240px;"><?php echo __('Selecting a color makes the story easily recognizable'); ?>.</div>
			<?php echo image_tag('spinning_20.gif', array('id' => 'color_selector_'.$issue->getID().'_indicator', 'style' => 'position: absolute; right: 2px; top: 2px; display: none;')); ?>
		</div>
	</div>
	<div class="story_estimate">
		<a href="javascript:void(0);" onclick="$('scrum_story_<?php echo $issue->getID(); ?>_estimation').toggle();" alt="<?php echo __('Change estimate'); ?>" title="<?php echo __('Change estimate'); ?>"><?php echo image_tag('scrum_estimate.png'); ?></a>
		<span id="scrum_story_<?php echo $issue->getID(); ?>_points"><?php echo $issue->getEstimatedPoints(); ?></span>
	</div>
	<div class="story_color" id="story_color_<?php echo $issue->getID(); ?>" onclick="$('color_selector_<?php echo $issue->getID(); ?>').toggle();" style="cursor: pointer; background-color: <?php echo $issue->getScrumColor(); ?>;">&nbsp;</div>
	<div class="story_no"><?php echo $issue->getIssueNo(); ?></div>
	<div class="story_title"><?php echo $issue->getTitle(); ?></div>
	<input type="hidden" id="scrum_story_<?php echo $issue->getID(); ?>_id" value="<?php echo $issue->getIssueNo(); ?>">
	<div id="scrum_story_<?php echo $issue->getID(); ?>_estimation" class="story_estimation_div" style="display: none;">
		<form id="scrum_story_<?php echo $issue->getID(); ?>_estimation_form" action="<?php echo make_url('project_scrum_story_setpoints', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>" method="post" accept-charset="<?php echo BUGSsettings::getCharset(); ?>" onsubmit="setStoryEstimatedPoints('<?php echo make_url('project_scrum_story_setpoints', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>);return false;">
			<div class="header"><?php echo __('New story estimate'); ?></div>
			<?php echo image_tag('spinning_20.gif', array('id' => 'point_selector_'.$issue->getID().'_indicator', 'style' => 'display: none;')); ?>
			<input type="text" name="points" value="<?php echo $issue->getEstimatedPoints(); ?>" id="scrum_story_<?php echo $issue->getID(); ?>_points_input">
			<input type="submit" value="<?php echo __('Set'); ?>">
			<?php echo __('%set% or %cancel%', array('%set%' => '', '%cancel%' => '<a href="javascript:void(0);" onclick="$(\'scrum_story_' . $issue->getID() . '_estimation\').toggle();">' . __('cancel') . '</a>')); ?>
		</form>
	</div>
	<div class="actions">
		<label><?php echo __('Actions'); ?>:</label>
		<?php echo link_tag(make_url('viewissue', array('issue_no' => $issue->getIssueNo(), 'project_key' => $issue->getProject()->getKey())), image_tag('tab_new.png', array('title' => __('Open in new window'))), array('target' => '_blank')); ?>
		<a href="javascript:void(0);" onclick="showUserStoryEdit('url', <?php echo $issue->getID(); ?>);"><?php echo image_tag('icon_edit.png', array('title' => __('Edit user story'))); ?></a>
	</div>
</li>