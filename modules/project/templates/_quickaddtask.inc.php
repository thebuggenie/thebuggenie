<div class="rounded_box shadowed lightgrey quickaddtask" id="scrum_story_<?php echo $issue->getID(); ?>_add_task_div" style="margin: 5px 0 5px 0; display: none; width: 400px;">
	<form id="scrum_story_<?php echo $issue->getID(); ?>_add_task_form" action="<?php echo make_url('project_scrum_story_addtask', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>" method="post" accept-charset="<?php echo TBGSettings::getCharset(); ?>" onsubmit="TBG.Issues.addUserStoryTask('<?php echo make_url('project_scrum_story_addtask', array('project_key' => $issue->getProject()->getKey(), 'story_id' => $issue->getID())); ?>', <?php echo $issue->getID(); ?>, 'scrum');return false;">
		<div>
			<label for="scrum_story_<?php echo $issue->getID(); ?>_task_name_input"><?php echo __('Add task'); ?>&nbsp;</label>
			<input type="text" name="task_name" id="scrum_story_<?php echo $issue->getID(); ?>_task_name_input">
			<input type="hidden" name="mode" value="<?php echo $mode; ?>" >
			<input type="submit" value="<?php echo __('Add task'); ?>">
			<a class="close_micro_popup_link" href="javascript:void(0);" onclick="$('scrum_story_<?php echo $issue->getID(); ?>_add_task_div').toggle();"><?php echo __('Done'); ?></a>
			<?php echo image_tag('spinning_20.gif', array('id' => 'add_task_'.$issue->getID().'_indicator', 'style' => 'display: none;')); ?><br>
		</div>
	</form>
</div>