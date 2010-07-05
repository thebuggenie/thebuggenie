<?php

	$tbg_response->setTitle(__('"%project_name%" roadmap', array('%project_name%' => $selected_project->getName())));

?>
<div id="project_roadmap">
	<?php foreach ($selected_project->getAllMilestones() as $milestone): ?>
	<div class="roadmap_header">
		<?php echo __('%milestone% roadmap', array('%milestone%' => $milestone->getName())); ?>
		<div class="roadmap_dates">
			<?php echo $milestone->getStartingDate(); ?>
			<?php if ($milestone->hasStartingDate() && $milestone->hasScheduledDate()): ?>
			<?php endif; ?>
		</div>
	</div>
	<div class="roadmap_percentbar">
		<?php include_template('main/percentbar', array('percent' => $milestone->getPercentComplete(), 'height' => 25)); ?>
	</div>
	<div class="roadmap_percentdescription">
		<?php if ($milestone->isSprint()): ?>
			<?php if ($milestone->countClosedIssues() == 1): ?>
				<?php echo __('%num_closed% story closed of %num_assigned% assigned', array('%num_closed%' => $milestone->countClosedIssues(), '%num_assigned%' => $milestone->countIssues())); ?>
			<?php else: ?>
				<?php echo __('%num_closed% stories closed of %num_assigned% assigned', array('%num_closed%' => $milestone->countClosedIssues(), '%num_assigned%' => $milestone->countIssues())); ?>
			<?php endif; ?>
		<?php else: ?>
			<?php echo __('%num_closed% issue(s) closed of %num_assigned% assigned', array('%num_closed%' => $milestone->countClosedIssues(), '%num_assigned%' => $milestone->countIssues())); ?>
		<?php endif; ?>
	</div>
	<?php endforeach; ?>
</div>