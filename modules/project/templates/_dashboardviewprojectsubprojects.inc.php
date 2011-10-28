<?php if (count($subprojects) > 0): ?>
	<ul class="project_list simple_list">
	<?php foreach ($subprojects as $project): ?>
		<li><?php include_component('project/overview', array('project' => $project)); ?></li>
	<?php endforeach; ?>
	</ul>
<?php else: ?>
	<div class="faded_out" style="font-weight: normal;"><?php echo __('This project has no subprojects'); ?></div>
<?php endif; ?>
<a style="float: right;" href="javascript:void(0);" class="button button-silver" onclick="TBG.Main.Helpers.Backdrop.show('<?php echo make_url('get_partial_for_backdrop', array('key' => 'project_archived_projects', 'pid' => TBGContext::getCurrentProject()->getID())); ?>');"><?php echo __('View archived subprojects'); ?></a>
<br style="clear: both;">
