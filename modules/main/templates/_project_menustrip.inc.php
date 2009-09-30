<div class="menu_project_strip">
	<div class="project_name"><?php echo $project->getName(); ?></div>
	<div class="project_stuff">
		<a href="#" class="faded_medium">Project dashboard</a> | 
		<a href="#" class="faded_medium">Planning</a> | 
		<a href="#" class="faded_medium">Issues</a> | 
		<a href="#" class="faded_medium">Team</a>
	</div>
	<form action="<?php echo make_url('project_reportissue', array('project_key' => $project->getKey())); ?>" method="get" style="clear: none; display: inline; position: absolute; right: 3px; top: 3px;">
		<div class="report_button" style="width: 150px;"><input type="submit" value="<?php echo __('Report an issue'); ?>"></div>
	</form>
</div>