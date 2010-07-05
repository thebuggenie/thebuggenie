<?php

	$tbg_response->setTitle(__('"%project_name%" roadmap', array('%project_name%' => $selected_project->getName())));

?>
<div id="project_roadmap">
	<?php foreach ($selected_project->getAllMilestones() as $milestone): ?>
		<div class="roadmap_milestone">
			<div class="roadmap_header">
				<?php echo $milestone->getName(); ?>
				<div class="roadmap_dates">
					<?php if ($milestone->hasStartingDate() && $milestone->hasScheduledDate()): ?>
						<?php if ($milestone->getStartingDate() < time() && $milestone->getScheduledDate() < time()): ?>
							<?php echo __('%milestone_name% (started %start_date% - ended %end_date%)', array('%milestone_name%' => '', '%start_date%' => tbg_formatTime($milestone->getStartingDate(), 23), '%end_date%' => tbg_formatTime($milestone->getScheduledDate(), 23))); ?>
						<?php elseif ($milestone->getStartingDate() < time() && $milestone->getScheduledDate() > time()): ?>
							<?php echo __('%milestone_name% (started %start_date% - ends %end_date%)', array('%milestone_name%' => '', '%start_date%' => tbg_formatTime($milestone->getStartingDate(), 23), '%end_date%' => tbg_formatTime($milestone->getScheduledDate(), 23))); ?>
						<?php elseif ($milestone->getStartingDate() > time()): ?>
							<?php echo __('%milestone_name% (starts %start_date% - ended %end_date%)', array('%milestone_name%' => '', '%start_date%' => tbg_formatTime($milestone->getStartingDate(), 23), '%end_date%' => tbg_formatTime($milestone->getScheduledDate(), 23))); ?>
						<?php endif; ?>
					<?php elseif ($milestone->hasStartingDate()): ?>
						<?php if ($milestone->getStartingDate() < time()): ?>
							<?php echo __('%milestone_name% (started %start_date%)', array('%milestone_name%' => '', '%start_date%' => tbg_formatTime($milestone->getStartingDate(), 23))); ?>
						<?php else: ?>
							<?php echo __('%milestone_name% (starts %start_date%)', array('%milestone_name%' => '', '%start_date%' => tbg_formatTime($milestone->getStartingDate(), 23))); ?>
						<?php endif; ?>
					<?php elseif ($milestone->hasScheduledDate()): ?>
						<?php if ($milestone->getScheduledDate() < time()): ?>
							<?php echo __('%milestone_name% (released: %date%)', array('%milestone_name%' => '', '%date%' => tbg_formatTime($milestone->getScheduledDate(), 23))); ?>
						<?php else: ?>
							<?php echo __('%milestone_name% (will be released: %date%)', array('%milestone_name%' => '', '%date%' => tbg_formatTime($milestone->getScheduledDate(), 23))); ?>
						<?php endif; ?>
					<?php endif; ?>
				</div>
			</div>
			<div class="roadmap_percentbar">
				<?php include_template('main/percentbar', array('percent' => $milestone->getPercentComplete(), 'height' => 25)); ?>
			</div>
			<div class="roadmap_percentdescription">
				<?php if ($milestone->isSprint()): ?>
					<?php if ($milestone->countClosedIssues() == 1): ?>
						<?php echo __('%num_closed% story (%closed_points% pts) closed of %num_assigned% (%assigned_points% pts) assigned', array('%num_closed%' => '<b>'.$milestone->countClosedIssues().'</b>', '%closed_points%' => '<i>'.$milestone->getPointsSpent().'</i>', '%num_assigned%' => '<b>'.$milestone->countIssues().'</b>', '%assigned_points%' => '<i>'.$milestone->getPointsEstimated().'</i>')); ?>
					<?php else: ?>
						<?php echo __('%num_closed% stories (%closed_points% pts) closed of %num_assigned% (%assigned_points% pts) assigned', array('%num_closed%' => '<b>'.$milestone->countClosedIssues().'</b>', '%closed_points%' => '<i>'.$milestone->getPointsSpent().'</i>', '%num_assigned%' => '<b>'.$milestone->countIssues().'</b>', '%assigned_points%' => '<i>'.$milestone->getPointsEstimated().'</i>')); ?>
					<?php endif; ?>
				<?php else: ?>
					<?php echo __('%num_closed% issue(s) closed of %num_assigned% assigned', array('%num_closed%' => '<b>'.$milestone->countClosedIssues().'</b>', '%num_assigned%' => '<b>'.$milestone->countIssues().'</b>')); ?>
				<?php endif; ?>
			</div>
			<div class="roadmap_issues">
				<?php foreach ($milestone->getIssues() as $issue): ?>
					<div class="roadmap_issue<?php if ($issue->isClosed()): ?> faded_medium issue_closed<?php endif; ?>">
						<div class="issue_status"><div style="border: 1px solid #AAA; background-color: <?php echo ($issue->getStatus() instanceof TBGStatus) ? $issue->getStatus()->getColor() : '#FFF'; ?>; font-size: 1px; width: 13px; height: 13px;" title="<?php echo ($issue->getStatus() instanceof TBGStatus) ? $issue->getStatus()->getName() : ''; ?>">&nbsp;</div></div>
						<?php echo link_tag(make_url('viewissue', array('project_key' => $issue->getProject()->getKey(), 'issue_no' => $issue->getIssueNo())), $issue->getFormattedIssueNo(true)); ?> - <?php echo $issue->getTitle(); ?>
						<?php if ($milestone->isSprint()): ?>
							<div class="issue_points"><?php echo __('%pts% points', array('%pts%' => $issue->getEstimatedPoints())); ?></div>
						<?php endif; ?>
					</div>
				<?php endforeach; ?>
			</div>
		</div>
	<?php endforeach; ?>
</div>