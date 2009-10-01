<div class="menu_project_strip">
	<?php if ($project instanceof BUGSproject): ?>
		<div class="project_name">
			<?php echo image_tag('spinning_32.gif', array('id' => 'project_menustrip_indicator', 'style' => 'display: none;')); ?>
			<span id="project_menustrip_name"><?php echo $project->getName(); ?></span>
		</div>
		<div class="project_stuff">
			<a href="#" class="faded_medium">Project dashboard</a> | 
			<a href="#" class="faded_medium">Planning</a> | 
			<a href="#" class="faded_medium">Issues</a> | 
			<a href="#" class="faded_medium">Team</a>
		</div>
		<?php if ($bugs_response->getPage() != 'reportissue' && (!isset($hide_button) || (isset($hide_button) && $hide_button == false))): ?>
			<form action="<?php echo make_url('project_reportissue', array('project_key' => $project->getKey())); ?>" method="get" style="clear: none; display: inline; position: absolute; right: 3px; top: 3px;">
				<div class="report_button" style="width: 150px;"><input type="submit" value="<?php echo __('Report an issue'); ?>"></div>
			</form>
		<?php endif; ?>
	<?php else: ?>
		<div class="project_name faded_dark">
			<?php echo image_tag('spinning_32.gif', array('id' => 'project_menustrip_indicator', 'style' => 'display: none;')); ?>
			<span id="project_menustrip_name"><?php echo __('There is no project selected'); ?></span>
		</div>
	<?php endif; ?>
</div>